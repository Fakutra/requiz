<?php

namespace App\Exports;

use App\Models\Applicant;
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
            ->with(['position','batch','latestEmailLog'])
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
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle])
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                  );
            });
        }

        return $q->orderBy('name');
    }

    public function headings(): array
    {
        return [
            // 'ID',
            'NAMA',
            'EMAIL',
            'JURUSAN',
            'POSISI DILAMAR',
            'UMUR',
            'BATCH',
            'STATUS SELEKSI',
            'STATUS EMAIL', // kolom tambahan
        ];
    }

    /**
     * @param \App\Models\Applicant $a
     */
    public function map($a): array
    {
        // Tentukan status seleksi untuk tampilan
        $status = $a->status;
        if ($status === 'Tes Tulis') {
            $status = 'Lolos Seleksi Administrasi';
        }

        // Tentukan status email terakhir khusus tahap "Seleksi Administrasi"
        $log = $a->latestEmailLog;
        if ($log && $log->stage !== 'Seleksi Administrasi') {
            $log = null;
        }

        $emailStatus = 'Belum Dikirim';
        if ($log) {
            $emailStatus = $log->success ? 'Terkirim' : 'Gagal';
        }

        return [
            // $a->id,
            $a->name,
            $a->email,
            $a->jurusan,
            $a->position->name ?? '-',
            $a->age ?? '-',
            $a->batch->name ?? $a->batch_id,
            $status,       // ✅ sudah di-convert
            $emailStatus,  // status email
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // heading bold
        ];
    }
}
