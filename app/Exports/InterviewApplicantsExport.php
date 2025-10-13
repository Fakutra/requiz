<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InterviewApplicantsExport implements FromCollection, WithHeadings
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
                'interviewResults.user'
            ])
            ->whereIn('status', [
                'Interview',
                'Offering',
                'Menerima Offering',
                'Tidak Lolos Interview',
                'Menolak Offering',
            ]);

        if ($this->batchId) {
            $q->where('batch_id', $this->batchId);
        }

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

            // 🧮 Hitung rata-rata dari semua pewawancara
            $avgInterview = $a->interviewResults->count()
                ? number_format(
                    $a->interviewResults->avg(function($r) {
                        $total = $r->poin_kepribadian + $r->poin_wawasan + $r->poin_gestur + $r->poin_cara_bicara;
                        return $total / 4;
                    }), 2
                  )
                : '-';

            // 📝 Gabungkan semua note pewawancara
            $allNotes = $a->interviewResults
                ->filter(fn($r) => !empty($r->note))
                ->map(fn($r) => ($r->user->name ?? 'Tanpa Nama') . ': ' . $r->note)
                ->implode(' | ');
            
            if (empty($allNotes)) {
                $allNotes = '-';
            }

            return [
                'Nama'            => $a->name,
                'Email'           => $a->email,
                'Jurusan'         => $a->jurusan,
                'Posisi'          => $a->position->name ?? '-',
                'Batch'           => $a->batch->name ?? '-',
                'Score Interview' => $avgInterview,
                'Catatan'         => $allNotes,
                'Status'          => $a->status,
                'Tanggal Daftar'  => $a->created_at->format('d-m-Y'),
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
            'Score Interview',
            'Catatan',
            'Status',
            'Tanggal Daftar'
        ];
    }
}
