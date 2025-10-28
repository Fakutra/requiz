<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Test;
use App\Models\Position;
use App\Models\TestSection;
use Illuminate\Http\Request;
use App\Models\QuestionBundle;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;

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
            'nilai_minimum' => 'nullable|numeric|min:0',
            'test_date'     => 'nullable|date',
            'test_closed'   => 'nullable|date|after_or_equal:test_date',
            'test_end'      => 'nullable|date|after_or_equal:test_closed',
        ]);

        $test = Test::create($validated);

        // ✅ LOG CREATE
        ActivityLogger::log(
            'create',
            'Test',
            auth()->user()->name . " membuat test baru: '{$test->name}'",
            "Test ID: {$test->id}"
        );

        return redirect()->route('test.index')->with('success', 'New Quiz has been added!');
    }

    public function update(Request $request, Test $test)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'position_id'   => 'required|exists:positions,id',
            'nilai_minimum' => 'nullable|numeric|min:0',
            'test_date'     => 'nullable|date',
            'test_closed'   => 'nullable|date|after_or_equal:test_date',
            'test_end'      => 'nullable|date|after_or_equal:test_closed',
        ]);

        // ✅ Ambil data lama sebelum update (untuk referensi log)
        $oldData = $test->only(['name', 'position_id', 'nilai_minimum', 'test_date', 'test_closed', 'test_end']);

        // ✅ Lakukan update
        $test->update($validatedData);

        // ✅ Ambil hanya field yang berubah
        $changes = $test->getChanges();

        if (!empty($changes)) {
            // Format perubahan
            $changeList = collect($changes)->map(function ($new, $key) use ($oldData) {
                $old = $oldData[$key] ?? '(kosong)';

                // Format tanggal agar rapi
                if (str_contains($key, 'date') || str_contains($key, 'closed') || str_contains($key, 'end')) {
                    try {
                        $old = $old ? \Carbon\Carbon::parse($old)->format('Y-m-d') : '(kosong)';
                        $new = $new ? \Carbon\Carbon::parse($new)->format('Y-m-d') : '(kosong)';
                    } catch (\Exception $e) {
                        // Abaikan jika parsing gagal
                    }
                }

                return "{$key}: '{$old}' → '{$new}'";
            })->implode(', ');

            // ✅ Catat log aktivitas update
            ActivityLogger::log(
                'update',
                'Test',
                (auth()->user()->name ?? 'System') .
                " memperbarui data Test '{$test->name}' — {$changeList}",
                "Test ID: {$test->id}"
            );
        }

        return redirect()->route('test.index')->with('success', 'Quiz has been updated!');
    }

    public function destroy(Test $test)
    {
        try {
            $name = $test->name;

            $test->delete();

            // ✅ LOG DELETE
            ActivityLogger::log(
                'delete',
                'Test',
                auth()->user()->name . " menghapus test: '{$name}'",
                "Test ID: {$test->id}"
            );

            return redirect()->route('test.index')->with('success', 'Quiz berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('test.index')->with('error', 'Gagal menghapus quiz. Error: ' . $e->getMessage());
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Test::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
