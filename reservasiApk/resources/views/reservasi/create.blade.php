@extends('layout.main')

@section('title', 'Reservasi')

@section('content')
<style>
    /* WRAPPER UTAMA */
    .booking-page {
        padding: 0 0 100px;
    }
    .booking-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 8px;
    }

    @media (max-width: 576px) {
        .booking-container {
            max-width: 100%;
            padding: 0 6px;
        }
    }

    /* HAPUS GUTTER & PADDING BAWAAN BOOTSTRAP */
    .no-gutter {
        --bs-gutter-x: 0 !important;
        --bs-gutter-y: 0 !important;
    }
    .no-padding {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* CARD UTAMA RESERVASI */
    .booking-card {
        border-radius: 22px;
        margin: 0 0 24px 0;
        border: 0;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        overflow: hidden;
        width: 100%;
        background: #ffffff;
    }

    /* HEADER TANGGAL – DEFAULT: SCROLLABLE (UTK HP) */
    .date-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 4px 2px 12px;
        margin-bottom: 16px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .date-tabs::-webkit-scrollbar {
        display: none;
    }

    .date-pill {
        flex: 0 0 auto;
        min-width: 78px;
        text-align: center;
        padding: 8px 6px;
        border-radius: 12px;
        background: #f5f5f5;
        text-decoration: none;
        color: #222;
        border: 1px solid #e0e0e0;
        font-size: 0.8rem;
    }
    .date-pill .day-name {
        font-weight: 600;
        font-size: 0.8rem;
    }
    .date-pill .day-num {
        font-weight: 800;
        font-size: 1.1rem;
        line-height: 1.1;
    }
    .date-pill .month {
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .date-pill.active {
        background: #00805C;
        color: #fff;
        border-color: #00805C;
    }

    /* DI DESKTOP: TANGGAL TIDAK DI-SCROLL, LANGSUNG WRAP BEBERAPA BARIS */
    @media (min-width: 992px) {
        .date-tabs {
            overflow-x: visible;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 900px;
            margin: 0 auto 20px;
        }
        .date-pill {
            min-width: 90px;
            margin-bottom: 8px;
        }
    }

    /* GRID SLOT JAM */
    .slot-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(2, minmax(0, 1fr)); /* HP & tablet: 2 kolom */
    }
    @media (min-width: 992px) {
        .slot-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)); /* desktop: 3 kolom */
        }
    }

    .slot-btn {
        width: 100%;
        font-weight: 600;
        border-radius: 10px;
        padding: 8px 4px !important;
        border-width: 2px !important;
    }

    .slot-btn.btn-outline-success {
        border-color: #198754;
        color: #198754;
        background: #fff;
    }

    .slot-btn.btn-success {
        background: #198754 !important;
        border-color: #198754 !important;
        color: #fff !important;
    }

    .slot-time {
        display: block;
        font-size: 0.9rem;
    }
    .slot-price {
        display: block;
        font-size: 0.8rem;
        margin-top: 2px;
    }

    /* BAR MENGAMBANG TOMBOL RESERVASI */
    .reserve-bar {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 1100px;
        padding: 0 18px;
        background: transparent;
        z-index: 9999;
    }

    .reserve-btn {
        width: 100%;
        background: #059669;
        border: none;
        color: #fff;
        font-size: 1.1rem;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 600;
        box-shadow: 0 6px 18px rgba(0,0,0,0.25);
    }

    @media (max-width: 576px) {
        .reserve-bar {
            bottom: 16px;
            padding: 0 12px;
        }
        .reserve-btn {
            font-size: 1rem;
            padding: 11px 14px;
        }
    }
</style>

<div class="booking-page">
    <div class="booking-container py-4">
        <h3 class="fw-bold mb-3 text-center">Reserve Your Court</h3>

        {{-- ALERT ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (!$lapanganTerpilih)
            <div class="alert alert-warning">
                Belum ada data lapangan tersedia.
            </div>
        @else

        {{-- HEADER TANGGAL (1 BULAN) --}}
        <div class="date-tabs">
            @foreach($dateTabs as $tab)
                @php
                    // pastikan locale id
                    $carbonDate = \Carbon\Carbon::parse($tab['value'])->locale('id');
                @endphp

                <a href="{{ route('reservasi.create', [
                        'lapangan_id' => $lapanganTerpilih->id,
                        'tanggal'     => $tab['value'],
                    ]) }}"
                class="date-pill {{ $tab['isActive'] ? 'active' : '' }}">

                    {{-- HARI LENGKAP: Senin, Selasa, Rabu, ... --}}
                    <div class="day-name">
                        {{ $carbonDate->translatedFormat('l') }}
                    </div>

                    {{-- TANGGAL (01, 02, 03, ...) --}}
                    <div class="day-num">
                        {{ $carbonDate->format('d') }}
                    </div>

                    {{-- BULAN (Jan, Feb, Mar) versi Indonesia --}}
                    <div class="month">
                        {{ $carbonDate->translatedFormat('M') }}
                        {{-- kalau mau full: translatedFormat('F') -> "Desember" --}}
                    </div>
                </a>
            @endforeach
        </div>


        {{-- KARTU INFO LAPANGAN + JAM --}}
        <div class="card booking-card">
            <div class="row no-gutter">
                {{-- FOTO LAPANGAN --}}
                <div class="col-lg-5 col-12 no-padding">
                    <div style="height: 100%; min-height: 220px; overflow:hidden;">
                        @if($lapanganTerpilih->photo)
                            <img src="{{ asset('storage/'.$lapanganTerpilih->photo) }}"
                                 class="w-100 h-100"
                                 style="object-fit:cover;"
                                 alt="{{ $lapanganTerpilih->location }}">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                No photo
                            </div>
                        @endif
                    </div>
                </div>

                {{-- INFO LAPANGAN + JAM --}}
                <div class="col-lg-7 col-12 no-padding">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">{{ $lapanganTerpilih->location }}</h5>

                        <div class="slot-grid">
                            @for ($h = 7; $h < 23; $h++)
                                @php
                                    $start = sprintf('%02d:00', $h);
                                    $end   = sprintf('%02d:00', $h + 1);
                                    $label = $start . ' - ' . $end;
                                    $value = $start . '-' . $end;
                                    $isBooked = in_array($start, $bookedSlots);
                                @endphp

                                @if ($isBooked)
                                    @continue
                                @endif

                                <button type="button"
                                        class="btn btn-sm slot-btn btn-outline-success"
                                        data-value="{{ $value }}">
                                    <span class="slot-time">{{ $label }}</span>
                                    <span class="slot-price">
                                        Rp {{ number_format($lapanganTerpilih->price_per_hour, 0, ',', '.') }}
                                    </span>
                                </button>
                            @endfor
                        </div>

                        {{-- hidden inputs --}}
                        <div id="slotsContainer"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM POST RESERVASI --}}
        <form method="POST" action="{{ route('reservasi.store') }}" id="formReservasi">
            @csrf
            <input type="hidden" name="lapangan_id" value="{{ $lapanganTerpilih->id }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            {{-- PILIH COACH (OPSIONAL) --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Select Coach (optional)</label>
                <select name="coach_id" class="form-select">
                    <option value="">-- No Coach --</option>
                    @foreach($coaches as $coach)
                        <option value="{{ $coach->id }}"
                            {{ old('coach_id') == $coach->id ? 'selected' : '' }}>
                            {{ $coach->name }} (Rp {{ number_format($coach->price,0,',','.') }} / session)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TOMBOL CANCEL (STATIS) --}}
            <div class="mb-5">
                <a href="{{ url('/') }}" class="btn btn-secondary btn-lg w-100">
                    Cancel
                </a>
            </div>

            {{-- BAR MENGAMBANG: HANYA TOMBOL RESERVASI --}}
            <div id="reserveBar" class="reserve-bar d-none">
                <button type="submit" class="reserve-btn">
                    Reservasi - Rp <span id="btnTotal">0</span>
                </button>
            </div>
        </form>

        @endif
    </div>
</div>

<script>
    const hargaPerJam    = {{ $lapanganTerpilih ? $lapanganTerpilih->price_per_hour : 0 }};
    const slotsContainer = document.getElementById('slotsContainer');
    const btnTotalEl     = document.getElementById('btnTotal');
    const reserveBar     = document.getElementById('reserveBar');

    let selectedSlots = [];

    document.querySelectorAll('.slot-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const value = btn.dataset.value; // "07:00-08:00"
            const idx = selectedSlots.indexOf(value);

            if (idx === -1) {
                selectedSlots.push(value);
                btn.classList.remove('btn-outline-success');
                btn.classList.add('btn-success', 'text-white');
            } else {
                selectedSlots.splice(idx, 1);
                btn.classList.add('btn-outline-success');
                btn.classList.remove('btn-success', 'text-white');
            }

            renderSelectedSlots();
        });
    });

    function renderSelectedSlots() {
        slotsContainer.innerHTML = '';

        selectedSlots.forEach(val => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'slots[]';
            input.value = val;
            slotsContainer.appendChild(input);
        });

        const totalJam   = selectedSlots.length;
        const totalHarga = totalJam * hargaPerJam;

        btnTotalEl.textContent = totalHarga.toLocaleString('id-ID');

        if (totalJam > 0) {
            reserveBar.classList.remove('d-none');
        } else {
            reserveBar.classList.add('d-none');
        }
    }
</script>
@endsection
