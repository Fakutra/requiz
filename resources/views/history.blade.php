<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 sm:px-6 lg:px-8">
            <div>
                <h1 class="mb-4 font-bold text-4xl">Riwayat Lamaran</h1>
            </div>
            @push('scripts')
            @if (session('success'))
            <script>
                window.addEventListener('load', function() {
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
            <div>
                @forelse ($applicants as $applicant)
                {{-- CARD --}}
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
                        // Lolos / Positif
                        'Menerima Offering' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Seleksi Administrasi' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Tes Tulis' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Technical Test' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',
                        'Lolos Interview' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200',

                        // Gagal / Negatif
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
                        'Technical Test' => ['index' => 2, 'result' => 'pending'],
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

                        <div class="flex items-center gap-3 sm:gap-4">
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
                        <div class="flex-1 h-0.5
                        @if($active) bg-[#009DA9] @else bg-zinc-200 @endif"></div>
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

            $tr = \App\Models\TestResult::where('applicant_id', $applicant->id)
            ->where('test_id', $t->id)->first();

            $hasStarted = $tr && ($tr->started_at ||
            \App\Models\TestSectionResult::where('test_result_id', $tr->id)->whereNotNull('started_at')->exists());

            $inWindow = $open && $close ? $now->between($open, $close, true) : false;
            $beforeEnd = $end ? $now->lt($end) : true;
            $canEnter = $inWindow || ($hasStarted && $beforeEnd);

            $signedUrl = \Illuminate\Support\Facades\URL::signedRoute('quiz.start', ['slug' => $t->slug]);
            @endphp

            <div class="mt-6 pt-4 border-t border-zinc-100 text-center space-y-3">
                <p class="text-sm text-zinc-600">
                    Buka:
                    <span class="font-semibold text-sky-700">
                        {{ optional($open)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                    </span>
                    —
                    Tutup Tombol:
                    <span class="font-semibold text-sky-700">
                        {{ optional($close)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                    </span><br>
                    Hard End:
                    <span class="font-semibold text-rose-600">
                        {{ optional($end)?->translatedFormat('l, d F Y, H:i') ?? '—' }}
                    </span>
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
                    Tombol aktif hanya pada rentang jadwal buka–tutup. Jika sudah mulai, bisa lanjut hingga <em>Hard End</em>.
                </p>
            </div>
            @endif

            {{-- TECHNICAL TEST --}}
            @if ($applicant->status === 'Technical Test')
            @php
            $sched = optional($applicant->position->technicalSchedules ?? collect())->sortByDesc('schedule_date')->first();
            $latest = $sched
            ? $sched->answers->where('applicant_id', $applicant->id)->sortByDesc('submitted_at')->first()
            : null;

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
                            <a href="{{ $sched->zoom_link }}" target="_blank" class="text-sky-700 hover:underline">
                                Buka Link Zoom
                            </a>
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

                {{-- Ringkasan upload terbaru --}}
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
            </article>
            @empty
            <div class="col-span-full text-md text-gray-500 py-10">
                Belum ada lamaran
            </div>
            @endforelse

        </div>
    </div>
    </div>
</x-guest-layout>