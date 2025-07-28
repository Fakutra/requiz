<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\TestSection;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class TestSectionController extends Controller
{
    public function store(Request $request)
    {
        // Sesuaikan aturan validasi
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'name' => 'required|string|max:255', // diubah dari 'title'
            'type' => 'required|in:pg,multiple,poin,essay', // sesuaikan dengan enum
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes' => 'required|integer|min:1', // diubah dari 'duration'
            'shuffle_questions' => 'sometimes|boolean',
            'shuffle_options' => 'sometimes|boolean',
        ]);
        
        // Konversi checkbox jika tidak dicentang
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_options'] = $request->has('shuffle_options');

        TestSection::create($validated);

        return redirect()->route('test.show', $request->test)->with('success', 'Section baru berhasil ditambahkan!');
    }

    public function update(Request $request, TestSection $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pg,multiple,poin,essay',
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes' => 'required|integer|min:1',
            'shuffle_questions' => 'sometimes|boolean',
            'shuffle_options' => 'sometimes|boolean',
        ]);
        
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_options'] = $request->has('shuffle_options');

        $section->update($validated);

        return redirect()->route('test.show', $section->test)->with('success', 'Section berhasil diperbarui!');
    }

    public function destroy(TestSection $section)
    {
        $testId = $section->test;
        $section->delete();
        return redirect()->route('test.show', $testId)->with('success', 'Section berhasil dihapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(TestSection::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}