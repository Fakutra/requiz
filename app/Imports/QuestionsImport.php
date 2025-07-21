<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Membersihkan data poin jika tipe bukan 'Poin'
        if (strtolower($row['type']) !== 'poin') {
            $row['point_a'] = null;
            $row['point_b'] = null;
            $row['point_c'] = null;
            $row['point_d'] = null;
            $row['point_e'] = null;
        }

        // Membersihkan data opsi dan jawaban jika tipe 'Essay'
        if (strtolower($row['type']) === 'essay') {
            $row['option_a'] = null;
            $row['option_b'] = null;
            $row['option_c'] = null;
            $row['option_d'] = null;
            $row['option_e'] = null;
            $row['answer'] = null;
        }

        return new Question([
            'type'     => $row['type'],
            'category' => $row['category'],
            'question' => $row['question'],
            'option_a' => $row['option_a'],
            'option_b' => $row['option_b'],
            'option_c' => $row['option_c'],
            'option_d' => $row['option_d'],
            'option_e' => $row['option_e'],
            'answer'   => $row['answer'],
            'point_a'  => $row['point_a'],
            'point_b'  => $row['point_b'],
            'point_c'  => $row['point_c'],
            'point_d'  => $row['point_d'],
            'point_e'  => $row['point_e'],
        ]);
    }

    /**
     * Aturan validasi untuk setiap baris.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:PG,Multiple,Poin,Essay'],
            'category' => ['required', 'in:Umum,Teknis,Psikologi'],
            'question' => ['required', 'string'],

            // *.field_name digunakan untuk validasi setiap baris
            '*.option_a' => ['required_if:*.type,PG,Multiple,Poin', 'nullable'],
            '*.option_b' => ['required_if:*.type,PG,Multiple,Poin', 'nullable'],

            '*.point_a' => ['required_if:*.type,Poin', 'nullable', 'integer'],
            '*.point_b' => ['required_if:*.type,Poin', 'nullable', 'integer'],
            '*.point_c' => ['required_if:*.type,Poin', 'nullable', 'integer'],
            '*.point_d' => ['required_if:*.type,Poin', 'nullable', 'integer'],
            '*.point_e' => ['required_if:*.type,Poin', 'nullable', 'integer'],

            '*.answer' => ['required_if:*.type,PG,Multiple', 'nullable'],
        ];
    }
}
