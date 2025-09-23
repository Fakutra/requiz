<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\SelectionLog;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RekapController extends Controller
{
    /**
     * Rekap seleksi per tahap + dropdown Batch (filter berdasarkan batch).
     * View yang dipakai: resources/views/admin/applicant/seleksi/index.blade.php
     */
    public function index(Request $request)
    {
        $batches = Batch::orderBy('id')->get();
        $currentBatchId = $request->query('batch') ?: ($batches->first()->id ?? null);

        $totalApplicants = null;
        $rekap = [];

        if (!$currentBatchId) {
            return view('admin.applicant.seleksi.index', compact('batches','currentBatchId','totalApplicants','rekap'));
        }

        // ✅ Pakai applicants.batch_id (seperti versi kamu sebelumnya)
        $applicantIds = Applicant::where('batch_id', $currentBatchId)->pluck('id');
        $totalApplicants = $applicantIds->count();

        // Mapping tahap → nama route halaman tahap
        $stages = [
            'Seleksi Administrasi' => 'admin.applicant.seleksi.administrasi',
            'Tes Tulis'            => 'admin.applicant.seleksi.tes_tulis',
            'Technical Test'       => 'admin.applicant.seleksi.technical_test',
            'Interview'            => 'admin.applicant.seleksi.interview',
            'Offering'             => 'admin.applicant.seleksi.offering',
        ];

        foreach ($stages as $label => $routeName) {
            $stageKey = Str::slug($label);

            // Ambil log TERBARU per applicant untuk stage ini
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
}
