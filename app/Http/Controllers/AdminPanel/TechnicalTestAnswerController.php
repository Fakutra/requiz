<?php
// app/Http/Controllers/TechnicalTestAnswerController.php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\TechnicalTestAnswer;
use App\Models\TechnicalTestSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TechnicalTestAnswerController extends Controller
{
    /**
     * Simpan / re-upload jawaban technical test untuk suatu schedule.
     * - Valid PDF (≤ 10MB)
     * - Link screen record wajib (sesuai migrasi kamu: NOT NULL)
     * - Hanya pemilik applicant (user login) yang boleh upload
     * - Harus sebelum upload_deadline (jika ada)
     * - Posisi applicant harus sama dengan posisi schedule
     */
    public function store(Request $request, TechnicalTestSchedule $schedule)
    {
        // Validasi input
        $validated = $request->validate([
            'applicant_id'      => ['nullable','integer','exists:applicants,id'],
            'answer_pdf'        => ['required','file','mimes:pdf','max:10240'], // 10MB
            'screen_record_url' => ['required','url'],
        ], [
            'answer_pdf.mimes'  => 'File jawaban harus berformat PDF.',
            'answer_pdf.max'    => 'Ukuran file maksimal 10MB.',
            'screen_record_url.required' => 'Link rekaman layar wajib diisi.',
        ]);

        // Ambil applicant milik user (boleh dikirim tersembunyi, atau otomatis dicari)
        $applicant = null;

        if (!empty($validated['applicant_id'])) {
            $applicant = Applicant::whereKey($validated['applicant_id'])
                ->where('user_id', auth()->id())
                ->first();
        }

        // Jika tidak dikirim, cari applicant milik user untuk posisi di schedule ini
        if (!$applicant) {
            $applicant = Applicant::where('user_id', auth()->id())
                ->where('position_id', $schedule->position_id)
                ->latest('id')
                ->first();
        }

        if (!$applicant) {
            return back()->withErrors(['applicant_id' => 'Data pelamar untuk posisi jadwal ini tidak ditemukan.']);
        }

        // Posisi applicant harus sesuai schedule
        if ((int)$applicant->position_id !== (int)$schedule->position_id) {
            return back()->withErrors(['applicant_id' => 'Pelamar tidak sesuai dengan posisi jadwal.']);
        }

        // Cek deadline
        if (!is_null($schedule->upload_deadline) && now()->gt($schedule->upload_deadline)) {
            return back()->withErrors(['answer_pdf' => 'Batas waktu upload sudah lewat.']);
        }

        // Simpan file ke storage public
        $pdf  = $request->file('answer_pdf');
        $path = $pdf->store('technical_test_answers/'.$applicant->id, 'public'); // storage/app/public/...

        // Buat atau update jawaban untuk pasangan (schedule, applicant)
        $answer = TechnicalTestAnswer::updateOrCreate(
            [
                'technical_test_schedule_id' => $schedule->id,
                'applicant_id'               => $applicant->id,
            ],
            [
                'answer_path'       => $path,
                'screen_record_url' => $validated['screen_record_url'],
                'submitted_at'      => now(),
            ]
        );

        return back()->with('success', 'Jawaban berhasil diunggah.');
    }
}
