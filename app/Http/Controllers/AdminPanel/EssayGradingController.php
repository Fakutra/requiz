<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Batch;
use App\Models\Position;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EssayGradingController extends Controller
{
    /**
     * Tampilkan daftar PESERTA (per TestResult) yang punya jawaban Essay.
     * Mendukung filter: batch, position, search (nama/email/test), dan pending only.
     */
    public function index(Request $request)
    {
        // Dropdown filters
        $batches = Batch::orderByDesc('start_date')->orderBy('name')->get();

        $positions = Position::when($request->filled('batch_id'), function ($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            })
            ->orderBy('name')
            ->get();

        // Base query: hanya result yang punya jawaban Essay
        $query = TestResult::query()
            ->with([
                'applicant',
                'test',
                'sectionResults.testSection',
                // Muat hanya jawaban Essay + pertanyaannya
                'sectionResults.answers' => function ($q) {
                    $q->whereHas('question', fn($qq) => $qq->where('type', 'Essay'))
                      ->with('question');
                },
            ])
            ->whereHas('sectionResults.answers', function ($q) {
                $q->whereHas('question', fn($qq) => $qq->where('type', 'Essay'));
            });

        // Filter: Batch (via applicant.position.batch_id)
        $query->when($request->filled('batch_id'), function ($q) use ($request) {
            $q->whereHas('applicant.position', fn($qq) =>
                $qq->where('batch_id', $request->batch_id)
            );
        });

        // Filter: Position
        $query->when($request->filled('position_id'), function ($q) use ($request) {
            $q->whereHas('applicant', fn($qq) =>
                $qq->where('position_id', $request->position_id)
            );
        });

        // Filter: Search (nama/email/test)
        $query->when($request->filled('q'), function ($q) use ($request) {
            $s = strtolower($request->q);
            $q->where(function ($w) use ($s) {
                $w->whereHas('applicant', function ($wa) use ($s) {
                        $wa->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"])
                           ->orWhereRaw('LOWER(email) LIKE ?', ["%{$s}%"]);
                    })
                  ->orWhereHas('test', function ($wt) use ($s) {
                        $wt->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"]);
                    });
            });
        });

        // Filter: hanya yang masih pending (ada Essay score NULL)
        $query->when($request->boolean('pending_only'), function ($q) {
            $q->whereHas('sectionResults.answers', function ($qa) {
                $qa->whereHas('question', fn($qq) => $qq->where('type', 'Essay'))
                   ->whereNull('score');
            });
        });

        $results = $query
            ->latest('finished_at')
            ->latest('started_at')
            ->paginate(10)
            ->withQueryString();

        // Ringkasan global (seperti halaman technical-test)
        $counts = [
            'total'    => Answer::whereHas('question', fn($qq) => $qq->where('type', 'Essay'))->count(),
            'scored'   => Answer::whereHas('question', fn($qq) => $qq->where('type', 'Essay'))->whereNotNull('score')->count(),
            'unscored' => Answer::whereHas('question', fn($qq) => $qq->where('type', 'Essay'))->whereNull('score')->count(),
        ];

        return view('admin.essay-grading.index', compact('results', 'batches', 'positions', 'counts'));
    }

    /**
     * Simpan skor Essay untuk satu TestResult (tidak berubah).
     */
    public function updateResult(Request $request, TestResult $testResult)
    {
        $request->validate([
            'scores'   => 'array',
            'scores.*' => 'nullable|integer|min:0|max:100',
        ]);

        $testResult->load(['sectionResults']);
        $sectionResultIds = $testResult->sectionResults->pluck('id')->all();

        $essayAnswers = Answer::whereIn('test_section_result_id', $sectionResultIds)
            ->whereHas('question', fn($q) => $q->where('type', 'Essay'))
            ->get(['id','test_section_result_id','score']);

        $answerMap = $essayAnswers->pluck('test_section_result_id', 'id');

        DB::transaction(function () use ($request, $essayAnswers, $answerMap, $testResult) {
            $updatedSectionResultIds = [];

            foreach ($request->input('scores', []) as $answerId => $score) {
                if ($score === '' || $score === null) continue;
                $answerId = (int) $answerId;
                if (! $answerMap->has($answerId)) continue;

                $score = (int) $score;
                $answer = $essayAnswers->firstWhere('id', $answerId);
                if (! $answer) continue;

                $answer->score = $score;
                $answer->save();

                $updatedSectionResultIds[$answer->test_section_result_id] = true;
            }

            $updatedSectionResultIds = array_keys($updatedSectionResultIds);
            if (!empty($updatedSectionResultIds)) {
                foreach ($updatedSectionResultIds as $srId) {
                    $sum = Answer::where('test_section_result_id', $srId)->sum('score');
                    \App\Models\TestSectionResult::where('id', $srId)->update(['score' => $sum]);
                }
            }

            $newTotal = \App\Models\TestSectionResult::where('test_result_id', $testResult->id)->sum('score');
            $testResult->score = $newTotal;
            $testResult->save();
        });

        return back()->with('status', 'Penilaian essay peserta berhasil disimpan.');
    }
}
