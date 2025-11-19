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
        // === 5 Admins ===
        $admins = [
            ['name' => 'Admin Utama', 'email' => 'admin@iconpln.co.id'],
            ['name' => 'Admin HR',    'email' => 'admin2@iconpln.co.id'],
            ['name' => 'Admin IT',    'email' => 'admin3@iconpln.co.id'],
            ['name' => 'Admin Finance','email' => 'admin4@iconpln.co.id'],
            ['name' => 'Admin Project','email' => 'admin5@iconpln.co.id'],
        ];

        foreach ($admins as $data) {
            User::factory()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]);
        }

        // === 20 Regular Users ===
        // $users = [
        //     ['name' => 'Delanda', 'email' => 'delanda.f@gmail.com'],
        //     ['name' => 'Sela', 'email' => 'marselatrianggraini27@gmail.com'],
        //     ['name' => 'Dina', 'email' => 'dina@gmail.com'],
        //     ['name' => 'Rizky', 'email' => 'rizky@gmail.com'],
        //     ['name' => 'Salsa', 'email' => 'salsa@gmail.com'],
        //     ['name' => 'Ahmad', 'email' => 'ahmad@gmail.com'],
        //     ['name' => 'Budi', 'email' => 'budi@gmail.com'],
        //     ['name' => 'Citra', 'email' => 'citra@gmail.com'],
        //     ['name' => 'Dewi', 'email' => 'dewi@gmail.com'],
        //     ['name' => 'Eka', 'email' => 'eka@gmail.com'],
        //     ['name' => 'Farhan', 'email' => 'farhan@gmail.com'],
        //     ['name' => 'Gina', 'email' => 'gina@gmail.com'],
        //     ['name' => 'Hafiz', 'email' => 'hafiz@gmail.com'],
        //     ['name' => 'Indra', 'email' => 'indra@gmail.com'],
        //     ['name' => 'Jihan', 'email' => 'jihan@gmail.com'],
        //     ['name' => 'Kiki', 'email' => 'kiki@gmail.com'],
        //     ['name' => 'Lina', 'email' => 'lina@gmail.com'],
        //     ['name' => 'Miko', 'email' => 'miko@gmail.com'],
        //     ['name' => 'Nadia', 'email' => 'nadia@gmail.com'],
        //     ['name' => 'Rafi', 'email' => 'rafi@gmail.com'],
        // ];

        // foreach ($users as $data) {
        //     User::factory()->create([
        //         'name' => $data['name'],
        //         'email' => $data['email'],
        //         'role' => 'user',
        //         'password' => bcrypt('password'),
        //     ]);
        // }
    }
}
