<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicantsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        $query = Applicant::query()->with('position')->orderBy('name', 'asc');

        // Terapkan filter pencarian yang sama dengan di halaman web
        $query->when($this->search, function ($q, $search) {
            return $q->where('name', 'like', "%{$search}%")
                ->orWhere('jurusan', 'like', "%{$search}%")
                ->orWhere('pendidikan', 'like', "%{$search}%")
                ->orWhereHas('position', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
        });

        return $query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Mendefinisikan header untuk kolom Excel
        return [
            'NAMA',
            'POSISI_DILAMAR',
            'EMAIL',
            'NIK',
            'NO_TELP',
            'TPT_LAHIR',
            'TGL_LAHIR',
            'UMUR',
            'ALAMAT',
            'PENDIDIKAN',
            'UNIVERSITAS',
            'JURUSAN',
            'THN_LULUS',
            'SKILLS',
            // 'Link CV',
        ];
    }

    /**
     * @param mixed $applicant
     * @return array
     */
    public function map($applicant): array
    {
        // Memetakan data dari setiap pelamar ke kolom yang sesuai
        return [
            $applicant->name,
            $applicant->position->name,
            $applicant->email,
            $applicant->nik,
            $applicant->no_telp,
            $applicant->tpt_lahir,
            $applicant->tgl_lahir,
            $applicant->age,
            $applicant->alamat,
            $applicant->pendidikan,
            $applicant->universitas,
            $applicant->jurusan,
            $applicant->thn_lulus,
            $applicant->skills,
            // $applicant->cv_document ? asset('storage/' . $applicant->cv_document) : 'Tidak ada',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Membuat baris header menjadi tebal (bold)
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
