<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Position;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan seleksi.
     */
    public function index(Request $request)
    {
        try {
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

            // log access
            try {
                $user = Auth::user()?->name ?? 'System';
                $batchLabel  = $batchId ? "Batch ID {$batchId}" : "Semua Batch";
                $searchLabel = $search ? "dengan pencarian '{$search}'" : 'tanpa filter pencarian';

                ActivityLogger::log(
                    'view',
                    'Report',
                    "{$user} mengakses halaman laporan seleksi ({$batchLabel}, {$searchLabel})"
                );
            } catch (\Throwable $e) {
                Log::warning('Gagal mencatat log view Report: ' . $e->getMessage());
            }

            return view('admin.report.index', compact('batches', 'positions', 'batchId', 'search'));

        } catch (\Throwable $e) {
            Log::error('Gagal memuat Report: ' . $e->getMessage());

            return back()
                ->with('error', 'Gagal memuat data laporan. Silakan coba lagi nanti.');
        }
    }

    /**
     * EXPORT LAPORAN SELEKSI KE EXCEL
     */
    public function export(Request $request)
    {
        try {
            $batchId = $request->query('batch');
            $fileName = 'Laporan_Seleksi_' . now()->format('Ymd_His') . '.xlsx';

            return Excel::download(new ReportExport($batchId), $fileName);

        } catch (\Throwable $e) {
            Log::error('Gagal export laporan: ' . $e->getMessage());

            return back()
                ->with('error', 'Gagal mengekspor laporan. Periksa kembali data atau coba beberapa saat lagi.');
        }
    }
}
