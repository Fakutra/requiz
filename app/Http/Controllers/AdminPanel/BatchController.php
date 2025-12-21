<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'status'     => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan batch. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $batch = Batch::create($validated);

            // rangkai data buat log
            $details = collect($validated)
                ->map(fn($v, $k) => "{$k}='{$v}'")
                ->implode(', ');

            ActivityLogger::log(
                'create',
                'Batch',
                auth()->user()->name . " menambahkan batch baru dengan data: {$details}",
                "Batch: {$batch->name}"
            );

            return redirect()
                ->route('batch.index')
                ->with('success', 'New Batch has been added!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan batch. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'status'     => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal mengupdate batch. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $batch = Batch::findOrFail($id);

            // data sebelum update
            $oldData = $batch->only(['name', 'status', 'start_date', 'end_date']);

            // update
            $batch->update($validated);

            // data sesudah update
            $newData = $batch->only(['name', 'status', 'start_date', 'end_date']);

            ActivityLogger::logUpdate(
                'Batch',
                $batch,
                $oldData,
                $newData
            );

            return redirect()
                ->route('batch.index')
                ->with('success', 'Batch has been updated!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate batch. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $batch = Batch::findOrFail($id);

            $batchName = $batch->name;

            $batch->delete();

            ActivityLogger::log(
                'delete',
                'Batch',
                auth()->user()->name . " menghapus batch {$batchName}",
                "Batch: {$batchName}"
            );

            return redirect()
                ->route('batch.index')
                ->with('success', 'Batch has been deleted!');

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('batch.index')
                ->with('error', 'Terjadi kesalahan saat menghapus batch. Silakan coba lagi.');
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Batch::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
