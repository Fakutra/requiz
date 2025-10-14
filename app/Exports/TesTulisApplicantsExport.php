<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TesTulisApplicantsExport implements FromCollection, WithHeadings
{
    protected $batchId;
    protected $positionId;
    protected $search;

    public function __construct($batchId, $positionId, $search)
    {
        $this->batchId    = $batchId;
        $this->positionId = $positionId;
        $this->search     = $search;
    }

    public function collection()
    {
        $q = Applicant::with([
            'position',
            'batch',
            'latestTestResult.sectionResults.testSection'
        ])
        ->where('batch_id', $this->batchId)
        ->whereIn('status', [
            'Tes Tulis',
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Tes Tulis',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering'
        ]);

        if ($this->positionId) {
            $q->where('position_id', $this->positionId);
        }

        if ($this->search) {
            $needle = "%".mb_strtolower($this->search)."%";
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle]);
            });
        }

        return $q->get()->map(function ($a) {
            // Ambil hasil tes terbaru
            $latestTest = $a->latestTestResult;

            // Default nilai section kosong
            $sections = [
                'section_1' => null,
                'section_2' => null,
                'section_3' => null,
                'section_4' => null,
                'section_5' => null,
                'total'     => null,
            ];

            if ($latestTest) {
                foreach ($latestTest->sectionResults as $sr) {
                    $sectionNumber = $sr->testSection->order ?? null;
                    if ($sectionNumber && $sectionNumber >= 1 && $sectionNumber <= 5) {
                        $sections["section_{$sectionNumber}"] = $sr->score;
                    }
                }

                // Hitung total nilai (jika ada)
                $sections['total'] = $latestTest->score ?? array_sum(array_filter($sections));
            }

            // ✅ Logika konversi status ke hasil Tes Tulis
            $statusAsli = $a->status;
            $statusTampil = match (true) {
                str_contains($statusAsli, 'Tidak Lolos Tes Tulis') => 'Tidak Lolos Tes Tulis',
                str_contains($statusAsli, 'Tes Tulis') && $statusAsli !== 'Tes Tulis' => 'Lolos Tes Tulis',
                str_contains($statusAsli, 'Technical Test'),
                str_contains($statusAsli, 'Interview'),
                str_contains($statusAsli, 'Offering'),
                str_contains($statusAsli, 'Menerima Offering') => 'Lolos Tes Tulis',
                default => $statusAsli,
            };

            return [
                'Nama'        => $a->name,
                'Email'       => $a->email,
                'Jurusan'     => $a->jurusan,
                'Posisi'      => $a->position->name ?? '-',
                'Batch'       => $a->batch->name ?? '-',
                'Section 1'   => $sections['section_1'],
                'Section 2'   => $sections['section_2'],
                'Section 3'   => $sections['section_3'],
                'Section 4'   => $sections['section_4'],
                'Section 5'   => $sections['section_5'],
                'Total Nilai' => $sections['total'],
                'Status'      => $statusTampil,
                'Tanggal Daftar' => $a->created_at->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Jurusan',
            'Posisi',
            'Batch',
            'Section 1',
            'Section 2',
            'Section 3',
            'Section 4',
            'Section 5',
            'Total Nilai',
            'Status',
            'Tanggal Daftar',
        ];
    }
}
