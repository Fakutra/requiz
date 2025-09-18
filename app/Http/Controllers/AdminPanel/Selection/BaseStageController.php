<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Position;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class BaseStageController extends Controller
{
    /** Contoh: 'Tes Tulis' / 'Interview' / dst */
    protected string $stage;

    /** Override kalau view path berbeda dari pola default() */
    protected function view(): string
    {
        // default: resources/views/admin/applicant/seleksi/{slug}/index.blade.php
        $slug = Str::slug($this->stage);            // seleksi-administrasi, tes-tulis, ...
        $slug = str_replace('seleksi-', '', $slug); // administrasi, tes-tulis, ...
        return "admin.applicant.seleksi.{$slug}.index";
    }

    /** Bisa dioverride untuk join/with khusus tahap (contoh: Tes Tulis ambil skor) */
    protected function augmentQuery($q): void
    {
        // default: no-op
    }

    /** Bisa dioverride untuk modifikasi koleksi setelah paginate (tambah properti, dll.) */
    protected function augmentAfterPaginate($applicants): void
    {
        // default: no-op
    }

    /** GET /halaman tahap */
    public function index(Request $request)
    {
        $batchId    = $request->integer('batch');
        $positions  = Position::orderBy('name')->get();
        $allJurusan = Applicant::whereNotNull('jurusan')->distinct()->pluck('jurusan');

        $nextStage  = $this->nextStageExact($this->stage);
        $failEnum   = $this->failEnumFor($this->stage);

        $q = Applicant::with('position');

        // Filter batch (posisi punya batch_id, sesuaikan bila batch_id di applicants)
        if ($batchId) {
            $q->whereHas('position', function ($w) use ($batchId) {
                $w->where('batch_id', $batchId);
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $st = $request->status;
            if ($st === '__NEXT__') {
                $q->where(fn($w) => $w->where('status', $nextStage)->orWhere('status', 'Lolos '.$this->stage));
            } elseif ($st === '__FAILED__') {
                $q->where(fn($w) => $w->where('status', $failEnum)->orWhere('status', 'Tidak Lolos '.$this->stage));
            } else {
                $q->where('status', $st);
            }
        } else {
            $q->where(function ($w) use ($nextStage, $failEnum) {
                $w->where('status', $this->stage)
                  ->orWhere('status', $nextStage)
                  ->orWhere('status', $failEnum)
                  ->orWhere('status', 'Lolos '.$this->stage)
                  ->orWhere('status', 'Tidak Lolos '.$this->stage);
            });
        }

        // Search
        if ($s = trim((string) $request->input('search'))) {
            $q->where(function ($x) use ($s) {
                $x->where('name', 'ilike', "%{$s}%")
                  ->orWhere('email', 'ilike', "%{$s}%")
                  ->orWhere('jurusan', 'ilike', "%{$s}%");
            });
        }
        if ($request->filled('position')) $q->where('position_id', $request->position);
        if ($request->filled('jurusan'))  $q->where('jurusan',      $request->jurusan);

        // Hook khusus tahap
        $this->augmentQuery($q);

        $applicants = $q->orderBy('name')->paginate(20)->appends($request->query());

        // Hook koleksi (mis. tempel skor)
        $this->augmentAfterPaginate($applicants);

        // Badge tampilan
        $collection = $applicants->getCollection();
        $collection->transform(function ($a) use ($nextStage, $failEnum) {
            $a->_stage_state  = 'other';
            $a->_stage_status = $a->status;
            $a->_stage_badge  = 'bg-slate-50 text-slate-600 border border-slate-200';

            if ($this->stage === 'Offering') {
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

            if ($a->status === $this->stage) {
                $a->_stage_state  = 'current';
                $a->_stage_status = $this->stage;
                $a->_stage_badge  = 'bg-gray-100 text-gray-800 border border-gray-200';
            } elseif ($a->status === $failEnum || $a->status === 'Tidak Lolos '.$this->stage) {
                $a->_stage_state  = 'gagal';
                $a->_stage_status = $a->status;
                $a->_stage_badge  = 'bg-red-50 text-red-700 border border-red-200';
            } elseif ($a->status === $nextStage || $a->status === 'Lolos '.$this->stage) {
                $a->_stage_state  = 'lolos';
                $a->_stage_status = 'Lolos '.$this->stage;
                $a->_stage_badge  = 'bg-green-50 text-green-700 border border-green-200';
            }
            return $a;
        });

        // Status email (terkirim/tidak)
        $ids = $collection->pluck('id')->all();
        $sentMap = EmailLog::whereIn('applicant_id', $ids)
            ->where('stage', $this->stage)
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

        return view($this->view(), [
            'applicants' => $applicants,
            'positions'  => $positions,
            'allJurusan' => $allJurusan,
            'stage'      => $this->stage,
            'nextStage'  => $nextStage,
            'failEnum'   => $failEnum,
        ]);
    }

    // ===== util mapping =====
    protected function nextStageExact(string $stage): string
    {
        return match ($stage) {
            'Seleksi Administrasi' => 'Tes Tulis',
            'Tes Tulis'            => 'Technical Test',
            'Technical Test'       => 'Interview',
            'Interview'            => 'Offering',
            'Offering'             => 'Offering',
            default                => $stage,
        };
    }

    protected function failEnumFor(string $stage): string
    {
        return match ($stage) {
            'Seleksi Administrasi' => 'Tidak Lolos Seleksi Administrasi',
            'Tes Tulis'            => 'Tidak Lolos Seleksi Tes Tulis',
            'Technical Test'       => 'Tidak Lolos Technical Test',
            'Interview'            => 'Tidak Lolos interview',
            'Offering'             => 'Menolak Offering',
            default                => $stage,
        };
    }
}
