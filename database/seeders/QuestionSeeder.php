<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // Reset table to avoid duplicates on reseed
        DB::table('questions')->truncate();

        $questions = [
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Ibu kota negara Jepang adalah?",
                "option_a" => "Kyoto", "option_b" => "Osaka", "option_c" => "Tokyo", "option_d" => "Hiroshima", "option_e" => "Nagoya",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Mata uang negara Thailand adalah?",
                "option_a" => "Ringgit", "option_b" => "Baht", "option_c" => "Dong", "option_d" => "Yen", "option_e" => "Rupiah",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Gunung tertinggi di dunia adalah?",
                "option_a" => "K2", "option_b" => "Kangchenjunga", "option_c" => "Lhotse", "option_d" => "Everest", "option_e" => "Makalu",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Benua terluas di dunia adalah?",
                "option_a" => "Afrika", "option_b" => "Eropa", "option_c" => "Amerika", "option_d" => "Australia", "option_e" => "Asia",
                "answer" => "E",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Siapa penemu bola lampu pijar modern?",
                "option_a" => "Alexander Graham Bell", "option_b" => "Thomas Edison", "option_c" => "Nikola Tesla", "option_d" => "Albert Einstein", "option_e" => "Isaac Newton",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Planet yang dikenal sebagai \"Planet Merah\" adalah?",
                "option_a" => "Venus", "option_b" => "Jupiter", "option_c" => "Mars", "option_d" => "Saturnus", "option_e" => "Merkurius",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Lagu kebangsaan Indonesia adalah?",
                "option_a" => "Indonesia Pusaka", "option_b" => "Garuda Pancasila", "option_c" => "Maju Tak Gentar", "option_d" => "Indonesia Raya", "option_e" => "Rayuan Pulau Kelapa",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Apa nama samudra terluas di dunia?",
                "option_a" => "Samudra Atlantik", "option_b" => "Samudra Hindia", "option_c" => "Samudra Pasifik", "option_d" => "Samudra Arktik", "option_e" => "Samudra Antarktika",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Negara dengan populasi terbanyak di dunia adalah?",
                "option_a" => "Tiongkok", "option_b" => "Amerika Serikat", "option_c" => "Indonesia", "option_d" => "India", "option_e" => "Pakistan",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Unsur paling melimpah di kerak Bumi adalah?",
                "option_a" => "Besi", "option_b" => "Oksigen", "option_c" => "Silikon", "option_d" => "Aluminium", "option_e" => "Kalsium",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Ibu kota Australia adalah?",
                "option_a" => "Sydney", "option_b" => "Melbourne", "option_c" => "Perth", "option_d" => "Canberra", "option_e" => "Brisbane",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Sungai terpanjang di dunia menurut banyak sumber modern adalah?",
                "option_a" => "Nil", "option_b" => "Amazon", "option_c" => "Yangtze", "option_d" => "Mississippi", "option_e" => "Kongo",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Bahasa yang paling banyak penuturnya sebagai bahasa ibu adalah?",
                "option_a" => "Bahasa Inggris", "option_b" => "Bahasa Mandarin", "option_c" => "Bahasa Spanyol", "option_d" => "Bahasa Hindi", "option_e" => "Bahasa Arab",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Organ terbesar pada tubuh manusia adalah?",
                "option_a" => "Jantung", "option_b" => "Kulit", "option_c" => "Paru-paru", "option_d" => "Hati", "option_e" => "Otak",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Simbol kimia untuk Natrium adalah?",
                "option_a" => "Na", "option_b" => "N", "option_c" => "Ne", "option_d" => "Ni", "option_e" => "Nd",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Ibukota Kanada adalah?",
                "option_a" => "Toronto", "option_b" => "Vancouver", "option_c" => "Ottawa", "option_d" => "Montreal", "option_e" => "Quebec City",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Tahun Proklamasi Kemerdekaan Indonesia adalah?",
                "option_a" => "1942", "option_b" => "1945", "option_c" => "1947", "option_d" => "1950", "option_e" => "1955",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Lukisan 'Mona Lisa' dibuat oleh?",
                "option_a" => "Michelangelo", "option_b" => "Leonardo da Vinci", "option_c" => "Raphael", "option_d" => "Donatello", "option_e" => "Caravaggio",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Lapisan atmosfer terdekat dengan permukaan bumi adalah?",
                "option_a" => "Stratosfer", "option_b" => "Mesosfer", "option_c" => "Troposfer", "option_d" => "Termosfer", "option_e" => "Eksosfer",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Hewan tercepat di darat adalah?",
                "option_a" => "Singa", "option_b" => "Cheetah", "option_c" => "Kuda", "option_d" => "Antelop", "option_e" => "Serigala",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Ibukota Mesir adalah?",
                "option_a" => "Kairo", "option_b" => "Alexandria", "option_c" => "Giza", "option_d" => "Luxor", "option_e" => "Aswan",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Alat untuk mengukur tekanan udara adalah?",
                "option_a" => "Higrometer", "option_b" => "Barometer", "option_c" => "Termometer", "option_d" => "Anemometer", "option_e" => "Seismograf",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Samudra yang paling dalam adalah?",
                "option_a" => "Atlantik", "option_b" => "Hindia", "option_c" => "Pasifik", "option_d" => "Arktik", "option_e" => "Antarktika",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Nama kimia garam dapur adalah?",
                "option_a" => "Natrium klorida", "option_b" => "Kalium karbonat", "option_c" => "Kalsium sulfat", "option_d" => "Magnesium oksida", "option_e" => "Amonium nitrat",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Gunung api aktif di Indonesia yang terkenal meletus tahun 1883 adalah?",
                "option_a" => "Merapi", "option_b" => "Tambora", "option_c" => "Krakatau", "option_d" => "Kelud", "option_e" => "Semeru",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Negara yang dijuluki Negeri Matador adalah?",
                "option_a" => "Portugal", "option_b" => "Italia", "option_c" => "Spanyol", "option_d" => "Argentina", "option_e" => "Brasil",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Satuan internasional untuk arus listrik adalah?",
                "option_a" => "Volt", "option_b" => "Ohm", "option_c" => "Ampere", "option_d" => "Watt", "option_e" => "Coulomb",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Benua terkecil di dunia adalah?",
                "option_a" => "Eropa", "option_b" => "Australia", "option_c" => "Antarktika", "option_d" => "Amerika Selatan", "option_e" => "Afrika",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Kerajaan Hindu-Buddha terbesar di Indonesia yang meninggalkan Candi Borobudur adalah?",
                "option_a" => "Majapahit", "option_b" => "Sriwijaya", "option_c" => "Mataram Kuno", "option_d" => "Kutai", "option_e" => "Tarumanegara",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Zodiak yang melambangkan singa adalah?",
                "option_a" => "Aries", "option_b" => "Taurus", "option_c" => "Gemini", "option_d" => "Leo", "option_e" => "Virgo",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Ibu kota Turki sejak 1923 adalah?",
                "option_a" => "Istanbul", "option_b" => "Izmir", "option_c" => "Bursa", "option_d" => "Ankara", "option_e" => "Antalya",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Unit untuk frekuensi adalah?",
                "option_a" => "Newton", "option_b" => "Hertz", "option_c" => "Pascal", "option_d" => "Joule", "option_e" => "Tesla",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Negara kepulauan terbesar di dunia adalah?",
                "option_a" => "Filipina", "option_b" => "Jepang", "option_c" => "Indonesia", "option_d" => "Maladewa", "option_e" => "Fiji",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Bapak Proklamator Indonesia terdiri dari?",
                "option_a" => "Soekarno & Hatta", "option_b" => "Soedirman & Hatta", "option_c" => "Soekarno & Sjahrir", "option_d" => "Hatta & Tan Malaka", "option_e" => "Soekarno & Soedirman",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Burung terbesar yang masih hidup adalah?",
                "option_a" => "Albatros", "option_b" => "Elang botak", "option_c" => "Burung Unta", "option_d" => "Kasuari", "option_e" => "Flamingo",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Instrument untuk mengukur gempa adalah?",
                "option_a" => "Barometer", "option_b" => "Seismograf", "option_c" => "Altimeter", "option_d" => "Anemometer", "option_e" => "Higrometer",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Cagar alam laut terbesar di dunia terletak di?",
                "option_a" => "Karibia", "option_b" => "Great Barrier Reef", "option_c" => "Segitiga Terumbu Karang", "option_d" => "Teluk Meksiko", "option_e" => "Laut Baltik",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Logam paling ringan adalah?",
                "option_a" => "Aluminium", "option_b" => "Magnesium", "option_c" => "Lithium", "option_d" => "Sodium", "option_e" => "Kalsium",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Waktu yang dibutuhkan bumi mengelilingi matahari adalah?",
                "option_a" => "24 jam", "option_b" => "7 hari", "option_c" => "30 hari", "option_d" => "365 hari", "option_e" => "12 jam",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Negara pertama yang mendaratkan manusia di Bulan adalah?",
                "option_a" => "Rusia", "option_b" => "Tiongkok", "option_c" => "Amerika Serikat", "option_d" => "Jepang", "option_e" => "India",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Tari Saman berasal dari?",
                "option_a" => "Sumatra Barat", "option_b" => "Aceh", "option_c" => "Bali", "option_d" => "Jawa Barat", "option_e" => "NTT",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Lambang kimia untuk emas adalah?",
                "option_a" => "Ag", "option_b" => "Au", "option_c" => "Fe", "option_d" => "Cu", "option_e" => "Pt",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Gunung tertinggi di Indonesia adalah?",
                "option_a" => "Kerinci", "option_b" => "Semeru", "option_c" => "Rinjani", "option_d" => "Carstensz (Puncak Jaya)", "option_e" => "Slamet",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Suhu air mendidih pada skala Celcius adalah?",
                "option_a" => "0°", "option_b" => "50°", "option_c" => "80°", "option_d" => "95°", "option_e" => "100°",
                "answer" => "E",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Alat musik tradisional 'Angklung' berasal dari?",
                "option_a" => "Jawa Tengah", "option_b" => "Bali", "option_c" => "Jawa Barat", "option_d" => "Sumatra Utara", "option_e" => "Kalimantan Barat",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Badan dunia yang berfungsi menjaga perdamaian dunia adalah?",
                "option_a" => "WHO", "option_b" => "UNICEF", "option_c" => "UNESCO", "option_d" => "PBB", "option_e" => "FAO",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Penemu gravitasi yang terkenal dengan apel jatuh?",
                "option_a" => "Galileo Galilei", "option_b" => "Isaac Newton", "option_c" => "Albert Einstein", "option_d" => "Johannes Kepler", "option_e" => "Niels Bohr",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Suhu normal tubuh manusia sekitar?",
                "option_a" => "34°C", "option_b" => "35°C", "option_c" => "36–37°C", "option_d" => "38–39°C", "option_e" => "40°C",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Hewan mamalia yang bisa terbang adalah?",
                "option_a" => "Burung unta", "option_b" => "Kelelawar", "option_c" => "Ayam", "option_d" => "Ikan terbang", "option_e" => "Kupu-kupu",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Umum",
                "question" => "Danau terbesar di dunia berdasarkan luas permukaan adalah?",
                "option_a" => "Victoria", "option_b" => "Superior", "option_c" => "Caspia", "option_d" => "Baikal", "option_e" => "Tanganyika",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Apa kepanjangan dari HTML?",
                "option_a" => "Hyperlinks and Text Markup Language", "option_b" => "Hyper Text Markup Language", "option_c" => "Home Tool Markup Language", "option_d" => "Hyper Tool Multi Language", "option_e" => "Hyper Text Multi Language",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Manakah yang bukan framework PHP?",
                "option_a" => "Laravel", "option_b" => "CodeIgniter", "option_c" => "Symfony", "option_d" => "Django", "option_e" => "Yii",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Perintah SQL untuk memilih semua data dari tabel users adalah?",
                "option_a" => "GET * FROM users", "option_b" => "SELECT ALL FROM users", "option_c" => "SELECT * FROM users", "option_d" => "READ * FROM users", "option_e" => "FETCH * FROM users",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Apa fungsi dari CSS?",
                "option_a" => "Menangani logika server", "option_b" => "Mengatur struktur halaman", "option_c" => "Mengatur tampilan dan gaya halaman", "option_d" => "Mengelola database", "option_e" => "Membuat interaksi pengguna",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Format data paling umum untuk API modern adalah?",
                "option_a" => "XML", "option_b" => "CSV", "option_c" => "HTML", "option_d" => "JSON", "option_e" => "TXT",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "VCS yang paling populer saat ini adalah?",
                "option_a" => "SVN", "option_b" => "Mercurial", "option_c" => "Git", "option_d" => "CVS", "option_e" => "Bazaar",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Manakah database NoSQL?",
                "option_a" => "MySQL", "option_b" => "PostgreSQL", "option_c" => "SQLite", "option_d" => "MongoDB", "option_e" => "Oracle",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "HTTP status code untuk 'Not Found' adalah?",
                "option_a" => "200", "option_b" => "301", "option_c" => "404", "option_d" => "500", "option_e" => "403",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Framework JavaScript yang dikembangkan Meta (Facebook) adalah?",
                "option_a" => "Angular", "option_b" => "Vue.js", "option_c" => "Svelte", "option_d" => "Ember.js", "option_e" => "React",
                "answer" => "E",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Apa kepanjangan dari API?",
                "option_a" => "Application Programming Interface", "option_b" => "Automated Programming Interface", "option_c" => "Application Protocol Interface", "option_d" => "Applied Programming Interaction", "option_e" => "Application Programming Interaction",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Lapisan OSI yang menangani routing adalah?",
                "option_a" => "Physical", "option_b" => "Data Link", "option_c" => "Network", "option_d" => "Transport", "option_e" => "Session",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Port default HTTP adalah?",
                "option_a" => "21", "option_b" => "22", "option_c" => "25", "option_d" => "80", "option_e" => "443",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Port default HTTPS adalah?",
                "option_a" => "20", "option_b" => "22", "option_c" => "110", "option_d" => "143", "option_e" => "443",
                "answer" => "E",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Perintah Git untuk menggabungkan perubahan dari branch lain ke branch aktif?",
                "option_a" => "git clone", "option_b" => "git pull", "option_c" => "git merge", "option_d" => "git commit", "option_e" => "git stash",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Metode HTTP yang idempotent (umumnya) adalah?",
                "option_a" => "POST", "option_b" => "PUT", "option_c" => "PATCH", "option_d" => "DELETE", "option_e" => "CONNECT",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Tipe index di database yang mempercepat pencarian adalah?",
                "option_a" => "Trigger", "option_b" => "View", "option_c" => "B-Tree", "option_d" => "Stored Procedure", "option_e" => "CTE",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di SQL, perintah untuk menghapus tabel adalah?",
                "option_a" => "DROP TABLE", "option_b" => "DELETE TABLE", "option_c" => "REMOVE TABLE", "option_d" => "ERASE TABLE", "option_e" => "TRUNCATE DATABASE",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Standar JSON mewajibkan string diapit dengan?",
                "option_a" => "Tanda petik tunggal", "option_b" => "Tanda petik ganda", "option_c" => "Backtick", "option_d" => "Bebas", "option_e" => "Tidak ada",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di CSS, selector untuk memilih elemen dengan class 'box'?",
                "option_a" => ".box", "option_b" => "#box", "option_c" => "box", "option_d" => "*box", "option_e" => "div#box",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Ekstensi berkas untuk file Docker adalah?",
                "option_a" => "dockerfile", "option_b" => "Dockerfile", "option_c" => ".docker", "option_d" => ".dock", "option_e" => "compose.yml",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Dalam REST, sumber daya sebaiknya diidentifikasi dengan?",
                "option_a" => "Verba", "option_b" => "Kata kerja", "option_c" => "URL/URI", "option_d" => "Body", "option_e" => "Cookie",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Relasi one-to-many di SQL diwakili oleh?",
                "option_a" => "Primary key di kedua tabel", "option_b" => "Foreign key di tabel many", "option_c" => "Unique key di tabel many", "option_d" => "Index gabungan", "option_e" => "Tanpa key",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Perintah Linux untuk melihat proses yang berjalan?",
                "option_a" => "ls", "option_b" => "ps", "option_c" => "cat", "option_d" => "grep", "option_e" => "pwd",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Unit pengukuran kecepatan jaringan adalah?",
                "option_a" => "kB", "option_b" => "KB/s", "option_c" => "bps", "option_d" => "Hz", "option_e" => "rpm",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Protokol email untuk menerima surat di sisi klien adalah?",
                "option_a" => "SMTP", "option_b" => "IMAP/POP3", "option_c" => "FTP", "option_d" => "SSH", "option_e" => "Telnet",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di OOP, pilar yang memungkinkan class memiliki banyak bentuk adalah?",
                "option_a" => "Encapsulation", "option_b" => "Abstraction", "option_c" => "Inheritance", "option_d" => "Polymorphism", "option_e" => "Composition",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di Laravel, file konfigurasi database berada di?",
                "option_a" => "routes/web.php", "option_b" => "config/app.php", "option_c" => "config/database.php", "option_d" => ".env saja", "option_e" => "database/seeders",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Command artisan untuk menjalankan migration?",
                "option_a" => "php artisan migrate", "option_b" => "php artisan db:seed", "option_c" => "php artisan serve", "option_d" => "php artisan make:model", "option_e" => "composer dump-autoload",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "HTTP status code 201 berarti?",
                "option_a" => "OK", "option_b" => "Created", "option_c" => "Accepted", "option_d" => "No Content", "option_e" => "Moved Permanently",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di CSS, properti untuk membuat elemen menjadi flex container adalah?",
                "option_a" => "display: grid", "option_b" => "display: block", "option_c" => "display: inline", "option_d" => "display: flex", "option_e" => "position: flex",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di PostgreSQL, tipe data untuk menyimpan JSON adalah?",
                "option_a" => "TEXT", "option_b" => "VARCHAR", "option_c" => "JSON/JSONB", "option_d" => "ARRAY", "option_e" => "BLOB",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Perintah Git untuk membuat branch baru dan berpindah ke sana adalah?",
                "option_a" => "git branch new && git checkout new", "option_b" => "git checkout -b new", "option_c" => "git new branch", "option_d" => "git switch --create new", "option_e" => "git clone -b new",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Kueri SQL untuk menghitung jumlah baris adalah?",
                "option_a" => "COUNT(*)", "option_b" => "SUM(*)", "option_c" => "TOTAL()", "option_d" => "LEN()", "option_e" => "ROWS()",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Manakah bukan NoSQL?",
                "option_a" => "Cassandra", "option_b" => "Redis", "option_c" => "Elasticsearch", "option_d" => "MySQL", "option_e" => "CouchDB",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Algoritma hashing untuk kata sandi yang disarankan?",
                "option_a" => "MD5", "option_b" => "SHA-1", "option_c" => "bcrypt/argon2", "option_d" => "SHA-256 mentah", "option_e" => "CRC32",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Konsep ACID, huruf A berarti?",
                "option_a" => "Atomicity", "option_b" => "Availability", "option_c" => "Authentication", "option_d" => "Authorization", "option_e" => "Aggregation",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Teknik caching sisi klien berbasis file statis disebut?",
                "option_a" => "Server-Side Rendering", "option_b" => "Client-Side Rendering", "option_c" => "CDN", "option_d" => "Memoization", "option_e" => "Preloading",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di jaringan, DHCP berfungsi untuk?",
                "option_a" => "Memberi alamat IP otomatis", "option_b" => "Resolusi nama domain", "option_c" => "Keamanan jaringan", "option_d" => "Transfer file", "option_e" => "Manajemen email",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Bahasa yang berjalan di browser secara native adalah?",
                "option_a" => "Python", "option_b" => "PHP", "option_c" => "JavaScript", "option_d" => "Go", "option_e" => "Rust",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Di Linux, untuk melihat penggunaan disk?",
                "option_a" => "ifconfig", "option_b" => "du/df", "option_c" => "top", "option_d" => "tail", "option_e" => "uptime",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Manakah komponen dari URL?",
                "option_a" => "Header", "option_b" => "Body", "option_c" => "Protocol, host, path", "option_d" => "Cookie", "option_e" => "Payload",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Konsep SOLID, huruf S berarti?",
                "option_a" => "Single Responsibility Principle", "option_b" => "Scalable Principle", "option_c" => "Service Principle", "option_d" => "Secure Principle", "option_e" => "Simple Principle",
                "answer" => "A",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "ORM di Laravel disebut?",
                "option_a" => "Hibernate", "option_b" => "TypeORM", "option_c" => "ActiveRecord", "option_d" => "Eloquent", "option_e" => "Prisma",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "HTTP method yang biasa digunakan untuk mengambil data tanpa efek samping?",
                "option_a" => "POST", "option_b" => "PUT", "option_c" => "GET", "option_d" => "DELETE", "option_e" => "PATCH",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Perintah Composer untuk menambahkan paket?",
                "option_a" => "composer run", "option_b" => "composer add", "option_c" => "composer install paket", "option_d" => "composer require vendor/paket", "option_e" => "composer build",
                "answer" => "D",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Pola arsitektur yang memisahkan UI, logika, dan data di web modern?",
                "option_a" => "Monolitik", "option_b" => "MVC", "option_c" => "Microkernel", "option_d" => "Peer-to-peer", "option_e" => "ETL",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Dalam Docker, untuk menjalankan container dari image?",
                "option_a" => "docker build", "option_b" => "docker run", "option_c" => "docker push", "option_d" => "docker commit", "option_e" => "docker exec",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Kunci yang menjamin keunikan baris di tabel disebut?",
                "option_a" => "Foreign key", "option_b" => "Primary key", "option_c" => "Composite index", "option_d" => "Check constraint", "option_e" => "Default",
                "answer" => "B",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "Teknik untuk mengamankan API publik dari penyalahgunaan permintaan berlebihan?",
                "option_a" => "Load balancing", "option_b" => "Sharding", "option_c" => "Rate limiting", "option_d" => "Caching", "option_e" => "Mirroring",
                "answer" => "C",
            ],
            [
                "type" => "PG", "category" => "Teknis",
                "question" => "HTTP 429 berarti?",
                "option_a" => "Too Many Requests", "option_b" => "Unauthorized", "option_c" => "Forbidden", "option_d" => "Unavailable", "option_e" => "Conflict",
                "answer" => "A",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Jelaskan peran ilmu pengetahuan dan teknologi dalam meningkatkan kualitas hidup manusia modern.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Apa dampak urbanisasi terhadap lingkungan dan kehidupan sosial masyarakat?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana upaya yang efektif untuk mengurangi sampah plastik di perkotaan?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Uraikan manfaat dan risiko perkembangan kecerdasan buatan (AI) bagi dunia kerja.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Mengapa literasi media penting di era banjir informasi?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bahas tantangan ketahanan pangan global di tengah perubahan iklim.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Apa saja langkah strategis memajukan pariwisata tanpa merusak budaya lokal?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana peran olahraga dalam membentuk karakter generasi muda?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Jelaskan pentingnya toleransi dan dialog lintas budaya di masyarakat multikultural.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Analisis dampak ekonomi dari event olahraga internasional bagi negara tuan rumah.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Apa saja faktor yang memengaruhi tingkat kebahagiaan suatu negara?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Uraikan hubungan antara demokrasi, kebebasan pers, dan akuntabilitas pemerintah.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana cara mengatasi kesenjangan digital antar wilayah?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Jelaskan peran perpustakaan modern di tengah maraknya sumber informasi digital.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Analisis pro dan kontra kebijakan pajak karbon.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Mengapa pelestarian bahasa daerah penting untuk identitas budaya?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Ulas tantangan dan peluang ekonomi kreatif di era digital.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana strategi pendidikan karakter yang relevan di sekolah?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Peran komunitas lokal dalam mitigasi bencana alam.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Dampak globalisasi terhadap kuliner dan tradisi makan masyarakat.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Strategi efektif mengurangi hoaks di platform media sosial.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Uraikan keterkaitan kesehatan mental dan produktivitas kerja.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Apa dampak pariwisata berlebihan (overtourism) dan cara mengatasinya?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bahas pentingnya ruang terbuka hijau di kota besar.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Peran seni dan budaya dalam memperkuat diplomasi antarnegara.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana teknologi finansial (fintech) mengubah perilaku konsumsi masyarakat?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Dampak ekonomi gig terhadap kesejahteraan pekerja.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Strategi meningkatkan literasi keuangan generasi muda.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Jelaskan pentingnya etika dalam penelitian ilmiah.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Apa saja dilema etis dalam penggunaan data pribadi?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Peran komunitas sains warga (citizen science) dalam riset lingkungan.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Mengapa kesetaraan gender bermanfaat bagi pertumbuhan ekonomi?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Ulas manfaat dan tantangan kerja jarak jauh (remote work).",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana membangun budaya membaca di keluarga?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Jelaskan dampak budaya populer terhadap gaya hidup remaja.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Strategi mengurangi kemacetan di kota metropolitan.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Peran UMKM dalam pemulihan ekonomi nasional.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana kebijakan subsidi mempengaruhi harga dan konsumsi energi?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Ulas dampak kemajuan transportasi terhadap pola migrasi.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Pentingnya pendidikan seksual komprehensif bagi kesehatan remaja.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Jelaskan peran data terbuka (open data) untuk transparansi publik.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana olahraga tradisional bisa dilestarikan di era modern?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Analisis pengaruh film dan serial terhadap pariwisata suatu lokasi.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Peran komunitas lokal dalam konservasi satwa liar.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Dampak budaya kerja 'hustle' terhadap keseimbangan hidup.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana transformasi digital mempengaruhi layanan kesehatan?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Strategi mengurangi kesenjangan kualitas pendidikan antar daerah.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Peran teknologi dalam pelestarian cagar budaya dan museum.",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Apa arti kebahagiaan dalam perspektif budaya yang berbeda?",
            ],
            [
                "type" => "Essay", "category" => "Umum",
                "question" => "Bagaimana meningkatkan partisipasi pemuda dalam kegiatan sosial?",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan arsitektur dan perbandingan monolitik vs microservices disertai contoh kasus.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Apa itu containerization dan bagaimana perannya dalam CI/CD?",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Uraikan strategi penskalaan basis data: vertical scaling vs horizontal scaling.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Bagaimana cara kerja DNS dan mengapa caching DNS penting?",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan prinsip RESTful API dan bandingkan dengan GraphQL.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Apa itu Event-Driven Architecture dan kapan sebaiknya digunakan?",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Strategi observability modern: logging, metrics, dan tracing.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Bahas teknik indexing dan query optimization pada database relasional.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan konsep CAP theorem pada sistem terdistribusi.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Bagaimana mekanisme OAuth 2.0 dan OpenID Connect untuk otentikasi?",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Uraikan praktik terbaik pengamanan API publik.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Perbandingan NoSQL (dokumen, key-value, wide-column, graph) dan use case-nya.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan prinsip idempotency pada API dan cara implementasinya.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Bagaimana CDN bekerja dan kapan layak digunakan?",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Arsitektur front-end modern: bundler, transpiler, dan module system.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Strategi caching multi-layer (client, edge, server, database).",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan konsep Infrastruktur sebagai Kode (IaC) dan alat-alatnya.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Pengantar Kubernetes: pod, deployment, service, dan ingress.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Uraikan strategi backup dan disaster recovery untuk database produksi.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Bagaimana WebSocket bekerja dan kapan lebih tepat dibanding polling/HTTP.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Teknik mitigasi serangan OWASP Top 10 terkini.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Optimasi performa web: critical rendering path dan lazy loading.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan desain skema database untuk sistem e-commerce.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Pengelolaan transaksi dan isolation level di RDBMS.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Arsitektur multi-tenant: shared vs isolated resources.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Mekanisme message queue dan pola pub/sub dalam integrasi sistem.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Perbandingan model konsistensi: strong, eventual, causal.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Service discovery dan load balancing di microservices.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Desain sistem short URL dengan fokus skalabilitas.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Strategi pengujian: unit, integration, e2e, dan contract testing.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan model keamanan Zero Trust dan penerapannya.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Optimasi biaya cloud: autoscaling, spot instance, dan right-sizing.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Arsitektur data lake vs data warehouse, serta ETL/ELT.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Desain sistem rekomendasi konten pada platform streaming.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Teknik pengelolaan rahasia (secrets management) yang aman.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Perbandingan arsitektur serverless dengan container-based.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Edge computing: manfaat dan tantangan.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Desain logging terstruktur dan korelasi trace-id.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan prinsip reactive programming dan use case-nya.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Membangun pipeline ML yang reproducible dan terkelola.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Desain API rate limiting dan throttling yang adil.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Optimasi query di PostgreSQL: explain, analyze, dan tuning index.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Prinsip Accessibility (a11y) dalam pengembangan web modern.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Perbandingan protokol gRPC dan REST dalam layanan internal.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Desain sistem pembayaran dengan idempotent key dan audit log.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Teknik proteksi data PII sesuai regulasi privasi.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Jelaskan pattern Circuit Breaker dan fallback.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Desain sistem chat real-time berskala besar.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Strategi blue/green dan canary deployment.",
            ],
            [
                "type" => "Essay", "category" => "Teknis",
                "question" => "Pengujian keamanan aplikasi: SAST, DAST, IAST, RASP.",
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat bekerja dalam tim, Anda lebih suka menjadi?",
                "option_a" => "Pemimpin yang mengarahkan", "option_b" => "Anggota yang mengikuti instruksi", "option_c" => "Pemberi ide dan inovasi", "option_d" => "Penengah jika ada konflik", "option_e" => "Observer yang menganalisis",
                "point_a" => 5, "point_b" => 2, "point_c" => 4, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Jika Anda gagal dalam suatu hal, reaksi pertama Anda adalah?",
                "option_a" => "Menyalahkan diri sendiri", "option_b" => "Mencari alasan eksternal", "option_c" => "Mengevaluasi kesalahan dan belajar darinya", "option_d" => "Merasa putus asa", "option_e" => "Segera mencoba lagi tanpa berpikir",
                "point_a" => 2, "point_b" => 1, "point_c" => 5, "point_d" => 1, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Bagaimana cara Anda mengelola stres?",
                "option_a" => "Melakukan hobi", "option_b" => "Bercerita kepada teman", "option_c" => "Berolahraga", "option_d" => "Mendengarkan musik", "option_e" => "Menghindarinya dan berharap hilang sendiri",
                "point_a" => 4, "point_b" => 4, "point_c" => 5, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam mengambil keputusan penting, Anda lebih mengandalkan?",
                "option_a" => "Logika dan data", "option_b" => "Intuisi dan perasaan", "option_c" => "Nasihat dari orang dipercaya", "option_d" => "Pengalaman masa lalu", "option_e" => "Kombinasi logika dan intuisi",
                "point_a" => 4, "point_b" => 3, "point_c" => 2, "point_d" => 3, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Apa yang paling memotivasi Anda dalam bekerja?",
                "option_a" => "Gaji dan bonus", "option_b" => "Pengakuan dan pujian", "option_c" => "Kesempatan belajar dan berkembang", "option_d" => "Lingkungan kerja yang nyaman", "option_e" => "Dampak positif dari pekerjaan",
                "point_a" => 2, "point_b" => 3, "point_c" => 5, "point_d" => 4, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika menerima kritik, Anda cenderung?",
                "option_a" => "Merasa terserang dan defensif", "option_b" => "Menerima dengan lapang dada", "option_c" => "Mempertimbangkan kritik secara objektif", "option_d" => "Mengabaikannya", "option_e" => "Meminta penjelasan lebih lanjut",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 2, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Anda menggambarkan diri Anda sebagai orang yang?",
                "option_a" => "Sangat terorganisir dan rapi", "option_b" => "Kreatif dan sedikit berantakan", "option_c" => "Sangat sosial dan suka keramaian", "option_d" => "Pendiam dan suka menyendiri", "option_e" => "Fleksibel dan mudah beradaptasi",
                "point_a" => 4, "point_b" => 3, "point_c" => 3, "point_d" => 2, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Bagaimana Anda menghadapi perubahan yang tidak terduga?",
                "option_a" => "Merasa cemas dan tidak nyaman", "option_b" => "Melihatnya sebagai tantangan", "option_c" => "Mencari informasi sebanyak mungkin", "option_d" => "Mengikuti alur saja", "option_e" => "Membuat rencana baru dengan cepat",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 3, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Prioritas utama Anda saat ini adalah?",
                "option_a" => "Karir dan pekerjaan", "option_b" => "Keluarga dan hubungan", "option_c" => "Pengembangan diri", "option_d" => "Kesehatan dan kesejahteraan", "option_e" => "Keseimbangan semua aspek",
                "point_a" => 3, "point_b" => 4, "point_c" => 4, "point_d" => 4, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam diskusi, Anda lebih sering?",
                "option_a" => "Banyak berbicara menyampaikan pendapat", "option_b" => "Banyak mendengarkan", "option_c" => "Mencari titik tengah", "option_d" => "Menjadi pengamat", "option_e" => "Mengajukan pertanyaan pendalaman",
                "point_a" => 3, "point_b" => 4, "point_c" => 4, "point_d" => 2, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat menghadapi tenggat waktu, Anda biasanya?",
                "option_a" => "Menunda hingga mendekati batas waktu", "option_b" => "Membuat rencana dan mencicil", "option_c" => "Bekerja intensif di awal", "option_d" => "Menunggu motivasi datang", "option_e" => "Membagi tugas dengan tim",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika muncul konflik di tim, Anda?",
                "option_a" => "Menghindar", "option_b" => "Menghadapi secara langsung", "option_c" => "Memediasi pihak terkait", "option_d" => "Melapor ke atasan", "option_e" => "Menganalisis akar masalah",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 3, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat memulai proyek baru, langkah awal Anda adalah?",
                "option_a" => "Mencari referensi", "option_b" => "Membuat to-do list", "option_c" => "Diskusi dengan tim", "option_d" => "Langsung eksekusi", "option_e" => "Menganalisis risiko",
                "point_a" => 4, "point_b" => 4, "point_c" => 4, "point_d" => 2, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam kondisi tekanan tinggi, Anda cenderung?",
                "option_a" => "Panik", "option_b" => "Tetap tenang dan fokus", "option_c" => "Mencari bantuan", "option_d" => "Menyalahkan keadaan", "option_e" => "Menunda pekerjaan",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 1, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Gaya komunikasi yang paling sering Anda gunakan?",
                "option_a" => "Langsung dan tegas", "option_b" => "Tidak langsung dan halus", "option_c" => "Humoris", "option_d" => "Analitis", "option_e" => "Empatik",
                "point_a" => 4, "point_b" => 3, "point_c" => 3, "point_d" => 4, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat ada pekerjaan monoton, Anda?",
                "option_a" => "Mencari variasi", "option_b" => "Tetap konsisten menyelesaikan", "option_c" => "Mendelegasikan", "option_d" => "Menunda", "option_e" => "Minta rotasi tugas",
                "point_a" => 4, "point_b" => 5, "point_c" => 3, "point_d" => 1, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Bagaimana Anda mengatur prioritas?",
                "option_a" => "Berbasis urgensi", "option_b" => "Berbasis dampak", "option_c" => "Berbasis kemudahan", "option_d" => "Berdasarkan arahan atasan", "option_e" => "Mengalir saja",
                "point_a" => 4, "point_b" => 5, "point_c" => 2, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Jika target tidak tercapai, Anda?",
                "option_a" => "Mencari kambing hitam", "option_b" => "Evaluasi objektif", "option_c" => "Meningkatkan usaha", "option_d" => "Menyesuaikan target", "option_e" => "Berhenti mencoba",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam hal kolaborasi lintas tim, Anda?",
                "option_a" => "Menghindari", "option_b" => "Aktif membangun jaringan", "option_c" => "Menunggu diarahkan", "option_d" => "Fokus tim sendiri", "option_e" => "Berbagi ilmu",
                "point_a" => 1, "point_b" => 5, "point_c" => 2, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika ide Anda ditolak, Anda?",
                "option_a" => "Kesal berkepanjangan", "option_b" => "Mencari umpan balik", "option_c" => "Mengajukan alternatif", "option_d" => "Menerima dan lanjut bekerja", "option_e" => "Mencari dukungan pihak lain",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 4, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat menghadapi tenggat waktu, Anda biasanya?",
                "option_a" => "Menunda hingga mendekati batas waktu", "option_b" => "Membuat rencana dan mencicil", "option_c" => "Bekerja intensif di awal", "option_d" => "Menunggu motivasi datang", "option_e" => "Membagi tugas dengan tim",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika muncul konflik di tim, Anda?",
                "option_a" => "Menghindar", "option_b" => "Menghadapi secara langsung", "option_c" => "Memediasi pihak terkait", "option_d" => "Melapor ke atasan", "option_e" => "Menganalisis akar masalah",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 3, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat memulai proyek baru, langkah awal Anda adalah?",
                "option_a" => "Mencari referensi", "option_b" => "Membuat to-do list", "option_c" => "Diskusi dengan tim", "option_d" => "Langsung eksekusi", "option_e" => "Menganalisis risiko",
                "point_a" => 4, "point_b" => 4, "point_c" => 4, "point_d" => 2, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam kondisi tekanan tinggi, Anda cenderung?",
                "option_a" => "Panik", "option_b" => "Tetap tenang dan fokus", "option_c" => "Mencari bantuan", "option_d" => "Menyalahkan keadaan", "option_e" => "Menunda pekerjaan",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 1, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Gaya komunikasi yang paling sering Anda gunakan?",
                "option_a" => "Langsung dan tegas", "option_b" => "Tidak langsung dan halus", "option_c" => "Humoris", "option_d" => "Analitis", "option_e" => "Empatik",
                "point_a" => 4, "point_b" => 3, "point_c" => 3, "point_d" => 4, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat ada pekerjaan monoton, Anda?",
                "option_a" => "Mencari variasi", "option_b" => "Tetap konsisten menyelesaikan", "option_c" => "Mendelegasikan", "option_d" => "Menunda", "option_e" => "Minta rotasi tugas",
                "point_a" => 4, "point_b" => 5, "point_c" => 3, "point_d" => 1, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Bagaimana Anda mengatur prioritas?",
                "option_a" => "Berbasis urgensi", "option_b" => "Berbasis dampak", "option_c" => "Berbasis kemudahan", "option_d" => "Berdasarkan arahan atasan", "option_e" => "Mengalir saja",
                "point_a" => 4, "point_b" => 5, "point_c" => 2, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Jika target tidak tercapai, Anda?",
                "option_a" => "Mencari kambing hitam", "option_b" => "Evaluasi objektif", "option_c" => "Meningkatkan usaha", "option_d" => "Menyesuaikan target", "option_e" => "Berhenti mencoba",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam hal kolaborasi lintas tim, Anda?",
                "option_a" => "Menghindari", "option_b" => "Aktif membangun jaringan", "option_c" => "Menunggu diarahkan", "option_d" => "Fokus tim sendiri", "option_e" => "Berbagi ilmu",
                "point_a" => 1, "point_b" => 5, "point_c" => 2, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika ide Anda ditolak, Anda?",
                "option_a" => "Kesal berkepanjangan", "option_b" => "Mencari umpan balik", "option_c" => "Mengajukan alternatif", "option_d" => "Menerima dan lanjut bekerja", "option_e" => "Mencari dukungan pihak lain",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 4, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat menghadapi tenggat waktu, Anda biasanya?",
                "option_a" => "Menunda hingga mendekati batas waktu", "option_b" => "Membuat rencana dan mencicil", "option_c" => "Bekerja intensif di awal", "option_d" => "Menunggu motivasi datang", "option_e" => "Membagi tugas dengan tim",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika muncul konflik di tim, Anda?",
                "option_a" => "Menghindar", "option_b" => "Menghadapi secara langsung", "option_c" => "Memediasi pihak terkait", "option_d" => "Melapor ke atasan", "option_e" => "Menganalisis akar masalah",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 3, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat memulai proyek baru, langkah awal Anda adalah?",
                "option_a" => "Mencari referensi", "option_b" => "Membuat to-do list", "option_c" => "Diskusi dengan tim", "option_d" => "Langsung eksekusi", "option_e" => "Menganalisis risiko",
                "point_a" => 4, "point_b" => 4, "point_c" => 4, "point_d" => 2, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam kondisi tekanan tinggi, Anda cenderung?",
                "option_a" => "Panik", "option_b" => "Tetap tenang dan fokus", "option_c" => "Mencari bantuan", "option_d" => "Menyalahkan keadaan", "option_e" => "Menunda pekerjaan",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 1, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Gaya komunikasi yang paling sering Anda gunakan?",
                "option_a" => "Langsung dan tegas", "option_b" => "Tidak langsung dan halus", "option_c" => "Humoris", "option_d" => "Analitis", "option_e" => "Empatik",
                "point_a" => 4, "point_b" => 3, "point_c" => 3, "point_d" => 4, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat ada pekerjaan monoton, Anda?",
                "option_a" => "Mencari variasi", "option_b" => "Tetap konsisten menyelesaikan", "option_c" => "Mendelegasikan", "option_d" => "Menunda", "option_e" => "Minta rotasi tugas",
                "point_a" => 4, "point_b" => 5, "point_c" => 3, "point_d" => 1, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Bagaimana Anda mengatur prioritas?",
                "option_a" => "Berbasis urgensi", "option_b" => "Berbasis dampak", "option_c" => "Berbasis kemudahan", "option_d" => "Berdasarkan arahan atasan", "option_e" => "Mengalir saja",
                "point_a" => 4, "point_b" => 5, "point_c" => 2, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Jika target tidak tercapai, Anda?",
                "option_a" => "Mencari kambing hitam", "option_b" => "Evaluasi objektif", "option_c" => "Meningkatkan usaha", "option_d" => "Menyesuaikan target", "option_e" => "Berhenti mencoba",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam hal kolaborasi lintas tim, Anda?",
                "option_a" => "Menghindari", "option_b" => "Aktif membangun jaringan", "option_c" => "Menunggu diarahkan", "option_d" => "Fokus tim sendiri", "option_e" => "Berbagi ilmu",
                "point_a" => 1, "point_b" => 5, "point_c" => 2, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika ide Anda ditolak, Anda?",
                "option_a" => "Kesal berkepanjangan", "option_b" => "Mencari umpan balik", "option_c" => "Mengajukan alternatif", "option_d" => "Menerima dan lanjut bekerja", "option_e" => "Mencari dukungan pihak lain",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 4, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat menghadapi tenggat waktu, Anda biasanya?",
                "option_a" => "Menunda hingga mendekati batas waktu", "option_b" => "Membuat rencana dan mencicil", "option_c" => "Bekerja intensif di awal", "option_d" => "Menunggu motivasi datang", "option_e" => "Membagi tugas dengan tim",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika muncul konflik di tim, Anda?",
                "option_a" => "Menghindar", "option_b" => "Menghadapi secara langsung", "option_c" => "Memediasi pihak terkait", "option_d" => "Melapor ke atasan", "option_e" => "Menganalisis akar masalah",
                "point_a" => 1, "point_b" => 4, "point_c" => 5, "point_d" => 3, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat memulai proyek baru, langkah awal Anda adalah?",
                "option_a" => "Mencari referensi", "option_b" => "Membuat to-do list", "option_c" => "Diskusi dengan tim", "option_d" => "Langsung eksekusi", "option_e" => "Menganalisis risiko",
                "point_a" => 4, "point_b" => 4, "point_c" => 4, "point_d" => 2, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam kondisi tekanan tinggi, Anda cenderung?",
                "option_a" => "Panik", "option_b" => "Tetap tenang dan fokus", "option_c" => "Mencari bantuan", "option_d" => "Menyalahkan keadaan", "option_e" => "Menunda pekerjaan",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 1, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Gaya komunikasi yang paling sering Anda gunakan?",
                "option_a" => "Langsung dan tegas", "option_b" => "Tidak langsung dan halus", "option_c" => "Humoris", "option_d" => "Analitis", "option_e" => "Empatik",
                "point_a" => 4, "point_b" => 3, "point_c" => 3, "point_d" => 4, "point_e" => 5,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Saat ada pekerjaan monoton, Anda?",
                "option_a" => "Mencari variasi", "option_b" => "Tetap konsisten menyelesaikan", "option_c" => "Mendelegasikan", "option_d" => "Menunda", "option_e" => "Minta rotasi tugas",
                "point_a" => 4, "point_b" => 5, "point_c" => 3, "point_d" => 1, "point_e" => 3,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Bagaimana Anda mengatur prioritas?",
                "option_a" => "Berbasis urgensi", "option_b" => "Berbasis dampak", "option_c" => "Berbasis kemudahan", "option_d" => "Berdasarkan arahan atasan", "option_e" => "Mengalir saja",
                "point_a" => 4, "point_b" => 5, "point_c" => 2, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Jika target tidak tercapai, Anda?",
                "option_a" => "Mencari kambing hitam", "option_b" => "Evaluasi objektif", "option_c" => "Meningkatkan usaha", "option_d" => "Menyesuaikan target", "option_e" => "Berhenti mencoba",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 3, "point_e" => 1,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Dalam hal kolaborasi lintas tim, Anda?",
                "option_a" => "Menghindari", "option_b" => "Aktif membangun jaringan", "option_c" => "Menunggu diarahkan", "option_d" => "Fokus tim sendiri", "option_e" => "Berbagi ilmu",
                "point_a" => 1, "point_b" => 5, "point_c" => 2, "point_d" => 2, "point_e" => 4,
            ],
            [
                "type" => "Poin", "category" => "Psikologi",
                "question" => "Ketika ide Anda ditolak, Anda?",
                "option_a" => "Kesal berkepanjangan", "option_b" => "Mencari umpan balik", "option_c" => "Mengajukan alternatif", "option_d" => "Menerima dan lanjut bekerja", "option_e" => "Mencari dukungan pihak lain",
                "point_a" => 1, "point_b" => 5, "point_c" => 4, "point_d" => 4, "point_e" => 3,
            ],
        ];

        foreach ($questions as $questionData) {
            // Normalize fields for each type
            $questionData["point_a"] = $questionData["point_a"] ?? null;
            $questionData["point_b"] = $questionData["point_b"] ?? null;
            $questionData["point_c"] = $questionData["point_c"] ?? null;
            $questionData["point_d"] = $questionData["point_d"] ?? null;
            $questionData["point_e"] = $questionData["point_e"] ?? null;
            $questionData["answer"] = $questionData["answer"] ?? null;
            $questionData["option_a"] = $questionData["option_a"] ?? null;
            $questionData["option_b"] = $questionData["option_b"] ?? null;
            $questionData["option_c"] = $questionData["option_c"] ?? null;
            $questionData["option_d"] = $questionData["option_d"] ?? null;
            $questionData["option_e"] = $questionData["option_e"] ?? null;
            $questionData["image_path"] = $questionData["image_path"] ?? null;

            Question::create($questionData);
        }
    }
}