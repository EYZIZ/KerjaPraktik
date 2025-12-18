@extends('layout.main')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="container" style="max-width:1100px;margin:20px auto;">
    <h3 class="fw-bold mb-3 text-white">Laporan Pendapatan</h3>

    {{-- FILTER --}}
    <form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('laporan.index') }}">
        <div class="col-md-4">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start" value="{{ $start->toDateString() }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end" value="{{ $end->toDateString() }}" class="form-control">
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-success fw-bold">Terapkan</button>
        </div>
    </form>

    {{-- KPI --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body">
                    <div class="text-muted">Total Pendapatan</div>
                    <div class="fw-bold" style="font-size:1.6rem;">
                        Rp {{ number_format($totalPendapatan,0,',','.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body">
                    <div class="text-muted">Jumlah Transaksi (Paid)</div>
                    <div class="fw-bold" style="font-size:1.6rem;">
                        {{ number_format($totalTransaksi,0,',','.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body">
                    <div class="fw-bold mb-2">Pendapatan Per Hari</div>
                    <canvas id="chartDaily" height="140"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body">
                    <div class="fw-bold mb-2">Pendapatan Per Bulan</div>
                    <canvas id="chartMonthly" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm mt-3" style="border-radius:16px;">
        <div class="card-body">
            <div class="fw-bold mb-2">Detail Harian</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($harian as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->tgl)->format('d-m-Y') }}</td>
                                <td class="text-end">Rp {{ number_format($row->total,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Belum ada data paid di rentang ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const dailyLabels = @json($dailyLabels);
  const dailyTotals = @json($dailyTotals);

  const monthLabels = @json($monthLabels);
  const monthTotals = @json($monthTotals);

  // Line chart harian
  new Chart(document.getElementById('chartDaily'), {
    type: 'line',
    data: {
      labels: dailyLabels,
      datasets: [{
        label: 'Pendapatan',
        data: dailyTotals,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          ticks: {
            callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
          }
        }
      }
    }
  });

  // Bar chart bulanan
  new Chart(document.getElementById('chartMonthly'), {
    type: 'bar',
    data: {
      labels: monthLabels,
      datasets: [{
        label: 'Pendapatan',
        data: monthTotals
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          ticks: {
            callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
          }
        }
      }
    }
  });
</script>
@endsection
