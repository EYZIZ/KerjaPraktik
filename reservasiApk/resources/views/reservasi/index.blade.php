@extends('layout.main')

@section('title', 'Riwayat Reservasi')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Riwayat Reservasi</h3>
        <a href="{{ route('reservasi.create') }}" class="btn btn-primary">
            + Reservasi Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="card shadow-lg border-0" style="border-radius: 18px;">
        <div class="card-body p-4">

            @if($reservasis->isEmpty())
                <p class="text-muted text-center mb-0">Belum ada reservasi yang dibuat.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status Reservasi</th>
                                <th>Status Pembayaran</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservasis as $index => $reservasi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        {{ optional($reservasi->lapangan)->location ?? 'Lapangan' }}
                                        <br>
                                        <small class="text-muted">
                                            ID: <code>{{ $reservasi->id }}</code>
                                        </small>
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($reservasi->tanggal)->format('d-m-Y') }}</td>

                                    <td>
                                        {{ $reservasi->jam_mulai }} - {{ $reservasi->jam_selesai }}
                                    </td>

                                    <td>{{ $reservasi->durasi }} jam</td>

                                    <td>Rp {{ number_format($reservasi->total_harga) }}</td>

                                    <td>
                                        @php
                                            $status = $reservasi->status;
                                        @endphp

                                        <span class="badge
                                            @if($status === 'pending') bg-warning text-dark
                                            @elseif($status === 'disetujui') bg-success
                                            @else bg-secondary @endif">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $pStatus = $reservasi->payment_status;
                                        @endphp

                                        <span class="badge
                                            @if($pStatus === 'paid') bg-success
                                            @elseif($pStatus === 'unpaid') bg-warning text-dark
                                            @else bg-danger @endif">
                                            {{ strtoupper($pStatus) }}
                                        </span>

                                        @if($reservasi->paid_at)
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($reservasi->paid_at)->format('d/m H:i') }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        @if($reservasi->payment_status !== 'paid')
                                            <form action="{{ route('reservasi.destroy', $reservasi->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Batalkan reservasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger w-100 mb-1">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif
                                        {{-- Jika mau, nanti bisa tambahkan tombol "Bayar ulang" di sini --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
