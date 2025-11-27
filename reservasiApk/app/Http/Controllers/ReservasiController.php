<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;

class ReservasiController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans dari config/midtrans.php
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production', false);
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    /**
     * Daftar reservasi milik user login.
     */
    public function index()
    {
        $reservasis = Reservasi::with('lapangan')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->get();

        return view('reservasi.index', compact('reservasis'));
    }

    /**
     * Form buat reservasi.
     * Bisa pre-select lapangan: /reservasi/create?lapangan_id=...
     */
    public function create(Request $request)
    {
        $lapangans = Lapangan::where('status', 'Tersedia')->get();
        $lapanganTerpilih = null;

        if ($request->has('lapangan_id')) {
            $lapanganTerpilih = Lapangan::find($request->lapangan_id);
        }

        return view('reservasi.create', compact('lapangans', 'lapanganTerpilih'));
    }

    /**
     * Simpan reservasi + buat transaksi pembayaran (Midtrans Snap).
     */
    public function store(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'tanggal'     => 'required|date|after_or_equal:today',
            'jam_mulai'   => 'required',
            'durasi'      => 'required|integer|in:1,2,3',
        ]);

        $lapangan = Lapangan::findOrFail($request->lapangan_id);

        // Pastikan durasi berupa integer
        $durasi = (int) $request->input('durasi');

        // Hitung jam selesai dari jam_mulai + durasi
        $start = Carbon::createFromFormat('H:i', $request->jam_mulai);
        $end   = $start->copy()->addHours($durasi);

        $jamMulai   = $start->format('H:i');
        $jamSelesai = $end->format('H:i');

        // Hitung total harga
        $totalHarga = $durasi * $lapangan->price_per_hour;

        // Cek bentrok jadwal (lapangan & tanggal yang sama)
        $bentrok = Reservasi::where('lapangan_id', $lapangan->id)
            ->where('tanggal', $request->tanggal)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                // existing.jam_mulai < new_jam_selesai
                // AND existing.jam_selesai > new_jam_mulai
                $q->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai);
            })
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();

        if ($bentrok) {
            return back()
                ->withErrors(['tanggal' => 'Jadwal di lapangan ini sudah terisi. Silakan pilih jam atau durasi lain.'])
                ->withInput();
        }

        // 1. Buat reservasi dulu dengan status pending & unpaid
        $reservasi = Reservasi::create([
            'user_id'                => Auth::id(),
            'lapangan_id'            => $lapangan->id,
            'tanggal'                => $request->tanggal,
            'jam_mulai'              => $jamMulai,
            'durasi'                 => $durasi,
            'jam_selesai'            => $jamSelesai,
            'total_harga'            => $totalHarga,
            'status'                 => 'pending',
            'payment_status'         => 'unpaid',
            'payment_method'         => 'midtrans',
            'payment_transaction_id' => null,
            'paid_at'                => null,
        ]);

        // 2. Siapkan parameter untuk Midtrans Snap
        $orderId = $reservasi->id; // pakai UUID reservasi sebagai order_id

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $totalHarga,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id'       => $lapangan->id,
                    'price'    => $lapangan->price_per_hour,
                    'quantity' => $durasi, // pakai integer durasi
                    'name'     => 'Sewa Lapangan Padel - ' . ($lapangan->location ?? 'Lapangan'),
                ],
            ],
        ];

        // 3. Dapatkan Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Simpan order_id / transaction_id kalau mau
        $reservasi->update([
            'payment_transaction_id' => $orderId,
        ]);

        // 4. Kirim ke view yang akan menjalankan Snap JS
        return view('reservasi.pay', [
            'reservasi' => $reservasi,
            'snapToken' => $snapToken,
        ]);
    }


    /**
     * Callback / webhook dari Midtrans.
     * Route ini harus dipasang di URL yang kamu daftarkan di dashboard Midtrans.
     */
    public function callback(Request $request)
    {
        $notification = new Notification();

        $status       = $notification->transaction_status;
        $paymentType  = $notification->payment_type;
        $orderId      = $notification->order_id;
        $fraudStatus  = $notification->fraud_status;

        // Cari reservasi berdasarkan order_id (di sini kita pakai id reservasi sebagai order_id)
        $reservasi = Reservasi::where('id', $orderId)->first();

        if (! $reservasi) {
            return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
        }

        if ($status == 'capture' || $status == 'settlement') {
            // Pembayaran sukses
            $reservasi->update([
                'payment_status' => 'paid',
                'status'         => 'disetujui',
                'paid_at'        => now(),
            ]);
        } elseif ($status == 'pending') {
            // Menunggu pembayaran
            $reservasi->update([
                'payment_status' => 'unpaid',
                'status'         => 'pending',
            ]);
        } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
            // Gagal / kadaluarsa / dibatalkan
            $reservasi->update([
                'payment_status' => 'failed',
                'status'         => 'dibatalkan',
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * (Opsional) Batalkan reservasi oleh user (kalau belum paid).
     */
    public function destroy(Reservasi $reservasi)
    {
        if ($reservasi->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya boleh batalkan kalau belum dibayar
        if ($reservasi->payment_status === 'paid') {
            return redirect()->route('reservasi.index')
                ->with('error', 'Reservasi yang sudah dibayar tidak dapat dibatalkan melalui sistem.');
        }

        $reservasi->update([
            'status'         => 'dibatalkan',
            'payment_status' => 'failed',
        ]);

        return redirect()->route('reservasi.index')
            ->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
