<x-guest-layout >
    <div class="min-h-screen bg-[#EAF3F9] flex flex-col items-center justify-center px-6 py-12">
        <div class="w-full max-w-3xl bg-white shadow-md border border-gray-200 rounded-2xl p-10 leading-relaxed">

            <div class="flex flex-col items-center mb-8">
                <h1 class="text-xl font-bold text-gray-800">Selamat Datang di ReQuiz!</h1>
            </div>

            <div class="prose max-w-none text-gray-700 leading-relaxed">
                {!! $test->intro ?? '<p>Tidak ada deskripsi singkat untuk kuis ini.</p>' !!}
            </div>

            <p class="text-gray-700 font-medium mb-8">Selamat mengerjakan dan semoga berhasil.</p>

            <div class="flex justify-center gap-5 text-center">
                <a href="{{ route('history.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-[#009DA9] px-5
                text-[#009DA9] hover:bg-[#009DA9]s
                focus:outline-none focus:ring-2 focus:ring-[#009DA9] focus:ring-offset-2
                transition">
                    Kembali
                </a>
                <a href="{{ URL::signedRoute('quiz.start', ['slug' => $test->slug, 'no' => 1]) }}"
                    class="inline-flex items-center justify-center rounded-lg bg-[#009DA9] text-white font-semibold px-8 py-3 hover:bg-[#1B1A5E] transition">
                    MULAI
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>