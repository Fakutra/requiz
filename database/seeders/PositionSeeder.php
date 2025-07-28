<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\Position;
use Illuminate\Support\Str;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $batches = Batch::all();

        foreach ($batches as $batch) {
            for ($i = 1; $i <= 5; $i++) {
                $positionName = "Posisi {$i} - {$batch->name}";

                Position::create([
                    'batch_id' => $batch->id,
                    'name' => $positionName,
                    'slug' => Str::slug($positionName),
                    'quota' => rand(5, 20),
                    'status' => 'Active',
                    'description' => 'Deskripsi untuk ' . $positionName, // ⬅️ tambahkan ini
                ]);
            }
        }
    }
}