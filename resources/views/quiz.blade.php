{{-- resources/views/quiz.blade.php --}}
<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quiz: ') }} {{ $test->name }}
        </h2>
    </x-slot> --}}

    {{-- Include CSS khusus quiz --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/quiz.css') }}">
    @endpush

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">

                {{-- Progress Section Header --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        {{-- <div class="text-sm text-gray-500">Posisi: {{ $test->position->name }}</div> --}}
                        <div class="text-lg font-semibold text-gray-800">{{ $test->position->name }}</div>
                    </div>
                    <div class="quiz-timer">
                        <span class="text-sm text-gray-500 block">Sisa Waktu</span>
                        <span id="countdown" class="text-2xl font-bold"></span>
                    </div>
                </div>

                {{-- Visual Progress --}}
                <div class="flex items-center gap-2 mb-6">
                    @foreach ($sections as $s)
                        @php
                            $isCurrent = $s->id === $currentSection->id;
                            // aman-kan akses relasi (jika lazy loading dimatikan)
                            $srCollection = isset($testResult->sectionResults)
                                ? $testResult->sectionResults
                                : collect();
                            $sr = $srCollection->firstWhere('test_section_id', $s->id);
                            $isDone = $sr && $sr->finished_at;
                        @endphp
                        <div class="flex items-center flex-1 min-w-0">
                            <div
                                class="w-9 h-9 rounded-full flex items-center justify-center
                                {{ $isDone ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-blue-500 text-white ring-4 ring-blue-200' : 'bg-gray-200 text-gray-600') }}">
                                {{ $loop->iteration }}
                            </div>
                            @if (!$loop->last)
                                <div class="flex-1 h-1 {{ $isDone ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Form Soal --}}
                <form action="{{ route('quiz.submit', ['slug' => $test->slug]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <input type="hidden" name="section_id" value="{{ $currentSection->id }}">

                    @forelse ($questions as $idx => $q)
                        <div class="question-card">
                            <div class="question-header">
                                <div class="q-number">No. {{ $idx + 1 }}</div>
                                <div class="q-type">{{ $q->type }}</div>
                            </div>
                            <div class="question-body">
                                <div class="q-text">{!! nl2br(e($q->question)) !!}</div>

                                @if ($q->image_path)
                                    <div class="q-image">
                                        <img src="{{ asset('storage/' . $q->image_path) }}" alt="Gambar Soal">
                                    </div>
                                @endif

                                {{-- Render input sesuai tipe --}}
                                @if ($q->type === 'PG' || $q->type === 'Poin')
                                    <div class="q-options">
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $opt)
                                            @php $key = 'option_'.strtolower($opt); @endphp
                                            @if (!empty($q->$key))
                                                <label class="q-option">
                                                    <input type="radio" name="answers[{{ $q->id }}]"
                                                        value="{{ $opt }}">
                                                    <span class="opt-label">{{ $opt }}.</span>
                                                    <span class="opt-text">{{ $q->$key }}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif ($q->type === 'Multiple')
                                    <div class="q-options">
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $opt)
                                            @php $key = 'option_'.strtolower($opt); @endphp
                                            @if (!empty($q->$key))
                                                <label class="q-option">
                                                    <input type="checkbox" name="answers[{{ $q->id }}][]"
                                                        value="{{ $opt }}">
                                                    <span class="opt-label">{{ $opt }}.</span>
                                                    <span class="opt-text">{{ $q->$key }}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif ($q->type === 'Essay')
                                    <div>
                                        <textarea name="answers[{{ $q->id }}]" class="q-essay" rows="5"
                                            placeholder="Ketik jawaban Anda di sini..."></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500">Belum ada soal pada section ini.</div>
                    @endforelse

                    @php
                        // Ganti $loop->last (yang tidak tersedia di luar loop) dengan flag section terakhir
                        $isLastSection = optional($sections->last())->id === $currentSection->id;
                    @endphp

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary">
                            {{ $isLastSection ? 'Selesai Tes' : 'Simpan & Lanjut' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Timer & anti back-section --}}
    <script>
        (function() {
            const deadlineIso = @json($deadline);
            const end = new Date(deadlineIso).getTime();
            const el = document.getElementById('countdown');

            function pad(n) {
                return n.toString().padStart(2, '0');
            }

            function tick() {
                const now = new Date().getTime();
                let diff = Math.max(0, Math.floor((end - now) / 1000));
                const m = Math.floor(diff / 60);
                const s = diff % 60;
                if (el) el.textContent = pad(m) + ':' + pad(s);

                if (diff <= 0) {
                    // auto submit form saat waktu habis
                    const form = document.querySelector('form');
                    if (form) form.submit();
                    return;
                }
                requestAnimationFrame(tick);
            }
            tick();

            // Cegah kembali ke section sebelumnya
            window.history.pushState(null, '', window.location.href);
            window.addEventListener('popstate', function() {
                window.history.pushState(null, '', window.location.href);
            });
        })();
    </script>
</x-app-layout>
