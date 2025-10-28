<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuestionBundle;
use App\Models\Question;
use App\Services\ActivityLogger;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class QuestionBundleController extends Controller
{
    public function index()
    {
        $bundles = QuestionBundle::withCount('questions')->latest()->paginate(9);
        return view('admin.bundle.index', compact('bundles'));
    }

    public function show(QuestionBundle $bundle)
    {
        $questionsInBundle = $bundle->questions()->paginate(10);
        $existingQuestionIds = $bundle->questions()->pluck('questions.id');
        $availableQuestions = Question::whereNotIn('id', $existingQuestionIds)->get();
        $categories = $availableQuestions->pluck('category')->unique()->sort();

        return view('admin.bundle.show', compact(
            'bundle',
            'questionsInBundle',
            'availableQuestions',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:question_bundles,name',
            'description' => 'nullable|string',
        ]);

        $bundle = new QuestionBundle();
        $bundle->name = $validated['name'];
        $bundle->description = $validated['description'];
        $bundle->save();

        ActivityLogger::log(
            'create',
            'Question Bundle',
            auth()->user()->name . " membuat bundle baru: '{$bundle->name}'",
            "Bundle ID: {$bundle->id}"
        );

        return redirect()->route('bundle.index')
            ->with('success', 'Bundle baru berhasil dibuat!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(QuestionBundle::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }

    public function update(Request $request, QuestionBundle $bundle)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $oldData = $bundle->toArray();

        $bundle->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $bundle->refresh();
        $newData = $bundle->toArray();

        ActivityLogger::logUpdate('Question Bundle', $bundle, $oldData, $newData);

        return redirect()->route('bundle.index')
            ->with('success', 'Bundle berhasil diperbarui!');
    }

    public function destroy(QuestionBundle $bundle)
    {
        ActivityLogger::log(
            'delete',
            'Question Bundle',
            auth()->user()->name . " menghapus bundle: '{$bundle->name}'",
            "Bundle ID: {$bundle->id}"
        );

        $bundle->delete();

        return redirect()->route('bundle.index')
            ->with('success', 'Bundle berhasil dihapus!');
    }

    public function addQuestion(Request $request, QuestionBundle $bundle)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $existingQuestionIds = $bundle->questions()->pluck('questions.id')->toArray();
        $newQuestionIds = array_diff($request->question_ids, $existingQuestionIds);

        if (!empty($newQuestionIds)) {
            $bundle->questions()->attach($newQuestionIds);

            ActivityLogger::log(
                'attach',
                'Question Bundle',
                auth()->user()->name . " menambahkan " . count($newQuestionIds) . " soal ke bundle '{$bundle->name}'",
                "Bundle ID: {$bundle->id}"
            );

            return redirect()->back()->with('success', count($newQuestionIds) . ' soal berhasil ditambahkan!');
        }

        return redirect()->back()->with('info', 'Tidak ada soal baru yang ditambahkan.');
    }

    public function removeQuestion(QuestionBundle $bundle, Question $question)
    {
        $bundle->questions()->detach($question->id);

        ActivityLogger::log(
            'detach',
            'Question Bundle',
            auth()->user()->name . " menghapus soal ID {$question->id} dari bundle '{$bundle->name}'",
            "Bundle ID: {$bundle->id}"
        );

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari bundle!');
    }
}
