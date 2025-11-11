<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skregis;

class SkregisSeeder extends Seeder
{
    public function run(): void
    {
        Skregis::truncate(); // biar bersih dulu kalau mau reset isi

        $data = [
            [
                'content' => 'judul',
                'title' => null,
                'description' => 'Dengan melakukan registrasi pada website ini, Anda setuju untuk terikat oleh Syarat dan Ketentuan yang berlaku. Harap membaca secara seksama sebelum melanjutkan proses registrasi. Ketentuan ini disusun untuk melindungi hak dan kewajiban pengguna serta pihak pengelola website.',
            ],
            [
                'content' => 'list',
                'title' => 'Pendaftaran Akun',
                'description' => 'Dengan melakukan registrasi, Anda menyatakan bahwa data yang diberikan adalah benar, lengkap, dan milik Anda sendiri.',
            ],
            [
                'content' => 'list',
                'title' => 'Kerahasiaan Akun',
                'description' => 'Anda bertanggung jawab atas kerahasiaan kredensial (email dan kata sandi) serta seluruh aktivitas yang terjadi melalui akun Anda.',
            ],
            [
                'content' => 'list',
                'title' => 'Penggunaan yang Dilarang',
                'description' => 'Dilarang menggunakan layanan untuk tujuan yang melanggar hukum, menipu, merugikan pihak lain, atau melanggar hak kekayaan intelektual.',
            ],
            [
                'content' => 'list',
                'title' => 'Perubahan dan Ketersediaan Layanan',
                'description' => 'Pengelola berhak melakukan pembaruan, perubahan, penangguhan, atau penghentian layanan sewaktu-waktu.',
            ],
            [
                'content' => 'list',
                'title' => 'Privasi dan Data Pribadi',
                'description' => 'Dengan mendaftar, Anda menyetujui pengumpulan dan pemrosesan data sesuai kebijakan privasi kami.',
            ],
            [
                'content' => 'list',
                'title' => 'Pembatasan Tanggung Jawab',
                'description' => 'Pengelola tidak bertanggung jawab atas kerugian langsung maupun tidak langsung yang timbul dari penggunaan layanan.',
            ],
            [
                'content' => 'list',
                'title' => 'Persetujuan',
                'description' => 'Dengan mencentang persetujuan dan menekan tombol “Registrasi Akun”, Anda dianggap telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan ini.',
            ],
        ];

        foreach ($data as $item) {
            Skregis::create($item);
        }
    }
}
