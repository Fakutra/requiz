{{-- resources/views/admin/quiz-results/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Hasil Quiz
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info ringkas hasil --}}
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

            {{-- Per section --}}
            @forelse ($sections as $section)
                <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-lg font-semibold">{{ $section['name'] }}</div>
                        <div class="text-sm">Skor Section: <span
                                class="font-semibold">{{ $section['score'] ?? '—' }}</span></div>
                    </div>

                    {{-- Loop soal --}}
                    <div class="space-y-6">
                        @forelse ($section['answers'] as $a)
                            <div class="border border-gray-200 rounded-xl p-4">
                                {{-- Teks soal --}}
                                <div class="mb-3">
                                    <div class="text-sm text-gray-500">{{ $a['type'] }}</div>
                                    <div class="font-medium">{!! nl2br(e($a['question_text'])) !!}</div>
                                </div>

                                @if (in_array($a['type'], ['PG', 'Multiple', 'Poin']))
                                    {{-- Opsi A–E --}}
                                    <div class="grid gap-2">
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $L)
                                            @php
                                                $optText = $a['options'][$L] ?? null;
                                                if (!$optText) {
                                                    continue;
                                                }

                                                $isSelected = in_array($L, $a['selected_letters'] ?? []);
                                                $isKey = in_array($L, $a['correct_letters'] ?? []);
                                                $point = $a['type'] === 'Poin' ? $a['option_points'][$L] ?? null : null;

                                                $wrap = 'border border-gray-200 bg-white';
                                                $statusBadge = '';

                                                if (in_array($a['type'], ['PG', 'Multiple'])) {
                                                    if ($isKey) {
                                                        $wrap = 'border border-green-300 bg-green-100';
                                                        $statusBadge = 'Benar';
                                                    } elseif ($isSelected && !$isKey) {
                                                        $wrap = 'border border-red-300 bg-red-100';
                                                        $statusBadge = 'Salah';
                                                    }
                                                }

                                                if ($a['type'] === 'Poin' && $isSelected) {
                                                    if (!is_null($point) && $point > 0) {
                                                        $wrap = 'border border-green-300 bg-green-100';
                                                        $statusBadge = 'Dipilih';
                                                    } else {
                                                        $wrap = 'border border-red-300 bg-red-100';
                                                        $statusBadge = 'Dipilih';
                                                    }
                                                }
                                            @endphp

                                            <div class="rounded-lg px-3 py-2 {{ $wrap }}">
                                                <div class="flex items-start gap-2 flex-wrap">
                                                    <div class="font-semibold shrink-0">{{ $L }}.</div>
                                                    <div class="flex-1">{{ $optText }}</div>

                                                    @if ($isSelected)
                                                        <span
                                                            class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">Dipilih</span>
                                                    @endif

                                                    @if ($statusBadge && in_array($a['type'], ['PG', 'Multiple']))
                                                        <span
                                                            class="px-2 py-0.5 text-xs rounded-full {{ $statusBadge === 'Salah' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }}">
                                                            {{ $statusBadge }}
                                                        </span>
                                                    @endif

                                                    @if ($a['type'] === 'Poin')
                                                        <span
                                                            class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800">Poin:
                                                            {{ is_null($point) ? '—' : $point }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Ringkas jawaban / kunci --}}
                                    <div class="mt-3 text-xs text-gray-500">
                                        Jawaban Pelamar: {{ $a['user_answer'] }}
                                        |
                                        Kunci:
                                        @if (in_array($a['type'], ['PG', 'Multiple']))
                                            {{ $a['correct_answer'] ?: '—' }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                @elseif ($a['type'] === 'Essay')
                                    <div class="rounded-lg border border-gray-200 bg-white p-3">
                                        <div class="text-sm text-gray-500 mb-1">Jawaban Pelamar</div>
                                        <div class="whitespace-pre-line">{{ $a['user_answer'] }}</div>
                                    </div>
                                @else
                                    <div class="text-gray-500 text-sm">Tipe tidak dikenali.</div>
                                @endif

                                {{-- Footer skor/status --}}
                                <div class="mt-3 flex items-center gap-3 text-sm">
                                    <div>Nilai : <span class="font-semibold">{{ $a['score'] }}</span></div>
                                    <div> Status :
                                        @if ($a['status'] === 'correct')
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-green-100 text-green-700">Benar</span>
                                        @elseif ($a['status'] === 'wrong')
                                            <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700">Salah</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500">Tidak ada jawaban.</div>
                        @endforelse
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
