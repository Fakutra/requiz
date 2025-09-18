<?php
namespace App\Http\Controllers\AdminPanel\Selection;

use App\Models\TestResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class TesTulisController extends BaseStageController
{
    protected string $stage = 'Tes Tulis';

    protected function augmentAfterPaginate($applicants): void
    {
        $ids = $applicants->pluck('id');

        // 1) Tentukan kolom waktu yang ada
        $timeCol = $this->detectTimeColumn();

        // 2) Tentukan ekspresi kolom skor (single-col atau penjumlahan PG+Essay)
        $scoreExpr = $this->scoreExpression('t1'); // string SQL, mis. "t1.final_score" atau "COALESCE(t1.pg_score,0)+COALESCE(t1.essay_score,0)"

        // 3) Subquery: waktu terakhir per applicant
        $latest = DB::table('test_results as t2')
            ->select('applicant_id', DB::raw("MAX($timeCol) AS max_time"))
            ->whereNotNull($timeCol)
            ->groupBy('applicant_id');

        // 4) Join ke baris terakhir untuk ambil skor yang relevan
        $rows = DB::table('test_results as t1')
            ->joinSub($latest, 'mx', function ($j) use ($timeCol) {
                $j->on('t1.applicant_id', '=', 'mx.applicant_id')
                ->on(DB::raw("t1.$timeCol"), '=', DB::raw('mx.max_time'));
            })
            ->whereIn('t1.applicant_id', $ids)
            ->select('t1.applicant_id', DB::raw("$scoreExpr AS total_score"))
            ->get()
            ->keyBy('applicant_id');

        // 5) Tempel ke koleksi
        $col = $applicants->getCollection();
        $col->transform(function ($a) use ($rows) {
            $a->_quiz_score = optional($rows->get($a->id))->total_score; // bisa null jika tak ada data
            return $a;
        });
        $applicants->setCollection($col);
    }

    /** Pilih kolom waktu yang tersedia di test_results */
    private function detectTimeColumn(): string
    {
        $candidates = [
            'finished_at','completed_at','submitted_at',
            'end_time','ended_at','updated_at','created_at'
        ];
        foreach ($candidates as $c) {
            if (Schema::hasColumn('test_results', $c)) {
                return $c;
            }
        }
        // fallback aman
        return 'created_at';
    }

    /** Bangun ekspresi SQL kolom skor total */
private function scoreExpression(string $prefix = 't1'): string
{
    // Kandidat satu kolom untuk total
    $single = [
        'total_score','final_score','overall_score',
        'score_total','score','nilai_total','nilai','total_nilai'
    ];
    foreach ($single as $col) {
        if (Schema::hasColumn('test_results', $col)) {
            return "$prefix.$col";
        }
    }

    // Jika tidak ada single total, coba jumlahkan PG + Essay
    $pgCols    = ['score_pg','pg_score','nilai_pg'];
    $essayCols = ['score_essay','essay_score','nilai_essay'];

    $pgExpr    = $this->firstExistingColumnExpr($prefix, $pgCols, '0');
    $essayExpr = $this->firstExistingColumnExpr($prefix, $essayCols, '0');

    if ($pgExpr !== '0' || $essayExpr !== '0') {
        return "COALESCE($pgExpr,0) + COALESCE($essayExpr,0)";
    }

    // Kalau benar-benar tidak ada apa-apa, biarkan NULL
    return 'NULL';
}

private function firstExistingColumnExpr(string $prefix, array $cols, string $default = 'NULL'): string
{
    foreach ($cols as $c) {
        if (Schema::hasColumn('test_results', $c)) {
            return "$prefix.$c";
        }
    }
    return $default;
}

}
