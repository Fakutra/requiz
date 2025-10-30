<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\InterviewSchedule;
use App\Models\Position;
use App\Models\TechnicalTestSchedule;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssessmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $batchIdDefault = Batch::query()->value('id')   // ambil batch pertama
            ?? Batch::create([
                'name' => 'Batch Demo',
                'status' => 'Active',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ])->id;

        // dua posisi contoh – bebas lo ubah
        $positions = [
            ['name' => 'Technical Writer',  'slug' => 'technical-writer'],
            [
                'name' => 'Frontend Engineer',
                'slug' => 'frontend-engineer',
                'batch_id' => 1,
                'quota' => 100,
                'description' => 'Lorem ipsum ada loker baru imnida'
            ],
        ];

        foreach ($positions as $p) {
            // bikin posisi kalau belum ada (minimal name+slug)

            $position = Position::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name'          => $p['name'],
                    'batch_id'      => $p['batch_id'] ?? $batchIdDefault, // <-- WAJIB
                    'quota'         => $p['quota'] ?? 1,  // <-- WAJIB isi (minimal 1)
                    'status'        => 'Active',
                    'description'   => $p['description'] ?? 'Loker baru nich',
                ]
            );

            // ---- tests (hasOne di Position) ----
            Test::updateOrCreate(
                ['position_id' => $position->id], // satu test per posisi
                [
                    'name'        => 'Tes ' . $p['name'],
                    'slug'        => Str::slug('tes ' . $p['slug']),
                    // window tes tulis
                    'test_date'   => Carbon::now()->addDays(1), // buka
                    'test_closed' => Carbon::now()->addDays(3), // tombol tutup
                    'test_end'    => Carbon::now()->addDays(4), // hard end
                ]
            );

            // ---- technical_test_schedules (hasMany) ----
            TechnicalTestSchedule::firstOrCreate(
                [
                    'position_id'   => $position->id,
                    'schedule_date' => Carbon::now()->addDays(5)->startOfHour(),
                ],
                [
                    'zoom_link'      => 'https://zoom.us/j/1111111111',
                    'zoom_id'        => '111-111-1111',
                    'zoom_passcode'  => '12345',
                    'keterangan'     => 'Sesi utama',
                    'upload_deadline' => Carbon::now()->addDays(7)->endOfDay(),
                ]
            );

            // opsional: sesi tambahan biar kelihatan banyak
            TechnicalTestSchedule::firstOrCreate(
                [
                    'position_id'   => $position->id,
                    'schedule_date' => Carbon::now()->addDays(12)->startOfHour(),
                ],
                [
                    'zoom_link'      => 'https://zoom.us/j/2222222222',
                    'zoom_id'        => '222-222-2222',
                    'zoom_passcode'  => 'abcde',
                    'keterangan'     => 'Sesi cadangan',
                    'upload_deadline' => Carbon::now()->addDays(14)->endOfDay(),
                ]
            );

            // ---- interview_schedules (hasMany) ----
            InterviewSchedule::firstOrCreate(
                [
                    'position_id'    => $position->id,
                    'schedule_start' => Carbon::now()->addDays(10)->setTime(10, 0),
                    'schedule_end'   => Carbon::now()->addDays(10)->setTime(11, 0),
                ],
                [
                    'zoom_link'     => 'https://zoom.us/j/3333333333',
                    'zoom_id'       => '333-333-3333',
                    'zoom_passcode' => 'interv',
                    'keterangan'    => 'Interview panel',
                ]
            );
        }
    }
}
