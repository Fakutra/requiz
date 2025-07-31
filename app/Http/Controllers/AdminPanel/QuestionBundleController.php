<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuestionBundle;
use App\Models\Question; // <-- Tambahkan ini
use \Cviebrock\EloquentSluggable\Services\SlugService;

class QuestionBundleController extends Controller
{
    /**
     * Menampilkan daftar semua bundle.
     */
    public function index()
    {
        $bundles = QuestionBundle::withCount('questions')->latest()->paginate(9);
        
        // Variabel $allQuestions dihapus karena tidak digunakan di view index
        return view('admin.bundle.index', compact('bundles'));
    }

    /**
     * Menampilkan halaman detail untuk satu bundle dengan soal yang dipaginasi.
     */
    public function show(QuestionBundle $bundle)
    {
        // 1. Ambil soal yang SUDAH ADA di dalam bundle untuk tabel utama.
        $questionsInBundle = $bundle->questions()->paginate(10);

        // 2. Ambil soal yang BELUM ADA di dalam bundle untuk modal.
        $existingQuestionIds = $bundle->questions()->pluck('questions.id');
        $availableQuestions = Question::whereNotIn('id', $existingQuestionIds)->get();

        // 3. BARU: Siapkan daftar kategori unik dari soal yang tersedia.
        $categories = $availableQuestions->pluck('category')->unique()->sort();

        // 4. Kirim semua data yang diperlukan ke view.
        return view('admin.bundle.show', compact(
            'bundle',
            'questionsInBundle',
            'availableQuestions',
            'categories' // <-- Tambahkan variabel ini
        ));
    }

    /**
     * Menyimpan bundle baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        // Tambahkan validasi 'unique' untuk mencegah nama bundle yang sama
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:question_bundles,name',
            'description' => 'nullable|string',
        ]);

        // 2. Buat data baru (CARA YANG DIPERBAIKI)
        // Jangan gunakan ::create() secara langsung jika ada properti (seperti slug)
        // yang dibuat secara otomatis oleh model event.

        // Buat instance model baru
        $bundle = new QuestionBundle();

        // Isi propertinya dari data yang sudah divalidasi
        $bundle->name = $validated['name'];
        $bundle->description = $validated['description'];

        // Simpan model. Method save() akan memicu event 'creating'
        // yang akan menjalankan package sluggable untuk mengisi $bundle->slug secara otomatis.
        $bundle->save();

        // 3. Redirect kembali ke halaman index dengan pesan sukses
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
        // 1. Validasi input dari form edit
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 2. Update data pada model yang ditemukan
        $bundle->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('bundle.index')
                         ->with('success', 'Bundle berhasil diperbarui!');
    }

    // Anda juga memerlukan metode destroy jika ingin fungsionalitas hapus bekerja
    public function destroy(QuestionBundle $bundle)
    {
        $bundle->delete();
        return redirect()->route('bundle.index')
                         ->with('success', 'Bundle berhasil dihapus!');
    }

    public function addQuestion(Request $request, QuestionBundle $bundle)
    {
        // 1. Validasi input, pastikan question_ids adalah array dan setiap isinya ada di tabel questions
        $request->validate([
            'question_ids' => 'required|array', // Pastikan ini adalah array
            'question_ids.*' => 'exists:questions,id', // Validasi setiap item di dalam array
        ]);

        // 2. Ambil ID soal yang sudah ada di bundle untuk mencegah duplikasi
        $existingQuestionIds = $bundle->questions()->pluck('questions.id')->toArray();

        // 3. Filter ID soal yang dikirim dari form, hanya ambil yang belum ada di bundle
        $newQuestionIds = array_diff($request->question_ids, $existingQuestionIds);

        // 4. Jika ada soal baru yang valid, tambahkan ke bundle menggunakan attach()
        if (!empty($newQuestionIds)) {
            $bundle->questions()->attach($newQuestionIds);
            return redirect()->back()->with('success', count($newQuestionIds) . ' soal berhasil ditambahkan!');
        }

        // 5. Jika tidak ada soal baru yang ditambahkan (mungkin karena sudah ada semua)
        return redirect()->back()->with('info', 'Tidak ada soal baru yang ditambahkan. Mungkin semua sudah ada di dalam bundle.');
    }

    /**
     * Menghapus soal dari bundle.
     */
    public function removeQuestion(QuestionBundle $bundle, Question $question)
    {
        // Gunakan detach() untuk menghapus relasi dari tabel pivot
        $bundle->questions()->detach($question->id);

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari bundle!');
    }

    
}
