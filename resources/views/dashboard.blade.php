<x-guest-layout>
    <div class="max-w-7xl py-8 mx-auto">
        <div>
            <h4>Selamat Datang,</h4>
            <h2 class="font-bold">{{ Auth::user()->name }}</h2>
        </div>
        <div class="flex items-stretch gap-3 mt-6">
            <a href="{{ route('history.index') }}" class="flex-1 no-underline text-neutral-950">
                <div class="h-full min-h-14 bg-white border-2 border-gray-300 rounded-xl px-4 py-3
                flex items-center justify-between transition-colors hover:border-gray-400">
                    <h3 class="m-0 leading-tight font-semibold">3 Riwayat Lamaran</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="size-6 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex-1 no-underline text-neutral-950">
                <div class="h-full min-h-14 bg-white border-2 border-gray-300 rounded-xl px-4 py-3
                flex items-center justify-between transition-colors hover:border-gray-400">
                    <h3 class="m-0 leading-tight font-semibold">Profil Saya</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="size-6 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </a>
        </div>
    </div>
</x-guest-layout>