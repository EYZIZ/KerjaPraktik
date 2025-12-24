@extends('layout.main')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="py-5 mt-3">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4">

          <h3 class="mb-2 text-center fw-bold text-dark">Verifikasi OTP</h3>
          <p class="text-center small text-muted mb-4">
            OTP dikirim ke <b>{{ $email }}</b>
          </p>

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

            <form action="{{ route('password.otp.reset') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Kode OTP</label>
                    <input type="text"
                            name="otp"
                            class="form-control"
                            maxlength="6"
                            placeholder="Masukkan 6 digit OTP"
                            required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password"
                            name="password"
                            class="form-control"
                            placeholder="Minimal 8 karakter"
                            required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password"
                            required>
                </div>

                <div class="d-grid mb-2">
                    <button class="btn btn-primary" type="submit">Reset Password</button>
                </div>
            </form>

            <form action="{{ route('password.otp.resend') }}" method="POST">
                @csrf
                <button class="btn btn-link small text-decoration-none" type="submit">
                    Kirim ulang OTP
                </button>
            </form>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
