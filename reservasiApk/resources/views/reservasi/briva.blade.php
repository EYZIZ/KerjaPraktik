@extends('layout.main')

@section('title', 'BRIVA Dummy')

@section('content')
<div class="container" style="max-width:720px;margin:18px auto;">
  <div class="card border-0 shadow-sm" style="border-radius:18px;">
    <div class="card-body p-3">

      <h4 class="fw-bold mb-1 text-center">BRIVA LUXURY PADEL</h4>
      <div class="text-muted text-center mb-3">Invoice: <b>{{ $reservasi->payment_transaction_id }}</b></div>

      <div class="p-3 rounded-3 mb-2" style="background:#fdfcf7;border:1px solid #eee;">
        <div class="d-flex justify-content-between">
          <span>Total</span>
          <b>Rp {{ number_format($reservasi->total_harga,0,',','.') }}</b>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center">
          <span>Nomor VA (Dummy)</span>
          <b style="font-size:1.1rem;">{{ $vaDummy }}</b>
        </div>
      </div>

      @php
        $isExpired = ($reservasi->payment_status !== 'unpaid');
      @endphp

      <div class="text-danger text-center" style="font-weight:600;">
        <div class="mt-1">
          Batas pembayaran: <span id="countdown">--:--</span>
        </div>
        <div id="expiredText" class="mt-1 {{ $isExpired ? '' : 'd-none' }}">
          Pembayaran sudah berakhir / status: <b>{{ strtoupper($reservasi->payment_status) }}</b>
        </div>
      </div>

      <div class="d-grid gap-2 mt-3">
        <form method="POST" action="{{ route('reservasi.simulatePaid', $reservasi->id) }}">
          @csrf
          <button id="btnSimulate" class="btn btn-primary fw-bold" {{ $isExpired ? 'disabled' : '' }}>
            Simulasi Bayar (Set Paid)
          </button>
        </form>
        <a href="{{ route('reservasi.pay', $reservasi->id) }}" class="btn btn-outline-secondary">Ganti Metode</a>
      </div>

      <small class="text-muted d-block mt-3 text-center">
        Note : Jika sudah melakukan pembayaran dan membatalkan tidak dapat melakukan pengembalian dana.
      </small>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const deadlineMs = new Date(@json($deadline->toIso8601String())).getTime();

  const countdownEl = document.getElementById('countdown');
  const expiredText = document.getElementById('expiredText');
  const btnSimulate = document.getElementById('btnSimulate');

  // ✅ redirect ke riwayat reservasi
  const redirectUrl = @json(route('reservasi.index'));

  function pad(n){ return String(n).padStart(2,'0'); }

  function tick(){
    const nowMs = Date.now();
    let diff = deadlineMs - nowMs;

    if (diff <= 0) {
      countdownEl.textContent = "00:00";
      expiredText.classList.remove('d-none');
      if (btnSimulate) {
        btnSimulate.disabled = true;
        btnSimulate.textContent = 'Waktu pembayaran habis';
        btnSimulate.classList.remove('btn-primary');
        btnSimulate.classList.add('btn-secondary');
      }

      clearInterval(timer);

      // ✅ auto redirect
      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 1200);

      return;
    }

    const totalSec = Math.floor(diff / 1000);
    const min = Math.floor(totalSec / 60);
    const sec = totalSec % 60;

    countdownEl.textContent = `${pad(min)}:${pad(sec)}`;
  }

  tick();
  const timer = setInterval(tick, 1000);
});
</script>

@endsection
