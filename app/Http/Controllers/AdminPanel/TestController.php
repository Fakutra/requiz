<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Test;
use App\Models\Position;
use App\Models\TestSection;
use Illuminate\Http\Request;
use App\Models\QuestionBundle;
use App\Http\Controllers\Controller;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class TestController extends Controller
{
    public function index()
    {
        $positions = Position::all();
        $tests = Test::withCount('sections')->orderBy('id', 'asc')->get();
        return view('admin.test.index', compact('tests', 'positions'));
    }

    public function show(Test $test)
    {
        $question_bundles = QuestionBundle::all();

        $test->load(['sections' => function ($query) {
            $query->orderBy('order', 'asc');
        }]);

        return view('admin.test.show', compact('test', 'question_bundles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'position_id'   => 'required|exists:positions,id',
            'nilai_minimum' => 'nullable|numeric|min:0', // ✅ tambahkan validasi ini
            'test_date'     => 'nullable|date',
            'test_closed'   => 'nullable|date|after_or_equal:test_date',
            'test_end'      => 'nullable|date|after_or_equal:test_closed',
        ]);

        Test::create($validated); // ✅ otomatis menyimpan nilai_minimum juga karena sudah ada di $fillable

        return redirect()->route('test.index')->with('success', 'New Quiz has been added!');
    }

    public function update(Request $request, Test $test)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'position_id'   => 'required|exists:positions,id',
            'nilai_minimum' => 'nullable|numeric|min:0', // ✅ tambahkan validasi ini
            'test_date'     => 'nullable|date',
            'test_closed'   => 'nullable|date|after_or_equal:test_date',
            'test_end'      => 'nullable|date|after_or_equal:test_closed',
        ]);

        $test->update($validatedData); // ✅ otomatis meng-update nilai_minimum juga

        return redirect()->route('test.index')->with('success', 'Quiz has been updated!');
    }

    public function destroy(Test $test)
    {
        try {
            $test->delete();
            return redirect()->route('test.index')->with('success', 'Quiz berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('test.index')->with('error', 'Gagal menghapus quiz. Error: '.$e->getMessage());
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Test::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
