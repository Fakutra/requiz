<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Position;
use App\Models\Applicant;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $batchId = $request->query('batch');
        $search  = trim((string) $request->query('search'));

        $batches = Batch::orderBy('id')->get();

        $positions = Position::query()
            ->withCount([
                // Semua pelamar
                'applicants as total_pendaftar',

                // Lolos Administrasi = sudah lanjut ke Tes Tulis atau lebih
                'applicants as lolos_administrasi' => fn($q) => $q->whereIn('status', [
                    'Tes Tulis', 'Technical Test', 'Interview', 'Offering', 'Menerima Offering', 'Menolak Offering'
                ]),

                // Lolos Tes Tulis = lanjut ke Technical Test atau lebih
                'applicants as lolos_tes_tulis' => fn($q) => $q->whereIn('status', [
                    'Technical Test', 'Interview', 'Offering', 'Menerima Offering', 'Menolak Offering'
                ]),

                // Lolos Technical Test = lanjut ke Interview atau lebih
                'applicants as lolos_technical' => fn($q) => $q->whereIn('status', [
                    'Interview', 'Offering', 'Menerima Offering', 'Menolak Offering'
                ]),

                // Lolos Interview = lanjut ke Offering / hasil akhir
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

    public function export(Request $request)
    {
        return Excel::download(new ReportExport($request->query('batch')), 'laporan-seleksi.xlsx');
    }
}
