<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Faker\Factory as Faker;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil user yang role-nya 'user'
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            Profile::create([
                'user_id'     => $user->id,
                'identity_num' => $faker->nik(), // generate NIK random
                'phone_number' => $faker->phoneNumber(),
                'birthplace'   => $faker->city(),
                'birthdate'    => $faker->date(),
                'address'      => $faker->address(),
            ]);
        }
    }
}
