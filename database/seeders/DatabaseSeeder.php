<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create(
            [
            'name' => 'Admin',
            'email' => 'admin@iconpln.co.id',
            'role' => 'admin',
            ],
        );
        \App\Models\User::factory()->create(
            [
            'name' => 'Wahyu',
            'email' => 'why@gmail.com',
            ],
        );
        \App\Models\User::factory()->create(
            [
            'name' => 'Fatur',
            'email' => 'fatur@gmail.com',
            ],
        );
        \App\Models\Position::create(
            [
            'name' => 'Technical Support',
            'slug' => 'technical-support',
            'quota' => 100,
            'description' => 'Dicari TS',
            ],
        );
        \App\Models\Position::create(
            [
            'name' => 'Technical Writer',
            'slug' => 'technical-writer',
            'quota' => 50,
            'description' => 'Dicari TW',
            ],
        );

        // \App\Models\Applicant::create(
        //     [
        //     'user_id' => '3',
        //     'position_id' => '1',
        //     'name' => 'paps',
        //     'email' => 'paps@gmail.com',
        //     'nik' => '1111444466669999',
        //     'no_telp' => '081209871234',
        //     'tpt_lahir' => 'Jakarta',
        //     'tgl_lahir' => '01/01/2000',
        //     'alamat' => 'Jalan Margonda',
        //     'pendidikan' => 'S1',
        //     'universitas' => 'ITPLN',
            
        //     ],
        // );

    }
}
