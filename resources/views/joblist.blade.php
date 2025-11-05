<x-guest-layout>
    <div class="min-h-screen">
        <div class="bg-cover bg-center bg-no-repeat px-8 py-20 md:px-18" style="background-image: url('/img/bg-cover.png');">
            <div class="max-w-7xl mx-auto">
                <h2 class="font-semibold text-4xl text-white">Lowongan Tersedia</h2>
                <h4 class="text-xl text-white mt-2 font-sans">Mari berkarir bersama kami</h4>
            </div>
        </div>
        <div class="px-8 md:px-18">
            <div class="max-w-7xl mx-auto py-6">
                <div class="bg-yellow-50 rounded rounded-lg p-3 border border-1 border-yellow-500 flex gap-2 text-yellow-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <h4>Anda hanya dapat melamar pada satu job yang tersedia, mohon untuk melamar job yang sesuai dengan kualifikasi yang Anda miliki.</h4>
                </div>
                <div class="mb-4">
                    <h4 class="mb-2 text-lg">Menampilkan <span class="font-semibold">{{ $positions->total() }}</span> lowongan aktif</h4>
                    <form method="GET" action="{{ route('joblist') }}">
                        <div class="flex flex-col sm:flex-row justify-between gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <!-- Heroicons search icon (SVG) -->
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                        </svg>
                                    </span>
                                    <input
                                        type="text" name="q" value="{{ $q }}"
                                        placeholder="Cari lowongan..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#009DA9] focus:border-[#009DA9] sm:text-sm" />
                                </div>
                            </div>
                            <div class="flex-2">
                                <select name="edu"
                                    class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#009DA9] focus:border-[#009DA9] sm:text-sm">
                                    <option value="">Semua Jenjang Pendidikan</option>
                                    @foreach (['SMA/Sederajat','D1','D2','D3','D4','S1','S2','S3'] as $opt)
                                        <option value="{{ $opt }}" {{ $edu === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <input type="submit" class="w-full block text-center bg-[#009DA9] text-white px-4 py-2 sm:py-0 h-full rounded-lg hover:bg-blue-600" value="Cari">
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-7">
                    @forelse ($positions as $position)
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition duration-300">
                        <div>
                            <h2 class="text-xl font-semibold mb-1">{{ $position->name }}</h2>
                        </div>
                        <div class="text-md text-gray-500">
                            <div class="flex flex-wrap items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41
                                            60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493
                                            a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489
                                            a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675
                                            A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>

                                <span>{{ $position->pendidikan_minimum ?? '-' }}</span>
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
                    @empty
                    <div class="col-span-full flex justify-center w-full mt-10">
                        <div class="flex flex-col items-center text-center md:p-4 max-w-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor" class="w-16 h-16 text-red-600 mb-3">
                                <path fill-rule="evenodd"
                                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748
                                    c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5
                                    L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75
                                    0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75
                                    0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>

                            <h1 class="font-bold text-2xl text-gray-800 mb-1">
                                Tidak dapat menampilkan data
                            </h1>
                            <p class="text-gray-400 text-lg leading-relaxed">
                                Belum ada lowongan pekerjaan yang buka saat ini,<br>
                                silahkan untuk memeriksa kembali dalam beberapa waktu ke depan.
                            </p>
                        </div>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $positions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>