<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>@yield('title')</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" type="image/png"  href="{{ url('assets/images/luxurylogo.png') }}">

    <!-- ========================= CSS here ========================= -->
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ url('assets/css/LineIcons.2.0.css') }}" />
    <link rel="stylesheet" href="{{ url('assets/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ url('assets/css/tiny-slider.css') }}" />
    <link rel="stylesheet" href="{{ url('assets/css/glightbox.min.css') }}" />
    <link rel="stylesheet" href="{{ url('assets/css/main.css') }}" />
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* ================= HEADER / NAVBAR ================= */
        .header.navbar-area {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            background: linear-gradient(
                90deg,
                #8d6e32,
                #d4af37,
                #ffdd44
            ) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .header.navbar-area.sticky {
            background: linear-gradient(90deg, #8d6e32, #d4af37, #ffdd44) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        /* Warna link nav */
        .header.navbar-area .navbar-nav .nav-item a {
            color: #000 !important;
            font-weight: 600;
        }

        .header.navbar-area .navbar-nav .nav-item a:hover {
            color: #333 !important;
        }

        /* Tombol Login */
        .header.navbar-area .btn {
            background: #1f2933 !important;
            color: #fff !important;
            font-weight: 600;
            border-radius: 3px;
            border: none !important;
        }
        .header.navbar-area .btn:hover {
            opacity: 0.9;
        }

        .navbar-brand img {
            object-fit: cover;
            width: 65px;
            height: 75px;
        }

        /* Hero digeser ke bawah */
        .hero-area {
            background: #02b008 !important;
            padding-top: 120px;
        }

        body {
            background: #02b008 !important;
        }

        /* ==================== FIX: HAPUS OUTLINE / HOVER BACKGROUND NAVBAR ==================== */

        /* Hilangkan efek button pada link navbar */
        .header.navbar-area .navbar-nav .nav-item a,
        .header.navbar-area .navbar-nav .nav-item a:hover,
        .header.navbar-area .navbar-nav .nav-item a:focus,
        .header.navbar-area .navbar-nav .nav-item a:active,
        .header.navbar-area .navbar-nav .nav-item a:focus-visible {
            background: transparent !important;
            outline: none !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Hilangkan pseudo underline jika template memakai efek garis */
        .header.navbar-area .navbar-nav .nav-item a::before,
        .header.navbar-area .navbar-nav .nav-item a::after {
            content: none !important;
            display: none !important;
        }

        /* Hilangkan outline tombol login */
        .header.navbar-area .btn,
        .header.navbar-area .btn:hover,
        .header.navbar-area .btn:focus,
        .header.navbar-area .btn:active,
        .header.navbar-area .btn:focus-visible {
            outline: none !important;
            box-shadow: none !important;
        }

        /* Hilangkan outline button dropdown */
        .header.navbar-area button:focus,
        .header.navbar-area button:active {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>

</head>

<body>

    <!-- Start Header Area -->
    <header class="header navbar-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="nav-inner">

                        <nav class="navbar navbar-expand-lg">

                            <a class="navbar-brand" href="/">
                                <img src="{{ url('assets/images/luxurybackground.png') }}" alt="Logo">
                            </a>

                            <button class="navbar-toggler mobile-menu-btn" type="button"
                                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                <ul id="nav" class="navbar-nav ms-auto">

                                    <li class="nav-item">
                                        @auth
                                            <a href="{{ route('dashboard') }}">Beranda</a>
                                        @else
                                            <a href="{{ url('/') }}">Beranda</a>
                                        @endauth
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ url('lapangan') }}">Lapangan</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ url('coach') }}">Coach</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ url('reservasi') }}">Reservasi</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ url('kontak') }}">Kontak Kami</a>
                                    </li>

                                    @guest
                                    <li class="nav-item">
                                        <a href="{{ route('login') }}" class="btn bg-transparent">Login</a>
                                    </li>
                                    @endguest

                                </ul>
                            </div>

                            <div class="button add-list-button">
                                @auth
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                        style="background:transparent; border:none; font-weight:600;">
                                        {{ Auth::user()->name }}
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @endauth
                            </div>

                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header Area -->

    <!-- Start Hero Area -->
    <section id="home" class="hero-area">
        <div class="container">
            <div class="hero-content">
                @yield('content')
            </div>
        </div>
    </section>

    {{--  <!-- SCROLL TOP -->
    <a href="#" class="scroll-top">
        <i class="lni lni-chevron-up"></i>
    </a>  --}}

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('assets/js/wow.min.js') }}"></script>
    <script src="{{ url('assets/js/tiny-slider.js') }}"></script>
    <script src="{{ url('assets/js/glightbox.min.js') }}"></script>
    <script src="{{ url('assets/js/count-up.min.js') }}"></script>
    <script src="{{ url('assets/js/main.js') }}"></script>

</body>
</html>
