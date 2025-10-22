<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionBundle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BundleQuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bundle_questions')->truncate();

        // Ambil ID bundle
        $pgUmum      = QuestionBundle::where('slug', 'pg-umum')->first();
        $pgTeknis    = QuestionBundle::where('slug', 'pg-teknis')->first();
        $essayUmum   = QuestionBundle::where('slug', 'essay-umum')->first();
        $essayTeknis = QuestionBundle::where('slug', 'essay-teknis')->first();
        $psikologi   = QuestionBundle::where('slug', 'psikologi')->first();

        // TRACKER ID yang sudah dipakai (agar tidak duplikat)
        $usedIds = collect();

        // PG Umum (50)
        $pgUmumIds = Question::where('type', 'PG')->where('category', 'Umum')
            ->whereNotIn('id', $usedIds)
            ->limit(25)
            ->pluck('id');
        $this->assign($pgUmum->id, $pgUmumIds);
        $usedIds = $usedIds->merge($pgUmumIds);

        // PG Teknis (50)
        $pgTeknisIds = Question::where('type', 'PG')->where('category', 'Teknis')
            ->whereNotIn('id', $usedIds)
            ->limit(25)
            ->pluck('id');
        $this->assign($pgTeknis->id, $pgTeknisIds);
        $usedIds = $usedIds->merge($pgTeknisIds);

        // Essay Umum (50)
        $essayUmumIds = Question::where('type', 'Essay')->where('category', 'Umum')
            ->whereNotIn('id', $usedIds)
            ->limit(10)
            ->pluck('id');
        $this->assign($essayUmum->id, $essayUmumIds);
        $usedIds = $usedIds->merge($essayUmumIds);

        // Essay Teknis (50)
        $essayTeknisIds = Question::where('type', 'Essay')->where('category', 'Teknis')
            ->whereNotIn('id', $usedIds)
            ->limit(10)
            ->pluck('id');
        $this->assign($essayTeknis->id, $essayTeknisIds);
        $usedIds = $usedIds->merge($essayTeknisIds);

        // Psikologi (50)
        $psikologiIds = Question::where('type', 'Poin')
            ->whereNotIn('id', $usedIds)
            ->limit(35)
            ->pluck('id');
        $this->assign($psikologi->id, $psikologiIds);
    }

    private function assign($bundleId, $questionIds)
    {
        foreach ($questionIds as $id) {
            DB::table('bundle_questions')->insert([
                'question_bundle_id' => $bundleId,
                'question_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
