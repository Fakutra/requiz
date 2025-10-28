<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PersonalityRuleController extends Controller
{
    public function index(Request $r)
    {
        $batches = Batch::orderBy('id')->get();
        $batchId = $r->query('batch') ?: $batches->first()?->id;

        $rules = collect();
        if ($batchId) {
            $rules = DB::table('personality_rules')
                ->where('batch_id', $batchId)
                ->orderBy('min_percentage')
                ->get();
        }

        // ✅ Log aktivitas akses halaman
        try {
            $user = Auth::user()?->name ?? 'System';
            $batchLabel = $batchId ? "Batch ID {$batchId}" : 'Semua Batch';
            ActivityLogger::log(
                'view',
                'Personality Rules',
                "{$user} mengakses halaman aturan kepribadian untuk {$batchLabel}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log view Personality Rules: ' . $e->getMessage());
        }

        return view('admin.personality-rules.index', compact('batches', 'batchId', 'rules'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'batch_id'       => 'required|exists:batches,id',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|numeric|min:0',
        ]);

        // Normalisasi jika min > max
        if (!is_null($data['max_percentage']) && $data['min_percentage'] > $data['max_percentage']) {
            [$data['min_percentage'], $data['max_percentage']] = [$data['max_percentage'], $data['min_percentage']];
        }

        $id = DB::table('personality_rules')->insertGetId([
            'batch_id'       => $data['batch_id'],
            'min_percentage' => $data['min_percentage'],
            'max_percentage' => $data['max_percentage'],
            'score_value'    => $data['score_value'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // ✅ Log CREATE
        try {
            $user = Auth::user()?->name ?? 'System';
            $batch = Batch::find($data['batch_id'])?->name ?? "Batch {$data['batch_id']}";
            $min = $data['min_percentage'];
            $max = $data['max_percentage'] ?? '∞';
            $score = $data['score_value'];

            ActivityLogger::log(
                'create',
                'Personality Rules',
                "{$user} menambahkan rule kepribadian ({$min}% - {$max}%) = skor {$score} pada {$batch}",
                "Rule ID: {$id}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log create Personality Rules: ' . $e->getMessage());
        }

        return back()->with('success', 'Rule berhasil ditambahkan.');
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate([
            'batch_id'       => 'required|exists:batches,id',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|numeric|min:0',
        ]);

        if (!is_null($data['max_percentage']) && $data['min_percentage'] > $data['max_percentage']) {
            [$data['min_percentage'], $data['max_percentage']] = [$data['max_percentage'], $data['min_percentage']];
        }

        // Ambil data lama sebelum update
        $oldData = DB::table('personality_rules')->where('id', $id)->first();

        DB::table('personality_rules')
            ->where('id', $id)
            ->update([
                'batch_id'       => $data['batch_id'],
                'min_percentage' => $data['min_percentage'],
                'max_percentage' => $data['max_percentage'],
                'score_value'    => $data['score_value'],
                'updated_at'     => now(),
            ]);

        // ✅ Log UPDATE
        try {
            $user = Auth::user()?->name ?? 'System';
            $batch = Batch::find($data['batch_id'])?->name ?? "Batch {$data['batch_id']}";
            $changes = [];

            foreach ($data as $key => $value) {
                $oldValue = $oldData->$key ?? null;
                if ($oldValue != $value) {
                    $changes[] = "{$key}: '{$oldValue}' → '{$value}'";
                }
            }

            if (!empty($changes)) {
                ActivityLogger::log(
                    'update',
                    'Personality Rules',
                    "{$user} memperbarui rule kepribadian (Rule ID {$id}) pada {$batch} — " . implode(', ', $changes),
                    "Rule ID: {$id}"
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log update Personality Rules: ' . $e->getMessage());
        }

        return back()->with('success', 'Rule berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rule = DB::table('personality_rules')->where('id', $id)->first();

        DB::table('personality_rules')->where('id', $id)->delete();

        // ✅ Log DELETE
        try {
            $user = Auth::user()?->name ?? 'System';
            $batch = Batch::find($rule->batch_id)?->name ?? "Batch {$rule->batch_id}";
            $min = $rule->min_percentage;
            $max = $rule->max_percentage ?? '∞';
            $score = $rule->score_value;

            ActivityLogger::log(
                'delete',
                'Personality Rules',
                "{$user} menghapus rule kepribadian ({$min}% - {$max}%) = skor {$score} dari {$batch}",
                "Rule ID: {$id}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log delete Personality Rules: ' . $e->getMessage());
        }

        return back()->with('success', 'Rule dihapus.');
    }
}
