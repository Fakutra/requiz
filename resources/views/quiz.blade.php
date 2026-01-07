<x-guest-layout>
    {{-- CSS khusus halaman quiz: blok select untuk semua elemen non-input --}}
    <style>
        .quiz-no-select,
        .quiz-no-select *:not(textarea):not(input):not(select) {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        .strong-warning {
            font-weight: 700;
            color: #dc2626;
        }
    </style>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            {{-- tambahin class quiz-no-select di container utama --}}
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200 quiz-no-select">

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
                        {{-- ====== KIRI: SEMUA SOAL (show/hide per nomor) ====== --}}
                        <div class="md:col-span-8">
                            <div id="question-root">
                                @foreach ($questions as $i => $q)
                                    @php $no = $i + 1; @endphp
                                    @include('_question', [
                                        'currentQ'       => $q,
                                        'currentNo'      => $no,
                                        'totalQuestions' => $totalQuestions,
                                        'isActive'       => $no === $currentNo,
                                    ])
                                @endforeach
                            </div>

                            <div class="mt-6 flex justify-between items-center gap-2" id="controls">
                                {{-- PREV --}}
                                <button type="button"
                                    class="btn-prev inline-flex items-center rounded-xl bg-gray-100 px-5 py-2 text-gray-800 shadow-sm transition hover:bg-gray-200 {{ $currentNo<=1 ? 'pointer-events-none opacity-50' : '' }}"
                                    data-no="{{ $currentNo>1 ? $currentNo-1 : '' }}"
                                    aria-disabled="{{ $currentNo<=1 ? 'true' : 'false' }}">
                                    <span data-label>←</span>
                                    <svg data-spin class="hidden animate-spin h-5 w-5 text-gray" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                        </path>
                                    </svg>
                                </button>

                                {{-- NEXT (panah →) --}}
                                <button type="button" id="next-btn"
                                    class="btn-next inline-flex items-center rounded-xl bg-blue-600 px-5 py-2 text-white shadow-sm transition hover:bg-blue-700 {{ $currentNo < $totalQuestions ? '' : 'hidden' }}"
                                    data-no="{{ $currentNo+1 <= $totalQuestions ? $currentNo+1 : '' }}">
                                    <span data-label>→</span>
                                    <svg data-spin class="hidden animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </button>

                                {{-- SUBMIT (hanya di soal terakhir) --}}
                                <button type="submit" id="submit-btn"
                                    class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2 text-white shadow-sm transition hover:bg-blue-700 {{ $currentNo >= $totalQuestions ? '' : 'hidden' }}">
                                    {{ $isLastSection ? 'Selesai Tes' : 'Section Selanjutnya' }}
                                </button>
                            </div>
                        </div>

                        {{-- ====== KANAN: Navigator ====== --}}
                        <aside class="md:col-span-4">
                            <div class="sticky top-4 space-y-4">
                                <div>
                                    <div class="mb-2 text-sm font-medium">Navigasi Soal</div>
                                    <div class="grid grid-cols-5 gap-2" id="nav-grid">
                                        @php
                                            $answeredNos = $answeredNos ?? [];
                                        @endphp
                                        @foreach ($questions as $i => $q)
                                            @php $no = $i + 1; @endphp
                                            <a href="#"
                                               data-nav="1"
                                               data-nav-no="{{ $no }}"
                                               class="block rounded-md px-2 py-1 text-sm text-center relative
                                                    @if ($no == $currentNo)
                                                        bg-blue-600 text-white
                                                    @elseif (!empty($answeredNos[$no]))
                                                        bg-green-500 text-white
                                                    @else
                                                        bg-gray-100 text-gray-700 hover:bg-gray-200
                                                    @endif
                                               ">
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
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($isLastSection)
                            Konfirmasi Selesai Tes
                        @else
                            Konfirmasi Lanjut Section
                        @endif
                    </h3>
                    
                    <div class="mt-2 text-sm text-gray-600">
                        @if($isLastSection)
                            <!-- Pesan untuk SELESAI TES -->
                            <div class="space-y-2">
                                <p>Waktu Tes masih ada.</p>
                                <p>Apakah kamu yakin ingin menyelesaikan tes?</p>
                            </div>
                        @else
                            <!-- Pesan untuk SECTION SELANJUTNYA -->
                            <div class="space-y-2">
                                <p>Waktu pada section ini masih ada.</p>
                                <p>
                                    Setelah melanjutkan, kamu 
                                    <span class="strong-warning">TIDAK BISA</span> 
                                    kembali ke section ini.
                                </p>
                                <p>Apakah kamu yakin ingin melanjutkan?</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" 
                            class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"
                            data-finish-cancel>
                            Batal
                        </button>
                        
                        <button type="button" 
                            class="px-4 h-10 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                            data-finish-confirm>
                            @if($isLastSection)
                                Ya, Selesaikan Tes
                            @else
                                Ya, Lanjutkan
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('quiz-form');
            if (!form) return;

            const token        = form.querySelector('input[name="_token"]')?.value;
            const sectionId    = parseInt(form.querySelector('input[name="section_id"]')?.value || '0', 10);
            const questionRoot = document.getElementById('question-root');
            const navGrid      = document.getElementById('nav-grid');

            const AUTOSAVE_URL   = @json(route('quiz.autosave', ['slug' => $test->slug]));
            const totalQuestions = parseInt(@json($totalQuestions), 10) || 1;
            let currentNo        = parseInt(@json($currentNo), 10) || 1;
            let answeredNos      = @json($answeredNos ?? []);

            const $  = (s, r = document) => r.querySelector(s);
            const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

            // ====== GLOBAL BLOKIR COPY / PASTE & PASTE ESSAY ======

            // blokir copy & cut di seluruh halaman quiz
            document.addEventListener('copy', (e) => {
                e.preventDefault();
            });
            document.addEventListener('cut', (e) => {
                e.preventDefault();
            });

            // blokir paste khusus textarea jawaban (essay)
            document.addEventListener('paste', (e) => {
                const target = e.target;
                if (target && target.matches && target.matches('textarea[name^="answers["]')) {
                    e.preventDefault();
                }
            });

            // blok shortcut Ctrl/⌘ + C / V / X kalau lagi fokus di textarea jawaban
            document.addEventListener('keydown', (e) => {
                if (!e.ctrlKey && !e.metaKey) return;
                const key = (e.key || '').toLowerCase();
                if (!['c','v','x'].includes(key)) return;

                const target = e.target;
                if (target && target.matches && target.matches('textarea[name^="answers["]')) {
                    e.preventDefault();
                }
            });

            // matiin right–click di area soal (biar gak gampang "inspect → copy")
            if (questionRoot) {
                questionRoot.addEventListener('contextmenu', (e) => {
                    const wrapper = e.target.closest('.quiz-no-copy');
                    if (wrapper) {
                        e.preventDefault();
                    }
                });
            }

            // ====== COUNTDOWN (side) ======
            (function countdown() {
                const deadlineIso = @json($deadline ?? null);
                if (!deadlineIso) return;

                const end    = new Date(deadlineIso).getTime();
                const elSide = document.getElementById('countdownSide');
                let submitted = false;

                const pad = n => String(n).padStart(2, '0');

                (function tick() {
                    const now  = Date.now();
                    const diff = Math.max(0, Math.floor((end - now) / 1000));
                    const m = Math.floor(diff / 60);
                    const s = diff % 60;
                    const val = `${pad(m)}:${pad(s)}`;

                    if (elSide) elSide.textContent = val;

                    if (diff <= 0 && !submitted) {
                        submitted = true;
                        try { if (typeof autosaveNow === 'function') autosaveNow(); } catch {}
                        form.submit();
                    } else {
                        requestAnimationFrame(tick);
                    }
                })();
            })();

            // ====== META SOAL AKTIF ======
            function getArticle() {
                return questionRoot.querySelector('article[data-question-id]:not(.hidden)') ||
                    questionRoot.querySelector('article[data-question-id]');
            }

            function getMeta() {
                const art   = getArticle();
                const qid   = art ? parseInt(art.getAttribute('data-question-id'), 10) : null;
                const qtype = art ? (art.getAttribute('data-question-type') || '').toLowerCase() : '';
                let optionMap = {};
                if (art) {
                    try { optionMap = JSON.parse(art.getAttribute('data-option-map') || '{}'); }
                    catch { optionMap = {}; }
                }
                return { art, qid, qtype, optionMap };
            }

            // ====== BACA JAWABAN (untuk autosave) ======
            function readAnswerNormalized() {
                const { art, qtype } = getMeta();
                if (!art) return null;

                // kirim HURUF DISPLAY (A/B/C/...) saja
                if (['pg', 'poin', 'pilihan ganda'].includes(qtype)) {
                    const r = art.querySelector('input[type="radio"]:checked');
                    return r ? r.value : null;
                }

                if (qtype === 'multiple') {
                    return $$('input[type="checkbox"]:checked', art)
                        .map(i => i.value)
                        .join(',');
                }

                if (qtype === 'essay') {
                    const ta = art.querySelector('textarea');
                    return ta ? ta.value : '';
                }

                return null;
            }

            function isCurrentAnswered() {
                const { art, qtype } = getMeta();
                if (!art) return false;

                if (['pg','poin','pilihan ganda'].includes(qtype)) {
                    return !!art.querySelector('input[type="radio"]:checked');
                }
                if (qtype === 'multiple') {
                    return $$('input[type="checkbox"]:checked', art).length > 0;
                }
                if (qtype === 'essay') {
                    const ta = art.querySelector('textarea');
                    return !!(ta && ta.value.trim().length);
                }
                return false;
            }

            function updateAnsweredStateForCurrent() {
                if (!currentNo) return;
                answeredNos[currentNo] = isCurrentAnswered();
            }

            // ====== AUTOSAVE KE BACKEND (1 SOAL AKTIF) ======
            async function autosaveNow() {
                const { qid } = getMeta();
                if (!qid || !sectionId) return true;

                const body = {
                    section_id: sectionId,
                    answers: { [qid]: readAnswerNormalized() }
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
                    return true;
                } catch (e) {
                    console.warn('autosave gagal:', e);
                    return false;
                }
            }
            window.autosaveNow = autosaveNow;

            // autosave fallback saat tab ditutup (optional, cuma sekali)
            window.addEventListener('pagehide', () => {
                try {
                    const { qid } = getMeta();
                    if (!qid || !sectionId) return;
                    const data = new Blob([JSON.stringify({
                        section_id: sectionId,
                        answers: { [qid]: readAnswerNormalized() }
                    })], { type: 'application/json' });
                    navigator.sendBeacon(AUTOSAVE_URL, data);
                } catch {}
            });

            // ====== AUTOSAVE SAAT JAWABAN BERUBAH ======
            (function bindAutosaveOnAnswerChange() {
                if (!questionRoot) return;

                questionRoot.addEventListener('change', (e) => {
                    const target = e.target;
                    if (!target) return;

                    if (
                        target.matches('input[type="radio"][name^="answers["]') ||
                        target.matches('input[type="checkbox"][name^="answers["]') ||
                        target.matches('textarea[name^="answers["]')
                    ) {
                        (async () => {
                            try { await autosaveNow(); }
                            catch (err) { console.warn('autosave on change gagal:', err); }
                        })();
                    }
                });
            })();

            // ====== NAVIGATOR (warna hijau/biru/abu) ======
            function refreshNavigatorHighlight() {
                if (!navGrid) return;
                navGrid.querySelectorAll('a[data-nav-no]').forEach(a => {
                    const n = parseInt(a.getAttribute('data-nav-no'), 10);
                    a.classList.remove(
                        'bg-blue-600','text-white',
                        'bg-green-500',
                        'bg-gray-100','text-gray-700','hover:bg-gray-200'
                    );

                    if (n === currentNo) {
                        a.classList.add('bg-blue-600','text-white');
                    } else if (answeredNos[n]) {
                        a.classList.add('bg-green-500','text-white');
                    } else {
                        a.classList.add('bg-gray-100','text-gray-700','hover:bg-gray-200');
                    }
                });
            }

            // ====== TAMPILKAN SOAL BERDASARKAN NOMOR ======
            function showQuestion(no) {
                no = parseInt(no, 10);
                if (!no || no < 1 || no > totalQuestions) return;

                const articles = $$('article[data-question-no]', questionRoot);
                articles.forEach(article => {
                    const n = parseInt(article.getAttribute('data-question-no'), 10);
                    if (n === no) article.classList.remove('hidden');
                    else          article.classList.add('hidden');
                });

                currentNo = no;

                const btnPrev   = document.querySelector('.btn-prev');
                const btnNext   = document.querySelector('.btn-next');
                const submitBtn = document.getElementById('submit-btn');

                if (btnPrev) {
                    if (currentNo > 1) {
                        btnPrev.dataset.no = currentNo - 1;
                        btnPrev.classList.remove('pointer-events-none','opacity-50');
                    } else {
                        btnPrev.dataset.no = '';
                        btnPrev.classList.add('pointer-events-none','opacity-50');
                    }
                }

                if (btnNext) {
                    if (currentNo < totalQuestions) {
                        btnNext.dataset.no = currentNo + 1;
                        btnNext.classList.remove('hidden');
                    } else {
                        btnNext.dataset.no = '';
                        btnNext.classList.add('hidden');
                    }
                }

                if (submitBtn) {
                    const isLast = currentNo === totalQuestions;
                    submitBtn.classList.toggle('hidden', !isLast);
                    
                    // Update teks tombol berdasarkan isLastSection
                    if (isLast) {
                        const isLastSection = @json($isLastSection ?? false);
                        submitBtn.textContent = isLastSection ? 'Selesai Tes' : 'Section Selanjutnya';
                    }
                }

                refreshNavigatorHighlight();

                const u = new URL(window.location.href);
                u.searchParams.set('no', currentNo);
                window.history.pushState({ no: currentNo }, '', u);
            }

            // pindah soal TANPA autosave paksa
            function goToQuestion(no) {
                updateAnsweredStateForCurrent();
                showQuestion(no);
            }

            // ====== PREV/NEXT BUTTONS ======
            $$('.btn-next,.btn-prev').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetNo = parseInt(btn.dataset.no || '', 10);
                    if (targetNo) goToQuestion(targetNo);
                });
            });

            // ====== NAVIGATOR GRID (klik nomor) ======
            if (navGrid) {
                navGrid.addEventListener('click', (e) => {
                    const a = e.target.closest('a[data-nav-no]');
                    if (!a) return;
                    e.preventDefault();
                    const n = parseInt(a.getAttribute('data-nav-no'), 10);
                    if (n) goToQuestion(n);
                });
            }

            // ====== FINISH SECTION / SELESAI TES ======
            const deadlineIso2 = @json($deadline ?? null);
            const endMs       = deadlineIso2 ? new Date(deadlineIso2).getTime() : null;

            const finishInput = document.getElementById('finish_section');
            const btnFinish   = document.getElementById('submit-btn');
            const modal       = document.getElementById('confirmFinishModal');
            const btnConfirm  = modal?.querySelector('[data-finish-confirm]');
            const btnCancel   = modal?.querySelector('[data-finish-cancel]');
            let submitting    = false;

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

                // optional: coba autosave sekali lagi (non-blocking)
                (async () => {
                    try { await autosaveNow(); } catch(e) {}
                })();

                if (btnFinish) {
                    btnFinish.setAttribute('disabled','disabled');
                    btnFinish.textContent = 'Mengirim...';
                    btnFinish.classList.add('opacity-70','cursor-wait');
                }

                form.submit();
            }

            if (btnFinish) {
                btnFinish.addEventListener('click', (e) => {
                    e.preventDefault();
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

            // ====== BACK/FORWARD browser ======
            window.addEventListener('popstate', (e) => {
                const no = (e.state && e.state.no)
                    ? e.state.no
                    : parseInt(new URL(location.href).searchParams.get('no') || '1', 10);
                if (no) showQuestion(no);
            });

            // initial highlight
            refreshNavigatorHighlight();
        })();
    </script>
</x-guest-layout>