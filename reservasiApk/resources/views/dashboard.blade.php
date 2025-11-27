@extends('layout.main')

@section('title', 'Luxury Padel')

@section('content')
    <style>
        /* ================= HERO ================= */
        .hero-luxury {
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            height: 280px;              /* tinggi hero, bisa diubah 260–320 */
            position: relative;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.75);
        }

        /* Gradasi hitam dari bawah supaya teks kebaca */
        .hero-gradient {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(0, 0, 0, 0.85),
                rgba(0, 0, 0, 0.0) 65%
            );
            pointer-events: none;
        }

        /* Teks hero */
        .hero-text {
            position: absolute;
            left: 0;
            bottom: 40px;              /* NAIK / TURUNKAN TEKS DI SINI (mobile) */
            padding: 1.5rem 1.5rem;
            text-align: left;
        }

        @media (min-width: 768px) {
            .hero-text {
                bottom: 60px;          /* versi desktop sedikit lebih naik */
                padding: 2rem 3rem;
            }
        }

        .hero-small {
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 0px 2px 6px rgba(0, 0, 0, 0.9);
        }

        .hero-title {
            color: #ffffff;
            font-weight: 800;
            font-size: 2.4rem;
            text-shadow: 0px 3px 10px rgba(0, 0, 0, 0.9);
        }

        @media (min-width: 768px) {
            .hero-title {
                font-size: 3.2rem;
            }
        }

        .hero-address {
            color: #ffffff;
            font-size: 0.9rem;
            text-shadow: 0px 2px 6px rgba(0, 0, 0, 0.9);
        }

        /* ===== CAROUSEL DOT CUSTOM ===== */
        #lapanganCarousel .carousel-indicators {
            position: static;
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
            background-color: #ffffff;
            border: 2px solid #0d6efd;
            opacity: 1;
        }

        #lapanganCarousel .carousel-indicators .active {
            background-color: #0d6efd;
        }
    </style>

    <div class="bg-light min-vh-100">
        {{-- HERO FULL WIDTH --}}
        <div class="hero-luxury">
            <img src="{{ url('assets/images/luxurybackground.png') }}"
                 class="hero-bg"
                 alt="Luxury Padel Background">

            <div class="hero-gradient"></div>

            <div class="hero-text">
                <p class="hero-small mb-1">BOOK A COURT NOW AT</p>
                <h1 class="hero-title mb-2">LUXURY PADEL</h1>
                <p class="hero-address mb-0">
                    Jl. Soekarno Hatta No.824, Talang Kelapa, Kec. Alang-Alang Lebar
                </p>
            </div>
        </div>

        {{-- WELCOME & ABOUT CARD SECTION --}}
        <div class="container py-5">
            <div class="row justify-content-center">

                <div class="col-lg-8">
                    <div class="card shadow-lg border-0"
                        style="border-radius:20px; overflow:hidden; background:#fff;">

                        {{-- HEADER GOLD --}}
                        <div style="
                            background: linear-gradient(135deg, #d4af37, #f9e79f, #d4af37);
                            padding: 20px;
                            text-align:center;
                        ">
                            <h3 class="fw-bold mb-0"
                                style="color:#3b3b3b; font-size:1.5rem; white-space: nowrap;">
                                Welcome to Luxury Padel
                            </h3>
                        </div>

                        {{-- BODY CONTENT --}}
                        <div class="card-body p-4 p-md-5 text-center">

                            <p class="mb-4" style="font-size:1.1rem; color:#555;">
                                Experience a new standard of padel excellence —
                                where sport, lifestyle, and comfort blend into a world–class environment.
                            </p>

                            <h4 class="fw-bold mb-3" style="color:#3b3b3b;">About Us</h4>

                            <p style="font-size:1rem; color:#666;">
                                Luxury Padel offers premium courts, modern facilities, and
                                a relaxing atmosphere designed for players of all levels.
                                Whether you're here to compete, train, or simply enjoy the game,
                                we deliver an exclusive experience with high–quality amenities and
                                professional service to elevate your play.
                            </p>

                            {{-- OPTIONAL BUTTON --}}
                            <a href="{{ url('kontak') }}"
                            class="btn mt-3 px-4 py-2 fw-semibold"
                            style="
                                    background: linear-gradient(135deg, #d4af37, #f8e287);
                                    border:none;
                                    color:#2d2d2d;
                                    border-radius:30px;
                            ">
                                Learn More
                            </a>

                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- SECTION LAPANGAN: CAROUSEL + DOT --}}
        <div class="container-fluid py-3 px-0">
            <div class="mx-auto px-2 px-md-3" style="max-width: 980px;">
                <h2 class="fw-bold text-dark mb-3">Luxury Padel Court</h2>

                @if($lapangans->count())
                    <div id="lapanganCarousel" class="carousel slide" data-bs-ride="carousel">

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

                        {{-- DOT INDICATOR --}}
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
