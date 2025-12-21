<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('id', 'asc')->get();
        return view('admin.batch.position.index', compact('positions'));
    }

    private function parseListToArray(?string $text): array
    {
        if (!$text) return [];

        $parts = preg_split('/\r\n|\r|\n/', $text);
        $clean = [];

        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') continue;
            $p = preg_replace('/^[\-\*\•\>\s]+/u', '', $p);
            $p = preg_replace('/\s+/', ' ', $p);
            $clean[] = $p;
        }

        $seen = [];
        $result = [];
        foreach ($clean as $item) {
            $k = mb_strtolower($item);
            if (isset($seen[$k])) continue;
            $seen[$k] = true;
            $result[] = $item;
        }

        return $result;
    }

    public function store(Request $request, Batch $batch)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'quota'              => 'required|integer|min:1',
            'status'             => 'required|string|in:Active,Inactive',
            'pendidikan_minimum' => 'required|in:SMA/Sederajat,D1,D2,D3,D4,S1,S2,S3',
            'descriptions'       => 'required|string',
            'skills'             => 'nullable|string',
            'requirements'       => 'nullable|string',
            'majors'             => 'nullable|string',
            'deadline'           => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan posisi. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $position = $batch->position()->create([
                'name'               => $validated['name'],
                'slug'               => Str::slug($validated['name']) . '-' . uniqid(),
                'quota'              => $validated['quota'],
                'status'             => $validated['status'],
                'pendidikan_minimum' => $validated['pendidikan_minimum'],
                'description'        => $this->parseListToArray($validated['descriptions']),
                'skills'             => $this->parseListToArray($validated['skills'] ?? null),
                'requirements'       => $this->parseListToArray($validated['requirements'] ?? null),
                'majors'             => $this->parseListToArray($validated['majors'] ?? null),
                'deadline'           => $validated['deadline'] ?? null,
            ]);

            ActivityLogger::log(
                'create',
                'Position',
                auth()->user()->name . " menambahkan posisi baru '{$position->name}' pada batch '{$batch->name}'",
                "Position ID: {$position->id}"
            );

            return redirect()
                ->route('batch.show', $batch)
                ->with('success', 'Posisi baru telah berhasil ditambahkan!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan posisi. Silakan coba lagi.');
        }
    }

    public function update(Request $request, Position $position)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'quota'              => 'required|integer|min:1',
            'status'             => 'required|string|in:Active,Inactive',
            'pendidikan_minimum' => 'required|in:SMA/Sederajat,D1,D2,D3,D4,S1,S2,S3',
            'descriptions'       => 'required|string',
            'skills'             => 'nullable|string',
            'requirements'       => 'nullable|string',
            'majors'             => 'nullable|string',
            'deadline'           => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui posisi. Periksa kembali data yang diinput.');
        }

        try {
            $validated = $validator->validated();

            $oldData = $position->toArray();

            $position->update([
                'name'               => $validated['name'],
                'slug'               => Str::slug($validated['name']) . '-' . uniqid(),
                'quota'              => $validated['quota'],
                'status'             => $validated['status'],
                'pendidikan_minimum' => $validated['pendidikan_minimum'],
                'description'        => $this->parseListToArray($validated['descriptions']),
                'skills'             => $this->parseListToArray($validated['skills'] ?? null),
                'requirements'       => $this->parseListToArray($validated['requirements'] ?? null),
                'majors'             => $this->parseListToArray($validated['majors'] ?? null),
                'deadline'           => $validated['deadline'] ?? null,
            ]);

            ActivityLogger::logUpdate('Position', $position, $oldData, $position->toArray());

            return redirect()
                ->route('batch.show', $position->batch)
                ->with('success', 'Posisi telah berhasil diperbarui!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui posisi. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $position = Position::findOrFail($id);
            $batch = $position->batch;
            $name = $position->name;

            $position->delete();

            ActivityLogger::log(
                'delete',
                'Position',
                auth()->user()->name . " menghapus posisi '{$name}' dari batch '{$batch->name}'",
                "Position ID: {$position->id}"
            );

            return redirect()
                ->route('batch.show', $batch)
                ->with('success', 'Posisi telah berhasil dihapus!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus posisi. Silakan coba lagi.');
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Position::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
