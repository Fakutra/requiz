<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_identitas'  => ['required', 'string', 'max:20', 'unique:users,identity_num'],
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'no_telp'       => [
                'required',
                'string',
                'max:20',
                'regex:/^(?:\+?62|62|0)8[1-9][0-9]{6,11}$/',
            ],
            'tpt_lahir'  => ['required', 'string', 'max:100'],
            'tgl_lahir'  => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'alamat_ktp' => ['required', 'string', 'max:1000'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'terms'      => ['accepted'],
        ], [
            'terms.accepted' => 'Kamu harus menyetujui Syarat & Ketentuan terlebih dahulu.',
        ]);

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone_number'  => $validated['no_telp'],
            'birthplace'    => $validated['tpt_lahir'],
            'birthdate'     => $validated['tgl_lahir'],   // akan di-cast ke date kalau diset di model
            'address'       => $validated['alamat_ktp'],
            'identity_num'  => $validated['no_identitas'],
            'password'      => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        //Auth::login($user);

        return redirect()->route('login')->with('status', 'Registrasi berhasil! Silakan login');
    }
}
