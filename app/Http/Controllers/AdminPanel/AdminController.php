<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Position;
use App\Models\Applicant;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Ambil batch aktif (status = Active)
        $currentBatch = Batch::where('status', 'Active')
            ->orderByDesc('start_date')
            ->first();

        // Kalau belum ada batch aktif
        if (!$currentBatch) {
            return view('admin.dashboard', [
                'currentBatch' => null,
                'positionCards' => collect(),
                'quota' => ['applied' => 0, 'limit' => 0, 'percent' => 0],
                'progress' => ['admin' => 0, 'quiz' => 0, 'tech' => 0, 'interview' => 0, 'offering' => 0],
                'chartData' => ['months' => [], 'series' => []],
                'pieData' => ['labels' => [], 'series' => []],
            ]);
        }

        // Posisi dalam batch aktif + jumlah pelamar tiap posisi
        $positions = Position::withCount([
                'applicants as applicants_count' => function ($q) use ($currentBatch) {
                    $q->where('batch_id', $currentBatch->id);
                }
            ])
            ->where('batch_id', $currentBatch->id)
            ->orderBy('name')
            ->get();

        $positionCards = $positions->map(fn ($p) => [
            'name' => $p->name,
            'count' => (int) $p->applicants_count,
        ]);

        // Kuota total vs jumlah pelamar
        $totalQuota = (int) $positions->sum('quota');
        $totalApplicants = (int) Applicant::where('batch_id', $currentBatch->id)->count();
        $percent = $totalQuota > 0 ? round(($totalApplicants / $totalQuota) * 100, 1) : 0;

        $quota = [
            'applied' => $totalApplicants,
            'limit'   => $totalQuota,
            'percent' => $percent,
        ];

        // Progress per tahap seleksi
        $like = fn($frag) => DB::raw("LOWER(status) LIKE '%" . strtolower($frag) . "%'");
        // ambil semua pelamar batch aktif
        $applicants = Applicant::where('batch_id', $currentBatch->id)
            ->select('id', 'status')
            ->get();

        // inisialisasi hitungan per tahap
        $progress = [
            'admin' => 0,
            'quiz' => 0,
            'tech' => 0,
            'interview' => 0,
            'offering' => 0,
        ];

        // loop setiap pelamar
        foreach ($applicants as $a) {
            $status = strtolower($a->status);

            // jika dia pernah sampai tahap tertentu, semua tahap sebelumnya juga dianggap sudah dilalui
            if (str_contains($status, 'offering')) {
                $progress['offering']++;
                $progress['interview']++;
                $progress['tech']++;
                $progress['quiz']++;
                $progress['admin']++;
            } elseif (str_contains($status, 'interview')) {
                $progress['interview']++;
                $progress['tech']++;
                $progress['quiz']++;
                $progress['admin']++;
            } elseif (str_contains($status, 'technical test')) {
                $progress['tech']++;
                $progress['quiz']++;
                $progress['admin']++;
            } elseif (str_contains($status, 'tes tulis')) {
                $progress['quiz']++;
                $progress['admin']++;
            } elseif (str_contains($status, 'administrasi')) {
                $progress['admin']++;
            }
        }

        // =============================
        // 📊 Grafik Batang (6 Bulan Terakhir)
        // =============================
        $startDate = now()->subMonths(5)->startOfMonth();
        $months = collect(range(0, 5))->map(fn($i) => now()->subMonths(5 - $i)->format('M'));

        $monthlyData = Applicant::select(
                DB::raw("TO_CHAR(DATE_TRUNC('month', applicants.created_at), 'Mon') as month"),
                'positions.name as position',
                DB::raw('COUNT(applicants.id) as total')
            )
            ->join('positions', 'positions.id', '=', 'applicants.position_id')
            ->where('applicants.batch_id', $currentBatch->id)
            ->where('applicants.created_at', '>=', $startDate)
            ->groupBy('month', 'positions.name')
            ->orderBy('month')
            ->get();

        // Kelompokkan data per posisi
        $grouped = $monthlyData->groupBy('position');

        // Ambil 3 posisi dengan total pelamar terbanyak selama 6 bulan
        $topPositions = $monthlyData->groupBy('position')
            ->map->sum('total')
            ->sortDesc()
            ->take(3)
            ->keys();

        // Susun data untuk chart
        $series = $topPositions->map(function ($pos) use ($months, $grouped) {
            $monthTotals = $months->map(function ($month) use ($grouped, $pos) {
                return optional($grouped[$pos]?->firstWhere('month', $month))['total'] ?? 0;
            });
            return [
                'name' => $pos,
                'data' => $monthTotals,
            ];
        })->values();

        $chartData = [
            'months' => $months,
            'series' => $series,
        ];

        // =============================
        // 🥧 Grafik Lingkaran (Persentase per Posisi)
        // =============================
        $positions = Position::where('batch_id', $currentBatch->id)
            ->withCount('applicants')
            ->get();

        $totalApplicants = $positions->sum('applicants_count');

        $pieData = [
            'labels' => $positions->pluck('name'),
            'series' => $positions->map(fn($p) =>
                $totalApplicants > 0 ? round(($p->applicants_count / $totalApplicants) * 100, 2) : 0
            ),
        ];

        return view('admin.dashboard', compact(
            'currentBatch',
            'positionCards',
            'quota',
            'progress',
            'chartData',
            'pieData'
        ));
    }
}
