<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">

            <h1 class="mb-4 font-bold text-3xl md:text-4xl">Riwayat Lamaran</h1>

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

                <article class="rounded-2xl border bg-white p-6">
                    {{-- HEADER --}}
                    <header class="flex flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-zinc-100">
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

                        <span class="inline-flex items-center px-3 py-1 text-xs sm:text-sm font-medium rounded-full max-w-[160px] truncate {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </header>

                    {{-- COLLAPSIBLE --}}
                    <details class="group" open>
                        <summary class="flex cursor-pointer items-center justify-between list-none">
                            <h4 class="text-sm font-semibold text-zinc-700 mb-4 tracking-wide">PROSES SELEKSI</h4>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-zinc-500 transition-transform duration-300 group-[open]:rotate-180">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </summary>

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
                    Lamaran Anda sedang dalam proses verifikasi berkas oleh tim rekrutmen. Hasil seleksi administrasi akan diperbarui pada halaman Riwayat Lamaran dan diumumkan melalui
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

                {{-- ===== QUIZ / TES TULIS ===== --}}
                @if ($showQuiz)
                @php
                $test = $applicant->position->test;
                $now = now();
                $open = $test->test_date;
                $close = $test->test_closed;
                $end = $test->test_end;

                // ambil sesi tes tulis (test_results)
                $tr = \App\Models\TestResult::where('applicant_id', $applicant->id)
                ->where('test_id', $test->id)
                ->latest('id') // kalau bisa ambil yang paling baru
                ->first();

                // sudah mulai kalau sudah ada session + sudah mulai salah satu section
                $hasStarted = $tr && (
                $tr->started_at ||
                \App\Models\TestSectionResult::where('test_result_id', $tr->id)
                ->whereNotNull('started_at')
                ->exists()
                );

                // ✅ dianggap selesai kalau finished_at KEISI
                $isFinished = $tr && $tr->finished_at;

                // window waktu tes
                $inWindow = ($open && $close) ? $now->between($open, $close, true) : false;
                $beforeEnd = $end ? $now->lt($end) : true;

                // ✅ hanya bisa masuk kalau BELUM selesai
                $canEnter = !$isFinished && ($inWindow || ($hasStarted && $beforeEnd));

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
                            class="px-5 h-10 inline-flex items-center rounded-lg bg-[#009DA9] text-white text-sm font-medium
                                {{ $canEnter ? 'hover:bg-sky-700' : 'pointer-events-none opacity-50' }}">
                            {{-- label tombol disesuaikan status --}}
                            {{ $isFinished
                                ? 'Tes Selesai'
                                : ($hasStarted ? 'Lanjutkan Tes' : 'Mulai Tes') }}
                        </a>

                        @if(!$canEnter && !$isFinished)
                        <span class="text-xs text-zinc-500">
                            Tombol aktif saat periode dibuka.
                        </span>
                        @elseif($isFinished)
                        <span class="text-xs text-zinc-500">
                            Anda telah menyelesaikan Tes Tulis.
                        </span>
                        @endif
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
                        
                        // 🔥 PERBAIKAN: Ambil data dari controller yang sudah dihitung
                        $techSched = $applicant->techSchedule ?? optional($applicant->position->technicalSchedules ?? collect())
                            ->sortByDesc('schedule_date')->first();
                        
                        // 🔥 PERBAIKAN: Gunakan status yang sudah dihitung di controller
                        $isActivePeriod = $applicant->techTestActive ?? false;
                        
                        // 🔥 PERBAIKAN: Ambil dari eager loaded answers (FIX N+1)
                        $latestTech = null;
                        if ($techSched && $techSched->answers) {
                            $latestTech = $techSched->answers
                                ->where('applicant_id', $applicant->id)
                                ->sortByDesc('submitted_at')
                                ->first();
                        }
                        
                        $modalKey = $techSched?->id ? $techSched->id . '-' . $applicant->id : 'x-' . $applicant->id;
                        
                        // Status message
                        $statusMessage = $applicant->techStatusMessage ?? '';
                    @endphp

                    @if ($techSched)
                    <div>
                        <h4 class="text-md font-semibold text-[#009DA9] mb-3">Technical Test</h4>

                        {{-- INFO STATUS PERIODE --}}
                        {{-- @if (!$isActivePeriod && $statusMessage)
                        <div class="mb-4 p-3 rounded-lg {{ $now->lt($techSched->schedule_date ?? now()) ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-rose-50 border border-rose-200 text-rose-700' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">{{ $statusMessage }}</span>
                            </div>
                        </div>
                        @endif --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="text-zinc-600">Dijadwalkan pada:</div>
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
                                <div class="text-zinc-600">Batas Upload:</div>
                                <div class="font-medium {{ ($techSched->upload_deadline && $now->gt($techSched->upload_deadline)) ? 'text-rose-600' : 'text-zinc-900' }}">
                                    {{ $techSched->upload_deadline?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                                </div>

                                {{-- ✅ TAMBAHKAN KETERANGAN/NOTE DI SINI --}}
                                @if($techSched->keterangan)
                                <div class="mt-2">
                                    <div class="text-zinc-600">Keterangan:</div>
                                    <div class="font-medium text-zinc-900 mt-1">
                                        {{ $techSched->keterangan }}
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- @if (!empty($techSched->module_url) || !empty($techSched->module_path))
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
                            @endif --}}
                        </div>

                        @if ($latestTech)
                        <div class="mt-5 text-sm text-gray-700">
                            <div class="font-medium">Upload Terbaru:</div>
                            <div>PDF:
                                <a class="text-blue-600 hover:underline"
                                    href="{{ '/storage/'.$latestTech->answer_path }}"
                                    target="_blank" rel="noopener noreferrer">
                                    Lihat berkas
                                </a>
                            </div>

                            @if (!empty($latestTech->screen_record_url))
                            <div>Rekaman Layar:
                                <a class="text-blue-600 hover:underline"
                                    href="{{ $latestTech->screen_record_url }}"
                                    target="_blank" rel="noopener noreferrer">
                                    Buka tautan
                                </a>
                            </div>
                            @endif

                            <div class="text-gray-500">
                                Dikumpulkan: {{ $latestTech->submitted_at?->translatedFormat('d F Y, H:i') ?? '—' }}
                            </div>
                        </div>
                        @endif

                        {{-- 🔥 PERBAIKAN: TOMBOL ZOOM DAN UPLOAD --}}
                        <div class="mt-6 flex flex-wrap items-center gap-2">
                            @if (!empty($techSched->zoom_link))
                                <button type="button"
                                    @if ($isActivePeriod)
                                        onclick="window.open('{{ $techSched->zoom_link }}', '_blank', 'noopener,noreferrer')"
                                    @else
                                        disabled
                                    @endif
                                    class="px-4 h-10 rounded-lg border border-[#009DA9] text-[#009DA9] text-sm font-medium hover:bg-[#009DA9]/10 inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    Buka Link Zoom
                                </button>
                            @else
                                <span class="text-gray-500">Belum ada link</span>
                            @endif

                            {{-- 🔥 PERBAIKAN: Tombol Upload menggunakan $isActivePeriod --}}
                            <button type="button"
                                class="px-4 h-10 inline-flex items-center rounded-lg bg-[#009DA9] text-white text-sm font-medium hover:bg-[#008a95] disabled:opacity-50 disabled:cursor-not-allowed"
                                data-open-upload="upload-{{ $techSched->id }}-{{ $applicant->id }}" 
                                {{ $isActivePeriod ? '' : 'disabled' }}>
                                {{ $latestTech ? 'Upload Ulang Jawaban' : 'Upload Jawaban Technical Test' }}
                            </button>
                            
                            {{-- INFO TAMBAHAN --}}
                            @if (!$isActivePeriod)
                                <span class="text-sm {{ $now->lt($techSched->schedule_date ?? now()) ? 'text-amber-600' : 'text-rose-600' }}">
                                    @if($now->lt($techSched->schedule_date ?? now()))
                                        🔒 Tombol akan aktif saat jadwal dimulai
                                    @else
                                        🔒 Periode technical test telah berakhir
                                    @endif
                                </span>
                            @endif
                        </div>

                        {{-- Modal Upload --}}
                        <div data-modal="upload-{{ $techSched->id }}-{{ $applicant->id }}" class="fixed inset-0 z-50 hidden">
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-close-upload="upload-{{ $techSched->id }}-{{ $applicant->id }}"></div>
                            <div class="relative mx-auto my-12 max-w-md rounded-2xl bg-white p-6 shadow-xl">
                                <h3 class="text-lg font-semibold text-zinc-900">Upload Jawaban Technical Test</h3>
                                
                                {{-- PERINGATAN JIKA TIDAK DALAM PERIODE AKTIF --}}
                                @if (!$isActivePeriod)
                                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-lg">
                                    <div class="flex items-center gap-2 text-rose-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        <span class="font-medium">Periode upload tidak aktif</span>
                                    </div>
                                    <p class="mt-1 text-sm text-rose-600">
                                        @if($now->lt($techSched->schedule_date ?? now()))
                                            Upload akan dibuka mulai {{ $techSched->schedule_date?->translatedFormat('l, d F Y, H:i') ?? '' }}
                                        @else
                                            Batas waktu upload telah berakhir pada {{ $techSched->upload_deadline?->translatedFormat('l, d F Y, H:i') ?? '' }}
                                        @endif
                                    </p>
                                </div>
                                @endif
                                
                                <form class="mt-4 space-y-4" method="POST"
                                    action="{{ route('technical.answers.store', $techSched) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700">File Jawaban</label>
                                        <input type="file" name="answer_pdf" accept="application/pdf" required
                                            class="mt-1 block w-full text-sm rounded-lg border border-zinc-300 p-2"
                                            {{ $isActivePeriod ? '' : 'disabled' }}>
                                        <p class="mt-1 text-xs text-zinc-500">Maks 1MB. Format PDF.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700">Link Rekaman Layar (Google Drive)</label>
                                        <input type="url" name="screen_record_url" required placeholder="https://drive.google.com/..."
                                            value="{{ old('screen_record_url', optional($latestTech)->screen_record_url) }}"
                                            class="mt-1 block w-full text-sm rounded-lg border border-zinc-300 p-2"
                                            {{ $isActivePeriod ? '' : 'disabled' }}>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-2">
                                        <button type="button"
                                            class="px-4 h-10 rounded-lg border border-zinc-300 text-zinc-700 hover:bg-zinc-50"
                                            data-close-upload="upload-{{ $techSched->id }}-{{ $applicant->id }}">Batal</button>
                                        <button type="submit" class="px-4 h-10 rounded-lg bg-[#009DA9] text-white hover:bg-[#008a95]"
                                            {{ $isActivePeriod ? '' : 'disabled' }}>Kirim</button>
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
                ->sortByDesc('schedule_start')
                ->first();

                $now = now();
                $start = $iSch?->schedule_start;
                $end = $iSch?->schedule_end;

                $earlyMinutes = 10;

                // waktu mulai join: 10 menit sebelum jadwal
                $joinOpenAt = $start ? $start->copy()->subMinutes($earlyMinutes) : null;

                // boleh join HANYA antara (start - 10 menit) s/d end
                $canJoin = false;
                if ($iSch && $iSch->zoom_link && $joinOpenAt && $end) {
                $canJoin = $now->between($joinOpenAt, $end, true);
                }
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
                            @if ($canJoin)
                            onclick="window.open('{{ $iSch->zoom_link }}', '_blank', 'noopener,noreferrer')"
                            @else
                            disabled
                            @endif
                            class="px-5 h-10 mt-4 inline-flex items-center rounded-lg bg-[#009DA9] text-white text-sm font-medium hover:bg-[#008a95] disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            Buka Link Zoom
                        </button>

                        @if (!$canJoin)
                        <p class="mt-2 text-xs text-gray-500">
                            Link Zoom hanya aktif saat jadwal interview berlangsung.
                        </p>
                        @endif
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
                @if (in_array($applicant->status, ['Offering', 'Menerima Offering', 'Menolak Offering']))
                <section class="mt-6 rounded-xl border border-[#009DA9] p-4 bg-[#EFFEFF]">

                    <h4 class="text-md font-semibold text-[#009DA9] mb-2">Offering</h4>

                    @php
                        $offering = $applicant->offering;
                    @endphp

                    {{-- GUARD: offering belum ada --}}
                    @if ($offering)
                    @php
                        $canAccessAcceptedOfferingDocs =
                            $applicant->status === 'Menerima Offering'
                            && $offering;
                    @endphp

                    {{-- TEXT STATUS OFFERING --}}
                    @if ($applicant->status === 'Offering')
                    <p class="text-zinc-800 text-md mb-4">
                        Selamat! Anda mendapatkan offering dari kami. Berikut rinciannya:
                    </p>
                    @endif

                    {{-- GRID DETAIL (SELALU TAMPIL UNTUK OFFERING & MENERIMA OFFERING) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        {{-- KIRI --}}
                        <div class="space-y-2">
                            <p class="flex flex-col">
                                <span class="text-gray-500">Bidang</span>
                                <span class="font-medium text-zinc-900">{{ $offering?->field?->name ?? '-' }}</span>
                            </p>
                            <p class="flex flex-col">
                                <span class="text-gray-500">Sub Bidang</span>
                                <span class="font-medium text-zinc-900">{{ $offering?->subField?->name ?? '-' }}</span>
                            </p>
                            <p class="flex flex-col">
                                <span class="text-gray-500">Seksi</span>
                                <span class="font-medium text-zinc-900">{{ $offering?->seksi?->name ?? '-' }}</span>
                            </p>
                            <p class="flex flex-col">
                                <span class="text-gray-500">Posisi</span>
                                <span class="font-medium text-zinc-900">{{ $offering?->job?->name ?? '-' }}</span>
                            </p>
                        </div>

                        {{-- TENGAH --}}
                        <div class="space-y-2">
                            <p class="flex flex-col">
                                <span class="text-gray-500">Gaji Pokok</span>
                                <span class="font-medium text-zinc-900">
                                    Rp {{ number_format((float)($offering?->gaji ?? 0), 0, ',', '.') }}
                                </span>
                            </p>
                            <p class="flex flex-col">
                                <span class="text-gray-500">Uang Makan</span>
                                <span class="font-medium text-zinc-900">
                                    Rp {{ number_format((float)($offering?->uang_makan ?? 0), 0, ',', '.') }}
                                </span>
                            </p>
                            <p class="flex flex-col">
                                <span class="text-gray-500">Uang Transport</span>
                                <span class="font-medium text-zinc-900">
                                    Rp {{ number_format((float)($offering?->uang_transport ?? 0), 0, ',', '.') }}
                                </span>
                            </p>
                            <p class="flex flex-col">
                                <span class="text-gray-500">Periode Kontrak</span>
                                <span class="font-medium text-zinc-900">
                                    {{ $offering?->kontrak_mulai?->translatedFormat('d F Y') ?? '-' }}
                                    —
                                    {{ $offering?->kontrak_selesai?->translatedFormat('d F Y') ?? '-' }}
                                </span>
                            </p>
                        </div>

                        {{-- KANAN --}}
                        @if ($applicant->status === 'Menerima Offering')
                        <div class="space-y-2">
                            <p class="flex flex-col">
                                <span class="text-gray-500">Link PKWT</span>
                                @if($offering?->link_pkwt)
                                <a href="{{ $offering->link_pkwt }}" target="_blank" rel="noopener noreferrer"
                                    class="text-[#009DA9] underline inline-flex items-center gap-1 break-all">
                                    Lihat PKWT
                                </a>
                                @else
                                <span class="text-gray-400 cursor-not-allowed flex items-center gap-1">
                                    Lihat PKWT
                                </span>
                                @endif
                            </p>

                            <p class="flex flex-col">
                                <span class="text-gray-500">Link Berkas</span>
                                @if($offering?->link_berkas)
                                <a href="{{ $offering->link_berkas }}" target="_blank" rel="noopener noreferrer"
                                    class="text-[#009DA9] underline inline-flex items-center gap-1 break-all">
                                    Lihat Berkas
                                </a>
                                @else
                                <span class="text-gray-400 cursor-not-allowed flex items-center gap-1">
                                    Lihat Berkas
                                </span>
                                @endif
                            </p>

                            <p class="flex flex-col">
                                <span class="text-gray-500">Formulir Pelamar</span>
                                @if($offering?->link_form_pelamar)
                                <a href="{{ $offering->link_form_pelamar }}" target="_blank" rel="noopener noreferrer"
                                    class="text-[#009DA9] underline inline-flex items-center gap-1 break-all">
                                    Isi Formulir
                                </a>
                                @else
                                <span class="text-gray-400 cursor-not-allowed flex items-center gap-1">
                                    Isi Formulir
                                </span>
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>

                    <br>

                    {{-- ✅ PERBAIKAN: Tampilkan deadline dari response_deadline --}}
                    @if ($applicant->status === 'Offering')
                        @if(!$applicant->isOfferingExpired)
                        <p>
                            <strong>Batas respon offering:</strong>  
                            {{ $offering->response_deadline->translatedFormat('l, d F Y, H:i') }} (WIB).<br>
                            Jika melewati batas waktu tersebut, maka kami anggap Menolak Offering.
                        </p>
                        @else
                        <p class="text-red-600 font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Offering sudah kadaluarsa sejak 
                            {{ $offering->response_deadline->translatedFormat('l, d F Y, H:i') }} (WIB).
                        </p>
                        @endif
                    @endif

                    {{-- FORM HANYA SAAT OFFERING --}}
                    @if ($applicant->status === 'Offering')
                    <form id="offeringForm-{{ $applicant->id }}"
                        method="POST"
                        action="{{ route('offering.response', $offering->id) }}"
                        class="mt-4">
                        @csrf
                        <input type="hidden" name="action" id="offeringAction-{{ $applicant->id }}">

                        <button type="button"
                            onclick="confirmOffering({{ $applicant->id }},'accept')"
                            {{ $applicant->isOfferingExpired ? 'disabled' : '' }}
                            class="px-5 h-10 inline-flex items-center rounded-lg bg-[#009DA9] text-white text-sm font-medium hover:bg-[#008a95]
                            {{ $applicant->isOfferingExpired ? 'opacity-50 bg-gray-400 cursor-not-allowed' : '' }} transition-colors">
                            {{ $applicant->isOfferingExpired ? 'Offering sudah kadaluarsa' : 'Terima Offering' }}
                        </button>

                        @if(!$applicant->isOfferingExpired)
                        <button type="button"
                            onclick="confirmOffering({{ $applicant->id }}, 'decline')"
                            class="px-4 h-10 rounded-lg border border-[#009DA9] text-[#009DA9] text-sm font-medium hover:bg-[#009DA9]/10 inline-flex items-center gap-2">
                            Tolak
                        </button>
                        @endif
                    </form>
                    @endif

                    {{-- 2) Sudah menerima --}}
                    @if ($applicant->status === 'Menerima Offering')
                    <p class="text-zinc-800 text-md mt-4">
                        Selamat! Anda telah menerima offering dari kami. Apabila terdapat pertanyaan lebih lanjut, mohon hubungi Contact Person.
                    </p>
                    @endif

                    {{-- STATUS: MENOLAK OFFERING --}}
                    @if ($applicant->status === 'Menolak Offering')
                        @php
                            $isExpiredDecline =
                                $applicant->offering
                                && $applicant->offering->decision === 'declined'
                                && $applicant->offering->decision_reason === 'expired';
                        @endphp

                        @if ($isExpiredDecline)
                            <p class="text-zinc-800 text-md">
                                Anda tidak memberikan respon hingga batas waktu yang ditentukan,
                                sehingga offering ini otomatis dianggap ditolak.
                            </p>
                        @else
                            <p class="text-zinc-800 text-md">
                                Offering ini telah ditolak.
                                Terima kasih telah mengikuti proses seleksi ini.
                            </p>
                        @endif
                    @endif

                    @else
                    <p class="text-zinc-800 text-md">
                        Data offering belum tersedia. Silakan menunggu informasi selanjutnya.
                    </p>
                    @endif

                </section>
                @endif

            </details>


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

        function confirmOffering(applicantId, action) {
            const form = document.getElementById(`offeringForm-${applicantId}`);
            const input = document.getElementById(`offeringAction-${applicantId}`);

            if (!form || !input) {
                console.error('Form / input not found', {
                    applicantId,
                    action
                });
                return;
            }

            Swal.fire({
                title: action === 'accept' ? 'Konfirmasi Offering' : 'Tolak Offering?',
                text: action === 'accept' ?
                    'Pastikan Anda sudah mempelajari offering dan melengkapi semua berkas' : 'Anda akan dianggap mengundurkan diri dari proses seleksi',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: action === 'accept' ? '#009DA9' : '#d33',
                confirmButtonText: action === 'accept' ? 'Ya, Terima' : 'Ya, Tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((res) => {
                if (res.isConfirmed) {
                    input.value = action;
                    form.submit();
                }
            });
        }
    </script>

    @push('scripts')
    @if (session('success'))
    <script>
        window.addEventListener('load', () => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#009DA9'
            });
        });
    </script>
    @endif
    @endpush
</x-guest-layout>