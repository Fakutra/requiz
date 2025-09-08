<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Storage;
use App\Models\SelectionLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Exports\ApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Position;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $applicants = $this->getFilteredApplicants($request)->orderBy('id','asc')->paginate(10);
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
            return $q->where(function($subQ) use ($search) {
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

        $query->when($status, function ($q, $status) {
            return $q->where('status', $status);
        });

        $query->when($positionId, function ($q, $positionId) {
            return $q->where('position_id', $positionId);
        });

        return $query;
    }

    /**
     * EDIT/UPDATE pelamar (opsional: validasi status dibatasi enum).
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

        return redirect()->route('applicant.index')->with('success', 'Data pelamar berhasil diperbarui.');
    }

    public function destroy(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('applicant.index')->with('success', 'Data pelamar berhasil dihapus.');
    }

    public function destroySeleksi(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('applicant.seleksi.index')->with('success', 'Data pelamar berhasil dihapus.');
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
     * ====== HELPER: Pemetaan tahap berikutnya & gagal sesuai ENUM ======
     */
    private function nextStageExact(string $stage): string
    {
        $map = [
            'Seleksi Administrasi' => 'Tes Tulis',
            'Tes Tulis'            => 'Technical Test',
            'Technical Test'       => 'Interview',
            'Interview'            => 'Offering',
            'Offering'             => 'Offering', // terminal (final di status lain)
        ];
        return $map[$stage] ?? $stage;
    }

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
     * LIST & FILTER peserta per tahap (menggunakan enum valid).
     */
    public function process(Request $request, string $stage)
    {
        $positions  = Position::orderBy('name')->get();
        $allJurusan = Applicant::whereNotNull('jurusan')
                        ->select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');

        $nextStage  = $this->nextStageExact($stage);
        $failEnum   = $this->failEnumFor($stage);

        $q = Applicant::with('position');

        // ===== Filter status tahap ini =====
        if ($request->filled('status')) {
            $st = $request->status;

            if ($st === $stage) {
                // Sedang di tahap ini
                $q->where('status', $stage);

            } elseif ($st === '__NEXT__') {
                // "Lolos tahap ini"
                if ($stage === 'Offering') {
                    // Lolos Offering = Menerima Offering
                    $q->where('status', 'Menerima Offering');
                } else {
                    // Lolos = sudah pindah ke tahap berikutnya
                    $q->where('status', $nextStage);
                }

            } elseif ($st === '__FAILED__') {
                // Gagal tahap ini = enum gagal spesifik
                $q->where('status', $failEnum);

            } else {
                // Fallback enum lain (aman)
                $q->where('status', $st);
            }
        } else {
            // Default: tampilkan semua yang relevan dgn tahap ini
            $q->where(function ($w) use ($stage, $nextStage, $failEnum) {
                if ($stage === 'Offering') {
                    // Offering: tampilkan "sedang Offering", "Menerima Offering", dan "Menolak Offering"
                    $w->whereIn('status', ['Offering', 'Menerima Offering', 'Menolak Offering']);
                } else {
                    // Tahap lain: sedang tahap ini, sudah next (anggap lolos), dan gagal tahap ini
                    $w->where('status', $stage)
                    ->orWhere('status', $nextStage)
                    ->orWhere('status', $failEnum);
                }
            });
        }

        // ===== Filter lain =====
        if ($s = $request->search) {
            $q->where(function($x) use ($s) {
                $x->where('name','like',"%{$s}%")
                ->orWhere('email','like',"%{$s}%")
                ->orWhere('jurusan','like',"%{$s}%");
            });
        }
        if ($request->filled('position')) $q->where('position_id', $request->position);
        if ($request->filled('jurusan'))  $q->where('jurusan', $request->jurusan);

        $applicants = $q->orderBy('name')->paginate(20)->withQueryString();

        // ===== Mapping badge/label untuk kolom "Status Seleksi" + auto select email =====
        $applicants->setCollection(
            $applicants->getCollection()->transform(function ($a) use ($stage, $nextStage, $failEnum) {
                if ($a->status === $stage) {
                    $a->_stage_state  = 'current';
                    $a->_stage_status = $stage;
                    $a->_stage_badge  = 'bg-gray-100 text-gray-800 border border-gray-200';

                } elseif ($a->status === $failEnum || ($stage === 'Offering' && $a->status === 'Menolak Offering')) {
                    // ❌ Gagal (termasuk Menolak Offering)
                    $a->_stage_state  = 'gagal';
                    $a->_stage_status = $stage === 'Offering' ? 'Menolak Offering' : $failEnum;
                    $a->_stage_badge  = 'bg-red-50 text-red-700 border border-red-200';

                } elseif (
                    // ✅ Lolos
                    ($stage !== 'Offering' && $a->status === $nextStage) ||
                    ($stage === 'Offering' && $a->status === 'Menerima Offering')
                ) {
                    $a->_stage_state  = 'lolos';
                    $a->_stage_status = $stage === 'Offering' ? 'Menerima Offering' : "Lolos {$stage}";
                    $a->_stage_badge  = 'bg-green-50 text-green-700 border border-green-200';

                } else {
                    // Status lain (biarkan apa adanya)
                    $a->_stage_state  = 'other';
                    $a->_stage_status = $a->status;
                    $a->_stage_badge  = 'bg-slate-50 text-slate-600 border border-slate-200';
                }
                return $a;
            })
        );

        return view('admin.applicant.seleksi.process', compact(
            'applicants','positions','allJurusan','stage','nextStage','failEnum'
        ));
    }


    /**
     * Kirim email (tetap).
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

        $rawEmails = str_replace(["\r\n", "\n", ";"], ",", $request->recipients);
        $emails = collect(explode(',', $rawEmails))
            ->map(fn($e) => trim($e))
            ->filter()
            ->unique()
            ->values();

        $ids = collect(explode(',', $request->recipient_ids))
            ->map(fn($v) => trim($v))
            ->filter()
            ->map(fn($v) => (int) $v)
            ->values();

        $applicants = \App\Models\Applicant::whereIn('id', $ids)->get();

        if ($emails->isEmpty() || $applicants->isEmpty()) {
            return back()->withErrors(['recipients' => 'Penerima tidak valid/ kosong.'])->withInput();
        }

        $file   = $request->file('attachment');
        $failed = [];
        $total  = $emails->count();

        foreach ($emails as $to) {
            $fullName = optional($applicants->firstWhere('email', $to))->name ?? $to;

            if ($useTemplate) {
                ['subject' => $subject, 'message' => $body] = $this->defaultEmailForStage($request->stage, $fullName);
            } else {
                $subject = $request->input('subject', 'Informasi Seleksi');
                $body    = $request->input('message', '');
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
            } catch (\Throwable $e) {
                $failed[] = $to;
                Log::error('Gagal kirim email', ['to' => $to, 'error' => $e->getMessage()]);
            }
        }

        $sent = $total - count($failed);
        if ($sent === 0) {
            return back()->with('error', 'Semua pengiriman gagal. Cek konfigurasi email & log aplikasi.');
        }

        $msg = "Email terkirim ke {$sent} dari {$total} penerima.";
        if (!empty($failed)) $msg .= ' Gagal: ' . implode(', ', $failed);

        return back()->with('success', $msg);
    }

    private function defaultEmailForStage(string $stage, string $fullName): array
    {
        $subject = "Pemberitahuan Hasil {$stage}";
        $body = "Kepada {$fullName}

Selamat anda lolos pada tahap {$stage}. Silahkan cek jadwal anda di file yang telah dikirimkan.

Demikian
Admin ReQuiz";
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
            'status'              => 'required|array', // [id => 'lolos'|'tidak_lolos']
        ]);

        $stage    = (string) $request->stage;
        $stageKey = Str::slug($stage);

        DB::transaction(function () use ($request, $stage, $stageKey) {
            foreach ($request->selected_applicants as $applicantId) {
                $applicant = Applicant::with('position')->findOrFail($applicantId);
                $action    = $request->status[$applicantId] ?? null;

                if ($action === 'lolos') {
                    if ($stage === 'Offering') {
                        // Lolos Offering = Menerima Offering (final)
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
            'applicants','stage','allJurusan','filteredStatuses','nextStage','failEnum'
        ));
    }
}
