<?php

namespace App\Http\Controllers\AdminPanel\Selection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TesTulisController extends BaseStageController
{
    protected string $stage = 'Tes Tulis';

    

    protected function augmentAfterPaginate($applicants): void
    {
        $ids = $applicants->pluck('id');
        if ($ids->isEmpty()) return;

        $timeCol   = $this->detectTimeColumn();
        $scoreExpr = $this->scoreExpression('t1');

        $latest = DB::table('test_results as t2')
            ->select('applicant_id', DB::raw("MAX($timeCol) AS max_time"))
            ->whereNotNull($timeCol)
            ->groupBy('applicant_id');

        $rows = DB::table('test_results as t1')
            ->joinSub($latest, 'mx', function ($j) use ($timeCol) {
                $j->on('t1.applicant_id', '=', 'mx.applicant_id')
                  ->on(DB::raw("t1.$timeCol"), '=', DB::raw('mx.max_time'));
            })
            ->whereIn('t1.applicant_id', $ids)
            ->select('t1.applicant_id', DB::raw("$scoreExpr AS total_score"))
            ->get()
            ->keyBy('applicant_id');

        $col = $applicants->getCollection();
        $col->transform(function ($a) use ($rows) {
            $a->_quiz_score = optional($rows->get($a->id))->total_score;
            return $a;
        });
        $applicants->setCollection($col);
    }

    private function detectTimeColumn(): string
    {
        foreach (['finished_at','completed_at','submitted_at','end_time','ended_at','updated_at','created_at'] as $c) {
            if (Schema::hasColumn('test_results', $c)) return $c;
        }
        return 'created_at';
    }

    private function scoreExpression(string $prefix = 't1'): string
    {
        foreach (['total_score','final_score','overall_score','score_total','score','nilai_total','nilai','total_nilai'] as $col) {
            if (Schema::hasColumn('test_results', $col)) return "$prefix.$col";
        }
        $pg    = $this->firstExistingColumnExpr($prefix, ['score_pg','pg_score','nilai_pg'], '0');
        $essay = $this->firstExistingColumnExpr($prefix, ['score_essay','essay_score','nilai_essay'], '0');
        if ($pg !== '0' || $essay !== '0') return "COALESCE($pg,0)+COALESCE($essay,0)";
        return 'NULL';
    }

    private function firstExistingColumnExpr(string $prefix, array $cols, string $default = 'NULL'): string
    {
        foreach ($cols as $c) if (Schema::hasColumn('test_results', $c)) return "$prefix.$c";
        return $default;
    }
}
