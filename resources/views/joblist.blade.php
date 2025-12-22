<x-guest-layout>
    <div class="min-h-screen">

        {{-- Header --}}
        <div class="bg-cover bg-center bg-no-repeat px-8 py-20 md:px-18"
            style="background-image: url('/img/bg-cover.png');">
            <div class="max-w-7xl mx-auto">
                <h2 class="font-semibold text-3xl md:text-4xl text-white">Lowongan Tersedia</h2>
                <h4 class="text-lg md:text-xl text-white mt-2 font-sans">Mari berkarir bersama kami</h4>
            </div>
        </div>

        <div class="px-8 md:px-18">
            <div class="max-w-7xl mx-auto py-6">
                {{-- Alert --}}
                <div
                    class="bg-yellow-50 rounded-lg p-3 border border-yellow-500 flex gap-2 text-yellow-600 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="size-6 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <p>
                        Anda hanya dapat melamar pada satu job yang tersedia,
                        mohon untuk melamar job yang sesuai dengan kualifikasi yang Anda miliki.
                    </p>
                </div>
                {{-- Filter --}}
                <div class="mb-6">
                    <h4 class="mb-2 text-lg">
                        Menampilkan <span class="font-semibold">{{ $positions->total() }}</span> lowongan aktif
                    </h4>
                    <form method="GET" action="{{ route('joblist') }}">
                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Search --}}
                            <div class="relative flex-grow">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                    </svg>
                                </span>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $q }}"
                                    placeholder="Cari lowongan..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-[#009DA9] focus:border-[#009DA9]" />
                            </div>
                            {{-- Education --}}
                            <div class="flex flex-row gap-3">
                                <div class="w-full sm:w-64">
                                    <select
                                        name="edu"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#009DA9] focus:border-[#009DA9]">
                                        <option value="">Semua Jenjang Pendidikan</option>
                                        @foreach (['SMA/Sederajat','D1','D2','D3','D4','S1','S2','S3'] as $opt)
                                        <option value="{{ $opt }}" {{ $edu === $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Submit --}}
                                <div class="flex-none">
                                    <button
                                        type="submit"
                                        class="w-full h-full bg-[#009DA9] text-white px-6 py-2 rounded-lg hover:bg-[#008A95]">
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- Content --}}
                @if ($positions->count())

                {{-- Job Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($positions as $position)
                    <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6
                                hover:shadow-lg transition">
                        <h2 class="text-xl font-semibold mb-2">
                            {{ $position->name }}
                        </h2>
                        <div class="text-gray-500 space-y-1">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                                <span>Minimal {{ $position->pendidikan_minimum ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <span>
                                    Batas lamaran:
                                    {{ $position->deadline
                                                ? \Carbon\Carbon::parse($position->deadline)->translatedFormat('d F Y')
                                                : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('jobdetail', $position) }}" class="block w-full text-center bg-[#009DA9] text-white px-4 py-3 rounded-lg hover:bg-[#008A95]">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($positions->hasPages())
                <div class="mt-10">
                    {{ $positions->links() }}
                </div>
                @endif

                @elseif ($positions->count() == 0 && request()->hasAny(['q','edu']))

                {{-- Empty State 1 --}}
                <div class="flex items-center justify-center min-h-[50vh]">
                    <div class="text-center max-w-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-16 h-16 text-red-600 mb-3 mx-auto">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748 c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5 L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                        </svg>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">
                            Lowongan tidak ditemukan
                        </h1>
                        <p class="text-gray-400 text-lg">
                            Kami tidak menemukan lowongan yang sesuai. <br> Silakan ubah kata kunci atau filter yang digunakan.<br>
                        </p>
                    </div>
                </div>

                @else

                {{-- Empty State 2 --}}
                <div class="flex items-center justify-center min-h-[50vh]">
                    <div class="text-center max-w-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-16 h-16 text-red-600 mb-3 mx-auto">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748 c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5 L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                        </svg>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">
                            Lowongan tidak ditemukan
                        </h1>
                        <p class="text-gray-400 text-lg">
                            Saat ini belum ada lowongan pekerjaan yang dibuka. Silakan periksa kembali dalam beberapa waktu ke depan.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>