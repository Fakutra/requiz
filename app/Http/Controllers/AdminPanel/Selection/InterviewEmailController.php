<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use App\Mail\SelectionResultMail;
use App\Models\Applicant;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

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

        // Tentukan target applicants
        if ($data['type'] === 'selected') {
            $ids = array_filter(explode(',', $data['ids'] ?? ''));
            $applicants = Applicant::whereIn('id', $ids)->get();
        } else {
            $query = Applicant::where('batch_id', $data['batch']);
            if ($data['position']) {
                $query->where('position_id', $data['position']);
            }

            if ($data['type'] === 'lolos') {
                $query->where('status', 'Offering'); // dianggap Lolos Interview
            } else {
                $query->where('status', 'Tidak Lolos Interview');
            }

            $applicants = $query->get();
        }

        $successCount = 0;

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
            }
        }

        return back()->with(
            'success',
            "Email berhasil dikirim ke {$successCount} peserta dari total " . count($applicants)
        );
    }
}
