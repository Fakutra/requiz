<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\SubField;
use Illuminate\Http\Request;

class SubFieldController extends Controller
{
    public function index()
    {
        $subfields = SubField::with('field')->orderBy('name')->get();
        $fields = Field::orderBy('name')->get(); // ⬅️ WAJIB

        return view('admin.master.subfields.index', compact('subfields', 'fields'));
    }

    public function create()
    {
        return view('admin.master.subfields.form', [
            'subfield' => new SubField(),
            'fields' => Field::orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
        ]);

        SubField::create($validated);

        return redirect()
            ->route('admin.subfields.index')
            ->with('success', 'Sub Bidang berhasil ditambahkan.');
    }

    public function edit(SubField $subfield)
    {
        return view('admin.master.subfields.form', [
            'subfield' => $subfield,
            'fields' => Field::orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, SubField $subfield)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
        ]);

        $subfield->update($validated);

        return redirect()
            ->route('admin.subfields.index')
            ->with('success', 'Sub Bidang berhasil diperbarui.');
    }

    public function destroy(SubField $subfield)
    {
        $subfield->delete();

        return redirect()
            ->route('admin.subfields.index')
            ->with('success', 'Sub Bidang berhasil dihapus.');
    }
}
