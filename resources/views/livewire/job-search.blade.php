<div>
    {{-- Bagian Header & Filter --}}
    <div class="mb-6">
        <h4 class="mb-2 text-lg">
            Menampilkan <span class="font-semibold">{{ $positions->total() }}</span> lowongan aktif
        </h4>

        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search Input --}}
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                    </svg>
                </span>
                <input
                    wire:model.live.debounce.500ms="q"
                    type="text"
                    placeholder="Ketik untuk mencari lowongan..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#009DA9] focus:border-[#009DA9] outline-none shadow-sm" />

                {{-- Indikator Loading (Muncul saat Livewire sedang fetch data) --}}
                <div wire:loading wire:target="q, edu" class="absolute right-3 top-2.5">
                    <svg class="animate-spin h-5 w-5 text-[#009DA9]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            {{-- Education Filter --}}
            <div class="w-full sm:w-64">
                <select wire:model.live="edu" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#009DA9] outline-none shadow-sm">
                    <option value="">Semua Jenjang Pendidikan</option>
                    @foreach (['SMA/Sederajat','D1','D2','D3','D4','S1','S2','S3'] as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if($positions->count() > 0)

    {{-- Daftar Lowongan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($positions as $position)
        {{-- Card Job --}}
        <div class="bg-white flex flex-col justify-between h-full rounded-2xl shadow-md p-6 hover:shadow-lg transition">
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
    <div class="mt-6">
        {{ $positions->links() }}
    </div>
    @endif
    @else
    {{-- Empty State --}}
    <div class="flex items-center justify-center min-h-[36vh] bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
        <div class="text-center max-w-md px-6">
            <div class="bg-red-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-red-600">
                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748 c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5 L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">
                {{ ($q != '' || $edu != '') ? 'Lowongan tidak ditemukan' : 'Lowongan belum tersedia' }}
            </h1>
            <p class="text-gray-500 text-sm md:text-lg leading-relaxed">
                {{ ($q != '' || $edu != '') ? 'Coba gunakan kata kunci atau filter pendidikan yang lain' : 'Saat ini belum ada lowongan pekerjaan yang dibuka. Silakan periksa kembali dalam beberapa waktu ke depan' }}
            </p>
        </div>
    </div>
    @endif
</div>