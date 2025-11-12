<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">

            <h1 class="mb-4 font-bold sm:text-4xl text-3xl">Riwayat Lamaran</h1>

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

                {{-- ===== CARD ===== --}}
                <article class="rounded-2xl border border-zinc-200 bg-white shadow-sm p-6">
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
                    @php
                    $stageMapping = [
                    'Seleksi Administrasi' => ['index' => 0, 'result' => 'pending'],
                    'Tidak Lolos Seleksi Administrasi' => ['index' => 0, 'result' => 'failed'],

                    'Tes Tulis' => ['index' => 1, 'result' => 'pending'],
                    'Tidak Lolos Tes Tulis' => ['index' => 1, 'result' => 'failed'],

                    'Technical Test' => ['index' => 2, 'result' => 'pending'],
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

                    <section class="mb-5">
                        <h4 class="text-sm font-semibold text-zinc-700 mb-4 tracking-wide">PROSES SELEKSI</h4>

                        <div class="flex sm:items-center sm:flex-row flex-col gap-3 sm:gap-4">
                            @foreach ($stages as $i => $name)
                            @php
                            $isPassed = $i < $currentIndex || ($i===$currentIndex && $currentResult==='passed' );
                                $isCurrent=$i===$currentIndex && $currentResult==='pending' ;
                                $isFailed=$i===$currentIndex && $currentResult==='failed' ;
                                $active=$isPassed || $isCurrent;

                                $highlight=$isCurrent ? 'shadow-[0_0_0_6px_rgba(2,132,199,.12)] animate-pulse' : '' ;
                                @endphp

                                {{-- Node --}}
                                <div class="flex min-w-0 flex-col items-center">
                                <div class="grid place-items-center w-10 h-10 rounded-full transition-all duration-300 {{ $highlight }}
                                        @if($isFailed) bg-rose-500 text-white
                                        @elseif($isPassed) bg-emerald-500 text-white
                                        @elseif($isCurrent) bg-[#009DA9] text-white ring-4 ring-[#009DA9]/15
                                        @else bg-zinc-200 text-zinc-500 @endif"
                                    @if($isCurrent) aria-current="step" @endif
                                    aria-label="{{ $name }}: {{ $isFailed ? 'gagal' : ($isPassed ? 'selesai' : ($isCurrent ? 'berjalan' : 'belum')) }}">
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
                                        @elseif($isCurrent) text-[#009DA9] font-bold
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

            {{-- SELEKSI ADMINISTRASI: Sedang diproses --}}
            @if ($applicant->status === 'Seleksi Administrasi')
            <section class="mt-6 rounded-xl border border-[#009DA9] p-4" style="background-color:#EFFEFF;">

                <h4 class="text-sm font-semibold text-[#009DA9] mb-3">Seleksi Administrasi</h4>

                <div class="text-sm text-zinc-700 leading-relaxed">
                        Lamaran Anda sedang dalam proses verifikasi berkas oleh tim rekrutmen. Hasil seleksi administrasi akan diumumkan melalui
                        <strong>email resmi</strong> yang terdaftar pada akun Anda. Harap periksa kotak masuk secara berkala — termasuk folder <em>Spam/Promotions</em>.
                    </div>

            </section>
            @endif

            @php
            // flags per section
            $showQuiz = ($applicant->status === 'Tes Tulis') && optional($applicant->position)->test;
            $showTech = in_array($applicant->status, ['Technical Test','Seleksi Tes Praktek']) && $applicant->position;
            $showInterview = ($applicant->status === 'Interview') && $applicant->position;
            @endphp

            {{-- TES TULIS: Belum dibuat --}}
            @if ($applicant->status === 'Tes Tulis' && ! optional($applicant->position)->test)
            <section class="mt-6 rounded-xl border border-[#009DA9] p-4" style="background-color:#EFFEFF;">

                <h4 class="text-sm font-semibold text-[#009DA9] mb-3">Tes Tulis (Belum Tersedia)</h4>

                <div class="text-sm text-zinc-700 leading-relaxed">
                    Anda telah masuk ke tahap <strong>Tes Tulis</strong>, namun saat ini tes belum disiapkan oleh tim rekrutmen.
                    Informasi terkait jadwal, instruksi, dan akses pengerjaan akan ditampilkan pada halaman ini setelah tes tersedia.
                </div>
            </section>
            @endif

            @if ($showQuiz || $showTech || $showInterview)
            <section class="mt-6 rounded-xl border border-[#009DA9] p-4" style="background-color:#EFFEFF;">

                {{-- ===== QUIZ ===== --}}
                @if ($showQuiz)
                @php
                $test = $applicant->position->test;
                $now = now();
                $open = $test->test_date;
                $close = $test->test_closed;
                $end = $test->test_end;

                $tr = \App\Models\TestResult::where('applicant_id',$applicant->id)
                ->where('test_id',$test->id)->first();

                $hasStarted = $tr && ($tr->started_at ||
                \App\Models\TestSectionResult::where('test_result_id',$tr->id)
                ->whereNotNull('started_at')->exists());

                $inWindow = ($open && $close) ? $now->between($open, $close, true) : false;
                $beforeEnd = $end ? $now->lt($end) : true;
                $canEnter = $inWindow || ($hasStarted && $beforeEnd);

                $startUrl = URL::signedRoute('quiz.intro', ['slug' => $test->slug]);
                @endphp

                <div>
                    <h4 class="text-sm font-semibold text-[#009DA9] mb-3">Tes Tulis (Online Quiz)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                        <div>
                            <div class="text-zinc-600">Dibuka</div>
                            <div class="font-medium text-zinc-900">{{ $open?->translatedFormat('l, d F Y, H:i') ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-zinc-600">Ditutup</div>
                            <div class="font-medium text-zinc-900">{{ $close?->translatedFormat('l, d F Y, H:i') ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-zinc-600">Batas Akhir Pengerjaan</div>
                            <div class="font-medium text-rose-600">{{ $end?->translatedFormat('l, d F Y, H:i') ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <a href="{{ $startUrl }}"
                            @if(!$canEnter) aria-disabled="true" @endif
                            class="px-5 h-10 inline-flex items-center rounded-lg bg-[#009DA9] text-white text-sm font-medium hover:bg-sky-700 {{ $canEnter ? '' : 'pointer-events-none opacity-50' }}">
                            {{ $hasStarted ? 'Lanjutkan Tes' : 'Mulai Tes' }}
                        </a>
                        @unless($canEnter)
                        <span class="text-xs text-zinc-500">Tombol aktif saat periode dibuka.</span>
                        @endunless
                    </div>
                </div>
                @endif

                {{-- divider --}}
                @if (($showQuiz && $showTech) || ($showQuiz && $showInterview))
                <div class="border-t border-[#009DA9]/20 my-4"></div>
                @endif

                {{-- ===== TECHNICAL TEST ===== --}}
                @if ($showTech)
                @php
                $now = now();
                $techSched = $techSched
                ?? optional($applicant->position->technicalSchedules ?? collect())
                ->sortByDesc('schedule_date')->first();

                $withinDeadline = isset($techSched->upload_deadline) ? $now->lte($techSched->upload_deadline) : true;
                $latestTech = $latestTech
                ?? \App\Models\TechnicalTestAnswer::where('applicant_id',$applicant->id)
                ->latest('submitted_at')->first();

                $modalKey = $techSched?->id ? $techSched->id . '-' . $applicant->id : 'x-' . $applicant->id;
                @endphp

                @if ($techSched)
                <div>
                    <h4 class="text-md font-semibold text-[#009DA9] mb-3">Technical Test</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <div class="text-zinc-600">Dijadwalkan pada</div>
                            <div class="font-medium text-zinc-900">
                                {{ $techSched->schedule_date?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                            </div>

                            @if (!empty($techSched->zoom_id) || !empty($techSched->zoom_passcode))
                            <div class="mt-2 text-gray-500 text-sm">
                                <div>ID: {{ $techSched->zoom_id ?? '—' }}</div>
                                <div>Passcode: {{ $techSched->zoom_passcode ?? '—' }}</div>
                            </div>
                            @endif
                        </div>

                        <div>
                            <div class="text-zinc-600">Batas Upload</div>
                            <div class="font-medium {{ ($techSched->upload_deadline && $now->gt($techSched->upload_deadline)) ? 'text-rose-600' : 'text-zinc-900' }}">
                                {{ $techSched->upload_deadline?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                            </div>
                        </div>

                        @if (!empty($techSched->module_url) || !empty($techSched->module_path))
                        <div>
                            <div class="text-zinc-600">Modul / Brief</div>
                            <div class="font-medium">
                                @if (!empty($techSched->module_url))
                                <a href="{{ $techSched->module_url }}" class="text-blue-600 hover:underline" target="_blank" rel="noopener noreferrer">Buka Modul</a>
                                @else
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($techSched->module_path) }}" class="text-blue-600 hover:underline" target="_blank" rel="noopener noreferrer">Unduh Modul</a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    @if ($latestTech)
                    <div class="mt-5 text-sm text-gray-700">
                        <div class="font-medium">Upload Terbaru:</div>
                        <div>PDF:
                            <a class="text-blue-600 hover:underline"
                                href="{{ \Illuminate\Support\Facades\Storage::url($latestTech->answer_path) }}"
                                target="_blank" rel="noopener noreferrer">Lihat berkas</a>
                        </div>
                        @if (!empty($latestTech->screen_record_url))
                        <div>Rekaman Layar:
                            <a class="text-blue-600 hover:underline" href="{{ $latestTech->screen_record_url }}" target="_blank" rel="noopener noreferrer">Buka tautan</a>
                        </div>
                        @endif
                        <div class="text-gray-500">
                            Dikumpulkan: {{ $latestTech->submitted_at?->translatedFormat('d F Y, H:i') ?? '—' }}
                        </div>
                    </div>
                    @endif

                    <div class="mt-6 flex flex-wrap items-center gap-2">
                        @if (!empty($techSched->zoom_link))
                        <button type="button"
                            onclick="window.open('{{ $techSched->zoom_link }}', '_blank', 'noopener,noreferrer')"
                            class="px-4 h-10 rounded-lg border border-[#009DA9] text-[#009DA9] text-sm font-medium hover:bg-[#009DA9]/10 inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            Buka Link Zoom
                        </button>
                        @else
                        <span class="text-gray-500">Belum ada link</span>
                        @endif

                        <button type="button"
                            class="px-4 h-10 inline-flex items-center rounded-lg bg-[#009DA9] text-white text-sm font-medium hover:bg-[#008a95] disabled:opacity-50 disabled:cursor-not-allowed"
                            data-open-upload="upload-{{ $techSched->id }}-{{ $applicant->id }}" {{ $withinDeadline ? '' : 'disabled' }}>
                            {{ $latestTech ? 'Upload Ulang Jawaban' : 'Upload Jawaban Technical Test' }}
                        </button>
                    </div>

                    {{-- Modal Upload tetap sama --}}
                    <div data-modal="upload-{{ $techSched->id }}-{{ $applicant->id }}" class="fixed inset-0 z-50 hidden">
                        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-close-upload="upload-{{ $techSched->id }}-{{ $applicant->id }}"></div>
                        <div class="relative mx-auto my-12 max-w-md rounded-2xl bg-white p-6 shadow-xl">
                            <h3 class="text-lg font-semibold text-zinc-900">Upload Jawaban Technical Test</h3>
                            <form class="mt-4 space-y-4" method="POST"
                                action="{{ route('technical.answers.store', $techSched) }}" enctype="multipart/form-data">
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
                                    <input type="url" name="screen_record_url" required placeholder="https://drive.google.com/..."
                                        value="{{ old('screen_record_url', optional($latestTech)->screen_record_url) }}"
                                        class="mt-1 block w-full text-sm rounded-lg border border-zinc-300 p-2">
                                </div>
                                <div class="mt-6 flex justify-end gap-2">
                                    <button type="button"
                                        class="px-4 h-10 rounded-lg border border-zinc-300 text-zinc-700 hover:bg-zinc-50"
                                        data-close-upload="upload-{{ $techSched->id }}-{{ $applicant->id }}">Batal</button>
                                    <button type="submit" class="px-4 h-10 rounded-lg bg-[#009DA9] text-white hover:bg-[#008a95]"
                                        {{ $withinDeadline ? '' : 'disabled' }}>Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-yellow-700 bg-yellow-50 border border-yellow-300 rounded-md p-3 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Jadwal Technical Test belum ditentukan.</span>
                </div>
                @endif
                @endif


                {{-- divider --}}
                @if (($showTech && $showInterview) || (!$showTech && $showQuiz && $showInterview))
                <div class="border-t border-[#009DA9]/20 my-4"></div>
                @endif

                {{-- ===== INTERVIEW ===== --}}
                @if ($showInterview)
                @php
                $iSch = optional($applicant->position->interviewSchedules ?? collect())
                ->sortByDesc('schedule_start')->first();
                $now = now();
                $start = $iSch?->schedule_start;
                $end = $iSch?->schedule_end;
                $earlyMinutes = 10;
                $canEarly = $start ? $now->gte($start->copy()->subMinutes($earlyMinutes)) : false;
                $inWindow = $start && $end ? $now->between($start, $end, true) : false;
                $canJoin = $iSch && $iSch->zoom_link ? ($inWindow || $canEarly) : false;
                @endphp

                @if ($iSch)
                <div class="text-md">
                    <h4 class="font-semibold text-[#009DA9] mb-3">Interview</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <div class="text-gray-600">Dijadwalkan pada</div>
                            <div class="font-medium text-gray-900">
                                {{ $start?->translatedFormat('l, d F Y, H:i') ?? '—' }} — {{ $end?->translatedFormat('H:i') ?? '—' }}
                            </div>
                        </div>

                        @if (!empty($iSch->keterangan))
                        <div class="text-gray-700">
                            <div class="font-semibold text-gray-800">Keterangan:</div>
                            <div class="whitespace-pre-line">{{ $iSch->keterangan }}</div>
                        </div>
                        @endif

                    </div>

                    <div class="mt-6">
                        @if ($iSch->zoom_id || $iSch->zoom_passcode)
                        <div class="text-gray-500 mt-2">ID: {{ $iSch->zoom_id ?? '—' }}</div>
                        <div class="text-gray-500">Passcode: {{ $iSch->zoom_passcode ?? '—' }}</div>
                        @endif
                        @if ($iSch->zoom_link)
                        <button type="button"
                            onclick="window.open('{{ $iSch->zoom_link }}', '_blank', 'noopener,noreferrer')"
                            class="px-5 h-10 mt-4 inline-flex items-center rounded-lg bg-[#009DA9] text-white text-sm font-medium hover:bg-[#008a95] hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            Buka Link Zoom
                        </button>
                        @else
                        <span class="text-gray-500">Belum ada link</span>
                        @endif
                    </div>

                </div>
                @else
                <div
                    class="text-yellow-700 bg-yellow-50 border border-yellow-300 rounded-md p-3 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <h4>Jadwal Interview belum ditentukan.</h4>
                </div>
                @endif
                @endif
            </section>
            @endif

            {{-- ===== OFFERING ===== --}}
            @if ($applicant->status === 'Offering' || $applicant->status === 'Menerima Offering')
            <section class="mt-6 rounded-xl border border-[#009DA9] p-4" style="background-color:#EFFEFF;">
                <h4 class="text-md font-semibold text-[#009DA9] mb-2">Offering</h4>
                <p class="text-zinc-800 text-md">Selamat anda lolos, silahkan untuk memeriksa email.</p>
            </section>
            @endif

            </article>

            @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <p>Anda belum pernah melamar pekerjaan apapun.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        const lockScroll = () => document.body.classList.add('overflow-hidden');
        const unlockScroll = () => document.body.classList.remove('overflow-hidden');

        document.addEventListener('click', (e) => {
            const openBtn = e.target.closest('[data-open-upload]');
            if (openBtn && !openBtn.hasAttribute('disabled')) {
                const key = openBtn.getAttribute('data-open-upload');

                // cari modal secara global, bukan cuma di dalam article
                const modal = document.querySelector(`[data-modal="${key}"]`);
                if (!modal) return;

                modal.classList.remove('hidden');
                lockScroll();

                const firstInput = modal.querySelector('input, button, textarea, select, a[href]');
                if (firstInput) firstInput.focus();

                const onClose = (evt) => {
                    if (evt.target.matches(`[data-close-upload="${key}"]`) || evt.target === modal) {
                        modal.classList.add('hidden');
                        unlockScroll();
                        modal.removeEventListener('click', onClose);
                        document.removeEventListener('keydown', onEsc);
                    }
                };
                const onEsc = (evt) => {
                    if (evt.key === 'Escape') {
                        modal.classList.add('hidden');
                        unlockScroll();
                        modal.removeEventListener('click', onClose);
                        document.removeEventListener('keydown', onEsc);
                    }
                };
                modal.addEventListener('click', onClose);
                document.addEventListener('keydown', onEsc);
            }
        });
    </script>

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
    @endpush
</x-guest-layout>