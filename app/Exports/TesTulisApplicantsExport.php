<?php

namespace App\Exports;

use App\Models\Applicant;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // ⬅️ perlu buat baca personality_rules
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TesTulisApplicantsExport implements FromCollection, WithHeadings
{
    protected $batchId;
    protected $positionId;
    protected $search;

    public function __construct($batchId, $positionId, $search)
    {
        $this->batchId    = $batchId;
        $this->positionId = $positionId;
        $this->search     = $search;
    }

    public function collection()
    {
        $q = Applicant::with([
            'position',
            'batch',
            'latestEmailLog',
            'latestTestResult.test',
            'latestTestResult.sectionResults.testSection',
            'latestTestResult.sectionResults.testSection.questionBundle.questions',
            'latestTestResult.sectionResults.answers.question',
        ])
        ->where('batch_id', $this->batchId)
        ->whereIn('status', [
            'Tes Tulis',
            'Technical Test',
            'Interview',
            'Offering',
            'Menerima Offering',
            'Tidak Lolos Tes Tulis',
            'Tidak Lolos Technical Test',
            'Tidak Lolos Interview',
            'Menolak Offering',
        ]);

        if ($this->positionId) {
            $q->where('position_id', $this->positionId);
        }

        if ($this->search) {
            $needle = "%".mb_strtolower($this->search)."%";
            $q->where(function ($w) use ($needle) {
                $w->whereRaw('LOWER(name) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle]);
            });
        }

        $rows = $q->get();

        // 🔎 ambil max personality final score utk batch ini (kalau ada rules)
        $maxPersonalityFinal = (int) DB::table('personality_rules')
            ->where('batch_id', $this->batchId)
            ->max('score_value') ?: 0;

        // 🧾 Log export
        try {
            $userName    = Auth::user()?->name ?? 'System';
            $batchInfo   = $this->batchId ? "Batch ID {$this->batchId}" : "Semua Batch";
            $positionInf = $this->positionId ? "Posisi ID {$this->positionId}" : "Semua Posisi";
            $count       = $rows->count();

            ActivityLogger::log(
                'export',
                'Tes Tulis',
                "{$userName} mengekspor data peserta Tes Tulis ({$count} baris, {$batchInfo}, {$positionInf})",
                "Jumlah Data: {$count}"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export TesTulisApplicants: '.$e->getMessage());
        }

        // 🎯 Mapping baris export
        return $rows->map(function ($a) use ($maxPersonalityFinal) {
            $latestTest = $a->latestTestResult;

            // Default tampilan per-section
            $secDisp = [
                1 => '-',
                2 => '-',
                3 => '-',
                4 => '-',
                5 => '-',
            ];

            $finalTotal = null;
            $maxTotal   = null;

            if ($latestTest) {
                $finalSum = 0;
                $maxSum   = 0;

                foreach ($latestTest->sectionResults as $sr) {
                    $section   = $sr->testSection;
                    if (!$section) continue;

                    $order     = (int) ($section->order ?? 0);
                    if ($order < 1 || $order > 5) continue;

                    $questions = $section->questionBundle->questions ?? collect();
                    $raw       = (float) ($sr->score ?? 0);

                    // deteksi section personality
                    $isPersonality = $questions->contains(fn($q) => $q->type === 'Poin');

                    // hitung MAX
                    if ($isPersonality) {
                        // max raw di soal personality = jml pertanyaan * 5 (tiap opsi 1-5)
                        $maxRaw = $questions->count() * 5;
                        $maxSum += $maxPersonalityFinal; // max FINAL utk section personality = aturan tertinggi
                    } else {
                        $pg     = $questions->where('type','PG')->count();
                        $multi  = $questions->where('type','Multiple')->count();
                        $essay  = $questions->where('type','Essay')->count();
                        $maxRaw = ($pg * 1) + ($multi * 1) + ($essay * 3);
                        $maxSum += $maxRaw;
                    }

                    // hitung FINAL (khusus personality → konversi % ke skor final via rules)
                    if ($isPersonality) {
                        $percent = $maxRaw > 0 ? ($raw / $maxRaw) * 100 : 0;
                        $rule = DB::table('personality_rules')
                            ->where('batch_id', $a->batch_id)
                            ->where('min_percentage', '<=', $percent)
                            ->where(function ($q) use ($percent) {
                                $q->where('max_percentage', '>=', $percent)
                                  ->orWhereNull('max_percentage');
                            })
                            ->orderByDesc('min_percentage')
                            ->first();
                        $final = $rule ? (int) $rule->score_value : 0;

                        // tampilkan "raw/max → final"
                        $secDisp[$order] = sprintf('%s / %s → %s', $this->num($raw), $this->num($maxRaw), $this->num($final));
                        $finalSum += $final;
                    } else {
                        // non-personality: final = raw biasa
                        $secDisp[$order] = sprintf('%s / %s', $this->num($raw), $this->num($maxRaw));
                        $finalSum += $raw;
                    }
                }

                $finalTotal = $finalSum;
                $maxTotal   = $maxSum;
            }

            // Status tampilan (konsisten dgn page)
            $statusTampil = (function ($s) {
                $lolos = [
                    'Technical Test',
                    'Interview',
                    'Offering',
                    'Menerima Offering',
                    'Tidak Lolos Technical Test',
                    'Tidak Lolos Interview',
                    'Menolak Offering',
                ];
                if (in_array($s, $lolos, true)) return 'Lolos Tes Tulis';
                if ($s === 'Tidak Lolos Tes Tulis') return 'Tidak Lolos Tes Tulis';
                return $s; // 'Tes Tulis' dll
            })($a->status);

            // KKM (nilai minimum di Test)
            $kkm = $a->latestTestResult?->test?->nilai_minimum;

            // Status email utk stage Tes Tulis
            $log = $a->latestEmailLog;
            if ($log && $log->stage !== 'Tes Tulis') $log = null;
            $emailStatus = $log ? ($log->success ? 'Terkirim' : 'Gagal') : 'Belum Dikirim';

            return [
                'Nama'           => $a->name,
                'Email'          => $a->email,
                'Jurusan'        => $a->jurusan,
                'Posisi'         => $a->position->name ?? '-',
                'Batch'          => $a->batch->name ?? '-',
                'Section 1'      => $secDisp[1],
                'Section 2'      => $secDisp[2],
                'Section 3'      => $secDisp[3],
                'Section 4'      => $secDisp[4],
                'Section 5'      => $secDisp[5],
                'Total (Final/Max)' => ($finalTotal !== null && $maxTotal !== null)
                    ? ($this->num($finalTotal).' / '.$this->num($maxTotal))
                    : '-',
                'KKM'            => $kkm ?? '-',
                'Status'         => $statusTampil,
                'Status Email'   => $emailStatus,
                'Tanggal Daftar' => $a->created_at?->format('d-m-Y H:i:s') ?? '-',
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
            'Section 1',
            'Section 2',
            'Section 3',
            'Section 4',
            'Section 5',
            'Total (Final/Max)',
            'KKM',
            'Status',
            'Status Email',
            'Tanggal Daftar',
        ];
    }

    // helper buat format angka tanpa ribet (biar gak tampil "0" jadi "0")
    private function num($v)
    {
        // angka integer tampil plain, desimal tetap sesuai float tanpa trailing .0 panjang
        if (is_numeric($v)) {
            return (floor($v) == $v) ? (string) (int) $v : rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.');
        }
        return (string) $v;
    }
}
