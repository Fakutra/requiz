<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
}
