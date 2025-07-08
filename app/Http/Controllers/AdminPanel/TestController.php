<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Test;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::with('position')->orderBy('created_at', 'asc')->get();
        $positions = Position::orderBy('name')->get();

        return view('admin.test.index', compact('tests', 'positions'));
    }



    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'slug' => 'string',
            'position_id' => 'required',
        ]);

        Test::create($validated);

        return redirect()->route('test.index')->with('success', 'New Quiz has been added!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position_id' => 'required',
            // 'status' => 'required',
            // 'description' => 'required',
        ]);

        $test = Test::findOrFail($id);

        // Deteksi perubahan name agar slug bisa regenerate
        if ($validated['name'] !== $test->name) {
            $test->slug = null; // trigger slug regeneration
        }

        $test->update($validated);

        return redirect()->route('test.index')->with('success', 'Quiz has been updated!');
    }

    public function destroy($id)
    {
        $test = Test::findOrFail($id);
        $test->delete();

        return redirect()->route('test.index')->with('success', 'Quiz has been deleted!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Test::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }

}


// public function update(Request $request, Test $test)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'type' => 'required',
    //     ]);

    //     $test->update($request->all());
    //     return back()->with('success', 'Test berhasil diperbarui');
    // }

    // public function destroy(Test $test)
    // {
    //     $test->delete();
    //     return back()->with('success', 'Test berhasil dihapus');
    // }