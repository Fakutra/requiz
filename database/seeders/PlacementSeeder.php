<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlacementSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('placements')->insert([
            [
                'id' => 1,
                'name' => 'LPC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
