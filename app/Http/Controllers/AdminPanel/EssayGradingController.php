<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EssayGradingController extends Controller
{
    /**
     * Tampilkan daftar PESERTA (per TestResult) yang memiliki jawaban essay.
     * Tiap baris punya tombol "Nilai Essay" yang membuka modal berisi semua jawaban essay peserta tsb.
     */
    public function index(Request $request)
    {
        // Ambil hanya testResult yang punya jawaban Essay (pending atau sudah dinilai)
        $results = TestResult::with([
                'applicant',
                'test',
                'sectionResults.testSection',
                // hanya muat jawaban Essay + pertanyaannya
                'sectionResults.answers' => function ($q) {
                    $q->whereHas('question', fn($qq) => $qq->where('type', 'Essay'))
                      ->with('question');
                },
            ])
            ->whereHas('sectionResults.answers', function ($q) {
                $q->whereHas('question', fn($qq) => $qq->where('type', 'Essay'));
            })
            ->latest('finished_at')
            ->latest('started_at')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.essay-grading.index', compact('results'));
    }

    /**
     * Simpan skor untuk SEMUA jawaban essay milik satu peserta (satu TestResult) dari modal.
     * Sekaligus re-calc skor tiap section yang terdampak dan total skor test.
     */
    public function updateResult(Request $request, TestResult $testResult)
    {
        $request->validate([
            'scores'   => 'array',
            'scores.*' => 'nullable|integer|min:0|max:100', // kolom answers.score = unsignedTinyInteger
        ]);

        // Ambil semua answer essay milik testResult ini (lintas section)
        $testResult->load(['sectionResults']); // untuk dapatkan id section
        $sectionResultIds = $testResult->sectionResults->pluck('id')->all();

        $essayAnswers = Answer::whereIn('test_section_result_id', $sectionResultIds)
            ->whereHas('question', fn($q) => $q->where('type', 'Essay'))
            ->get(['id','test_section_result_id','score']);

        // Mapping answer_id -> section_result_id agar efisien re-calc
        $answerMap = $essayAnswers->pluck('test_section_result_id', 'id');

        $updatedSectionResultIds = [];

        DB::transaction(function () use ($request, $essayAnswers, $answerMap, &$updatedSectionResultIds, $testResult) {
            // Update skor jawaban essay (hanya yang dikirim)
            foreach ($request->input('scores', []) as $answerId => $score) {
                if ($score === '' || $score === null) continue;

                $answerId = (int) $answerId;
                if (! $answerMap->has($answerId)) continue; // keamanan: pastikan milik testResult ini

                $score = (int) $score;
                $answer = $essayAnswers->firstWhere('id', $answerId);
                if (! $answer) continue;

                $answer->score = $score;
                $answer->save();

                $updatedSectionResultIds[$answer->test_section_result_id] = true;
            }

            // Re-calc skor untuk setiap SectionResult yang terdampak
            $updatedSectionResultIds = array_keys($updatedSectionResultIds);
            if (!empty($updatedSectionResultIds)) {
                foreach ($updatedSectionResultIds as $srId) {
                    $sum = Answer::where('test_section_result_id', $srId)->sum('score');
                    // update langsung tanpa eager
                    \App\Models\TestSectionResult::where('id', $srId)->update(['score' => $sum]);
                }
            }

            // Re-calc total skor test result
            $newTotal = \App\Models\TestSectionResult::where('test_result_id', $testResult->id)->sum('score');
            $testResult->score = $newTotal;
            $testResult->save();
        });

        return back()->with('status', 'Penilaian essay peserta berhasil disimpan.');
    }
}
