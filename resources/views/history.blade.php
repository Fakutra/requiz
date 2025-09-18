<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">
            <div>
                <h1 class="mb-4 font-bold sm:text-4xl text-3xl">Riwayat Lamaran</h1>
            </div>

            {{-- BANNER: Status --}}
            @if (session('status'))
            <div class="mb-4 rounded-lg bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200 px-4 py-2 text-sm">
                {{ session('status') }}
            </div>
            @endif

            {{-- BANNER: Validation errors --}}
            @if ($errors->any())
            <div class="mb-4 rounded-lg bg-rose-50 text-rose-800 ring-1 ring-inset ring-rose-200 px-4 py-3 text-sm">
                <div class="font-semibold mb-1">Periksa input:</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="space-y-6 mt-6">
                @forelse ($applicants as $applicant)
                {{-- CARD --}}
                <article class="rounded-2xl border border-zinc-200 bg-white shadow-sm p-6 mb-6">
                    {{-- HEADER --}}
                    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-zinc-100">
                        <div>
                            <h3 class="text-xl font-bold text-zinc-900">
                                {{ $applicant->position->name ?? 'Posisi Dihapus' }}
                            </h3>
                            <p class="text-sm text-zinc-500 mt-1">
                                Dilamar pada: {{ $applicant->created_at->translatedFormat('l, d F Y') }}
                            </p>
                        </div>

                        @php
                        $status = $applicant->status;
                        $badgeMap = [
                        'Menerima Offering' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Seleksi Administrasi' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Tes Tulis' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Technical Test' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Interview' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',

                        'Tidak Lolos Seleksi Administrasi' => 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200',
                        'Tidak Lolos Tes Tulis' => 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200',
                        'Tidak Lolos Technical Test' => 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200',
                        'Tidak Lolos Interview' => 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200',
                        'Menolak Offering' => 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200',
                        ];
                        $badgeClass = $badgeMap[$status] ?? 'bg-sky-100 text-sky-800 ring-1 ring-inset ring-sky-200';
                        @endphp

                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </header>

                    {{-- PROGRESS TRACKER --}}
                    <section>
                        <h4 class="text-sm font-semibold text-zinc-700 mb-4 tracking-wide">PROSES SELEKSI</h4>

                        @php
                        $stageMapping = [
                        'Seleksi Administrasi' => ['index' => 0, 'result' => 'pending'],
                        'Tidak Lolos Seleksi Administrasi' => ['index' => 0, 'result' => 'failed'],
                        'Tes Tulis' => ['index' => 1, 'result' => 'pending'],
                        'Tidak Lolos Tes Tulis' => ['index' => 1, 'result' => 'failed'],
                        'Seleksi Tes Praktek' => ['index' => 2, 'result' => 'pending'],
                        'Tidak Lolos Technical Test' => ['index' => 2, 'result' => 'failed'],
                        'Interview' => ['index' => 3, 'result' => 'pending'],
                        'Tidak Lolos Interview' => ['index' => 3, 'result' => 'failed'],
                        'Offering' => ['index' => 4, 'result' => 'pending'],
                        'Menolak Offering' => ['index' => 4, 'result' => 'failed'],
                        'Menerima Offering' => ['index' => 4, 'result' => 'passed'],
                        ];
                        $info = $stageMapping[$applicant->status] ?? ['index' => -1, 'result' => 'unknown'];
                        $currentIndex = $info['index'];
                        $currentResult = $info['result'];
                        $stages = ['Seleksi Administrasi','Tes Tulis','Technical Test','Interview','Offering'];
                        @endphp

                        <div class="flex sm:items-center sm:flex-row flex-col gap-3 sm:gap-4">
                            @foreach ($stages as $i => $name)
                            @php
                            $isPassed = $i < $currentIndex || ($i===$currentIndex && $currentResult==='passed' );
                                $isCurrent=$i===$currentIndex && $currentResult==='pending' ;
                                $isFailed=$i===$currentIndex && $currentResult==='failed' ;
                                $active=$isPassed || $isCurrent;
                                @endphp

                                {{-- Node --}}
                                <div class="flex min-w-0 flex-col items-center">
                                <div class="grid place-items-center w-10 h-10 rounded-full
                @if($isFailed) bg-rose-500 text-white
                @elseif($isPassed) bg-emerald-500 text-white
                @elseif($isCurrent) bg-[#009DA9] text-white ring-4 ring-[#009DA9]/15
                @else bg-zinc-200 text-zinc-500 @endif">
                                    @if ($isFailed)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    @elseif ($isPassed)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    @else
                                    <span class="text-sm font-semibold">{{ $i + 1 }}</span>
                                    @endif
                                </div>
                                <p class="mt-2 w-24 text-center text-xs leading-snug
                @if($isFailed) text-rose-600 font-semibold
                @elseif($isPassed) text-zinc-600
                @elseif($isCurrent) text-[#009DA9] font-semibold
                @else text-zinc-400 @endif">
                                    {{ $name }}
                                </p>
                        </div>

                        {{-- Connector --}}
                        @unless ($loop->last)
                        <div class="flex-1 h-0.5 @if($active) bg-[#009DA9] @else bg-zinc-200 @endif"></div>
                        @endunless
                        @endforeach
            </div>
            </section>

            {{-- TES TULIS --}}
            @if ($applicant->status === 'Tes Tulis' && $applicant->position && $applicant->position->test)
            @php
            $t = $applicant->position->test;
            $now = now();
            $open = $t->test_date;
            $close = $t->test_closed;
            $end = $t->test_end;

            $tr = \App\Models\TestResult::where('applicant_id', $applicant->id)->where('test_id', $t->id)->first();
            $hasStarted = $tr && ($tr->started_at ||
            \App\Models\TestSectionResult::where('test_result_id', $tr->id)->whereNotNull('started_at')->exists());

            $inWindow = $open && $close ? $now->between($open, $close, true) : false;
            $beforeEnd = $end ? $now->lt($end) : true;
            $canEnter = $inWindow || ($hasStarted && $beforeEnd);

            $signedUrl = \Illuminate\Support\Facades\URL::signedRoute('quiz.start', ['slug' => $t->slug]);
            @endphp

            <div class="mt-6 pt-4 border-t border-zinc-100 text-center space-y-3">
                <p class="text-sm text-zinc-600">
                    Tes akan dimulai dari
                    <span class="font-semibold text-sky-700">{{ optional($open)?->translatedFormat('l, d F Y, H:i') ?? '—' }}</span>
                    sampai
                    <span class="font-semibold text-sky-700">{{ optional($close)?->translatedFormat('l, d F Y, H:i') ?? '—' }}</span><br>
                    Batas akhir pengerjaan tes dapat dilakukan sampai:
                    <span class="font-semibold text-rose-600">{{ optional($end)?->translatedFormat('l, d F Y, H:i') ?? '—' }}</span>
                </p>

                <button
                    type="button"
                    class="start-test-btn inline-flex items-center justify-center px-6 h-10 rounded-full text-sm font-medium
                   shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed
                   bg-sky-600 text-white hover:bg-sky-700 active:bg-sky-800"
                    data-url="{{ $signedUrl }}"
                    data-title="{{ $applicant->position->name }}"
                    {{ $canEnter ? '' : 'disabled' }}>
                    Mulai Tes
                </button>

                <p class="text-xs text-zinc-500">
                    Tombol akan tersedia selama periode dibuka. Setelah pengerjaan dimulai, maka pengerjaan tetap bisa dilanjutkan sampai batas akhir periode.</p>
            </div>
            @endif

            {{-- TECHNICAL TEST --}}
            @if ($applicant->status === 'Technical Test')
            @php
            $sched = optional($applicant->position->technicalSchedules ?? collect())->sortByDesc('schedule_date')->first();
            $latest = $sched ? $sched->answers->where('applicant_id', $applicant->id)->sortByDesc('submitted_at')->first() : null;
            $now = now();
            $withinDeadline = !$sched?->upload_deadline || $now->lte($sched->upload_deadline);
            @endphp

            <div class="mt-6 rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <h4 class="text-sm font-semibold text-indigo-800 mb-3">Technical Test</h4>

                @if ($sched)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-zinc-600">Jadwal</div>
                        <div class="font-medium text-zinc-900">
                            {{ optional($sched->schedule_date)?->translatedFormat('l, d F Y, H:i') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-zinc-600">Zoom</div>
                        <div class="font-medium">
                            <a href="{{ $sched->zoom_link }}" target="_blank" class="text-sky-700 hover:underline">Buka Link Zoom</a>
                            @if ($sched->zoom_id)
                            <div class="text-zinc-500">ID: {{ $sched->zoom_id }}</div>
                            @endif
                            @if ($sched->zoom_passcode)
                            <div class="text-zinc-500">Passcode: {{ $sched->zoom_passcode }}</div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-zinc-600">Batas Upload</div>
                        <div class="font-medium {{ $sched->upload_deadline && $now->gt($sched->upload_deadline) ? 'text-rose-600' : 'text-zinc-900' }}">
                            {{ optional($sched->upload_deadline)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                        </div>
                    </div>
                </div>

                @if ($sched->keterangan)
                <div class="mt-4 text-sm text-zinc-700">
                    <div class="font-semibold text-zinc-800">Keterangan:</div>
                    <div class="whitespace-pre-line">{{ $sched->keterangan }}</div>
                </div>
                @endif

                @if ($latest)
                <div class="mt-5 text-sm text-zinc-700 space-y-1">
                    <div class="font-medium">Upload Terbaru:</div>
                    <div>PDF:
                        <a class="text-sky-700 hover:underline"
                            href="{{ \Illuminate\Support\Facades\Storage::url($latest->answer_path) }}" target="_blank">
                            Lihat berkas
                        </a>
                    </div>
                    <div>Rekaman Layar:
                        <a class="text-sky-700 hover:underline" href="{{ $latest->screen_record_url }}" target="_blank">
                            Buka tautan
                        </a>
                    </div>
                    <div class="text-zinc-500">
                        Dikumpulkan: {{ $latest->submitted_at->translatedFormat('d F Y, H:i') }}
                    </div>
                </div>
                @endif

                <div class="mt-5">
                    <button type="button"
                        class="px-5 h-10 inline-flex items-center rounded-full bg-indigo-600 text-white text-sm font-medium
                       hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        data-open-upload="{{ $sched->id }}" {{ $withinDeadline ? '' : 'disabled' }}>
                        {{ $latest ? 'Upload Ulang Jawaban' : 'Upload Jawaban Technical Test' }}
                    </button>
                    <p class="text-xs text-zinc-500 mt-2">Format PDF + link Google Drive rekaman layar.</p>
                </div>

                {{-- Modal Upload --}}
                <div id="uploadModal-{{ $sched->id }}" class="fixed inset-0 z-50 hidden">
                    <div class="absolute inset-0 bg-black/50" data-close-upload="{{ $sched->id }}"></div>
                    <div class="relative mx-auto my-12 max-w-md rounded-2xl bg-white p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-zinc-900">Upload Jawaban Technical Test</h3>

                        <form class="mt-4 space-y-4" method="POST"
                            action="{{ route('technical.answers.store', $sched) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

                            <div>
                                <label class="block text-sm font-medium text-zinc-700">File Jawaban (PDF)</label>
                                <input type="file" name="answer_pdf" accept="application/pdf" required
                                    class="mt-1 block w-full text-sm rounded-lg border border-zinc-300 p-2">
                                <p class="mt-1 text-xs text-zinc-500">Maks 10MB. Format PDF.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-zinc-700">Link Rekaman Layar (Google Drive)</label>
                                <input type="url" name="screen_record_url" required
                                    placeholder="https://drive.google.com/..."
                                    value="{{ old('screen_record_url', optional($latest)->screen_record_url) }}"
                                    class="mt-1 block w-full text-sm rounded-lg border border-zinc-300 p-2">
                            </div>

                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button"
                                    class="px-4 h-10 rounded-lg border border-zinc-300 text-zinc-700 hover:bg-zinc-50"
                                    data-close-upload="{{ $sched->id }}">Batal</button>
                                <button type="submit"
                                    class="px-4 h-10 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
                                    {{ $withinDeadline ? '' : 'disabled' }}>Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <p class="text-sm text-zinc-600">Jadwal Technical Test belum ditentukan untuk posisi ini.</p>
                @endif
            </div>
            @endif

            {{-- INTERVIEW --}}
            @if ($applicant->status === 'Interview' && $applicant->position)
            @php
            $iSch = ($applicant->position->interviewSchedules ?? collect())->sortByDesc('schedule_start')->first();
            $now = now();
            $start = $iSch?->schedule_start;
            $end = $iSch?->schedule_end;
            $earlyMinutes = 10;
            $canEarly = $start ? $now->gte($start->copy()->subMinutes($earlyMinutes)) : false;
            $inWindow = $start && $end ? $now->between($start, $end, true) : false;
            $canJoin = $iSch && $iSch->zoom_link ? $inWindow || $canEarly : false;
            @endphp

            <div class="mt-6 rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <h4 class="text-sm font-semibold text-indigo-800 mb-3">Interview</h4>

                @if ($iSch)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-zinc-600">Jadwal</div>
                        <div class="font-medium text-zinc-900">
                            {{ $start?->translatedFormat('l, d F Y, H:i') ?? '—' }} —
                            {{ $end?->translatedFormat('H:i') ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-zinc-600">Meeting</div>
                        <div class="font-medium">
                            @if ($iSch->zoom_link)
                            <a href="{{ $iSch->zoom_link }}" target="_blank" class="text-sky-700 hover:underline">Buka Link Zoom</a>
                            @else
                            <span class="text-zinc-500">Belum ada link</span>
                            @endif
                            @if ($iSch->zoom_id || $iSch->zoom_passcode)
                            <div class="text-zinc-500">ID: {{ $iSch->zoom_id ?? '—' }}</div>
                            <div class="text-zinc-500">Passcode: {{ $iSch->zoom_passcode ?? '—' }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <p class="text-sm text-zinc-600">Jadwal Interview belum ditentukan untuk posisi ini.</p>
                @endif
            </div>
            @endif
            </article>
            @empty
            <div class="text-zinc-700">Anda belum pernah melamar pekerjaan apapun.</div>
            @endforelse
        </div>

        {{-- SweetAlert sukses kirim lamaran --}}
        @push('scripts')
        @if (session('success'))
        <script>
            window.addEventListener('load', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Selamat, lamaran anda telah berhasil dikirim!',
                    confirmButtonColor: '#009DA9'
                });
            });
        </script>
        @endif

        {{-- Modal handlers (upload & start test) --}}
        <script>
            document.addEventListener('click', (e) => {
                const openU = e.target.closest('[data-open-upload]');
                const closeU = e.target.closest('[data-close-upload]');
                if (openU) {
                    const id = openU.getAttribute('data-open-upload');
                    document.getElementById(`uploadModal-${id}`)?.classList.remove('hidden');
                }
                if (closeU) {
                    const id = closeU.getAttribute('data-close-upload');
                    document.getElementById(`uploadModal-${id}`)?.classList.add('hidden');
                }

                const startBtn = e.target.closest('.start-test-btn');
                if (startBtn) {
                    const url = startBtn.dataset.url;
                    const title = startBtn.dataset.title || 'Mulai Tes?';
                    const m = document.getElementById('startTestModal');
                    document.getElementById('startTestTitle').textContent = title;
                    m.classList.remove('hidden');
                    const confirm = document.getElementById('confirmStartBtn');
                    const handler = () => {
                        window.location.href = url;
                    };
                    confirm.addEventListener('click', handler, {
                        once: true
                    });
                    m.querySelector('[data-close-start-modal]').addEventListener('click', () => m.classList.add('hidden'), {
                        once: true
                    });
                }
            });
        </script>
        @endpush

        {{-- MODAL: Konfirmasi Mulai Tes --}}
        <div id="startTestModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="startTestTitle">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-close-start-modal></div>
            <div class="relative mx-auto my-12 max-w-md bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-semibold text-zinc-900" id="startTestTitle">Mulai Tes?</h3>
                <p class="text-sm text-zinc-600 mt-2">
                    Anda hanya dapat mengerjakan tes ini <span class="font-semibold">SATU KALI</span>. Setelah dimulai, waktu akan berjalan.
                </p>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="px-4 h-10 rounded-lg border border-zinc-300 text-zinc-700 hover:bg-zinc-50" data-close-start-modal>Batal</button>
                    <button type="button" class="px-4 h-10 rounded-lg bg-sky-600 text-white hover:bg-sky-700" id="confirmStartBtn">Mulai</button>
                </div>
            </div>
        </div>

        {{-- MODAL: Sukses Upload Jawaban (opsional: tampil via session) --}}
        @if (session('success'))
        <div id="successUploadModal" class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50" data-close-success></div>
            <div class="relative mx-auto my-12 max-w-md bg-white rounded-2xl shadow-xl p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900">Berhasil</h3>
                        <p class="text-sm text-zinc-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button type="button" class="px-4 h-10 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700" data-close-success>OK</button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        // ===== Modal "Mulai Tes" (Tes Tulis)
        (() => {
            // ===== helpers
            const lockScroll = () => document.body.classList.add('overflow-hidden');
            const unlockScroll = () => document.body.classList.remove('overflow-hidden');

            // ===== START TEST MODAL
            const startModal = document.getElementById('startTestModal');
            const startTitleEl = document.getElementById('startTestTitle');
            const startConfirm = document.getElementById('confirmStartBtn');
            let targetUrl = null,
                lastFocus = null;

            function openStart(url, title) {
                targetUrl = url;
                startTitleEl.textContent = title ? `Mulai Tes: ${title}?` : 'Mulai Tes?';
                lastFocus = document.activeElement;
                startModal.classList.remove('hidden');
                lockScroll();
                // fokus-in tombol konfirmasi
                startConfirm?.focus();

                // ESC close (auto lepas pas sekali dipakai)
                const escHandler = (e) => {
                    if (e.key === 'Escape') closeStart();
                };
                document.addEventListener('keydown', escHandler, {
                    once: true
                });

                // click overlay / tombol close (auto lepas)
                const clickHandler = (e) => {
                    if (e.target === startModal || e.target.hasAttribute('data-close-start-modal')) {
                        closeStart();
                    }
                };
                startModal.addEventListener('click', clickHandler, {
                    once: true
                });
            }

            function closeStart() {
                startModal.classList.add('hidden');
                unlockScroll();
                targetUrl = null;
                lastFocus?.focus?.();
            }

            startConfirm?.addEventListener('click', () => {
                if (targetUrl) location.href = targetUrl;
            });

            // ===== UPLOAD MODAL (generic)
            function openUpload(id) {
                const m = document.getElementById('uploadModal-' + id);
                if (!m) return;
                m.classList.remove('hidden');
                lockScroll();

                const escHandler = (e) => {
                    if (e.key === 'Escape') closeUpload(m, id);
                };
                document.addEventListener('keydown', escHandler, {
                    once: true
                });

                const clickHandler = (e) => {
                    if (e.target === m || e.target.matches(`[data-close-upload="${id}"]`)) {
                        closeUpload(m, id);
                    }
                };
                m.addEventListener('click', clickHandler, {
                    once: true
                });
            }

            function closeUpload(m) {
                m.classList.add('hidden');
                unlockScroll();
            }

            // ===== SUCCESS MODAL (auto show if exists)
            const successModal = document.getElementById('successUploadModal');
            if (successModal) {
                lockScroll();
                const close = () => {
                    successModal.remove();
                    unlockScroll();
                };
                successModal.addEventListener('click', (e) => {
                    if (e.target === successModal || e.target.hasAttribute('data-close-success')) close();
                }, {
                    once: true
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') close();
                }, {
                    once: true
                });
            }

            // ===== EVENT DELEGATION (1 listener untuk semuanya)
            document.addEventListener('click', (e) => {
                const startBtn = e.target.closest('.start-test-btn');
                if (startBtn && !startBtn.hasAttribute('disabled')) {
                    openStart(startBtn.dataset.url, startBtn.dataset.title);
                    return;
                }
                const upBtn = e.target.closest('[data-open-upload]');
                if (upBtn && !upBtn.hasAttribute('disabled')) {
                    openUpload(upBtn.getAttribute('data-open-upload'));
                }
            });
        })();
    </script>
</x-guest-layout>