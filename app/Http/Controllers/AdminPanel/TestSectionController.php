<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\TestSection;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;

class TestSectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'test_id'            => 'required|exists:tests,id',
            'name'               => 'required|string|max:255',
            'category'           => ['required', Rule::in(TestSection::CATEGORIES)], // ⬅️ kategori wajib & terkontrol
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes'   => 'required|integer|min:1',
            'shuffle_questions'  => 'sometimes|boolean',
            'shuffle_options'    => 'sometimes|boolean',
        ]);
        
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_options']   = $request->has('shuffle_options');

        // --- HITUNG ORDER (urutan tampil di quiz) ---
        $maxOrder = TestSection::where('test_id', $validated['test_id'])->max('order');
        $validated['order'] = ($maxOrder ?? 0) + 1;

        // --- CREATE SECTION ---
        $section = TestSection::create($validated);
        $test    = Test::find($validated['test_id']);

        // ✅ LOG CREATE
        ActivityLogger::log(
            'create',
            'Test Section',
            auth()->user()->name . " menambahkan section baru '{$section->name}' pada Test '{$test->name}'",
            "Section ID: {$section->id}"
        );

        return redirect()
            ->route('test.show', $test)
            ->with('success', 'Section baru berhasil ditambahkan!');
    }

    public function update(Request $request, TestSection $section)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'category'           => ['required', Rule::in(TestSection::CATEGORIES)], // ⬅️ bisa ganti kategori
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes'   => 'required|integer|min:1',
            'shuffle_questions'  => 'sometimes|boolean',
            'shuffle_options'    => 'sometimes|boolean',
            'order'              => 'required|integer|min:1', // urutan pengerjaan di quiz
        ]);
        
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_options']   = $request->has('shuffle_options');

        // ✅ LOG DATA LAMA
        $oldData = $section->toArray();

        // UPDATE
        $section->update($validated);
        $section->refresh();

        // ✅ LOG DATA BARU
        $newData = $section->toArray();

        // ✅ LOG UPDATE
        ActivityLogger::logUpdate(
            'Test Section',
            $section,
            $oldData,
            $newData
        );

        return redirect()
            ->route('test.show', $section->test)
            ->with('success', 'Section berhasil diperbarui!');
    }

    public function destroy(TestSection $section)
    {
        $test = $section->test;
        $name = $section->name;
        $id   = $section->id;

        $section->delete();

        // ✅ LOG DELETE
        ActivityLogger::log(
            'delete',
            'Test Section',
            auth()->user()->name . " menghapus section '{$name}' dari Test '{$test->name}'",
            "Section ID: {$id}"
        );

        return redirect()
            ->route('test.show', $test)
            ->with('success', 'Section berhasil dihapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(TestSection::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
