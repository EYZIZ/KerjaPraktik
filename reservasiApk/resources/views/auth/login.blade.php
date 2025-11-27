@extends('layout.main')

@section('title', 'LOGIN')

@section('content')
<div class="py-5 mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">

                    <h3 class="mb-4 text-center fw-bold text-dark">Form Login</h3>

                    {{-- tampilkan error kalau ada --}}
                    @if ($errors->any())
                        <div class="alert alert-danger small">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('login') }}" method="POST">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text"
                                   name="email"
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="Masukkan email atau username"
                                   required
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Masukkan password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Remember me + lupa password --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="#" class="small text-decoration-none">
                                Lupa password?
                            </a>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>
                        </div>

                        <p class="text-center text-dark small mb-0">
                            Belum punya akun?
                            <a href="{{ url('register') }}" class="text-decoration-none">Daftar sekarang</a>
                        </p>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
