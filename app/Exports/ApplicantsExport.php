<?php

namespace App\Exports;

use App\Models\Applicant;
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
        $q = Applicant::query()->with(['position','batch']);

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

        if (!empty($this->positionId)) {
            $q->where('position_id', $this->positionId);
        }
        if (!empty($this->batchId)) {
            $q->where('batch_id', $this->batchId);
        }

        return $q->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID','Nama','Email','NIK','No Telp','Tempat Lahir','Tanggal Lahir',
            'Alamat','Pendidikan','Universitas','Jurusan','Tahun Lulus',
            'Posisi','Batch','Status','Skills'
        ];
    }

    public function map($a): array
    {
        return [
            $a->id,
            $a->name,
            $a->email,
            $a->nik,
            $a->no_telp,
            $a->tpt_lahir,
            $a->tgl_lahir?->format('Y-m-d'),
            $a->alamat,
            $a->pendidikan,
            $a->universitas,
            $a->jurusan,
            $a->thn_lulus,
            $a->position->name ?? null,
            $a->batch->name ?? $a->batch_id,
            $a->status,
            $a->skills,
        ];
    }

    /** 🔥 Heading tebal */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // baris pertama (heading) jadi bold
        ];
    }
}
