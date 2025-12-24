@extends('layout.main')

@section('title', 'Akun Saya')

@section('content')
<div class="container mt-4">

    <h3 class="fw-bold text-white mb-3">Akun Saya</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- PROFIL --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg" style="border-radius: 18px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Profil</h5>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $user->name) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $user->email) }}">
                        </div>

                        <button class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- PASSWORD --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg" style="border-radius: 18px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Ubah Password</h5>

                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <button class="btn btn-dark">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
