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
                @livewire('job-search')
            </div>
        </div>
    </div>
</x-guest-layout>