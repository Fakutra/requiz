<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Position;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UserSeeder::class,
            ProfileSeeder::class,
            BatchSeeder::class,
            ApplicantSeeder::class,
            QuestionSeeder::class,
            DivisionSeeder::class,
            JobSeeder::class,
            PlacementSeeder::class,
            QuestionBundleSeeder::class,
            BundleQuestionSeeder::class,
            PersonalityRuleSeeder::class,
            SkregisSeeder::class,
        ]);

    }
}
