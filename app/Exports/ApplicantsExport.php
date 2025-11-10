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

class ApplicantsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?string $search,
        private ?string $positionId,
        private ?string $batchId
    ) {}

    public function query()
    {
        $q = Applicant::query()
            ->with([
                'position:id,name',
                'batch:id,name',
            ])
            ->select('applicants.*');

        // 🔎 search di kolom applicants sendiri + relasi position
        if (!empty($this->search)) {
            $needle = '%'.mb_strtolower(trim($this->search)).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(applicants.name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(applicants.email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(applicants.jurusan) LIKE ?', [$needle])
                  ->orWhereHas('position', fn($p) =>
                      $p->whereRaw('LOWER(name) LIKE ?', [$needle])
                  );
            });
        }

        if (!empty($this->positionId)) $q->where('applicants.position_id', $this->positionId);
        if (!empty($this->batchId))    $q->where('applicants.batch_id', $this->batchId);

        // log ringan
        try {
            $count = (clone $q)->count();
            $user  = Auth::user()?->name ?? 'System';
            ActivityLogger::log(
                'export',
                'Data Pelamar',
                "{$user} mengekspor data pelamar ({$count} baris)"
                .($this->batchId ? " | Batch {$this->batchId}" : " | Semua Batch")
                .($this->positionId ? " | Posisi {$this->positionId}" : ""),
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal log export pelamar: '.$e->getMessage());
        }

        // urut by applicants.name (fallback: applicants.id)
        return $q->orderBy('applicants.name')->orderBy('applicants.id');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'NIK',
            'No Telp',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Pendidikan',
            'Universitas',
            'Jurusan',
            'Tahun Lulus',
            'Posisi',
            'Batch',
            'Status',
            'Skills',
            'Ekspektasi Gaji (Rp)',
            'CV Path',
            'Dokumen Tambahan Path',
        ];
    }

    public function map($a): array
    {
        // $a->birthdate udah cast date di model Applicant (recommended)
        $birth = $a->birthdate;

        return [
            $a->id,
            $a->name,
            $a->email,
            $a->identity_num,
            $a->phone_number,
            $a->birthplace,
            $birth ? $birth->translatedFormat('j F Y') : null,
            $a->address,
            $a->pendidikan,
            $a->universitas,
            $a->jurusan,
            $a->thn_lulus,
            $a->position?->name,
            $a->batch?->name ?? $a->batch_id,
            $a->status,
            $a->skills,
            isset($a->ekspektasi_gaji) ? number_format((int)$a->ekspektasi_gaji, 0, ',', '.') : null,
            $a->cv_document,
            $a->doc_tambahan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
