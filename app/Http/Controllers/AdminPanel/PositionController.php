<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('id', 'asc')->get();
        return view('admin.position.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.position.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quota' => 'required|integer',
            'status' => 'required',
            'description' => 'required',
        ]);

        $validated['description'] = strip_tags($request->description);

        Position::create($validated);

        return redirect()->route('position.index')->with('success', 'New Position has been added!');
    }

    public function edit($id)
    {
        $positions = Position::findOrFail($id);
        return view('admin.position.edit', compact('positions'));
    }

    public function update(Request $request, Position $positions, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quota' => 'required|integer',
            'status' => 'required',
            'description' => 'required',
        ]);

        // $validated['description'] = strip_tags($request->description);

        $positions = Position::findOrFail($id);
        $positions->update($validated);
        // Position::where('id', $positions->id)
        //     ->update($validated);

        // dd($validated);

        return redirect()->route('position.index')->with('success', 'Position has been updated!');
    }

    public function ContactView($id)
    {
        //
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->route('position.index')->with('success', 'Position has been deleted!');
    }

}
