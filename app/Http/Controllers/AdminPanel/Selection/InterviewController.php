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
use App\Services\ActivityLogger;
use App\Models\Vendor;
use App\Models\User;

class InterviewController extends Controller
{
    protected string $stage = 'Seleksi Interview';

    public function index(Request $request)
    {
        try {
            $batchId    = $request->query('batch');
            $positionId = $request->query('position');
            $search     = trim((string) $request->query('search'));

            $batches   = Batch::orderBy('id')->get();
            $positions = $batchId ? Position::where('batch_id', $batchId)->get() : collect();
            $vendors   = Vendor::orderBy('nama_vendor')->get();
            $admins    = User::where('role', 'admin')->orderBy('name')->get(); // ⬅️ TAMBAHAN

            // Parameter sorting
            $sort      = $request->query('sort', 'name');
            $direction = $request->query('direction', 'asc');

            $allowedSorts = [
                'name', 'universitas', 'jurusan', 'posisi',
                'ekspektasi_gaji', 'dokumen',
                'quiz_final', 'praktik_final', 'interview_final'
            ];
            if (!in_array($sort, $allowedSorts)) {
                $sort = 'name';
            }

            $q = Applicant::with([
                    'position',
                    'batch',
                    'vendor',
                    'pickedBy',
                    'latestEmailLog',
                    'latestTestResult.sectionResults.testSection.questionBundle.questions',
                    'technicalTestAnswers',
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

            $applicants = $q->get();
            $applicantIds = $applicants->pluck('id')->all();

            // max personality
            $maxPersonalityFinal = 0;
            try {
                $ruleQ = DB::table('personality_rules');
                if ($batchId) $ruleQ->where('batch_id', $batchId);
                $maxPersonalityFinal = (int) ($ruleQ->max('score_value') ?? 0);
            } catch (\Throwable $e) {
                Log::warning("Gagal baca personality_rules: ".$e->getMessage());
            }

            // rata-rata interview per applicant
            $avgInterviews = InterviewResult::whereIn('applicant_id', $applicantIds)
                ->select('applicant_id', DB::raw('AVG(score) as avg_score'))
                ->groupBy('applicant_id')
                ->pluck('avg_score', 'applicant_id')
                ->toArray();

            // potential admins
            $potentialResults = InterviewResult::whereIn('applicant_id', $applicantIds)
                ->where('potencial', true)
                ->with('user:id,name')
                ->get();

            $potentials = $potentialResults
                ->groupBy('applicant_id')
                ->map(fn($col) => $col
                    ->map(fn($r) => $r->user?->name)
                    ->filter()
                    ->values()
                    ->all()
                )
                ->toArray();

            $potentialAdmins = $potentialResults
                ->groupBy('applicant_id')
                ->map(function ($col) {
                    return $col->map(function ($r) {
                        if (!$r->user) return null;
                        return [
                            'id'   => $r->user->id,
                            'name' => $r->user->name,
                        ];
                    })->filter()->values()->all();
                })
                ->toArray();

            // latest notes
            $latestNotes = InterviewResult::whereIn('applicant_id', $applicantIds)
                ->select('applicant_id', 'note', DB::raw('MAX(id) as mx'))
                ->groupBy('applicant_id', 'note')
                ->get()
                ->groupBy('applicant_id')
                ->map(fn($g) => last($g)->note ?? null)
                ->toArray();

            // latest technical answers
            $latestTechAnswers = DB::table('technical_test_answers')
                ->whereIn('applicant_id', $applicantIds)
                ->select('applicant_id', 'score', 'submitted_at')
                ->orderByDesc('submitted_at')
                ->get()
                ->unique('applicant_id')
                ->keyBy('applicant_id')
                ->toArray();

            foreach ($applicants as $a) {
                // TES TULIS
                $testResult = $a->latestTestResult;
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

                // INTERVIEW
                $avgInterview = $avgInterviews[$a->id] ?? null;
                $a->interview_final = $avgInterview ? round($avgInterview, 2) : null;
                $a->interview_max   = 100;

                // PRAKTIK
                $latestAns = $latestTechAnswers[$a->id] ?? null;
                $a->praktik_final = $latestAns->score ?? null;
                $a->praktik_max   = 100;

                // dokumen
                $a->dokumen = $a->cv_path ? 1 : 0;

                // posisi
                $a->posisi = $a->position?->name ?? null;

                // data tambahan
                $a->potential_by     = $potentials[$a->id] ?? [];
                $a->potential_admins = $potentialAdmins[$a->id] ?? [];
                $a->interview_note   = $latestNotes[$a->id] ?? null;

                $a->picked_by_name = $a->pickedBy?->name;
            }

            $applicants = $applicants->sortBy(function ($item) use ($sort) {
                $val = $item->{$sort} ?? $item->posisi ?? $item->name ?? '';
                if (is_array($val)) return strtolower(implode(',', $val));
                return is_null($val) ? '' : strtolower((string)$val);
            }, SORT_NATURAL, $direction === 'desc')->values();

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
                'batches',
                'positions',
                'batchId',
                'positionId',
                'applicants',
                'vendors',
                'admins' // ⬅️ DIKIRIM KE VIEW
            ));
        } catch (\Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Seleksi Interview',
                (Auth::user()?->name ?? 'System').' gagal memuat data Seleksi Interview',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal memuat data Interview. Coba refresh atau cek log server.');
        }
    }

    public function export(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = $request->query('search');

        $fileName = 'Interview_Applicants_' . now()->format('Ymd_His') . '.xlsx';

        try {
            ActivityLogger::log(
                'export',
                'Seleksi Interview',
                Auth::user()->name." mengekspor data peserta Seleksi Interview ke file Excel.",
                "File: {$fileName}"
            );

            return Excel::download(
                new InterviewApplicantsExport($batchId, $positionId, $search),
                $fileName
            );
        } catch (\Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Seleksi Interview',
                (Auth::user()?->name ?? 'System').' GAGAL export data Interview',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal export Excel Interview. Coba lagi atau cek log server.');
        }
    }

    public function storeScore(Request $request)
    {
        try {
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

            $newData = [
                'score'     => number_format($result->score, 2),
                'note'      => $result->note,
                'potencial' => $result->potencial ? 'Ya' : 'Tidak'
            ];

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
        } catch (\Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Seleksi Interview',
                (Auth::user()?->name ?? 'System').' GAGAL menyimpan nilai interview',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal menyimpan penilaian interview. Coba ulang.');
        }
    }

    protected function newStatus(string $action, string $currentStatus): string
    {
        if ($action === 'lolos') {
            if (in_array($currentStatus, ['Offering', 'Menerima Offering', 'Menolak Offering'])) {
                return $currentStatus;
            }
            return 'Offering';
        }

        if ($action === 'tidak_lolos') {
            return 'Tidak Lolos Interview';
        }

        return $currentStatus;
    }

    public function bulkMark(Request $r)
    {
        $data = $r->validate([
            'ids'         => 'required|array',
            'ids.*'       => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
            'vendor_id'   => 'nullable|exists:vendors,id',
            'picked_by'   => 'nullable|exists:users,id',
        ]);

        if ($data['bulk_action'] === 'lolos') {
            if (empty($data['vendor_id'])) {
                return back()->with('error', 'Vendor wajib dipilih untuk peserta yang diloloskan.');
            }
            if (empty($data['picked_by'])) {
                return back()->with('error', 'Picked By wajib dipilih untuk peserta yang diloloskan.');
            }
        }

        $success = 0;
        $failed  = 0;
        $failedNames = [];

        foreach ($data['ids'] as $id) {
            try {
                $a = Applicant::find($id);
                if (!$a) {
                    $failed++;
                    $failedNames[] = "#{$id}";
                    continue;
                }

                $oldStatus = $a->status;
                $newStatus = $this->newStatus($data['bulk_action'], $a->status);

                SelectionLogger::write($a, $this->stage, $data['bulk_action'], auth()->id());

                $payload = ['status' => $newStatus];
                if ($data['bulk_action'] === 'lolos') {
                    $payload['vendor_id'] = $data['vendor_id'];
                    $payload['picked_by'] = $data['picked_by'];
                }

                $a->forceFill($payload)->save();

                ActivityLogger::log(
                    $data['bulk_action'],
                    'Seleksi Interview',
                    Auth::user()->name." mengubah status peserta {$a->name} — '{$oldStatus}' → '{$newStatus}'",
                    "Applicant ID: {$a->id}"
                );

                $success++;
            } catch (\Throwable $e) {
                report($e);

                $failed++;
                $failedNames[] = $a->name ?? "#{$id}";

                ActivityLogger::log(
                    'error',
                    'Seleksi Interview',
                    (Auth::user()?->name ?? 'System').' GAGAL update status interview',
                    $e->getMessage()
                );
            }
        }

        $resp = back();

        if ($success > 0) {
            $resp = $resp->with('success', "Status {$success} peserta berhasil diperbarui.");
        }

        if ($failed > 0) {
            $names = implode(', ', array_slice($failedNames, 0, 10));
            $suffix = count($failedNames) > 10 ? ' (dan lainnya)' : '';
            $resp = $resp->with('error', "Ada {$failed} peserta gagal diproses: {$names}{$suffix}.");
        }

        return $resp;
    }

    public function bulkSetPickedBy(Request $request)
    {
        $data = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'picked_by'    => 'required|exists:users,id',
        ]);

        $applicant = Applicant::with('pickedBy')->findOrFail($data['applicant_id']);

        $oldName = $applicant->pickedBy?->name;
        $newUser = User::find($data['picked_by']);

        $applicant->picked_by = $data['picked_by'];
        $applicant->save();

        try {
            ActivityLogger::log(
                'set_picked_by',
                'Seleksi Interview',
                Auth::user()->name
                ." mengubah Picked By untuk peserta {$applicant->name}"
                ." dari '".($oldName ?? '-')."' menjadi '{$newUser->name}'",
                "Applicant ID: {$applicant->id}"
            );
        } catch (\Throwable $e) {
            Log::warning("Gagal mencatat activity log set_picked_by: ".$e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status'      => 'ok',
                'applicant_id'=> $applicant->id,
                'picked_by'   => [
                    'id'   => $newUser->id,
                    'name' => $newUser->name,
                ],
            ]);
        }

        return back()->with('success', 'Picked By berhasil diperbarui.');
    }
}
