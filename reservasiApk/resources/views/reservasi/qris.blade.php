@extends('layout.main')

@section('title', 'QRIS Dummy')

@section('content')
<div class="container" style="max-width:720px;margin:18px auto;">
  <div class="card border-0 shadow-sm" style="border-radius:18px;">
    <div class="card-body p-3 text-center">

      <h4 class="fw-bold mb-1">LUXURY PADEL</h4>

      <div class="text-muted mb-2">
        Invoice: <b>{{ $reservasi->payment_transaction_id }}</b>
      </div>

      <div class="mb-3">
        Total: <b>Rp {{ number_format($reservasi->total_harga,0,',','.') }}</b>
      </div>

      {{-- QR CODE --}}
      <div class="p-3 d-inline-block bg-white rounded-3 border">
        {!! QrCode::size(240)->generate($qrString) !!}
      </div>

      {{-- COUNTDOWN --}}
      <div class="mt-3 text-danger fw-bold" style="font-size:1.1rem;">
        Batas pembayaran: <span id="countdown">15:00</span>
      </div>

      {{-- DEADLINE (hidden, untuk JS) --}}
      <span id="deadline"
            data-deadline="{{ $deadline->toIso8601String() }}"
            class="d-none"></span>

      {{-- ACTION --}}
      <div class="d-grid gap-2 mt-4">
        <form method="POST" action="{{ route('reservasi.simulatePaid', $reservasi->id) }}">
          @csrf
          <button id="btnPay" class="btn btn-primary fw-bold">
            Simulasi Bayar (Set Paid)
          </button>
        </form>

        <a href="{{ route('reservasi.pay', $reservasi->id) }}"
           class="btn btn-outline-secondary">
          Ganti Metode
        </a>
      </div>

      <small class="text-muted d-block mt-3">
        Ini dummy untuk testing alur UI + status.
      </small>

    </div>
  </div>
</div>

{{-- COUNTDOWN SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deadlineIso = document.getElementById('deadline').dataset.deadline;
    const deadline   = new Date(deadlineIso).getTime();
    const el         = document.getElementById('countdown');
    const btnPay     = document.getElementById('btnPay');

    function tick() {
        const now = Date.now();
        let diff  = Math.max(0, deadline - now);

        const totalSeconds = Math.floor(diff / 1000);
        const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');

        el.textContent = `${minutes}:${seconds}`;

        if (diff <= 0) {
            el.textContent = '00:00';

            if (btnPay) {
                btnPay.disabled = true;
                btnPay.textContent = 'Waktu pembayaran habis';
                btnPay.classList.remove('btn-primary');
                btnPay.classList.add('btn-secondary');
            }

            clearInterval(timer);
        }
    }

    tick();
    const timer = setInterval(tick, 1000);
});
</script>
@endsection
