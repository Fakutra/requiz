<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    private int $rowCount = 0;

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function model(array $row)
    {
        // Normalize TYPE
        $typeLower = strtolower(trim($row['type'] ?? ''));
        $typeMap = [
            'pg'       => 'PG',
            'multiple' => 'Multiple',
            'poin'     => 'Poin',
            'essay'    => 'Essay',
        ];

        if (!isset($typeMap[$typeLower])) {
            throw new \Exception("Invalid TYPE: '{$row['type']}'. Allowed: PG, Multiple, Poin, Essay.");
        }

        $typeCanonical = $typeMap[$typeLower];

        // Normalize OPTIONS (Capitalize)
        $opt = [];
        foreach (['option_a','option_b','option_c','option_d','option_e'] as $k) {
            $val = $row[$k] ?? null;
            $opt[$k] = $val !== null && trim($val) !== '' ? Str::ucfirst(trim($val)) : null;
        }

        // Normalize ANSWER (UPPERCASE for PG/Multiple)
        $answerNormalized = null;
        $answerRaw = $row['answer'] ?? null;

        if (in_array($typeLower, ['pg','multiple'])) {
            if (!is_null($answerRaw) && trim($answerRaw) !== '') {
                $parts = array_map('trim', explode(',', (string)$answerRaw));
                $parts = array_filter($parts, fn($v) => $v !== '');
                $parts = array_map(fn($v) => strtoupper($v), $parts);
                $answerNormalized = implode(',', $parts);
            } else {
                throw new \Exception("ANSWER is required for type: {$typeCanonical}");
            }
        }

        // Normalize POINTS
        $pts = [];
        foreach (['point_a','point_b','point_c','point_d','point_e'] as $k) {
            $v = $row[$k] ?? null;
            $pts[$k] = ($v === '' || is_null($v)) ? null : (int)$v;
        }

        // Cleanup by Type
        switch ($typeLower) {
            case 'pg':
            case 'multiple':
                $pts = array_map(fn() => null, $pts);
                break;

            case 'poin':
                if ($pts['point_a'] === null && $pts['point_b'] === null) {
                    throw new \Exception("POINT fields are required for type: Poin");
                }
                $answerNormalized = null;
                break;

            case 'essay':
                $opt = array_map(fn() => null, $opt);
                $pts = array_map(fn() => null, $pts);
                $answerNormalized = null;
                break;
        }

        // Count success row
        $this->rowCount++;

        // Final insert
        return new Question([
            'type'      => $typeCanonical,
            'category'  => $row['category'] ?? null,
            'question'  => $row['question'] ?? null,
            'option_a'  => $opt['option_a'],
            'option_b'  => $opt['option_b'],
            'option_c'  => $opt['option_c'],
            'option_d'  => $opt['option_d'],
            'option_e'  => $opt['option_e'],
            'answer'    => $answerNormalized,
            'point_a'   => $pts['point_a'],
            'point_b'   => $pts['point_b'],
            'point_c'   => $pts['point_c'],
            'point_d'   => $pts['point_d'],
            'point_e'   => $pts['point_e'],
        ]);
    }

    public function rules(): array
    {
        return [
            'type'     => ['required'],
            'question' => ['required', 'string'],
            'category' => ['nullable', 'string'],
        ];
    }
}
