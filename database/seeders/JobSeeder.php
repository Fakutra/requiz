<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jobs')->insert([
            [
                'id' => 1,
                'name' => 'Junior Technical Support',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Junior Technical Writer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
