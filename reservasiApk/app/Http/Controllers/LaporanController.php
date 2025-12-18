<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Laporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // filter tanggal (default: bulan ini)
        $tz = config('app.timezone', 'Asia/Jakarta');

        $start = $request->get('start')
            ? Carbon::parse($request->start, $tz)->startOfDay()
            : Carbon::now($tz)->startOfMonth()->startOfDay();

        $end = $request->get('end')
            ? Carbon::parse($request->end, $tz)->endOfDay()
            : Carbon::now($tz)->endOfMonth()->endOfDay();

        // ====== KPI ======
        $totalPendapatan = DB::table('reservasis')
            ->where('payment_status', 'paid')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('total_harga');

        $totalTransaksi = DB::table('reservasis')
            ->where('payment_status', 'paid')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->count();

        // ====== PENDAPATAN PER HARI ======
        // group by tanggal (DATE)
        $harian = DB::table('reservasis')
            ->selectRaw('tanggal as tgl, SUM(total_harga) as total')
            ->where('payment_status', 'paid')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // ====== PENDAPATAN PER BULAN ======
        // MySQL: DATE_FORMAT(tanggal, "%Y-%m")
        $bulanan = DB::table('reservasis')
            ->selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as ym, SUM(total_harga) as total')
            ->where('payment_status', 'paid')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        // data untuk chart
        $dailyLabels = $harian->pluck('tgl')->map(fn($d) => Carbon::parse($d, $tz)->format('d M'))->values();
        $dailyTotals = $harian->pluck('total')->map(fn($x) => (int)$x)->values();

        $monthLabels = $bulanan->pluck('ym')->map(function ($ym) use ($tz) {
            return Carbon::createFromFormat('Y-m', $ym, $tz)->format('M Y');
        })->values();
        $monthTotals = $bulanan->pluck('total')->map(fn($x) => (int)$x)->values();

        return view('laporan.index', compact(
            'start','end',
            'totalPendapatan','totalTransaksi',
            'harian','bulanan',
            'dailyLabels','dailyTotals',
            'monthLabels','monthTotals'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Laporan $laporan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laporan $laporan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Laporan $laporan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Laporan $laporan)
    {
        //
    }
}
