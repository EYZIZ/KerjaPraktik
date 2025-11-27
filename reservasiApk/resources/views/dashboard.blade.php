@extends('layout.main')

@section('title', 'Luxury Padel')

@section('content')
    <style>
        /* HERO full-bleed */
        .hero-luxury {
            position: relative;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            margin-top: 0;
        }

        @media (min-width: 768px) {
            .hero-luxury {
                margin-top: -1rem;
            }
        }

        /* ===== CAROUSEL DOT CUSTOM ===== */
        #lapanganCarousel .carousel-indicators {
            position: static;          /* pindah ke bawah card */
            margin-top: 16px;
            margin-bottom: 0;
            display: flex;
            justify-content: center;
            gap: 6px;
        }

        #lapanganCarousel .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ffffff;  /* default putih */
            border: 2px solid #0d6efd;  /* outline biru */
            opacity: 1;
        }

        #lapanganCarousel .carousel-indicators .active {
            background-color: #0d6efd;  /* slide aktif biru */
        }
    </style>

    <div class="bg-light min-vh-100">
        {{-- HERO FULL WIDTH TANPA JARAK KIRI/KANAN/ATAS --}}
        <div class="hero-luxury overflow-hidden" style="min-height: 220px;">
            <img src="{{ url('assets/images/BACKGROUND.png') }}"
                 alt="Luxury Padel Background"
                 class="w-100 d-block"
                 style="object-fit: cover; filter: brightness(0.6);">

            <div class="position-absolute top-0 start-0 w-100 h-100"
                 style="background: linear-gradient(90deg, rgba(0,0,0,0.65), rgba(0,0,0,0.15));">
            </div>

            <div class="position-absolute top-50 start-0 translate-middle-y ps-4 ps-md-5 pe-4 text-start">
                <p class="text-white mb-1 fw-bold small text-uppercase">
                    BOOK A COURT NOW AT
                </p>

                <h1 class="text-white fw-bold lh-1 mb-2" style="font-size: 3rem;">
                    LUXURY PADEL
                </h1>

                <p class="text-white mb-0 small">
                    Jl. Soekarno Hatta No.824, Talang Kelapa, Kec. Alang-Alang Lebar
                </p>
            </div>
        </div>

        {{-- SECTION LAPANGAN: CAROUSEL + DOT --}}
        <div class="container-fluid py-3 px-0">
            <div class="mx-auto px-2 px-md-3" style="max-width: 980px;">
                <h2 class="fw-bold text-dark mb-3">Luxury Padel Court</h2>

                @if($lapangans->count())
                    <div id="lapanganCarousel" class="carousel slide" data-bs-ride="carousel">

                        {{-- SLIDE LAPANGAN --}}
                        <div class="carousel-inner">
                            @foreach($lapangans as $lapangan)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden w-100">

                                        {{-- Foto Lapangan --}}
                                        <div style="height: 360px; overflow: hidden;">
                                            @if($lapangan->photo)
                                                <img src="{{ asset('storage/' . $lapangan->photo) }}"
                                                     class="w-100 h-100"
                                                     style="object-fit: cover;"
                                                     alt="{{ $lapangan->location }}">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                                    No photo
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Detail Lapangan --}}
                                        <div class="p-4" style="background:#f6f8ff;">
                                            <h4 class="fw-bold text-dark mb-1">
                                                {{ $lapangan->location }}
                                            </h4>

                                            @if($lapangan->description)
                                                <p class="text-muted mb-4">
                                                    {{ $lapangan->description }}
                                                </p>
                                            @endif

                                            <a href="{{ route('reservasi.create', ['lapangan_id' => $lapangan->id]) }}"
                                            class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- DOT INDICATOR (HANYA SEKALI, DI LUAR .carousel-inner) --}}
                        <div class="carousel-indicators">
                            @foreach($lapangans as $item)
                                <button type="button"
                                        data-bs-target="#lapanganCarousel"
                                        data-bs-slide-to="{{ $loop->index }}"
                                        class="{{ $loop->first ? 'active' : '' }}"
                                        @if($loop->first) aria-current="true" @endif
                                        aria-label="Slide {{ $loop->iteration }}">
                                </button>
                            @endforeach
                        </div>

                        {{-- TOMBOL NEXT/PREV --}}
                        <button class="carousel-control-prev" type="button"
                                data-bs-target="#lapanganCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                                data-bs-target="#lapanganCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                @else
                    <p class="text-muted">Belum ada data lapangan.</p>
                @endif
            </div>
        </div>

        {{-- SECTION MAP --}}
        <div class="container py-4">
            <div class="row g-4">
                {{-- MAP --}}
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0 fw-semibold text-dark">
                                Location Map Luxury Padel
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div style="height: 380px;">
                                <iframe
                                    src="https://www.google.com/maps?q=Jl.+Soekarno+Hatta+No.824,+Talang+Klp.,+Alang-Alang+Lebar,+Palembang,+Sumatera+Selatan+30153&output=embed"
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
