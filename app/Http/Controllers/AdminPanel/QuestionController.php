<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Services\ActivityLogger;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = strtolower($request->search);
            return $q->whereRaw('LOWER(question) LIKE ?', ['%' . $searchTerm . '%']);
        });

        $query->when($request->filled('type'), fn($q) => $q->where('type', $request->type));
        $query->when($request->filled('category'), fn($q) => $q->where('category', $request->category));

        $questions = $query->latest()->paginate(10)->appends($request->query());
        $categories = Question::pluck('category')->unique();

        return view('admin.question.index', compact('questions', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:PG,Multiple,Poin,Essay'],
            'category' => ['required', 'string'],
            'question' => ['required', 'string', 'min:5'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'option_a' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_b' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_c' => ['nullable', 'string'],
            'option_d' => ['nullable', 'string'],
            'option_e' => ['nullable', 'string'],
            'point_a' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_b' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_c' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_d' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_e' => ['required_if:type,Poin', 'nullable', 'integer'],
            'answer'  => ['required_if:type,PG,Multiple', 'nullable'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Validation failed!');
        }

        $data = $request->except(['_token', 'image']);

        switch ($request->type) {
            case 'Multiple':
                $data['answer'] = is_array($request->answer) ? implode(',', $request->answer) : null;
                $data = array_merge($data, ['point_a'=>null,'point_b'=>null,'point_c'=>null,'point_d'=>null,'point_e'=>null]);
                break;
            case 'PG':
                $data = array_merge($data, ['point_a'=>null,'point_b'=>null,'point_c'=>null,'point_d'=>null,'point_e'=>null]);
                break;
            case 'Poin':
                $data['answer'] = null;
                break;
            case 'Essay':
                $data = array_merge($data, [
                    'option_a'=>null,'option_b'=>null,'option_c'=>null,'option_d'=>null,'option_e'=>null,
                    'point_a'=>null,'point_b'=>null,'point_c'=>null,'point_d'=>null,'point_e'=>null,
                    'answer'=>null
                ]);
                break;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('questions', 'public');
            $data['image_path'] = 'storage/' . $path;
        }

        $question = Question::create($data);

        ActivityLogger::log(
            'create',
            'Question',
            auth()->user()->name . " menambahkan soal baru: '{$question->question}'",
            "Question ID: {$question->id}"
        );

        return redirect()->route('question.index')->with('success', 'Question created successfully!');
    }

    public function update(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:PG,Multiple,Poin,Essay'],
            'category' => ['required', 'string'],
            'question' => ['required', 'string', 'min:5'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'option_a' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_b' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_c' => ['nullable', 'string'],
            'option_d' => ['nullable', 'string'],
            'option_e' => ['nullable', 'string'],
            'point_a' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_b' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_c' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_d' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_e' => ['required_if:type,Poin', 'nullable', 'integer'],
            'answer'  => ['required_if:type,PG,Multiple', 'nullable'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Update failed!');
        }

        $oldData = $question->toArray();
        $data = $request->except(['_token', '_method', 'image']);

        switch ($request->type) {
            case 'Multiple':
                $data['answer'] = is_array($request->answer) ? implode(',', $request->answer) : null;
                $data = array_merge($data, ['point_a'=>null,'point_b'=>null,'point_c'=>null,'point_d'=>null,'point_e'=>null]);
                break;
            case 'PG':
                $data['answer'] = $request->answer;
                $data = array_merge($data, ['point_a'=>null,'point_b'=>null,'point_c'=>null,'point_d'=>null,'point_e'=>null]);
                break;
            case 'Poin':
                $data['answer'] = null;
                break;
            case 'Essay':
                $data = array_merge($data, [
                    'option_a'=>null,'option_b'=>null,'option_c'=>null,'option_d'=>null,'option_e'=>null,
                    'point_a'=>null,'point_b'=>null,'point_c'=>null,'point_d'=>null,'point_e'=>null,
                    'answer'=>null
                ]);
                break;
        }

        if ($request->hasFile('image')) {
            if ($question->image_path) {
                Storage::delete(str_replace('storage/', 'public/', $question->image_path));
            }

            $path = $request->file('image')->store('questions', 'public');
            $data['image_path'] = 'storage/' . $path;
        } else {
            unset($data['image_path']);
        }

        $question->update($data);
        $newData = $question->toArray();

        // Log detail perubahan
        ActivityLogger::logUpdate('Question', $question, $oldData, $newData);

        // Log khusus untuk perubahan gambar (agar tercatat jelas)
        if ($request->hasFile('image')) {
            ActivityLogger::log(
                'update',
                'Question',
                auth()->user()->name . " mengubah gambar soal: '{$question->question}'",
                "Question ID: {$question->id}"
            );
        }

        return redirect()->route('question.index')->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        $name = $question->question;

        if ($question->image_path) {
            Storage::delete(str_replace('storage/', 'public/', $question->image_path));
        }

        $question->delete();

        ActivityLogger::log(
            'delete',
            'Question',
            auth()->user()->name . " menghapus soal: '{$name}'",
            "Question: {$name}"
        );

        return redirect()->route('question.index')->with('success', 'Question deleted successfully!');
    }

    public function import(Request $request)
    {
        $request->validate(['excel_file' => 'required|mimes:xlsx,xls']);

        try {
            $importer = new QuestionsImport;
            Excel::import($importer, $request->file('excel_file'));

            $count = $importer->getRowCount();

            ActivityLogger::log(
                'import',
                'Question',
                auth()->user()->name . " mengimpor {$count} soal dari file: " . $request->file('excel_file')->getClientOriginalName()
            );

            return redirect()->route('question.index')->with('success', "Berhasil mengimpor {$count} soal!");

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $filePath = public_path('templates/question_import_template.xlsx');

        ActivityLogger::log(
            'export',
            'Question',
            auth()->user()->name . " mendownload template soal"
        );

        if (!file_exists($filePath)) {
            abort(404, 'Template file not found.');
        }

        return response()->download($filePath);
    }
}
