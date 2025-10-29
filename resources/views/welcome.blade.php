<x-guest-layout>
    <div>
        <div class="bg-cover bg-center bg-no-repeat px-8 py-20 md:px-18" style="background-image: url('/img/bg-cover.png');">
            <div class="max-w-7xl mx-auto">
                <h3 class="text-3xl text-white">Selamat Datang!</h3>
                @auth
                <h1 class="font-semibold text-4xl mt-2 text-white">
                    {{ Auth::user()->name }}
                </h1>
                @endauth
                @guest
                <h1 class="font-semibold text-4xl mt-2 text-white">
                    Rekrutmen Tenaga Alih Daya
                </h1>
                @endguest
            </div>
        </div>
        <div class="w-full px-8" id="job">
            <div class="max-w-7xl py-10 mx-auto">
                <div class="flex flex-wrap justify-between">
                    <h1 class="font-bold text-3xl">Lowongan tersedia</h1>
                    <a href="{{ route('joblist') }}">
                        <div class="flex flex-wrap gap-2 items-center text-[#009DA9]">
                            Lihat lainnya
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 10.28a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h5.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                </div>

                @if ($latestPositions->isNotEmpty())
                <div class="grid grid-cols-1 mt-4 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($latestPositions as $position)
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">[{{ $position->batch?->name ?? '-' }}] - {{ $position->name }}</h2>
                        </div>
                        <div class="text-md text-gray-500">
                            <div class="flex flex-wrap items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                                <span>Diploma 3</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Batas lamaran: {{ $position->batch?->end_date_formatted }}
                            </div>
                        </div>
                        {{-- CTA --}}
                        <div class="mt-5">
                            <a href="{{ route('jobdetail', $position) }}"
                                class="w-full block text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex-col justify-items-center md:p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-14 text-red-600">
                        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                    </svg>
                    <h1 class="font-bold text-2xl mt-1 text-center">Tidak dapat menampilkan data</h1>
                    <p class="text-gray-400 text-lg text-center">Belum ada lowongan pekerjaan yang buka saat ini, silahkan untuk memeriksa kembali dalam beberapa waktu ke depan.</p>
                </div>
                @endif
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