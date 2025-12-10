<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Models\User;
use App\Models\Vendor;
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
        $tab = $request->query('tab', 'registered');

        /**
         * =========================
         * TAB: REGISTERED USER
         * =========================
         */
        if ($tab === 'registered') {
            $search    = trim((string) $request->query('search'));
            $sort      = $request->query('sort', 'name');
            $direction = $request->query('direction', 'asc');

            // kolom yang boleh di-sort
            $allowedSorts = ['id', 'name', 'email', 'role'];

            if (! in_array($sort, $allowedSorts, true)) {
                $sort = 'name';
            }

            // normalisasi direction
            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

            $users = User::query()
                ->where('role', 'user') // hanya role 'user' di tab Registered
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'ILIKE', "%{$search}%")
                            ->orWhere('email', 'ILIKE', "%{$search}%");
                    });
                })
                ->orderBy($sort, $direction)
                ->paginate(15)
                ->appends($request->query());

            return view('admin.user.index', [
                'tab'        => $tab,
                'users'      => $users,
                'search'     => $search,
                // supaya view tidak error saat akses variabel di tab lain
                'applicants' => collect(),
                'positions'  => collect(),
                'batches'    => collect(),
            ]);
        }


        /**
         * =========================
         * TAB: APPLICANT USER
         * =========================
         */
        // parameter dari query string
        $search    = trim((string) $request->query('search'));
        $batchId   = $request->query('batch');     // filter batch
        $positionId= $request->query('position');  // filter posisi
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        // whitelist kolom yang boleh di-sort
        $allowedSorts = [
            'name',
            'email',
            'position_id',
            'ekspektasi_gaji',
            'umur',
            'pendidikan',
            'jurusan',
            'batch_id',
        ];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        // normalisasi direction
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $applicantsQuery = Applicant::with(['position', 'batch', 'user'])
            // SEARCH: Nama / Email / Jurusan / Posisi
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    // jika kolom name/email ada di tabel applicants
                    $qq->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhere('jurusan', 'ILIKE', "%{$search}%")
                        // cari dari relasi position
                        ->orWhereHas('position', function ($qp) use ($search) {
                            $qp->where('name', 'ILIKE', "%{$search}%");
                        });
                });
            })
            // FILTER: Batch
            ->when($batchId, function ($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            })
            // FILTER: Position
            ->when($positionId, function ($q) use ($positionId) {
                $q->where('position_id', $positionId);
            });

        // SORTING
        $applicantsQuery->orderBy($sort, $direction);

        $applicants = $applicantsQuery
            ->paginate(15)
            ->appends($request->query());

        // data referensi untuk filter di modal
        $positions = Position::orderBy('name')->get();
        $batches   = Batch::orderBy('name')->get();

        return view('admin.user.index', [
            'tab'        => $tab,
            'users'      => collect(),
            'applicants' => $applicants,
            'positions'  => $positions,
            'batches'    => $batches,
            'search'     => $search,
        ]);
    }


    public function admin(Request $request)
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
            ->when($role === 'vendor', fn ($q) => $q->with('vendor'))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $vendors = Vendor::orderBy('nama_vendor')->get();

        return view('admin.administrator.index', compact('users', 'search', 'tab', 'vendors'));
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
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone_number'  => 'required|string|max:20',      // nomor telp
            'vendor_id'     => 'required|exists:vendors,id',  // pastiin vendor ada
            'password'      => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone_number'      => $validated['phone_number'],
            'vendor_id'         => $validated['vendor_id'],
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

    public function updateVendor(Request $request, User $user)
    {
        // pastikan yang di-edit emang vendor
        if ($user->role !== 'vendor') {
            abort(404);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone_number' => 'required|string|max:20',
            'vendor_id'    => 'required|exists:vendors,id',
            'password'     => 'nullable|min:6|confirmed',
        ]);

        $oldData = $user->only(['name', 'email', 'phone_number', 'vendor_id']);

        $data = [
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'vendor_id'    => $validated['vendor_id'],
            // role tetap 'vendor'
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLogger::logUpdate(
            'User',
            $user,
            $oldData,
            $user->only(['name', 'email', 'phone_number', 'vendor_id'])
        );

        if (!empty($validated['password'])) {
            ActivityLogger::log(
                'update',
                'User',
                auth()->user()->name . " mengubah password user vendor '{$user->name}'",
                "User ID: {$user->id}, Vendor ID: {$user->vendor_id}"
            );
        }

        return back()->with('success', 'Admin eksternal berhasil diperbarui.');
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
