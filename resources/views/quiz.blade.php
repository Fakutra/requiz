<x-guest-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h5 class="text-gray-500">Tes Tulis</h5>
                        <div class="text-lg font-semibold text-gray-800">{{ $test->position->name }}</div>
                    </div>
                    <div class="rounded border bg-green-50 px-4 py-3">
                        <div class="text-green-700">Time Remaining</div>
                        <div id="countdownSide" class="text-lg font-bold text-green-800"></div>
                    </div>
                </div>

                <form id="quiz-form" action="{{ route('quiz.submit', ['slug'=> $test->slug]) }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <input type="hidden" name="section_id" value="{{ $currentSection->id }}">
                    <input type="hidden" name="finish_section" id="finish_section" value="0">

                    <div class="grid gap-8 md:grid-cols-12">
                        {{-- ====== KIRI: SATU SOAL ====== --}}
                        <div class="md:col-span-8">
                            <div class="question-root" id="question-root">
                                @include('_question', [
                                'currentQ' => $currentQ,
                                'currentNo' => $currentNo,
                                'totalQuestions' => $totalQuestions
                                ])
                            </div>

                            <div class="mt-6 flex justify-between items-center gap-2" id="controls">
                                {{-- PREV --}}
                                <button type="button"
                                    class="btn-prev inline-flex items-center rounded-xl bg-gray-100 px-5 py-2 text-gray-800 shadow-sm transition hover:bg-gray-200 {{ $currentNo<=1 ? 'pointer-events-none opacity-50' : '' }}"
                                    data-no="{{ $currentNo>1 ? $currentNo-1 : '' }}" aria-disabled="{{ $currentNo<=1 ? 'true' : 'false' }}">
                                    <span data-label>←</span>
                                    <svg data-spin class="hidden animate-spin h-5 w-5 text-gray" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                        </path>
                                    </svg>
                                </button>

                                {{-- NEXT: selalu ada, tapi bisa hidden --}}
                                <button type="button" id="next-btn"
                                    class="btn-next inline-flex items-center rounded-xl bg-blue-600 px-5 py-2 text-white shadow-sm transition hover:bg-blue-700 {{ $currentNo < $totalQuestions ? '' : 'hidden' }}"
                                    data-no="{{ $currentNo+1 <= $totalQuestions ? $currentNo+1 : '' }}">
                                    <span data-label>→</span>
                                    <svg data-spin class="hidden animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </button>

                                {{-- SUBMIT: selalu ada, tapi default hidden kecuali last --}}
                                <button type="submit" id="submit-btn"
                                    class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2 text-white shadow-sm transition hover:bg-blue-700 {{ $currentNo >= $totalQuestions ? '' : 'hidden' }}">
                                    Selesai Tes
                                </button>
                            </div>
                        </div>

                        {{-- ====== KANAN: Navigator ====== --}}
                        <aside class="md:col-span-4">
                            <div class="sticky top-4 space-y-4">
                                <div>
                                    <div class="mb-2 text-sm font-medium">Navigasi Soal</div>
                                    <div class="grid grid-cols-5 gap-2" id="nav-grid">
                                        @foreach ($questions as $i => $q)
                                        @php $no = $i + 1; @endphp
                                        <a href="{{ URL::signedRoute('quiz.start', ['slug'=>$test->slug, 'no'=>$no]) }}"
                                            data-nav="1" data-nav-no="{{ $no }}"
                                            class="block rounded-md px-2 py-1 text-sm text-center relative
                                            {{ $no==$currentNo
                                                ? 'bg-blue-600 text-white'
                                                : (($answeredMap[$q['id']]??false) ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200') }}">
                                            <span data-label>{{ $no }}</span>
                                            <svg data-spin class="hidden animate-spin h-4 w-4 absolute inset-0 m-auto"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </form>
            </div>

            {{-- Modal konfirmasi finish section --}}
            <div id="confirmFinishModal" class="fixed inset-0 z-50 hidden">
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="relative mx-auto my-12 max-w-md rounded-2xl bg-white p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-gray-900">Yakin mau lanjut?</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Waktu pada section ini masih ada. Setelah melanjutkan, kamu <span class="font-semibold">tidak bisa kembali</span> ke section ini.
                    </p>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50" data-finish-cancel>Batal</button>
                        <button type="button" class="px-4 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700" data-finish-confirm>Ya, lanjut</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            // ====== SETUP DASAR ======
            const form = document.getElementById('quiz-form');
            if (!form) return;

            const token = form.querySelector('input[name="_token"]')?.value;
            const sectionId = parseInt(form.querySelector('input[name="section_id"]')?.value || '0', 10);
            const saveIndicator = document.getElementById('save-indicator');
            const questionRoot = document.getElementById('question-root') || document; // fallback kalau belum dibungkus
            const navGrid = document.getElementById('nav-grid');

            const SLUG = @json($test->slug);
            const AUTOSAVE_URL = @json(route('quiz.autosave', ['slug' => $test -> slug]));
            const Q_BASE = @json(route('quiz.q', ['slug' => $test -> slug])); // endpoint AJAX partial
            const qUrl = (no, urlSigned = null) => urlSigned ?? `${Q_BASE}?no=${no}`;

            const pad2 = n => String(n).padStart(2, '0');
            const $ = (s, r = document) => r.querySelector(s);
            const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

            // ====== COUNTDOWN (aman kalau elemen nggak ada) ======
            (function countdown() {
                const deadlineIso = @json($deadline ?? null);
                if (!deadlineIso) return;

                const end = new Date(deadlineIso).getTime();
                const elTop = document.getElementById('countdownTop');
                const elSide = document.getElementById('countdownSide');
                let submitted = false;

                const pad = n => String(n).padStart(2, '0');

                (function tick() {
                    const now = Date.now();
                    const diff = Math.max(0, Math.floor((end - now) / 1000));
                    const m = Math.floor(diff / 60),
                        s = diff % 60;
                    const val = `${pad(m)}:${pad(s)}`;

                    if (elTop) elTop.textContent = val;
                    if (elSide) elSide.textContent = val;

                    if (diff <= 0 && !submitted) {
                        submitted = true;
                        try {
                            if (typeof autosaveNow === 'function') autosaveNow();
                        } catch {}
                        form.submit();
                    } else {
                        requestAnimationFrame(tick);
                    }
                })();
            })();

            // ====== UTIL: metadata soal aktif + mapping opsi ======
            function getArticle() {
                return questionRoot.querySelector('[data-question-id]');
            }

            function getMeta() {
                const art = getArticle();
                const qid = art ? parseInt(art.getAttribute('data-question-id'), 10) : null;
                const qtype = art ? (art.getAttribute('data-question-type') || '').toLowerCase() : '';
                let optionMap = {};
                if (art) {
                    try {
                        optionMap = JSON.parse(art.getAttribute('data-option-map') || '{}');
                    } catch {
                        optionMap = {};
                    }
                }
                return {
                    art,
                    qid,
                    qtype,
                    optionMap
                };
            }
            const toOriginal = (display, map) => {
                const o = map?.[display];
                return o ? String(o).toUpperCase() : null;
            };

            function showSaved() {
                if (!saveIndicator) return;
                const t = new Date();
                saveIndicator.textContent = `Saved ${pad2(t.getHours())}:${pad2(t.getMinutes())}:${pad2(t.getSeconds())}`;
            }

            // ====== BACA JAWABAN (essay raw) ======
            function readAnswerNormalized() {
                const {
                    art,
                    qtype,
                    optionMap
                } = getMeta();
                if (!art) return null;

                if (['pg', 'poin', 'pilihan ganda'].includes(qtype)) {
                    const r = art.querySelector('input[type="radio"]:checked');
                    return r ? toOriginal(r.value, optionMap) : null; // kirim huruf ORI
                }
                if (qtype === 'multiple') {
                    return $$('input[type="checkbox"]:checked', art)
                        .map(i => toOriginal(i.value, optionMap))
                        .filter(Boolean)
                        .join(','); // "A,C,E"
                }
                if (qtype === 'essay') {
                    const ta = art.querySelector('textarea');
                    return ta ? ta.value : ''; // RAW (no uppercase)
                }
                return null;
            }

            // ====== AUTOSAVE (dipanggil sebelum pindah soal) ======
            async function autosaveNow() {
                const {
                    qid
                } = getMeta();
                if (!qid || !sectionId) return true;

                const body = {
                    section_id: sectionId,
                    answers: {
                        [qid]: readAnswerNormalized()
                    }
                };

                try {
                    const res = await fetch(AUTOSAVE_URL, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                    if (!res.ok) throw new Error('Autosave ' + res.status);
                    showSaved();
                    window.dispatchEvent(new CustomEvent('answered', {
                        detail: {
                            id: qid
                        }
                    }));
                    return true;
                } catch (e) {
                    console.warn('autosave gagal:', e);
                    return false;
                }
            }
            // expose (optional)
            window.autosaveNow = autosaveNow;

            // Fallback saat tab ditutup/berpindah
            window.addEventListener('pagehide', () => {
                try {
                    const {
                        qid
                    } = getMeta();
                    if (!qid) return;
                    const data = new Blob([JSON.stringify({
                        section_id: sectionId,
                        answers: {
                            [qid]: readAnswerNormalized()
                        }
                    })], {
                        type: 'application/json'
                    });
                    navigator.sendBeacon(AUTOSAVE_URL, data);
                } catch {}
            });

            // ====== UI helpers (spinner di tombol) ======
            function setBtnLoading(btn, on) {
                if (!btn) return;
                const label = btn.querySelector('[data-label]');
                const spin = btn.querySelector('[data-spin]');
                btn.disabled = !!on;
                if (label && spin) {
                    label.classList.toggle('hidden', !!on);
                    spin.classList.toggle('hidden', !on);
                }
            }

            // Baca target NO dari tombol:
            // - Prefer data-no
            // - Fallback parse dari data-url (?no=)
            function resolveTarget(el) {
                const href = el.getAttribute('href') || el.getAttribute('data-url') || null;
                let no = parseInt(el.getAttribute('data-no') || '', 10);
                if (!Number.isNaN(no) && no > 0) return {
                    no,
                    url: href || null
                };
                if (href) {
                    try {
                        const u = new URL(href, window.location.origin);
                        const q = parseInt(u.searchParams.get('no') || '', 10);
                        if (!Number.isNaN(q) && q > 0) return {
                            no: q,
                            url: href
                        };
                    } catch {}
                }
                return {
                    no: null,
                    url: null
                };
            }

            // ====== GANTI SOAL via AJAX ======
            async function loadQuestion(target, clickedBtn = null) {
                const {
                    no,
                    url
                } = target || {};
                if (!no) return;
                setBtnLoading(clickedBtn, true);

                try {
                    // autosave dulu
                    await autosaveNow();

                    // fetch partial soal
                    const res = await fetch(qUrl(no, url), {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) throw new Error('Load ' + res.status);

                    const data = await res.json();

                    const sectionInput = document.querySelector('input[name="section_id"]');
                    if (sectionInput && data.section_id) {
                        sectionInput.value = data.section_id; // sinkronin section aktif
                    }

                    if (data.done) {
                        window.location.href = @json(route('quiz.finish', ['slug' => $test -> slug]));
                        return;
                    }

                    // pastikan ada container
                    const questionRoot = document.getElementById('question-root');
                    if (questionRoot && data.html) {
                        questionRoot.innerHTML = data.html;
                    }

                    // tombol prev/next
                    const btnPrev = document.querySelector('.btn-prev');
                    const btnNext = document.querySelector('.btn-next');
                    const submitBtn = document.getElementById('submit-btn');
                    if (btnPrev) {
                        const pn = data.prevNo ?? null;
                        const pu = data.prevUrl ?? null;
                        if (pn) {
                            btnPrev.dataset.no = pn;
                            if (pu) btnPrev.setAttribute('data-url', pu);
                            btnPrev.classList.remove('pointer-events-none', 'opacity-50');
                        } else {
                            btnPrev.dataset.no = '';
                            btnPrev.classList.add('pointer-events-none', 'opacity-50');
                        }
                    }
                    if (btnNext) {
                        const nn = data.nextNo ?? null;
                        if (nn) {
                            btnNext.dataset.no = nn;
                            btnNext.classList.remove('hidden');
                        } else {
                            btnNext.dataset.no = '';
                            btnNext.classList.add('hidden');
                        }
                    }

                    if (submitBtn) {
                        const isLast = (data.nextNo == null);
                        submitBtn.classList.toggle('hidden', !isLast);
                    }

                    // update nav highlight
                    const navGrid = document.getElementById('nav-grid');
                    if (navGrid) {
                        const answered = data.answeredMap || {};
                        navGrid.querySelectorAll('a[data-nav-no]').forEach(a => {
                            const n = parseInt(a.getAttribute('data-nav-no'), 10);
                            a.classList.remove('bg-blue-600', 'text-white', 'bg-green-500', 'bg-gray-100', 'text-gray-700');
                            if (n === data.currentNo) {
                                a.classList.add('bg-blue-600', 'text-white');
                            } else if (answered[n]) {
                                a.classList.add('bg-green-500', 'text-white');
                            } else {
                                a.classList.add('bg-gray-100', 'text-gray-700');
                            }
                        });
                    }

                    // update URL tanpa reload
                    const u = new URL(window.location.href);
                    u.searchParams.set('no', data.currentNo);
                    window.history.pushState({
                        no: data.currentNo
                    }, '', u);


                } catch (err) {
                    console.error('loadQuestion error:', err);
                    alert('Terjadi kesalahan saat memuat soal. Coba refresh halaman.');
                } finally {
                    setBtnLoading(clickedBtn, false);
                }
            }


            // ====== Bind tombol Prev/Next (tanpa reload) ======
            $$('.btn-next,.btn-prev').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const target = resolveTarget(btn);
                    if (target.no) loadQuestion(target, btn);
                });
            });

            // ====== Intercept navigator grid (klik nomor) ======
            if (navGrid) {
                navGrid.addEventListener('click', (e) => {
                    const a = e.target.closest('a[data-nav-no]');
                    if (!a) return;
                    e.preventDefault();
                    const target = resolveTarget(a);
                    if (target.no) loadQuestion(target, a);
                });
            }

            // ====== FINISH SECTION (modal konfirmasi) ======
            const deadlineIso = @json($deadline ?? null);
            const endMs = deadlineIso ? new Date(deadlineIso).getTime() : null;

            const finishInput = document.getElementById('finish_section'); // hidden input
            const btnFinish = document.getElementById('submit-btn'); // "Selesai Tes"
            const modal = document.getElementById('confirmFinishModal');
            const btnConfirm = modal?.querySelector('[data-finish-confirm]');
            const btnCancel = modal?.querySelector('[data-finish-cancel]');

            let submitting = false;

            function openModal() {
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                if (!modal) return;
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            async function hardSubmit() {
                if (submitting) return;
                submitting = true;

                if (finishInput) finishInput.value = '1';
                try {
                    await autosaveNow();
                } catch {}

                if (btnFinish) {
                    btnFinish.setAttribute('disabled', 'disabled');
                    btnFinish.textContent = 'Mengirim...';
                    btnFinish.classList.add('opacity-70', 'cursor-wait');
                }
                form.submit();
            }

            if (btnFinish) {
                btnFinish.addEventListener('click', (e) => {
                    e.preventDefault();
                    // kalau waktu habis / nggak ada deadline → langsung submit
                    if (!endMs || Date.now() >= endMs) {
                        hardSubmit();
                    } else {
                        openModal();
                    }
                });
            }
            btnCancel?.addEventListener('click', () => closeModal());
            btnConfirm?.addEventListener('click', async () => {
                closeModal();
                await hardSubmit();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal?.classList.contains('hidden')) closeModal();
            });

            // ====== Back/Forward tombol browser ======
            window.addEventListener('popstate', (e) => {
                const no = (e.state && e.state.no) ?
                    e.state.no :
                    parseInt(new URL(location.href).searchParams.get('no') || '1', 10);
                if (no) loadQuestion(no);
            });

        })();
    </script>

</x-guest-layout>