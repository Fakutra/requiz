<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use Illuminate\Support\Str;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            [
                'name' => 'Frontend Developer',
                'quota' => 5,
                'status' => 'Active',
                'description' => 'Bertanggung jawab membangun antarmuka pengguna aplikasi.',
            ],
            [
                'name' => 'Backend Developer',
                'quota' => 4,
                'status' => 'Active',
                'description' => 'Menangani logika aplikasi dan koneksi ke database.',
            ],
            [
                'name' => 'UI/UX Designer',
                'quota' => 3,
                'status' => 'Active',
                'description' => 'Merancang tampilan dan pengalaman pengguna aplikasi.',
            ],
            [
                'name' => 'Data Analyst',
                'quota' => 2,
                'status' => 'Active',
                'description' => 'Menganalisis data dan memberikan insight untuk pengambilan keputusan.',
            ],
            [
                'name' => 'DevOps Engineer',
                'quota' => 2,
                'status' => 'Active',
                'description' => 'Mengelola infrastruktur dan proses deployment aplikasi.',
            ],
        ];

        foreach ($positions as $pos) {
            Position::create([
                'name' => $pos['name'],
                'slug' => Str::slug($pos['name']),
                'quota' => $pos['quota'],
                'status' => $pos['status'],
                'description' => $pos['description'],
            ]);
        }
    }
}