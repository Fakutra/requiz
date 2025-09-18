<x-guest-layout>
    {{-- Panggil CSS proteksi --}}
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/quiz-protect.css') }}">
    @endpush

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">

                @if (session('status'))
                <div class="mb-4 rounded-lg bg-yellow-50 text-yellow-800 px-4 py-2 text-sm">
                    {{ session('status') }}
                </div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $test->position->name }}
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Sisa Waktu</div>
                        <div id="countdown" class="text-2xl font-bold"></div>
                        <div id="save-indicator" class="text-xs text-gray-500 mt-1"></div>
                    </div>
                </div>

                <form id="quiz-form" action="{{ route('quiz.submit', ['slug' => $test->slug]) }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <input type="hidden" name="section_id" value="{{ $currentSection->id }}">

                    <div class="grid gap-6 md:grid-cols-12">
                        {{-- ====== KIRI: SOAL ====== --}}
                        <div class="md:col-span-8">
                            <div class="js-nocopy nocopy">
                                @forelse ($questions as $idx => $q)
                                <article id="q-{{ $idx+1 }}"
                                    class="mb-4 rounded-xl border border-gray-200 p-4 scroll-mt-24"
                                    data-question-id="{{ $q['id'] }}"
                                    data-question-type="{{ $q['type'] }}"
                                    data-option-map='@json($q['option_map'])'>

                                    <div class="mb-2 flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-900">No. {{ $idx + 1 }}</p>
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                            {{ $q['type'] }}
                                        </span>
                                    </div>

                                    <div class="mb-3 text-gray-900">{!! nl2br(e($q['question'])) !!}</div>

                                    @if (!empty($q['image_path']))
                                    <img class="my-3 max-h-72 w-auto rounded-lg select-none" src="{{ asset('storage/' . $q['image_path']) }}"
                                        alt="Gambar Soal" draggable="false" oncontextmenu="return false">
                                    @endif

                                    {{-- PG / Poin --}}
                                    @if (in_array($q['type'], ['PG','Poin']))
                                    <div class="grid gap-2">
                                        @foreach (['A','B','C','D','E'] as $L)
                                        @if (!empty($q['options'][$L] ?? null))
                                        <label class="group flex cursor-pointer items-start gap-2 rounded-lg border border-transparent p-2 hover:border-gray-200">
                                            <input type="radio" name="answers[{{ $q['id'] }}]" value="{{ $L }}"
                                                class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                {{ in_array($L, (array)($q['checked'] ?? []), true) ? 'checked' : '' }}>
                                            <span class="font-semibold text-gray-900">{{ $L }}.</span>
                                            <span class="text-gray-700">{{ $q['options'][$L] }}</span>
                                        </label>
                                        @endif
                                        @endforeach
                                    </div>

                                    {{-- Multiple --}}
                                    @elseif ($q['type'] === 'Multiple')
                                    <div class="grid gap-2">
                                        @foreach (['A','B','C','D','E'] as $L)
                                        @if (!empty($q['options'][$L] ?? null))
                                        <label class="group flex cursor-pointer items-start gap-2 rounded-lg border border-transparent p-2 hover:border-gray-200">
                                            <input type="checkbox" name="answers[{{ $q['id'] }}][]" value="{{ $L }}"
                                                class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                {{ in_array($L, (array)($q['checked'] ?? []), true) ? 'checked' : '' }}>
                                            <span class="font-semibold text-gray-900">{{ $L }}.</span>
                                            <span class="text-gray-700">{{ $q['options'][$L] }}</span>
                                        </label>
                                        @endif
                                        @endforeach
                                    </div>

                                    {{-- Essay --}}
                                    @elseif ($q['type'] === 'Essay')
                                    <textarea name="answers[{{ $q['id'] }}]" rows="5" onpaste="return false"
                                        class="w-full rounded-xl border border-gray-300 p-3 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 no-paste"
                                        placeholder="Ketik jawaban Anda di sini...">{{ is_string($q['checked'] ?? '') ? $q['checked'] : '' }}</textarea>
                                    @endif
                                </article>
                                @empty
                                <p class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                                    Belum ada soal pada section ini.
                                </p>
                                @endforelse
                            </div>

                            @php $isLastSection = optional($sections->last())->id === $currentSection->id; @endphp
                            <div class="mt-6 flex justify-end">
                                <button type="submit" id="submit-btn"
                                    class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2 text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    {{ $isLastSection ? 'Selesai Tes' : 'Simpan & Lanjut' }}
                                </button>
                            </div>
                        </div>

                        {{-- ====== KANAN: TIMER + NAV NOMOR ====== --}}
                        <aside class="md:col-span-4">
                            <div class="sticky top-4 space-y-4">

                                {{-- Timer --}}
                                <div class="rounded-lg border border-green-200 bg-green-50 p-3">
                                    <div class="text-xs text-green-700">Time Remaining</div>
                                    <div id="countdown" class="text-lg font-bold text-green-800"></div>
                                    <div id="save-indicator" class="mt-1 text-[11px] text-gray-500"></div>
                                </div>

                                {{-- Navigator Nomor --}}
                                <div x-data="{ current: 1, answered: @js($questions->mapWithKeys(fn($q,$i)=>[$q['id'] => !empty($q['checked'])])->all()) }"
                                    x-on:answered.window="answered[$event.detail.id] = true">
                                    <div class="mb-2 text-sm font-medium text-gray-900">Navigasi Soal</div>
                                    <div class="grid grid-cols-5 gap-2">
                                        @foreach ($questions as $i => $q)
                                        <a href="#q-{{ $i+1 }}"
                                            data-nav-qid="{{ $q['id'] }}"
                                            @click="current={{ $i+1 }}"
                                            class="block rounded-md border px-0.5 py-1 text-center text-sm transition"
                                            :class="current==={{ $i+1 }}
                   ? 'border-blue-600 bg-blue-600 text-white'
                   : (answered[{{ $q['id'] }}] ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50')">
                                            {{ $i+1 }}
                                        </a>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </aside>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi lanjut section (muncul jika waktu masih ada) --}}
    <div id="confirmNextModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative mx-auto my-12 max-w-md bg-white rounded-2xl shadow-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800">Lanjut ke section berikutnya?</h3>
            <p class="text-sm text-gray-600 mt-2">Waktu section ini masih tersisa. Anda tidak akan bisa kembali jika
                ingin melanjutkan ke section selanjutnya. Yakin ingin melanjutkan?</p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                    data-cancel-next>Batal</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
                    data-confirm-next>Ya, Lanjut</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const deadlineIso = @json($deadline);
            const end = new Date(deadlineIso).getTime();
            const el = document.getElementById('countdown');
            const form = document.getElementById('quiz-form');
            const token = form.querySelector('input[name="_token"]').value;
            const sectionId = form.querySelector('input[name="section_id"]').value;
            const saveIndicator = document.getElementById('save-indicator');
            const submitBtn = document.getElementById('submit-btn');
            const confirmModal = document.getElementById('confirmNextModal');
            const btnConfirm = confirmModal.querySelector('[data-confirm-next]');
            const btnCancel = confirmModal.querySelector('[data-cancel-next]');

            let submitted = false;

            function pad(n) {
                return n.toString().padStart(2, '0');
            }

            function showSaved() {
                const t = new Date();
                saveIndicator.textContent = 'Saved ' + pad(t.getHours()) + ':' + pad(t.getMinutes()) + ':' + pad(t
                    .getSeconds());
            }

            function tick() {
                const now = Date.now();
                let diff = Math.max(0, Math.floor((end - now) / 1000));
                const m = Math.floor(diff / 60),
                    s = diff % 60;
                if (el) el.textContent = pad(m) + ':' + pad(s);
                if (diff <= 0) {
                    if (!submitted) {
                        submitted = true;
                        form.submit();
                    }
                    return;
                }
                requestAnimationFrame(tick);
            }
            tick();

            function openNextModal() {
                confirmModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeNextModal() {
                confirmModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            // AUTOSAVE
            async function autosaveOne(qEl) {
                const qid = qEl.getAttribute('data-question-id');
                const type = qEl.getAttribute('data-question-type');
                let payloadValue = null;

                if (type === 'PG' || type === 'Poin') {
                    const sel = qEl.querySelector('input[type="radio"]:checked');
                    if (!sel) return;
                    payloadValue = sel.value;
                } else if (type === 'Multiple') {
                    payloadValue = Array.from(qEl.querySelectorAll('input[type="checkbox"]:checked')).map(i => i
                        .value);
                } else if (type === 'Essay') {
                    const ta = qEl.querySelector('textarea');
                    if (!ta) return;
                    payloadValue = ta.value;
                }

                const body = {
                    section_id: parseInt(sectionId, 10),
                    answers: {}
                };
                body.answers[qid] = payloadValue;

                try {
                    const res = await fetch(@json(route('quiz.autosave', ['slug' => $test -> slug])), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                    if (res.ok) showSaved();
                    window.dispatchEvent(new CustomEvent('answered', { detail: { id: parseInt(qid, 10) } }));
                } catch (e) {
                    console.warn('autosave gagal', e);
                }
            }

            function debounce(fn, ms) {
                let t;
                return (...a) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...a), ms);
                };
            }
            const debouncedSave = debounce(autosaveOne, 700);

            document.querySelectorAll('[data-question-id]').forEach(qEl => {
                const type = qEl.getAttribute('data-question-type');
                if (type === 'PG' || type === 'Poin' || type === 'Multiple') {
                    qEl.addEventListener('change', () => autosaveOne(qEl));
                } else if (type === 'Essay') {
                    qEl.addEventListener('input', () => debouncedSave(qEl));
                }
            });

            // Intercept submit -> modal jika waktu masih ada & bukan section terakhir
            form.addEventListener('submit', (e) => {
                if (submitted) return;
                const now = Date.now();
                const timeLeft = end - now;
                const isLast = @json(optional($sections -> last()) -> id === $currentSection -> id);
                if (timeLeft > 1000 && !isLast) {
                    e.preventDefault();
                    openNextModal();
                } else {
                    submitted = true;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Menyimpan...';
                }
            });
            btnCancel.addEventListener('click', () => closeNextModal());
            btnConfirm.addEventListener('click', () => {
                closeNextModal();
                submitted = true;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Menyimpan...';
                form.submit();
            });

            // Cegah back
            window.history.pushState(null, '', window.location.href);
            window.addEventListener('popstate', () => {
                window.history.pushState(null, '', window.location.href);
            });

            // ====== Proteksi ANTI-COPY di area .nocopy ======
            const nocopyRoot = document.querySelector('.js-nocopy.nocopy');
            if (nocopyRoot) {
                const allowForFormField = (el) => el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el
                    .isContentEditable);

                // blok klik kanan/contextmenu
                nocopyRoot.addEventListener('contextmenu', (e) => {
                    if (!allowForFormField(e.target)) e.preventDefault();
                });

                // blok copy/cut
                ['copy', 'cut'].forEach(evt => {
                    nocopyRoot.addEventListener(evt, (e) => {
                        if (!allowForFormField(e.target)) e.preventDefault();
                    });
                });

                // blok start select & drag & drop
                nocopyRoot.addEventListener('selectstart', (e) => {
                    if (!allowForFormField(e.target)) e.preventDefault();
                });
                nocopyRoot.addEventListener('dragstart', (e) => {
                    e.preventDefault();
                }, true);
                nocopyRoot.addEventListener('drop', (e) => {
                    e.preventDefault();
                }, true);

                // blok Ctrl/Cmd + C / X / A (kecuali di input/textarea)
                nocopyRoot.addEventListener('keydown', (e) => {
                    const key = e.key.toLowerCase();
                    if ((e.ctrlKey || e.metaKey) && (key === 'c' || key === 'x' || key === 'a')) {
                        if (!allowForFormField(e.target)) e.preventDefault();
                    }
                }, true);

                // pastikan semua img tak bisa drag + klik kanan
                nocopyRoot.querySelectorAll('img').forEach(img => {
                    img.setAttribute('draggable', 'false');
                    img.addEventListener('contextmenu', e => e.preventDefault());
                });
            }

            // ====== Proteksi ANTI-PASTE di textarea essay ======
            document.querySelectorAll('textarea.no-paste').forEach(ta => {
                ta.addEventListener('paste', (e) => {
                    e.preventDefault();
                    // feedback ringan
                    ta.classList.add('ring', 'ring-red-400');
                    setTimeout(() => ta.classList.remove('ring', 'ring-red-400'), 500);
                });
                ta.addEventListener('drop', (e) => e.preventDefault());
                ta.addEventListener('dragstart', (e) => e.preventDefault());
            });
        })();
    </script>
</x-guest-layout>