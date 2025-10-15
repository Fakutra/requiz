<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Position;
use App\Models\Applicant;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use App\Services\ActivityLogger; // ✅ tambahkan
use Illuminate\Support\Facades\Auth; // ✅ tambahkan
use Illuminate\Support\Facades\Log;  // ✅ tambahkan

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $batchId = $request->query('batch');
        $search  = trim((string) $request->query('search'));

        $batches = Batch::orderBy('id')->get();

        $positions = Position::query()
            ->withCount([
                'applicants as total_pendaftar',
                'applicants as lolos_administrasi' => fn($q) => $q->whereIn('status', [
                    'Tes Tulis', 'Technical Test', 'Interview', 'Offering', 'Menerima Offering', 'Menolak Offering'
                ]),
                'applicants as lolos_tes_tulis' => fn($q) => $q->whereIn('status', [
                    'Technical Test', 'Interview', 'Offering', 'Menerima Offering', 'Menolak Offering'
                ]),
                'applicants as lolos_technical' => fn($q) => $q->whereIn('status', [
                    'Interview', 'Offering', 'Menerima Offering', 'Menolak Offering'
                ]),
                'applicants as lolos_interview' => fn($q) => $q->whereIn('status', [
                    'Offering', 'Menerima Offering', 'Menolak Offering'
                ]),
            ])
            ->when($batchId, fn($q) => $q->where('batch_id', $batchId))
            ->when($search !== '', fn($q) => $q->where('name', 'ilike', "%$search%"))
            ->orderBy('batch_id')
            ->get();

        return view('admin.report.index', compact('batches', 'positions', 'batchId', 'search'));
    }

    /**
     * EXPORT LAPORAN SELEKSI
     */
    public function export(Request $request)
    {
        $batchId = $request->query('batch');
        $fileName = 'Laporan_Seleksi_' . now()->format('Ymd_His') . '.xlsx';

        try {
            // ✅ Catat log aktivitas export
            $user = Auth::user()?->name ?? 'System';
            $batchLabel = $batchId ? "Batch ID {$batchId}" : "Semua Batch";

            ActivityLogger::log(
                'export',
                'Report',
                "{$user} mengekspor laporan seleksi ({$batchLabel})"
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log export Report: ' . $e->getMessage());
        }

        return Excel::download(new ReportExport($batchId), $fileName);
    }
}
