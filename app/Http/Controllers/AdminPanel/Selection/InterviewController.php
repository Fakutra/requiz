<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\InterviewResult;
use Illuminate\Http\Request;
use App\Services\SelectionLogger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Batch;
use App\Models\Position;
use App\Exports\InterviewApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ActivityLogger; // ✅ Tambahkan

class InterviewController extends Controller
{
    protected string $stage = 'Seleksi Interview';

    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : collect();

        $q = Applicant::with(['position', 'batch', 'latestEmailLog', 'myInterviewResult'])
            ->whereIn('status', [
                'Interview',
                'Offering',
                'Menerima Offering',
                'Tidak Lolos Interview',
                'Menolak Offering',
            ]);

        if ($batchId) $q->where('batch_id', $batchId);
        if ($positionId) $q->where('position_id', $positionId);
        if ($search !== '') {
            $needle = "%".mb_strtolower($search)."%";
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle]);
            });
        }

        $applicants = $q->orderBy('name')->paginate(20)->appends($request->query());

        foreach ($applicants as $a) {

            // ========= TES TULIS (FINAL & MAX) =========
            $testResult = $a->latestTestResult;
            $finalTotal = 0;
            $maxTotal   = 0;

            if ($testResult) {
                foreach ($testResult->sectionResults as $sr) {
                    $section = $sr->testSection;
                    if (!$section) continue;

                    $rawScore  = (float) ($sr->score ?? 0);
                    $questions = $section->questionBundle->questions ?? collect();

                    $isPersonality = $questions->contains(fn($q) => $q->type === 'Poin');

                    // Hitung Max Tiap Section (HANYA PG, Multiple, Essay yang masuk maxTotal)
                    if ($isPersonality) {
                        // personality raw TIDAK ikut max_total (hanya untuk kalkulasi final)
                        $maxSection = $questions->count() * 5; // tetap dihitung untuk persentase
                    } else {
                        $pgCount     = $questions->where('type','PG')->count();
                        $multiCount  = $questions->where('type','Multiple')->count();
                        $essayCount  = $questions->where('type','Essay')->count();
                        $maxSection  = ($pgCount * 1) + ($multiCount * 1) + ($essayCount * 3);
                        $maxTotal   += $maxSection;
                    }

                    // Hitung Final Score Section
                    if ($isPersonality) {
                        $percent = $maxSection > 0 ? ($rawScore / $maxSection) * 100 : 0;

                        $rule = DB::table('personality_rules')
                            ->where('min_percentage', '<=', $percent)
                            ->where(function ($q) use ($percent) {
                                $q->where('max_percentage', '>=', $percent)
                                ->orWhereNull('max_percentage');
                            })
                            ->orderByDesc('min_percentage')
                            ->first();

                        $finalTotal += $rule ? (int) $rule->score_value : 0;
                    } else {
                        $finalTotal += $rawScore;
                    }
                }
            }

            $a->quiz_final = $finalTotal ?: null;
            $a->quiz_max   = $maxTotal ?: null;


            // ========= INTERVIEW (FINAL & MAX 100) =========
            $avgInterview = InterviewResult::where('applicant_id', $a->id)->avg('score');
            $a->interview_final = $avgInterview ? round($avgInterview, 2) : null;
            $a->interview_max   = 100;


            // ========= DATA TAMBAHAN (seperti sebelumnya) =========
            $a->praktik_score  = $a->technicalTestAnswers()->latest()->value('score');
            $a->potential_by   = InterviewResult::where('applicant_id', $a->id)
                                ->where('potencial', true)
                                ->with('user')
                                ->get()
                                ->map(fn($r) => $r->user?->name)
                                ->filter()
                                ->toArray();
            $a->interview_note = InterviewResult::where('applicant_id', $a->id)
                                ->orderByDesc('id')
                                ->value('note');
        }
        
        return view('admin.applicant.seleksi.interview.index', compact(
            'batches', 'positions', 'batchId', 'positionId', 'applicants'
        ));
    }

    /**
     * EXPORT data interview ke Excel + catat log
     */
    public function export(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = $request->query('search');

        $fileName = 'Interview_Applicants_' . now()->format('Ymd_His') . '.xlsx';

        // ✅ Catat log aktivitas export
        // ActivityLogger::log(
        //     'export',
        //     'Seleksi Interview',
        //     Auth::user()->name." mengekspor data peserta Seleksi Interview ke file Excel.",
        //     "File: {$fileName}"
        // );

        return Excel::download(
            new InterviewApplicantsExport($batchId, $positionId, $search),
            $fileName
        );
    }

    /**
     * Simpan atau update nilai interview + log perbandingan before/after
     */
    public function storeScore(Request $request)
    {
        $data = $request->validate([
            'applicant_id'     => 'required|exists:applicants,id',
            'poin_kepribadian' => 'required|integer|min:0|max:100',
            'poin_wawasan'     => 'required|integer|min:0|max:100',
            'poin_gestur'      => 'required|integer|min:0|max:100',
            'poin_cara_bicara' => 'required|integer|min:0|max:100',
            'note'             => 'nullable|string',
            'potencial'        => 'nullable',
        ]);

        $total = (
            $data['poin_kepribadian'] +
            $data['poin_wawasan'] +
            $data['poin_gestur'] +
            $data['poin_cara_bicara']
        ) / 4;

        // Cek apakah data sebelumnya sudah ada
        $existing = InterviewResult::where('applicant_id', $data['applicant_id'])
            ->where('user_id', auth()->id())
            ->first();

        $oldData = $existing ? [
            'score'      => number_format($existing->score, 2),
            'note'       => $existing->note,
            'potencial'  => $existing->potencial ? 'Ya' : 'Tidak'
        ] : null;

        $result = InterviewResult::updateOrCreate(
            [
                'applicant_id' => $data['applicant_id'],
                'user_id'      => auth()->id(),
            ],
            [
                'poin_kepribadian' => $data['poin_kepribadian'],
                'poin_wawasan'     => $data['poin_wawasan'],
                'poin_gestur'      => $data['poin_gestur'],
                'poin_cara_bicara' => $data['poin_cara_bicara'],
                'note'             => $data['note'],
                'potencial'        => $request->boolean('potencial'),
                'score'            => $total,
            ]
        );

        // Siapkan data baru
        $newData = [
            'score'     => number_format($result->score, 2),
            'note'      => $result->note,
            'potencial' => $result->potencial ? 'Ya' : 'Tidak'
        ];

        // ✅ Catat log perubahan
        if ($oldData) {
            $changes = [];
            foreach ($newData as $key => $value) {
                if ($oldData[$key] != $value) {
                    $changes[] = "{$key}: '{$oldData[$key]}' → '{$value}'";
                }
            }

            $desc = Auth::user()->name." memperbarui hasil interview peserta "
                .$result->applicant->name." (ID: {$result->applicant_id}) — "
                .implode(', ', $changes);
        } else {
            $desc = Auth::user()->name." menambahkan hasil interview baru untuk peserta "
                .$result->applicant->name." (Score: {$total}, Potensial: "
                .($request->boolean('potencial') ? 'Ya' : 'Tidak').")";
        }

        ActivityLogger::log(
            'update_score',
            'Seleksi Interview',
            $desc,
            "Applicant: {$result->applicant->name}"
        );

        return back()->with('success', 'Penilaian interview berhasil disimpan.');
    }

    /**
     * Tandai hasil interview lolos/tidak lolos + log aktivitas
     */
    public function bulkMark(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
        ]);

        $count = 0;
        foreach ($data['ids'] as $id) {
            $a = Applicant::find($id);
            $oldStatus = $a->status;
            $newStatus = $this->newStatus($data['bulk_action'], $a->status);

            SelectionLogger::write($a, $this->stage, $data['bulk_action'], auth()->id());
            $a->forceFill(['status' => $newStatus])->save();
            $count++;

            // ✅ Catat log perubahan
            ActivityLogger::log(
                $data['bulk_action'],
                'Seleksi Interview',
                Auth::user()->name." mengubah status peserta {$a->name} — status: '{$oldStatus}' → '{$newStatus}'",
                "Applicant ID: {$a->id}"
            );
        }

        return back()->with('success', "{$count} peserta diperbarui.");
    }

    private function newStatus(string $result, string $current): string
    {
        return $result === 'lolos' ? 'Offering' : 'Tidak Lolos Interview';
    }
}
