@extends('layout.main')

@section('title', 'Pembayaran Reservasi')

@section('content')
<style>
    /* HILANGKAN PADDING CONTAINER DI HALAMAN INI SAJA */
    .pay-page-wrapper {
        max-width: 720px;
        margin: 0 auto;
    }

    /* BOTTOM SHEET PILIH PEMBAYARAN */
    .payment-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1050;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }
    .payment-overlay.d-none {
        display: none;
    }
    .payment-sheet {
        width: 100%;
        max-width: 720px;
        background: #3b2926;
        color: #fff;
        border-radius: 18px 18px 0 0;
        max-height: 70vh;
        overflow-y: auto;
        padding: 12px 0 8px;
    }
    .payment-sheet-header {
        padding: 0 16px 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .payment-sheet-header h5 {
        margin: 0;
        font-size: 1rem;
    }
    .payment-option-row {
        padding: 10px 16px;
        border-top: 1px solid rgba(255,255,255,0.12);
        font-size: 0.95rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .payment-option-row:first-of-type {
        border-top: none;
    }
    .payment-option-row button {
        all: unset;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .payment-radio {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #fff;
        display: inline-block;
        position: relative;
    }
    .payment-radio.active::after {
        content: '';
        position: absolute;
        inset: 3px;
        border-radius: 50%;
        background: #ffb74d;
    }
</style>

<div class="container-fluid px-0">
    <div class="pay-page-wrapper mt-3">

        <div class="card shadow-sm border-0" style="border-radius:18px; margin:0;">
            <div class="card-body px-3 py-3">

                {{-- HEADER TRANSAKSI --}}
                <div class="mb-3">
                    <h4 class="fw-bold mb-1" style="color:#00805C;">
                        Transaksi No {{ $reservasi->id }}
                    </h4>
                    <p class="mb-0" style="color:#777;">
                        Tanggal Transaksi
                        {{ \Carbon\Carbon::parse($reservasi->created_at)->translatedFormat('d M Y') }}
                    </p>
                </div>

                {{-- DETAIL COURT --}}
                <div class="mb-3 p-3 rounded-3"
                     style="background:#f9f6ea;border:1px solid #e0d9c0;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="me-2 fw-semibold" style="font-size:0.95rem;">
                                    Court
                                </span>
                                <span class="badge rounded-pill" style="background:#00805C;">
                                    {{ optional($reservasi->lapangan)->location ?? 'Lapangan' }}
                                </span>
                            </div>
                            <small style="color:#555;">
                                {{ \Carbon\Carbon::parse($reservasi->tanggal)->translatedFormat('d M Y') }}
                                · Jam {{ $reservasi->jam_mulai }} – {{ $reservasi->jam_selesai }}
                                · {{ $reservasi->durasi }} jam
                            </small>
                        </div>
                        <div class="fw-bold" style="color:#333;">
                            {{ number_format($reservasi->total_harga, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- RINGKASAN PEMBAYARAN --}}
                <div class="mb-3 p-3 rounded-3" style="background:#fdfcf7;border:1px solid #eee;">

                    {{-- baris: Total Transaksi --}}
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="fw-semibold">Total Transaksi</span>
                        <span class="fw-bold">
                            {{ number_format($reservasi->total_harga, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- baris: Pembayaran Online --}}
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span>Pembayaran Online</span>
                        <span class="fw-bold">
                            {{ number_format($reservasi->total_harga, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- baris: Jenis Pembayaran Online --}}
                    <div class="py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-normal">Jenis Pembayaran Online</div>
                            </div>
                            <button type="button"
                                    class="btn btn-outline-dark btn-sm"
                                    id="btn-open-payment">
                                <span id="selected_method_label">QRIS</span>
                            </button>
                        </div>
                    </div>

                    {{-- baris: Biaya Pembayaran --}}
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span>Biaya Pembayaran</span>
                        <span id="fee_amount"
                              class="fw-bold px-3 py-1 rounded-3"
                              style="background:#f7f0b3;">
                            0
                        </span>
                    </div>

                    {{-- baris: Total Pembayaran Online --}}
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span>Total Pembayaran Online</span>
                        <span id="total_online"
                              class="fw-bold px-3 py-1 rounded-3"
                              style="background:#f7eaa1;">
                            {{ number_format($reservasi->total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- BATAS PEMBAYARAN --}}
                @php
                    $deadline = \Carbon\Carbon::parse($reservasi->created_at)
                                ->addMinutes(30)
                                ->translatedFormat('d M Y H:i:s');
                @endphp
                <p class="text-center mb-3" style="color:#d32f2f;font-weight:600;">
                    Batas Pembayaran {{ $deadline }}
                </p>

                {{-- TOMBOL AKSI --}}
                <div class="d-grid gap-2">
                    <a href="{{ route('reservasi.index') }}"
                       class="btn fw-bold"
                       style="background:#b0b0b0;color:#fff;">
                        Kembali
                    </a>

                    <form action="{{ route('reservasi.destroy', $reservasi->id) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn w-100 fw-bold"
                                style="background:#d32f2f;color:#fff;">
                            Cancel Reservasi
                        </button>
                    </form>

                    <button id="pay-button"
                            class="btn w-100 fw-bold"
                            style="background:#00805C;color:#fff;">
                        Proses Bayar
                    </button>
                </div>

                <small class="text-muted d-block mt-3 text-center">
                    Setelah pembayaran berhasil, status reservasi akan otomatis diperbarui.
                </small>

                {{-- hidden jika mau diproses di server --}}
                <input type="hidden" id="payment_channel_input" name="payment_channel" value="QRIS">
                <input type="hidden" id="payment_fee_input" name="payment_fee" value="0">
            </div>
        </div>
    </div>
</div>

{{-- BOTTOM SHEET PILIH PEMBAYARAN (MENGAMBANG) --}}
<div id="paymentOverlay" class="payment-overlay d-none">
    <div class="payment-sheet">
        <div class="payment-sheet-header">
            <h5>Pilih Pembayaran</h5>
            <button type="button" id="btn-close-payment" class="btn btn-sm btn-light">
                Tutup
            </button>
        </div>

        {{-- LIST METODE --}}
        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="CIMB_VA" data-label="CIMB NIAGA VA" data-fee="3000">
                <span>CIMB NIAGA VA</span>
                <span class="payment-radio"></span>
            </button>
        </div>
        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="BNI_VA" data-label="BNI VA" data-fee="3000">
                <span>BNI VA</span>
                <span class="payment-radio"></span>
            </button>
        </div>
        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="MANDIRI_VA" data-label="MANDIRI VA H2H" data-fee="3000">
                <span>MANDIRI VA H2H</span>
                <span class="payment-radio"></span>
            </button>
        </div>
        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="BRI_VA" data-label="BRI VA" data-fee="3000">
                <span>BRI VA</span>
                <span class="payment-radio"></span>
            </button>
        </div>
        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="BCA_VA" data-label="BCA VA H2H" data-fee="3000">
                <span>BCA VA H2H</span>
                <span class="payment-radio"></span>
            </button>
        </div>

        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="QRIS" data-label="QRIS" data-fee="0">
                <span>QRIS</span>
                <span class="payment-radio"></span>
            </button>
        </div>
        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="OVO" data-label="OVO" data-fee="0">
                <span>OVO</span>
                <span class="payment-radio"></span>
            </button>
        </div>
        <div class="payment-option-row">
            <button class="payment-option"
                    data-code="SHOPEEPAY" data-label="SHOPEEPAY APP" data-fee="0">
                <span>SHOPEEPAY APP</span>
                <span class="payment-radio"></span>
            </button>
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
    const baseAmount   = {{ $reservasi->total_harga }};
    const feeEl        = document.getElementById('fee_amount');
    const totalOnline  = document.getElementById('total_online');
    const selectedLbl  = document.getElementById('selected_method_label');
    const hiddenCode   = document.getElementById('payment_channel_input');
    const hiddenFee    = document.getElementById('payment_fee_input');

    const overlay      = document.getElementById('paymentOverlay');
    const btnOpen      = document.getElementById('btn-open-payment');
    const btnClose     = document.getElementById('btn-close-payment');

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(num);
    }

    function setMethod(code, label, fee, buttonEl) {
        selectedLbl.textContent = label;
        hiddenCode.value = code;
        hiddenFee.value  = fee;

        feeEl.textContent = formatNumber(fee);
        totalOnline.textContent = formatNumber(baseAmount + fee);

        // update radio
        document.querySelectorAll('.payment-radio').forEach(r => r.classList.remove('active'));
        if (buttonEl) {
            const radio = buttonEl.querySelector('.payment-radio');
            if (radio) radio.classList.add('active');
        }
    }

    // default: QRIS
    setMethod('QRIS', 'QRIS', 0, null);

    btnOpen.addEventListener('click', () => {
        overlay.classList.remove('d-none');
    });

    btnClose.addEventListener('click', () => {
        overlay.classList.add('d-none');
    });

    // klik luar sheet untuk tutup
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.add('d-none');
        }
    });

    document.querySelectorAll('.payment-option').forEach(btn => {
        btn.addEventListener('click', function () {
            const code  = this.dataset.code;
            const label = this.dataset.label;
            const fee   = parseInt(this.dataset.fee || '0', 10);

            setMethod(code, label, fee, this);
            overlay.classList.add('d-none');
        });
    });

    const payButton = document.getElementById('pay-button');
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
