<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VendorPasswordController extends Controller
{
    public function edit()
    {
        return view('admin.vendor.password');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // manual validator supaya bisa pakai ->with('error')
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.min'                      => 'Password minimal 8 karakter.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui password. Periksa kembali data yang diinput.');
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Password berhasil diperbarui.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat memperbarui password. Silakan coba lagi.');
        }
    }
}
