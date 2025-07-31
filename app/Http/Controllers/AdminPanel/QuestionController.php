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

         // Ambil semua daftar kategori yang unik dari tabel untuk mengisi dropdown
        $categories = Question::pluck('category')->unique();

        // Mengirim data ke view
        return view('admin.question.index', compact('questions', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (disesuaikan agar lebih akurat)
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:PG,Multiple,Poin,Essay'],
            'category' => ['required', 'string'],
            'question' => ['required', 'string', 'min:5'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // Opsi wajib jika tipe bukan Essay
            'option_a' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_b' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_c' => ['nullable', 'string'],
            'option_d' => ['nullable', 'string'],
            'option_e' => ['nullable', 'string'],
            
            // Poin hanya wajib jika tipe adalah Poin
            'point_a' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_b' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_c' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_d' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_e' => ['required_if:type,Poin', 'nullable', 'integer'],
            
            // Jawaban wajib jika tipe PG atau Multiple
            'answer' => ['required_if:type,PG,Multiple', 'nullable'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed! Please check the form fields.');
        }

        // 2. Menyiapkan Data
        $data = $request->except(['_token', 'image']);

        // 3. Menangani & Membersihkan Data berdasarkan Tipe Soal
        switch ($request->type) {
            case 'Multiple':
                $data['answer'] = is_array($request->answer) ? implode(',', $request->answer) : null;
                // Kosongkan semua poin
                $data = array_merge($data, ['point_a' => null, 'point_b' => null, 'point_c' => null, 'point_d' => null, 'point_e' => null]);
                break;
            case 'PG':
                // Kosongkan semua poin
                $data = array_merge($data, ['point_a' => null, 'point_b' => null, 'point_c' => null, 'point_d' => null, 'point_e' => null]);
                break;
            case 'Poin':
                // Kosongkan jawaban
                $data['answer'] = null;
                break;
            case 'Essay':
                // Kosongkan semua opsi, poin, dan jawaban
                $data = array_merge($data, [
                    'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'option_e' => null,
                    'point_a' => null, 'point_b' => null, 'point_c' => null, 'point_d' => null, 'point_e' => null,
                    'answer' => null
                ]);
                break;
        }

        // 4. Menangani Upload Gambar
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/questions');
            $data['image_path'] = Storage::url($path);
        }

        // 5. Simpan ke Database
        Question::create($data);

        return redirect()->route('question.index')->with('success', 'Question created successfully!');
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
        // 1. Validasi Input (perbaikan pada validasi point)
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:PG,Multiple,Poin,Essay'],
            'category' => ['required', 'string'],
            'question' => ['required', 'string', 'min:5'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // Opsi wajib jika tipe bukan Essay
            'option_a' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_b' => ['exclude_if:type,Essay', 'required', 'string'],
            'option_c' => ['nullable', 'string'],
            'option_d' => ['nullable', 'string'],
            'option_e' => ['nullable', 'string'],

            // Poin hanya wajib jika tipe adalah Poin
            'point_a' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_b' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_c' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_d' => ['required_if:type,Poin', 'nullable', 'integer'],
            'point_e' => ['required_if:type,Poin', 'nullable', 'integer'],
            
            // Jawaban wajib jika tipe PG atau Multiple
            'answer' => ['required_if:type,PG,Multiple', 'nullable'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Update failed! Please check the form fields.');
        }

        // (Sisa dari fungsi update Anda sudah benar)
        // 2. Menyiapkan Data
        $data = $request->except(['_token', '_method', 'image']);

        // 3. Menangani & Membersihkan Data berdasarkan Tipe Soal
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
            if ($question->image_path) {
                $oldPath = str_replace('/storage/', 'public/', $question->image_path);
                Storage::delete($oldPath);
            }
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
