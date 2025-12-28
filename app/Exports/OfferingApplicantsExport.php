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
    protected $status; // Ganti jurusan menjadi status

    public function __construct($batchId, $positionId, $search, $status)
    {
        $this->batchId    = $batchId;
        $this->positionId = $positionId;
        $this->search     = $search;
        $this->status     = $status; // Ganti jurusan menjadi status
    }

    public function collection()
    {
        $q = Applicant::with([
                'position',
                'batch',
                'offering.field',
                'offering.subfield',
                'offering.job',
                'offering.seksi',
            ])
            ->whereIn('status', ['Offering', 'Menerima Offering', 'Menolak Offering']);

        if ($this->batchId) {
            $q->where('batch_id', $this->batchId);
        }

        if ($this->positionId) {
            $q->where('position_id', $this->positionId);
        }

        if ($this->status) {
            $q->where('status', $this->status); // Filter berdasarkan status
        }

        // 🔥 PERBAIKAN SEARCH: Sama seperti di Controller
        if ($this->search) {
            $needle = "%" . mb_strtolower($this->search) . "%";
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereHas('position', function($p) use ($needle) {
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle]);
                  })
                  ->orWhereHas('offering.job', function($j) use ($needle) {
                      $j->whereRaw('LOWER(name) LIKE ?', [$needle]);
                  })
                  ->orWhereHas('offering.field', function($f) use ($needle) {
                      $f->whereRaw('LOWER(name) LIKE ?', [$needle]);
                  })
                  ->orWhereHas('offering.subfield', function($sf) use ($needle) {
                      $sf->whereRaw('LOWER(name) LIKE ?', [$needle]);
                  })
                  ->orWhereHas('offering.seksi', function($s) use ($needle) {
                      $s->whereRaw('LOWER(name) LIKE ?', [$needle]);
                  });
            });
        }

        $data = $q->get();

        // log export
        try {
            $userName = Auth::user()?->name ?? 'System';
            $batchInfo = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $positionInfo = $this->positionId ? "Posisi ID {$this->positionId}" : "Semua Posisi";
            $statusInfo = $this->status ? "Status: {$this->status}" : "Semua Status";
            $count = $data->count();

            ActivityLogger::log(
                'export',
                'Offering',
                "{$userName} mengekspor data peserta Offering ({$count} baris, {$batchInfo}, {$positionInfo}, {$statusInfo})",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export OfferingApplicants: '.$e->getMessage());
        }

        return $data->map(function ($a) {
            $off = $a->offering;

            return [
                'Nama'              => $a->name,
                'Email'             => $a->email,
                'Jurusan'           => $a->jurusan ?? '-',
                'Batch'             => optional($a->batch)->name ?? '-',
                'Posisi Dilamar'    => optional($a->position)->name ?? '-',
                'Bidang'            => optional(optional($off)->field)->name ?? '-',
                'Sub Bidang'        => optional(optional($off)->subfield)->name ?? '-',
                'Jabatan'           => optional(optional($off)->job)->name ?? '-',
                'Seksi'             => optional($off?->seksi)->name ?? '-',
                'Status'            => $a->status ?? '-',
                'By'                => $this->formatDecisionBy($off?->decision_by), // Kolom baru
                'Tanggal Mulai'     => optional($off?->kontrak_mulai)?->format('d-m-Y') ?? '-',
                'Tanggal Selesai'   => optional($off?->kontrak_selesai)?->format('d-m-Y') ?? '-',
                'Deadline Offering' => optional($off?->response_deadline)?->format('d-m-Y H:i') ?? '-', // Kolom baru
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
            'Seksi',
            'Status',
            'By', // Kolom baru
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Deadline Offering', // Kolom baru
        ];
    }

    /**
     * Format decision_by untuk kolom "By"
     */
    private function formatDecisionBy($decisionBy)
    {
        if (!$decisionBy) {
            return '-';
        }

        $mapping = [
            'system' => 'System',
            'user' => 'Pelamar',
            'admin' => 'Admin',
            'vendor' => 'Vendor',
        ];

        return $mapping[$decisionBy] ?? ucfirst($decisionBy);
    }
}