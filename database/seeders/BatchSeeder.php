<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BatchSeeder extends Seeder
{
    public function run()
    {
        $batches = [
            [
                'name' => 'Batch 1',
                'status' => 'Active',
                'start_date' => '2025-07-01',
                'end_date' => '2025-07-31',
                'positions' => [
                    'Software Engineer',
                    'Data Analyst',
                    'UI/UX Designer',
                    'Network Engineer',
                    'Technical Writer',
                ],
            ],
            [
                'name' => 'Batch 2',
                'status' => 'Active',
                'start_date' => '2025-08-01',
                'end_date' => '2025-08-31',
                'positions' => [
                    'Project Manager',
                    'Quality Assurance',
                    'System Administrator',
                    'Database Administrator',
                    'Cyber Security Specialist',
                ],
            ],
            [
                'name' => 'Batch 3',
                'status' => 'Active',
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-30',
                'positions' => [
                    'Product Owner',
                    'Frontend Developer',
                    'Backend Developer',
                    'DevOps Engineer',
                    'IT Support',
                ],
            ],
        ];

        foreach ($batches as $batchData) {
            $batch = Batch::create([
                'name' => $batchData['name'],
                'slug' => Str::slug($batchData['name']),
                'status' => $batchData['status'],
                'start_date' => $batchData['start_date'],
                'end_date' => $batchData['end_date'],
            ]);

            foreach ($batchData['positions'] as $name) {
                Position::create([
                    'batch_id'     => $batch->id,
                    'name'         => $name,
                    'slug'         => Str::slug($name . '-' . $batch->id),
                    'quota'        => rand(10, 100),
                    'status'       => 'Active',

                    // === Kolom pendidikan minimum (baru) ===
                    'pendidikan_minimum' => match ($name) {
                        'Software Engineer', 
                        'Data Analyst', 
                        'UI/UX Designer', 
                        'Network Engineer', 
                        'Technical Writer',
                        'System Administrator'=> 'S2',
                        'Database Administrator',
                        'Cyber Security Specialist',
                        'Backend Developer',
                        'Frontend Developer',
                        'DevOps Engineer' => 'D3',
                        'Project Manager',
                        'Product Owner' => 'S1',
                        'IT Support' => 'D1',
                        'Quality Assurance' => 'D3',
                        default => 'D3',
                    },

                    // === Deskripsi pekerjaan ===
                    'description'  => match ($name) {
                        'Software Engineer' => 'Bertanggung jawab dalam pengembangan aplikasi dan sistem menggunakan bahasa pemrograman modern serta memastikan performa dan keamanan kode.',
                        'Data Analyst' => 'Menganalisis data perusahaan untuk menghasilkan insight yang berguna bagi pengambilan keputusan bisnis.',
                        'UI/UX Designer' => 'Mendesain antarmuka aplikasi yang menarik dan ramah pengguna berdasarkan riset dan kebutuhan pengguna.',
                        'Network Engineer' => 'Mengelola, merancang, dan memelihara infrastruktur jaringan agar sistem tetap stabil dan aman.',
                        'Technical Writer' => 'Menyusun dokumentasi teknis, panduan pengguna, serta laporan sistem secara jelas dan mudah dipahami.',
                        'Project Manager' => 'Mengelola proyek dari perencanaan hingga implementasi dengan mengoordinasikan tim lintas divisi.',
                        'Quality Assurance' => 'Melakukan pengujian produk untuk memastikan kualitas, keandalan, dan fungsionalitas sebelum dirilis.',
                        'System Administrator' => 'Mengelola server, jaringan, dan sistem operasi untuk memastikan layanan berjalan optimal.',
                        'Database Administrator' => 'Merancang, mengelola, dan mengoptimalkan basis data agar aman, efisien, dan mudah diakses.',
                        'Cyber Security Specialist' => 'Melindungi sistem informasi dari ancaman siber dengan melakukan monitoring dan analisis keamanan.',
                        'Product Owner' => 'Menentukan prioritas pengembangan fitur dan memastikan produk memenuhi kebutuhan pengguna.',
                        'Frontend Developer' => 'Mengembangkan tampilan antarmuka aplikasi berbasis web agar interaktif dan responsif.',
                        'Backend Developer' => 'Mengembangkan logika aplikasi, API, dan integrasi dengan database untuk mendukung fungsionalitas sistem.',
                        'DevOps Engineer' => 'Mengelola pipeline CI/CD, deployment, dan infrastruktur cloud agar pengembangan berjalan efisien.',
                        'IT Support' => 'Memberikan dukungan teknis kepada pengguna terkait perangkat keras, perangkat lunak, dan jaringan.',
                        default => 'Melaksanakan tugas sesuai peran dan tanggung jawab pada posisi terkait.',
                    },

                    // === Kolom tambahan ===
                    'skills'       => match ($name) {
                        'Software Engineer'       => 'PHP, Java, JavaScript',
                        'Data Analyst'             => 'SQL, Python, Excel',
                        'UI/UX Designer'           => 'Figma, Adobe XD, Wireframing',
                        'Network Engineer'         => 'Cisco, Mikrotik, LAN/WAN',
                        'Technical Writer'         => 'Microsoft Office, Grammarly, Documentation',
                        'Project Manager'          => 'Leadership, Planning, Communication',
                        'Quality Assurance'        => 'Testing, Postman, Selenium',
                        'System Administrator'     => 'Linux, Windows Server, Virtualization',
                        'Database Administrator'   => 'MySQL, PostgreSQL, Backup',
                        'Cyber Security Specialist'=> 'Firewall, Penetration Testing, Monitoring',
                        'Product Owner'            => 'Scrum, Backlog, Agile Tools',
                        'Frontend Developer'       => 'HTML, CSS, JavaScript',
                        'Backend Developer'        => 'Laravel, Node.js, API',
                        'DevOps Engineer'          => 'Docker, CI/CD, GitLab',
                        'IT Support'               => 'Troubleshooting, Microsoft Office, Networking',
                        default                    => 'Microsoft Office, Communication',
                    },

                    'requirements' => "Min. GPA ≥ 3.00\nUsia maksimal 35 tahun",
                    'majors'       => "Teknik Informatika\nSistem Informasi\nIlmu Komputer\nTeknik Komputer",
                    'deadline'     => $batchData['end_date'],
                ]);
            }
        }
    }
}
