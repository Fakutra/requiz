<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use App\Models\Applicant;
use App\Models\Position;
use App\Models\Batch;
use App\Models\TechnicalTestAnswer;
use App\Models\TechnicalTestSchedule;

class TechnicalTestController extends Controller
{
    private string $stage = 'Technical Test';
    private string $nextStage = 'Interview';
    private string $failEnum = 'Tidak Lolos Technical Test';

    public function index(Request $request)
    {
        // Ambil batch dari Rekap (?batch=...) ATAU dari form halaman ini (?batch_id=...)
        $batchId    = $request->integer('batch_id')    ?? $request->integer('batch');
        $positionId = $request->integer('position_id') ?? $request->integer('position');
        $search     = trim((string) ($request->query('q') ?? $request->query('search')));

        // Query utama jawaban
        $q = TechnicalTestAnswer::query()
            ->with(['applicant.position', 'schedule']);

        // Filter by batch (via applicant.batch_id)
        if ($batchId) {
            $q->whereHas('applicant', fn($w) => $w->where('batch_id', $batchId));
        }

        // Filter by position
        if ($positionId) {
            $q->whereHas('applicant', fn($w) => $w->where('position_id', $positionId));
        }

        // Search nama/email/posisi
        if ($search !== '') {
            $needle = '%'.mb_strtolower($search).'%';
            $q->where(function ($w) use ($needle) {
                $w->whereHas('applicant', function ($a) use ($needle) {
                    $a->whereRaw('LOWER(name) LIKE ?',  [$needle])
                      ->orWhereRaw('LOWER(email) LIKE ?', [$needle]);
                })
                ->orWhereHas('applicant.position', fn($p) => $p->whereRaw('LOWER(name) LIKE ?', [$needle]));
            });
        }

        // Urutan default: terbaru dulu
        $q->orderByDesc('submitted_at');

        $answers = $q->paginate(20)->withQueryString();

        // Dropdown data
        $batches   = Batch::orderByDesc('start_date')->orderByDesc('id')->get(['id','name','start_date']);
        $positions = Position::orderBy('name')->get(['id','name']);

        // (opsional) dropdown schedules, filter via relasi position → batch_id (Opsi A)
        $schedules = collect();
        if (Schema::hasTable('technical_test_schedules')) {
            $schQ = TechnicalTestSchedule::query()
                ->with('position:id,name,batch_id')
                ->orderByDesc('id');

            if ($batchId)    $schQ->whereHas('position', fn($w) => $w->where('batch_id', $batchId));
            if ($positionId) $schQ->where('position_id', $positionId);

            $schedules = $schQ->limit(200)->get(['id','position_id','schedule_date','zoom_link','zoom_id','zoom_passcode','keterangan','upload_deadline']);
        }

        // Kirim ke view baru (tech-answers)
        return view('admin.tech-answers.index', [
            'answers'         => $answers,
            'batches'         => $batches,
            'positions'       => $positions,
            'schedules'       => $schedules,
            'stage'           => $this->stage,
            'nextStage'       => $this->nextStage,
            'failEnum'        => $this->failEnum,
            // untuk kenyamanan di Blade:
            'selectedBatchId' => $batchId,
            'selectedPosId'   => $positionId,
            'q'               => $search,
        ]);
    }

    public function updateAnswer(Request $request, TechnicalTestAnswer $answer)
    {
        $data = $request->validate([
            'score'      => ['nullable','numeric','min:0','max:100'],
            'keterangan' => ['nullable','string','max:10000'],
        ]);

        $answer->update($data);

        return back()->with('success', 'Skor/keterangan berhasil diperbarui.');
    }

    public function bulkStatus(Request $request)
    {
        $data = $request->validate([
            'ids'    => ['required','string'],
            'status' => ['required','string','max:255', Rule::in($this->allowedStatuses())],
        ]);

        $answerIds = collect(explode(',', $data['ids']))
            ->map(fn($v) => (int) trim($v))
            ->filter()
            ->unique()
            ->values();

        if ($answerIds->isEmpty()) {
            return back()->with('error', 'Tidak ada jawaban yang dipilih.');
        }

        $applicantIds = TechnicalTestAnswer::whereIn('id', $answerIds)
            ->pluck('applicant_id')->unique()->values();

        if ($applicantIds->isEmpty()) {
            return back()->with('error', 'Applicant tidak ditemukan dari jawaban terpilih.');
        }

        DB::transaction(function () use ($applicantIds, $data) {
            Applicant::whereIn('id', $applicantIds)->update(['status' => $data['status']]);

            if (Schema::hasTable('selection_logs')) {
                $now  = now();
                $rows = $applicantIds->map(fn($id) => [
                    'applicant_id' => $id,
                    'stage_key'    => Str::slug($this->stage),
                    'stage_label'  => $this->stage,
                    'result'       => $this->resultFromStatus($data['status']),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ])->all();
                DB::table('selection_logs')->insert($rows);
            }
        });

        return back()->with('success', 'Status applicant berhasil diperbarui.');
    }

    public function sendEmail(Request $request)
    {
        $data = $request->validate([
            'ids'         => ['required','string'],
            'recipients'  => ['nullable','string'],
            'use_template'=> ['nullable'],
            'subject'     => ['nullable','string','max:255'],
            'message'     => ['nullable','string','max:20000'],
            'attachment'  => ['nullable','file','mimes:pdf','max:5120'],
        ]);

        $answerIds = collect(explode(',', $data['ids']))
            ->map(fn($v) => (int) trim($v))
            ->filter()
            ->unique()
            ->values();

        if ($answerIds->isEmpty()) {
            return back()->with('error', 'Tidak ada jawaban yang dipilih.');
        }

        $picked = TechnicalTestAnswer::with('applicant.position')
            ->whereIn('id', $answerIds)->get();

        $autoEmails = $picked->pluck('applicant.email')->filter()->values();
        $manual = collect([]);
        if (!empty($data['recipients'])) {
            $manual = collect(preg_split('/[,\s;]+/', $data['recipients']))->filter();
        }

        $emails = $manual->merge($autoEmails)->unique()->values();
        if ($emails->isEmpty()) {
            return back()->with('error', 'Tidak ada email valid untuk dikirim.');
        }

        $useTemplate = $request->boolean('use_template');
        $file = $request->file('attachment');

        $appMap = $picked->mapWithKeys(function ($ans) {
            return [$ans->applicant->email => $ans->applicant];
        });

        $ok = 0; $fail = 0;
        foreach ($emails as $to) {
            $app = $appMap->get($to);
            $fullName     = $app?->name ?? $to;
            $positionName = $app?->position?->name ?? '-';

            if ($useTemplate) {
                $subject = "INFORMASI HASIL SELEKSI {$this->stage} - PLN ICON PLUS";
                $body = "Halo {$fullName}

Terima kasih atas partisipasi Anda pada proses {$this->stage} untuk posisi {$positionName}.

Selamat, Anda dinyatakan LOLOS pada tahap {$this->stage}.
Silakan cek lampiran untuk informasi lanjutan.

Hormat kami.";
            } else {
                $subject = $data['subject'] ?: 'Informasi Seleksi';
                $body    = $data['message'] ?: '';
            }

            try {
                \Mail::raw($body, function ($mail) use ($to, $subject, $file) {
                    $mail->to($to)
                         ->subject($subject)
                         ->from(config('mail.from.address'), config('mail.from.name'));
                    if ($file) {
                        $mail->attach($file->getRealPath(), [
                            'as'   => $file->getClientOriginalName(),
                            'mime' => 'application/pdf',
                        ]);
                    }
                });

                if (Schema::hasTable('email_logs')) {
                    DB::table('email_logs')->insert([
                        'applicant_id' => $app?->id,
                        'email'        => $to,
                        'stage'        => $this->stage,
                        'subject'      => $subject,
                        'success'      => true,
                        'error'        => null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
                $ok++;
            } catch (\Throwable $e) {
                \Log::error('Gagal kirim email', ['to' => $to, 'error' => $e->getMessage()]);

                if (Schema::hasTable('email_logs')) {
                    DB::table('email_logs')->insert([
                        'applicant_id' => $app?->id,
                        'email'        => $to,
                        'stage'        => $this->stage,
                        'subject'      => $subject ?? 'Informasi Seleksi',
                        'success'      => false,
                        'error'        => mb_substr($e->getMessage(), 0, 1000),
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
                $fail++;
            }
        }

        return back()->with($fail ? 'error' : 'success', "Email terkirim: {$ok}, gagal: {$fail}.");
    }

    // ================= Helpers =================

    private function allowedStatuses(): array
    {
        return [
            'Technical Test',
            'Lolos Technical Test',
            'Tidak Lolos Technical Test',
            'Interview',
        ];
    }

    private function resultFromStatus(string $status): string
    {
        return match (true) {
            $status === 'Lolos '.$this->stage || $status === $this->nextStage => 'lolos',
            $status === 'Tidak Lolos '.$this->stage || $status === $this->failEnum => 'tidak_lolos',
            default => 'lainnya',
        };
    }
}
