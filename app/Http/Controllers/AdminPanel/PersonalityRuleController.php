<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        // 🧪 VALIDASI MANUAL
        $validator = Validator::make($r->all(), [
            'batch_id'       => 'required|exists:batches,id',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menambahkan rule. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();

            // Normalisasi jika min > max
            if (!is_null($data['max_percentage']) && $data['min_percentage'] > $data['max_percentage']) {
                [$data['min_percentage'], $data['max_percentage']] = [
                    $data['max_percentage'],
                    $data['min_percentage'],
                ];
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
                $user  = Auth::user()?->name ?? 'System';
                $batch = Batch::find($data['batch_id'])?->name ?? "Batch {$data['batch_id']}";
                $min   = $data['min_percentage'];
                $max   = $data['max_percentage'] ?? '∞';
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

        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan Personality Rule: ' . $e->getMessage());
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan rule. Silakan coba lagi.');
        }
    }

    public function update(Request $r, $id)
    {
        // 🧪 VALIDASI MANUAL
        $validator = Validator::make($r->all(), [
            'batch_id'       => 'required|exists:batches,id',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'nullable|numeric|min:0|max:100',
            'score_value'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui rule. Periksa kembali data yang diinput.');
        }

        try {
            $data = $validator->validated();

            if (!is_null($data['max_percentage']) && $data['min_percentage'] > $data['max_percentage']) {
                [$data['min_percentage'], $data['max_percentage']] = [
                    $data['max_percentage'],
                    $data['min_percentage'],
                ];
            }

            // Ambil data lama sebelum update
            $oldData = DB::table('personality_rules')->where('id', $id)->first();

            if (!$oldData) {
                return back()->with('error', 'Rule tidak ditemukan.');
            }

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
                $user  = Auth::user()?->name ?? 'System';
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

        } catch (\Throwable $e) {
            Log::error('Gagal mengupdate Personality Rule: ' . $e->getMessage());
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui rule. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            $rule = DB::table('personality_rules')->where('id', $id)->first();

            if (!$rule) {
                return back()->with('error', 'Rule tidak ditemukan.');
            }

            DB::table('personality_rules')->where('id', $id)->delete();

            // ✅ Log DELETE
            try {
                $user  = Auth::user()?->name ?? 'System';
                $batch = Batch::find($rule->batch_id)?->name ?? "Batch {$rule->batch_id}";
                $min   = $rule->min_percentage;
                $max   = $rule->max_percentage ?? '∞';
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

        } catch (\Throwable $e) {
            Log::error('Gagal menghapus Personality Rule: ' . $e->getMessage());
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus rule. Silakan coba lagi.');
        }
    }
}
