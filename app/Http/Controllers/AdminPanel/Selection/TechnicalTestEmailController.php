<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use App\Mail\SelectionResultMail;
use App\Models\Applicant;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Throwable;
use App\Services\ActivityLogger;

class TechnicalTestEmailController extends Controller
{
    protected string $stage = 'Technical Test';

    public function send(Request $request)
    {
        $data = $request->validate([
            'batch'         => 'required|exists:batches,id',
            'position'      => 'nullable|exists:positions,id',
            'type'          => 'required|in:lolos,tidak_lolos,selected',
            'subject'       => 'required|string',
            'message'       => 'required|string', // HTML dari Trix/CKEditor
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120',
            'ids'           => 'nullable|string', // hanya untuk selected
        ]);

        // =========================
        // Tentukan target applicants
        // =========================
        $selectedIds = [];
        if ($data['type'] === 'selected') {
            $selectedIds = array_values(array_filter(explode(',', (string)($data['ids'] ?? ''))));
            if (empty($selectedIds)) {
                return back()->with('error', 'Silakan pilih peserta terlebih dahulu.');
            }
            $applicants = Applicant::with('user')->whereIn('id', $selectedIds)->get();
        } else {
            // kandidat yang relevan di stage Technical Test
            $query = Applicant::with('user')->where('batch_id', $data['batch']);

            if (!empty($data['position'])) {
                $query->where('position_id', $data['position']);
            }

            if ($data['type'] === 'lolos') {
                // dianggap "Lolos Technical Test" bila sudah lanjut / melewati tahap ini
                $query->whereIn('status', [
                    'Interview',
                    'Offering',
                    'Menerima Offering',
                    'Tidak Lolos Interview',
                    'Menolak Offering',
                ]);
            } else {
                // tab "Tidak Lolos" utk stage ini
                $query->where('status', 'Tidak Lolos Technical Test');
            }

            $applicants = $query->get();
        }

        // =========================
        // Kirim email + catat log
        // =========================
        $successCount = 0;
        $failCount    = 0;

        foreach ($applicants as $a) {
            // fallback aman: applicant.email -> user.email
            $recipient = trim((string) ($a->email ?? $a->user?->email ?? ''));

            if ($recipient === '') {
                // log gagal khusus: email kosong
                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => '(missing)',
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => 'Missing recipient email',
                ]);
                $failCount++;
                continue;
            }

            try {
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

                Mail::to($recipient)->send($mail);

                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $recipient,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => true,
                    'error'        => null,
                ]);

                $successCount++;
            } catch (Throwable $e) {
                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $recipient, // tetap simpan string valid
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => $e->getMessage(),
                ]);
                $failCount++;
            }
        }

        // =========================
        // Activity Logger
        // =========================
        $total    = $applicants->count();
        $action   = match ($data['type']) {
            'lolos'       => 'send_email_lolos',
            'tidak_lolos' => 'send_email_tidak_lolos',
            'selected'    => 'send_email_terpilih',
            default       => 'send_email',
        };
        $tabLabel = match ($data['type']) {
            'lolos'       => 'Lolos Technical Test',
            'tidak_lolos' => 'Tidak Lolos Technical Test',
            'selected'    => 'Peserta Terpilih Technical Test',
            default       => 'Technical Test',
        };

        $targetInfo = $data['type'] === 'selected'
            ? ('Selected IDs: ' . implode(',', $selectedIds))
            : ('Batch ID: ' . $data['batch'] . ', Position ID: ' . ($data['position'] ?? 'Semua Posisi'));

        ActivityLogger::log(
            $action,
            'Technical Test',
            Auth::user()->name . " mengirim email hasil {$tabLabel} ke {$successCount} dari {$total} peserta (gagal: {$failCount})",
            $targetInfo
        );

        return back()->with('success', "Email berhasil dikirim ke {$successCount} peserta dari total {$total}");
    }
}
