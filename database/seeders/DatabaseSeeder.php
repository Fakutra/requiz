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
            'quota' => 100,
            'description' => 'Dicari TS',
            ],
        );
        \App\Models\Position::create(
            [
            'name' => 'Technical Writer',
            'quota' => 50,
            'description' => 'Dicari TW',
            ],
        );

    }
}
