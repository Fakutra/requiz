<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Services\SelectionLogger;
use Illuminate\Support\Facades\Auth;
use App\Exports\TesTulisApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SelectionNotifier;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Throwable;

class TesTulisController extends Controller
{
    protected string $stage = 'Tes Tulis';

    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));
        $status     = $request->query('status');

        if (!$batchId) {
            return back()->with('error', 'Batch wajib dipilih untuk melihat data Tes Tulis.');
        }

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId ? Position::where('batch_id', $batchId)->get() : collect();

        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        $allowedSorts = ['name', 'section_1', 'section_2', 'section_3', 'section_4', 'section_5', 'total_nilai'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'name';

        try {
            $q = Applicant::with([
                'position',
                'batch',
                'latestEmailLog',
                'latestTestResult.test',
                'latestTestResult.sectionResults.testSection',
                'latestTestResult.sectionResults.testSection.questionBundle.questions',
                'latestTestResult.sectionResults.answers.question',
            ])
            ->where('batch_id', $batchId)
            ->whereIn('status', [
                'Tes Tulis','Technical Test','Interview','Offering','Menerima Offering',
                'Tidak Lolos Tes Tulis','Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
            ]);

            if ($positionId) $q->where('position_id', $positionId);
            if ($status)     $q->where('status', $status);

            if ($search !== '') {
                $keyword = '%'.mb_strtolower($search).'%';
                $q->where(function ($w) use ($keyword) {
                    $w->whereRaw('LOWER(applicants.name) LIKE ?', [$keyword])
                      ->orWhereRaw('LOWER(applicants.email) LIKE ?', [$keyword])
                      ->orWhereRaw('LOWER(applicants.jurusan) LIKE ?', [$keyword])
                      ->orWhereHas('position', fn($p) =>
                          $p->whereRaw('LOWER(name) LIKE ?', [$keyword])
                      );
                });
            }

            $applicants = $q->get();

            $maxPersonalityFinal = (int) DB::table('personality_rules')
                ->where('batch_id', $batchId)
                ->max('score_value') ?: 0;

            foreach ($applicants as $a) {
                $testResult = $a->latestTestResult;
                if (!$testResult) {
                    $a->final_total_score = null;
                    $a->max_total_score   = null;
                    continue;
                }

                $finalTotal = 0;
                $maxTotal   = 0;

                foreach ($testResult->sectionResults as $sr) {
                    $section = $sr->testSection;
                    if (!$section) continue;

                    $rawScore  = (float) ($sr->score ?? 0);
                    $questions = $section->questionBundle->questions ?? collect();
                    $isPersonality = $questions->contains(fn($qq) => $qq->type === 'Poin');

                    if ($isPersonality) {
                        $maxTotal += $maxPersonalityFinal;
                    } else {
                        $pg    = $questions->where('type','PG')->count();
                        $multi = $questions->where('type','Multiple')->count();
                        $essay = $questions->where('type','Essay')->count();
                        $maxTotal += ($pg * 1) + ($multi * 1) + ($essay * 3);
                    }

                    if ($isPersonality) {
                        $rawMaxSection = $questions->count() * 5;
                        $percent = $rawMaxSection > 0 ? ($rawScore / $rawMaxSection) * 100 : 0;

                        $rule = DB::table('personality_rules')
                            ->where('batch_id', $batchId)
                            ->where('min_percentage', '<=', $percent)
                            ->where(function ($qq) use ($percent) {
                                $qq->where('max_percentage', '>=', $percent)
                                   ->orWhereNull('max_percentage');
                            })
                            ->orderByDesc('min_percentage')
                            ->first();

                        $finalTotal += $rule ? (int) $rule->score_value : 0;
                    } else {
                        $finalTotal += $rawScore;
                    }
                }

                $a->final_total_score = $finalTotal;
                $a->max_total_score   = $maxTotal;
            }

            $applicants = $applicants->sortBy(function ($a) use ($sort) {
                return match ($sort) {
                    'name'        => mb_strtolower($a->name ?? ''),
                    'total_nilai' => $a->final_total_score ?? -1,
                    default       => null,
                };
            }, SORT_NATURAL, $direction === 'desc');

            $page = (int) request('page', 1);
            $perPage = 20;
            $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
                $applicants->forPage($page, $perPage)->values(),
                $applicants->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('admin.applicant.seleksi.tes-tulis.index', compact(
                'batches', 'positions', 'batchId', 'positionId', 'applicants'
            ));
        } catch (Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Tes Tulis',
                (Auth::user()?->name ?? 'System').' gagal memuat data Tes Tulis',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal memuat data Tes Tulis. Coba lagi atau cek log server.');
        }
    }

    public function scoreEssay(Request $request)
    {
        $data = $request->validate([
            'section_result_ids'   => 'required|array',
            'section_result_ids.*' => 'exists:test_section_results,id',
            'answer_scores'        => 'required|array',
        ]);

        try {
            foreach ($data['section_result_ids'] as $sectionResultId) {
                $sectionResult = \App\Models\TestSectionResult::findOrFail($sectionResultId);

                $totalScore = 0;
                $count = 0;

                foreach ($data['answer_scores'][$sectionResultId] ?? [] as $answerId => $score) {
                    $answer = \App\Models\Answer::where('test_section_result_id', $sectionResult->id)->find($answerId);
                    if ($answer) {
                        $answer->score = $score;
                        $answer->save();
                        $totalScore += (int) $score;
                        $count++;
                    }
                }

                $sectionResult->score = $count > 0 ? $totalScore : null;
                $sectionResult->save();

                $testResult = $sectionResult->testResult;
                if ($testResult) {
                    $total = $testResult->sectionResults()->whereNotNull('score')->sum('score');
                    $testResult->score = $total > 0 ? $total : null;
                    $testResult->save();
                }
            }

            ActivityLogger::log(
                'update_score',
                'Tes Tulis',
                Auth::user()->name . ' memperbarui nilai essay peserta Tes Tulis'
            );

            return back()->with('success', 'Nilai essay berhasil disimpan.');
        } catch (Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Tes Tulis',
                (Auth::user()?->name ?? 'System').' GAGAL menyimpan nilai essay',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal menyimpan nilai essay. Coba lagi atau cek log server.');
        }
    }

    public function bulkMark(Request $r)
    {
        $data = $r->validate([
            'ids'         => 'required|array',
            'ids.*'       => 'exists:applicants,id',
            'bulk_action' => 'required|in:lolos,tidak_lolos',
        ]);

        $success = 0;
        $failed  = 0;
        $failedNames = [];

        foreach ($data['ids'] as $id) {
            $a = null;

            try {
                $a = Applicant::find($id);
                if (!$a) {
                    $failed++;
                    $failedNames[] = "#{$id}";
                    continue;
                }

                SelectionLogger::write($a, $this->stage, $data['bulk_action'], Auth::id());

                $newStatus = $this->newStatus($data['bulk_action'], (string) $a->status);
                $a->forceFill(['status' => $newStatus])->save();

                SelectionNotifier::notify($a, $this->stage, $data['bulk_action'], $newStatus);

                ActivityLogger::log(
                    $data['bulk_action'],
                    'Tes Tulis',
                    Auth::user()->name . " menandai peserta {$a->name} sebagai '".strtoupper($data['bulk_action'])."'",
                    "Applicant ID: {$a->id}"
                );

                $success++;
            } catch (Throwable $e) {
                report($e);

                $failed++;
                $failedNames[] = $a->name ?? "#{$id}";

                ActivityLogger::log(
                    'error',
                    'Tes Tulis',
                    (Auth::user()?->name ?? 'System')." GAGAL memproses peserta ".($a->name ?? "#{$id}")." (bulk_action={$data['bulk_action']})",
                    $e->getMessage()
                );

                continue;
            }
        }

        $resp = back();

        if ($success > 0) {
            $resp = $resp->with('success', "Status {$success} peserta berhasil diperbarui.");
        }

        if ($failed > 0) {
            $names = implode(', ', array_slice($failedNames, 0, 10));
            $suffix = count($failedNames) > 10 ? ' (dan lainnya)' : '';
            $resp = $resp->with('error', "Ada {$failed} peserta yang gagal diproses: {$names}{$suffix}. Coba ulang / cek log ya.");
        }

        if ($success === 0 && $failed > 0) {
            $resp = $resp->with('error', "Semua proses gagal. Total gagal: {$failed}. Cek log server untuk detailnya.");
        }

        return $resp;
    }

    private function newStatus(string $result, string $current): string
    {
        return $result === 'lolos' ? 'Technical Test' : 'Tidak Lolos Tes Tulis';
    }

    public function export(Request $r)
    {
        try {
            return Excel::download(
                new TesTulisApplicantsExport(
                    $r->query('batch'),
                    $r->query('position'),
                    $r->query('search')
                ),
                'seleksi-tes-tulis.xlsx'
            );
        } catch (Throwable $e) {
            report($e);

            ActivityLogger::log(
                'error',
                'Tes Tulis',
                (Auth::user()?->name ?? 'System').' GAGAL export peserta Tes Tulis',
                $e->getMessage()
            );

            return back()->with('error', 'Gagal export Excel Tes Tulis. Coba lagi atau cek log server.');
        }
    }
}
