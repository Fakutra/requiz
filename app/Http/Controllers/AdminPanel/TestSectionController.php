<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\TestSection;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;

class TestSectionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id'            => 'required|exists:tests,id',
            'name'               => 'required|string|max:255',
            'category'           => ['required', Rule::in(TestSection::CATEGORIES)],
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes'   => 'required|integer|min:1',
            'shuffle_questions'  => 'sometimes|boolean',
            'shuffle_options'    => 'sometimes|boolean',
        ]);

        // ❌ VALIDATION FAIL
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan section. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $validated['shuffle_questions'] = $request->has('shuffle_questions');
            $validated['shuffle_options']   = $request->has('shuffle_options');

            // hitung urutan otomatis
            $maxOrder = TestSection::where('test_id', $validated['test_id'])->max('order');
            $validated['order'] = ($maxOrder ?? 0) + 1;

            $section = TestSection::create($validated);
            $test    = Test::find($validated['test_id']);

            ActivityLogger::log(
                'create',
                'Test Section',
                auth()->user()->name . " menambahkan section baru '{$section->name}' pada Test '{$test->name}'",
                "Section ID: {$section->id}"
            );

            return redirect()
                ->route('test.show', $test)
                ->with('success', 'Section baru berhasil ditambahkan!');

        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan section. Silakan coba lagi.');
        }
    }

    public function update(Request $request, TestSection $section)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'category'           => ['required', Rule::in(TestSection::CATEGORIES)],
            'question_bundle_id' => 'nullable|exists:question_bundles,id',
            'duration_minutes'   => 'required|integer|min:1',
            'shuffle_questions'  => 'sometimes|boolean',
            'shuffle_options'    => 'sometimes|boolean',
            'order'              => 'required|integer|min:1',
        ]);

        // ❌ VALIDATION FAIL
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui section. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $validated['shuffle_questions'] = $request->has('shuffle_questions');
            $validated['shuffle_options']   = $request->has('shuffle_options');

            // simpan data lama untuk log
            $oldData = $section->toArray();

            $section->update($validated);
            $section->refresh();

            ActivityLogger::logUpdate(
                'Test Section',
                $section,
                $oldData,
                $section->toArray()
            );

            return redirect()
                ->route('test.show', $section->test)
                ->with('success', 'Section berhasil diperbarui!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui section. Silakan coba lagi.');
        }
    }

    public function destroy(TestSection $section)
    {
        try {
            $test = $section->test;
            $name = $section->name;
            $id   = $section->id;

            $section->delete();

            ActivityLogger::log(
                'delete',
                'Test Section',
                auth()->user()->name . " menghapus section '{$name}' dari Test '{$test->name}'",
                "Section ID: {$id}"
            );

            return redirect()
                ->route('test.show', $test)
                ->with('success', 'Section berhasil dihapus!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus section. Silakan coba lagi.');
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(TestSection::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
