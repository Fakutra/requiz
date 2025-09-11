<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\SelectionLog;
use App\Models\Position;
use App\Models\EmailLog;
use App\Models\Batch; // pastikan model Batch ada
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Exports\ApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $applicants = $this->getFilteredApplicants($request)
            ->orderBy('id', 'asc')
            ->paginate(10);

        $positions = Position::orderBy('name')->get();

        return view('admin.applicant.index', compact('applicants', 'positions'));
    }

    public function export(Request $request)
    {
        $fileName = 'data-pelamar-' . date('Y-m-d') . '.xlsx';
        return Excel::download(new ApplicantsExport($request), $fileName);
    }

    private function getFilteredApplicants(Request $request)
    {
        $search     = $request->input('search');
        $status     = $request->input('status');
        $positionId = $request->input('position');

        $query = Applicant::query()->with('position')->orderBy('name', 'asc');

        $query->when($search, function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(pendidikan) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(universitas) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(jurusan) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw("DATE_PART('year', AGE(CURRENT_DATE, tgl_lahir)) = ?", [(int) $search])
                    ->orWhereHas('position', function ($posQ) use ($search) {
                        $posQ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        });

        $query->when($status, fn ($q) => $q->where('status', $status));
        $query->when($positionId, fn ($q) => $q->where('position_id', $positionId));

        return $query;
    }

    public function update(Request $request, Applicant $applicant)
    {
        $allowedPendidikan = ['SMA/Sederajat','Diploma','S1','S2','S3'];
        $allowedStatus = [
            'Seleksi Administrasi','Tes Tulis','Technical Test','Interview','Offering',
            'Tidak Lolos Seleksi Administrasi','Tidak Lolos Seleksi Tes Tulis',
            'Tidak Lolos Technical Test','Tidak Lolos interview',
            'Menerima Offering','Menolak Offering',
        ];

        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            // kalau mau unik di tabel applicants, pakai baris di bawah ini,
            // kalau tidak, ganti saja dengan: ['required','email','max:255']
            'email'       => ['required','email','max:255', Rule::unique('applicants','email')->ignore($applicant->id)],
            'nik'         => ['required','string','max:16'],
            'no_telp'     => ['required','string','max:14'],
            'tpt_lahir'   => ['required','string','max:255'],
            'tgl_lahir'   => ['required','date'],
            'alamat'      => ['required','string','max:255'],
            'pendidikan'  => ['required', Rule::in($allowedPendidikan)],
            'universitas' => ['required','string','max:255'],
            'jurusan'     => ['required','string','max:255'],
            'thn_lulus'   => ['nullable','digits:4'],
            'status'      => ['required', Rule::in($allowedStatus)],
            'cv_document' => ['nullable','file','mimes:pdf','max:3072'], // 3 MB
            'position_id' => ['required','exists:positions,id'],
        ]);

        if ($request->hasFile('cv_document')) {
            if ($applicant->cv_document) {
                Storage::disk('public')->delete($applicant->cv_document);
            }
            $data['cv_document'] = $request->file('cv_document')->store('cv_documents', 'public');
        }

        $applicant->update($data);

        // balik ke halaman modal berasal (tetap di per-tahap)
        return back()->with('success', 'Data pelamar berhasil diperbarui.');
    }

    public function destroy(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('admin.applicant.index')->with('success', 'Data pelamar berhasil dihapus.');
    }

    public function destroySeleksi(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('admin.applicant.seleksi.index')->with('success', 'Data pelamar berhasil dihapus.');
    }

    /**
     * REKAP seleksi per tahap + dropdown Batch (filter berdasarkan batch).
     */
    public function seleksiIndex(Request $request)
    {
        $batches = Batch::orderBy('id')->get();
        $currentBatchId = $request->query('batch') ?: ($batches->first()->id ?? null);

        $totalApplicants = null;
        $rekap = [];

        if (!$currentBatchId) {
            return view('admin.applicant.seleksi.index', compact('batches','currentBatchId','totalApplicants','rekap'));
        }

        // ✅ Pakai applicants.batch_id (bukan positions.batch_id)
        $applicantIds = Applicant::where('batch_id', $currentBatchId)->pluck('id');
        $totalApplicants = $applicantIds->count();

        $stages = [
            'Seleksi Administrasi' => 'admin.applicant.seleksi.administrasi',
            'Tes Tulis'            => 'admin.applicant.seleksi.tes_tulis',
            'Technical Test'       => 'admin.applicant.seleksi.technical_test',
            'Interview'            => 'admin.applicant.seleksi.interview',
            'Offering'             => 'admin.applicant.seleksi.offering',
        ];

        foreach ($stages as $label => $routeName) {
            $stageKey = \Illuminate\Support\Str::slug($label);

            $latestIds = SelectionLog::where('stage_key', $stageKey)
                ->whereIn('applicant_id', $applicantIds)
                ->select(DB::raw('MAX(id) AS id'))
                ->groupBy('applicant_id')
                ->pluck('id');

            if ($latestIds->isEmpty()) {
                $rekap[] = ['label' => $label, 'lolos' => 0, 'gagal' => 0, 'route_name' => $routeName];
                continue;
            }

            $counts = SelectionLog::whereIn('id', $latestIds)
                ->selectRaw("
                    SUM(CASE WHEN result='lolos' THEN 1 ELSE 0 END) AS lolos,
                    SUM(CASE WHEN result='tidak_lolos' THEN 1 ELSE 0 END) AS gagal
                ")
                ->first();

            $rekap[] = [
                'label'      => $label,
                'lolos'      => (int) ($counts->lolos ?? 0),
                'gagal'      => (int) ($counts->gagal ?? 0),
                'route_name' => $routeName,
            ];
        }

        return view('admin.applicant.seleksi.index', compact(
            'batches','currentBatchId','totalApplicants','rekap'
        ));
    }

    // ===== Wrapper per tahap (URL tanpa parameter) =====
    // ApplicantController.php


    public function editSeleksi($id)
    {
        $applicant = Applicant::with('position')->findOrFail($id);
        return view('admin.applicants.edit-seleksi', compact('applicant'));
    }

    private function nextStageExact(string $stage): string
    {
        $map = [
            'Seleksi Administrasi' => 'Tes Tulis',
            'Tes Tulis'            => 'Technical Test',
            'Technical Test'       => 'Interview',
            'Interview'            => 'Offering',
            'Offering'             => 'Offering',
        ];
        return $map[$stage] ?? $stage;
    }

    private function failEnumFor(string $stage): string
    {
        $map = [
            'Seleksi Administrasi' => 'Tidak Lolos Seleksi Administrasi',
            'Tes Tulis'            => 'Tidak Lolos Seleksi Tes Tulis',
            'Technical Test'       => 'Tidak Lolos Technical Test',
            'Interview'            => 'Tidak Lolos interview',
            'Offering'             => 'Menolak Offering',
        ];
        return $map[$stage] ?? $stage;
    }

    /**
     * LOGIKA INTI halaman proses per tahap.
     */
    
    public function process(Request $request, string $stage)
    {
        $batchId    = $request->integer('batch');
        $positions  = Position::orderBy('name')->get();
        $allJurusan = Applicant::whereNotNull('jurusan')->distinct()->pluck('jurusan');

        $nextStage  = $this->nextStageExact($stage);
        $failEnum   = $this->failEnumFor($stage);

        $q = Applicant::with('position');

        // ==== FILTER BATCH (konsisten dgn seleksiIndex) ====
        if ($batchId) {
            // Jika batch_id ada di tabel positions:
            $q->whereHas('position', function ($w) use ($batchId) {
                $w->where('batch_id', $batchId);
            });

            // Jika sebaliknya batch_id ada di applicants, pakai ini saja:
            // $q->where('batch_id', $batchId);
        }

        // ==== FILTER STATUS ====
        if ($request->filled('status')) {
            $st = $request->status;
            if ($st === '__NEXT__') {
                $q->where(function ($w) use ($stage, $nextStage) {
                    $w->where('status', $nextStage)
                    ->orWhere('status', 'Lolos '.$stage);
                });
            } elseif ($st === '__FAILED__') {
                $q->where(function ($w) use ($stage, $failEnum) {
                    $w->where('status', $failEnum)
                    ->orWhere('status', 'Tidak Lolos '.$stage);
                });
            } else {
                $q->where('status', $st);
            }
        } else {
            $q->where(function ($w) use ($stage, $nextStage, $failEnum) {
                $w->where('status', $stage)
                ->orWhere('status', $nextStage)
                ->orWhere('status', $failEnum)
                ->orWhere('status', 'Lolos '.$stage)
                ->orWhere('status', 'Tidak Lolos '.$stage);
            });
        }

        // ==== SEARCH & FILTER TAMBAHAN ====
        if ($s = trim((string) $request->input('search'))) {
            // Jika pakai Postgres, ILIKE oke; untuk MySQL ubah ke LIKE.
            $q->where(function ($x) use ($s) {
                $x->where('name', 'ilike', "%{$s}%")
                ->orWhere('email', 'ilike', "%{$s}%")
                ->orWhere('jurusan', 'ilike', "%{$s}%");
            });
        }
        if ($request->filled('position')) $q->where('position_id', $request->position);
        if ($request->filled('jurusan'))  $q->where('jurusan', $request->jurusan);

        $applicants = $q->orderBy('name')->paginate(20)->appends($request->query());

        // ==== BADGE/TAMPILAN ====
        $collection = $applicants->getCollection();
        $collection->transform(function ($a) use ($stage, $nextStage, $failEnum) {
            $a->_stage_state  = 'other';
            $a->_stage_status = $a->status;
            $a->_stage_badge  = 'bg-slate-50 text-slate-600 border border-slate-200';

            if ($stage === 'Offering') {
                if ($a->status === 'Menerima Offering') {
                    $a->_stage_state  = 'lolos';
                    $a->_stage_status = 'Menerima Offering';
                    $a->_stage_badge  = 'bg-green-50 text-green-700 border border-green-200';
                    return $a;
                }
                if ($a->status === 'Menolak Offering') {
                    $a->_stage_state  = 'gagal';
                    $a->_stage_status = 'Menolak Offering';
                    $a->_stage_badge  = 'bg-red-50 text-red-700 border border-red-200';
                    return $a;
                }
            }

            if ($a->status === $stage) {
                $a->_stage_state  = 'current';
                $a->_stage_status = $stage;
                $a->_stage_badge  = 'bg-gray-100 text-gray-800 border border-gray-200';
            } elseif ($a->status === $failEnum || $a->status === 'Tidak Lolos '.$stage) {
                $a->_stage_state  = 'gagal';
                $a->_stage_status = $a->status;
                $a->_stage_badge  = 'bg-red-50 text-red-700 border border-red-200';
            } elseif ($a->status === $nextStage || $a->status === 'Lolos '.$stage) {
                $a->_stage_state  = 'lolos';
                $a->_stage_status = 'Lolos '.$stage;
                $a->_stage_badge  = 'bg-green-50 text-green-700 border border-green-200';
            }
            return $a;
        });

        // ==== STATUS EMAIL ====
        $ids = $collection->pluck('id')->all();
        $sentMap = EmailLog::whereIn('applicant_id', $ids)
            ->where('stage', $stage)
            ->where('success', true)
            ->select('applicant_id', DB::raw('MAX(created_at) as last_sent'))
            ->groupBy('applicant_id')
            ->pluck('last_sent', 'applicant_id');

        $collection->transform(function ($a) use ($sentMap) {
            $a->_email_sent    = $sentMap->has($a->id);
            $a->_email_sent_at = $sentMap->get($a->id);
            return $a;
        });
        $applicants->setCollection($collection);

        return view(
            $this->viewForStage($stage),
            compact('applicants','positions','allJurusan','stage','nextStage','failEnum')
        );
    }

    private function viewForStage(string $stage): string
    {
        return match ($stage) {
            'Seleksi Administrasi' => 'admin.applicant.seleksi.administrasi.index',
            'Tes Tulis'            => 'admin.applicant.seleksi.tes_tulis.index',
            'Technical Test'       => 'admin.applicant.seleksi.technical_test.index',
            'Interview'            => 'admin.applicant.seleksi.interview.index',
            'Offering'             => 'admin.applicant.seleksi.offering.index',
            default                => 'admin.applicant.seleksi.administrasi.index',
        };
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'recipients'    => 'required|string',
            'recipient_ids' => 'required|string',
            'stage'         => 'required|string',
            'use_template'  => 'nullable|boolean',
            'subject'       => 'nullable|string|max:255',
            'message'       => 'nullable|string|max:20000',
            'attachment'    => 'required|file|mimes:pdf|max:5120',
        ]);

        $useTemplate = $request->boolean('use_template', true);

        $emails = \Illuminate\Support\Str::of(str_replace(["\r\n", "\n", ";"], ",", $request->recipients))
            ->explode(',')
            ->map(fn($e) => trim($e))
            ->filter()
            ->unique()
            ->values();

        $ids = collect(explode(',', $request->recipient_ids))
            ->map(fn($v) => (int) trim($v))
            ->filter()
            ->values();

        $applicantsByEmail = Applicant::with('position')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('email');

        $file = $request->file('attachment');

        foreach ($emails as $to) {
            $match         = $applicantsByEmail->get($to);
            $applicantId   = $match?->id;
            $fullName      = $match?->name ?? $to;
            $positionName  = $match?->position?->name ?? '-';

            if ($useTemplate) {
                ['subject' => $subject, 'message' => $body]
                    = $this->defaultEmailForStage($request->stage, $fullName, $positionName);
            } else {
                $subject = $request->input('subject', 'Informasi Seleksi');
                $body    = $request->input('message', '');
            }

            try {
                \Illuminate\Support\Facades\Mail::raw($body, function ($mail) use ($to, $subject, $file) {
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
                    'stage'        => $request->stage,
                    'subject'      => $subject,
                    'success'      => true,
                    'error'        => null,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal kirim email', ['to' => $to, 'error' => $e->getMessage()]);

                EmailLog::create([
                    'applicant_id' => $applicantId,
                    'email'        => $to,
                    'stage'        => $request->stage,
                    'subject'      => $subject,
                    'success'      => false,
                    'error'        => substr($e->getMessage(), 0, 1000),
                ]);
            }
        }

        return back()->with('success', 'Proses kirim email selesai.');
    }

    private function defaultEmailForStage(string $stage, string $fullName, ?string $positionName = null): array
    {
        $positionName = $positionName ?: '-';

        $subject = "INFORMASI HASIL SELEKSI {$stage} TAD/OUTSOURCING - PLN ICON PLUS";
        $body = "Halo {$fullName}

Terima kasih atas partisipasi Saudara/i dalam mengikuti proses seleksi TAD/OUTSOURCING PLN ICON PLUS pada posisi {$positionName}.

Selamat Anda lolos pada tahap {$stage}. Selanjutnya, silakan cek jadwal Anda untuk tahap berikutnya pada lampiran email ini.

Demikian kami sampaikan.
Terima kasih atas partisipasinya dan semoga sukses.";

        return ['subject' => $subject, 'message' => $body];
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'stage'               => 'required|string',
            'selected_applicants' => 'required|array',
            'status'              => 'required|array',
        ]);

        $stage    = (string) $request->stage;
        $stageKey = Str::slug($stage);

        DB::transaction(function () use ($request, $stage, $stageKey) {
            foreach ($request->selected_applicants as $applicantId) {
                $applicant = Applicant::with('position')->findOrFail($applicantId);
                $action    = $request->status[$applicantId] ?? null;

                if ($action === 'lolos') {
                    if ($stage === 'Offering') {
                        $applicant->status = 'Menerima Offering';
                    } else {
                        $applicant->status = $this->nextStageExact($stage);
                    }
                    $applicant->save();

                    SelectionLog::create([
                        'applicant_id' => $applicant->id,
                        'stage'        => $stage,
                        'stage_key'    => $stageKey,
                        'result'       => 'lolos',
                        'position_id'  => $applicant->position_id,
                        'jurusan'      => $applicant->jurusan,
                        'acted_by'     => auth()->id(),
                    ]);
                } elseif ($action === 'tidak_lolos') {
                    $applicant->status = $this->failEnumFor($stage);
                    $applicant->save();

                    SelectionLog::create([
                        'applicant_id' => $applicant->id,
                        'stage'        => $stage,
                        'stage_key'    => $stageKey,
                        'result'       => 'tidak_lolos',
                        'position_id'  => $applicant->position_id,
                        'jurusan'      => $applicant->jurusan,
                        'acted_by'     => auth()->id(),
                    ]);
                }
            }
        });

        return back()->with('success', 'Status peserta & log berhasil diperbarui.');
    }
    

    public function showStageApplicants($stage)
    {
        $nextStage = $this->nextStageExact($stage);
        $failEnum  = $this->failEnumFor($stage);

        $query = Applicant::with('user', 'position');

        if (request()->filled('status')) {
            $st = request('status');
            if ($st === $stage) {
                $query->where('status', $stage);
            } elseif ($st === '__NEXT__') {
                $query->where('status', $nextStage);
            } elseif ($st === '__FAILED__') {
                $query->where('status', $failEnum);
            } else {
                $query->where('status', $st);
            }
        } else {
            $query->where(function ($w) use ($stage, $nextStage, $failEnum) {
                $w->where('status', $stage)
                  ->orWhere('status', $nextStage)
                  ->orWhere('status', $failEnum);
            });
        }

        if (request('jurusan')) $query->where('jurusan', request('jurusan'));

        $applicants   = $query->orderBy('name')->paginate(20)->withQueryString();
        $allJurusan   = Applicant::select('jurusan')->distinct()->pluck('jurusan');
        $filteredStatuses = ["__NEXT__", "__FAILED__", $stage];

        return view('admin.applicant.seleksi.process', compact(
            'applicants', 'stage', 'allJurusan', 'filteredStatuses', 'nextStage', 'failEnum'
        ));
    }
}