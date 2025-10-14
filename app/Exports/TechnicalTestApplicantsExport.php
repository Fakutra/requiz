<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Models\TechnicalTestAnswer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TechnicalTestApplicantsExport implements FromCollection, WithHeadings
{
    protected ?string $batchId;
    protected ?string $positionId;
    protected ?string $search;

    public function __construct($batchId = null, $positionId = null, $search = null)
    {
        $this->batchId    = $batchId;
        $this->positionId = $positionId;
        $this->search     = $search;
    }

    public function collection()
    {
        $relevantStatuses = [
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering',
        ];

        $q = Applicant::with(['position', 'batch'])
            ->whereIn('status', $relevantStatuses);

        if (!empty($this->batchId)) {
            $q->where('batch_id', $this->batchId);
        }
        if (!empty($this->positionId)) {
            $q->where('position_id', $this->positionId);
        }
        if (!empty($this->search)) {
            $needle = '%'.mb_strtolower(trim($this->search)).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle]);
            });
        }

        $apps = $q->orderBy('name')->get();

        // Ambil jawaban terbaru per applicant
        $answers = TechnicalTestAnswer::whereIn('applicant_id', $apps->pluck('id'))
            ->orderBy('applicant_id')
            ->orderByDesc('submitted_at')
            ->get()
            ->unique('applicant_id')
            ->keyBy('applicant_id');

        return $apps->map(function ($a) use ($answers) {
            $ans = $answers[$a->id] ?? null;

            // ✅ Logika konversi status
            $statusAsli = $a->status;
            $statusTampil = match (true) {
                str_contains($statusAsli, 'Tidak Lolos Technical Test') => 'Tidak Lolos Technical Test',
                str_contains($statusAsli, 'Interview'),
                str_contains($statusAsli, 'Offering'),
                str_contains($statusAsli, 'Menerima Offering'),
                str_contains($statusAsli, 'Tidak Lolos Interview'),
                str_contains($statusAsli, 'Menolak Offering') => 'Lolos Technical Test',
                $statusAsli === 'Technical Test' => 'Sedang Technical Test',
                default => $statusAsli,
            };

            return [
                'Nama'        => $a->name,
                'Email'       => $a->email,
                'Jurusan'     => $a->jurusan,
                'Posisi'      => $a->position->name ?? '-',
                'Batch'       => $a->batch->name ?? '-',
                'Nilai'       => is_null($ans?->score) ? '-' : $ans->score,
                'Keterangan'  => $ans?->keterangan ?? '-',
                'Status'      => $statusTampil,
                'Dikirim'     => optional($ans?->submitted_at)->format('d-m-Y H:i') ?? '-',
                'Jawaban PDF' => $ans?->answer_path ? url('storage/'.$ans->answer_path) : '-',
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
            'Nilai',
            'Keterangan',
            'Status',
            'Dikirim',
            'Jawaban PDF'
        ];
    }
}
