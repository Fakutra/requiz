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
        $batches = [
            [
                'name' => 'Batch 1',
                'status' => 'Active',
                'start_date' => '2025-07-01',
                'end_date' => '2025-07-31',
            ],
            [
                'name' => 'Batch 2',
                'status' => 'Active',
                'start_date' => '2025-08-01',
                'end_date' => '2025-08-31',
            ],
        ];

        $positions = [
            [
                'batch_id' => 1,
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'quota' => 100,
                'status' => 'Active',
                'description' => 'Dicari TS',
            ],
            [
                'batch_id' => 1,
                'name' => 'Technical Writer',
                'slug' => 'technical-writer',
                'quota' => 50,
                'status' => 'Active',
                'description' => 'Dicari TW',
            ],
            [
                'batch_id' => 1,
                'name' => 'Database Administrator',
                'slug' => 'database-administrator',
                'quota' => 50,
                'status' => 'Active',
                'description' => 'Dicari DBA',
            ],
            [
                'batch_id' => 2,
                'name' => 'Human Resources',
                'slug' => 'human-resources',
                'quota' => 20,
                'status' => 'Active',
                'description' => 'Dicari HR',
            ],
            [
                'batch_id' => 2,
                'name' => 'Software Engineer',
                'slug' => 'software-engineer',
                'quota' => 30,
                'status' => 'Active',
                'description' => 'Dicari SE',
            ],
            [
                'batch_id' => 2,
                'name' => 'Data Analyst',
                'slug' => 'data-analyst',
                'quota' => 20,
                'status' => 'Active',
                'description' => 'Dicari DA',
            ],
        ];

        foreach ($batches as $batchData) {
            $batch = Batch::create([
                'name' => $batchData['name'],
                'slug' => Str::slug($batchData['name']),
                'status' => $batchData['status'],
                'start_date' => $batchData['start_date'],
                'end_date' => $batchData['end_date'],
            ]);

            // for ($i = 1; $i <= 5; $i++) {
            //     $positionName = "Posisi {$i} - {$batch->name}";
            //     Position::create([
            //         'batch_id' => $batch->id,
            //         'name' => $positionName,
            //         'slug' => Str::slug($positionName),
            //         'quota' => 10,
            //         'status' => 'Active',
            //         'description' => 'Deskripsi untuk ' . $positionName,
            //     ]);
            // }
        }

        foreach ($positions as $positionData) {
            $position = Position::create([
                'batch_id' => $positionData['batch_id'],
                'name' => $positionData['name'],
                'slug' => Str::slug($positionData['name']),
                'status' => $positionData['status'],
                'quota' => $positionData['quota'],
                'description' => $positionData['description'],
            ]);
        }
    }
}