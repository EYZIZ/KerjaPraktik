<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\Lapangan;
use App\Models\Reservasi;
use Midtrans\Notification;
use Illuminate\Http\Request;
use App\Models\ReservasiSlot;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config as MidtransConfig;
use App\Models\Coach; // <-- TAMBAH INI

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
        $reservasis = Reservasi::with(['lapangan', 'coach']) // <-- load coach juga
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->get();

        return view('reservasi.index', compact('reservasis'));
    }

    /**
     * Form buat reservasi.
     * Bisa pre-select lapangan / coach:
     *  - /reservasi/create?lapangan_id=...
     *  - /reservasi/create?coach_id=...
     */
    public function create(Request $request)
    {
        $lapangans = Lapangan::where('status', 'Tersedia')->get();
        $coaches   = Coach::all();

        // --- coach terpilih dari query ?coach_id=... (boleh null) ---
        $coachTerpilih = null;
        if ($request->filled('coach_id')) {
            $coachTerpilih = $coaches->firstWhere('id', $request->coach_id);
        }

        // --- pilih lapangan seperti sebelumnya ---
        $lapanganTerpilih = null;
        if ($request->has('lapangan_id')) {
            $lapanganTerpilih = Lapangan::find($request->lapangan_id);
        } else {
            $lapanganTerpilih = $lapangans->first();
        }

        // kalau tetap tidak ada lapangan
        if (! $lapanganTerpilih) {
            return view('reservasi.create', [
                'lapangans'        => $lapangans,
                'lapanganTerpilih' => null,
                'coaches'          => $coaches,
                'coachTerpilih'    => $coachTerpilih,
                'tanggal'          => null,
                'dateTabs'         => [],
                'bookedSlots'      => [],
            ]);
        }

        // --- tanggal aktif (dari query ?tanggal=..., default hari ini) ---
        $tanggalAktif = $request->get('tanggal', Carbon::today()->toDateString());
        $tanggalAktif = Carbon::parse($tanggalAktif)->startOfDay();

        // ==== GENERATE 1 BULAN PENUH ====
        $firstOfMonth = $tanggalAktif->copy()->startOfMonth();
        $lastOfMonth  = $tanggalAktif->copy()->endOfMonth();

        $dateTabs = [];
        $current = $firstOfMonth->copy();

        while ($current->lte($lastOfMonth)) {
            $dateTabs[] = [
                'value'    => $current->toDateString(),
                'dayName'  => $current->format('D'),              // Fri
                'dayNum'   => $current->format('d'),              // 05
                'month'    => strtoupper($current->format('M')),  // DEC
                'isActive' => $current->isSameDay($tanggalAktif),
            ];
            $current->addDay();
        }

        // ==== cari slot yang sudah dibooking untuk lapangan + tanggal ini ====
        $bookedSlots = Reservasi::where('lapangan_id', $lapanganTerpilih->id)
            ->where('tanggal', $tanggalAktif->toDateString())
            ->pluck('jam_mulai')
            ->toArray();

        return view('reservasi.create', [
            'lapangans'        => $lapangans,
            'lapanganTerpilih' => $lapanganTerpilih,
            'coaches'          => $coaches,
            'coachTerpilih'    => $coachTerpilih,
            'tanggal'          => $tanggalAktif->toDateString(),
            'dateTabs'         => $dateTabs,
            'bookedSlots'      => $bookedSlots,
        ]);
    }


    /**
     * Simpan reservasi + buat transaksi pembayaran (Midtrans Snap).
     */
    public function store(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'coach_id'    => 'nullable|exists:coaches,id',
            'tanggal'     => 'required|date|after_or_equal:today',
            'slots'       => 'required|array|min:1',   // array slot "07:00-08:00"
        ]);

        $lapangan = Lapangan::findOrFail($request->lapangan_id);
        $coachId  = $request->coach_id ?: null;
        $tanggal  = $request->tanggal;

        // ====== AMBIL HARGA COACH (per session) ======
        $coachPrice = 0;
        $coach      = null;

        if ($coachId) {
            $coach = Coach::find($coachId);
            $coachPrice = $coach?->price ?? 0;
        }

        // PARSE SLOT: "07:00-08:00" -> ['start' => '07:00', 'end' => '08:00']
        $parsedSlots = [];
        foreach ($request->slots as $raw) {
            [$start, $end] = array_map('trim', explode('-', $raw));
            $parsedSlots[] = [
                'start' => $start,
                'end'   => $end,
            ];
        }

        // Urutkan berdasarkan jam mulai
        usort($parsedSlots, fn ($a, $b) => strcmp($a['start'], $b['start']));

        // Cek bentrok untuk tiap slot di lapangan
        foreach ($parsedSlots as $slot) {
            $bentrokLapangan = ReservasiSlot::where('lapangan_id', $lapangan->id)
                ->where('tanggal', $tanggal)
                ->where('jam_mulai', '<', $slot['end'])
                ->where('jam_selesai', '>', $slot['start'])
                ->whereHas('reservasi', function ($q) {
                    $q->whereIn('status', ['pending', 'disetujui']);
                })
                ->exists();

            if ($bentrokLapangan) {
                return back()
                    ->withErrors([
                        'slots' => "Jam {$slot['start']} - {$slot['end']} sudah dipesan. Silakan pilih jam lain."
                    ])
                    ->withInput();
            }
        }

        // Cek bentrok untuk coach (kalau dipilih)
        if ($coachId) {
            foreach ($parsedSlots as $slot) {
                $bentrokCoach = ReservasiSlot::where('tanggal', $tanggal)
                    ->where('jam_mulai', '<', $slot['end'])
                    ->where('jam_selesai', '>', $slot['start'])
                    ->whereHas('reservasi', function ($q) use ($coachId) {
                        $q->where('coach_id', $coachId)
                        ->whereIn('status', ['pending', 'disetujui']);
                    })
                    ->exists();

                if ($bentrokCoach) {
                    return back()
                        ->withErrors([
                            'coach_id' => "Coach ini sudah ada jadwal di jam {$slot['start']} - {$slot['end']}."
                        ])
                        ->withInput();
                }
            }
        }

        // ====== HITUNG RINGKASAN ======
        $jamMulai   = $parsedSlots[0]['start'];
        $jamSelesai = end($parsedSlots)['end'];
        $totalJam   = count($parsedSlots);

        $totalLapangan = $totalJam * $lapangan->price_per_hour;
        $totalHarga    = $totalLapangan + $coachPrice;   // <--- plus coach

        // 1. Buat reservasi
        $reservasi = Reservasi::create([
            'user_id'                => Auth::id(),
            'lapangan_id'            => $lapangan->id,
            'coach_id'               => $coachId,
            'tanggal'                => $tanggal,
            'jam_mulai'              => $jamMulai,
            'durasi'                 => $totalJam,
            'jam_selesai'            => $jamSelesai,
            'total_harga'            => $totalHarga,      // <--- sudah plus coach
            'status'                 => 'pending',
            'payment_status'         => 'unpaid',
            'payment_method'         => 'midtrans',
            'payment_transaction_id' => null,
            'paid_at'                => null,
        ]);

        // 2. Simpan semua slot ke tabel 'reservasi_slots'
        foreach ($parsedSlots as $slot) {
            ReservasiSlot::create([
                'reservasi_id' => $reservasi->id,
                'lapangan_id'  => $lapangan->id,
                'tanggal'      => $tanggal,
                'jam_mulai'    => $slot['start'],
                'jam_selesai'  => $slot['end'],
            ]);
        }

        // 3. Midtrans Snap
        $orderId  = $reservasi->id;

        $itemDetails = [
            [
                'id'       => $lapangan->id,
                'price'    => $lapangan->price_per_hour,
                'quantity' => $totalJam,
                'name'     => 'Sewa Lapangan Padel - ' . ($lapangan->location ?? 'Lapangan'),
            ],
        ];

        // Tambah item coach kalau ada
        if ($coachId && $coach) {
            $itemDetails[] = [
                'id'       => 'coach-' . $coach->id,
                'price'    => $coachPrice,
                'quantity' => 1,
                'name'     => 'Coach ' . $coach->name,
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $totalHarga,   // <--- total lapangan + coach
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
            'item_details' => $itemDetails,
        ];

        $snapToken = Snap::getSnapToken($params);

        $reservasi->update([
            'payment_transaction_id' => $orderId,
        ]);

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
     * Batalkan reservasi oleh user (kalau belum paid).
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
