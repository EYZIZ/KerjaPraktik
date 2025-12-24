<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lapangan;
use App\Models\Reservasi;
use App\Models\ReservasiSlot;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReservasiController extends Controller
{
    /**
     * Daftar reservasi milik user login.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $q = Reservasi::query()
            ->with(['user', 'lapangan'])
            ->latest();

        // ===== RULE AKSES =====
        if ($user->role === 'admin') {
            $q->where('payment_status', 'paid');
        } else {
            $q->where('user_id', $user->id);
        }

        // ===== FILTER =====
        if ($request->filled('q')) {
            $search = $request->q;
            $q->whereHas('user', function ($u) use ($search) {
                $u->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('from')) {
            $q->whereDate('tanggal', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $q->whereDate('tanggal', '<=', $request->to);
        }

        $reservasis = $q->paginate(10)->withQueryString();
        $showAksi = $reservasis->contains(function ($r) {
            return $r->payment_status !== 'paid';
        });

        return view('reservasi.index', compact('reservasis','showAksi'));
    }

    /**
     * Form buat reservasi.
     */
    public function create(Request $request)
    {
        $lapangans = Lapangan::where('status', 'Tersedia')->get();
        $coaches   = Coach::all();

        // coach terpilih
        $coachTerpilih = null;
        if ($request->filled('coach_id')) {
            $coachTerpilih = $coaches->firstWhere('id', $request->coach_id);
        }

        $lapanganTerpilih = $request->filled('lapangan_id')
            ? Lapangan::find($request->lapangan_id)
            : $lapangans->first();

        if (! $lapanganTerpilih) {
            return view('reservasi.create', [
                'lapangans'        => $lapangans,
                'lapanganTerpilih' => null,
                'coaches'          => $coaches,
                'coachTerpilih'    => $coachTerpilih,
                'tanggal'          => null,
                'dateTabs'         => [],
                'bookedSlots'      => [],
                'hiddenSlots'      => [],
            ]);
        }

        $now = Carbon::now();

        $tanggalAktif = $request->get('tanggal', $now->toDateString());
        $tanggalAktif = Carbon::parse($tanggalAktif)->startOfDay();

        if ($tanggalAktif->lt($now->copy()->startOfDay())) {
            return redirect()->route('reservasi.create', [
                'lapangan_id' => $lapanganTerpilih->id,
                'tanggal'     => $now->toDateString(),
                'coach_id'    => $request->get('coach_id'),
            ]);
        }

        $firstOfMonth = $tanggalAktif->copy()->startOfMonth();
        $lastOfMonth  = $tanggalAktif->copy()->endOfMonth();

        $startTabs = $firstOfMonth->copy();
        if ($startTabs->lt($now->copy()->startOfDay())) {
            $startTabs = $now->copy()->startOfDay();
        }

        $dateTabs = [];
        $current = $startTabs->copy();
        while ($current->lte($lastOfMonth)) {
            $dateTabs[] = [
                'value'    => $current->toDateString(),
                'dayName'  => $current->format('D'),
                'dayNum'   => $current->format('d'),
                'month'    => strtoupper($current->format('M')),
                'isActive' => $current->isSameDay($tanggalAktif),
            ];
            $current->addDay();
        }

        $holdMinutes = 15;
        $bookedSlots = ReservasiSlot::where('lapangan_id', $lapanganTerpilih->id)
            ->where('tanggal', $tanggalAktif->toDateString())
            ->whereHas('reservasi', function ($q) use ($holdMinutes) {
                $q->where(function ($x) use ($holdMinutes) {

                    $x->where('payment_status', 'paid')

                    ->orWhere(function ($y) use ($holdMinutes) {
                        $y->where('payment_status', 'unpaid')
                            ->where('created_at', '>', now()->subMinutes($holdMinutes));
                    });

                });
            })
            ->pluck('jam_mulai')
            ->map(fn ($t) => \Carbon\Carbon::parse($t)->format('H:i:s'))
            ->unique()
            ->values()
            ->toArray();

        $expiredSlots = [];
        if ($tanggalAktif->isSameDay($now)) {
            for ($h = 7; $h < 23; $h++) {
                $startDb = sprintf('%02d:00:00', $h);
                $slotStart = Carbon::parse($tanggalAktif->toDateString() . ' ' . $startDb);

                if ($slotStart->lte($now)) {
                    $expiredSlots[] = $startDb;
                }
            }
        }

        // gabungan hide: booked + expired
        $hiddenSlots = array_values(array_unique(array_merge($bookedSlots, $expiredSlots)));

        return view('reservasi.create', [
            'lapangans'        => $lapangans,
            'lapanganTerpilih' => $lapanganTerpilih,
            'coaches'          => $coaches,
            'coachTerpilih'    => $coachTerpilih,
            'tanggal'          => $tanggalAktif->toDateString(),
            'dateTabs'         => $dateTabs,
            'bookedSlots'      => $bookedSlots,
            'hiddenSlots'      => $hiddenSlots,
        ]);
    }

    /**
     * Simpan reservasi (tanpa Midtrans) -> lanjut ke halaman pilih pembayaran.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'coach_id'    => 'nullable|exists:coaches,id',
            'tanggal'     => 'required|date|after_or_equal:today',
            'slots'       => 'required|array|min:1',
        ]);

        $lapangan = Lapangan::findOrFail($request->lapangan_id);
        $coachId  = $request->coach_id ?: null;
        $tanggal  = $request->tanggal;

        $coachPrice = 0;
        $coach      = null;
        if ($coachId) {
            $coach = Coach::find($coachId);
            $coachPrice = $coach?->price ?? 0;
        }

        // parse slots dari UI: "07:00-08:00"
        $parsedSlots = [];
        foreach ($request->slots as $raw) {
            [$start, $end] = array_map('trim', explode('-', $raw));
            $parsedSlots[] = ['start' => $start, 'end' => $end];
        }
        usort($parsedSlots, fn ($a, $b) => strcmp($a['start'], $b['start']));

        /**
         * DEFINISI "AKTIF" (yang mengunci slot):
         * - payment_status = paid  -> selalu ngunci
         * - payment_status = unpaid dan created_at > now()-15 menit -> masih hold
         * FAILED / dibatalkan / expired -> tidak ngunci (slot kembali tersedia)
         */
        $activeHoldMinutes = 15;

        // ===== bentrok lapangan =====
        foreach ($parsedSlots as $slot) {
            $bentrokLapangan = ReservasiSlot::where('lapangan_id', $lapangan->id)
                ->where('tanggal', $tanggal)
                ->where('jam_mulai', '<', $slot['end'])
                ->where('jam_selesai', '>', $slot['start'])
                ->whereHas('reservasi', function ($q) use ($activeHoldMinutes) {
                    $q->where(function ($x) use ($activeHoldMinutes) {
                        $x->where('payment_status', 'paid')
                        ->orWhere(function ($y) use ($activeHoldMinutes) {
                            $y->where('payment_status', 'unpaid')
                                ->where('created_at', '>', now()->subMinutes($activeHoldMinutes));
                        });
                    });
                })
                ->exists();

            if ($bentrokLapangan) {
                return back()->withErrors([
                    'slots' => "Jam {$slot['start']} - {$slot['end']} sudah dipesan. Silakan pilih jam lain."
                ])->withInput();
            }
        }

        // ===== bentrok coach =====
        if ($coachId) {
            foreach ($parsedSlots as $slot) {
                $bentrokCoach = ReservasiSlot::where('tanggal', $tanggal)
                    ->where('jam_mulai', '<', $slot['end'])
                    ->where('jam_selesai', '>', $slot['start'])
                    ->whereHas('reservasi', function ($q) use ($coachId, $activeHoldMinutes) {
                        $q->where('coach_id', $coachId)
                        ->where(function ($x) use ($activeHoldMinutes) {
                            $x->where('payment_status', 'paid')
                                ->orWhere(function ($y) use ($activeHoldMinutes) {
                                    $y->where('payment_status', 'unpaid')
                                    ->where('created_at', '>', now()->subMinutes($activeHoldMinutes));
                                });
                        });
                    })
                    ->exists();

                if ($bentrokCoach) {
                    return back()->withErrors([
                        'coach_id' => "Coach ini sudah ada jadwal di jam {$slot['start']} - {$slot['end']}."
                    ])->withInput();
                }
            }
        }

        // hitung total
        $jamMulai   = $parsedSlots[0]['start'];
        $jamSelesai = end($parsedSlots)['end'];
        $totalJam   = count($parsedSlots);

        $totalLapangan = $totalJam * $lapangan->price_per_hour;
        $totalHarga    = $totalLapangan + $coachPrice;

        // buat reservasi
        $reservasi = Reservasi::create([
            'user_id'                => Auth::id(),
            'lapangan_id'            => $lapangan->id,
            'coach_id'               => $coachId,
            'tanggal'                => $tanggal,
            'jam_mulai'              => $jamMulai,
            'durasi'                 => $totalJam,
            'jam_selesai'            => $jamSelesai,
            'total_harga'            => $totalHarga,
            'status'                 => 'pending',

            // payment default
            'payment_status'         => 'unpaid',
            'payment_method'         => null,
            'payment_transaction_id' => null,
            'paid_at'                => null,
        ]);

        // simpan per jam di reservasi_slots (penting buat hide di create)
        foreach ($parsedSlots as $slot) {
            ReservasiSlot::create([
                'reservasi_id' => $reservasi->id,
                'lapangan_id'  => $lapangan->id,
                'tanggal'      => $tanggal,
                'jam_mulai'    => $slot['start'],
                'jam_selesai'  => $slot['end'],
            ]);
        }

        return redirect()->route('reservasi.pay', $reservasi->id);
    }


    /**
     * Halaman pilih pembayaran (hanya QRIS & BRIVA).
     */
    public function pay(Reservasi $reservasi)
    {
        $this->authorizeOwner($reservasi);

        // kalau sudah paid, jangan bayar lagi
        if ($reservasi->payment_status === 'paid') {
            return redirect()->route('reservasi.index')->with('success', 'Reservasi sudah dibayar.');
        }

        return view('reservasi.pay', compact('reservasi'));
    }

    /**
     * Buat pembayaran dummy (QRIS/BRIVA) -> redirect ke halaman instruksi.
     */
    public function createPaymentDummy(Request $request, Reservasi $reservasi)
    {
        $this->authorizeOwner($reservasi);

        if ($reservasi->payment_status === 'paid') {
            return redirect()->route('reservasi.index')->with('success', 'Reservasi sudah dibayar.');
        }

        $request->validate([
            'channel' => 'required|in:QRIS_BRI,BRIVA',
        ]);

        // buat invoice hanya sekali
        if (! $reservasi->payment_transaction_id) {
            $invoice = 'INV-' . strtoupper(Str::random(6)) . '-' . now()->format('YmdHis');

            $reservasi->update([
                'payment_transaction_id' => $invoice,
            ]);
        }

        $reservasi->update([
            'payment_method' => $request->channel, // QRIS_BRI / BRIVA
            'payment_status' => 'unpaid',
            'paid_at'        => null,
        ]);

        return $request->channel === 'QRIS_BRI'
            ? redirect()->route('reservasi.qris', $reservasi->id)
            : redirect()->route('reservasi.briva', $reservasi->id);
    }

    /**
     * Halaman QRIS dummy (generate QR dari string invoice|amount).
     */
    public function qris(Reservasi $reservasi)
    {
        $this->authorizeOwner($reservasi);

        if ($reservasi->payment_method !== 'QRIS_BRI') {
            return redirect()->route('reservasi.pay', $reservasi->id);
        }

        // DEADLINE = 15 MENIT DARI CREATED_AT
        $deadline = \Carbon\Carbon::parse($reservasi->created_at)->addMinutes(15);

        // kalau sudah lewat, tandai expired
        if (now()->greaterThan($deadline) && $reservasi->payment_status === 'unpaid') {
            $reservasi->update([
                'payment_status' => 'failed',
                'status'         => 'dibatalkan',
            ]);
        }


        $qrString = "DUMMYQRIS|{$reservasi->payment_transaction_id}|{$reservasi->total_harga}";

        return view('reservasi.qris', compact('reservasi', 'qrString', 'deadline'));
    }

    /**
     * Halaman BRIVA dummy (tampilkan nomor VA dummy).
     */
    public function briva(Reservasi $reservasi)
    {
        $this->authorizeOwner($reservasi);

        if ($reservasi->payment_method !== 'BRIVA') {
            return redirect()->route('reservasi.pay', $reservasi->id);
        }

        $tz = config('app.timezone', 'Asia/Jakarta');

        // contoh VA dummy (bukan VA asli)
        $vaDummy = '77777' . substr(preg_replace('/\D/', '', (string)$reservasi->payment_transaction_id), 0, 8);
        $vaDummy = substr($vaDummy, 0, 15);

        // ✅ DEADLINE = 15 menit dari created_at
        $deadline = \Carbon\Carbon::parse($reservasi->created_at, $tz)->addMinutes(15);
        $now = \Carbon\Carbon::now($tz);

        // ✅ kalau lewat deadline dan masih unpaid -> expired
        if ($now->greaterThan($deadline) && $reservasi->payment_status === 'unpaid') {
            $reservasi->update([
                'payment_status' => 'failed',
                'status'         => 'dibatalkan', // opsional, kalau kamu mau otomatis batal
            ]);
        }

        // refresh state setelah update
        $reservasi->refresh();

        return view('reservasi.briva', compact('reservasi', 'vaDummy', 'deadline'));
    }


    /**
     * Simulasi pembayaran sukses (testing).
     */
    public function simulatePaid(Reservasi $reservasi)
    {
        $this->authorizeOwner($reservasi);

        $reservasi->update([
            'payment_status' => 'paid',
            'status'         => 'disetujui',
            'paid_at'        => now(),
        ]);

        return redirect()->route('reservasi.index')->with('success', 'Simulasi: pembayaran berhasil.');
    }

    /**
     * Batalkan reservasi oleh user (kalau belum paid).
     */
    public function destroy(Reservasi $reservasi)
    {
        $this->authorizeOwner($reservasi);

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

    private function authorizeOwner(Reservasi $reservasi): void
    {
        if ($reservasi->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
