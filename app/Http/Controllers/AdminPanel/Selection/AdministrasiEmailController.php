<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use App\Mail\SelectionResultMail;
use App\Models\Applicant;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class AdministrasiEmailController extends Controller
{
    protected string $stage = 'Seleksi Administrasi';

    public function send(Request $request)
    {
        $data = $request->validate([
            'batch'         => 'nullable|exists:batches,id',
            'position'      => 'nullable|exists:positions,id',
            'type'          => 'required|in:lolos,tidak_lolos,selected',
            'ids'           => 'nullable|string', // untuk tab "Terpilih"
            'subject'       => 'required|string',
            'message'       => 'required|string', // HTML dari Trix
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120',   // 5 MB
        ]);

        // ========== Tentukan target applicants ==========
        $ids = [];
        if ($data['type'] === 'selected') {
            $ids = array_filter(array_map('trim', explode(',', $data['ids'] ?? '')));
            $applicants = Applicant::whereIn('id', $ids)->get();
        } else {
            $query = Applicant::query()
                ->when($data['batch'] ?? null, fn ($q, $b) => $q->where('batch_id', $b))
                ->when($data['position'] ?? null, fn ($q, $p) => $q->where('position_id', $p));

            if ($data['type'] === 'lolos') {
                $lolosAdminStatuses = [
                    'Tes Tulis',
                    'Technical Test',
                    'Interview',
                    'Offering',
                    'Menerima Offering',
                    'Tidak Lolos Tes Tulis',
                    'Tidak Lolos Technical Test',
                    'Tidak Lolos Interview',
                    'Menolak Offering',
                ];
                $query->whereIn('status', $lolosAdminStatuses);
            } else {
                $query->where('status', 'Tidak Lolos Seleksi Administrasi');
            }

            $applicants = $query->get();
        }

        $total        = $applicants->count();
        $successCount = 0;
        $failCount    = 0;

        // buat teaser biar admin tau yang gagal siapa (max 10 biar ga spam)
        $failSamples = [];

        foreach ($applicants as $a) {
            $targetEmail = $a->email ?: null;

            try {
                if (!$targetEmail) {
                    EmailLog::create([
                        'applicant_id' => $a->id,
                        'email'        => null,
                        'stage'        => $this->stage,
                        'subject'      => $data['subject'],
                        'success'      => false,
                        'error'        => 'Applicant email is empty',
                    ]);

                    $failCount++;
                    if (count($failSamples) < 10) $failSamples[] = ($a->name ?? "ID {$a->id}")." (email kosong)";
                    continue;
                }

                $mail = new SelectionResultMail(
                    $data['subject'],
                    $data['message'],
                    $this->stage,
                    $data['type'],
                    $a
                );

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $mail->attach($file->getRealPath(), [
                            'as'   => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType(),
                        ]);
                    }
                }

                Mail::to($targetEmail)->send($mail);

                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $targetEmail,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => true,
                    'error'        => null,
                ]);

                $successCount++;
            } catch (Throwable $e) {
                report($e);

                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $targetEmail,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => $e->getMessage(),
                ]);

                $failCount++;
                if (count($failSamples) < 10) $failSamples[] = ($a->name ?? "ID {$a->id}")." (".$e->getMessage().")";
            }
        }

        // ===== Activity log per tab =====
        $action  = match ($data['type']) {
            'lolos'       => 'send_email_lolos',
            'tidak_lolos' => 'send_email_tidak_lolos',
            'selected'    => 'send_email_terpilih',
        };
        $tabLabel = match ($data['type']) {
            'lolos'       => 'Lolos Seleksi Administrasi',
            'tidak_lolos' => 'Tidak Lolos Seleksi Administrasi',
            'selected'    => 'Peserta Terpilih',
        };

        ActivityLogger::log(
            $action,
            'Seleksi Administrasi',
            Auth::user()->name." mengirim email hasil {$tabLabel} ke {$successCount} dari {$total} peserta (gagal: {$failCount})",
            ($data['type'] === 'selected')
                ? ('Selected IDs: '.implode(',', $ids))
                : ('Batch ID: '.($data['batch'] ?? '-').', Position ID: '.($data['position'] ?? '-'))
        );

        // ===== Flash notif success + error =====
        $response = back();

        if ($successCount > 0) {
            $response = $response->with(
                'success',
                "Email berhasil dikirim ke {$successCount} peserta dari total {$total}."
            );
        }

        if ($failCount > 0) {
            $sampleText = implode(', ', $failSamples);
            $suffix = ($failCount > count($failSamples)) ? ' (dan lainnya)' : '';

            $response = $response->with(
                'error',
                "Ada {$failCount} email yang gagal dikirim: {$sampleText}{$suffix}. Cek Email Log untuk detail."
            );
        }

        // kalau total = 0 juga kasih info biar ga bingung
        if ($total === 0) {
            $response = $response->with('error', 'Tidak ada peserta yang cocok dengan filter/target yang dipilih.');
        }

        return $response;
    }
}
