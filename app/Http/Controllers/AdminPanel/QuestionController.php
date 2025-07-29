<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel; // <-- Tambahkan ini
use App\Imports\QuestionsImport;      // <-- Tambahkan ini
use Symfony\Component\HttpFoundation\BinaryFileResponse; // <-- Tambahkan ini

class QuestionController extends Controller
{
    /**
     * Menampilkan daftar pertanyaan dengan fitur pencarian dan filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Memulai query builder
        $query = Question::query();

        // Menerapkan filter berdasarkan input dari request
        $query->when($request->filled('search'), function ($q) use ($request) {
            // Mengubah input pencarian ke huruf kecil
            $searchTerm = strtolower($request->search);
            
            // Menggunakan whereRaw untuk perbandingan case-insensitive
            // Fungsi LOWER() akan mengubah isi kolom 'question' menjadi huruf kecil sebelum membandingkan
            return $q->whereRaw('LOWER(question) LIKE ?', ['%' . $searchTerm . '%']);
        });

        // 2. Filter berdasarkan tipe soal (kolom 'type')
        $query->when($request->filled('type'), function ($q) use ($request) {
            return $q->where('type', $request->type);
        });

        // 3. Filter berdasarkan kategori soal (kolom 'category')
        $query->when($request->filled('category'), function ($q) use ($request) {
            return $q->where('category', $request->category);
        });

        // Mengambil hasil dengan urutan terbaru dan paginasi
        $questions = $query->latest()->paginate(10)->appends($request->query()); // Diubah ke latest() agar lebih umum

        // Mengirim data ke view
        return view('admin.question.index', compact('questions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:PG,Multiple,Poin,Essay'],
            'category' => ['required', 'in:Umum,Teknis,Psikologi'],
            'question' => ['required', 'string', 'min:5'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            // Opsi hanya wajib jika tipe bukan Essay
            'option_a' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
            'option_b' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
            'option_c' => ['nullable', 'string'],
            'option_d' => ['nullable', 'string'],
            'option_e' => ['nullable', 'string'],

            // Poin hanya wajib jika tipe adalah Poin
            'point_a' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_b' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_c' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_d' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_e' => ['required_if:type,Poin', 'nullable', 'integer'],

            // Jawaban wajib jika tipe PG (string) atau Multiple (array)
            'answer' => ['required_if:type,PG,Multiple', 'nullable'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed! Please check the form fields.');
        }

        // 2. Menyiapkan Data
        $data = $request->only([
            'type', 'category', 'question',
            'option_a', 'option_b', 'option_c', 'option_d', 'option_e',
            'point_a', 'point_b', 'point_c', 'point_d', 'point_e',
        ]);

        // 3. Menangani Jawaban berdasarkan Tipe Soal
        if ($request->type === 'Multiple') {
            // Jika tipe Multiple, jawaban adalah array. Kita gabungkan menjadi string.
            if (is_array($request->answer)) {
                $data['answer'] = implode(',', $request->answer);
            } else {
                $data['answer'] = null;
            }
        } elseif ($request->type === 'PG') {
            // Jika tipe PG, jawaban sudah berupa string.
            $data['answer'] = $request->answer;
        } else {
            // Untuk tipe Poin dan Essay, tidak ada jawaban benar.
            $data['answer'] = null;
        }
        
        // Membersihkan data poin jika tipe bukan 'Poin'
        if ($request->type !== 'Poin') {
            $data['point_a'] = null;
            $data['point_b'] = null;
            $data['point_c'] = null;
            $data['point_d'] = null;
            $data['point_e'] = null;
        }
        
        // Membersihkan data opsi jika tipe 'Essay'
        if ($request->type === 'Essay') {
            $data['option_a'] = null;
            $data['option_b'] = null;
            $data['option_c'] = null;
            $data['option_d'] = null;
            $data['option_e'] = null;
        }


        // 4. Menangani Upload Gambar
        if ($request->hasFile('image')) {
            // Simpan gambar ke storage/app/public/questions
            $path = $request->file('image')->store('public/questions');
            // Simpan path yang dapat diakses publik
            $data['image_path'] = Storage::url($path);
        }

        // 5. Simpan ke Database
        try {
            Question::create($data);
            return redirect()->route('question.index')->with('success', 'Question created successfully!');
        } catch (\Exception $e) {
            // Jika ada error saat menyimpan, kembalikan dengan pesan error
            return redirect()->back()->withInput()->with('error', 'Failed to save question. Error: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui pertanyaan di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Question $question)
{
    // 1. Validasi Input (sama seperti store)
    $validator = Validator::make($request->all(), [
        'type' => ['required', 'in:PG,Multiple,Poin,Essay'],
        'category' => ['required', 'in:Umum,Teknis,Psikologi'],
        'question' => ['required', 'string', 'min:5'],
        'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        'option_a' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'option_b' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'option_c' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'option_d' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'option_e' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'point_a' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'point_b' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'point_c' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'point_d' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'point_e' => ['required_if:type,PG,Multiple,Poin', 'nullable', 'string'],
        'answer' => ['required_if:type,PG,Multiple', 'nullable'],
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('error', 'Update failed! Please check the form fields.');
    }

    // 2. Menyiapkan Data
    $data = $request->except(['_token', '_method', 'image']);

    // 3. Menangani & Membersihkan Data berdasarkan Tipe Soal
    // Logika ini sangat penting untuk menjaga konsistensi data
    switch ($request->type) {
        case 'Multiple':
            $data['answer'] = is_array($request->answer) ? implode(',', $request->answer) : null;
            $data = array_merge($data, ['point_a' => null, 'point_b' => null, 'point_c' => null, 'point_d' => null, 'point_e' => null]);
            break;
        case 'PG':
            $data['answer'] = $request->answer;
            $data = array_merge($data, ['point_a' => null, 'point_b' => null, 'point_c' => null, 'point_d' => null, 'point_e' => null]);
            break;
        case 'Poin':
            $data['answer'] = null;
            break;
        case 'Essay':
            $data = array_merge($data, [
                'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'option_e' => null,
                'point_a' => null, 'point_b' => null, 'point_c' => null, 'point_d' => null, 'point_e' => null,
                'answer' => null
            ]);
            break;
    }


    // 4. Menangani Upload Gambar Baru
    if ($request->hasFile('image')) {
        // Hapus gambar lama jika ada
        if ($question->image_path) {
            $oldPath = str_replace('/storage/', 'public/', $question->image_path);
            Storage::delete($oldPath);
        }
        // Simpan gambar baru
        $path = $request->file('image')->store('public/questions');
        $data['image_path'] = Storage::url($path);
    }

    // 5. Update ke Database
    $question->update($data);

    return redirect()->route('question.index')->with('success', 'Question updated successfully!');
}

    /**
     * Menghapus pertanyaan dari database.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Question $question)
    {
        // Hapus gambar terkait jika ada
        if ($question->image_path) {
            $oldPath = str_replace('/storage/', 'public/', $question->image_path);
            Storage::delete($oldPath);
        }

        // Hapus data dari database
        $question->delete();

        return redirect()->route('question.index')->with('success', 'Question deleted successfully!');
    }

    /**
     * Mengimpor data pertanyaan dari file Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new QuestionsImport, $request->file('excel_file'));
            
            return redirect()->route('question.index')->with('success', 'Questions imported successfully!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             // Anda bisa memformat error ini untuk ditampilkan ke user
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = "Row " . $failure->row() . ": " . implode(', ', $failure->errors());
             }
             return redirect()->back()->with('error', 'Import failed. Errors: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occurred during import: ' . $e->getMessage());
        }
    }

    /**
     * Menyediakan file template Excel untuk diunduh.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $filePath = public_path('templates/question_import_template.xlsx');

        // Pastikan file template ada di public/templates/question_import_template.xlsx
        if (!file_exists($filePath)) {
            // Jika tidak ada, buat file template secara dinamis (opsional, tapi lebih baik)
            // Untuk sekarang, kita asumsikan file sudah ada.
            // Anda bisa membuat file ini secara manual.
            abort(404, 'Template file not found.');
        }

        return response()->download($filePath);
    }

}
