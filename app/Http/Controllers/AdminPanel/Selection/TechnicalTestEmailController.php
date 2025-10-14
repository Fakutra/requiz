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
use App\Services\ActivityLogger; // ✅ tambahkan

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
            'message'       => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120',
            'ids'           => 'nullable|string',
        ]);

        // Tentukan target applicants
        if ($data['type'] === 'selected') {
            if (empty($data['ids'])) {
                return back()->with('error', 'Silakan pilih peserta terlebih dahulu.');
            }

            $ids = array_filter(explode(',', $data['ids']));
            $applicants = Applicant::whereIn('id', $ids)->get();
        } else {
            $query = Applicant::where('batch_id', $data['batch']);

            if ($data['position']) $query->where('position_id', $data['position']);

            if ($data['type'] === 'lolos') {
                $query->whereIn('status', [
                    'Interview',
                    'Offering',
                    'Menerima Offering',
                    'Tidak Lolos Interview',
                    'Menolak Offering',
                ]);
            } else {
                $query->where('status', 'Tidak Lolos Technical Test');
            }

            $applicants = $query->get();
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($applicants as $a) {
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

                Mail::to($a->email)->send($mail);

                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $a->email,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => true,
                    'error'        => null,
                ]);

                $successCount++;
            } catch (Throwable $e) {
                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $a->email,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => $e->getMessage(),
                ]);

                $failCount++;
            }
        }

        // ✅ Simpan aktivitas ke Activity Log
        $total = count($applicants);

        $action = match ($data['type']) {
            'lolos'        => 'send_email_lolos',
            'tidak_lolos'  => 'send_email_tidak_lolos',
            'selected'     => 'send_email_terpilih',
            default        => 'send_email',
        };

        $tabLabel = match ($data['type']) {
            'lolos'        => 'Lolos Technical Test',
            'tidak_lolos'  => 'Tidak Lolos Technical Test',
            'selected'     => 'Peserta Terpilih',
            default        => 'Technical Test',
        };

        ActivityLogger::log(
            $action,
            'Technical Test',
            Auth::user()->name." mengirim email hasil {$tabLabel} ke {$successCount} dari {$total} peserta (gagal: {$failCount})",
            $data['type'] === 'selected'
                ? 'Selected IDs: '.implode(',', $ids ?? [])
                : 'Batch ID: '.$data['batch']
        );

        return back()->with(
            'success',
            "Email berhasil dikirim ke {$successCount} peserta dari total {$total}"
        );
    }
}
