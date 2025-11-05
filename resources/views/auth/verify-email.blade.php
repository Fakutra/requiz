<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-[80vh] px-4">
        {{-- Card Container --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-md max-w-lg w-full p-8 text-center">

            {{-- Icon --}}
            <div class="flex justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor"
                     class="w-16 h-16 text-[#009DA9]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0L12 12.75 2.25 6.75" />
                </svg>
            </div>

            {{-- Header --}}
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Verifikasi Email Kamu</h2>

            {{-- Message --}}
            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Terima kasih telah mendaftar di <span class="font-semibold text-[#009DA9]">ReQuiz</span>!  
                Sebelum melanjutkan, silakan verifikasi alamat email kamu dengan mengklik tautan yang baru saja kami kirim ke:
            </p>

            <p class="text-base font-semibold text-gray-800">
                {{ Auth::user()->email }}
            </p>

            <p class="text-sm text-gray-500 mt-2">
                Jika kamu belum menerima email verifikasi, kamu dapat mengirim ulang dengan menekan tombol di bawah ini.
            </p>

            {{-- Success message --}}
            @if (session('status') == 'verification-link-sent')
                <div class="mt-4 text-green-600 text-sm font-medium">
                    Tautan verifikasi baru telah dikirim ke email kamu 🎉
                </div>
            @endif

            {{-- Actions --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                {{-- Resend email form --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="bg-[#1F2855] text-white font-medium px-6 py-2 rounded-full shadow-md hover:bg-[#27316B] transition">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                {{-- Logout --}}
                {{-- <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-sm underline text-gray-600 hover:text-gray-900 transition">
                        Keluar
                    </button>
                </form> --}}
            </div>
        </div>

        {{-- Footer info --}}
        <p class="mt-6 text-xs text-gray-500 text-center">
            Pastikan kamu memeriksa folder <span class="font-semibold">Spam</span> atau <span class="font-semibold">Promosi</span> jika email belum masuk.
        </p>
    </div>
</x-guest-layout>
