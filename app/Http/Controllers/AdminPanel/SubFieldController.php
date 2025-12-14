<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\SubField;
use Illuminate\Http\Request;

class SubFieldController extends Controller
{
    public function index()
    {
        $subfields = SubField::orderBy('name')->get();

        return view('admin.master.subfields.index', compact('subfields'));
    }

    public function create()
    {
        return view('admin.master.subfields.form', [
            'subfield' => new SubField(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_fields,name',
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
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, SubField $subfield)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_fields,name,' . $subfield->id,
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
