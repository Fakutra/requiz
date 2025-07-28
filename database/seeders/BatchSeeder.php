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
                'name' => 'Batch 1 - Juli 2025',
                'status' => 'Active',
                'start_date' => '2025-07-01',
                'end_date' => '2025-07-31',
            ],
            [
                'name' => 'Batch 2 - Agustus 2025',
                'status' => 'Closed',
                'start_date' => '2025-08-01',
                'end_date' => '2025-08-31',
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

            for ($i = 1; $i <= 5; $i++) {
                $positionName = "Posisi {$i} - {$batch->name}";
                Position::create([
                    'batch_id' => $batch->id,
                    'name' => $positionName,
                    'slug' => Str::slug($positionName),
                    'quota' => 10,
                    'status' => 'Active',
                    'description' => 'Deskripsi untuk ' . $positionName,
                ]);
            }
        }
    }
}