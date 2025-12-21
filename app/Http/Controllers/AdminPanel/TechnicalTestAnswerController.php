<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Models\TechnicalTestAnswer;
use App\Models\TechnicalTestSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TechnicalTestAnswerController extends Controller
{
    /**
     * PESERTA: Upload / re-upload jawaban technical test untuk suatu schedule.
     */
    public function store(Request $request, TechnicalTestSchedule $schedule)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'applicant_id'      => ['nullable','integer','exists:applicants,id'],
                'answer_pdf'        => ['required','file','mimes:pdf','max:1024'], // 1MB
                'screen_record_url' => ['required','url'],
            ],
            [
                'answer_pdf.mimes'  => 'File jawaban harus berformat PDF.',
                'answer_pdf.max'    => 'Ukuran file maksimal 1MB.',
                'screen_record_url.required' => 'Link rekaman layar wajib diisi.',
            ]
        );

        // ❌ VALIDATION FAIL
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal mengunggah jawaban. Periksa kembali data yang diinput.');
        }

        $validated = $validator->validated();

        // Tentukan applicant milik user & sesuai posisi schedule
        $applicant = null;

        if (!empty($validated['applicant_id'])) {
            $applicant = Applicant::whereKey($validated['applicant_id'])
                ->where('user_id', auth()->id())
                ->first();
        }

        if (!$applicant) {
            $applicant = Applicant::where('user_id', auth()->id())
                ->where('position_id', $schedule->position_id)
                ->latest('id')
                ->first();
        }

        if (!$applicant) {
            return back()
                ->withErrors(['applicant_id' => 'Data pelamar untuk posisi jadwal ini tidak ditemukan.'])
                ->withInput()
                ->with('error', 'Gagal mengunggah jawaban karena data pelamar tidak ditemukan.');
        }

        if ((int)$applicant->position_id !== (int)$schedule->position_id) {
            return back()
                ->withErrors(['applicant_id' => 'Pelamar tidak sesuai dengan posisi jadwal.'])
                ->withInput()
                ->with('error', 'Gagal mengunggah jawaban karena pelamar tidak sesuai dengan posisi jadwal.');
        }

        if (!is_null($schedule->upload_deadline) && now()->gt($schedule->upload_deadline)) {
            return back()
                ->withErrors(['answer_pdf' => 'Batas waktu upload sudah lewat.'])
                ->withInput()
                ->with('error', 'Gagal mengunggah jawaban karena batas waktu upload sudah lewat.');
        }

        $path = null; // ← FIX: declare dulu biar intelephense gak error

        try {
            // upload file
            $path = $request->file('answer_pdf')->store(
                'technical_test_answers/'.$applicant->id,
                'public'
            );

            TechnicalTestAnswer::updateOrCreate(
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

        } catch (\Throwable $e) {

            // hapus file kalau sempat terupload
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.');
        }
    }

    /**
     * ADMIN: Daftar jawaban + filter by batch/position + search.
     */
    public function index(Request $request)
    {
        $batches = Batch::orderByDesc('start_date')->orderBy('name')->get();

        $positions = Position::when($request->filled('batch_id'), function ($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            })
            ->orderBy('name')
            ->get();

        $answers = TechnicalTestAnswer::query()
            ->with([
                'applicant',
                'applicant.position',
                'schedule',
                'schedule.position',
            ])
            ->when($request->filled('batch_id'), function ($q) use ($request) {
                $q->whereHas('applicant.position', fn($qq) =>
                    $qq->where('batch_id', $request->batch_id)
                );
            })
            ->when($request->filled('position_id'), function ($q) use ($request) {
                $q->whereHas('applicant', fn($qq) => $qq->where('position_id', $request->position_id));
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $s = strtolower($request->q);
                $q->whereHas('applicant', function ($w) use ($s) {
                    $w->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"])
                      ->orWhereRaw('LOWER(email) LIKE ?', ["%{$s}%"]);
                });
            })
            ->orderByDesc('submitted_at')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'total'    => TechnicalTestAnswer::count(),
            'scored'   => TechnicalTestAnswer::whereNotNull('score')->count(),
            'unscored' => TechnicalTestAnswer::whereNull('score')->count(),
        ];

        return view('admin.tech-answers.index', compact('answers', 'batches', 'positions', 'counts'));
    }

    /**
     * ADMIN: Simpan nilai via modal.
     */
    public function update(Request $request, TechnicalTestAnswer $answer)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'score'      => ['required', 'numeric', 'min:0', 'max:100'],
                'keterangan' => ['nullable', 'string', 'max:2000'],
            ],
            [
                'score.required' => 'Nilai wajib diisi.',
                'score.numeric'  => 'Nilai harus angka.',
                'score.min'      => 'Nilai minimal 0.',
                'score.max'      => 'Nilai maksimal 100.',
            ]
        );

        // ❌ VALIDATION FAIL
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan nilai. Periksa kembali data yang diinput.');
        }

        try {
            $answer->update($validator->validated());

            return back()->with('success', 'Nilai berhasil disimpan.');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan nilai. Silakan coba lagi.');
        }
    }
}
