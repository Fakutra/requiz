<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Models\TechnicalTestAnswer;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TechnicalTestApplicantsExport implements FromCollection, WithHeadings
{
    protected ?string $batchId;
    protected ?string $positionId;
    protected ?string $search;

    public function __construct($batchId = null, $positionId = null, $search = null)
    {
        $this->batchId    = $batchId;
        $this->positionId = $positionId;
        $this->search     = $search;
    }

    public function collection()
    {
        $relevantStatuses = [
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering',
        ];

        // ✅ eager load biar hemat query
        $q = Applicant::with([
            'user:id,name,email',
            'position',
            'batch',
        ])->whereIn('status', $relevantStatuses);

        if (!empty($this->batchId)) {
            $q->where('batch_id', $this->batchId);
        }
        if (!empty($this->positionId)) {
            $q->where('position_id', $this->positionId);
        }

        // ✅ search fleksibel (jurusan + applicants.name/email + users.name/email)
        if (!empty($this->search)) {
            $needle = '%'.mb_strtolower(trim($this->search)).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(jurusan) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereHas('user', function ($u) use ($needle) {
                      $u->whereRaw('LOWER(name) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$needle]);
                  });
            });
        }

        // Sort di collection (akses $a->name yg mungkin dari relasi user)
        $apps = $q->get()
            ->sortBy(fn($a) => mb_strtolower(($a->name ?? $a->user?->name ?? '')), SORT_NATURAL)
            ->values();

        // 🧾 Log aktivitas export
        try {
            $userName     = Auth::user()?->name ?? 'System';
            $batchInfo    = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $positionInfo = $this->positionId ? "Posisi ID {$this->positionId}" : "Semua Posisi";
            $count        = $apps->count();

            ActivityLogger::log(
                'export',
                'Technical Test',
                "{$userName} mengekspor data peserta Technical Test ({$count} baris, {$batchInfo}, {$positionInfo})",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export TechnicalTestApplicants: '.$e->getMessage());
        }

        // Ambil jawaban terbaru per applicant
        $answers = TechnicalTestAnswer::whereIn('applicant_id', $apps->pluck('id'))
            ->orderBy('applicant_id')
            ->orderByDesc('submitted_at')
            ->get()
            ->unique('applicant_id')
            ->keyBy('applicant_id');

        return $apps->map(function ($a) use ($answers) {
            $ans = $answers[$a->id] ?? null;

            // 🎯 Normalisasi status tampilan
            $lolosTechStatuses = [
                'Interview',
                'Offering',
                'Menerima Offering',
                'Tidak Lolos Interview',
                'Menolak Offering',
            ];
            $statusAsli   = (string) $a->status;
            $statusTampil = in_array($statusAsli, $lolosTechStatuses, true)
                ? 'Lolos Technical Test'
                : ($statusAsli === 'Tidak Lolos Technical Test'
                    ? 'Tidak Lolos Technical Test'
                    : ($statusAsli === 'Technical Test' ? 'Sedang Technical Test' : $statusAsli));

            // 👤 Fallback nama & email ke relasi user
            $nama  = $a->name  ?? ($a->user?->name  ?? '-');
            $email = $a->email ?? ($a->user?->email ?? '-');

            return [
                'Nama'        => $nama,
                'Email'       => $email,
                'Jurusan'     => $a->jurusan,
                'Posisi'      => $a->position->name ?? '-',
                'Batch'       => $a->batch->name ?? '-',
                'Nilai'       => is_null($ans?->score) ? '-' : $ans->score,
                'Keterangan'  => $ans?->keterangan ?? '-',
                'Status'      => $statusTampil,
                'Dikirim'     => $ans?->submitted_at?->format('d-m-Y H:i:s') ?? '-',
                'Jawaban PDF' => $ans?->answer_path ? url('storage/'.$ans->answer_path) : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Jurusan',
            'Posisi',
            'Batch',
            'Nilai',
            'Keterangan',
            'Status',
            'Dikirim',
            'Jawaban PDF',
        ];
    }
}
