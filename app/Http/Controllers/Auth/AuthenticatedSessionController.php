<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

         // ====== SIMPAN LAST LOGIN & LAST IP ======
    $user = Auth::user();
    $user->last_login = now();
    $user->last_ip = $request->ip();
    $user->save();
    // ========================================

$hasRole = $user->roles->pluck('name')->contains('Operator Level Access');
$hasDepartment = $user->departments->pluck('name')->contains('Quality Control');

if ($hasRole && $hasDepartment) {
    return redirect()->route('qc.inspections.create');
}


        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
