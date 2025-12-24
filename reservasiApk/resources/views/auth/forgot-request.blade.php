@extends('layout.main')

@section('title', 'Lupa Password')

@section('content')
<div class="py-5 mt-3">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4">

          <h3 class="mb-4 text-center fw-bold text-dark">Lupa Password</h3>

          @if (session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
          @endif

          @if ($errors->any())
            <div class="alert alert-danger small">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

            <form action="{{ route('password.otp.send') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="Masukkan email terdaftar"
                            required>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">Kirim OTP</button>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ url('login') }}" class="small text-decoration-none">Kembali ke Login</a>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
