<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Application') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse ($applicants as $applicant)
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 border border-gray-200">
                        {{-- Header kartu --}}
                        <div
                            class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $applicant->position->name ?? 'Posisi Dihapus' }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Dilamar pada: {{ $applicant->created_at->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
                            <div class="mt-3 sm:mt-0 flex items-center">
                                @php
                                    $status = $applicant->status;
                                    $badgeColor = match ($status) {
                                        'Menerima Offering',
                                        'Lolos Seleksi Administrasi',
                                        'Lolos Tes Tulis',
                                        'Lolos Technical Test',
                                        'Lolos Interview'
                                            => 'bg-green-100 text-green-800',
                                        'Tidak Lolos Seleksi Administrasi',
                                        'Tidak Lolos Tes Tulis',
                                        'Tidak Lolos Technical Test',
                                        'Tidak Lolos Interview',
                                        'Menolak Offering'
                                            => 'bg-red-100 text-red-800',
                                        default => 'bg-blue-100 text-blue-800',
                                    };
                                @endphp
                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $badgeColor }}">
                                    {{ $status }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress tracker (tetap) --}}
                        <div>
                            <h4 class="text-md font-semibold text-gray-700 mb-4">PROSES SELEKSI</h4>
                            <div class="flex items-center justify-between">
                                @php
                                    $stageMapping = [
                                        'Seleksi Administrasi' => ['index' => 0, 'result' => 'pending'],
                                        'Tidak Lolos Seleksi Administrasi' => ['index' => 0, 'result' => 'failed'],
                                        'Tes Tulis' => ['index' => 1, 'result' => 'pending'],
                                        'Tidak Lolos Tes Tulis' => ['index' => 1, 'result' => 'failed'],
                                        'Technical Test' => ['index' => 2, 'result' => 'pending'],
                                        'Tidak Lolos Technical Test' => ['index' => 2, 'result' => 'failed'],
                                        'Interview' => ['index' => 3, 'result' => 'pending'],
                                        'Tidak Lolos Interview' => ['index' => 3, 'result' => 'failed'],
                                        'Offering' => ['index' => 4, 'result' => 'pending'],
                                        'Menolak Offering' => ['index' => 4, 'result' => 'failed'],
                                        'Menerima Offering' => ['index' => 4, 'result' => 'passed'],
                                    ];
                                    $currentStatusInfo = $stageMapping[$applicant->status] ?? [
                                        'index' => -1,
                                        'result' => 'unknown',
                                    ];
                                    $currentStageIndex = $currentStatusInfo['index'];
                                    $currentResult = $currentStatusInfo['result'];
                                    $stages = [
                                        'Seleksi Administrasi',
                                        'Tes Tulis',
                                        'Technical Test',
                                        'Interview',
                                        'Offering',
                                    ];
                                @endphp

                                @foreach ($stages as $index => $stageName)
                                    @php
                                        $isPassed =
                                            $index < $currentStageIndex ||
                                            ($index == $currentStageIndex && $currentResult == 'passed');
                                        $isCurrent = $index == $currentStageIndex && $currentResult == 'pending';
                                        $isFailed = $index == $currentStageIndex && $currentResult == 'failed';
                                        $isActive = $isPassed || $isCurrent;
                                    @endphp

                                    <div class="flex flex-col items-center flex-1 min-w-0">
                                        <div
                                            class="relative flex items-center justify-center w-10 h-10 rounded-full
                                            @if ($isFailed) bg-red-500 text-white
                                            @elseif ($isPassed) bg-green-500 text-white
                                            @elseif ($isCurrent) bg-blue-500 text-white ring-4 ring-blue-200
                                            @else bg-gray-200 text-gray-500 @endif">
                                            @if ($isFailed)
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @elseif ($isPassed)
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <span>{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <p
                                            class="text-center text-xs mt-2 w-24 whitespace-normal
                                            @if ($isFailed) text-red-600 font-semibold
                                            @elseif ($isPassed) text-gray-600
                                            @elseif ($isCurrent) text-blue-600 font-semibold
                                            @else text-gray-400 @endif">
                                            {{ $stageName }}
                                        </p>
                                    </div>

                                    @if (!$loop->last)
                                        <div
                                            class="flex-auto border-t-2 {{ $isActive ? 'border-green-500' : 'border-gray-200' }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Aksi Tes Tulis --}}
                        @if ($applicant->status === 'Tes Tulis' && $applicant->position && $applicant->position->test)
                            @php
                                $t = $applicant->position->test;
                                $now = now();
                                $open = $t->test_date;
                                $close = $t->test_closed;
                                $end = $t->test_end;

                                $tr = \App\Models\TestResult::where('applicant_id', $applicant->id)
                                    ->where('test_id', $t->id)
                                    ->first();

                                $hasStarted =
                                    $tr &&
                                    ($tr->started_at ||
                                        \App\Models\TestSectionResult::where('test_result_id', $tr->id)
                                            ->whereNotNull('started_at')
                                            ->exists());

                                $inWindow = $open && $close ? $now->between($open, $close, true) : false;
                                $beforeEnd = $end ? $now->lt($end) : true;
                                $canEnter = $inWindow || ($hasStarted && $beforeEnd);

                                $signedUrl = \Illuminate\Support\Facades\URL::signedRoute('quiz.start', [
                                    'slug' => $t->slug,
                                ]);
                            @endphp

                            <div class="mt-4 pt-4 border-t border-gray-100 text-center space-y-4">
                                <p class="text-sm text-gray-600">
                                    Buka:
                                    <span
                                        class="font-semibold text-blue-600">{{ optional($open)?->translatedFormat('l, d F Y, H:i') ?? '—' }}</span>
                                    —
                                    Tutup Tombol:
                                    <span
                                        class="font-semibold text-blue-600">{{ optional($close)?->translatedFormat('l, d F Y, H:i') ?? '—' }}</span>
                                    <br>
                                    Hard End:
                                    <span
                                        class="font-semibold text-red-600">{{ optional($end)?->translatedFormat('l, d F Y, H:i') ?? '—' }}</span>
                                </p>

                                <button type="button"
                                    class="start-test-btn inline-block px-6 py-2 bg-blue-600 text-white font-medium text-sm leading-tight rounded-full shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 transition duration-150 ease-in-out {{ $canEnter ? '' : 'opacity-50 cursor-not-allowed hover:bg-blue-600' }}"
                                    data-url="{{ $signedUrl }}" data-title="{{ $applicant->position->name }}"
                                    {{ $canEnter ? '' : 'disabled' }}>
                                    Mulai Tes
                                </button>

                                <div class="text-xs text-gray-500">
                                    Tombol aktif hanya pada rentang jadwal buka hingga tutup tombol.
                                    Jika sudah mulai, Anda tetap bisa melanjutkan hingga waktu <em>Hard End</em>.
                                </div>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 text-center">
                            <p>Anda belum pernah melamar pekerjaan apapun.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL: Konfirmasi Mulai Tes --}}
    <div id="startTestModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative mx-auto my-12 max-w-md bg-white rounded-2xl shadow-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800" id="startTestTitle">Mulai Tes?</h3>
            <p class="text-sm text-gray-600 mt-2">
                Anda hanya dapat mengerjakan tes ini <span class="font-semibold">SATU KALI</span>. Setelah dimulai,
                waktu akan berjalan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                    data-close-start-modal>Batal</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
                    id="confirmStartBtn">Mulai</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('startTestModal');
            const confirmBtn = document.getElementById('confirmStartBtn');
            const titleEl = document.getElementById('startTestTitle');
            let targetUrl = null;

            function openModal(url, title) {
                targetUrl = url;
                titleEl.textContent = title ? `Mulai Tes: ${title}?` : 'Mulai Tes?';
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                targetUrl = null;
            }

            document.querySelectorAll('.start-test-btn').forEach(btn => {
                if (btn.hasAttribute('disabled')) return;
                btn.addEventListener('click', () => openModal(btn.dataset.url, btn.dataset.title));
            });

            confirmBtn.addEventListener('click', () => {
                if (targetUrl) window.location.href = targetUrl;
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.hasAttribute('data-close-start-modal')) closeModal();
            });
            document.addEventListener('keydown', (e) => {
                if (!modal.classList.contains('hidden') && e.key === 'Escape') closeModal();
            });
        })();
    </script>
</x-app-layout>
