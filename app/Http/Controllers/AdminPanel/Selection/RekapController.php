<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Applicant;
use App\Models\SelectionLog;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $batches = Batch::orderBy('id')->get();
        $currentBatchId = $request->query('batch') ?: ($batches->first()->id ?? null);

        if (!$currentBatchId) {
            return view('admin.applicant.seleksi.index', compact('batches','currentBatchId'));
        }

        // Total pelamar = SEMUA applicant di batch (tetap)
        $totalApplicants = Applicant::where('batch_id', $currentBatchId)->count();

        // =======================
        // Seleksi Administrasi
        // =======================
        $admin_expected_statuses = [
            // semua yang pernah/masuk admin = total pelamar
            // expected = $totalApplicants (langsung)
        ];
        $admin_lolos_statuses = [
            // sudah lolos admin artinya sudah ke tahap berikutnya
            'Tes Tulis','Technical Test','Interview','Offering','Menerima Offering',
            // atau gagal di tahap setelahnya
            'Tidak Lolos Tes Tulis','Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
        ];
        $admin_gagal_statuses = ['Tidak Lolos Seleksi Administrasi'];

        $administrasi = [
            'expected'  => $totalApplicants,
            'processed' => $this->processedCount($currentBatchId, ['seleksi-administrasi','administrasi']),
            'lolos'     => $this->countByStatuses($currentBatchId, $admin_lolos_statuses),
            'gagal'     => $this->countByStatuses($currentBatchId, $admin_gagal_statuses),
        ];
        
        // Tahap + variasi stage_key + route tujuan
        $stages = [
            ['label'=>'Seleksi Administrasi','keys'=>['seleksi-administrasi','administrasi'],'route'=>'admin.applicant.seleksi.administrasi.index'],
            ['label'=>'Tes Tulis','keys'=>['tes-tulis','test-tulis','tulis'],'route'=>'admin.applicant.seleksi.tes_tulis.index'],
            ['label'=>'Technical Test','keys'=>['technical-test','technical'],'route'=>'admin.applicant.seleksi.technical_test.index'],
            ['label'=>'Interview','keys'=>['interview','wawancara'],'route'=>'admin.applicant.seleksi.interview.index'],
            ['label'=>'Offering','keys'=>['offering','offer'],'route'=>'admin.applicant.seleksi.offering.index'],
        ];

        // =======================
        // Tes Tulis
        // =======================
        $tulis_expected_statuses = [
            'Tes Tulis','Technical Test','Interview','Offering','Menerima Offering',
            'Tidak Lolos Tes Tulis','Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
        ];
        $tulis_lolos_statuses = [
            // sudah lolos tes tulis => minimal masuk Technical Test atau lebih
            'Technical Test','Interview','Offering','Menerima Offering',
            // atau gagal di tahap setelahnya
            'Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
        ];
        $tulis_gagal_statuses = ['Tidak Lolos Tes Tulis'];

        $tesTulis = [
            'expected'  => $this->countByStatuses($currentBatchId, $tulis_expected_statuses),
            'processed' => $this->processedCount($currentBatchId, ['tes-tulis','test-tulis','tulis']),
            'lolos'     => $this->countByStatuses($currentBatchId, $tulis_lolos_statuses),
            'gagal'     => $this->countByStatuses($currentBatchId, $tulis_gagal_statuses),
        ];

        // =======================
        // Technical Test
        // =======================
        $tech_expected_statuses = [
            'Technical Test','Interview','Offering','Menerima Offering',
            'Tidak Lolos Technical Test','Tidak Lolos Interview','Menolak Offering',
        ];
        $tech_lolos_statuses = [
            'Interview','Offering','Menerima Offering',
            // atau gagal di tahap setelahnya
            'Tidak Lolos Interview','Menolak Offering',
        ];
        $tech_gagal_statuses = ['Tidak Lolos Technical Test'];

        $technical = [
            'expected'  => $this->countByStatuses($currentBatchId, $tech_expected_statuses),
            'processed' => $this->processedCount($currentBatchId, ['technical-test','technical']),
            'lolos'     => $this->countByStatuses($currentBatchId, $tech_lolos_statuses),
            'gagal'     => $this->countByStatuses($currentBatchId, $tech_gagal_statuses),
        ];

        // =======================
        // Interview
        // =======================
        $iv_expected_statuses = [
            'Interview','Offering','Menerima Offering','Tidak Lolos Interview','Menolak Offering',
        ];
        $iv_lolos_statuses = [
            'Offering','Menerima Offering','Menolak Offering',
        ];
        $iv_gagal_statuses = ['Tidak Lolos Interview'];

        $interview = [
            'expected'  => $this->countByStatuses($currentBatchId, $iv_expected_statuses),
            'processed' => $this->processedCount($currentBatchId, ['interview','wawancara']),
            'lolos'     => $this->countByStatuses($currentBatchId, $iv_lolos_statuses),
            'gagal'     => $this->countByStatuses($currentBatchId, $iv_gagal_statuses),
        ];

        // =======================
        // Offering
        // =======================
        $off_expected_statuses = ['Offering','Menerima Offering','Menolak Offering'];
        $off_lolos_statuses    = ['Menerima Offering'];
        $off_gagal_statuses    = ['Menolak Offering'];

        $offering = [
            'expected'  => $this->countByStatuses($currentBatchId, $off_expected_statuses),
            'processed' => $this->processedCount($currentBatchId, ['offering','offer']),
            'lolos'     => $this->countByStatuses($currentBatchId, $off_lolos_statuses),
            'gagal'     => $this->countByStatuses($currentBatchId, $off_gagal_statuses),
        ];

        return view('admin.applicant.seleksi.index', compact(
            'batches','currentBatchId','totalApplicants',
            'administrasi','tesTulis','technical','interview','offering'
        ));
    }

    /**
     * Hitung jumlah applicant di batch dengan status termasuk dalam array $statuses.
     */
    private function countByStatuses(int|string $batchId, array $statuses): int
    {
        if (empty($statuses)) return 0;
        return Applicant::where('batch_id', $batchId)
            ->whereIn('status', $statuses)
            ->count();
    }

    /**
     * Hitung peserta yang sudah "diproses" pada suatu stage dari selection_logs (unik per applicant).
     */
    private function processedCount(int|string $batchId, array $stageKeys): int
    {
        if (empty($stageKeys)) return 0;

        return SelectionLog::whereIn('stage_key', $stageKeys)
            ->whereIn('applicant_id', function ($q) use ($batchId) {
                $q->select('id')->from('applicants')->where('batch_id', $batchId);
            })
            ->distinct()
            ->count('applicant_id');
    }
}
