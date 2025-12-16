<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $vendors = Vendor::orderBy('nama_vendor')->paginate(10);
        return view('admin.vendor.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        // manual validator biar bisa munculin notif error
        $validator = Validator::make($request->all(), [
            'nama_vendor'    => ['required', 'string', 'max:255'],
            'alamat'         => ['nullable', 'string'],
            'email'          => ['nullable', 'email', 'max:255'],
            'nomor_telepon'  => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan vendor. Periksa kembali data yang diinput.');
        }

        try {
            Vendor::create($validator->validated());

            return redirect()
                ->route('admin.vendor.index')
                ->with('success', 'Vendor berhasil ditambahkan.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan vendor. Silakan coba lagi.');
        }
    }

    public function edit(Vendor $vendor)
    {
        return view('admin.vendor.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validator = Validator::make($request->all(), [
            'nama_vendor'    => ['required', 'string', 'max:255'],
            'alamat'         => ['nullable', 'string'],
            'email'          => ['nullable', 'email', 'max:255'],
            'nomor_telepon'  => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui vendor. Periksa kembali data yang diinput.');
        }

        try {
            $vendor->update($validator->validated());

            return redirect()
                ->route('admin.vendor.index')
                ->with('success', 'Vendor berhasil diupdate.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui vendor. Silakan coba lagi.');
        }
    }

    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();

            return redirect()
                ->route('admin.vendor.index')
                ->with('success', 'Vendor berhasil dihapus.');

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.vendor.index')
                ->with('error', 'Terjadi kesalahan saat menghapus vendor. Silakan coba lagi.');
        }
    }
}
