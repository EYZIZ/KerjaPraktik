<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservasiController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/lapangan', [LapanganController::class, 'index'])
        ->name('lapangan.index');

Route::middleware('auth')->group(function () {

    Route::get('/lapangan/create', [LapanganController::class, 'create'])
        ->name('lapangan.create');

    Route::post('/lapangan', [LapanganController::class, 'store'])
        ->name('lapangan.store');

    // ========== ROUTE EDIT ==========
    Route::get('/lapangan/{id}/edit', [LapanganController::class, 'edit'])
        ->name('lapangan.edit');

    Route::put('/lapangan/{id}', [LapanganController::class, 'update'])
        ->name('lapangan.update');

    // ========== ROUTE DELETE ==========
    Route::delete('/lapangan/{id}', [LapanganController::class, 'destroy'])
        ->name('lapangan.destroy');
});

/*
|--------------------------------------------------------------------------
| RESERVASI (Hanya untuk user yang login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // List reservasi
    Route::get('/reservasi', [ReservasiController::class, 'index'])
        ->name('reservasi.index');

    // Form buat reservasi
    Route::get('/reservasi/create', [ReservasiController::class, 'create'])
        ->name('reservasi.create');

    // Simpan reservasi + generate SNAP TOKEN
    Route::post('/reservasi', [ReservasiController::class, 'store'])
        ->name('reservasi.store');

    // Batalkan reservasi (kalau unpaid)
    Route::delete('/reservasi/{reservasi}', [ReservasiController::class, 'destroy'])
        ->name('reservasi.destroy');
});

/*
|--------------------------------------------------------------------------
| MIDTRANS PAYMENT CALLBACK (TANPA AUTH)
|--------------------------------------------------------------------------
| Route ini wajib diizinkan Midtrans untuk mengirim notif.
| Pastikan kamu daftarkan URL ini di dashboard Midtrans.
|--------------------------------------------------------------------------
*/
Route::post('/payment/midtrans/callback', [ReservasiController::class, 'callback'])
    ->name('payment.midtrans.callback');


Route::get('/kontak', function () {
    return view('kontak');
})->name('kontak');

require __DIR__.'/auth.php';
