<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use App\Models\Position;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('id', 'asc')->get();
        return view('admin.position.index', compact('positions'));
    }

    public function create($slug)
    {
        $batchs = Batch::where('slug', $slug)->first();
        return view('admin.batch.position.create', compact('batchs'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'slug' => 'string',
            'quota' => 'required|integer',
            'status' => 'required',
            'description' => 'required',
        ]);

        Position::create($validated);

        return redirect()->route('position.index')->with('success', 'New Position has been added!');
    }

    public function edit($id)
    {
        $positions = Position::findOrFail($id);
        return view('admin.position.edit', compact('positions'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quota' => 'required|integer',
            'status' => 'required',
            'description' => 'required',
        ]);

        $position = Position::findOrFail($id);

        // Deteksi perubahan name agar slug bisa regenerate
        if ($validated['name'] !== $position->name) {
            $position->slug = null; // trigger slug regeneration
        }

        $position->update($validated);

        return redirect()->route('position.index')->with('success', 'Position has been updated!');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->route('position.index')->with('success', 'Position has been deleted!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Position::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
