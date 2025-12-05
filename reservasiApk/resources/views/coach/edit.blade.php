@extends('layout.main')

@section('title', 'Edit Coach')

@section('content')

<div class="container mt-4" style="max-width: 700px;">

    <div class="card shadow-lg border-0" style="border-radius: 18px;">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-3 text-primary">Edit Data Coach</h3>
            <p class="text-muted mb-4">Perbarui data coach lalu simpan perubahan.</p>

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

            <form action="{{ route('coach.update', $coach) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Foto --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Coach</label>

                    @if ($coach->photo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$coach->photo) }}"
                                 alt="{{ $coach->name }}"
                                 style="max-height: 140px; border-radius: 10px;">
                        </div>
                    @endif

                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <small class="text-muted">
                        Biarkan kosong jika tidak ingin mengganti foto.
                        Format: JPG, JPEG, PNG, WEBP (max 2MB).
                    </small>
                </div>

                {{-- Nama Coach --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Coach</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $coach->name) }}"
                           placeholder="Contoh: Coach Andi / Coach Emily"
                           required>
                </div>

                {{-- Nomor WhatsApp --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor WhatsApp</label>
                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="{{ old('phone', $coach->phone) }}"
                           placeholder="Contoh: +62813xxxxxxxx">
                </div>

                {{-- Harga Coach --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga per Sesi</label>
                    <input type="number"
                           name="price"
                           class="form-control"
                           value="{{ old('price', $coach->price) }}"
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
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
