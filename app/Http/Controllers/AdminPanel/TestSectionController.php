<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\TestSection;
use App\Models\Test; // Add this import statement
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class TestSectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'name' => 'required|string|max:255',
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes' => 'required|integer|min:1',
            'shuffle_questions' => 'sometimes|boolean',
            'shuffle_options' => 'sometimes|boolean',
        ]);
        
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_options'] = $request->has('shuffle_options');
        
        // --- LOGIKA PENAMBAHAN ORDER ---
        // Cari nilai order tertinggi untuk test_id yang sama
        $maxOrder = TestSection::where('test_id', $validated['test_id'])->max('order');
        // Tetapkan nilai order baru: maxOrder + 1 (jika belum ada, akan jadi 1)
        $validated['order'] = ($maxOrder ?? 0) + 1;
        // --- AKHIR LOGIKA PENAMBAHAN ORDER ---

        TestSection::create($validated);

        return redirect()->route('test.show', $request->test)->with('success', 'Section baru berhasil ditambahkan!');
    }

    public function update(Request $request, TestSection $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes' => 'required|integer|min:1',
            'shuffle_questions' => 'sometimes|boolean',
            'shuffle_options' => 'sometimes|boolean',
            'order' => 'required|integer|min:1', // Tambahkan validasi untuk order
        ]);
        
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_options'] = $request->has('shuffle_options');
        
        $section->update($validated);

        return redirect()->route('test.show', $section->test)->with('success', 'Section berhasil diperbarui!');
    }

    public function destroy(TestSection $section)
    {
        $test = $section->test; // Simpan test parent sebelum section dihapus
        $section->delete();

        return redirect()->route('test.show', $test)->with('success', 'Section berhasil dihapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(TestSection::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}