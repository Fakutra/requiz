<x-guest-layout>
    <div class="max-w-3xl mx-auto py-12 text-center">
        {{-- Header --}}
        <h2 class="text-2xl font-bold text-gray-800">
            Selamat Datang, {{ Auth::user()->name }} 👋
        </h2>
        <p class="mt-3 text-gray-600">
            Sebelum melanjutkan, silakan lengkapi profil Anda terlebih dahulu.
        </p>

        {{-- Illustration / Icon --}}
        <div class="mt-8 flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-24 h-24 text-[#009DA9]">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.7 0-5.223-.587-7.5-1.632Z"/>
            </svg>
        </div>

        {{-- Info Card --}}
        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm text-left p-6 mx-4 sm:mx-0">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Mengapa perlu melengkapi profil?</h3>
            <ul class="list-disc list-inside text-gray-600 space-y-1">
                <li>Data Anda digunakan untuk memproses lamaran kerja.</li>
                <li>Profil lengkap membantu HR menilai kualifikasi Anda.</li>
                <li>Pastikan informasi seperti pendidikan, pengalaman, dan kontak sudah benar.</li>
            </ul>
        </div>

        {{-- Tombol ke halaman profil --}}
        <div class="mt-8">
            <a href="{{ route('profile.edit') }}"
               class="inline-flex items-center gap-2 bg-[#1F2855] text-white font-medium px-6 py-3 rounded-full shadow-md hover:bg-[#27316B] transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.7 0-5.223-.587-7.5-1.632Z"/>
                </svg>
                Lengkapi Profil Sekarang
            </a>
        </div>

        {{-- Info tambahan --}}
        <p class="mt-4 text-sm text-gray-500">
            Anda dapat melanjutkan proses lamaran setelah profil lengkap diisi.
        </p>
    </div>
</x-guest-layout>
