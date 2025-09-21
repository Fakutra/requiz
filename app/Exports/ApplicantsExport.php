<?php

namespace App\Exports;

use App\Models\Applicant;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicantsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $search;

    public function __construct($search)
    {
        $this->search = trim((string) $search);
    }

    /** @return \Illuminate\Database\Eloquent\Builder */
    public function query()
    {
        return Applicant::query()
            ->with(['position:id,name'])
            ->orderBy('name')
            ->when($this->search, function ($q, $s) {
                $q->where(function ($x) use ($s) {
                    $x->where('name', 'like', "%{$s}%")
                      ->orWhere('jurusan', 'like', "%{$s}%")
                      ->orWhere('pendidikan', 'like', "%{$s}%")
                      ->orWhereHas('position', fn ($p) => $p->where('name', 'like', "%{$s}%"));
                });
            });
    }

    public function headings(): array
    {
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
        ];
    }

    public function map($applicant): array
    {
        $tgl = $applicant->tgl_lahir
            ? ( $applicant->tgl_lahir instanceof \DateTimeInterface
                ? $applicant->tgl_lahir->format('Y-m-d')
                : Carbon::parse($applicant->tgl_lahir)->format('Y-m-d') )
            : '';

        $age = $applicant->age ?? ($applicant->tgl_lahir ? Carbon::parse($applicant->tgl_lahir)->age : '');

        return [
            $applicant->name,
            data_get($applicant, 'position.name', '-'),
            $applicant->email,
            (string) $applicant->nik,        // jaga leading zero
            (string) $applicant->no_telp,    // jaga leading zero
            $applicant->tpt_lahir,
            $tgl,
            $age,
            $applicant->alamat,
            $applicant->pendidikan,
            $applicant->universitas,
            $applicant->jurusan,
            $applicant->thn_lulus,
            $applicant->skills,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        // A=Nama, B=Posisi, C=Email, D=NIK, E=No Telp, F=Tpt Lahir, G=Tgl Lahir, H=Umur, I=Alamat, J,K,L,M,N...
        return [
            'D' => NumberFormat::FORMAT_TEXT,              // NIK
            'E' => NumberFormat::FORMAT_TEXT,              // No Telp
            'G' => NumberFormat::FORMAT_DATE_YYYYMMDD2,    // Tgl Lahir
        ];
    }
}
