<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Applicant;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Str;

class ApplicantSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'user')->get();
        $positions = Position::all();

        if ($users->isEmpty() || $positions->isEmpty()) {
            return;
        }

        $total = 20;
        for ($i = 0; $i < $total; $i++) {
            $user = $users[$i % $users->count()]; // looping dari user yang ada
            $position = $positions->random();

            Applicant::create([
                'user_id' => $user->id,
                'position_id' => $position->id,
                'name' => $user->name,
                'email' => $user->email,
                'nik' => fake()->numerify('################'),
                'no_telp' => fake()->phoneNumber(),
                'tpt_lahir' => fake()->city(),
                'tgl_lahir' => fake()->date('Y-m-d', '2003-12-31'),
                'alamat' => fake()->address(),
                'pendidikan' => fake()->randomElement(['SMA/Sederajat', 'Diploma', 'S1', 'S2']),
                'universitas' => fake()->company() . ' University',
                'jurusan' => fake()->jobTitle(),
                'cv_document' => 'cv-applicant/4NRPoc9Px7yNoI9x890W9lzh0aS9FChn0EfRaYP9.pdf',
                'status' => 'Seleksi Administrasi',
            ]);
        }
    }
}



