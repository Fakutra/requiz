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

        // ========= Target applicant =========
        if ($data['type'] === 'selected') {
            $ids = array_filter(explode(',', $data['ids'] ?? ''));
            $applicants = Applicant::with(['user:id,name,email'])
                ->whereIn('id', $ids)
                ->get();
        } else {
            $query = Applicant::query()
                ->with(['user:id,name,email'])
                ->when($data['batch'] ?? null, fn($q, $b) => $q->where('batch_id', $b))
                ->when($data['position'] ?? null, fn($q, $p) => $q->where('position_id', $p));

            if ($data['type'] === 'lolos') {
                // dianggap “Lolos Administrasi” (sudah lanjut min. Tes Tulis)
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

        $successCount = 0;
        $failCount    = 0;

        foreach ($applicants as $a) {
            // email ambil dari relasi user
            $targetEmail = $a->user->email ?? null;

            try {
                if (!$targetEmail) {
                    // kalo gak ada email, catet gagal
                    EmailLog::create([
                        'applicant_id' => $a->id,
                        'email'        => null,
                        'stage'        => $this->stage,
                        'subject'      => $data['subject'],
                        'success'      => false,
                        'error'        => 'User email is empty',
                    ]);
                    $failCount++;
                    continue;
                }

                // ✉️ compose mail
                $mail = new SelectionResultMail(
                    $data['subject'],
                    $data['message'],
                    $this->stage,
                    $data['type'],
                    $a
                );

                // attachments (opsional)
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $mail->attach($file->getRealPath(), [
                            'as'   => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType(),
                        ]);
                    }
                }

                // kirim
                Mail::to($targetEmail)->send($mail);

                // log sukses
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
                // log gagal
                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $targetEmail,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => $e->getMessage(),
                ]);

                $failCount++;
            }
        }

        // ===== Activity log per tab =====
        $total   = count($applicants);
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
                ? ('Selected IDs: '.implode(',', $ids ?? []))
                : ('Batch ID: '.($data['batch'] ?? '-').', Position ID: '.($data['position'] ?? '-'))
        );

        return back()->with('success', "Email berhasil dikirim ke {$successCount} peserta dari total {$total}");
    }
}
