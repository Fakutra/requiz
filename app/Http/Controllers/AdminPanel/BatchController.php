<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Batch;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class BatchController extends Controller
{
    public function index()
    {
        $batchs = Batch::with('position')->orderBy('id', 'asc')->get();
        return view('admin.batch.index', compact('batchs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'slug' => 'string',
            'status' => 'required',
            'start_date' => 'date',
            'end_date' => 'date',
        ]);

        Batch::create($validated);

        return redirect()->route('batch.index')->with('success', 'New Batch has been added!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $batchs = Batch::findOrFail($id);
        $batchs->update($validated);

        return redirect()->route('batch.index')->with('success', 'Batch has been updated!');
    }

    public function destroy($id)
    {
        $batchs = Batch::findOrFail($id);
        $batchs->delete();

        return redirect()->route('batch.index')->with('success', 'Batch has been deleted!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Batch::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
