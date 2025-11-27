@extends('layout.main')

@section('title', 'Pembayaran Reservasi')

@section('content')
<div class="container mt-4" style="max-width: 700px;">

    <div class="card shadow-lg border-0" style="border-radius: 18px;">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-3 text-primary">Pembayaran Reservasi</h3>
            <p class="text-muted">
                Silakan cek kembali detail reservasi Anda, kemudian klik tombol
                <strong>Bayar Sekarang</strong> untuk melanjutkan ke pembayaran Midtrans.
            </p>

            {{-- Ringkasan Reservasi --}}
            <div class="mb-3">
                <h5 class="fw-bold mb-2">Detail Reservasi</h5>
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 30%;">Lapangan</th>
                        <td>
                            {{ optional($reservasi->lapangan)->location ?? 'Lapangan' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($reservasi->tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Jam</th>
                        <td>{{ $reservasi->jam_mulai }} - {{ $reservasi->jam_selesai }}</td>
                    </tr>
                    <tr>
                        <th>Durasi</th>
                        <td>{{ $reservasi->durasi }} jam</td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td><strong>Rp {{ number_format($reservasi->total_harga) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ strtoupper($reservasi->payment_status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <hr>

            {{-- Tombol Bayar --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('reservasi.index') }}" class="btn btn-secondary">
                    Kembali
                </a>

                <button id="pay-button" class="btn btn-primary">
                    Bayar Sekarang
                </button>
            </div>

            <small class="text-muted d-block mt-2">
                Setelah pembayaran berhasil, status reservasi akan otomatis diperbarui.
            </small>

        </div>
    </div>

</div>
@endsection

{{-- Snap JS Midtrans (SANDBOX) --}}
<script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.getElementById('pay-button');

            // Cek dulu apakah script Snap sudah ter-load
            console.log('Snap object:', window.snap);

            payButton.addEventListener('click', function () {
                if (typeof window.snap === 'undefined') {
                    alert('Snap JS belum ter-load. Cek client key atau URL Snap.');
                    return;
                }

                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function (result) {
                        console.log('success', result);
                        window.location.href = "{{ route('reservasi.index') }}";
                    },
                    onPending: function (result) {
                        console.log('pending', result);
                        window.location.href = "{{ route('reservasi.index') }}";
                    },
                    onError: function (result) {
                        console.log('error', result);
                        alert('Terjadi kesalahan saat proses pembayaran.');
                    },
                    onClose: function () {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
                    }
                });
            });
        });
</script>



{{--  Snap JS Midtrans
<script type="text/javascript"
    src="https://app.{{ config('midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script type="text/javascript">
    const payButton = document.getElementById('pay-button');

    payButton.addEventListener('click', function () {
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function (result) {
                console.log('success', result);
                window.location.href = "{{ route('reservasi.index') }}";
            },
            onPending: function (result) {
                console.log('pending', result);
                window.location.href = "{{ route('reservasi.index') }}";
            },
            onError: function (result) {
                console.log('error', result);
                alert('Terjadi kesalahan saat proses pembayaran.');
            },
            onClose: function () {
                alert('Anda menutup popup tanpa menyelesaikan pembayaran.');
            }
        });
    });
</script>  --}}
