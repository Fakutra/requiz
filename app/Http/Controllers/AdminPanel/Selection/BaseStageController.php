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
        $slug = str_replace('seleksi-', '', $slug);
         $slug = str_replace('-', '_', $slug);  // administrasi, tes-tulis, ...
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

        // ===== Filter batch =====
        // NOTE: Di tempat lain (RekapController) kamu pakai applicants.batch_id.
        // Jika itu yang resmi, lebih konsisten pakai where('batch_id', $batchId) di sini juga.
        if ($batchId) {
            $q->where('batch_id', $batchId);
            // Jika datamu nyambung via position.batch_id, ganti ke whereHas seperti ini:
            // $q->whereHas('position', fn($w) => $w->where('batch_id', $batchId));
        }

        // ===== Filter status =====
        if ($request->filled('status')) {
            $st = $request->status;
            if ($st === '__NEXT__') {
                $q->where('status', $nextStage);
            } elseif ($st === '__FAILED__') {
                $q->where('status', $failEnum);
            } else {
                $q->where('status', $st);
            }
        } else {
            // Tampilkan kandidat yang berada di stage saat ini, next stage, atau gagal di stage ini
            $q->where(function ($w) use ($nextStage, $failEnum) {
                $w->where('status', $this->stage)
                  ->orWhere('status', $nextStage)
                  ->orWhere('status', $failEnum);
            });
        }

        // ===== Search (portable MySQL/Postgres) =====
        if ($s = trim((string) $request->input('search'))) {
            $s = mb_strtolower($s);
            $q->where(function ($x) use ($s) {
                $x->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$s}%"])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', ["%{$s}%"]);
            });
        }

        if ($request->filled('position')) $q->where('position_id', $request->position);
        if ($request->filled('jurusan'))  $q->where('jurusan',      $request->jurusan);

        // Hook khusus tahap
        $this->augmentQuery($q);

        $applicants = $q->orderBy('name')->paginate(20)->appends($request->query());

        // Hook koleksi (mis. tempel skor)
        $this->augmentAfterPaginate($applicants);

        // Badge tampilan (pakai enum yang konsisten)
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
            } elseif ($a->status === $failEnum) {
                $a->_stage_state  = 'gagal';
                $a->_stage_status = $a->status;
                $a->_stage_badge  = 'bg-red-50 text-red-700 border border-red-200';
            } elseif ($a->status === $nextStage || $this->isAcceptedForStage($a->status)) {
                // Jika kamu ingin menandai "lolos" (meski enum tidak menyimpan "Lolos ..."),
                // kita anggap "sudah masuk next stage" == lolos stage ini.
                $a->_stage_state  = 'lolos';
                $a->_stage_status = 'Lolos ' . $this->stage;
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

    /**
     * Disamakan 100% dengan enum di schema applicants.status
     */
    protected function failEnumFor(string $stage): string
    {
        return match ($stage) {
            'Seleksi Administrasi' => 'Tidak Lolos Seleksi Administrasi',
            'Tes Tulis'            => 'Tidak Lolos Tes Tulis',
            'Technical Test'       => 'Tidak Lolos Technical Test',
            'Interview'            => 'Tidak Lolos Interview',
            'Offering'             => 'Menolak Offering',
            default                => $stage,
        };
    }

    /**
     * Penanda "diterima di stage ini" (tidak ada enum 'Lolos <Stage>' di DB-mu,
     * maka kita anggap status next stage = accepted)
     */
    protected function isAcceptedForStage(string $status): bool
    {
        $mapNext = [
            'Seleksi Administrasi' => 'Tes Tulis',
            'Tes Tulis'            => 'Technical Test',
            'Technical Test'       => 'Interview',
            'Interview'            => 'Offering',
            'Offering'             => 'Menerima Offering', // khusus offering
        ];
        $expected = $mapNext[$this->stage] ?? null;
        return $expected ? ($status === $expected) : false;
    }
}
