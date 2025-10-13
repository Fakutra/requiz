<?php

namespace App\Exports;

use App\Models\Position;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    protected $batchId;

    public function __construct($batchId = null)
    {
        $this->batchId = $batchId;
    }

    public function collection()
    {
        return Position::withCount([
            'applicants as total_pendaftar',
            'applicants as lolos_administrasi' => fn($q) => $q->where('status', 'Tes Tulis'),
            'applicants as lolos_tes_tulis' => fn($q) => $q->where('status', 'Technical Test'),
            'applicants as lolos_technical' => fn($q) => $q->where('status', 'Interview'),
            'applicants as lolos_interview' => fn($q) => $q->where('status', 'Offering'),
        ])
        ->when($this->batchId, fn($q) => $q->where('batch_id', $this->batchId))
        ->get()
        ->map(fn($p) => [
            'Batch' => $p->batch_id,
            'Posisi' => $p->name,
            'Tanggal Dibuat' => $p->created_at->format('d F Y, H:i'),
            'Pendaftar' => $p->total_pendaftar,
            'Lolos Administrasi' => $p->lolos_administrasi,
            'Lolos Tes Tulis' => $p->lolos_tes_tulis,
            'Lolos Technical Test' => $p->lolos_technical,
            'Lolos Interview' => $p->lolos_interview,
        ]);
    }

    public function headings(): array
    {
        return [
            'Batch', 'Posisi', 'Tanggal Dibuat', 'Pendaftar', 
            'Lolos Administrasi', 'Lolos Tes Tulis', 
            'Lolos Technical Test', 'Lolos Interview'
        ];
    }
}
