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
            'ids'           => 'nullable|string', // untuk selected
            'subject'       => 'required|string',
            'message'       => 'required|string', // CKEditor / Trix output (HTML)
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120', // maksimal 5 MB
        ]);

        // 🧩 Tentukan target applicants
        if ($data['type'] === 'selected') {
            $ids = array_filter(explode(',', $data['ids'] ?? ''));
            $applicants = Applicant::whereIn('id', $ids)->get();
        } else {
            $query = Applicant::where('batch_id', $data['batch']);
            if ($data['position']) {
                $query->where('position_id', $data['position']);
            }

            if ($data['type'] === 'lolos') {
                // ✅ Semua status yang dianggap Lolos Seleksi Administrasi
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
        $failCount = 0;

        foreach ($applicants as $a) {
            try {
                // ✉️ Buat mail object
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

                $failCount++;
            }
        }

        // ============================
        // ✅ Log Aktivitas Berdasarkan Tab Email
        // ============================
        $total = count($applicants);

        // Tentukan jenis aksi berdasarkan tab
        $action = match ($data['type']) {
            'lolos'        => 'send_email_lolos',
            'tidak_lolos'  => 'send_email_tidak_lolos',
            'selected'     => 'send_email_terpilih',
            default        => 'send_email',
        };

        // Label tampilan untuk log
        $tabLabel = match ($data['type']) {
            'lolos'        => 'Lolos Seleksi Administrasi',
            'tidak_lolos'  => 'Tidak Lolos Seleksi Administrasi',
            'selected'     => 'Peserta Terpilih',
            default        => 'Seleksi Administrasi',
        };

        // Simpan log ke ActivityLogger
        ActivityLogger::log(
            $action,
            'Seleksi Administrasi',
            Auth::user()->name." mengirim email hasil {$tabLabel} ke {$successCount} dari {$total} peserta (gagal: {$failCount})",
            $data['type'] === 'selected'
                ? 'Selected IDs: '.implode(',', $ids ?? [])
                : 'Batch ID: '.$data['batch']
        );

        // ============================

        return back()->with(
            'success',
            "Email berhasil dikirim ke {$successCount} peserta dari total {$total}"
        );
    }
}
