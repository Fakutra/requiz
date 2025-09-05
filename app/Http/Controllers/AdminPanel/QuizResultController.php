<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\TestResult;
use Illuminate\Support\Collection;

class QuizResultController extends Controller
{
    /**
     * Daftar hasil: susun kolom Section 1..N berdasarkan urutan section di quiz-nya.
     */
    public function index()
    {
        // Ambil hasil + urutan section per test
        $results = TestResult::with([
            'applicant',
            'test.position',
            'test.sections' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'sectionResults',
        ])->orderBy('id', 'desc')->paginate(15);

        // Hitung jumlah section maksimum di semua test untuk header kolom
        $maxSections = $results->getCollection()
            ->map(fn ($r) => $r->test?->sections?->count() ?? 0)
            ->max() ?? 0;

        // Tambahkan properti bantuan "orderedSectionResults" agar blade bisa akses nilai per urutan section
        $results->getCollection()->transform(function (TestResult $r) {
            $ordered = $r->test?->sections?->map(function ($section) use ($r) {
                return $r->sectionResults->firstWhere('test_section_id', $section->id);
            }) ?? collect();

            // simpan koleksi berurutan ke instance (hanya untuk tampilan)
            $r->orderedSectionResults = $ordered->values();
            return $r;
        });

        return view('admin.quiz-results.index', [
            'results'     => $results,
            'maxSections' => (int) $maxSections,
        ]);
    }

    /**
     * Detail 1 hasil: selalu render SEMUA section & SEMUA soal.
     * Untuk yang tidak dikerjakan, tampilkan jawaban "" dan skor 0.
     */
    public function show(TestResult $testResult)
    {
        // Eager load lengkap: urutan section + soal
        $testResult->load([
            'applicant',
            'test.position',
            'test.sections' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'sectionResults',
        ]);

        // Ambil semua Answer milik result ini sekali saja (hemat query)
        $allAnswers = Answer::where('test_result_id', $testResult->id)->get()
            ->groupBy('test_section_id'); // => [test_section_id => collect<Answer>]

        $sectionsPayload = [];
        $grandTotal = 0;

        foreach ($testResult->test->sections as $section) {
            $sr  = $testResult->sectionResults->firstWhere('test_section_id', $section->id);
            $answersInSection = $allAnswers->get($section->id, collect())->keyBy('question_id');

            // Ambil semua soal di section ini (jika ada bundle)
            $questions = optional($section->questionBundle)->questions ?? collect();

            $rows = [];
            $sectionScore = 0;

            foreach ($questions as $q) {
                // Cek ada jawaban di DB?
                $ansRow = $answersInSection->get($q->id);

                // Normalisasi tipe dan nilai default
                $type = (string) $q->type;

                // Default untuk soal yang tidak sempat dikerjakan:
                // - userAnswer: "" (string kosong)
                // - selectedLetters: [] (untuk pilihan ganda)
                // - score: 0 (sesuai permintaan Anda—termasuk essay)
                $userAnswerRaw   = $ansRow ? (string) $ansRow->answer : '';
                $score           = $ansRow ? (is_null($ansRow->score) ? 0 : (float) $ansRow->score) : 0;

                // Opsi terstruktur A–E untuk tampilan
                $options = [
                    'A' => $q->option_a,
                    'B' => $q->option_b,
                    'C' => $q->option_c,
                    'D' => $q->option_d,
                    'E' => $q->option_e,
                ];

                // Poin per opsi (untuk tipe Poin)
                $optionPoints = [
                    'A' => $q->point_a,
                    'B' => $q->point_b,
                    'C' => $q->point_c,
                    'D' => $q->point_d,
                    'E' => $q->point_e,
                ];

                // Jawaban benar (untuk PG/Multiple)
                $correctLetters = [];
                $correctAnswer  = null;
                if (in_array($type, ['PG', 'Multiple'])) {
                    $correctLetters = collect(explode(',', (string) $q->answer))
                        ->map(fn ($x) => strtoupper(trim($x)))
                        ->filter()
                        ->values()
                        ->all();
                    $correctAnswer  = implode(',', $correctLetters);
                }

                // Huruf yang dipilih user (untuk PG/Multiple/Poin)
                $selectedLetters = [];
                if (in_array($type, ['PG', 'Multiple', 'Poin'])) {
                    $selectedLetters = collect(explode(',', (string) $userAnswerRaw))
                        ->map(fn ($x) => strtoupper(trim($x)))
                        ->filter()
                        ->values()
                        ->all();
                }

                // Status benar/salah untuk PG/Multiple (berbasis skor yang sudah kita tetapkan 0 default)
                $status = null;
                if (in_array($type, ['PG', 'Multiple'])) {
                    $status = $score > 0 ? 'correct' : 'wrong';
                }

                $rows[] = [
                    'type'             => $type,
                    'question_text'    => (string) $q->question,
                    'options'          => $options,
                    'option_points'    => $optionPoints,
                    'selected_letters' => $selectedLetters,
                    'correct_letters'  => $correctLetters,
                    'correct_answer'   => $correctAnswer,
                    'user_answer'      => $type === 'Essay' ? $userAnswerRaw : implode(',', $selectedLetters),
                    'score'            => $score,
                    'status'           => $status, // null untuk Essay/Poin di tampilan Anda
                ];

                $sectionScore += $score;
            }

            // Jika bundle kosong, tetap tampilkan section kosong
            $sectionsPayload[] = [
                'id'     => $section->id,
                'name'   => $section->name,
                'score'  => $sr ? (is_null($sr->score) ? $sectionScore : (float) $sr->score) : $sectionScore,
                'answers'=> $rows,
            ];

            $grandTotal += end($sectionsPayload)['score'];
        }

        return view('admin.quiz-results.show', [
            'testResult' => $testResult,
            'sections'   => $sectionsPayload,
            'totalScore' => $grandTotal,
        ]);
    }
}
