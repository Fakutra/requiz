<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendors')->insert([
            [
                'id' => 1,
                'nama_vendor' => 'DPP',
                'nomor_telepon' => '082123456789',
                'alamat' => 'Jl. DPP',
                'email' => 'dpp@dpp.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama_vendor' => 'MUST',
                'nomor_telepon' => '082123456789',
                'alamat' => 'Jl. MUST',
                'email' => 'must@must.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
