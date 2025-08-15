<x-guest-layout>
    <div>
        <div class="bg-cover bg-center bg-no-repeat px-8 py-20 md:px-18" style="background-image: url('/img/cover.png');">
            <div class="max-w-7xl mx-auto">
                <h3 class="text-3xl text-white">Welcome!</h3>
                <h1 class="font-semibold text-4xl mt-2 text-white">Recruitment TAD/Outsourcing</h1>
            </div>
        </div>
        <div class="w-full px-8" id="job">
            <div class="max-w-7xl py-10 mx-auto">
                <h1 class="font-bold text-3xl">Lowongan tersedia</h1>
                <div class="grid grid-cols-1 mt-4 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical Support Grade I</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-blue-600">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-blue-600">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 1 -->
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">Junior Technical de I</h2>
                            <p class="text-gray-600 mb-4">Let's join with us</p>
                        </div>
                        <a href="" class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-blue-600">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full px-8" id="faq">
            <div class="max-w-7xl py-10 mx-auto">
                <h1 class="font-bold text-3xl">Frequently Asked Question</h1>
                <div class="max-w-7xl mx-auto mt-4 divide-y divide-gray-300">
                    <!-- Item 1 -->
                    <div class="py-4">
                        <button
                            class="w-full flex justify-between items-center text-left font-semibold text-gray-800 focus:outline-none"
                            onclick="toggleAccordion(this)">
                            <span class="text-2xl">Apa itu layanan kami?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="mt-2 text-gray-600 hidden text-xl">
                            Layanan kami adalah solusi digital untuk membantu bisnis Anda tumbuh secara online.
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="py-4">
                        <button
                            class="w-full flex justify-between items-center text-left font-semibold text-gray-800 focus:outline-none"
                            onclick="toggleAccordion(this)">
                            <span class="text-2xl">Bagaimana cara mendaftar?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="mt-2 text-gray-600 hidden text-xl">
                            Anda cukup mengisi form pendaftaran, lalu kami akan memandu prosesnya.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full px-8" id="about">
            <div class="max-w-7xl py-10 mx-auto">
                <h1 class="font-bold text-3xl">Tentang Kami</h1>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-5 mt-4">
                    <div class="flex-1">
                        <img src="{{ url('/img/icon.webp') }}" class="rounded-2xl w-full" />
                    </div>
                    <div class="flex-1">
                        <div class="max-w-7xl mt-4 text-2xl">
                            Sebagai salah satu supporting operasional dan pemeliharaannya PLN, PLN Iconplus bekerja sama dengan mitra dalam penyediaan tenaga kerja. Recruitment ini diselenggarakan untuk memenuhi kebutuhan tersebut.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>