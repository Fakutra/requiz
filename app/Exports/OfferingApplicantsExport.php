<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Services\ActivityLogger; // ✅ tambahkan
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // ✅ tambahkan
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

        $data = $q->get();

        // ✅ Catat log aktivitas export otomatis
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
