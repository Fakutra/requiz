<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Position;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 2 posisi pertama (pakai slug jika ada, fallback ke urutan id)
        $posA = Position::where('slug', 'technical-support')->first() ?? Position::orderBy('id')->first();
        $posB = Position::where('slug', 'technical-writer')->first()
            ?? Position::orderBy('id')->skip(1)->first()
            ?? $posA;

        if (!$posA) {
            $this->command->warn('Tidak ada Position. Seed Batch & Position dulu.');
            return;
        }

        // User 1
        $user1 = User::firstOrCreate(
            ['email' => 'delanda.f@gmail.com'],
            ['name' => 'Delanda', 'password' => Hash::make('password')]
        );

        // User 2
        $user2 = User::firstOrCreate(
            ['email' => 'marselatrianggraini27@gmail.com'],
            ['name' => 'Sela', 'password' => Hash::make('password')]
        );

        $now = Carbon::now();

        $rows = [
            [
                'user_id'     => $user1->id,
                'batch_id'    => $posA->batch_id,
                'position_id' => $posA->id,
                'name'        => 'Delanda',
                'email'       => 'delanda.f@gmail.com',
                'nik'         => '320101199801010001', // 16+ digit? --> pastikan 16 digit
                'no_telp'     => '081234567801',
                'tpt_lahir'   => 'Bandung',
                'tgl_lahir'   => '1998-01-01',
                'alamat'      => 'Jl. Anggrek No. 10, Bandung',
                'pendidikan'  => 'S1',
                'universitas' => 'Institut Teknologi Bandung',
                'jurusan'     => 'Informatika',
                'thn_lulus'   => '2020',
                'skills'      => 'Laravel, MySQL, Git, REST API',
                'cv_document' => 'cv-applicant/bNiMu0GHHkuATKPg4WadEGdenrY4C4NDrd1A9PU0.pdf',
                'status'      => 'Seleksi Administrasi',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'user_id'     => $user2->id,
                'batch_id'    => $posB?->batch_id ?? $posA->batch_id,
                'position_id' => $posB?->id ?? $posA->id,
                'name'        => 'Sela',
                'email'       => 'marselatrianggraini27@gmail.com',
                'nik'         => '320102199901020002', // pastikan 16 digit
                'no_telp'     => '081234567802',
                'tpt_lahir'   => 'Jakarta',
                'tgl_lahir'   => '1999-02-02',
                'alamat'      => 'Jl. Melati No. 5, Jakarta',
                'pendidikan'  => 'S1',
                'universitas' => 'Universitas Indonesia',
                'jurusan'     => 'Sistem Informasi',
                'thn_lulus'   => '2021',
                'skills'      => 'Technical Writing, Excel, Power BI',
                'cv_document' => 'cv-applicant/bNiMu0GHHkuATKPg4WadEGdenrY4C4NDrd1A9PU0.pdf',
                'status'      => 'Seleksi Administrasi',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        // Pastikan NIK 16 digit (kalau contoh di atas kepanjangan, ini pemotong aman)
        foreach ($rows as &$r) {
            $r['nik'] = substr(preg_replace('/\D/', '', $r['nik']), 0, 16);
        }

        DB::table('applicants')->insert($rows);

        $this->command->info('✅ 2 applicant berhasil di-seed.');
    }
}