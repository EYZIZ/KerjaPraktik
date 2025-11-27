<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua lapangan untuk ditampilkan di dashboard
        $lapangans = Lapangan::latest()->get();

        return view('dashboard', compact('lapangans'));
    }
}
