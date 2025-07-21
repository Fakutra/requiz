<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Kosongkan tabel questions sebelum mengisi data baru
        // Ini opsional, tapi bagus untuk mencegah duplikasi jika seeder dijalankan berkali-kali
        DB::table('questions')->truncate();

        $questions = [
            // =================================================================
            // 10 Soal Tipe 'PG' Kategori 'Umum'
            // =================================================================
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Ibu kota negara Jepang adalah?',
                'option_a' => 'Kyoto', 'option_b' => 'Osaka', 'option_c' => 'Tokyo', 'option_d' => 'Hiroshima', 'option_e' => 'Nagoya',
                'answer' => 'C',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Mata uang negara Thailand adalah?',
                'option_a' => 'Ringgit', 'option_b' => 'Baht', 'option_c' => 'Dong', 'option_d' => 'Yen', 'option_e' => 'Rupiah',
                'answer' => 'B',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Gunung tertinggi di dunia adalah?',
                'option_a' => 'K2', 'option_b' => 'Kangchenjunga', 'option_c' => 'Lhotse', 'option_d' => 'Everest', 'option_e' => 'Makalu',
                'answer' => 'D',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Benua terluas di dunia adalah?',
                'option_a' => 'Afrika', 'option_b' => 'Eropa', 'option_c' => 'Amerika', 'option_d' => 'Australia', 'option_e' => 'Asia',
                'answer' => 'E',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Siapa penemu bola lampu?',
                'option_a' => 'Alexander Graham Bell', 'option_b' => 'Thomas Edison', 'option_c' => 'Nikola Tesla', 'option_d' => 'Albert Einstein', 'option_e' => 'Isaac Newton',
                'answer' => 'B',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Planet yang dikenal sebagai "Planet Merah" adalah?',
                'option_a' => 'Venus', 'option_b' => 'Jupiter', 'option_c' => 'Mars', 'option_d' => 'Saturnus', 'option_e' => 'Merkurius',
                'answer' => 'C',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Lagu kebangsaan Indonesia adalah?',
                'option_a' => 'Indonesia Pusaka', 'option_b' => 'Garuda Pancasila', 'option_c' => 'Maju Tak Gentar', 'option_d' => 'Indonesia Raya', 'option_e' => 'Rayuan Pulau Kelapa',
                'answer' => 'D',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Berapa jumlah provinsi di Indonesia saat ini (2024)?',
                'option_a' => '34', 'option_b' => '35', 'option_c' => '36', 'option_d' => '37', 'option_e' => '38',
                'answer' => 'E',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Apa nama samudra terluas di dunia?',
                'option_a' => 'Samudra Atlantik', 'option_b' => 'Samudra Hindia', 'option_c' => 'Samudra Pasifik', 'option_d' => 'Samudra Arktik', 'option_e' => 'Samudra Antarktika',
                'answer' => 'C',
            ],
            [
                'type' => 'PG', 'category' => 'Umum', 'question' => 'Negara dengan populasi terbanyak di dunia adalah?',
                'option_a' => 'China', 'option_b' => 'Amerika Serikat', 'option_c' => 'Indonesia', 'option_d' => 'India', 'option_e' => 'Pakistan',
                'answer' => 'D',
            ],

            // =================================================================
            // 10 Soal Tipe 'PG' Kategori 'Teknis'
            // =================================================================
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Apa kepanjangan dari HTML?',
                'option_a' => 'Hyperlinks and Text Markup Language', 'option_b' => 'Hyper Text Markup Language', 'option_c' => 'Home Tool Markup Language', 'option_d' => 'Hyper Tool Multi Language', 'option_e' => 'Hyper Text Multi Language',
                'answer' => 'B',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Manakah yang bukan merupakan framework PHP?',
                'option_a' => 'Laravel', 'option_b' => 'CodeIgniter', 'option_c' => 'Symfony', 'option_d' => 'Django', 'option_e' => 'Yii',
                'answer' => 'D',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Perintah SQL untuk memilih semua data dari tabel "users" adalah?',
                'option_a' => 'GET * FROM users', 'option_b' => 'SELECT ALL FROM users', 'option_c' => 'SELECT * FROM users', 'option_d' => 'READ * FROM users', 'option_e' => 'FETCH * FROM users',
                'answer' => 'C',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Apa fungsi dari CSS?',
                'option_a' => 'Menangani logika server', 'option_b' => 'Mengatur struktur halaman web', 'option_c' => 'Mengatur tampilan dan gaya halaman web', 'option_d' => 'Mengelola database', 'option_e' => 'Membuat interaksi pengguna',
                'answer' => 'C',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Format data yang paling umum digunakan dalam API modern adalah?',
                'option_a' => 'XML', 'option_b' => 'CSV', 'option_c' => 'HTML', 'option_d' => 'JSON', 'option_e' => 'TXT',
                'answer' => 'D',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Sistem kontrol versi (VCS) yang paling populer saat ini adalah?',
                'option_a' => 'SVN', 'option_b' => 'Mercurial', 'option_c' => 'Git', 'option_d' => 'CVS', 'option_e' => 'Bazaar',
                'answer' => 'C',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Manakah dari berikut ini yang merupakan database NoSQL?',
                'option_a' => 'MySQL', 'option_b' => 'PostgreSQL', 'option_c' => 'SQLite', 'option_d' => 'MongoDB', 'option_e' => 'Oracle',
                'answer' => 'D',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'HTTP status code untuk "Not Found" adalah?',
                'option_a' => '200', 'option_b' => '301', 'option_c' => '404', 'option_d' => '500', 'option_e' => '403',
                'answer' => 'C',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Framework JavaScript untuk membangun antarmuka pengguna yang dikembangkan oleh Facebook adalah?',
                'option_a' => 'Angular', 'option_b' => 'Vue.js', 'option_c' => 'Svelte', 'option_d' => 'Ember.js', 'option_e' => 'React',
                'answer' => 'E',
            ],
            [
                'type' => 'PG', 'category' => 'Teknis', 'question' => 'Apa kepanjangan dari API?',
                'option_a' => 'Application Programming Interface', 'option_b' => 'Automated Programming Interface', 'option_c' => 'Application Protocol Interface', 'option_d' => 'Applied Programming Interaction', 'option_e' => 'Application Programming Interaction',
                'answer' => 'A',
            ],

            // =================================================================
            // 10 Soal Tipe 'Essay' Kategori 'Umum'
            // =================================================================
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Jelaskan dampak positif dan negatif dari media sosial bagi remaja.'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Menurut Anda, apa tantangan terbesar yang dihadapi Indonesia saat ini?'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Bagaimana cara menjaga kelestarian lingkungan di sekitar tempat tinggal Anda?'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Jelaskan pentingnya toleransi antarumat beragama dalam kehidupan bermasyarakat.'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Apa pendapat Anda tentang sistem pendidikan di Indonesia? Berikan saran perbaikan.'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Mengapa korupsi menjadi penghambat utama kemajuan suatu negara?'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Jelaskan peran generasi muda dalam pembangunan bangsa.'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Apa arti pahlawan menurut Anda di zaman sekarang?'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Bagaimana teknologi mengubah cara kita berkomunikasi sehari-hari?'],
            ['type' => 'Essay', 'category' => 'Umum', 'question' => 'Jelaskan pentingnya menjaga kesehatan mental selain kesehatan fisik.'],

            // =================================================================
            // 10 Soal Tipe 'Essay' Kategori 'Teknis'
            // =================================================================
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Jelaskan perbedaan antara database SQL dan NoSQL beserta contoh penggunaannya.'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Apa yang dimaksud dengan arsitektur microservices dan apa kelebihannya dibandingkan arsitektur monolitik?'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Jelaskan alur kerja (workflow) dasar menggunakan Git untuk kolaborasi tim.'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Apa itu Object-Oriented Programming (OOP)? Sebutkan dan jelaskan 4 pilar utamanya.'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Jelaskan perbedaan antara otentikasi (authentication) dan otorisasi (authorization).'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Apa yang dimaksud dengan "responsive web design" dan mengapa itu penting?'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Jelaskan konsep asynchronous programming dalam JavaScript.'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Apa fungsi dari sebuah package manager seperti NPM atau Composer?'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Jelaskan apa itu "Cloud Computing" dan sebutkan tiga model layanan utamanya (IaaS, PaaS, SaaS).'],
            ['type' => 'Essay', 'category' => 'Teknis', 'question' => 'Apa saja langkah-langkah yang perlu diperhatikan untuk mengamankan sebuah aplikasi web dari serangan umum?'],

            // =================================================================
            // 10 Soal Tipe 'poin' Kategori 'Psikologi'
            // =================================================================
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Saat bekerja dalam tim, Anda lebih suka menjadi?',
                'option_a' => 'Pemimpin yang mengarahkan', 'option_b' => 'Anggota yang mengikuti instruksi', 'option_c' => 'Pemberi ide dan inovasi', 'option_d' => 'Penengah jika ada konflik', 'option_e' => 'Observer yang menganalisis',
                'point_a' => 5, 'point_b' => 2, 'point_c' => 4, 'point_d' => 3, 'point_e' => 1,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Jika Anda gagal dalam suatu hal, reaksi pertama Anda adalah?',
                'option_a' => 'Menyalahkan diri sendiri', 'option_b' => 'Mencari alasan eksternal', 'option_c' => 'Mengevaluasi kesalahan dan belajar darinya', 'option_d' => 'Merasa putus asa', 'option_e' => 'Segera mencoba lagi tanpa berpikir',
                'point_a' => 2, 'point_b' => 1, 'point_c' => 5, 'point_d' => 1, 'point_e' => 3,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Bagaimana cara Anda mengelola stres?',
                'option_a' => 'Melakukan hobi', 'option_b' => 'Bercerita kepada teman', 'option_c' => 'Berolahraga', 'option_d' => 'Mendengarkan musik', 'option_e' => 'Menghindarinya dan berharap hilang sendiri',
                'point_a' => 4, 'point_b' => 4, 'point_c' => 5, 'point_d' => 3, 'point_e' => 1,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Dalam mengambil keputusan penting, Anda lebih mengandalkan?',
                'option_a' => 'Logika dan data', 'option_b' => 'Intuisi dan perasaan', 'option_c' => 'Nasihat dari orang yang dipercaya', 'option_d' => 'Pengalaman masa lalu', 'option_e' => 'Kombinasi logika dan intuisi',
                'point_a' => 4, 'point_b' => 3, 'point_c' => 2, 'point_d' => 3, 'point_e' => 5,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Apa yang paling memotivasi Anda dalam bekerja?',
                'option_a' => 'Gaji dan bonus', 'option_b' => 'Pengakuan dan pujian', 'option_c' => 'Kesempatan untuk belajar dan berkembang', 'option_d' => 'Lingkungan kerja yang nyaman', 'option_e' => 'Dampak positif dari pekerjaan',
                'point_a' => 2, 'point_b' => 3, 'point_c' => 5, 'point_d' => 4, 'point_e' => 5,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Ketika menerima kritik, Anda cenderung?',
                'option_a' => 'Merasa terserang dan defensif', 'option_b' => 'Menerima dengan lapang dada', 'option_c' => 'Mempertimbangkan kritik tersebut secara objektif', 'option_d' => 'Mengabaikannya', 'option_e' => 'Meminta penjelasan lebih lanjut',
                'point_a' => 1, 'point_b' => 4, 'point_c' => 5, 'point_d' => 2, 'point_e' => 5,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Anda menggambarkan diri Anda sebagai orang yang?',
                'option_a' => 'Sangat terorganisir dan rapi', 'option_b' => 'Kreatif dan sedikit berantakan', 'option_c' => 'Sangat sosial dan suka keramaian', 'option_d' => 'Pendiam dan suka menyendiri', 'option_e' => 'Fleksibel dan mudah beradaptasi',
                'point_a' => 4, 'point_b' => 3, 'point_c' => 3, 'point_d' => 2, 'point_e' => 5,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Bagaimana Anda menghadapi perubahan yang tidak terduga?',
                'option_a' => 'Merasa cemas dan tidak nyaman', 'option_b' => 'Melihatnya sebagai tantangan baru', 'option_c' => 'Berusaha mencari informasi sebanyak mungkin', 'option_d' => 'Mengikuti alur saja', 'option_e' => 'Membuat rencana baru dengan cepat',
                'point_a' => 1, 'point_b' => 5, 'point_c' => 4, 'point_d' => 3, 'point_e' => 5,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Prioritas utama Anda saat ini adalah?',
                'option_a' => 'Karir dan pekerjaan', 'option_b' => 'Keluarga dan hubungan', 'option_c' => 'Pengembangan diri', 'option_d' => 'Kesehatan dan kesejahteraan', 'option_e' => 'Keseimbangan antara semua aspek',
                'point_a' => 3, 'point_b' => 4, 'point_c' => 4, 'point_d' => 4, 'point_e' => 5,
            ],
            [
                'type' => 'Poin', 'category' => 'Psikologi', 'question' => 'Dalam diskusi, Anda lebih sering?',
                'option_a' => 'Banyak berbicara dan menyampaikan pendapat', 'option_b' => 'Banyak mendengarkan pendapat orang lain', 'option_c' => 'Berusaha mencari titik tengah', 'option_d' => 'Menjadi pengamat', 'option_e' => 'Mengajukan pertanyaan untuk memperdalam diskusi',
                'point_a' => 3, 'point_b' => 4, 'point_c' => 4, 'point_d' => 2, 'point_e' => 5,
            ],
        ];

        // Looping untuk memasukkan data ke database
        foreach ($questions as $questionData) {
            // Mengosongkan field yang tidak relevan berdasarkan tipe soal
            $questionData['point_a'] = $questionData['point_a'] ?? null;
            $questionData['point_b'] = $questionData['point_b'] ?? null;
            $questionData['point_c'] = $questionData['point_c'] ?? null;
            $questionData['point_d'] = $questionData['point_d'] ?? null;
            $questionData['point_e'] = $questionData['point_e'] ?? null;
            $questionData['answer'] = $questionData['answer'] ?? null;
            $questionData['option_a'] = $questionData['option_a'] ?? null;
            $questionData['option_b'] = $questionData['option_b'] ?? null;
            $questionData['option_c'] = $questionData['option_c'] ?? null;
            $questionData['option_d'] = $questionData['option_d'] ?? null;
            $questionData['option_e'] = $questionData['option_e'] ?? null;
            $questionData['image_path'] = $questionData['image_path'] ?? null;
            
            Question::create($questionData);
        }
    }
}
