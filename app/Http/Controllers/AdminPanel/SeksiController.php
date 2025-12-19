<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Seksi;
use App\Models\SubField;
use Illuminate\Http\Request;

class SeksiController extends Controller
{
    public function index()
    {
        $subfields = SubField::with('field')->orderBy('name')->get();
        $seksi = Seksi::with('subField.field')->orderBy('name')->get();

        return view('admin.master.seksi.index', compact('subfields', 'seksi'));
    }



    public function create()
    {
        return view('admin.master.seksi.form', [
            'seksi' => new Seksi(),
            'subfields' => SubField::orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_field_id' => 'required|exists:sub_fields,id',
            'name' => 'required|string|max:255',
        ]);

        Seksi::create($validated);

        return redirect()
            ->route('admin.seksi.index')
            ->with('success', 'Seksi berhasil ditambahkan.');
    }

    public function edit(Seksi $seksi)
    {
        return view('admin.master.seksi.form', [
            'seksi' => $seksi,
            'subfields' => SubField::orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Seksi $seksi)
    {
        $validated = $request->validate([
            'sub_field_id' => 'required|exists:sub_fields,id',
            'name' => 'required|string|max:255',
        ]);

        $seksi->update($validated);

        return redirect()
            ->route('admin.seksi.index')
            ->with('success', 'Seksi berhasil diperbarui.');
    }

    public function destroy(Seksi $seksi)
    {
        $seksi->delete();

        return redirect()
            ->route('admin.seksi.index')
            ->with('success', 'Seksi berhasil dihapus.');
    }
}
