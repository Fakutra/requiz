<x-app-layout>
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

                <form id="quiz-form" action="{{ route('quiz.submit', ['slug' => $test->slug]) }}" method="POST"
                    autocomplete="off">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <input type="hidden" name="section_id" value="{{ $currentSection->id }}">

                    {{-- ================== AREA SOAL/OPSI: DILINDUNGI COPY ================== --}}
                    <div class="js-nocopy nocopy">
                        @forelse ($questions as $idx => $q)
                            <div class="border rounded-xl p-4 mb-4" data-question-id="{{ $q['id'] }}"
                                data-question-type="{{ $q['type'] }}" data-option-map='@json($q['option_map'])'>

                                <div class="flex items-center justify-between mb-2">
                                    <div class="font-semibold">No. {{ $idx + 1 }}</div>
                                    <span
                                        class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">{{ $q['type'] }}</span>
                                </div>

                                <div class="mb-3">{!! nl2br(e($q['question'])) !!}</div>

                                @if (!empty($q['image_path']))
                                    <div class="my-3">
                                        <img class="max-h-72 rounded" src="{{ asset('storage/' . $q['image_path']) }}"
                                            alt="Gambar Soal" draggable="false" oncontextmenu="return false">
                                    </div>
                                @endif

                                @if (in_array($q['type'], ['PG', 'Poin']))
                                    <div class="grid gap-2">
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $L)
                                            @if (!empty($q['options'][$L] ?? null))
                                                <label class="flex items-start gap-2">
                                                    <input type="radio" name="answers[{{ $q['id'] }}]"
                                                        value="{{ $L }}"
                                                        {{ in_array($L, (array) ($q['checked'] ?? []), true) ? 'checked' : '' }}>
                                                    <span class="font-semibold">{{ $L }}.</span>
                                                    <span>{{ $q['options'][$L] }}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif ($q['type'] === 'Multiple')
                                    <div class="grid gap-2">
                                        @foreach (['A', 'B', 'C', 'D', 'E'] as $L)
                                            @if (!empty($q['options'][$L] ?? null))
                                                <label class="flex items-start gap-2">
                                                    <input type="checkbox" name="answers[{{ $q['id'] }}][]"
                                                        value="{{ $L }}"
                                                        {{ in_array($L, (array) ($q['checked'] ?? []), true) ? 'checked' : '' }}>
                                                    <span class="font-semibold">{{ $L }}.</span>
                                                    <span>{{ $q['options'][$L] }}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif ($q['type'] === 'Essay')
                                    {{-- Textarea ESSAY: blokir paste --}}
                                    <textarea name="answers[{{ $q['id'] }}]" class="w-full border rounded-xl p-3 no-paste" rows="5"
                                        placeholder="Ketik jawaban Anda di sini..." onpaste="return false">{{ is_string($q['checked'] ?? '') ? $q['checked'] : '' }}</textarea>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-gray-500">Belum ada soal pada section ini.</div>
                        @endforelse
                    </div>
                    {{-- ================== /AREA SOAL/OPSI ================== --}}

                    @php $isLastSection = optional($sections->last())->id === $currentSection->id; @endphp
                    <div class="mt-6 flex justify-end">
                        <button type="submit" id="submit-btn"
                            class="px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
                            {{ $isLastSection ? 'Selesai Tes' : 'Simpan & Lanjut' }}
                        </button>
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
                    const res = await fetch(@json(route('quiz.autosave', ['slug' => $test->slug])), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                    if (res.ok) showSaved();
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
                const isLast = @json(optional($sections->last())->id === $currentSection->id);
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
</x-app-layout>
