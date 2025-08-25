{{-- resources/views/admin/quiz-results/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Hasil Quiz
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Pelamar</div>
                        <div class="font-semibold">{{ $testResult->applicant->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Posisi</div>
                        <div class="font-semibold">
                            {{ optional($testResult->applicant->position)->name ?? (optional($testResult->test->position)->name ?? '—') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Nama Tes</div>
                        <div class="font-semibold">{{ $testResult->test->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Skor Total</div>
                        <div class="font-semibold">{{ $totalScore ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Mulai</div>
                        <div class="font-semibold">{{ optional($testResult->started_at)->format('Y-m-d H:i') ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Selesai</div>
                        <div class="font-semibold">{{ optional($testResult->finished_at)->format('Y-m-d H:i') ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            @forelse ($sections as $section)
                <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-lg font-semibold">{{ $section['name'] }}</div>
                        <div class="text-sm">Skor Section: <span
                                class="font-semibold">{{ $section['score'] ?? '—' }}</span></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Soal</th>
                                    <th class="py-2 text-left">Tipe</th>
                                    <th class="py-2 text-left">Jawaban Pelamar</th>
                                    <th class="py-2 text-left">Kunci Jawaban</th>
                                    <th class="py-2 text-left">Skor</th>
                                    <th class="py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($section['answers'] as $a)
                                    <tr class="border-b align-top">
                                        <td class="py-2">{!! nl2br(e($a['question_text'])) !!}</td>
                                        <td class="py-2">{{ $a['type'] }}</td>
                                        <td class="py-2">{{ $a['user_answer'] }}</td>
                                        <td class="py-2">{{ $a['correct_answer'] }}</td>
                                        <td class="py-2">{{ $a['score'] }}</td>
                                        <td class="py-2">
                                            @if ($a['status'] === 'correct')
                                                <span
                                                    class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">Benar</span>
                                            @elseif ($a['status'] === 'wrong')
                                                <span
                                                    class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs">Salah</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs">Menunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-4 text-center" colspan="6">Tidak ada jawaban.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200 text-center">
                    Belum ada section hasil.
                </div>
            @endforelse

            <div class="text-center">
                <a href="{{ route('quiz_results.index') }}"
                    class="inline-block px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200">
                    ← Kembali ke daftar
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
