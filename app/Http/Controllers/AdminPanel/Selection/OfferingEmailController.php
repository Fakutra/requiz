<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use App\Mail\SelectionResultMail;
use App\Models\Applicant;
use App\Models\Offering;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth; // ✅ tambahkan
use Illuminate\Support\Facades\Log;  // ✅ tambahkan
use App\Services\ActivityLogger;     // ✅ tambahkan
use Throwable;

class OfferingEmailController extends Controller
{
    protected string $stage = 'Offering';

    public function send(Request $request)
    {
        $data = $request->validate([
            'batch'       => 'nullable|exists:batches,id',
            'position'    => 'nullable|exists:positions,id',
            'type'        => 'required|in:offering,selected',
            'ids'         => 'nullable|string',
            'subject'     => 'required|string',
            'message'     => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ]);

        // --- Tentukan target applicant ---
        if ($data['type'] === 'selected') {
            $ids = array_filter(explode(',', $data['ids'] ?? ''));
            $applicants = Applicant::whereIn('id', $ids)->get();
        } else {
            $query = Applicant::where('batch_id', $data['batch']);
            if ($data['position']) {
                $query->where('position_id', $data['position']);
            }
            $query->where('status', 'Offering');
            $applicants = $query->get();
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($applicants as $a) {
            try {
                $offering = Offering::where('applicant_id', $a->id)->first();

                // --- Token data pribadi ---
                $placeholders = [
                    '{{name}}'            => $a->name ?? '-',
                    '{{job}}'             => optional($offering->job ?? null)->name ?? '-',
                    '{{field}}'           => optional($offering->field ?? null)->name ?? '-',
                    '{{subfield}}'        => optional($offering->subfield ?? null)->name ?? '-',
                    '{{seksi}}'           => optional($offering->seksi ?? null)->name ?? '-',
                    '{{gaji}}'            => $offering ? number_format($offering->gaji, 0, ',', '.') : '-',
                    '{{uang_makan}}'      => $offering ? number_format($offering->uang_makan, 0, ',', '.') : '-',
                    '{{uang_transport}}'  => $offering ? number_format($offering->uang_transport, 0, ',', '.') : '-',
                    '{{kontrak_mulai}}'   => optional($offering->kontrak_mulai)->format('d-m-Y') ?? '-',
                    '{{kontrak_selesai}}' => optional($offering->kontrak_selesai)->format('d-m-Y') ?? '-',
                    '{{periode_kontrak}}' => $offering && $offering->kontrak_mulai && $offering->kontrak_selesai
                        ? $offering->kontrak_mulai->diffInMonths($offering->kontrak_selesai) . ' bulan'
                        : '-',
                    '{{link_pkwt}}'        => $offering->link_pkwt ?? '#',
                    '{{link_berkas}}'      => $offering->link_berkas ?? '#',
                    '{{link_form_pelamar}}'=> $offering->link_form_pelamar ?? '#',
                ];

                // --- Ganti token dalam pesan ---
                $personalizedMessage = strtr($data['message'], $placeholders);

                $mail = new SelectionResultMail(
                    $data['subject'],
                    $personalizedMessage,
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
                $failCount++;
                EmailLog::create([
                    'applicant_id' => $a->id,
                    'email'        => $a->email,
                    'stage'        => $this->stage,
                    'subject'      => $data['subject'],
                    'success'      => false,
                    'error'        => $e->getMessage(),
                ]);
                Log::warning("Gagal kirim email Offering ke {$a->email}: ".$e->getMessage());
            }
        }

        // ✅ Catat log aktivitas setelah semua email selesai dikirim
        try {
            $user = Auth::user()?->name ?? 'System';
            $total = count($applicants);
            $typeLabel = $data['type'] === 'selected' ? 'individual' : 'massal';
            $desc = "{$user} mengirim email Offering ({$typeLabel}) ke {$total} peserta — Berhasil: {$successCount}, Gagal: {$failCount}";

            ActivityLogger::log(
                'send_email',
                'Offering',
                $desc,
                json_encode([
                    'subject' => $data['subject'],
                    'berhasil' => $successCount,
                    'gagal' => $failCount,
                    'total' => $total,
                    'tipe' => $data['type'],
                ])
            );
        } catch (Throwable $e) {
            Log::warning('Gagal mencatat log pengiriman email Offering: '.$e->getMessage());
        }

        return back()->with(
            'success',
            "Email offering berhasil dikirim ke {$successCount} peserta dari total " . count($applicants)
        );
    }
}
