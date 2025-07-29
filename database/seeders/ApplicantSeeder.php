<?php

namespace Database\Seeders;

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ApplicantSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $users = User::where('role', 'user')->get();
        $positions = Position::with('batch')->get();

        for ($i = 0; $i < 25; $i++) {
            $user = $users->random(); // ambil user (nama & email)
            $position = $positions->random();

            Applicant::create([
                'user_id' => $user->id,
                'batch_id' => $position->batch_id,
                'position_id' => $position->id,
                'name' => $user->name,
                'email' => $user->email,
                'nik' => $faker->numerify('################'), // 16 digit
                'no_telp' => $faker->numerify('08###########'),
                'tpt_lahir' => $faker->city,
                'tgl_lahir' => $faker->date('Y-m-d', '2003-01-01'),
                'alamat' => $faker->address,
                'pendidikan' => $faker->randomElement(['SMA/Sederajat', 'Diploma', 'S1', 'S2', 'S3']),
                'universitas' => $faker->company . ' University',
                'jurusan' => $faker->word,
                'thn_lulus' => $faker->year(),
                'skills' => $faker->word,
                'cv_document' => 'cv-applicant/4NRPoc9Px7yNoI9x890W9lzh0aS9FChn0EfRaYP9.pdf',
                'status' => 'Seleksi Administrasi',
            ]);
        }
    }
}
