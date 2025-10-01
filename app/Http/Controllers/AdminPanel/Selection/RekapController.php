<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Applicant;
use App\Models\SelectionLog;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    /**
     * Tampilkan rekap seleksi per batch (gabungan: expected + processed).
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

        // Ambil seluruh applicant ID di batch ini (dipakai untuk query processed)
        $applicantIds = Applicant::where('batch_id', $currentBatchId)->pluck('id');
        $totalApplicants = $applicantIds->count();

        // Tahap + variasi stage_key + route tujuan
        $stages = [
            ['label'=>'Seleksi Administrasi','keys'=>['seleksi-administrasi','administrasi'],'route'=>'admin.applicant.seleksi.administrasi.index'],
            ['label'=>'Tes Tulis','keys'=>['tes-tulis','test-tulis','tulis'],'route'=>'admin.applicant.seleksi.tes_tulis.index'],
            ['label'=>'Technical Test','keys'=>['technical-test','technical'],'route'=>'admin.applicant.seleksi.technical_test'],
            ['label'=>'Interview','keys'=>['interview','wawancara'],'route'=>'admin.applicant.seleksi.interview'],
            ['label'=>'Offering','keys'=>['offering','offer'],'route'=>'admin.applicant.seleksi.offering'],
        ];


        foreach ($stages as $s) {
            $stageLabel = $s['label'];
            $keys = $s['keys'];

            // --- PROCESSED: hitung dari selection_logs (unique applicant_id yang punya log untuk stage ini) ---
            $processed = 0;
            if ($applicantIds->isNotEmpty()) {
                $processed = SelectionLog::whereIn('stage_key', $keys)
                    ->whereIn('applicant_id', $applicantIds)
                    ->distinct()
                    ->count('applicant_id');
            }

            // --- LOLos / Gagal : hitung berdasarkan log TERBARU per applicant (joinSub pattern) ---
            if ($applicantIds->isNotEmpty()) {
                $latest = SelectionLog::whereIn('stage_key', $keys)
                    ->whereIn('applicant_id', $applicantIds)
                    ->select('applicant_id', DB::raw('MAX(created_at) AS max_ts'))
                    ->groupBy('applicant_id');

                $counts = SelectionLog::joinSub($latest, 'mx', function ($j) {
                        $j->on('selection_logs.applicant_id','=','mx.applicant_id')
                          ->on('selection_logs.created_at','=','mx.max_ts');
                    })
                    ->selectRaw("
                        SUM(CASE WHEN selection_logs.result='lolos' THEN 1 ELSE 0 END) AS lolos,
                        SUM(CASE WHEN selection_logs.result='tidak_lolos' THEN 1 ELSE 0 END) AS gagal
                    ")
                    ->first();
                $lolos = (int) ($counts->lolos ?? 0);
                $gagal = (int) ($counts->gagal ?? 0);
            } else {
                $lolos = 0;
                $gagal = 0;
            }

            // --- EXPECTED: hitung dari applicants.status (berapa yang perlu diproses sekarang) ---
            $nextLabel = $this->nextStatusForLabel($stageLabel);
            $failLabel = $this->failStatusForLabel($stageLabel);

            $expected = Applicant::where('batch_id', $currentBatchId)
                ->where(function($q) use ($stageLabel, $nextLabel, $failLabel) {
                    $q->where('status', $stageLabel)
                      ->orWhere('status', $nextLabel)
                      ->orWhere('status', $failLabel);
                })->count();

            $rekap[] = [
                'label'                 => $stageLabel,
                'participants_expected' => (int) $expected,
                'participants_processed'=> (int) $processed,
                'lolos'                 => $lolos,
                'gagal'                 => $gagal,
                'route_name'            => $s['route'],
            ];
        }

        return view('admin.applicant.seleksi.index', compact(
            'batches','currentBatchId','totalApplicants','rekap'
        ));
    }

    // ===== helper mapping =====
    private function nextStatusForLabel(string $label): string
    {
        return match ($label) {
            'Seleksi Administrasi' => 'Tes Tulis',
            'Tes Tulis'            => 'Technical Test',
            'Technical Test'       => 'Interview',
            'Interview'            => 'Offering',
            'Offering'             => 'Menerima Offering',
            default                => $label,
        };
    }

    private function failStatusForLabel(string $label): string
    {
        return match ($label) {
            'Seleksi Administrasi' => 'Tidak Lolos Seleksi Administrasi',
            'Tes Tulis'            => 'Tidak Lolos Tes Tulis',
            'Technical Test'       => 'Tidak Lolos Technical Test',
            'Interview'            => 'Tidak Lolos Interview',
            'Offering'             => 'Menolak Offering',
            default                => $label,
        };
    }
}
