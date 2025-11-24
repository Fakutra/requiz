<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        // 1. Proses autentikasi standar dari Breeze
        $request->authenticate();

        // 2. Regenerate session
        $request->session()->regenerate();

        $user = $request->user();

        // 3. Cek email sudah terverifikasi atau belum
        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            return redirect()
                ->route('verification.notice')
                ->withErrors([
                    'email' => 'Akun kamu belum diverifikasi. Silakan cek email kamu untuk verifikasi akun sebelum login.',
                ]);
        }

        // 4. Redirect sesuai role
        return match ($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'vendor' => redirect()->route('admin.dashboard'),   // vendor share dashboard admin (menu dibatesin di sidebar + middleware)
            'user'   => redirect()->route('welcome'),           // pastiin route welcome ada. kalau enggak, ganti ke route('dashboard')
            default  => redirect()->intended(RouteServiceProvider::HOME),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
