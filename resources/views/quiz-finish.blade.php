<x-guest-layout>
    <div class="py-4">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                <div class="text-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor"
                        class="size-14 mx-auto text-green-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <div class="mt-2 text-3xl font-bold">Pengerjaan kuis selesai</div>
                    <div class="text-gray-600 mt-2">Terima kasih, semua jawaban sudah tersimpan. <br> Mohon untuk menunggu pengumuman selanjutnya</div>
                </div>

                <div class="text-center mt-6">
                    <a href="{{ route('history.index') }}"
                        class="inline-block rounded-xl border border-[#009DA9] px-5 py-2
                text-[#009DA9] hover:bg-[#009DA9]/10
                focus:outline-none focus:ring-2 focus:ring-[#009DA9] focus:ring-offset-2
                transition">
                        Kembali ke Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>