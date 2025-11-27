@extends('layout.main')

@section('title', 'REGISTER')

@section('content')
<div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="card shadow-lg p-4" style="width: 450px; border-radius: 15px;">

        <h3 class="mb-3 fw-bold text-center text-dark">Form Register</h3>

        {{-- Error Validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="name" class="form-control"
                    value="{{ old('name') }}" placeholder="Masukkan nama lengkap">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control"
                    value="{{ old('email') }}" placeholder="Masukkan email">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Masukkan password">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control"
                    placeholder="Ulangi password">
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">
                Register
            </button>

            <p class="text-center text-dark mt-3 mb-1">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary fw-semibold">Login</a>
            </p>
        </form>

    </div>
</div>
@endsection
