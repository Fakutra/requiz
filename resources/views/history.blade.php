<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Application') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Status (tetap pakai banner) --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-yellow-50 text-yellow-800 px-4 py-2 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation errors (tetap pakai banner) --}}
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 text-sm">
                    <div class="font-semibold mb-1">Periksa input:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

                        {{-- Progress tracker --}}
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

                        {{-- Aksi: Tes Tulis (Mulai Tes) --}}
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

                            <div class="mt-6 pt-4 border-t border-gray-100 text-center space-y-4">
                                <p class="text-sm text-gray-600">
                                    Buka:
                                    <span class="font-semibold text-blue-600">
                                        {{ optional($open)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                                    </span>
                                    —
                                    Tutup Tombol:
                                    <span class="font-semibold text-blue-600">
                                        {{ optional($close)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                                    </span>
                                    <br>
                                    Hard End:
                                    <span class="font-semibold text-red-600">
                                        {{ optional($end)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                                    </span>
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

                        {{-- Aksi: Technical Test (jadwal per posisi + upload jawaban) --}}
                        @if ($applicant->status === 'Technical Test')
                            @php
                                $sched = optional($applicant->position->technicalSchedules ?? collect())
                                    ->sortByDesc('schedule_date')
                                    ->first();

                                $latest = $sched
                                    ? $sched->answers
                                        ->where('applicant_id', $applicant->id)
                                        ->sortByDesc('submitted_at')
                                        ->first()
                                    : null;

                                $now = now();
                                $withinDeadline = !$sched?->upload_deadline || $now->lte($sched->upload_deadline);
                            @endphp

                            <div class="mt-6 p-4 rounded-xl border border-indigo-200 bg-indigo-50">
                                <h4 class="text-md font-semibold text-indigo-800 mb-3">Technical Test</h4>

                                @if ($sched)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <div class="text-gray-600">Jadwal</div>
                                            <div class="font-medium text-gray-900">
                                                {{ optional($sched->schedule_date)?->translatedFormat('l, d F Y, H:i') }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-gray-600">Zoom</div>
                                            <div class="font-medium">
                                                <a href="{{ $sched->zoom_link }}" target="_blank"
                                                    class="text-blue-600 hover:underline">
                                                    Buka Link Zoom
                                                </a>
                                                @if ($sched->zoom_id)
                                                    <div class="text-gray-500">ID: {{ $sched->zoom_id }}</div>
                                                @endif
                                                @if ($sched->zoom_passcode)
                                                    <div class="text-gray-500">Passcode: {{ $sched->zoom_passcode }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-gray-600">Batas Upload</div>
                                            <div
                                                class="font-medium {{ $sched->upload_deadline && $now->gt($sched->upload_deadline) ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ optional($sched->upload_deadline)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                                            </div>
                                        </div>
                                    </div>

                                    @if ($sched->keterangan)
                                        <div class="mt-4 text-sm text-gray-700">
                                            <div class="font-semibold text-gray-800">Keterangan:</div>
                                            <div class="whitespace-pre-line">{{ $sched->keterangan }}</div>
                                        </div>
                                    @endif

                                    {{-- Ringkasan jawaban terbaru --}}
                                    @if ($latest)
                                        <div class="mt-5 text-sm text-gray-700">
                                            <div class="font-medium">Upload Terbaru:</div>
                                            <div>
                                                PDF:
                                                <a class="text-blue-600 hover:underline"
                                                    href="{{ \Illuminate\Support\Facades\Storage::url($latest->answer_path) }}"
                                                    target="_blank">Lihat berkas</a>
                                            </div>
                                            <div>
                                                Rekaman Layar:
                                                <a class="text-blue-600 hover:underline"
                                                    href="{{ $latest->screen_record_url }}" target="_blank">Buka
                                                    tautan</a>
                                            </div>
                                            <div class="text-gray-500">
                                                Dikumpulkan:
                                                {{ $latest->submitted_at->translatedFormat('d F Y, H:i') }}
                                            </div>
                                            {{-- @if (!is_null($latest->score))
                                                <div class="mt-1">
                                                    Nilai: <span class="font-semibold">{{ $latest->score }}</span>
                                                </div>
                                            @endif --}}
                                            {{-- @if ($latest->keterangan)
                                                <div class="mt-1 text-gray-600">
                                                    Catatan Admin: {{ $latest->keterangan }}
                                                </div>
                                            @endif --}}
                                        </div>
                                    @endif

                                    <div class="mt-5">
                                        <button type="button"
                                            class="px-5 py-2 rounded-full bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 {{ $withinDeadline ? '' : 'opacity-50 cursor-not-allowed' }}"
                                            data-open-upload="{{ $sched->id }}"
                                            {{ $withinDeadline ? '' : 'disabled' }}>
                                            {{ $latest ? 'Upload Ulang Jawaban' : 'Upload Jawaban Technical Test' }}
                                        </button>
                                        <div class="text-xs text-gray-500 mt-2">
                                            Format PDF untuk jawaban, dan tempelkan link Google Drive rekaman layar.
                                        </div>
                                    </div>

                                    {{-- Modal Upload --}}
                                    <div id="uploadModal-{{ $sched->id }}" class="fixed inset-0 z-50 hidden">
                                        {{-- overlay: klik luar menutup --}}
                                        <div class="absolute inset-0 bg-black/50"
                                            data-close-upload="{{ $sched->id }}"></div>
                                        <div class="relative mx-auto my-12 max-w-md bg-white rounded-2xl shadow-xl p-6">
                                            <h3 class="text-lg font-semibold text-gray-800">Upload Jawaban Technical
                                                Test</h3>
                                            <form class="mt-4" method="POST"
                                                action="{{ route('technical.answers.store', $sched) }}"
                                                enctype="multipart/form-data">
                                                @csrf

                                                {{-- Kirim applicant_id eksplisit --}}
                                                <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">File
                                                            Jawaban (PDF)</label>
                                                        <input type="file" name="answer_pdf" accept="application/pdf"
                                                            required
                                                            class="mt-1 block w-full text-sm border rounded-lg p-2">
                                                        <p class="text-xs text-gray-500 mt-1">Maks 10MB. Format PDF.</p>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Link
                                                            Rekaman Layar (Google Drive)</label>
                                                        <input type="url" name="screen_record_url" required
                                                            placeholder="https://drive.google.com/..."
                                                            value="{{ old('screen_record_url', optional($latest)->screen_record_url) }}"
                                                            class="mt-1 block w-full text-sm border rounded-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="mt-6 flex justify-end gap-2">
                                                    <button type="button"
                                                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                                                        data-close-upload="{{ $sched->id }}">Batal</button>
                                                    <button type="submit"
                                                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
                                                        {{ $withinDeadline ? '' : 'disabled' }}>Kirim</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-600">
                                        Jadwal Technical Test belum ditentukan untuk posisi ini.
                                    </div>
                                @endif
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

    {{-- MODAL: Konfirmasi Mulai Tes Tulis --}}
    <div id="startTestModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-close-start-modal></div>
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

    {{-- MODAL: Sukses Upload Jawaban (otomatis tampil jika session('success')) --}}
    @if (session('success'))
        <div id="successUploadModal" class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50" data-close-success></div>
            <div class="relative mx-auto my-12 max-w-md bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Berhasil</h3>
                        <p class="text-sm text-gray-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button type="button" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700"
                        data-close-success>OK</button>
                </div>
            </div>
        </div>
    @endif

    <script>
        // ===== Modal "Mulai Tes" (Tes Tulis)
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
                if (!btn.hasAttribute('disabled')) {
                    btn.addEventListener('click', () => openModal(btn.dataset.url, btn.dataset.title));
                }
            });

            confirmBtn.addEventListener('click', () => {
                if (targetUrl) window.location.href = targetUrl;
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.hasAttribute('data-close-start-modal')) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (!modal.classList.contains('hidden') && e.key === 'Escape') {
                    closeModal();
                }
            });
        })();

        // ===== Modal Upload Technical Test (generic handler untuk banyak jadwal)
        (function() {
            document.querySelectorAll('[data-open-upload]').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.hasAttribute('disabled')) return;
                    const id = btn.getAttribute('data-open-upload');
                    const modal = document.getElementById('uploadModal-' + id);
                    if (!modal) return;

                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');

                    // close via overlay or close button
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal || e.target.matches(
                                `[data-close-upload="${id}"]`)) {
                            modal.classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                        }
                    });

                    // close via ESC
                    const escHandler = (e) => {
                        if (!modal.classList.contains('hidden') && e.key === 'Escape') {
                            modal.classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                            document.removeEventListener('keydown', escHandler);
                        }
                    };
                    document.addEventListener('keydown', escHandler);
                });
            });
        })();

        // ===== Modal Success Upload (auto show jika ada di DOM)
        (function() {
            const modal = document.getElementById('successUploadModal');
            if (!modal) return;

            const close = () => {
                modal.parentNode && modal.parentNode.removeChild(modal);
                document.body.classList.remove('overflow-hidden');
            };

            document.body.classList.add('overflow-hidden');

            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.hasAttribute('data-close-success')) {
                    close();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            }, {
                once: true
            });
        })();
    </script>
</x-app-layout>
