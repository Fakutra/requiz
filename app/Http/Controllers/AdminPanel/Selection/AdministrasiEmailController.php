<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use App\Mail\SelectionResultMail;
use App\Models\Applicant;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdministrasiEmailController extends Controller
{
    protected string $stage = 'Seleksi Administrasi';

    public function send(Request $request)
    {
        $data = $request->validate([
            'batch'       => 'required|exists:batches,id',
            'position'    => 'nullable|exists:positions,id',
            'type'        => 'required|in:lolos,tidak_lolos',
            'subject'     => 'required|string',
            'message'     => 'required|string', // CKEditor output (HTML)
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120', // maksimal 5 MB per file
        ]);

        // Filter applicant sesuai tab
        $query = Applicant::where('batch_id', $data['batch']);
        if ($data['position']) {
            $query->where('position_id', $data['position']);
        }

        if ($data['type'] === 'lolos') {
            $query->where('status', 'Tes Tulis'); // dianggap Lolos Administrasi
        } else {
            $query->where('status', 'Tidak Lolos Seleksi Administrasi');
        }

        $applicants = $query->get();
        $successCount = 0;

        foreach ($applicants as $a) {
            try {
                // Buat mail object
                $mail = new SelectionResultMail(
                    $data['subject'],
                    $data['message'],
                    $this->stage,
                    $data['type'],
                    $a
                );

                // Lampirkan file jika ada
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $mail->attach($file->getRealPath(), [
                            'as'   => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType(),
                        ]);
                    }
                }

                // Kirim email
                Mail::to($a->email)->send($mail);

                // Simpan log sukses
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
                // Simpan log gagal
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
