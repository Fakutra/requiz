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
use Illuminate\Support\Facades\Validator;
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

            $allowedSorts = ['id', 'name', 'email', 'role'];
            if (! in_array($sort, $allowedSorts, true)) {
                $sort = 'name';
            }
            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

            $users = User::query()
                ->where('role', 'user')
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
        $search     = trim((string) $request->query('search'));
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $sort       = $request->query('sort', 'name');
        $direction  = $request->query('direction', 'asc');

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

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $applicantsQuery = Applicant::with(['position', 'batch', 'user'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhere('jurusan', 'ILIKE', "%{$search}%")
                        ->orWhereHas('position', function ($qp) use ($search) {
                            $qp->where('name', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->when($batchId, function ($q) use ($batchId) {
                $q->where('batch_id', $batchId);
            })
            ->when($positionId, function ($q) use ($positionId) {
                $q->where('position_id', $positionId);
            });

        $applicantsQuery->orderBy($sort, $direction);

        $applicants = $applicantsQuery
            ->paginate(15)
            ->appends($request->query());

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
        $tab = $request->query('tab', 'admin');

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
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan admin. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $user = User::create([
                'name'              => $validated['name'] ?? null,
                'email'             => $validated['email'] ?? null,
                'password'          => Hash::make($validated['password'] ?? ''),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]);

            ActivityLogger::log(
                'create',
                'User',
                auth()->user()->name . " menambahkan user baru '{$user->name}' (role: {$user->role}, email: {$user->email})",
                "User ID: {$user->id}"
            );

            return back()->with('success', 'Admin berhasil ditambahkan.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan admin. Silakan coba lagi.');
        }
    }

    /**
     * Tambah vendor baru (user dengan role vendor).
     */
    public function storeVendor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone_number'  => 'required|string|max:20',
            'vendor_id'     => 'required|exists:vendors,id',
            'password'      => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan admin eksternal. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $user = User::create([
                'name'              => $validated['name'] ?? null,
                'email'             => $validated['email'] ?? null,
                'phone_number'      => $validated['phone_number'] ?? null,
                'vendor_id'         => $validated['vendor_id'] ?? null,
                'password'          => Hash::make($validated['password'] ?? ''),
                'role'              => 'vendor',
                'email_verified_at' => now(),
            ]);

            ActivityLogger::log(
                'create',
                'User',
                auth()->user()->name . " menambahkan user vendor '{$user->name}' (role: {$user->role}, email: {$user->email})",
                "User ID: {$user->id}"
            );

            return back()->with('success', 'Vendor berhasil ditambahkan.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan admin eksternal. Silakan coba lagi.');
        }
    }

    /**
     * Update user (nama, email, password opsional).
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui user. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $oldData = $user->only(['name', 'email']);

            $user->update([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
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

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui user. Silakan coba lagi.');
        }
    }

    public function updateVendor(Request $request, User $user)
    {
        if ($user->role !== 'vendor') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui admin eksternal. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $oldData = $user->only(['name', 'email', 'phone_number', 'vendor_id']);

            $data = [
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'vendor_id'    => $validated['vendor_id'],
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

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui admin eksternal. Silakan coba lagi.');
        }
    }

    /**
     * Hapus user (admin maupun user biasa).
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
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

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.');
        }
    }
}
