@extends('layout.main')

@section('title', 'Tambah Coach')

@section('content')

<div class="container mt-4" style="max-width: 700px;">

    <div class="card shadow-lg border-0" style="border-radius: 18px;">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-3 text-primary">Tambah Coach Baru</h3>
            <p class="text-muted mb-4">Isi data coach dengan lengkap dan benar.</p>

            {{-- Validasi --}}
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

            <form action="{{ route('coach.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Foto --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Coach</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">

                    <small class="text-muted">Format: JPG, JPEG, PNG, WEBP (max 2MB).</small>
                </div>

                {{-- Nama Coach --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Coach</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           placeholder="Contoh: Coach Andi / Coach Emily"
                           required>
                </div>

                {{-- Nomor WhatsApp --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor WhatsApp</label>
                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="{{ old('phone') }}"
                           placeholder="Contoh: +62 812-3456-7890">
                </div>

                {{-- Harga Coach --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga per Sesi</label>
                    <input type="number"
                           name="price"
                           class="form-control"
                           value="{{ old('price') }}"
                           placeholder="Contoh: 100000"
                           required>

                    <small class="text-muted">Harga layanan coach (per sesi latihan).</small>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('coach.index') }}" class="btn btn-secondary px-4">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
