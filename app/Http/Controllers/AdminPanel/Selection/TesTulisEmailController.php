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

class TesTulisEmailController extends Controller
{
    protected string $stage = 'Tes Tulis';

    public function send(Request $request)
    {
        $data = $request->validate([
            'batch'         => 'required|exists:batches,id',
            'position'      => 'nullable|exists:positions,id',
            'type'          => 'required|in:lolos,tidak_lolos,selected',
            'subject'       => 'required|string',
            'message'       => 'required|string', // HTML (Trix/CKEditor)
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120',
            'ids'           => 'nullable|string', // only for selected
        ]);

        // =========================
        // Tentukan target applicants
        // =========================
        $selectedIds = [];
        if ($data['type'] === 'selected') {
            $selectedIds = array_values(array_filter(explode(',', $data['ids'] ?? '')));
            $applicants = Applicant::with('user')->whereIn('id', $selectedIds)->get();
        } else {
            $query = Applicant::with('user')->where('batch_id', $data['batch']);

            if (!empty($data['position'])) {
                $query->where('position_id', $data['position']);
            }

            if ($data['type'] === 'lolos') {
                // yang dianggap "Lolos Tes Tulis" (konsisten dengan UI)
                $query->whereIn('status', [
                    'Technical Test',
                    'Interview',
                    'Offering',
                    'Menerima Offering',
                    'Tidak Lolos Technical Test',
                    'Tidak Lolos Interview',
                    'Menolak Offering',
                ]);
            } else {
                // tab "Tidak Lolos"
                $query->where('status', 'Tidak Lolos Tes Tulis');
            }

            $applicants = $query->get();
        }

        // =========================
        // Kirim email + catat log
        // =========================
        $successCount = 0;
        $failCount    = 0;

        foreach ($applicants as $a) {
            // fallback aman: ambil dari applicant.email, kalau kosong ambil dari relasi user
            $recipient = trim((string) ($a->email ?? $a->user?->email ?? ''));

            // kalau tetep kosong → skip kirim, tapi log gagal (hindari NULL ke DB)
            if ($recipient === '') {
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
                    'email'        => $recipient, // tetap string valid
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
            'lolos'       => 'Lolos Tes Tulis',
            'tidak_lolos' => 'Tidak Lolos Tes Tulis',
            'selected'    => 'Peserta Terpilih Tes Tulis',
            default       => 'Tes Tulis',
        };

        $targetInfo = $data['type'] === 'selected'
            ? ('Selected IDs: ' . implode(',', $selectedIds))
            : ('Batch ID: ' . $data['batch'] . ', Position ID: ' . ($data['position'] ?? 'Semua Posisi'));

        ActivityLogger::log(
            $action,
            'Tes Tulis',
            Auth::user()->name . " mengirim email hasil {$tabLabel} ke {$successCount} dari {$total} peserta (gagal: {$failCount})",
            $targetInfo
        );

        return back()->with('success', "Email berhasil dikirim ke {$successCount} peserta dari total {$total}");
    }
}
