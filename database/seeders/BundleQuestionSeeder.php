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

        // Ambil soal untuk masing-masing bundle
        $this->assign($pgUmum->id, Question::where('type', 'PG')->where('category', 'Umum')->limit(5)->pluck('id'));
        $this->assign($pgTeknis->id, Question::where('type', 'PG')->where('category', 'Teknis')->limit(5)->pluck('id'));
        $this->assign($essayUmum->id, Question::where('type', 'Essay')->where('category', 'Umum')->limit(5)->pluck('id'));
        $this->assign($essayTeknis->id, Question::where('type', 'Essay')->where('category', 'Teknis')->limit(5)->pluck('id'));
        $this->assign($psikologi->id, Question::where('type', 'Poin')->limit(5)->pluck('id'));
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
