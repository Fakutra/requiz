<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Placement;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function index()
    {
        $placements = Placement::orderBy('name')->get();

        return view('admin.master.placements.index', compact('placements'));
    }

    public function create()
    {
        return view('admin.master.placements.form', [
            'placement' => new Placement(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:placements,name',
        ]);

        Placement::create($validated);

        return redirect()
            ->route('admin.placements.index')
            ->with('success', 'Penempatan berhasil ditambahkan.');
    }

    public function edit(Placement $placement)
    {
        return view('admin.master.placements.form', [
            'placement' => $placement,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Placement $placement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:placements,name,' . $placement->id,
        ]);

        $placement->update($validated);

        return redirect()
            ->route('admin.placements.index')
            ->with('success', 'Penempatan berhasil diperbarui.');
    }

    public function destroy(Placement $placement)
    {
        $placement->delete();

        return redirect()
            ->route('admin.placements.index')
            ->with('success', 'Penempatan berhasil dihapus.');
    }
}
