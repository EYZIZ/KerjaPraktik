@extends('layout.main')

@section('title', 'BRIVA Dummy')

@section('content')
<div class="container" style="max-width:720px;margin:18px auto;">
  <div class="card border-0 shadow-sm" style="border-radius:18px;">
    <div class="card-body p-3">

      <h4 class="fw-bold mb-1 text-center">BRIVA (TEST / DUMMY)</h4>
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

      <div class="text-danger text-center" style="font-weight:600;">
        Batas pembayaran: {{ $deadline->format('d M Y H:i:s') }}
      </div>

      <div class="d-grid gap-2 mt-3">
        <form method="POST" action="{{ route('reservasi.simulatePaid', $reservasi->id) }}">
          @csrf
          <button class="btn btn-primary fw-bold">Simulasi Bayar (Set Paid)</button>
        </form>
        <a href="{{ route('reservasi.pay', $reservasi->id) }}" class="btn btn-outline-secondary">Ganti Metode</a>
      </div>

      <small class="text-muted d-block mt-3 text-center">
        Ini dummy untuk testing alur. Belum VA asli.
      </small>

    </div>
  </div>
</div>
@endsection
