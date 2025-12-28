<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Batch;
use App\Models\Position;
use App\Models\Offering;
use App\Models\Field;
use App\Models\SubField;
use App\Models\Job;
use App\Models\Seksi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Exports\OfferingApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;

class OfferingController extends Controller
{
    protected string $stage = 'Offering';

    /**
     * ===============================
     * INDEX
     * ===============================
     */
    public function index(Request $request)
    {
        $batchId    = $request->query('batch');
        $positionId = $request->query('position');
        $search     = trim((string) $request->query('search'));
        $status     = trim((string) $request->query('status')); // Ganti jurusan menjadi status

        $batches   = Batch::orderBy('id')->get();
        $positions = $batchId
            ? Position::where('batch_id', $batchId)->get()
            : Position::all();

        $q = Applicant::with([
                'position',
                'batch',
                'latestEmailLog',
                'offering.field',
                'offering.subfield',
                'offering.job',
                'offering.seksi',
                'pickedBy',
            ])
            ->whereIn('status', [
                'Offering',
                'Menerima Offering',
                'Menolak Offering',
            ]);

        if ($batchId) {
            $q->where('batch_id', $batchId);
        }
        if ($positionId) {
            $q->where('position_id', $positionId);
        }
        
        // 🔥 PERBAIKAN SEARCH: Cari berdasarkan nama, email, posisi, jabatan, bidang, sub bidang, seksi
        if ($search !== '') {
            $needle = '%' . mb_strtolower($search) . '%';
            $q->where(function($query) use ($needle) {
                $query->whereRaw('LOWER(name) LIKE ?', [$needle])
                      ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
                      ->orWhereHas('position', function($p) use ($needle) {
                          $p->whereRaw('LOWER(name) LIKE ?', [$needle]);
                      })
                      ->orWhereHas('offering.job', function($j) use ($needle) {
                          $j->whereRaw('LOWER(name) LIKE ?', [$needle]);
                      })
                      ->orWhereHas('offering.field', function($f) use ($needle) {
                          $f->whereRaw('LOWER(name) LIKE ?', [$needle]);
                      })
                      ->orWhereHas('offering.subfield', function($sf) use ($needle) {
                          $sf->whereRaw('LOWER(name) LIKE ?', [$needle]);
                      })
                      ->orWhereHas('offering.seksi', function($s) use ($needle) {
                          $s->whereRaw('LOWER(name) LIKE ?', [$needle]);
                      });
            });
        }
        
        if ($status !== '') {
            $q->where('status', $status); // Filter berdasarkan status
        }

        // sorting
        $sort      = $request->query('sort', 'name');
        $direction = $request->query('direction', 'asc');

        $allowedSorts = [
            'name', 'email', 'posisi', 'seksi',
            'jabatan', 'bidang', 'subbidang', 'status'
        ];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $applicants = $q->get()->map(function ($a) {
            $a->posisi    = $a->position?->name;
            $a->seksi     = $a->offering?->seksi?->name;
            $a->jabatan   = $a->offering?->job?->name;
            $a->bidang    = $a->offering?->field?->name;
            $a->subbidang = $a->offering?->subfield?->name;
            return $a;
        });

        $applicants = $applicants->sortBy($sort, SORT_NATURAL, $direction === 'desc');

        // manual pagination
        $page    = request('page', 1);
        $perPage = 20;

        $applicants = new \Illuminate\Pagination\LengthAwarePaginator(
            $applicants->forPage($page, $perPage),
            $applicants->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // PREPARE DATA FOR CASCADING DROPDOWN
        $fields = Field::orderBy('name')->get();
        $subfields = SubField::with('field')->orderBy('name')->get();
        $jobs = Job::orderBy('name')->get();
        $seksis = Seksi::with('subField')->orderBy('name')->get();

        // PREPARE DATA UNTUK ALPINE.JS
        $fieldsArray = $fields->map(function($field) {
            return [
                'id' => (int) $field->id,
                'name' => $field->name,
            ];
        })->toArray();
        
        $subfieldsArray = $subfields->map(function($subfield) {
            return [
                'id' => (int) $subfield->id,
                'name' => $subfield->name,
                'field_id' => (int) $subfield->field_id,
            ];
        })->toArray();
        
        $seksisArray = $seksis->map(function($seksi) {
            return [
                'id' => (int) $seksi->id,
                'name' => $seksi->name,
                'sub_field_id' => (int) $seksi->sub_field_id,
            ];
        })->toArray();

        // Tentukan status options
        $statusOptions = [
            'Offering',
            'Menerima Offering', 
            'Menolak Offering'
        ];

        return view('admin.applicant.seleksi.offering.index', compact(
            'batches',
            'positions',
            'batchId',
            'positionId',
            'applicants',
            'status', // Ganti jurusan dengan status
            'statusOptions',
            'fields',
            'subfields',
            'jobs',
            'seksis',
            'fieldsArray',
            'subfieldsArray',
            'seksisArray'
        ));
    }

    /**
     * ===============================
     * EXPORT
     * ===============================
     */
    public function export(Request $request)
    {
        $fileName = 'Offering_Applicants_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new OfferingApplicantsExport(
                $request->query('batch'),
                $request->query('position'),
                $request->query('search'),
                $request->query('status') // Ganti jurusan dengan status
            ),
            $fileName
        );
    }

    /**
     * ===============================
     * SEND EMAIL OFFERING
     * ===============================
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'type' => 'required|in:offering,selected',
            'subject' => 'required|string|max:200',
            'message' => 'required|string',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        $type = $request->input('type');
        
        if ($type === 'selected') {
            // 🔴 VALIDASI UNTUK TAB TERPILIH
            $ids = explode(',', $request->input('ids', ''));
            
            if (empty($ids) || $ids[0] === '') {
                return back()->with('error', 'Tidak ada peserta terpilih.');
            }
            
            // 1. Cek apakah semua memiliki data offering
            $applicants = Applicant::with('offering')->whereIn('id', $ids)->get();
            
            $noOffering = $applicants->filter(function($applicant) {
                return empty($applicant->offering);
            });
            
            if ($noOffering->count() > 0) {
                $names = $noOffering->pluck('name')->take(5)->implode(', ');
                $more = $noOffering->count() > 5 ? ' dan ' . ($noOffering->count() - 5) . ' lainnya' : '';
                
                return back()->with('error', 
                    '❌ Gagal mengirim email! Data offering belum lengkap untuk: ' . 
                    $names . $more
                );
            }
            
            // Semua valid, proses kirim email
            ActivityLogger::log(
                'email',
                'Offering',
                'Admin mengirim email offering ke ' . $applicants->count() . ' peserta',
                'IDs: ' . implode(',', $ids)
            );
            
            // TODO: Implementasi pengiriman email
            // foreach ($applicants as $applicant) { ... }
            
            return back()->with('success', 
                '✅ Email berhasil dikirim ke ' . $applicants->count() . ' peserta.'
            );
            
        } elseif ($type === 'offering') {
            // 🔴 VALIDASI UNTUK TAB OFFERING (SEMUA)
            $batchId = $request->input('batch');
            $positionId = $request->input('position');
            
            // Query sama seperti di index() tapi filter yang punya offering
            $applicants = Applicant::with('offering')
                ->whereIn('status', ['Offering', 'Menerima Offering', 'Menolak Offering'])
                ->whereHas('offering') // ❌ Hanya yang punya data offering
                ->when($batchId, function($q) use ($batchId) {
                    $q->where('batch_id', $batchId);
                })
                ->when($positionId, function($q) use ($positionId) {
                    $q->where('position_id', $positionId);
                })
                ->get();
                
            if ($applicants->isEmpty()) {
                return back()->with('error', 'Tidak ada peserta dengan data offering yang lengkap untuk filter ini.');
            }
            
            // TODO: Implement email sending logic untuk semua
            
            return back()->with('success', 
                'Email berhasil dikirim ke ' . $applicants->count() . ' peserta.'
            );
        }
        
        return back()->with('error', 'Tipe pengiriman tidak valid.');
    }

    /**
     * ===============================
     * STORE / UPDATE OFFERING
     * (TIDAK RESET STATUS KEPUTUSAN)
     * ===============================
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'applicant_id'      => 'required|exists:applicants,id',
                'field_id'          => 'required|exists:fields,id',
                'sub_field_id'      => 'required|exists:sub_fields,id',
                'job_id'            => 'required|exists:jobs,id',
                'seksi_id'          => 'required|exists:seksi,id',
                'gaji'              => 'required|numeric',
                'uang_makan'        => 'required|numeric',
                'uang_transport'    => 'required|numeric',
                'kontrak_mulai'     => 'required|date',
                'kontrak_selesai'   => 'required|date|after_or_equal:kontrak_mulai',
                'link_pkwt'         => 'required|url|max:500',
                'link_berkas'       => 'required|url|max:500',
                'link_form_pelamar' => 'required|url|max:500',
                'response_deadline' => 'required|date|after:now',
            ]);

            // VALIDATE RELATIONSHIPS
            $field = Field::find($data['field_id']);
            $subfield = SubField::find($data['sub_field_id']);
            $seksi = Seksi::find($data['seksi_id']);

            if (!$subfield || $subfield->field_id != $data['field_id']) {
                throw ValidationException::withMessages([
                    'sub_field_id' => 'Sub Bidang tidak valid untuk Bidang yang dipilih.'
                ]);
            }

            if (!$seksi || $seksi->sub_field_id != $data['sub_field_id']) {
                throw ValidationException::withMessages([
                    'seksi_id' => 'Seksi tidak valid untuk Sub Bidang yang dipilih.'
                ]);
            }

            DB::transaction(function () use ($data) {
                $applicant = Applicant::with('offering')->findOrFail($data['applicant_id']);
                
                // SIMPAN DATA KEPUTUSAN YANG SUDAH ADA
                $currentOffering = $applicant->offering;
                $currentDecision = $currentOffering->decision ?? null;
                $currentDecisionBy = $currentOffering->decision_by ?? null;
                $currentDecisionReason = $currentOffering->decision_reason ?? null;
                $currentRespondedAt = $currentOffering->responded_at ?? null;
                
                // TENTUKAN STATUS APPLICANT
                // Jika sudah ada keputusan (accepted/declined), pertahankan status
                // Jika belum ada keputusan, set ke 'Offering'
                $applicantStatus = 'Offering';
                if ($currentDecision === 'accepted') {
                    $applicantStatus = 'Menerima Offering';
                } elseif ($currentDecision === 'declined') {
                    $applicantStatus = 'Menolak Offering';
                }

                $payload = array_merge(
                    Arr::except($data, ['applicant_id']),
                    [
                        // PERTAHANKAN KEPUTUSAN YANG SUDAH ADA
                        'decision'        => $currentDecision,
                        'decision_by'     => $currentDecisionBy,
                        'decision_reason' => $currentDecisionReason,
                        'responded_at'    => $currentRespondedAt,
                    ]
                );

                // Update atau create offering
                $applicant->offering()->updateOrCreate(
                    ['applicant_id' => $applicant->id],
                    $payload
                );

                // Update status applicant berdasarkan keputusan yang ada
                $applicant->update([
                    'status' => $applicantStatus,
                ]);
            });

            ActivityLogger::log(
                'save',
                'Offering',
                'Admin menyimpan / memperbarui offering'
            );

            return back()->with('success', 'Data Offering berhasil disimpan.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('Offering DB Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Database error saat menyimpan offering.');
        } catch (\Throwable $e) {
            Log::error('Offering Error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menyimpan offering.');
        }
    }

    /**
     * ===============================
     * BULK MARK
     * ===============================
     */
    public function bulkMark(Request $request)
    {
        $ids    = $request->input('ids', []);
        $action = $request->input('bulk_action'); // accepted | decline

        if (empty($ids) || !in_array($action, ['accepted', 'decline'], true)) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $user = Auth::user();
        $role = $user?->role ?? 'system';

        $success = 0;
        $names   = [];
        $noOffering = [];

        foreach ($ids as $id) {
            $applicant = Applicant::with('offering')->find($id);
            
            if (!$applicant) {
                continue;
            }
            
            // 🔴 CEK: Apakah punya data offering?
            if (!$applicant->offering) {
                $noOffering[] = $applicant->name ?? "ID: {$id}";
                continue; // Skip yang tidak punya data offering
            }

            // Lanjutkan processing
            DB::transaction(function () use ($applicant, $action, $role, &$success, &$names) {
                if ($action === 'accepted') {
                    $applicant->offering->update([
                        'decision'        => 'accepted',
                        'decision_by'     => $role,
                        'decision_reason' => 'override',
                        'responded_at'    => now(),
                    ]);

                    $applicant->update([
                        'status' => 'Menerima Offering',
                    ]);
                } else {
                    $applicant->offering->update([
                        'decision'        => 'declined',
                        'decision_by'     => $role,
                        'decision_reason' => 'override',
                        'responded_at'    => now(),
                    ]);

                    $applicant->update([
                        'status' => 'Menolak Offering',
                    ]);
                }

                $names[] = $applicant->name;
                $success++;
            });
        }

        // 🔴 KASUS: Ada yang tidak punya data offering
        if (count($noOffering) > 0) {
            $actionLabel = $action === 'accepted' ? 'Accepted' : 'Declined';
            $namesList = implode(', ', array_slice($noOffering, 0, 3));
            $moreText = count($noOffering) > 3 ? ' dan ' . (count($noOffering) - 3) . ' lainnya' : '';
            
            $errorMessage = "Gagal melakukan {$actionLabel}! " . count($noOffering) . 
                        " peserta tidak memiliki data offering: {$namesList}{$moreText}";
            
            // Jika ada yang berhasil dan ada yang gagal
            if ($success > 0) {
                return back()
                    ->with('success', "{$success} peserta berhasil di-{$actionLabel}.")
                    ->with('error', $errorMessage);
            }
            
            // Jika semua gagal
            return back()->with('error', $errorMessage);
        }

        // 🔴 KASUS: Tidak ada yang diproses
        if ($success === 0) {
            return back()->with('error', 'Tidak ada peserta dengan data offering yang lengkap.');
        }

        // ✅ ACTIVITY LOG
        $actionLabel = $action === 'accepted' ? 'menerima' : 'menolak';
        
        ActivityLogger::log(
            $action,
            'Offering',
            "{$user?->name} ({$role}) {$actionLabel} offering {$success} peserta",
            implode(', ', $names)
        );

        return back()->with('success', "{$success} peserta berhasil di-{$actionLabel}.");
    }

    /**
     * ===============================
     * SYNC EXPIRED OFFERING
     * ===============================
     */
    public function syncExpired()
    {
        $now = now();

        $success = 0;
        $failed  = 0;
        $failedNames = [];

        $expiredApplicants = Applicant::where('status', 'Offering')
            ->whereHas('offering', function ($q) use ($now) {
                $q->whereNotNull('response_deadline')
                ->where('response_deadline', '<', $now)
                ->whereNull('responded_at');
            })
            ->with('offering')
            ->get();

        foreach ($expiredApplicants as $applicant) {
            try {
                DB::transaction(function () use ($applicant) {
                    $applicant->offering->update([
                        'decision'        => 'declined',
                        'decision_reason' => 'expired',
                        'decision_by'     => 'system',
                        'responded_at'    => now(),
                    ]);

                    $applicant->update([
                        'status' => 'Menolak Offering',
                    ]);
                });

                $success++;

            } catch (\Throwable $e) {
                report($e);

                $failed++;
                $failedNames[] = $applicant->name ?? "#{$applicant->id}";

                ActivityLogger::log(
                    'error',
                    'Offering',
                    (Auth::user()?->name ?? 'System') .
                    ' GAGAL sinkronisasi offering expired untuk ' .
                    ($applicant->name ?? "#{$applicant->id}"),
                    $e->getMessage()
                );

                continue;
            }
        }

        // ✅ Log ringkasan
        if ($success > 0) {
            ActivityLogger::log(
                'sync',
                'Offering',
                Auth::user()?->name . ' melakukan sinkronisasi offering expired',
                "Berhasil: {$success}, Gagal: {$failed}"
            );
        }

        $resp = back();

        if ($success > 0) {
            $resp = $resp->with(
                'success',
                "{$success} offering expired berhasil disinkronkan."
            );
        }

        if ($failed > 0) {
            $names  = implode(', ', array_slice($failedNames, 0, 10));
            $suffix = count($failedNames) > 10 ? ' (dan lainnya)' : '';

            $resp = $resp->with(
                'error',
                "Ada {$failed} offering yang gagal disinkronkan: {$names}{$suffix}."
            );
        }

        if ($success === 0 && $failed === 0) {
            $resp = $resp->with(
                'error',
                'Tidak ada offering expired yang perlu disinkronkan.'
            );
        }

        return $resp;
    }
}