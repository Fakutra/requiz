<?php

namespace Database\Seeders;

use App\Models\QuestionBundle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuestionBundleSeeder extends Seeder
{
    public function run(): void
    {
        QuestionBundle::truncate();

        $bundles = [
            ['name' => 'PG Umum'],
            ['name' => 'PG Teknis'],
            ['name' => 'Essay Umum'],
            ['name' => 'Essay Teknis'],
            ['name' => 'Psikologi'],
        ];

        foreach ($bundles as $bundle) {
            QuestionBundle::create([
                'name' => $bundle['name'],
                'slug' => Str::slug($bundle['name']),
            ]);
        }
    }
}
