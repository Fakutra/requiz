<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::orderBy('name')->get();

        return view('admin.master.fields.index', compact('fields'));
    }

    public function create()
    {
        return view('admin.master.fields.form', [
            'field' => new Field(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fields,name',
        ]);

        Field::create($validated);

        return redirect()
            ->route('admin.fields.index')
            ->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function edit(Field $field)
    {
        return view('admin.master.fields.form', [
            'field' => $field,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Field $field)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fields,name,' . $field->id,
        ]);

        $field->update($validated);

        return redirect()
            ->route('admin.fields.index')
            ->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Field $field)
    {
        $field->delete();

        return redirect()
            ->route('admin.fields.index')
            ->with('success', 'Bidang berhasil dihapus.');
    }
}
