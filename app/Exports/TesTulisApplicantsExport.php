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
        $q = Applicant::with(['position', 'batch'])
            ->where('batch_id', $this->batchId)
            ->whereIn('status', ['Tes Tulis', 'Technical Test', 'Tidak Lolos Tes Tulis']);

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
            return [
                'Nama'      => $a->name,
                'Email'     => $a->email,
                'Jurusan'   => $a->jurusan,
                'Posisi'    => $a->position->name ?? '-',
                'Batch'     => $a->batch->name ?? '-',
                'Status'    => $a->status,
                'Created'   => $a->created_at->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Email', 'Jurusan', 'Posisi', 'Batch', 'Status', 'Tanggal Daftar'];
    }
}
