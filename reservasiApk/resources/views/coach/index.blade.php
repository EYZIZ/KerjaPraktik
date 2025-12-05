@extends('layout.main')

@section('title', 'Coach')

@section('content')

<style>
    /* Tombol create responsif */
    .btn-create {
        width: 100%;
        max-width: 100%;
    }

    @media (min-width: 768px) {
        .btn-create {
            width: auto;
            min-width: 180px;
        }
    }

    /* Card Style */
    .coach-card {
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
    }

    .coach-img {
        height: 380px;
        width: 100%;
        object-fit: cover;
    }

    .badge-coach {
        background: #0B6623;
        color: #fff;
        border-radius: 50px;
        font-size: 0.8rem;
        padding: 6px 14px;
    }
</style>

<div class="container mt-4">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between
                align-items-start align-items-md-center gap-2 mb-4">

        <h3 class="fw-bold mb-0 text-center text-md-start">
            Daftar Coach Padel
        </h3>

        <a href="{{ route('coach.create') }}"
           class="btn btn-success btn-create text-center">
            Create Coach
        </a>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    {{-- LIST COACH --}}
    <div class="row">

        @forelse ($coaches as $coach)
            <div class="col-12 col-md-6 col-lg-4 mb-4">

                <div class="card shadow-lg coach-card">

                    {{-- Foto --}}
                    <div class="position-relative">
                        @if($coach->photo)
                            <img src="{{ asset('storage/' . $coach->photo) }}"
                                 class="coach-img">
                        @else
                            <img src="https://via.placeholder.com/600x260?text=Coach+Image"
                                 class="coach-img">
                        @endif

                        <span class="badge-coach position-absolute top-0 end-0 m-3">
                            Coach
                        </span>
                    </div>

                    {{-- Isi --}}
                    <div class="card-body text-center">

                        <h4 class="fw-bold mb-1">{{ $coach->name }}</h4>

                        <p class="text-muted mb-1">
                            Nomor WhatsApp:
                            <span class="fw-semibold">{{ $coach->phone ?? '-' }}</span>
                        </p>

                        {{-- PRICE --}}
                        <p class="fw-bold text-primary mb-3">
                            Rp {{ number_format($coach->price) }} / sesi
                        </p>

                    </div>

                    {{-- Tombol --}}
                    <div class="card-footer bg-white border-0 d-flex justify-content-between px-3 pb-3">

                        <a href="{{ route('coach.edit', $coach->id) }}"
                           class="btn btn-warning">
                            Edit
                        </a>

                        <form action="{{ route('coach.destroy', $coach->id) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus coach ini?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">Belum ada data coach.</p>
        @endforelse
    </div>
</div>
@endsection
