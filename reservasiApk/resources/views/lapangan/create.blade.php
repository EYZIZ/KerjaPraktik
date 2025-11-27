@extends('layout.main')

@section('title', 'Tambah Lapangan')

@section('content')
<div class="container mt-4" style="max-width: 700px;">

    <div class="card shadow-lg border-0" style="border-radius: 18px;">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-3 text-primary">Tambah Lapangan Baru</h3>
            <p class="text-muted mb-4">Silakan isi data berikut untuk menambahkan lapangan.</p>

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

            <form action="{{ route('lapangan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Foto --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Lapangan</label>
                    <input type="file" name="photo" class="form-control">

                    <small class="text-muted">Format: jpg, jpeg, png, webp (max 2MB).</small>
                </div>

                {{-- Lokasi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lokasi Lapangan</label>
                    <input type="text" name="location" class="form-control"
                           value="{{ old('location') }}"
                           placeholder="Contoh: Lapangan A / Court 1 / Outdoor Arena">
                </div>

                {{-- Harga --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga per Jam</label>
                    <input type="number" name="price_per_hour" class="form-control"
                           value="{{ old('price_per_hour') }}"
                           placeholder="Contoh: 150000">
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Lapangan</label>
                    <select name="status" class="form-select" required>
                        <option value="Tersedia"  {{ old('status', $lapangan->status ?? '') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Perbaikan" {{ old('status', $lapangan->status ?? '') == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="Terisi"    {{ old('status', $lapangan->status ?? '') == 'Terisi' ? 'selected' : '' }}>Terisi</option>
                    </select>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" rows="4" class="form-control"
                              placeholder="Tuliskan deskripsi singkat tentang lapangan (opsional)">{{ old('description') }}</textarea>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('lapangan.index') }}" class="btn btn-secondary px-4">
                        Kembali
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
