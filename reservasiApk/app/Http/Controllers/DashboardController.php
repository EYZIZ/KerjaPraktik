<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Lapangan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $lapangans = Lapangan::latest()->get();
        $coaches   = Coach::all();

        return view('dashboard', compact('lapangans','coaches'));
    }
}
