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

    // public function create($slug)
    // {
    //     $batchs = Batch::where('slug', $slug)->first();
    //     return view('admin.batch.position.create', compact('batchs'));
    // }

    // public function store(Request $request, Batch $batch)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'slug' => 'string',
    //         'quota' => 'required|integer',
    //         'status' => 'required',
    //         'description' => 'required',
    //     ]);

    //     $validated['batch_id'] = $batch->id;

    //     Position::create($validated);

    //     return redirect('batch.index')->with('success', 'New Position has been added!');

    // }
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
        return redirect()->route('batch.index')->with('success', 'Posisi baru telah berhasil ditambahkan!');
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
        return redirect()->route('batch.index')->with('success', 'Posisi telah berhasil diperbarui!');
    }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'quota' => 'required|integer',
    //         'status' => 'required',
    //         'description' => 'required',
    //     ]);

    //     $position = Position::findOrFail($id);

    //     // Deteksi perubahan name agar slug bisa regenerate
    //     if ($validated['name'] !== $position->name) {
    //         $position->slug = null; // trigger slug regeneration
    //     }

    //     $position->update($validated);

    //     return redirect()->route('position.index')->with('success', 'Position has been updated!');
    // }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->route('batch.index')->with('success', 'Position has been deleted!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Position::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
