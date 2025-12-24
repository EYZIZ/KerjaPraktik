<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ForgotPasswordOtpController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
});


Route::middleware('auth')->group(function () {

    Route::get('/lapangan', [LapanganController::class, 'index'])
            ->name('lapangan.index');

});

Route::middleware(['auth','role:admin'])->group(function () {

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
| Route Coach
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/coach', [CoachController::class, 'index'])
            ->name('coach.index');

});

Route::middleware(['auth','role:admin'])->group(function () {

    Route::get('/coach/create', [CoachController::class, 'create'])
        ->name('coach.create');

    Route::post('/coach', [CoachController::class, 'store'])
        ->name('coach.store');

    Route::get('/coach/{coach}/edit', [CoachController::class, 'edit'])
        ->name('coach.edit');

    Route::put('/coach/{coach}', [CoachController::class, 'update'])
        ->name('coach.update');

    Route::delete('/coach/{coach}', [CoachController::class, 'destroy'])
        ->name('coach.destroy');
});

/*
|--------------------------------------------------------------------------
| RESERVASI (Hanya untuk user yang login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi.index');
    Route::get('/reservasi/create', [ReservasiController::class, 'create'])->name('reservasi.create');
    Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');

    Route::get('/reservasi/{reservasi}/pay', [ReservasiController::class, 'pay'])
        ->name('reservasi.pay');

    Route::post('/reservasi/{reservasi}/pay', [ReservasiController::class, 'createPaymentDummy'])
        ->name('reservasi.pay.create');

    Route::get('/reservasi/{reservasi}/qris', [ReservasiController::class, 'qris'])
        ->name('reservasi.qris');

    Route::get('/reservasi/{reservasi}/briva', [ReservasiController::class, 'briva'])
        ->name('reservasi.briva');

    Route::post('/reservasi/{reservasi}/simulate-paid', [ReservasiController::class, 'simulatePaid'])
        ->name('reservasi.simulatePaid');

    Route::delete('/reservasi/{reservasi}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy');
});

/*
|--------------------------------------------------------------------------
| Laporan (Hanya untuk admin yang login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});


Route::get('/kontak', function () {
    return view('kontak');
})->name('kontak');

Route::get('/forgot-password-otp', [ForgotPasswordOtpController::class, 'showRequestForm'])
    ->name('password.otp.request');

Route::post('/forgot-password-otp', [ForgotPasswordOtpController::class, 'sendOtp'])
    ->name('password.otp.send');

Route::get('/forgot-password-otp/verify', [ForgotPasswordOtpController::class, 'showOtpForm'])
    ->name('password.otp.form');

Route::post('/forgot-password-otp/reset', [ForgotPasswordOtpController::class, 'verifyOtpAndReset'])
    ->name('password.otp.reset');

Route::post('/forgot-password-otp/resend', [ForgotPasswordOtpController::class, 'resend'])
    ->name('password.otp.resend');


require __DIR__.'/auth.php';
