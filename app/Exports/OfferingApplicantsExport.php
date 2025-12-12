<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                'offering.field',
                'offering.subfield',
                'offering.job',
                'offering.placement',
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

        $data = $q->get();

        // log export
        try {
            $userName = Auth::user()?->name ?? 'System';
            $batchInfo = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $positionInfo = $this->positionId ? "Posisi ID {$this->positionId}" : "Semua Posisi";
            $jurusanInfo = $this->jurusan ? "Jurusan: {$this->jurusan}" : "Semua Jurusan";
            $count = $data->count();

            ActivityLogger::log(
                'export',
                'Offering',
                "{$userName} mengekspor data peserta Offering ({$count} baris, {$batchInfo}, {$positionInfo}, {$jurusanInfo})",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export OfferingApplicants: '.$e->getMessage());
        }

        return $data->map(function ($a) {
            $off = $a->offering;

            return [
                'Nama'            => $a->name,
                'Email'           => $a->email,
                'Jurusan'         => $a->jurusan ?? '-',
                'Batch'           => optional($a->batch)->name ?? '-',
                'Posisi Dilamar'  => optional($a->position)->name ?? '-',
                'Bidang'          => optional(optional($off)->field)->name ?? '-',
                'Sub Bidang'      => optional(optional($off)->subfield)->name ?? '-',
                'Jabatan'         => optional(optional($off)->job)->name ?? '-',
                'Penempatan'      => optional(optional($off)->placement)->name ?? '-',
                'Status'          => $a->status ?? '-',
                'Tanggal Mulai'   => optional($off?->kontrak_mulai)?->format('d-m-Y') ?? '-',
                'Tanggal Selesai' => optional($off?->kontrak_selesai)?->format('d-m-Y') ?? '-',
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
            'Bidang',
            'Sub Bidang',
            'Jabatan',
            'Penempatan',
            'Status',
            'Tanggal Mulai',
            'Tanggal Selesai',
        ];
    }
}
