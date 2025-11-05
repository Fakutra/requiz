<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Position;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $positions = Position::all();
        $users = User::where('role', 'user')->orderBy('id')->take(20)->get();

        if ($positions->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada data Position. Jalankan BatchSeeder dulu.');
            return;
        }
        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada data User (role=user). Jalankan UserSeeder dulu.');
            return;
        }

        $faker = \Faker\Factory::create('id_ID');
        $now = Carbon::now();
        $rows = [];

        // HANYA nilai yang lolos constraint
        $pendidikanOptions = ['SMA/Sederajat', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

        foreach ($users as $i => $user) {
            $position = $positions[$i % $positions->count()];

            // NIK 16 digit fix
            $nik = preg_replace('/\D/', '', $faker->numerify('################'));
            $nik = str_pad(substr($nik, 0, 16), 16, '0', STR_PAD_RIGHT);

            $rows[] = [
                'user_id'         => $user->id,
                'batch_id'        => $position->batch_id,
                'position_id'     => $position->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'nik'             => $nik,
                'no_telp'         => $faker->phoneNumber(),
                'tpt_lahir'       => $faker->city(),
                'tgl_lahir'       => $faker->date('Y-m-d', '2001-01-01'),
                'alamat'          => $faker->address(),
                'pendidikan'      => $faker->randomElement($pendidikanOptions), // ✅ aman utk CHECK
                'universitas'     => $faker->randomElement([
                    'Universitas Indonesia',
                    'Institut Teknologi Bandung',
                    'Universitas Gadjah Mada',
                    'Universitas Brawijaya',
                    'Universitas Diponegoro',
                ]),
                'jurusan'         => $faker->randomElement([
                    'Informatika', 'Sistem Informasi', 'Teknik Komputer',
                    'Manajemen', 'Statistika', 'Teknik Elektro'
                ]),
                'thn_lulus'       => $faker->numberBetween(2018, 2024),
                'skills'          => $faker->randomElement([
                    'Laravel, MySQL, Git',
                    'Python, Excel, Power BI',
                    'Networking, Mikrotik, Cisco',
                    'UI/UX, Figma, Tailwind',
                    'PHP, REST API, Docker'
                ]),
                'ekspektasi_gaji' => $faker->numberBetween(4_500_000, 8_000_000),
                'cv_document'     => 'cv-applicant/sample.pdf',
                'status'          => 'Seleksi Administrasi',
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        DB::table('applicants')->insert($rows);

        $this->command->info('✅ ' . count($rows) . ' applicant berhasil di-seed berdasarkan user.');
    }
}
