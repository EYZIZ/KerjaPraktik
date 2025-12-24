<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordOtpMail;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordOtpController extends Controller
{
    private int $ttlMinutes = 15;
    private int $maxAttempts = 5;

    public function showRequestForm()
    {
        return view('auth.forgot-request');
    }

    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->withInput();
        }

        // hapus OTP lama untuk email ini
        PasswordOtp::where('email', $data['email'])->delete();

        $otp = (string) random_int(100000, 999999);

        PasswordOtp::create([
            'email'      => $data['email'],
            'code_hash'  => Hash::make($otp),
            'expires_at' => now()->addMinutes($this->ttlMinutes),
            'attempts'   => 0,
        ]);

        Mail::to($data['email'])->send(new PasswordOtpMail($otp, $this->ttlMinutes));

        $request->session()->put('pw_reset_email', $data['email']);

        return redirect()->route('password.otp.form')
            ->with('success', 'OTP sudah dikirim ke email. Silakan cek inbox/spam.');
    }

    public function showOtpForm(Request $request)
    {
        $email = $request->session()->get('pw_reset_email');
        if (!$email) {
            return redirect()->route('password.otp.request');
        }

        return view('auth.forgot-otp', compact('email'));
    }

    public function verifyOtpAndReset(Request $request)
    {
        $email = $request->session()->get('pw_reset_email');
        if (!$email) {
            return redirect()->route('password.otp.request');
        }

        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $otpRow = PasswordOtp::where('email', $email)->latest()->first();

        if (!$otpRow) {
            return back()->withErrors(['otp' => 'OTP tidak ditemukan. Silakan kirim ulang OTP.']);
        }

        if (now()->greaterThan($otpRow->expires_at)) {
            return back()->withErrors(['otp' => 'OTP sudah kedaluwarsa. Silakan kirim ulang OTP.']);
        }

        if ($otpRow->attempts >= $this->maxAttempts) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan. Silakan kirim ulang OTP.']);
        }

        if (!Hash::check($data['otp'], $otpRow->code_hash)) {
            $otpRow->increment('attempts');
            return back()->withErrors(['otp' => 'OTP salah.'])->withInput();
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['otp' => 'User tidak ditemukan.']);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        PasswordOtp::where('email', $email)->delete();
        $request->session()->forget('pw_reset_email');

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }

    public function resend(Request $request)
    {
        $email = $request->session()->get('pw_reset_email');
        if (!$email) {
            return redirect()->route('password.otp.request');
        }

        $request->merge(['email' => $email]);
        return $this->sendOtp($request);
    }
}
