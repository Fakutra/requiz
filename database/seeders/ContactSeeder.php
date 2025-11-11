<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;


class ContactSeeder extends Seeder
{
    public function run(): void
    {
        Contact::create([
            'narahubung'       => 'ReQuiz Support Team',
            'email'            => 'support@requiz.site',
            'phone'            => '08123456789',
            'jam_operasional'  => 'Senin–Jumat, 09.00–17.00',
            'is_active'        => true, // jadi kontak utama
        ]);

        Contact::create([
            'narahubung'       => 'Tim HR ReQuiz',
            'email'            => 'hr@requiz.site',
            'phone'            => '08229876543',
            'jam_operasional'  => 'Senin–Jumat, 09.00–17.00',
            'is_active'        => true,
        ]);

        Contact::create([
            'narahubung'       => 'Tim Event & Partnership',
            'email'            => 'event@requiz.site',
            'phone'            => '083312345678',
            'jam_operasional'  => 'Senin–Sabtu, 10.00–18.00',
            'is_active'        => false,
        ]);
    }
}
