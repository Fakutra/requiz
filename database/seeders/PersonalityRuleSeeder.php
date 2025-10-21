<?php

namespace Database\Seeders;

use App\Models\PersonalityRule;
use Illuminate\Database\Seeder;

class PersonalityRuleSeeder extends Seeder
{
    public function run(): void
    {
        PersonalityRule::truncate();

        // Kamu bebas mengubah nanti lewat UI admin
        PersonalityRule::insert([
            ['min_percentage' => 75.00, 'max_percentage' => null,  'score_value' => 35],
            ['min_percentage' => 65.00, 'max_percentage' => 74.99, 'score_value' => 25],
            ['min_percentage' => 50.00, 'max_percentage' => 64.99, 'score_value' => 15],
            ['min_percentage' => 40.00, 'max_percentage' => 49.99, 'score_value' => 10],
        ]);
    }
}
