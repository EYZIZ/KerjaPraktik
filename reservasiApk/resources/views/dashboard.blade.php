@extends('layout.main')

@section('title', 'Luxury Padel')

@section('content')
    <style>
        /* ================= HERO ================= */
        .hero-luxury {
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            height: 400px;
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

        .hero-text {
            position: absolute;
            left: 0;
            bottom: 40px;
            padding: 1.5rem 1.5rem;
            text-align: left;
        }

        @media (min-width: 768px) {
            .hero-text {
                bottom: 60px;
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
            font-weight: 900;
            font-size: 3.0rem;
            text-shadow: 0px 3px 10px rgba(0, 0, 0, 0.9);
        }

        @media (min-width: 768px) {
            .hero-title {
                font-size: 3.9rem;
            }
        }

        .hero-address {
            color: #ffffff;
            font-size: 0.9rem;
            text-shadow: 0px 2px 6px rgba(0, 0, 0, 0.9);
        }

        /* ===== CAROUSEL DOT CUSTOM (Court & Coach) ===== */
        .lux-carousel .carousel-indicators {
            position: static;
            margin-top: 16px;
            margin-bottom: 0;
            display: flex;
            justify-content: center;
            gap: 6px;
        }

        .lux-carousel .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 2px solid #0d6efd;
            opacity: 1;
        }

        .lux-carousel .carousel-indicators .active {
            background-color: #0d6efd;
        }

        /* WRAPPER UTAMA SEMUA SECTION */
        .section-center {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 10px;
        }

        @media (min-width: 768px) {
            .section-center {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .section-center {
                max-width: 900px;
            }
        }

        @media (min-width: 1200px) {
            .section-center {
                max-width: 1050px;
            }
        }

        /* ===== HAPUS PADDING PEMBATAS DI WRAPPER UTAMA ===== */
        .hero-area .container {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
        }

        .hero-area .hero-content {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
        }

        /* ====== MODAL CUSTOM PILIH LAPANGAN UNTUK COACH ====== */
        .coach-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

        .coach-overlay.d-none {
            display: none !important;
        }

        .coach-modal {
            background: #ffffff;
            border-radius: 16px;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .coach-modal-header {
            padding: 14px 18px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .coach-modal-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .coach-modal-body {
            padding: 12px 18px 16px;
            max-height: 360px;
            overflow-y: auto;
            background: #f8fafc;
        }

        .coach-close-btn {
            border: none;
            background: transparent;
            font-size: 1.2rem;
            line-height: 1;
            padding: 0;
        }

        /* Card lapangan di dalam popup */
        .choose-court-card {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
        }

        .choose-court-img {
            width: 110px;
            height: 90px;
            flex-shrink: 0;
            overflow: hidden;
            background: #f1f1f1;
        }

        .choose-court-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .choose-court-body {
            padding: 8px 10px 8px 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .choose-court-title {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 3px;
            color: #111827;
        }

        .choose-court-price {
            font-size: 0.85rem;
            color: #059669;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .choose-court-btn {
            font-size: 0.85rem;
            padding: 5px 10px;
        }
    </style>

    <div style="background: transparent;">
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
        <div class="py-5">
            <div class="section-center">
                <div class="card shadow-lg border-0"
                     style="border-radius:20px; overflow:hidden; background:#fff;">

                    {{-- HEADER GOLD --}}
                    <div style="
                        background: linear-gradient(135deg, #d4af37, #f9e79f, #d4af37);
                        padding: 20px;
                        text-align:center;
                    ">
                        <h3 class="fw-bold mb-0"
                            style="color:#3b3b3b; font-size:1.7rem;">
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
                            we deliver an exclusive experience with high quality amenities and
                            professional service to elevate your play.
                        </p>

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

        {{-- SECTION LAPANGAN: CAROUSEL + DOT --}}
        <div class="py-3">
            <div class="section-center text-center">
                <h2 class="fw-bold text-white mb-3">Luxury Padel Court</h2>

                @if($lapangans->count())
                    <div id="lapanganCarousel" class="carousel slide lux-carousel" data-bs-ride="carousel">

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

        {{-- SECTION COACH: CAROUSEL MIRIP COURT --}}
        <div class="py-4">
            <div class="section-center text-center">
                <h2 class="fw-bold text-white mb-3">Our Professional Coaches</h2>

                @if(isset($coaches) && $coaches->count())
                    <div id="coachCarousel" class="carousel slide lux-carousel" data-bs-ride="carousel">

                        <div class="carousel-inner">
                            @foreach($coaches as $coach)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden w-100">

                                        {{-- Foto Coach --}}
                                        <div style="height: 320px; overflow: hidden;">
                                            @if($coach->photo)
                                                <img src="{{ asset('storage/' . $coach->photo) }}"
                                                     class="w-100 h-100"
                                                     style="object-fit: cover;"
                                                     alt="{{ $coach->name }}">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                                    No photo
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Detail Coach --}}
                                        <div class="p-4" style="background:#fdfaf4;">
                                            <h4 class="fw-bold text-dark mb-1">
                                                {{ $coach->name }}
                                            </h4>

                                            @if($coach->speciality ?? false)
                                                <p class="mb-1 text-muted">
                                                    {{ $coach->speciality }}
                                                </p>
                                            @endif

                                            @if(isset($coach->price))
                                                <p class="mb-3 fw-semibold" style="color:#00805C;">
                                                    Rp {{ number_format($coach->price, 0, ',', '.') }} / session
                                                </p>
                                            @endif

                                            {{-- TOMBOL BUKA POPUP PILIH LAPANGAN --}}
                                            <button type="button"
                                                    class="btn btn-outline-success px-4 py-2 rounded-pill fw-semibold btn-book-coach"
                                                    data-coach-id="{{ $coach->id }}"
                                                    data-coach-name="{{ $coach->name }}">
                                                Book with {{ $coach->name }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- DOT INDICATOR --}}
                        <div class="carousel-indicators">
                            @foreach($coaches as $item)
                                <button type="button"
                                        data-bs-target="#coachCarousel"
                                        data-bs-slide-to="{{ $loop->index }}"
                                        class="{{ $loop->first ? 'active' : '' }}"
                                        @if($loop->first) aria-current="true" @endif
                                        aria-label="Coach {{ $loop->iteration }}">
                                </button>
                            @endforeach
                        </div>

                        {{-- TOMBOL NEXT/PREV --}}
                        <button class="carousel-control-prev" type="button"
                                data-bs-target="#coachCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                                data-bs-target="#coachCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                @else
                    <p class="text-muted">Belum ada data coach.</p>
                @endif
            </div>
        </div>

        {{-- SECTION MAP --}}
        <div class="py-4">
            <div class="section-center">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-0 text-center">
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

    {{-- POPUP CUSTOM: PILIH LAPANGAN UNTUK COACH --}}
    @if($lapangans->count())
        <div id="coachOverlay" class="coach-overlay d-none">
            <div class="coach-modal">
                <div class="coach-modal-header">
                    <h5 class="coach-modal-title">
                        Pilih Lapangan untuk <span id="coachNameModal"></span>
                    </h5>
                    <button type="button" id="closeCoachOverlay" class="coach-close-btn" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="coach-modal-body">
                    @foreach($lapangans as $lap)
                        <div class="choose-court-card">
                            <div class="choose-court-img">
                                @if($lap->photo)
                                    <img src="{{ asset('storage/' . $lap->photo) }}"
                                         alt="{{ $lap->location }}">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                        No photo
                                    </div>
                                @endif
                            </div>
                            <div class="choose-court-body">
                                <div class="choose-court-title">
                                    {{ $lap->location }}
                                </div>

                                @if(isset($lap->price_per_hour))
                                    <div class="choose-court-price">
                                        Rp {{ number_format($lap->price_per_hour, 0, ',', '.') }} / hour
                                    </div>
                                @endif

                                <button type="button"
                                        class="btn btn-success btn-sm rounded-pill choose-court-btn"
                                        data-lapangan-id="{{ $lap->id }}">
                                    Book
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <script>
        const reservasiBaseUrl = "{{ route('reservasi.create') }}";

        let selectedCoachId = null;

        const coachOverlay   = document.getElementById('coachOverlay');
        const coachNameModal = document.getElementById('coachNameModal');
        const closeCoachBtn  = document.getElementById('closeCoachOverlay');

        // Buka overlay saat klik "Book with ..."
        document.querySelectorAll('.btn-book-coach').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (!coachOverlay) return;

                selectedCoachId = this.getAttribute('data-coach-id');
                const coachName = this.getAttribute('data-coach-name') || '';

                if (coachNameModal) {
                    coachNameModal.textContent = coachName;
                }

                coachOverlay.classList.remove('d-none');
            });
        });

        // Klik tombol Book di card lapangan -> redirect ke reservasi
        document.querySelectorAll('.choose-court-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!selectedCoachId) return;

                const lapanganId = this.getAttribute('data-lapangan-id');
                if (!lapanganId) return;

                const url = reservasiBaseUrl
                    + '?lapangan_id=' + encodeURIComponent(lapanganId)
                    + '&coach_id=' + encodeURIComponent(selectedCoachId);

                window.location.href = url;
            });
        });

        // Tutup overlay via tombol X
        if (closeCoachBtn && coachOverlay) {
            closeCoachBtn.addEventListener('click', function () {
                coachOverlay.classList.add('d-none');
            });

            // Tutup saat klik area gelap di luar modal
            coachOverlay.addEventListener('click', function (e) {
                if (e.target === coachOverlay) {
                    coachOverlay.classList.add('d-none');
                }
            });
        }
    </script>
@endsection
