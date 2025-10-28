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
     * Tampilkan daftar user admin.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $users = User::where('role', 'admin')
            ->when($search, fn($q) =>
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%$search%")
                      ->orWhere('email', 'ILIKE', "%$search%");
                })
            )
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.user.index', compact('users', 'search'));
    }

    /**
     * Tambah admin baru.
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
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // ✅ Log CREATE
        ActivityLogger::log(
            'create',
            'User',
            auth()->user()->name . " menambahkan admin baru '{$user->name}' (email: {$user->email})",
            "User ID: {$user->id}"
        );

        return back()->with('success', 'Admin berhasil ditambahkan.');
    }

    /**
     * Update admin.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'admin') abort(403);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
        ]);

        $oldData = $user->only(['name', 'email']);

        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password']
                ? Hash::make($validated['password'])
                : $user->password,
        ]);

        // ✅ Log UPDATE
        ActivityLogger::logUpdate(
            'User',
            $user,
            $oldData,
            $user->only(['name', 'email'])
        );

        // ✅ Log khusus jika password diubah
        if (!empty($validated['password'])) {
            ActivityLogger::log(
                'update',
                'User',
                auth()->user()->name . " mengubah password admin '{$user->name}'",
                "User ID: {$user->id}"
            );
        }

        return back()->with('success', 'Admin berhasil diperbarui.');
    }

    /**
     * Hapus admin.
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'admin') abort(403);

        $userName = $user->name;
        $userEmail = $user->email;
        $userId = $user->id;

        $user->delete();

        // ✅ Log DELETE
        ActivityLogger::log(
            'delete',
            'User',
            auth()->user()->name . " menghapus admin '{$userName}' (email: {$userEmail})",
            "User ID: {$userId}"
        );

        return back()->with('success', 'Admin berhasil dihapus.');
    }
}
