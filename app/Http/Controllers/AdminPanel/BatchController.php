<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;

class BatchController extends Controller
{
    public function index()
    {
        $batchs = Batch::withCount('position')->orderBy('id', 'asc')->get();
        return view('admin.batch.index', compact('batchs'));
    }

    public function show(Batch $batch)
    {
        $batch->load('position');
        return view('admin.batch.show', compact('batch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'status'     => 'required',
            'start_date' => 'date',
            'end_date'   => 'date',
        ]);

        $batch = Batch::create($validated);

        // ✅ Rangkai data untuk deskripsi log
        $details = collect($validated)
            ->map(fn($v, $k) => "{$k}='{$v}'")
            ->implode(', ');

        // ✅ Log CREATE
        ActivityLogger::log(
            'create',
            'Batch',
            auth()->user()->name." menambahkan batch baru dengan data: {$details}",
            "Batch: {$batch->name}"
        );

        return redirect()->route('batch.index')->with('success', 'New Batch has been added!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'status'     => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        $batch = Batch::findOrFail($id);

        // ✅ Data sebelum update
        $oldData = $batch->only(['name','status','start_date','end_date']);

        // ✅ Update data
        $batch->update($validated);

        // ✅ Data sesudah update
        $newData = $batch->only(['name','status','start_date','end_date']);

        // ✅ Log UPDATE (diff before → after)
        ActivityLogger::logUpdate(
            'Batch',
            $batch,
            $oldData,
            $newData
        );

        return redirect()->route('batch.index')->with('success', 'Batch has been updated!');
    }

    public function destroy($id)
    {
        $batch = Batch::findOrFail($id);

        // ✅ simpan nama sebelum delete
        $batchName = $batch->name;

        $batch->delete();

        // ✅ Log DELETE
        ActivityLogger::log(
            'delete',
            'Batch',
            auth()->user()->name." menghapus batch {$batchName}",
            "Batch: {$batchName}"
        );

        return redirect()->route('batch.index')->with('success', 'Batch has been deleted!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Batch::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
