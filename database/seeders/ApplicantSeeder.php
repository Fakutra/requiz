<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Position;
use App\Models\Profile;
use Faker\Factory as Faker;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $positions = Position::all();
        $users     = User::where('role', 'user')->orderBy('id')->take(20)->get();

        if ($positions->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada data Position. Seed Batch/Position dulu.');
            return;
        }
        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada data User (role=user). Seed User dulu.');
            return;
        }

        $faker = Faker::create('id_ID');
        $now   = Carbon::now();
        $rows  = [];

        $pendidikanOptions = ['SMA/Sederajat', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
        $universitasOpts   = [
            'Universitas Indonesia',
            'Institut Teknologi Bandung',
            'Universitas Gadjah Mada',
            'Universitas Brawijaya',
            'Universitas Diponegoro',
        ];
        $jurusanOpts       = [
            'Informatika','Sistem Informasi','Teknik Komputer',
            'Manajemen','Statistika','Teknik Elektro'
        ];
        $skillsOpts        = [
            'Laravel, MySQL, Git',
            'Python, Excel, Power BI',
            'Networking, Mikrotik, Cisco',
            'UI/UX, Figma, Tailwind',
            'PHP, REST API, Docker',
        ];

        foreach ($users as $i => $user) {
            // pastikan profile exist
            $profile = $user->profile;
            if (!$profile) {
                $profile = Profile::create([
                    'user_id'      => $user->id,
                    'identity_num' => $faker->nik(),
                    'phone_number' => $faker->phoneNumber(),
                    'birthplace'   => $faker->city(),
                    'birthdate'    => $faker->date(),
                    'address'      => $faker->address(),
                ]);
            }

            // sanitasi & normalisasi data profile -> sesuai constraint applicants
            // NIK 16 digit
            $nik = preg_replace('/\D/', '', (string)($profile->identity_num ?? ''));
            if (strlen($nik) < 16) {
                $nik = str_pad($nik, 16, '0');
            } else {
                $nik = substr($nik, 16 * -1); // ambil 16 digit terakhir kalau kepanjangan
            }

            // phone max 15 char numeric (schema kamu 15)
            $phone = preg_replace('/\D/', '', (string)($profile->phone_number ?? ''));
            if ($phone === '') $phone = '08' . $faker->numerify('##########');
            $phone = substr($phone, 0, 15);

            // birthdate Y-m-d
            $birthdate = $profile->birthdate
                ? Carbon::parse($profile->birthdate)->toDateString()
                : $faker->date();

            // pilih posisi berputar
            $position = $positions[$i % $positions->count()];

            $rows[] = [
                'user_id'        => $user->id,
                'batch_id'       => $position->batch_id,
                'position_id'    => $position->id,

                // ambil dari user & profile
                'name'           => $user->name,
                'email'          => $user->email,
                'identity_num'   => $nik,
                'phone_number'   => $phone,
                'birthplace'     => (string)($profile->birthplace ?? $faker->city()),
                'birthdate'      => $birthdate,
                'address'        => (string)($profile->address ?? $faker->address()),

                // akademik & lain-lain
                'pendidikan'     => $faker->randomElement($pendidikanOptions),
                'universitas'    => $faker->randomElement($universitasOpts),
                'jurusan'        => $faker->randomElement($jurusanOpts),
                'thn_lulus'      => (string)$faker->numberBetween(2018, 2024), // schema string
                'skills'         => $faker->randomElement($skillsOpts),
                'ekspektasi_gaji'=> $faker->numberBetween(4_500_000, 8_000_000),

                // file dummy (sesuai schema string, non-null)
                'cv_document'    => "cv-applicant/{$user->id}/sample.pdf",
                'doc_tambahan'   => "doc-applicant/{$user->id}/sample.pdf",

                'status'         => 'Seleksi Administrasi',
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        DB::table('applicants')->insert($rows);

        $this->command->info('✅ ' . count($rows) . ' applicant berhasil di-seed (data diri diambil dari user + profile).');
    }
}
