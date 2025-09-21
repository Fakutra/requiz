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
    public function sendEmail(Request $request)
    {
        $data = $request->validate([
            'recipients'    => 'required|string',
            'recipient_ids' => 'required|string',
            'stage'         => 'required|string',
            'use_template'  => 'nullable', // checkbox
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

                EmailLog::create([
                    'applicant_id' => $applicantId,
                    'email'        => $to,
                    'stage'        => $data['stage'],
                    'subject'      => $subject,
                    'success'      => true,
                    'error'        => null,
                ]);
                $ok++;
            } catch (\Throwable $e) {
                Log::error('Gagal kirim email', ['to' => $to, 'error' => $e->getMessage()]);

                EmailLog::create([
                    'applicant_id' => $applicantId,
                    'email'        => $to,
                    'stage'        => $data['stage'],
                    'subject'      => $subject ?? 'Informasi Seleksi',
                    'success'      => false,
                    'error'        => substr($e->getMessage(), 0, 1000),
                ]);
                $fail++;
            }
        }

        $msg = "Email terkirim: {$ok}, gagal: {$fail}.";
        return back()->with($fail ? 'error' : 'success', $msg);
    }
    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            // action: 'lolos', 'gagal', 'reset' (silakan sesuaikan dengan UI Anda)
            'action'       => 'required|string|in:lolos,gagal,reset',
            'stage'        => 'required|string', // ex: "Seleksi Administrasi", "Tes Tulis", "Technical Test", "Interview"
            // boleh kirim array ids[] atau CSV "1,2,3"
            'ids'          => 'nullable|array',
            'ids.*'        => 'integer',
            'applicant_ids'=> 'nullable|string',
            'note'         => 'nullable|string|max:2000',
        ]);

        // Gabungkan sumber id (array/CSV)
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

        // Tentukan kolom status per tahap bila tersedia; fallback ke 'status'
        $stage = $data['stage'];
        $statusColumnMap = [
            'Seleksi Administrasi' => 'status_administrasi',
            'Tes Tulis'            => 'status_tes_tulis',
            'Technical Test'       => 'status_technical_test',
            'Interview'            => 'status_interview',
        ];
        $targetCol = $statusColumnMap[$stage] ?? 'status';
        if (!Schema::hasColumn('applicants', $targetCol)) {
            // fallback aman
            $targetCol = Schema::hasColumn('applicants', 'status') ? 'status' : null;
        }

        // Nilai yang akan disimpan
        $value = match ($data['action']) {
            'lolos' => 'Lolos',
            'gagal' => 'Tidak Lolos',
            'reset' => null,
        };

        DB::transaction(function () use ($ids, $stage, $targetCol, $value, $data) {
            // Update kolom status jika ada kolom target
            if ($targetCol) {
                Applicant::whereIn('id', $ids)->update([$targetCol => $value]);
            }

            // Catat log per kandidat (jika ada tabel selection_logs)
            if (class_exists(SelectionLog::class)) {
                $now = now();
                $logs = $ids->map(fn ($id) => [
                    'applicant_id' => $id,
                    'stage'        => $stage,
                    'action'       => $data['action'], // 'lolos' / 'gagal' / 'reset'
                    'note'         => $data['note'] ?? null,
                    'admin_id'     => auth()->id(),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ])->all();

                if (!empty($logs)) {
                    SelectionLog::insert($logs);
                }
            }
        });

        return back()->with('status', "Status {$stage} untuk ".count($ids)." kandidat berhasil diupdate ({$data['action']}).");
    }
}
