@extends('layout.main')

@section('title', 'Kontak Kami')

@section('content')
<div class="min-vh-100 bg-white py-5">

    {{-- Judul --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold display-6 text-dark">
            Hubungi Kami
        </h1>
        <p class="mt-2 text-muted">
            Terhubung dengan <span class="fw-semibold text-success">Padel Luxury Club</span> melalui media sosial resmi.
        </p>
    </div>

    <div class="container">
        <div class="row g-4 justify-content-center">

            {{-- WhatsApp --}}
            <div class="col-12 col-sm-6 col-md-3">
                <a href="https://wa.me/6281373400943" target="_blank"
                   class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4
                                hover-shadow">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center
                                    rounded-circle bg-success-subtle text-success"
                             style="width: 56px; height: 56px;">
                            <i class="bi bi-whatsapp fs-3"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">WhatsApp</h5>
                        <small class="text-muted d-block mb-2">Fast response 08.00–23.00 WIB</small>
                        <span class="fw-semibold text-success">
                            +62 813-7340-0943
                        </span>
                    </div>
                </a>
            </div>

            {{-- Facebook --}}
            <div class="col-12 col-sm-6 col-md-3">
                <a href="https://facebook.com/" target="_blank"
                   class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center
                                    rounded-circle bg-primary-subtle text-primary"
                             style="width: 56px; height: 56px;">
                            <i class="bi bi-facebook fs-3"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Facebook</h5>
                        <small class="text-muted d-block mb-2">Ikuti berita & event terbaru</small>
                        <span class="fw-semibold text-primary">
                            @padel.luxury.club
                        </span>
                    </div>
                </a>
            </div>

            {{-- Instagram --}}
            <div class="col-12 col-sm-6 col-md-3">
                <a href="https://www.instagram.com/luxurypadel.id?igsh=dWxjMWJzaHl1ZzZh"
                target="_blank" rel="noopener noreferrer"
                class="text-decoration-none text-dark d-block h-100">

                    <div class="card border-0 shadow-sm text-center p-4 rounded-4 h-100">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center
                                    rounded-circle bg-danger-subtle text-danger"
                            style="width: 56px; height: 56px;">
                            <i class="bi bi-instagram fs-3"></i>
                        </div>

                        <h5 class="fw-semibold mb-2">Instagram</h5>
                        <small class="text-muted d-block mb-2">Highlight match & daily story</small>

                        <span class="fw-semibold text-danger">
                            @luxurypadel.id
                        </span>
                    </div>
                </a>
            </div>



            {{-- Email --}}
            <div class="col-12 col-sm-6 col-md-3">
                <a href="mailto:info@padelreservasi.com"
                   class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center
                                    rounded-circle bg-info-subtle text-info"
                             style="width: 56px; height: 56px;">
                            <i class="bi bi-envelope-fill fs-3"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Email</h5>
                        <small class="text-muted d-block mb-2">Kerja sama & informasi resmi</small>
                        <span class="fw-semibold text-info">
                            info@padelreservasi.com
                        </span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
