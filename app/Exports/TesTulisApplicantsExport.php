<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Services\ActivityLogger; // ✅ tambahkan
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // ✅ tambahkan
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

        $data = $q->get();

        /**
         * ✅ Catat log aktivitas export otomatis
         * ------------------------------------------------------------
         * Tercatat siapa yang mengekspor, berapa banyak data,
         * dan batch/posisi yang sedang difilter.
         */
        try {
            $userName = Auth::user()?->name ?? 'System';
            $batchInfo = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $positionInfo = $this->positionId ? "Posisi ID {$this->positionId}" : "Semua Posisi";
            $count = $data->count();

            ActivityLogger::log(
                'export',
                'Tes Tulis',
                "{$userName} mengekspor data peserta Tes Tulis ({$count} baris, {$batchInfo}, {$positionInfo})",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export TesTulisApplicants: '.$e->getMessage());
        }

        // 🔹 Mapping hasil export
        return $data->map(function ($a) {
            $latestTest = $a->latestTestResult;

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

                $sections['total'] = $latestTest->score ?? array_sum(array_filter($sections));
            }

            // ✅ Konversi status ke hasil Tes Tulis
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
                'Nama'           => $a->name,
                'Email'          => $a->email,
                'Jurusan'        => $a->jurusan,
                'Posisi'         => $a->position->name ?? '-',
                'Batch'          => $a->batch->name ?? '-',
                'Section 1'      => $sections['section_1'],
                'Section 2'      => $sections['section_2'],
                'Section 3'      => $sections['section_3'],
                'Section 4'      => $sections['section_4'],
                'Section 5'      => $sections['section_5'],
                'Total Nilai'    => $sections['total'],
                'Status'         => $statusTampil,
                'Tanggal Daftar' => $a->created_at->format('d-m-Y H:i:s'),
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
