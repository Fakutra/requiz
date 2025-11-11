<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Profile;
use App\Models\Skregis;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function create(): View
    {
        $sk = Skregis::orderBy('id', 'asc')->get();
        return view('auth.register', compact('sk'));
    }

    /**
     * Proses penyimpanan data registrasi.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Kamu harus menyetujui Syarat & Ketentuan terlebih dahulu.',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Buat profile kosong otomatis
        Profile::create([
            'user_id' => $user->id,
        ]);

        // Kirim email verifikasi
        event(new Registered($user));

        // Login langsung supaya bisa akses halaman verifikasi
        Auth::login($user);

        // Redirect ke halaman verifikasi email
        return redirect()->route('verification.notice');
    }
}
