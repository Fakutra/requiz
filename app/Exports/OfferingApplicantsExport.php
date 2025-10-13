<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OfferingApplicantsExport implements FromCollection, WithHeadings
{
    protected $batchId;
    protected $positionId;
    protected $search;
    protected $jurusan;

    public function __construct($batchId, $positionId, $search, $jurusan)
    {
        $this->batchId    = $batchId;
        $this->positionId = $positionId;
        $this->search     = $search;
        $this->jurusan    = $jurusan;
    }

    public function collection()
    {
        $q = Applicant::with([
                'position', 
                'batch',
                'offering.division',
                'offering.job',
                'offering.placement'
            ])
            ->whereIn('status', ['Offering', 'Menerima Offering', 'Menolak Offering']);

        if ($this->batchId) {
            $q->where('batch_id', $this->batchId);
        }

        if ($this->positionId) {
            $q->where('position_id', $this->positionId);
        }

        if ($this->jurusan) {
            $needle = "%".mb_strtolower($this->jurusan)."%";
            $q->whereRaw('LOWER(jurusan) LIKE ?', [$needle]);
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
                'Nama'            => $a->name,
                'Email'           => $a->email,
                'Jurusan'         => $a->jurusan ?? '-',
                'Batch'           => $a->batch->name ?? '-',
                'Posisi Dilamar'  => $a->position->name ?? '-',
                'Divisi'          => optional($a->offering->division ?? null)->name ?? '-',
                'Jabatan'         => optional($a->offering->job ?? null)->name ?? '-',
                'Penempatan'      => optional($a->offering->placement ?? null)->name ?? '-',
                'Status'          => $a->status ?? '-',
                'Tanggal Mulai'   => optional($a->offering->kontrak_mulai ?? null)?->format('d-m-Y') ?? '-',
                'Tanggal Selesai' => optional($a->offering->kontrak_selesai ?? null)?->format('d-m-Y') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Jurusan',
            'Batch',
            'Posisi Dilamar',
            'Divisi',
            'Jabatan',
            'Penempatan',
            'Status',
            'Tanggal Mulai',
            'Tanggal Selesai',
        ];
    }
}
