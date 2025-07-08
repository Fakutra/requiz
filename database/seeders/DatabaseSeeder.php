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

        $this->call([
            UserSeeder::class,
            PositionSeeder::class,
            ApplicantSeeder::class,
        ]);

    }
}



// \App\Models\Position::create(
        //     [
        //     'batch_id' => '2',
        //     'name' => 'Technical Support',
        //     'slug' => 'technical-support',
        //     'quota' => 100,
        //     'description' => 'Dicari TS',
        //     ],
        // );
        // \App\Models\Position::create(
        //     [
        //     'batch_id' => '1',
        //     'name' => 'Technical Writter',
        //     'slug' => 'technical-writter',
        //     'quota' => 50,
        //     'description' => 'Dicari TW',
        //     ],
        // );
        

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