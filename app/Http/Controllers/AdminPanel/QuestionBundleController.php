<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuestionBundle;
use App\Models\Question;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Validator;

class QuestionBundleController extends Controller
{
    public function index()
    {
        $bundles = QuestionBundle::withCount('questions')->latest()->paginate(9);
        return view('admin.bundle.index', compact('bundles'));
    }

    public function show(QuestionBundle $bundle)
    {
        $questionsInBundle     = $bundle->questions()->paginate(10);
        $existingQuestionIds   = $bundle->questions()->pluck('questions.id');
        $availableQuestions    = Question::whereNotIn('id', $existingQuestionIds)->get();
        $categories            = $availableQuestions->pluck('category')->unique()->sort();

        return view('admin.bundle.show', compact(
            'bundle',
            'questionsInBundle',
            'availableQuestions',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:question_bundles,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal membuat bundle. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();

            $bundle              = new QuestionBundle();
            $bundle->name        = $data['name'];
            $bundle->description = $data['description'] ?? null;
            $bundle->save();

            ActivityLogger::log(
                'create',
                'Question Bundle',
                auth()->user()->name . " membuat bundle baru: '{$bundle->name}'",
                "Bundle ID: {$bundle->id}"
            );

            return redirect()
                ->route('bundle.index')
                ->with('success', 'Bundle baru berhasil dibuat!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat bundle. Silakan coba lagi.');
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(QuestionBundle::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }

    public function update(Request $request, QuestionBundle $bundle)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui bundle. Periksa kembali data yang diinput.');
        }

        try {
            $data    = $validator->validated();
            $oldData = $bundle->toArray();

            $bundle->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $bundle->refresh();
            $newData = $bundle->toArray();

            ActivityLogger::logUpdate('Question Bundle', $bundle, $oldData, $newData);

            return redirect()
                ->route('bundle.index')
                ->with('success', 'Bundle berhasil diperbarui!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui bundle. Silakan coba lagi.');
        }
    }

    public function destroy(QuestionBundle $bundle)
    {
        try {
            ActivityLogger::log(
                'delete',
                'Question Bundle',
                auth()->user()->name . " menghapus bundle: '{$bundle->name}'",
                "Bundle ID: {$bundle->id}"
            );

            $bundle->delete();

            return redirect()
                ->route('bundle.index')
                ->with('success', 'Bundle berhasil dihapus!');

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('bundle.index')
                ->with('error', 'Terjadi kesalahan saat menghapus bundle. Silakan coba lagi.');
        }
    }

    public function addQuestion(Request $request, QuestionBundle $bundle)
    {
        $validator = Validator::make($request->all(), [
            'question_ids'   => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ], [
            'question_ids.required' => 'Minimal pilih satu soal terlebih dahulu.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan soal ke bundle. Periksa kembali pilihan soal.');
        }

        try {
            $data = $validator->validated();

            $existingQuestionIds = $bundle->questions()->pluck('questions.id')->toArray();
            $newQuestionIds      = array_diff($data['question_ids'], $existingQuestionIds);

            if (!empty($newQuestionIds)) {
                $bundle->questions()->attach($newQuestionIds);

                ActivityLogger::log(
                    'attach',
                    'Question Bundle',
                    auth()->user()->name . " menambahkan " . count($newQuestionIds) . " soal ke bundle '{$bundle->name}'",
                    "Bundle ID: {$bundle->id}"
                );

                return back()->with('success', count($newQuestionIds) . ' soal berhasil ditambahkan!');
            }

            // semua soal yang dipilih sudah ada di bundle
            return back()->with('error', 'Semua soal yang dipilih sudah ada di bundle.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat menambahkan soal ke bundle. Silakan coba lagi.');
        }
    }

    public function removeQuestion(QuestionBundle $bundle, Question $question)
    {
        try {
            $bundle->questions()->detach($question->id);

            ActivityLogger::log(
                'detach',
                'Question Bundle',
                auth()->user()->name . " menghapus soal ID {$question->id} dari bundle '{$bundle->name}'",
                "Bundle ID: {$bundle->id}"
            );

            return back()->with('success', 'Soal berhasil dihapus dari bundle!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus soal dari bundle. Silakan coba lagi.');
        }
    }
}
