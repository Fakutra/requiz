<?php

namespace App\Http\Controllers\AdminPanel;

use App\Models\Test;
use App\Models\Position;
use App\Models\TestSection;
use Illuminate\Http\Request;
use App\Models\QuestionBundle;
use App\Http\Controllers\Controller;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class TestController extends Controller
{
    public function index()
    {
        // Ambil semua data posisi
        $positions = Position::all();

        // Gunakan withCount untuk efisiensi
        $tests = Test::withCount('section')->orderBy('id', 'asc')->get();
        
        // Kirim kedua variabel ('tests' dan 'positions') ke view
        return view('admin.test.index', compact('tests', 'positions'));
    }

    public function show(Test $test)
    {
        // Ambil semua question bundle untuk dropdown di modal
        $question_bundles = QuestionBundle::all();

        // Load relasi section untuk ditampilkan
        $test->load('section'); 

        return view('admin.test.show', compact('test', 'question_bundles'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'test_date' => 'required|date',
            // 'slug' => 'string',
            'position_id' => 'required',
        ]);

        Test::create($validated);

        return redirect()->route('test.index')->with('success', 'New Quiz has been added!');
    }

    public function update(Request $request, Test $test)
    {
        // Validasi input dari form
        $rules = [
            'name' => 'required|string|max:255',
            'test_date' => 'required|date',
            'position_id' => 'required|exists:positions,id', // Pastikan position_id valid
        ];

        $validatedData = $request->validate($rules);

        // Lakukan update data
        // Karena 'onUpdate' => true di model, slug akan otomatis ter-update jika 'name' berubah
        $test->update($validatedData);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('test.index')->with('success', 'Quiz has been updated!');
    }

    public function destroy(Test $test)
    {
        try {
            // Hapus data test dari database.
            $test->delete();

            // Redirect kembali ke halaman index dengan pesan sukses.
            return redirect()->route('test.index')->with('success', 'Quiz berhasil dihapus!');

        } catch (\Exception $e) {
            // Jika terjadi error, redirect kembali dengan pesan error.
            return redirect()->route('test.index')->with('error', 'Gagal menghapus quiz. Error: ' . $e->getMessage());
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Test::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
    
}
