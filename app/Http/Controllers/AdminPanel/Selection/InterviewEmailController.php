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
use App\Services\ActivityLogger; // ✅ tambahkan ini

class InterviewEmailController extends Controller
{
    protected string $stage = 'Interview';

    public function send(Request $request)
    {
        $data = $request->validate([
            'batch'       => 'nullable|exists:batches,id',
            'position'    => 'nullable|exists:positions,id',
            'type'        => 'required|in:lolos,tidak_lolos,selected',
            'ids'         => 'nullable|string',
            'subject'     => 'required|string',
            'message'     => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ]);

        $ids = [];
        if ($data['type'] === 'selected') {
            $ids = array_filter(array_map('trim', explode(',', $data['ids'] ?? '')));
            if (empty($ids)) {
                return back()->with('error', 'Silakan pilih peserta terlebih dahulu.');
            }
            $applicants = Applicant::whereIn('id', $ids)->get();
        } else {
            $query = Applicant::where('batch_id', $data['batch']);
            if ($data['position']) $query->where('position_id', $data['position']);

            $query->where(
                'status',
                $data['type'] === 'lolos' ? 'Offering' : 'Tidak Lolos Interview'
            );

            $applicants = $query->get();
        }

        $total = $applicants->count();
        $success = 0;
        $failed  = 0;
        $failSamples = [];

        foreach ($applicants as $a) {
            $email = trim((string) $a->email);

            if ($email === '') {
                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => '(missing)',
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => 'Email kosong',
                ]);

                $failed++;
                if (count($failSamples) < 10) {
                    $failSamples[] = ($a->name ?? "ID {$a->id}") . ' (email kosong)';
                }
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

                Mail::to($email)->send($mail);

                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $email,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => true,
                ]);

                $success++;
            } catch (Throwable $e) {
                report($e);

                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $email,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => $e->getMessage(),
                ]);

                $failed++;
                if (count($failSamples) < 10) {
                    $failSamples[] = ($a->name ?? "ID {$a->id}") . ' (' . $e->getMessage() . ')';
                }
            }
        }

        ActivityLogger::log(
            'send_email',
            'Seleksi Interview',
            Auth::user()->name." kirim email Interview: {$success} sukses, {$failed} gagal",
            $data['type'] === 'selected'
                ? 'Selected IDs: '.implode(',', $ids)
                : 'Batch ID: '.$data['batch']
        );

        $resp = back();

        if ($success > 0) {
            $resp = $resp->with('success', "Email berhasil dikirim ke {$success} peserta dari total {$total}.");
        }

        if ($failed > 0) {
            $resp = $resp->with(
                'error',
                "Ada {$failed} email gagal dikirim: ".implode(', ', $failSamples)
            );
        }

        return $resp;
    }
}

