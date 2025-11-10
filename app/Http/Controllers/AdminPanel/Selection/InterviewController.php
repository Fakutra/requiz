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

        // Parameter sorting
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        // kolom yang boleh di-sort (harus sesuai properti yang nanti tersedia pada collection)
        $allowedSorts = [
            'name', 'universitas', 'jurusan', 'posisi',
            'ekspektasi_gaji', 'dokumen',
            'quiz_final', 'praktik_final', 'interview_final'
        ];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }

        // eager load relasi yg dibutuhkan supaya nggak N+1
        $q = Applicant::with([
                'position',
                'batch',
                'latestEmailLog',
                'latestTestResult.sectionResults.testSection.questionBundle.questions',
                'technicalTestAnswers', // untuk praktik_latest lookup (kita akan query latest per applicant tapi eager buat safety)
            ])
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

        // Ambil semua data dulu
        $applicants = $q->get();

        // ambil nilai-nilai agregat sekali saja (hindari loop DB)
        $applicantIds = $applicants->pluck('id')->all();

        // max personality final untuk batch tertentu (kalau batch diberikan, prioritas ke batch)
        $maxPersonalityFinal = 0;
        try {
            $ruleQ = DB::table('personality_rules');
            if ($batchId) $ruleQ->where('batch_id', $batchId);
            $maxPersonalityFinal = (int) ($ruleQ->max('score_value') ?? 0);
        } catch (\Throwable $e) {
            Log::warning("Gagal baca personality_rules: ".$e->getMessage());
        }

        // avg interview per applicant (single query)
        $avgInterviews = InterviewResult::whereIn('applicant_id', $applicantIds)
            ->select('applicant_id', DB::raw('AVG(score) as avg_score'))
            ->groupBy('applicant_id')
            ->pluck('avg_score', 'applicant_id')
            ->toArray();

        // potential reviewers (grouped) untuk tiap applicant (single query)
        $potentials = InterviewResult::whereIn('applicant_id', $applicantIds)
            ->where('potencial', true)
            ->with('user:id,name')
            ->get()
            ->groupBy('applicant_id')
            ->map(fn($col) => $col->map(fn($r) => $r->user?->name)->filter()->values()->all())
            ->toArray();

        // latest interview note per applicant
        $latestNotes = InterviewResult::whereIn('applicant_id', $applicantIds)
            ->select('applicant_id', 'note', DB::raw('MAX(id) as mx'))
            ->groupBy('applicant_id', 'note')
            // simpler fallback: get latest by id per applicant via subquery
            ->get()
            ->groupBy('applicant_id')
            ->map(fn($g) => last($g)->note ?? null)
            ->toArray();

        // latest technical test answer (latest submitted_at) per applicant
        $latestTechAnswers = DB::table('technical_test_answers')
            ->whereIn('applicant_id', $applicantIds)
            ->select('applicant_id', 'score', 'submitted_at')
            ->orderByDesc('submitted_at')
            ->get()
            ->unique('applicant_id')
            ->keyBy('applicant_id')
            ->toArray();

        // sekarang process tiap applicant tanpa nambah query
        foreach ($applicants as $a) {
            // ========= TES TULIS =========
            $testResult = $a->latestTestResult; // sudah eager loaded
            $finalTotal = 0;
            $maxTotal   = 0;

            if ($testResult) {
                foreach ($testResult->sectionResults as $sr) {
                    $section = $sr->testSection;
                    if (!$section) continue;

                    $rawScore  = (float) ($sr->score ?? 0);
                    $questions = $section->questionBundle->questions ?? collect();
                    $isPersonality = $questions->contains(fn($q) => ($q->type ?? null) === 'Poin');

                    if ($isPersonality) {
                        $maxTotal += $maxPersonalityFinal;
                    } else {
                        $pg    = $questions->where('type','PG')->count();
                        $multi = $questions->where('type','Multiple')->count();
                        $essay = $questions->where('type','Essay')->count();
                        $maxTotal += ($pg * 1) + ($multi * 1) + ($essay * 3);
                    }

                    if ($isPersonality) {
                        $rawMax = $questions->count() * 5;
                        $percent = $rawMax > 0 ? ($rawScore / $rawMax) * 100 : 0;

                        $rule = DB::table('personality_rules')
                            ->when($batchId, fn($q) => $q->where('batch_id', $batchId))
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

            // ========= INTERVIEW =========
            $avgInterview = $avgInterviews[$a->id] ?? null;
            $a->interview_final = $avgInterview ? round($avgInterview, 2) : null;
            $a->interview_max   = 100;

            // ========= PRAKTIK =========
            $latestAns = $latestTechAnswers[$a->id] ?? null;
            $a->praktik_final = $latestAns->score ?? null;
            $a->praktik_max   = 100;

            // ========= DOKUMEN (ada/tidak) =========
            $a->dokumen = $a->cv_path ? 1 : 0;

            // ========= POSISI (nama posisi relasi) =========
            $a->posisi = $a->position?->name ?? null;

            // ========= DATA TAMBAHAN =========
            $a->potential_by = $potentials[$a->id] ?? [];
            $a->interview_note = $latestNotes[$a->id] ?? null;
        }

        // Sorting collection — pakai closure supaya properti dinamis (name/posisi/quiz_final dll) bisa di-handle
        $applicants = $applicants->sortBy(function ($item) use ($sort) {
            $val = $item->{$sort} ?? $item->posisi ?? $item->name ?? '';
            if (is_array($val)) return strtolower(implode(',', $val));
            return is_null($val) ? '' : strtolower((string)$val);
        }, SORT_NATURAL, $direction === 'desc')->values();

        // Pagination manual
        $page = request('page', 1);
        $perPage = 20;
        $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
            $applicants->forPage($page, $perPage),
            $applicants->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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

        // Catat log aktivitas export
        try {
            ActivityLogger::log(
                'export',
                'Seleksi Interview',
                Auth::user()->name." mengekspor data peserta Seleksi Interview ke file Excel.",
                "File: {$fileName}"
            );
        } catch (\Throwable $e) {
            Log::warning("Gagal mencatat activity log export Interview: ".$e->getMessage());
        }

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

        // Catat log perubahan
        if ($oldData) {
            $changes = [];
            foreach ($newData as $key => $value) {
                if (($oldData[$key] ?? null) != $value) {
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

            // Catat log perubahan
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
