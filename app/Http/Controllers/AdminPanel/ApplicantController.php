<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\SelectionLog;
use App\Models\Position;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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

    /**
     * Ambil data pelamar dengan filter.
     */
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

    /**
     * EDIT/UPDATE pelamar.
     */
    public function update(Request $request, Applicant $applicant)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'nik'         => 'required|string|max:16',
            'no_telp'     => 'required|string|max:14',
            'tpt_lahir'   => 'required|string|max:255',
            'tgl_lahir'   => 'required|date',
            'alamat'      => 'required|string|max:255',
            'pendidikan'  => 'required|string|max:255',
            'universitas' => 'required|string|max:255',
            'jurusan'     => 'required|string|max:255',
            'thn_lulus'   => 'required|string|max:4',
            'status'      => 'required|in:Seleksi Administrasi,Tes Tulis,Technical Test,Interview,Offering,Tidak Lolos Seleksi Administrasi,Tidak Lolos Seleksi Tes Tulis,Tidak Lolos Technical Test,Tidak Lolos interview,Menerima Offering,Menolak Offering',
            'cv_document' => 'nullable|file|mimes:pdf|max:3072',
            'position_id' => 'required|exists:positions,id',
        ]);

        if ($request->hasFile('cv_document')) {
            if ($applicant->cv_document) {
                Storage::delete('public/' . $applicant->cv_document);
            }
            $path = $request->file('cv_document')->store('cv_documents', 'public');
            $validatedData['cv_document'] = $path;
        }

        $applicant->update($validatedData);

        return redirect()->route('admin.applicant.index')->with('success', 'Data pelamar berhasil diperbarui.');
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

    public function seleksiIndex()
    {
        $stages = [
            'Seleksi Administrasi',
            'Tes Tulis',
            'Technical Test',
            'Interview',
            'Offering',
        ];

        $rekap = [];

        foreach ($stages as $stage) {
            $stageKey = Str::slug($stage);

            // Ambil log terakhir per applicant untuk stage ini
            $latestIds = SelectionLog::where('stage_key', $stageKey)
                ->select(DB::raw('MAX(id) AS id'))
                ->groupBy('applicant_id')
                ->pluck('id');

            if ($latestIds->isEmpty()) {
                $rekap[$stage] = ['lolos' => 0, 'gagal' => 0, 'route' => $stage];
                continue;
            }

            $counts = SelectionLog::whereIn('id', $latestIds)
                ->selectRaw("
                    SUM(CASE WHEN result='lolos' THEN 1 ELSE 0 END) AS lolos,
                    SUM(CASE WHEN result='tidak_lolos' THEN 1 ELSE 0 END) AS gagal
                ")
                ->first();

            $rekap[$stage] = [
                'lolos' => (int) ($counts->lolos ?? 0),
                'gagal' => (int) ($counts->gagal ?? 0),
                'route' => $stage,
            ];
        }

        return view('admin.applicant.seleksi.index', compact('rekap'));
    }

    public function editSeleksi($id)
    {
        $applicant = Applicant::with('position')->findOrFail($id);
        return view('admin.applicants.edit-seleksi', compact('applicant'));
    }

    /**
     * Helper tahap berikutnya (enum valid).
     */
    private function nextStageExact(string $stage): string
    {
        $map = [
            'Seleksi Administrasi' => 'Tes Tulis',
            'Tes Tulis'            => 'Technical Test',
            'Technical Test'       => 'Interview',
            'Interview'            => 'Offering',
            'Offering'             => 'Offering', // terminal
        ];
        return $map[$stage] ?? $stage;
    }

    /**
     * Helper enum gagal untuk suatu stage.
     */
    private function failEnumFor(string $stage): string
    {
        $map = [
            'Seleksi Administrasi' => 'Tidak Lolos Seleksi Administrasi',
            'Tes Tulis'            => 'Tidak Lolos Seleksi Tes Tulis',
            'Technical Test'       => 'Tidak Lolos Technical Test',
            'Interview'            => 'Tidak Lolos interview', // sesuai enum (huruf i kecil)
            'Offering'             => 'Menolak Offering',
        ];
        return $map[$stage] ?? $stage;
    }

    /**
     * LIST & FILTER peserta per tahap + tandai status email.
     */
    public function process(Request $request, string $stage)
    {
        $positions  = Position::orderBy('name')->get();
        $allJurusan = Applicant::whereNotNull('jurusan')->distinct()->pluck('jurusan');

        $nextStage = $this->nextStageExact($stage);
        $failEnum  = $this->failEnumFor($stage);

        $q = Applicant::with('position');

        // ---- Filter status (mendukung token __NEXT__ & __FAILED__) ----
        if ($request->filled('status')) {
            $st = $request->status;

            if ($st === '__NEXT__') {
                $q->where(function ($w) use ($stage, $nextStage) {
                    $w->where('status', $nextStage)
                      ->orWhere('status', 'Lolos ' . $stage);
                });
            } elseif ($st === '__FAILED__') {
                $q->where(function ($w) use ($stage, $failEnum) {
                    $w->where('status', $failEnum)
                      ->orWhere('status', 'Tidak Lolos ' . $stage); // fallback legacy
                });
            } else {
                $q->where('status', $st);
            }
        } else {
            // Default: terkait tahap ini
            $q->where(function ($w) use ($stage, $nextStage, $failEnum) {
                $w->where('status', $stage)
                  ->orWhere('status', 'Lolos ' . $stage)
                  ->orWhere('status', $nextStage)
                  ->orWhere('status', $failEnum)
                  ->orWhere('status', 'Tidak Lolos ' . $stage); // fallback legacy
            });
        }

        // ---- Filter pencarian & lainnya ----
        if ($s = $request->search) {
            $q->where(function ($x) use ($s) {
                $x->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('jurusan', 'like', "%{$s}%");
            });
        }
        if ($request->filled('position')) $q->where('position_id', $request->position);
        if ($request->filled('jurusan'))  $q->where('jurusan', $request->jurusan);

        $applicants = $q->orderBy('name')->paginate(20)->appends($request->query());

        // ---- Tandai badge/status tampilan untuk tahap ini ----
        $collection = $applicants->getCollection();
        $collection->transform(function ($a) use ($stage, $nextStage, $failEnum) {
            // Default
            $a->_stage_state  = 'other';
            $a->_stage_status = $a->status;
            $a->_stage_badge  = 'bg-slate-50 text-slate-600 border border-slate-200';

            // Kasus Offering final
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
            } elseif ($a->status === $failEnum || $a->status === 'Tidak Lolos ' . $stage) {
                $a->_stage_state  = 'gagal';
                $a->_stage_status = $a->status;
                $a->_stage_badge  = 'bg-red-50 text-red-700 border border-red-200';
            } elseif ($a->status === 'Lolos ' . $stage || $a->status === $this->nextStageExact($stage)) {
                $a->_stage_state  = 'lolos';
                $a->_stage_status = 'Lolos ' . $stage;
                $a->_stage_badge  = 'bg-green-50 text-green-700 border border-green-200';
            }

            return $a;
        });

        // ---- Tandai status email terkirim untuk stage ini ----
        $ids = $collection->pluck('id')->all();
        $sentMap = EmailLog::whereIn('applicant_id', $ids)
            ->where('stage', $stage)      // HARUS identik dgn stage halaman
            ->where('success', true)
            ->select('applicant_id', DB::raw('MAX(created_at) as last_sent'))
            ->groupBy('applicant_id')
            ->pluck('last_sent', 'applicant_id'); // [id => datetime]

        $collection->transform(function ($a) use ($sentMap) {
            $a->_email_sent    = $sentMap->has($a->id);
            $a->_email_sent_at = $sentMap->get($a->id);
            return $a;
        });

        $applicants->setCollection($collection);

        return view(
            'admin.applicant.seleksi.process',
            compact('applicants', 'positions', 'allJurusan', 'stage', 'nextStage', 'failEnum')
        );
    }

    /**
     * Kirim email + log ke EmailLog.
     */
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

    // Normalisasi daftar email
    $emails = \Illuminate\Support\Str::of(str_replace(["\r\n", "\n", ";"], ",", $request->recipients))
        ->explode(',')
        ->map(fn($e) => trim($e))
        ->filter()
        ->unique()
        ->values();

    // Ambil kandidat by ID + bawa relasi position
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


    /**
     * UPDATE status peserta (patuhi enum).
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'stage'               => 'required|string',
            'selected_applicants' => 'required|array',
            'status'              => 'required|array',  // [id => 'lolos'|'tidak_lolos']
        ]);

        $stage    = (string) $request->stage;
        $stageKey = Str::slug($stage);

        DB::transaction(function () use ($request, $stage, $stageKey) {
            foreach ($request->selected_applicants as $applicantId) {
                $applicant = Applicant::with('position')->findOrFail($applicantId);
                $action    = $request->status[$applicantId] ?? null;

                if ($action === 'lolos') {
                    // Tahap Offering → final menerima
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
                    // Gagal pada tahap ini → enum gagal yang sesuai
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

    /**
     * (Opsional) tampilan lain yang masih dipakai.
     */
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
