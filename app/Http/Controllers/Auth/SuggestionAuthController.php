<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class SuggestionAuthController extends Controller
{
    // ================================================================
    // Durasi OTP dalam menit
    // ================================================================
    const OTP_EXPIRE_MINUTES = 5;
    const OTP_RESEND_COOLDOWN = 60; // detik
    const MAX_ATTEMPTS = 5;

    // ================================================================
    // SHOW LOGIN PAGE
    // ================================================================
    public function showLogin(): View|RedirectResponse
{
    if (Session::has('suggestion_user_id')) {

        $dept = Session::get('suggestion_dept', []);

        return redirect()->route('suggestion.dashboard');
    }

    return view('auth.login-ss');
}

    // ================================================================
    // SEND OTP
    // ================================================================
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $email = strtolower(trim($request->email));

        // ── 1. Cek apakah email terdaftar di database karyawan ──
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar sebagai karyawan. Hubungi HR/IT jika ada masalah.',
            ]);
        }

        // ── 2. Cek cooldown (mencegah spam kirim OTP) ──
        $cooldownKey = 'otp_cooldown_' . md5($email);
        if (Cache::has($cooldownKey)) {
            $remaining = Cache::get($cooldownKey . '_time', self::OTP_RESEND_COOLDOWN);
            throw ValidationException::withMessages([
                'email' => "Harap tunggu sebelum meminta kode baru. Cek email Anda.",
            ]);
        }

        // ── 3. Generate OTP 6 digit ──
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // ── 4. Simpan OTP di cache (expire sesuai konstanta) ──
        $cacheKey = 'suggestion_otp_' . md5($email);
        Cache::put($cacheKey, [
            'otp'      => bcrypt($otp), // hash OTP sebelum disimpan
            'email'    => $email,
            'attempts' => 0,
        ], now()->addMinutes(self::OTP_EXPIRE_MINUTES));

        // ── 5. Set cooldown ──
        Cache::put($cooldownKey, true, now()->addSeconds(self::OTP_RESEND_COOLDOWN));

        // ── 6. Kirim email OTP ──
        Mail::to($email)->send(new \App\Mail\SuggestionOtpMail($otp, $user->name));

        // ── 7. Redirect dengan session info ──
        return redirect()->route('suggestion.login')
            ->with('otp_sent', true)
            ->with('email_for_otp', $email)
            ->with('status', "Kode OTP berhasil dikirim ke {$email}");
    }

    // ================================================================
    // VERIFY OTP
    // ================================================================
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp'   => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus 6 digit angka.',
        ]);

        $email    = strtolower(trim($request->email));
        $cacheKey = 'suggestion_otp_' . md5($email);

        // ── 1. Ambil data OTP dari cache ──
        $cached = Cache::get($cacheKey);

        if (!$cached) {
            return redirect()->route('suggestion.login')
                ->with('email_for_otp', $email)
                ->with('otp_sent', true)
                ->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.']);
        }

        // ── 2. Cek jumlah percobaan ──
        if ($cached['attempts'] >= self::MAX_ATTEMPTS) {
            Cache::forget($cacheKey);
            return redirect()->route('suggestion.login')
                ->withErrors(['email' => 'Terlalu banyak percobaan. Silakan minta kode OTP baru.']);
        }

        // ── 3. Verifikasi OTP ──
        if (!password_verify($request->otp, $cached['otp'])) {
            // Increment attempt counter
            $cached['attempts']++;
            Cache::put($cacheKey, $cached, now()->addMinutes(self::OTP_EXPIRE_MINUTES));

            $remaining = self::MAX_ATTEMPTS - $cached['attempts'];

            return redirect()->route('suggestion.login')
                ->with('email_for_otp', $email)
                ->with('otp_sent', true)
                ->withErrors(['otp' => "Kode OTP salah. Sisa percobaan: {$remaining}."]);
        }

        // ── 4. OTP valid → hapus dari cache ──
        Cache::forget($cacheKey);

        // ── 5. Ambil data user ──
        $user = User::where('email', $email)->firstOrFail();

        // ── 6. Update last login ──
        $user->update([
            'last_login' => now(),
            'last_ip'    => $request->ip(),
        ]);

        $dept = $user->departments->pluck('name')->toArray();

        // ── 7. Buat session suggestion (TERPISAH dari auth internal) ──
        Session::put('suggestion_user_id',   $user->id);
        Session::put('suggestion_user_name', $user->name);
        Session::put('suggestion_user_email',$user->email);
        Session::put('suggestion_dept', $dept);
        Session::regenerate();

return redirect()
    ->route('suggestion.dashboard')
    ->with('status', "Selamat datang, {$user->name}!");
    }

    // ================================================================
    // LOGOUT
    // ================================================================
    public function logout(Request $request): RedirectResponse
    {
        Session::forget([
            'suggestion_user_id',
            'suggestion_user_name',
            'suggestion_user_email',
        ]);

        Session::regenerate();

        return redirect()->route('suggestion.login')
            ->with('status', 'Anda telah berhasil keluar.');
    }

    // ================================================================
    // RESEND OTP (via AJAX / fetch)
    // ================================================================
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $email = strtolower(trim($request->email));

        // Cek cooldown
        $cooldownKey = 'otp_cooldown_' . md5($email);
        if (Cache::has($cooldownKey)) {
            return response()->json(['message' => 'Harap tunggu sebelum meminta kode baru.'], 429);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }

        $otp      = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'suggestion_otp_' . md5($email);

        Cache::put($cacheKey, [
            'otp'      => bcrypt($otp),
            'email'    => $email,
            'attempts' => 0,
        ], now()->addMinutes(self::OTP_EXPIRE_MINUTES));

        Cache::put($cooldownKey, true, now()->addSeconds(self::OTP_RESEND_COOLDOWN));

        Mail::to($email)->send(new \App\Mail\SuggestionOtpMail($otp, $user->name));

        return response()->json(['message' => 'Kode OTP berhasil dikirim ulang.']);
    }
}