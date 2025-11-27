@extends('layout.main')

@section('title', 'Buat Reservasi')

@section('content')
<div class="container mt-4" style="max-width: 700px;">

    <div class="card shadow-lg border-0" style="border-radius: 18px;">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-3 text-primary">Buat Reservasi Lapangan</h3>
            <p class="text-muted mb-4">
                Pilih lapangan, tanggal, dan jam bermain. Pembayaran akan diproses otomatis melalui Midtrans.
            </p>

            {{-- pesan flash --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- validasi --}}
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

            <form action="{{ route('reservasi.store') }}" method="POST">
                @csrf

                {{-- Lapangan --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Lapangan</label>
                    <select name="lapangan_id" class="form-select">
                        <option value="">-- pilih lapangan --</option>
                        @foreach($lapangans as $lapangan)
                            <option value="{{ $lapangan->id }}"
                                @if(old('lapangan_id') == $lapangan->id || (isset($lapanganTerpilih) && $lapanganTerpilih && $lapanganTerpilih->id == $lapangan->id)) selected @endif>
                                {{ $lapangan->location ?? 'Lapangan' }} - Rp {{ number_format($lapangan->price_per_hour) }}/jam
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Main</label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ old('tanggal') }}">
                </div>

                {{-- Jam & Durasi --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control"
                               value="{{ old('jam_mulai') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Durasi Sewa</label>
                        <select name="durasi" class="form-select">
                            <option value="1" {{ old('durasi') == 1 ? 'selected' : '' }}>1 Jam</option>
                            <option value="2" {{ old('durasi') == 2 ? 'selected' : '' }}>2 Jam</option>
                            <option value="3" {{ old('durasi') == 3 ? 'selected' : '' }}>3 Jam</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-info small">
                    Total harga akan dihitung otomatis berdasarkan harga lapangan dan durasi sewa,
                    lalu diproses melalui pembayaran.
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('reservasi.index') }}" class="btn btn-secondary px-4">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        Payment
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
