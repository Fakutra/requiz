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
        $user = Auth::user();

        // 1. Ambil input nomor telepon dulu untuk dimanipulasi
        $phone = $request->phone_number;

        if ($phone) {
            // Hapus karakter non-angka (spasi, strip, dll)
            $phone = preg_replace('/[^0-9]/', '', $phone);

            // Kalau user ngetik "0812...", hapus angka "0" di depan
            if (str_starts_with($phone, '0')) {
                $phone = substr($phone, 1);
            }

            // Kalau user ngetik "62812...", hapus angka "62" di depan 
            // supaya nggak double pas kita pasang prefix nanti
            if (str_starts_with($phone, '62')) {
                $phone = substr($phone, 2);
            }

            // Simpan kembali ke request agar masuk validasi dengan format bersih
            $request->merge(['phone_number' => $phone]);
        }

        // ✅ Validasi input (phone_number sekarang divalidasi tanpa prefix)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'identity_num' => 'nullable|numeric|digits_between:1,16',
            'phone_number' => 'nullable|numeric|digits_between:8,13', // Standar nomor HP Indo
            'birthplace' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string|max:255',
        ]);

        // ✅ Update tabel users
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // ✅ Olah phone_number: tambahkan prefix +62 sebelum simpan
        $finalPhoneNumber = $validated['phone_number'] ? '62' . $validated['phone_number'] : null;

        // ✅ Update atau buat profile baru
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'identity_num' => $validated['identity_num'] ?? null,
                'phone_number' => $finalPhoneNumber, // Pakai yang sudah ada 62
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
