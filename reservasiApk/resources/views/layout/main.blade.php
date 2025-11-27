
<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>@yield('title')</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('assets/images/LOGO.png') }}" />

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
    /* Header fixed + putih (punyamu yang sekarang, biarkan) */
    .header.navbar-area {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 9999;
        background: #837979 !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .header.navbar-area.sticky {
        background: #ffffff !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .header.navbar-area .navbar-nav .nav-item a {
        color: #000 !important;
        font-weight: 600;
    }

    .header.navbar-area .btn {
        background: #ff8401 !important;
        color: #fff !important;
        font-weight: 600;
        border-radius: 3px;
        border: none !important;
    }

    .navbar-brand img {
        object-fit: cover;
        width: 65px;
        height: 75px;
    }

    .hero-area {
        background: #ffffff !important;
        padding-top: 120px;
    }

    body {
        background: #ffffff;
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
                        <!-- Start Navbar -->
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="/">
                                {{-- logo putih (awal) --}}
                                <img src="{{ url('assets/images/TPlogo.png') }}" alt="Logo" class="logo-light" height="80px">
                            </a>
                            <button class="navbar-toggler mobile-menu-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
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
                                        <a href="{{ url('lapangan') }}" aria-label="Toggle navigation">Lapangan</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('reservasi') }}" aria-label="Toggle navigation">Reservasi</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('kontak') }}" aria-label="Toggle navigation">Kontak Kami</a>
                                    </li>
                                </ul>
                            </div> <!-- navbar collapse -->
                            <div class="button add-list-button">
                                {{-- Jika belum login --}}
                                @guest
                                    <a href="{{ route('login') }}" class="btn">Login</a>
                                @endguest

                                {{-- Jika sudah login --}}
                                @auth
                                    <div class="dropdown">
                                        <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            style="
                                                background: transparent;
                                                border: none;
                                                box-shadow: none;
                                                padding: 0;
                                                color: black;
                                                font-weight: 600;
                                                cursor: pointer;
                                            ">
                                            {{ Auth::user()->name }}
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                                    Profil
                                                </a>
                                            </li>

                                            <li>
                                                <form action="{{ route('logout') }}" method="POST">
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
                        <!-- End Navbar -->
                    </div>
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </header>
    <!-- End Header Area -->

    <!-- Start Hero Area -->
    <section id="home" class="hero-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="hero-content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Hero Area -->

    <!-- ========================= scroll-top ========================= -->
    <a href="#" class="scroll-top">
        <i class="lni lni-chevron-up"></i>
    </a>

    <!-- ========================= JS here ========================= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('assets/js/wow.min.js') }}"></script>
    <script src="{{ url('assets/js/tiny-slider.js') }}"></script>
    <script src="{{ url('assets/js/glightbox.min.js') }}"></script>
    <script src="{{ url('assets/js/count-up.min.js') }}"></script>
    <script src="{{ url('assets/js/main.js') }}"></script>
</body>
</html>
