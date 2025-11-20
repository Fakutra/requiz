<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Services\ActivityLogger;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('id', 'asc')->get();
        return view('admin.batch.position.index', compact('positions'));
    }

    /**
     * Helper: parse multiline / comma-separated text into clean array.
     * - support newline and comma separators
     * - trim, remove leading bullets (-, *, •, >)
     * - normalize spaces
     * - dedupe case-insensitive while preserving order
     */
    private function parseListToArray(?string $text): array
    {
        if (!$text) return [];

        // normalize commas to newlines for unified splitting
        $normalized = str_replace(',', "\n", $text);
        $parts = preg_split('/\r\n|\r|\n/', $normalized);
        $clean = [];

        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') continue;
            // remove common bullets
            $p = preg_replace('/^[\-\*\•\>\s]+/u', '', $p);
            // collapse internal whitespace
            $p = preg_replace('/\s+/', ' ', $p);
            $clean[] = $p;
        }

        // dedupe case-insensitive
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|string|in:Active,Inactive',
            'pendidikan_minimum' => 'required|in:SMA/Sederajat,D1,D2,D3,D4,S1,S2,S3',
            'descriptions' => 'required|string',
            'skills' => 'nullable|string',
            'requirements' => 'nullable|string',
            'majors' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        // parse multiline inputs
        $descArr = $this->parseListToArray($validated['descriptions']);
        $skillsArr = $this->parseListToArray($validated['skills'] ?? null);
        $reqArr = $this->parseListToArray($validated['requirements'] ?? null);
        $majorsArr = $this->parseListToArray($validated['majors'] ?? null);

        // create (Batch model has relation position() -> hasMany)
        $position = $batch->position()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'quota' => $validated['quota'],
            'status' => $validated['status'],
            'pendidikan_minimum' => $validated['pendidikan_minimum'],
            // store as JSON strings (text column in DB)
            'description' => json_encode($descArr, JSON_UNESCAPED_UNICODE),
            'skills' => json_encode($skillsArr, JSON_UNESCAPED_UNICODE),
            'requirements' => json_encode($reqArr, JSON_UNESCAPED_UNICODE),
            'majors' => json_encode($majorsArr, JSON_UNESCAPED_UNICODE),
            'deadline' => $validated['deadline'] ?? null,
        ]);

        // Log
        $details = collect($validated)->map(fn($v,$k) => "{$k}='{$v}'")->implode(', ');
        ActivityLogger::log(
            'create',
            'Position',
            auth()->user()->name . " menambahkan posisi baru pada Batch '{$batch->name}' dengan data: {$details}",
            "Posisi: {$position->name}"
        );

        return redirect()->route('batch.show', $batch)->with('success', 'Posisi baru telah berhasil ditambahkan!');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|string|in:Active,Inactive',
            'pendidikan_minimum' => 'required|in:SMA/Sederajat,D1,D2,D3,D4,S1,S2,S3',
            'descriptions' => 'required|string',
            'skills' => 'nullable|string',
            'requirements' => 'nullable|string',
            'majors' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        // old data for logging
        $oldData = $position->only(['name','quota','status','pendidikan_minimum','description','skills','requirements','majors','deadline']);

        // parse lists
        $descArr = $this->parseListToArray($validated['descriptions']);
        $skillsArr = $this->parseListToArray($validated['skills'] ?? null);
        $reqArr = $this->parseListToArray($validated['requirements'] ?? null);
        $majorsArr = $this->parseListToArray($validated['majors'] ?? null);

        // update (store JSON strings)
        $position->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'quota' => $validated['quota'],
            'status' => $validated['status'],
            'pendidikan_minimum' => $validated['pendidikan_minimum'],
            'description' => json_encode($descArr, JSON_UNESCAPED_UNICODE),
            'skills' => json_encode($skillsArr, JSON_UNESCAPED_UNICODE),
            'requirements' => json_encode($reqArr, JSON_UNESCAPED_UNICODE),
            'majors' => json_encode($majorsArr, JSON_UNESCAPED_UNICODE),
            'deadline' => $validated['deadline'] ?? null,
        ]);

        $newData = $position->only(['name','quota','status','pendidikan_minimum','description','skills','requirements','majors','deadline']);

        ActivityLogger::logUpdate('Position', $position, $oldData, $newData);

        return redirect()->route('batch.show', $position->batch)->with('success', 'Posisi telah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $batch = $position->batch;
        $name = $position->name;
        $position->delete();

        ActivityLogger::log(
            'delete',
            'Position',
            auth()->user()->name . " menghapus posisi {$name} dari Batch '{$batch->name}'",
            "Posisi: {$name}"
        );

        return redirect()->route('batch.show', $batch)->with('success', 'Posisi telah berhasil dihapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Position::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
