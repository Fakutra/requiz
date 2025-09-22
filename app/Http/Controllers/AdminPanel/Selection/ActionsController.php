<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\EmailLog;
use App\Models\SelectionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActionsController extends Controller
{
    /* ======================== EMAIL ======================== */
    public function sendEmail(Request $request)
    {
        $data = $request->validate([
            'recipients'    => 'required|string',
            'recipient_ids' => 'required|string',
            'stage'         => 'required|string',
            'use_template'  => 'nullable',
            'subject'       => 'nullable|string|max:255',
            'message'       => 'nullable|string|max:20000',
            'attachment'    => 'required|file|mimes:pdf|max:5120',
        ]);

        $useTemplate = $request->boolean('use_template');

        $emails = collect(preg_split('/[,\s;]+/', $data['recipients']))->filter()->unique()->values();
        $ids    = collect(explode(',', $data['recipient_ids']))->map(fn($v)=>(int)trim($v))->filter()->values();

        $applicants = Applicant::with('position')->whereIn('id', $ids)->get()->keyBy('email');
        $file = $request->file('attachment');

        $ok = 0; $fail = 0;

        foreach ($emails as $to) {
            $match        = $applicants->get($to);
            $applicantId  = $match?->id;
            $fullName     = $match?->name ?? $to;
            $positionName = $match?->position?->name ?? '-';

            if ($useTemplate) {
                $subject = "INFORMASI HASIL SELEKSI {$data['stage']} TAD/OUTSOURCING - PLN ICON PLUS";
                $body = "Halo {$fullName}

Terima kasih atas partisipasi Saudara/i dalam mengikuti proses seleksi TAD/OUTSOURCING PLN ICON PLUS pada posisi {$positionName}.

Selamat Anda lolos pada tahap {$data['stage']}. Selanjutnya, silakan cek jadwal Anda untuk tahap berikutnya pada lampiran email ini.

Demikian kami sampaikan.
Terima kasih atas partisipasinya dan semoga sukses.";
            } else {
                $subject = $data['subject'] ?: 'Informasi Seleksi';
                $body    = $data['message'] ?: '';
            }

            try {
                Mail::raw($body, function ($mail) use ($to, $subject, $file) {
                    $mail->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->attach($file->getRealPath(), [
                            'as'   => $file->getClientOriginalName(),
                            'mime' => 'application/pdf',
                        ]);
                });

                if (class_exists(EmailLog::class)) {
                    EmailLog::create([
                        'applicant_id' => $applicantId,
                        'email'        => $to,
                        'stage'        => $data['stage'],
                        'subject'      => $subject,
                        'success'      => true,
                        'error'        => null,
                    ]);
                }
                $ok++;
            } catch (\Throwable $e) {
                Log::error('Gagal kirim email', ['to' => $to, 'error' => $e->getMessage()]);

                if (class_exists(EmailLog::class)) {
                    EmailLog::create([
                        'applicant_id' => $applicantId,
                        'email'        => $to,
                        'stage'        => $data['stage'],
                        'subject'      => $subject ?? 'Informasi Seleksi',
                        'success'      => false,
                        'error'        => substr($e->getMessage(), 0, 1000),
                    ]);
                }
                $fail++;
            }
        }

        $msg = "Email terkirim: {$ok}, gagal: {$fail}.";
        return back()->with($fail ? 'error' : 'success', $msg);
    }

    /* ====================== UPDATE STATUS ====================== */
    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'action'        => 'required|string|in:lolos,gagal,reset',
            'stage'         => 'required|string',
            'ids'           => 'nullable|array',
            'ids.*'         => 'integer',
            'applicant_ids' => 'nullable|string',
            'note'          => 'nullable|string|max:2000', // tidak disimpan (skema selection_logs tidak punya kolom 'note')
        ]);

        // Gabungkan sumber ID (array dan/atau CSV)
        $ids = collect($data['ids'] ?? [])
            ->when(!empty($data['applicant_ids']), function ($c) use ($data) {
                return $c->merge(
                    Str::of($data['applicant_ids'])->explode(',')
                        ->map(fn ($v) => (int) trim($v))
                        ->filter()
                );
            })
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return back()->with('status', 'Tidak ada kandidat yang dipilih.');
        }

        $stage = $data['stage'];

        // Pilih kolom target: khusus tahap bila ada, kalau tidak ada pakai 'status'
        $statusColumnMap = [
            'Seleksi Administrasi' => 'status_administrasi',
            'Tes Tulis'            => 'status_tes_tulis',
            'Technical Test'       => 'status_technical_test',
            'Interview'            => 'status_interview',
            // Offering biasanya tidak ada kolom khusus
        ];
        $targetCol = $statusColumnMap[$stage] ?? 'status';
        if (!Schema::hasColumn('applicants', $targetCol)) {
            $targetCol = Schema::hasColumn('applicants', 'status') ? 'status' : null;
        }

        // Nilai yang valid sesuai kolom target
        if ($targetCol === 'status') {
            // kolom global 'status' tunduk CHECK/ENUM → pakai enum valid
            $value = match ($data['action']) {
                'lolos' => $this->mapLolosForGlobalStatus($stage),
                'gagal' => $this->failEnumFor($stage),
                'reset' => $stage,
            };
        } else {
            // kolom khusus tahap
            $value = match ($data['action']) {
                'lolos' => 'Lolos',
                'gagal' => 'Tidak Lolos',
                'reset' => null,
            };
        }

        DB::transaction(function () use ($ids, $stage, $targetCol, $value, $data) {
            /* 1) Update status kandidat */
            if ($targetCol) {
                Applicant::whereIn('id', $ids)->update([$targetCol => $value]);
            }

            /* 2) Insert selection_logs sesuai skema:
                  applicant_id, stage, stage_key, result('lolos'|'tidak_lolos'), position_id, jurusan, acted_by
                 - aksi 'reset' TIDAK dilog agar tidak melanggar enum. */
            if (class_exists(SelectionLog::class) && Schema::hasTable('selection_logs')) {
                $resultForLog = match ($data['action']) {
                    'lolos' => 'lolos',
                    'gagal' => 'tidak_lolos',
                    default => null, // 'reset' -> tidak dilog
                };

                if ($resultForLog) {
                    $now      = now();
                    $actorId  = auth()->id();
                    $stageKey = $this->stageKeyFor($stage);

                    $applicants = Applicant::whereIn('id', $ids)
                        ->get(['id', 'position_id', 'jurusan'])
                        ->keyBy('id');

                    $rows = [];
                    foreach ($ids as $id) {
                        $a = $applicants->get($id);
                        $rows[] = [
                            'applicant_id' => $id,
                            'stage'        => $stage,
                            'stage_key'    => $stageKey,
                            'result'       => $resultForLog,     // ✅ hanya 'lolos' atau 'tidak_lolos'
                            'position_id'  => $a?->position_id,
                            'jurusan'      => $a?->jurusan,
                            'acted_by'     => $actorId,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ];
                    }

                    if (!empty($rows)) {
                        SelectionLog::insert($rows);
                    }
                }
            }
        });

        return back()->with('status', "Status {$stage} untuk ".count($ids)." kandidat berhasil diupdate ({$data['action']}).");
    }

    /* ====================== HELPERS ====================== */

    private function nextStageExact(string $stage): string
    {
        return match ($stage) {
            'Seleksi Administrasi' => 'Tes Tulis',
            'Tes Tulis'            => 'Technical Test',
            'Technical Test'       => 'Interview',
            'Interview'            => 'Offering',
            'Offering'             => 'Offering',
            default                => $stage,
        };
    }

    private function failEnumFor(string $stage): string
    {
        return match ($stage) {
            'Seleksi Administrasi' => 'Tidak Lolos Seleksi Administrasi',
            'Tes Tulis'            => 'Tidak Lolos Seleksi Tes Tulis',
            'Technical Test'       => 'Tidak Lolos Technical Test',
            'Interview'            => 'Tidak Lolos Interview',
            'Offering'             => 'Menolak Offering',
            default                => $stage,
        };
    }

    private function mapLolosForGlobalStatus(string $stage): string
    {
        // kolom global 'status': jika lolos → tahap berikutnya (Offering → Menerima Offering)
        return match ($stage) {
            'Offering' => 'Menerima Offering',
            default    => $this->nextStageExact($stage),
        };
    }

    private function stageKeyFor(string $stage): string
    {
        // normalisasi key tahap untuk selection_logs.stage_key
        return match ($stage) {
            'Seleksi Administrasi' => 'administrasi',
            'Tes Tulis'            => 'tes_tulis',
            'Technical Test'       => 'technical_test',
            'Interview'            => 'interview',
            'Offering'             => 'offering',
            default                => Str::slug($stage, '_'),
        };
    }
}
