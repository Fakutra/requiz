<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogger;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (dipisah via tab: admin / user).
     */
    public function index(Request $request)
    {
        // tab: 'admin', 'user', atau 'vendor' (default: admin)
        $tab = $request->query('tab', 'admin');

        // mapping tab -> role
        $role = match ($tab) {
            'user'   => 'user',
            'vendor' => 'vendor',
            default  => 'admin',
        };

        $search = trim((string) $request->query('search'));

        $users = User::where('role', $role)
            ->when($search, fn ($q) =>
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
                })
            )
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.user.index', compact('users', 'search', 'tab'));
    }

    /**
     * Tambah admin baru.
     * (Hanya dipakai di TAB "Kelola Admin"; role dipaksa 'admin')
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => 'admin', // selalu admin dari halaman ini
            'email_verified_at' => now(),
        ]);

        ActivityLogger::log(
            'create',
            'User',
            auth()->user()->name . " menambahkan user baru '{$user->name}' (role: {$user->role}, email: {$user->email})",
            "User ID: {$user->id}"
        );

        return back()->with('success', 'Admin berhasil ditambahkan.');
    }

    /**
     * Tambah vendor baru.
     * (Dipakai di TAB "Vendor"; role dipaksa 'vendor')
     */
    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => 'vendor', // selalu vendor dari halaman ini
            'email_verified_at' => now(),
        ]);

        ActivityLogger::log(
            'create',
            'User',
            auth()->user()->name . " menambahkan user vendor '{$user->name}' (role: {$user->role}, email: {$user->email})",
            "User ID: {$user->id}"
        );

        return back()->with('success', 'Vendor berhasil ditambahkan.');
    }

    /**
     * Update user (nama, email, password opsional).
     * Dipakai dari kedua tab (admin & user).
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
        ]);

        $oldData = $user->only(['name', 'email']);

        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            // role TIDAK diubah di halaman ini
            'password' => $validated['password']
                ? Hash::make($validated['password'])
                : $user->password,
        ]);

        ActivityLogger::logUpdate(
            'User',
            $user,
            $oldData,
            $user->only(['name', 'email'])
        );

        if (!empty($validated['password'])) {
            ActivityLogger::log(
                'update',
                'User',
                auth()->user()->name . " mengubah password user '{$user->name}'",
                "User ID: {$user->id}"
            );
        }

        return back()->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user (admin maupun user biasa).
     */
    public function destroy(User $user)
    {
        // optional: cegah user hapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName  = $user->name;
        $userEmail = $user->email;
        $userRole  = $user->role;
        $userId    = $user->id;

        $user->delete();

        ActivityLogger::log(
            'delete',
            'User',
            auth()->user()->name . " menghapus user '{$userName}' (role: {$userRole}, email: {$userEmail})",
            "User ID: {$userId}"
        );

        return back()->with('success', 'User berhasil dihapus.');
    }
}
