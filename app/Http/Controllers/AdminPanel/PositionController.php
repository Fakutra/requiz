<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use App\Models\Position;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('id', 'asc')->get();
        return view('admin.batch.position.index', compact('positions'));
    }

    public function store(Request $request, Batch $batch)
    {
        // 1. Validasi semua data yang masuk dari form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|string',
            'description' => 'required|string', // Validasi untuk Trix Editor
        ]);

        // 2. Buat posisi baru menggunakan relasi dari Batch
        // Ini secara otomatis akan mengisi 'batch_id'
        $batch->position()->create($validated);

        // 3. Arahkan kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('batch.show', $batch)->with('success', 'Posisi baru telah berhasil ditambahkan!');
    }

    public function update(Request $request, Position $position)
    {
        // 1. Validasi data yang masuk
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|string',
            'description' => 'required|string',
        ]);

        // 2. Update posisi menggunakan data yang sudah divalidasi
        $position->update($validated);

        // 3. Arahkan kembali dengan pesan sukses
        return redirect()->route('batch.show', $position->batch)->with('success', 'Posisi telah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $batch = $position->batch; // Ambil batch sebelum dihapus
        $position->delete();

        // Ubah redirect ke halaman show batch
        return redirect()->route('batch.show', $batch)->with('success', 'Position has been deleted!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Position::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
