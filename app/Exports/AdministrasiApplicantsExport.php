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
                'user:id,name,email',
                'user.profile:id,user_id,birthdate', // biar accessor age jalan mulus
            ])
            ->whereIn('status', [
                'Seleksi Administrasi',
                'Tes Tulis',
                'Tidak Lolos Seleksi Administrasi',
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
                $w->whereRaw('LOWER(applicants.jurusan) LIKE ?', [$needle])
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                  )
                  ->orWhereHas('user', function ($u) use ($needle) {
                      $u->whereRaw('LOWER(name) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$needle]);
                  });
            });
        }

        // 📝 log export
        try {
            $count = $q->count();
            $userName = Auth::user()?->name ?? 'System';
            $batchInfo = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $positionInfo = $this->positionId ? "Posisi ID {$this->positionId}" : "Semua Posisi";

            ActivityLogger::log(
                'export',
                'Seleksi Administrasi',
                "{$userName} mengekspor data peserta Seleksi Administrasi ({$count} baris, {$batchInfo}, {$positionInfo})",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export AdministrasiApplicants: '.$e->getMessage());
        }

        // 🧭 urutkan by users.name (join buat ordering doang)
        return $q->leftJoin('users', 'users.id', '=', 'applicants.user_id')
                 ->select('applicants.*')
                 ->orderBy('users.name');
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
        // Map status tampilan (Tes Tulis = Lolos Admin)
        $displayStatus = $a->status === 'Tes Tulis'
            ? 'Lolos Seleksi Administrasi'
            : $a->status;

        // Ambil status email terakhir khusus stage ini
        $log = $a->latestEmailLog;
        if ($log && $log->stage !== 'Seleksi Administrasi') {
            $log = null;
        }
        $emailStatus = $log ? ($log->success ? 'Terkirim' : 'Gagal') : 'Belum Dikirim';

        return [
            $a->user->name ?? '-',                 // NAMA
            $a->user->email ?? '-',                // EMAIL
            $a->jurusan ?? '-',                    // JURUSAN
            $a->position->name ?? '-',             // POSISI
            $a->age ?? '-',                        // UMUR (accessor)
            $a->batch->name ?? $a->batch_id,       // BATCH
            $displayStatus,                        // STATUS SELEKSI
            $emailStatus,                          // STATUS EMAIL
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
