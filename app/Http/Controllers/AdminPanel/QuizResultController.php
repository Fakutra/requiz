<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use App\Models\Question;
use Illuminate\Http\Request;

class QuizResultController extends Controller
{
    public function index(Request $request)
    {
        // List semua hasil (terbaru di atas), bisa ditambah filter jika perlu
        $results = TestResult::with([
                'applicant',           // name, email, dll
                'applicant.position',  // posisi yang dilamar
                'test.position',       // posisi pada test
            ])
            ->latest('finished_at')
            ->latest('started_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.quiz-results.index', compact('results'));
    }

    public function show(TestResult $testResult)
    {
        // Muat detail lengkap untuk tampilan per section + per soal
        $testResult->load([
            'applicant',
            'applicant.position',
            'test.position',
            'sectionResults.testSection',
            'sectionResults.answers.question',
        ]);

        // Hitung total skor (fallback jika kolom score null)
        $totalScore = $testResult->score ?? $testResult->sectionResults->sum('score');

        // Siapkan data terformat per section -> per answer
        $sections = $testResult->sectionResults->map(function ($sr) {
            $sectionName = optional($sr->testSection)->name ?? 'Section';
            $sectionScore = $sr->score;

            $answers = $sr->answers->map(function ($ans) {
                /** @var Question $q */
                $q = $ans->question;
                $type = $q->type ?? '-';
                $userAns = (string) $ans->answer;
                $correctAns = (string) ($q->answer ?? '');

                // Tampilkan jawaban user dalam format "A. Teks" / "A,B. ..."
                $userAnsPretty = $this->formatAnswerPretty($q, $userAns);

                // Tampilkan jawaban benar dalam format yang sama
                $correctPretty = $this->formatAnswerPretty($q, $correctAns);

                // Tentukan benar/salah/pending
                $judge = $this->judge($q, $userAns);

                return [
                    'question_text'  => $q->question,
                    'type'           => $type,
                    'user_answer'    => $userAnsPretty ?: '—',
                    'correct_answer' => $correctPretty ?: '—',
                    'score'          => is_null($ans->score) ? '—' : $ans->score,
                    'status'         => $judge, // 'correct' | 'wrong' | 'pending' (essay/no key)
                ];
            });

            return [
                'name'    => $sectionName,
                'score'   => $sectionScore,
                'answers' => $answers,
            ];
        });

        return view('admin.quiz-results.show', compact('testResult', 'totalScore', 'sections'));
    }

    /**
     * Menentukan status jawaban (correct / wrong / pending) berdasarkan tipe soal.
     */
    private function judge(Question $q, string $userRaw): string
    {
        $type = $q->type;

        if ($type === 'Essay') {
            // Essay dinilai manual → pending (meskipun score bisa terisi nanti)
            return 'pending';
        }

        if ($type === 'PG') {
            if ($userRaw === '') return 'wrong';
            return strtoupper($userRaw) === strtoupper((string) $q->answer) ? 'correct' : 'wrong';
        }

        if ($type === 'Multiple') {
            // Bandingkan set persis
            $correct = collect(explode(',', (string) $q->answer))
                ->map(fn ($x) => strtoupper(trim($x)))
                ->filter();
            $user = collect(explode(',', $userRaw))
                ->map(fn ($x) => strtoupper(trim($x)))
                ->filter();
            if ($user->isEmpty()) return 'wrong';
            return $user->diff($correct)->isEmpty() && $correct->diff($user)->isEmpty() ? 'correct' : 'wrong';
        }

        if ($type === 'Poin') {
            // Tidak ada "benar/salah", lebih ke poin; anggap correct jika poin > 0
            $map = [
                'A' => $q->point_a, 'B' => $q->point_b, 'C' => $q->point_c,
                'D' => $q->point_d, 'E' => $q->point_e,
            ];
            $p = $map[strtoupper($userRaw)] ?? 0;
            return $p > 0 ? 'correct' : 'wrong';
        }

        return 'pending';
    }

    /**
     * Mengubah "A,B" menjadi "A. <teks A>; B. <teks B>"
     * Untuk Essay, kembalikan teks mentah.
     */
    private function formatAnswerPretty(Question $q, string $letters): string
    {
        if ($q->type === 'Essay') {
            return trim($letters);
        }

        $letters = trim($letters);
        if ($letters === '') return '';

        $opts = [
            'A' => $q->option_a,
            'B' => $q->option_b,
            'C' => $q->option_c,
            'D' => $q->option_d,
            'E' => $q->option_e,
        ];

        $parts = collect(explode(',', $letters))
            ->map(fn ($x) => strtoupper(trim($x)))
            ->filter()
            ->map(function ($L) use ($opts) {
                $text = $opts[$L] ?? '';
                return $text !== '' ? "{$L}. {$text}" : $L;
            });

        return $parts->join('; ');
    }
}
