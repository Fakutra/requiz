<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@iconpln.co.id',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Admin2',
            'email' => 'admin2@iconpln.co.id',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // 5 User dummy
        $users = [
            ['name' => 'Wahyu',  'email' => 'why@gmail.com'],
            ['name' => 'Fatur',  'email' => 'fatur@gmail.com'],
            ['name' => 'Dina',   'email' => 'dina@gmail.com'],
            ['name' => 'Rizky',  'email' => 'rizky@gmail.com'],
            ['name' => 'Salsa',  'email' => 'salsa@gmail.com'],
        ];

        foreach ($users as $data) {
            User::factory()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'user',
                'password' => bcrypt('password'),
            ]);
        }
    }
}
