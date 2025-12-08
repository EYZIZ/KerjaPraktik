@extends('layout.main')

@section('title', 'Lapangan')

@section('content')

<style>
    .btn-create-court {
        width: 100%;
        max-width: 100%;
    }

    @media (min-width: 768px) {
        .btn-create-court {
            width: auto;
            max-width: none;
            min-width: 180px;
        }
    }
</style>

<div class="container mt-4">

    {{-- HEADER SECTION --}}
    <div class="d-flex flex-column flex-md-row justify-content-between
                align-items-start align-items-md-center gap-2 mb-4">
        <h3 class="fw-bold mb-0 text-center text-white text-md-start">
            Daftar Lapangan Padel
        </h3>

        <a href="{{ route('lapangan.create') }}"
           class="btn btn-success btn-create-court d-block d-md-inline-block text-center">
            Create Court
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($lapangans as $lapangan)
            <div class="col-md-4 mb-4">
                <div class="card shadow-lg h-100 border-0" style="border-radius: 18px; overflow: hidden;">

                    {{-- Foto Lapangan --}}
                    <div class="position-relative">
                        @if($lapangan->photo)
                            <img src="{{ asset('storage/' . $lapangan->photo) }}"
                                 class="card-img-top"
                                 style="height: 220px; object-fit: cover;">
                        @else
                            <img src="https://via.placeholder.com/500x220?text=Lapangan"
                                 class="card-img-top"
                                 style="height: 220px; object-fit: cover;">
                        @endif

                        {{-- Badge Status --}}
                        <span class="badge position-absolute top-0 end-0 m-3 px-3 py-2
                            @if($lapangan->status == 'Tersedia') bg-success
                            @elseif($lapangan->status == 'Terisi') bg-warning text-dark
                            @else bg-secondary @endif"
                            style="border-radius: 999px; font-size: 0.8rem;">
                            {{ $lapangan->status }}
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold mb-1">
                            {{ $lapangan->location ?? 'Lokasi belum diisi' }}
                        </h5>

                        <p class="card-text text-muted mb-3" style="min-height: 60px; max-height: 80px; overflow: hidden;">
                            {{ $lapangan->description ?? 'Tidak ada deskripsi untuk lapangan ini.' }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div>
                                <small class="text-muted d-block">Harga per jam</small>
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($lapangan->price_per_hour) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 d-flex justify-content-between pt-0 pb-3 px-3">
                        <a href="{{ route('lapangan.edit', $lapangan->id) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        {{-- Tombol buat reservasi lapangan ini --}}
                        <a href="{{ route('reservasi.create', ['lapangan_id' => $lapangan->id]) }}"
                           class="btn btn-sm btn-primary">
                            Reservasi
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center">Belum ada data lapangan.</p>
        @endforelse
    </div>

</div>
@endsection
