<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('seksi')->insert([
            [
                // 'id' => 1,
                'sub_field_id' => 1,
                'name' => 'OALPC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 2,
                'sub_field_id' => 1,
                'name' => 'PLN Aplikasi Korporat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 3,
                'sub_field_id' => 1,
                'name' => 'Operasi Aplikasi Billing PLN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 4,
                'sub_field_id' => 2,
                'name' => 'Operasi Aplikasi Pelaporan Niaga PLN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 6,
                'sub_field_id' => 2,
                'name' => 'Seksi A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 'id' => 5,
                'sub_field_id' => 3,
                'name' => 'PLN Aplikasi Transmisi Distribusi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
