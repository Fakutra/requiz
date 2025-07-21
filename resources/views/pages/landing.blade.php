<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReQuiz</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">

    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Hero Section --}}
    <section class="text-center py-20 bg-blue-100">
        <h1 class="text-4xl font-bold mb-4">Temukan Pekerjaan Impianmu di Sini</h1>
        <p class="text-lg text-gray-700 mb-6">Daftar dan lamar pekerjaan dengan mudah</p>
        <a href="{{ url('/register') }}" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">Daftar Sekarang</a>
    </section>

    {{-- Section Tambahan --}}
    <section class="max-w-4xl mx-auto py-16">
        <h2 class="text-2xl font-semibold text-center mb-8">Mengapa Memilih Kami?</h2>
        <div class="grid md:grid-cols-3 gap-6 text-center">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-bold text-lg mb-2">Lowongan Terbaru</h3>
                <p>Update harian dari berbagai perusahaan terpercaya.</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-bold text-lg mb-2">Mudah & Cepat</h3>
                <p>Proses pendaftaran dan lamaran dalam hitungan menit.</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-bold text-lg mb-2">Akses Gratis</h3>
                <p>Tidak ada biaya untuk pelamar pekerjaan.</p>
            </div>
        </div>
    </section>

</body>
</html>
