<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $vendors = Vendor::orderBy('nama_vendor')->paginate(10);

        return view('admin.vendor.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:255'],
            'pic'         => ['nullable', 'string', 'max:255'],
            'kontak'      => ['nullable', 'string', 'max:255'],
        ]);

        Vendor::create($validated);

        return redirect()
            ->route('admin.vendor.index')
            ->with('status', 'Vendor berhasil ditambahkan.');
    }

    // ✨ Form edit
    public function edit(Vendor $vendor)
    {
        return view('admin.vendor.edit', compact('vendor'));
    }

    // ✨ Proses update
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:255'],
            'pic'         => ['nullable', 'string', 'max:255'],
            'kontak'      => ['nullable', 'string', 'max:255'],
        ]);

        $vendor->update($validated);

        return redirect()
            ->route('admin.vendor.index')
            ->with('status', 'Vendor berhasil diupdate.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()
            ->route('admin.vendor.index')
            ->with('status', 'Vendor berhasil dihapus.');
    }
}
