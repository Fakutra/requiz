<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Tampilkan form edit profil.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile; // relasi dari User ke Profile

        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update data profil pengguna.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'identity_num' => 'nullable|digits_between:1,16|numeric',
            'phone_number' => 'nullable|digits_between:1,15|numeric',
            'birthplace' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string|max:255',
        ]);

        // ✅ Update tabel users
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // ✅ Update atau buat profile baru jika belum ada
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'identity_num' => $validated['identity_num'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'birthplace' => $validated['birthplace'] ?? null,
                'birthdate' => $validated['birthdate'] ?? null,
                'address' => $validated['address'] ?? null,
            ]
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
