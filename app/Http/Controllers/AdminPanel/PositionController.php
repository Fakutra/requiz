<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('id', 'asc')->get();
        return view('admin.batch.position.index', compact('positions'));
    }

    public function store(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'quota'        => 'required|integer|min:1',
            'status'       => 'required|string|in:Active,Inactive',
            'description'  => 'required|string',
            'skills'       => 'nullable|string',
            'requirements' => 'nullable|string',
            'majors'       => 'nullable|string',
            'deadline'     => 'nullable|date',
        ]);

        // Buat posisi baru di batch terkait
        $position = $batch->position()->create($validated);

        // ✅ Log CREATE
        $details = collect($validated)
            ->map(fn($v, $k) => "{$k}='{$v}'")
            ->implode(', ');

        ActivityLogger::log(
            'create',
            'Position',
            auth()->user()->name . " menambahkan posisi baru pada Batch '{$batch->name}' dengan data: {$details}",
            "Posisi: {$position->name}"
        );

        return redirect()
            ->route('batch.show', $batch)
            ->with('success', 'Posisi baru telah berhasil ditambahkan!');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'quota'        => 'required|integer|min:1',
            'status'       => 'required|string|in:Active,Inactive',
            'description'  => 'required|string',
            'skills'       => 'nullable|string',
            'requirements' => 'nullable|string',
            'majors'       => 'nullable|string',
            'deadline'     => 'nullable|date',
        ]);

        // Simpan data lama sebelum update
        $oldData = $position->only([
            'name',
            'quota',
            'status',
            'description',
            'skills',
            'requirements',
            'majors',
            'deadline',
        ]);

        // Update posisi
        $position->update($validated);

        // Data baru setelah update
        $newData = $position->only([
            'name',
            'quota',
            'status',
            'description',
            'skills',
            'requirements',
            'majors',
            'deadline',
        ]);

        // ✅ Log UPDATE (diff)
        ActivityLogger::logUpdate(
            'Position',
            $position,
            $oldData,
            $newData
        );

        return redirect()
            ->route('batch.show', $position->batch)
            ->with('success', 'Posisi telah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $batch = $position->batch;
        $name = $position->name;

        $position->delete();

        // ✅ Log DELETE
        ActivityLogger::log(
            'delete',
            'Position',
            auth()->user()->name . " menghapus posisi {$name} dari Batch '{$batch->name}'",
            "Posisi: {$name}"
        );

        return redirect()
            ->route('batch.show', $batch)
            ->with('success', 'Posisi telah berhasil dihapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Position::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
