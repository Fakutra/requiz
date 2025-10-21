<?php

namespace App\Services;

use App\Models\PersonalityRule;
use App\Models\TestSectionResult;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;

class PersonalityScoring
{
    /**
     * Hitung & simpan skor final Personality untuk satu TestSectionResult (section personality).
     * - Menggunakan total poin jawaban (answers.score) sebagai total_score
     * - max_score = jumlah_soal * 5 (skala 1–5 fix)
     * - Persentase = total_score / max_score * 100
     * - Konversi pakai personality_rules -> score_value
     * - Hasil akhir disimpan ke test_section_results.score
     *
     * @return array ringkasan (total_score, total_questions, max_score, percentage, final_value)
     */
    public static function calculateAndSave(int $testSectionResultId): array
    {
        /** @var TestSectionResult $sectionResult */
        $sectionResult = TestSectionResult::query()->findOrFail($testSectionResultId);

        // Ambil semua jawaban pada section ini (hanya yang punya score)
        $answers = Answer::query()
            ->where('test_section_result_id', $testSectionResultId)
            ->whereNotNull('score')
            ->get(['score']);

        $totalQuestions = $answers->count();
        if ($totalQuestions === 0) {
            // Tidak ada soal/ jawaban → kosongkan skor agar jelas
            $sectionResult->score = null;
            $sectionResult->save();

            return [
                'total_score'     => 0,
                'total_questions' => 0,
                'max_score'       => 0,
                'percentage'      => 0.0,
                'final_value'     => null,
            ];
        }

        $totalScore = (int) $answers->sum('score');
        $maxScore   = $totalQuestions * 5; // skala tetap 1–5
        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0.0;

        // Ambil rule yang match (min <= p <= max | max null)
        $rule = PersonalityRule::query()
            ->where('min_percentage', '<=', $percentage)
            ->where(function ($q) use ($percentage) {
                $q->where('max_percentage', '>=', $percentage)
                  ->orWhereNull('max_percentage');
            })
            ->orderByDesc('min_percentage')
            ->first();

        $finalValue = $rule?->score_value;

        // Simpan nilai AKHIR personality ke kolom score (sesuai keputusan kamu)
        $sectionResult->score = $finalValue;
        $sectionResult->save();

        return [
            'total_score'     => $totalScore,
            'total_questions' => $totalQuestions,
            'max_score'       => $maxScore,
            'percentage'      => $percentage,
            'final_value'     => $finalValue,
        ];
    }
}
