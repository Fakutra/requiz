<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sub_fields')->insert([
            [
                // 'id' => 1,
                'field_id' => 1,
                'name' => 'Penyediaan Data dan Support Aplikasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 2,
                'field_id' => 2,
                'name' => 'Sub Bidang A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 3,
                'field_id' => 3,
                'name' => 'Sub Bidang B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
