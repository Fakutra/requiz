<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Str;

class ProcessController extends Controller
{
    /**
     * Tampilkan halaman proses untuk sebuah tahap.
     *
     * Parameter:
     *  - $request: ambil query param 'batch'
     *  - $stage: label tahap, mis. "Seleksi Administrasi"
     *  - $view: optional view path (jika ingin pakai folder per-tahap)
     */
    public function index(Request $request, string $stage, ?string $view = null)
    {
        // ambil batch dari querystring (contoh: ?batch=2)
        $batchId  = $request->integer('batch') ?: null;
        $perPage  = 20;

        $nextStage = $this->nextStageExact($stage);
        $failEnum  = $this->failEnumFor($stage);

        $q = Applicant::with('position');

        // ====== FILTER BY BATCH (important) ======
        if ($batchId) {
            // gunakan applicants.batch_id (sesuai schema)
            $q->where('batch_id', $batchId);
        }

        // Tampilkan applicant yang relevan dengan tahap:
        // status = current stage OR status = nextStage OR status = failEnum
        $q->where(function ($w) use ($stage, $nextStage, $failEnum) {
            $w->where('status', $stage)
              ->orWhere('status', $nextStage)
              ->orWhere('status', $failEnum)
              ->orWhere('status', 'Lolos '.$stage)
              ->orWhere('status', 'Tidak Lolos '.$stage);
        });

        // Search text
        if ($s = trim((string) $request->input('search'))) {
            $needle = mb_strtolower($s);
            $q->where(function ($x) use ($needle) {
                $x->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$needle}%"])
                  ->orWhereRaw('LOWER(jurusan) LIKE ?', ["%{$needle}%"]);
            });
        }

        // optional filters: position, jurusan
        if ($request->filled('position')) {
            $q->where('position_id', $request->input('position'));
        }
        if ($request->filled('jurusan')) {
            $q->where('jurusan', $request->input('jurusan'));
        }

        // order & paginate (pastikan append querystring supaya batch tetap)
        $applicants = $q->orderBy('name')->paginate($perPage)->appends($request->query());

        // tentukan view path (bisa dikirim via routes closure)
        if ($view) {
            $viewPath = $view;
        } else {
            $slug = Str::slug($stage);
            $slug = str_replace('seleksi-', '', $slug);
            $viewPath = "admin.applicant.seleksi.{$slug}.index";
        }

        return view($viewPath, [
            'stage'      => $stage,
            'nextStage'  => $nextStage,
            'failEnum'   => $failEnum,
            'applicants' => $applicants,
            'batchId'    => $batchId,
        ]);
    }

    private function nextStageExact(string $stage): string
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

    private function failEnumFor(string $stage): string
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
}
