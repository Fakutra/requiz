<?php

namespace App\Exports;

use App\Models\Position;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth; // ✅ tambahkan
use Illuminate\Support\Facades\Log;  // ✅ tambahkan
use App\Services\ActivityLogger;     // ✅ tambahkan

class ReportExport implements FromCollection, WithHeadings
{
    protected $batchId;

    public function __construct($batchId = null)
    {
        $this->batchId = $batchId;
    }

    public function collection()
    {
        $data = Position::withCount([
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
                'Tanggal Dibuat' => $p->created_at->format('d F Y, H:i:s'),
                'Pendaftar' => $p->total_pendaftar,
                'Lolos Administrasi' => $p->lolos_administrasi,
                'Lolos Tes Tulis' => $p->lolos_tes_tulis,
                'Lolos Technical Test' => $p->lolos_technical,
                'Lolos Interview' => $p->lolos_interview,
            ]);

        // ✅ Catat aktivitas export report
        try {
            $user = Auth::user()?->name ?? 'System';
            $batchLabel = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $count = $data->count();

            ActivityLogger::log(
                'export',
                'Report',
                "{$user} mengekspor data report seleksi ({$batchLabel}) berisi {$count} posisi.",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export ReportExport: '.$e->getMessage());
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Batch',
            'Posisi',
            'Tanggal Dibuat',
            'Pendaftar',
            'Lolos Administrasi',
            'Lolos Tes Tulis',
            'Lolos Technical Test',
            'Lolos Interview',
        ];
    }
}
