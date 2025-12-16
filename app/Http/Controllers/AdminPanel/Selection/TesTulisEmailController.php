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
            'message'       => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:5120',
            'ids'           => 'nullable|string',
        ]);

        $selectedIds = [];
        if ($data['type'] === 'selected') {
            $selectedIds = array_values(array_filter(array_map('trim', explode(',', (string)($data['ids'] ?? '')))));
            $applicants = Applicant::with(['user:id,name,email'])
                ->whereIn('id', $selectedIds)
                ->get();
        } else {
            $query = Applicant::with(['user:id,name,email'])
                ->where('batch_id', $data['batch']);

            if (!empty($data['position'])) {
                $query->where('position_id', $data['position']);
            }

            if ($data['type'] === 'lolos') {
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
                $query->where('status', 'Tidak Lolos Tes Tulis');
            }

            $applicants = $query->get();
        }

        $total        = $applicants->count();
        $successCount = 0;
        $failCount    = 0;
        $failSamples  = [];

        foreach ($applicants as $a) {
            $recipient = trim((string) ($a->email ?? $a->user?->email ?? ''));

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
                if (count($failSamples) < 10) $failSamples[] = ($a->name ?? "ID {$a->id}") . " (email kosong)";
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
                report($e);

                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $recipient,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => $e->getMessage(),
                ]);

                $failCount++;
                if (count($failSamples) < 10) $failSamples[] = ($a->name ?? "ID {$a->id}") . " (" . $e->getMessage() . ")";
            }
        }

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
            (Auth::user()->name ?? 'System') . " mengirim email hasil {$tabLabel} ke {$successCount} dari {$total} peserta (gagal: {$failCount})",
            $targetInfo
        );

        $response = back();

        if ($total === 0) {
            return $response->with('error', 'Tidak ada peserta yang cocok dengan filter/target yang dipilih.');
        }

        if ($successCount > 0) {
            $response = $response->with('success', "Email berhasil dikirim ke {$successCount} peserta dari total {$total}.");
        }

        if ($failCount > 0) {
            $sampleText = implode(', ', $failSamples);
            $suffix = ($failCount > count($failSamples)) ? ' (dan lainnya)' : '';
            $response = $response->with('error', "Ada {$failCount} email yang gagal dikirim: {$sampleText}{$suffix}. Cek Email Log untuk detail.");
        }

        return $response;
    }
}
