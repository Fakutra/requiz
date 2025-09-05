{{-- resources/views/quiz-finish.blade.php --}}
<x-app-layout>
    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                <div class="text-center mb-6">
                    <div class="text-3xl font-bold">Terima kasih!</div>
                    <div class="text-gray-600 mt-2">Jawaban kamu sudah terekam.</div>
                </div>

                <div class="text-center mt-6">
                    <a href="{{ route('history.index') }}" class="btn-primary inline-block">Kembali ke Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
