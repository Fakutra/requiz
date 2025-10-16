<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BatchSeeder extends Seeder
{
    public function run()
    {
        // === Daftar Batch ===
        $batches = [
            [
                'name' => 'Batch 1',
                'status' => 'Active',
                'start_date' => '2025-07-01',
                'end_date' => '2025-07-31',
                'positions' => [
                    'Software Engineer',
                    'Data Analyst',
                    'UI/UX Designer',
                    'Network Engineer',
                    'Technical Writer',
                ],
            ],
            [
                'name' => 'Batch 2',
                'status' => 'Active',
                'start_date' => '2025-08-01',
                'end_date' => '2025-08-31',
                'positions' => [
                    'Project Manager',
                    'Quality Assurance',
                    'System Administrator',
                    'Database Administrator',
                    'Cyber Security Specialist',
                ],
            ],
            [
                'name' => 'Batch 3',
                'status' => 'Active',
                'start_date' => '2025-08-01',
                'end_date' => '2025-08-31',
                'positions' => [
                    'Project Manager',
                    'Quality Assurance',
                    'System Administrator',
                    'Database Administrator',
                    'Cyber Security Specialist',
                ],
            ],
        ];

        foreach ($batches as $batchData) {
            // Buat batch
            $batch = Batch::create([
                'name' => $batchData['name'],
                'slug' => Str::slug($batchData['name']),
                'status' => $batchData['status'],
                'start_date' => $batchData['start_date'],
                'end_date' => $batchData['end_date'],
            ]);

            // Buat posisi sesuai daftar pada batch
            foreach ($batchData['positions'] as $name) {
                Position::create([
                    'batch_id' => $batch->id,
                    'name' => $name,
                    'slug' => Str::slug($name . '-' . $batch->id),
                    'quota' => rand(10, 100),
                    'status' => 'Active',
                    'description' => "Lowongan untuk posisi {$name} pada {$batch->name}",
                ]);
            }
        }
    }
}
