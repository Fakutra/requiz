<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdministrasiApplicantsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?string $batchId = null,
        private ?string $positionId = null,
        private ?string $search = null
    ) {}

    public function query()
    {
        $q = Applicant::query()
            ->with([
                'position:id,name',
                'batch:id,name',
                'latestEmailLog',
            ])
            ->whereIn('status', [
                'Seleksi Administrasi',
                'Tes Tulis',
                'Tidak Lolos Seleksi Administrasi',
                // biarin aman kalau udah lanjut
                'Technical Test','Interview','Offering',
                'Menerima Offering','Tidak Lolos Tes Tulis',
                'Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
            ]);

        if (!empty($this->batchId)) {
            $q->where('batch_id', $this->batchId);
        }

        if (!empty($this->positionId)) {
            $q->where('position_id', $this->positionId);
        }

        if (!empty($this->search)) {
            $needle = '%'.mb_strtolower(trim($this->search)).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(applicants.name) LIKE ?', [$needle])      // 🔎 by applicant.name
                  ->orWhereRaw('LOWER(applicants.email) LIKE ?', [$needle])   // 🔎 by applicant.email
                  ->orWhereRaw('LOWER(applicants.jurusan) LIKE ?', [$needle]) // 🔎 by jurusan
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                  );
            });
        }

        // 📝 log export
        try {
            $count = (clone $q)->count();
            $userName    = Auth::user()?->name ?? 'System';
            $batchInfo   = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $positionInfo= $this->positionId ? "Posisi ID {$this->positionId}" : "Semua Posisi";

            ActivityLogger::log(
                'export',
                'Seleksi Administrasi',
                "{$userName} mengekspor data peserta Seleksi Administrasi ({$count} baris, {$batchInfo}, {$positionInfo})",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export AdministrasiApplicants: '.$e->getMessage());
        }

        // 🧭 urutkan by applicants.name langsung
        return $q->select('applicants.*')->orderBy('applicants.name');
    }

    public function headings(): array
    {
        return [
            'NAMA',
            'EMAIL',
            'JURUSAN',
            'POSISI DILAMAR',
            'UMUR',
            'BATCH',
            'STATUS SELEKSI',
            'STATUS EMAIL',
        ];
    }

    public function map($a): array
    {
        // Map status tampilan: semua status >= Tes Tulis dianggap "Lolos Seleksi Administrasi"
        $lolosAdminStatuses = [
            'Tes Tulis','Technical Test','Interview','Offering',
            'Menerima Offering','Tidak Lolos Tes Tulis',
            'Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
        ];

        $displayStatus = in_array($a->status, $lolosAdminStatuses, true)
            ? 'Lolos Seleksi Administrasi'
            : ($a->status ?? '-');

        // Ambil status email terakhir khusus stage ini
        $log = $a->latestEmailLog;
        if ($log && $log->stage !== 'Seleksi Administrasi') {
            $log = null;
        }
        $emailStatus = $log ? ($log->success ? 'Terkirim' : 'Gagal') : 'Belum Dikirim';

        // Umur: pake accessor $a->age (yang ngitung dari applicants.birthdate). Kalau ga ada, fallback '-'.
        return [
            $a->name ?? '-',                      // NAMA (dari applicants.name)
            $a->email ?? '-',                     // EMAIL (dari applicants.email)
            $a->jurusan ?? '-',                   // JURUSAN
            $a->position->name ?? '-',            // POSISI
            $a->age ?? '-',                       // UMUR
            $a->batch->name ?? $a->batch_id,      // BATCH
            $displayStatus,                       // STATUS SELEKSI (mapped)
            $emailStatus,                         // STATUS EMAIL
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
