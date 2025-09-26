{{-- resources/views/components/app-admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ReQuiz') }}</title>

    {{-- Fonts (pilih salah satu, contoh pakai Bunny.net Figtree) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- Trix --}}
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js" defer></script>

    {{-- AlpineJS (cukup sekali) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ApexCharts (opsional) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Styles khusus halaman tertentu --}}
    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important
        }

        /* Style tambahan untuk tab dan chart dummy */
        input[name="summary-tab"]:checked+label {
            background-color: #0d6efd;
            color: white;
        }

        #tab-chart:checked~.card-body #summary-chart,
        #tab-text:checked~.card-body #summary-text {
            display: block;
        }

        #summary-chart,
        #summary-text {
            display: none;
        }

        .tab-label {
            cursor: pointer;
            margin-right: 5px;
        }

        .dummy-chart {
            width: 100%;
            height: 200px;
            background: linear-gradient(to right, #0d6efd 40%, #dee2e6 40%);
            position: relative;
        }

        .dummy-chart::before {
            content: '';
            position: absolute;
            top: 0;
            left: 60%;
            height: 100%;
            width: 20%;
            background: #0d6efd;
        }
    </style>
</head>


<body class="font-sans antialiased bg-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-full max-h-full mx-auto">
            <!-- Header -->
            <div class="bg-white p-2 flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden focus:outline-none">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <img src="{{ asset('/app-logo.svg') }}" alt="Logo" class="w-14 ms-2" />
            </div>

            <div class="flex">
                {{-- Sidebar --}}
                @php
                    $name = Auth::user()->name ?? 'Admin';
                    $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                @endphp

                <aside
                    class="w-64 bg-white p-6 2xl:mt-6 2xl:rounded-xl absolute z-30 inset-y-0 left-0 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
                    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
                    x-data="{
                        userOpen: {{ request()->is('admin/applicant*') || request()->is('admin') || request()->is('selection') ? 'true' : 'false' }},
                        jobOpen: {{ request()->is('admin/batch') ? 'true' : 'false' }},
                        quizOpen: {{ request()->is('quiz') ? 'true' : 'false' }},
                        scheduleOpen: {{ request()->is('tech-answers*') || request()->is('interview-schedule*') ? 'true' : 'false' }}
                    }">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex gap-3">
                            <div
                                class="bg-orange-500 w-12 h-12 flex items-center justify-center rounded-full text-white">
                                <span>{{ $initials }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">{{ $name }}</h4>
                                <span class="text-sm text-slate-700">Administrator</span>
                            </div>
                        </div>
                        <button @click="sidebarOpen = false" class="lg:hidden">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <nav class="space-y-5">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center no-underline {{ request()->is('admin/dashboard') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-2' : 'text-gray-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25A2.25 2.25 0 0 1 8.25 10.5H6A2.25 2.25 0 0 1 3.75 8.25V6Z M3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25A2.25 2.25 0 0 1 10.5 15.75V18A2.25 2.25 0 0 1 8.25 20.25H6A2.25 2.25 0 0 1 3.75 18V15.75Z M13.5 6A2.25 2.25 0 0 1 15.75 3.75H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25A2.25 2.25 0 0 1 13.5 8.25V6Z M13.5 15.75A2.25 2.25 0 0 1 15.75 13.5H18A2.25 2.25 0 0 1 20.25 15.75V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18V15.75Z" />
                            </svg>
                            Dashboard
                        </a>

                        {{-- Mengelola User --}}
                        <div class="py-1">
                            <button @click="userOpen = !userOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z M4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                User
                                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': userOpen }"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="userOpen" x-transition class="mt-3 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('admin.applicant.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('admin/applicant') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                                    Applicant
                                </a>
                                <a href="{{ route('admin.applicant.seleksi.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('admin/applicant/seleksi') || request()->is('admin/applicant/seleksi/*') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                                    Selection
                                </a>
                            </div>
                        </div>

                        {{-- Mengelola Batch --}}
                        <div class="py-1">
                            <button @click="jobOpen = !jobOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    class="size-6 me-4" fill="currentColor">
                                    <path d="M8 16h12V6H8zm0 2q-.825 0-1.412-.587T6 16V4q0-.825.588-1.412T8 2h12q.825 0 1.413.588T22 4v12q0 .825-.587 1.413T20 18zm-4 4q-.825 0-1.412-.587T2 20V6h2v14h14v2zM8 4v12z"/>
                                </svg>

                                Jobs
                                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': jobOpen }" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="jobOpen" x-transition class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('batch.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('admin/batch') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                                    Batch
                                </a>
                            </div>
                        </div>

                        {{-- Mengelola Kuis --}}
                        <div class="py-1">
                            <button @click="quizOpen = !quizOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Quiz
                                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': quizOpen }"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="quizOpen" x-transition class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('test.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('admin/quiz') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Quiz</a>
                                <a href="{{ route('question.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('admin/question') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Question</a>
                                <a href="{{ route('bundle.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('admin/bundle') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Bundle</a>
                            </div>
                        </div>

                        {{-- Schedule --}}
                        <div class="py-1">
                            <button @click="scheduleOpen = !scheduleOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 21h15a1.5 1.5 0 001.5-1.5V7.5a1.5 1.5 0 00-1.5-1.5h-15A1.5 1.5 0 003 7.5v12a1.5 1.5 0 001.5 1.5z" />
                                </svg>
                                Schedule
                                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': scheduleOpen }"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="scheduleOpen" x-transition class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('tech-schedule.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('tech-answers*') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                                    Technical Test
                                </a>
                                <a href="{{ route('interview-schedule.index') }}"
                                    class="block hover:text-blue-600 no-underline {{ request()->is('interview-schedule*') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">
                                    Interview
                                </a>
                            </div>
                        </div>
                    </nav>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="mt-6 w-full bg-blue-600 text-white py-2 rounded-full hover:bg-blue-700">
                            <div class="flex items-center justify-center">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.75 8H10.75C9.64543 8 8.75 8.89543 8.75 10V22C8.75 23.1046 9.64543 24 10.75 24H14.75" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle opacity="0.2" cx="13.6406" cy="16.4446" r="4" fill="#C2C7D5"/>
                                    <path d="M11.75 16H22.75" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.75 12L22.75 16L18.75 20" stroke="white" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                Logout
                            </div>
                        </button>
                    </form>
                </aside>

                {{-- Overlay mobile --}}
                <div class="fixed inset-0 bg-black bg-opacity-0 z-20 lg:hidden" x-show="sidebarOpen"
                    @click="sidebarOpen = false" x-transition.opacity></div>
                <div class="min-h-screen w-full bg-gray-100">
                    @if (isset($header))
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif
                        
                    {{-- Page Content --}}
                    <main class="flex-1 p-8 md:p-8 max-w-7xl mx-auto overflow-x-auto md:overflow-x-visible">
                            {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </div>

    {{-- Helpers untuk halaman seleksi --}}
    <script>
    document.addEventListener('alpine:init', () => {
    Alpine.data('stageSeleksiGlobal', () => ({
        /* ================= STATE ================ */
        // stage baca dari hidden input name="stage" (fallback: null)
        stage: null,

        // Email modal
        emailModalOpen: false,
        useTemplate: true,
        selectedEmails: [],
        selectedMeta: [], // [{id,email,name,position}]

        // CV modal
        cvModalOpen: false,
        cvUrl: null,
        cvName: '',

        // Edit modal
        editModalOpen: false,
        updateBase: '', // diisi dari #update_base => "admin/applicant/__ID__"
        form: {
        id: null,
        name: '', email: '',
        nik: '', no_telp: '',
        tpt_lahir: '', tgl_lahir: '',
        alamat: '',
        pendidikan: 'S1',
        universitas: '', jurusan: '',
        thn_lulus: '',
        position_id: '',
        status: 'Seleksi Administrasi',
        },

        /* ================= INIT ================ */
        init() {
        // select all
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
            document.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = e.target.checked);
            });
        }
        // base URL edit
        const ub = document.getElementById('update_base')?.value;
        if (ub) this.updateBase = ub;

        // stage untuk template
        this.stage = document.querySelector('input[name="stage"]')?.value || this.stage;
        },

        /* ============ MASS ACTION: LOLOS/GAGAL ============ */
        submitStatus(action, opts = {}) {
        const form = document.getElementById('statusForm');
        if (!form) return this._err('Form status (#statusForm) tidak ditemukan.');

        if (opts.autoSelectAll) {
            document.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = true);
        }

        const checked = Array.from(document.querySelectorAll('.applicant-checkbox:checked'));
        if (checked.length === 0) return this._err('Pilih minimal satu peserta.');

        const box = document.getElementById('statusInputs');
        if (!box) return this._err('Container hidden input (#statusInputs) tidak ditemukan.');
        box.innerHTML = '';

        // isi selected_applicants[] + status[id]
        checked.forEach(cb => {
            const sel = document.createElement('input');
            sel.type = 'hidden';
            sel.name = 'selected_applicants[]';
            sel.value = cb.value;
            box.appendChild(sel);

            const st = document.createElement('input');
            st.type = 'hidden';
            st.name = `status[${cb.value}]`;
            st.value = (action === 'lolos') ? 'lolos' : 'tidak_lolos';
            box.appendChild(st);
        });

        // pastikan ada 'stage'
        if (!form.querySelector('input[name="stage"]')) {
            const stageFromData = form.dataset?.stage;
            if (!stageFromData) return this._err("Input 'stage' tidak ditemukan di form.");
            const stageInput = document.createElement('input');
            stageInput.type = 'hidden';
            stageInput.name = 'stage';
            stageInput.value = stageFromData;
            box.appendChild(stageInput);
        }

        form.submit();
        },

        /* ================== EMAIL ================== */
        // tombol "Email" serbaguna:
        // - jika ada checkbox dicentang → pakai itu
        // - kalau tidak ada → auto pilih yang data-stage-state="lolos"
        openEmailModal() {
        const selected = Array.from(document.querySelectorAll('.applicant-checkbox:checked'));
        let nodes = selected.length
            ? selected
            : Array.from(document.querySelectorAll('.applicant-checkbox[data-stage-state="lolos"]'));

        if (!nodes.length) return this._err('Belum ada peserta dipilih / berstatus LOLOS di halaman ini.');

        this.selectedEmails = [...new Set(nodes.map(cb => cb.dataset.email).filter(Boolean))];
        this.selectedMeta = nodes.map(cb => ({
            id: cb.value,
            email: cb.dataset.email || '',
            name: cb.dataset.name || 'Peserta',
            position: cb.dataset.position || '-',
        }));

        // isi hidden input di modal
        document.getElementById('recipients')?.setAttribute('value', this.selectedEmails.join(','));
        document.getElementById('recipient_ids')?.setAttribute('value', nodes.map(cb => cb.value).join(','));

        // default: pakai template → preview untuk penerima pertama
        const useTpl = document.getElementById('use_template');
        if (useTpl) useTpl.checked = true;
        this.useTemplate = true;
        this._syncTemplateInputs(true);

        this.emailModalOpen = true;
        },

        // versi eksplisit auto-lolos (opsional, kalau kamu pakai @click="openEmailModalAuto()")
        openEmailModalAuto() {
        document.querySelectorAll('.applicant-checkbox[data-stage-state="lolos"]').forEach(cb => cb.checked = true);
        this.openEmailModal();
        },

        // toggle dari checkbox "gunakan template"
        toggleTemplate() {
        const on = document.getElementById('use_template')?.checked ?? true;
        this.useTemplate = !!on;
        this._syncTemplateInputs(this.useTemplate);
        },
        // alias supaya kompatibel kalau markup lama memanggil toggleUseTemplate
        toggleUseTemplate(ev) { this.toggleTemplate(ev); },

        // isi subject/message (preview) + readonly saat pakai template
        _syncTemplateInputs(useTpl) {
        const subjectEl = document.querySelector('[name="subject"]');
        const msgEl = document.querySelector('[name="message"]');
        if (!subjectEl || !msgEl) return;

        if (useTpl) {
            const first = this.selectedMeta?.[0] || {};
            const tplSubject =
            document.getElementById('tpl_subject')?.value
            || `INFORMASI HASIL SELEKSI ${this.stage || ''} TAD/OUTSOURCING - PLN ICON PLUS`;

            let tplMsg =
            document.getElementById('tpl_message')?.value
            || `Halo {NAMA_PESERTA}

    Terima kasih atas partisipasi Saudara/i dalam mengikuti proses seleksi TAD/OUTSOURCING PLN ICON PLUS pada posisi {POSISI}.

    Selamat Anda lolos pada tahap ${this.stage || ''}. Selanjutnya, silakan cek jadwal Anda untuk tahap berikutnya pada lampiran email ini.

    Demikian kami sampaikan.
    Terima kasih atas partisipasinya dan semoga sukses.`;

            subjectEl.value = tplSubject;
            msgEl.value = tplMsg
            .replaceAll('{NAMA_PESERTA}', first.name || 'Peserta')
            .replaceAll('{POSISI}', first.position || '-');

            subjectEl.readOnly = true;
            msgEl.readOnly = true;
        } else {
            subjectEl.readOnly = false;
            msgEl.readOnly = false;
        }
        },

        // validasi kirim email
        validateAndSubmit(e) {
        const form = e.target;
        const file = form.querySelector('input[type="file"][name="attachment"]')?.files?.[0];

        if (!file) { e.preventDefault(); return this._err('Wajib unggah lampiran PDF.'); }
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        if (!isPdf) { e.preventDefault(); return this._err('Lampiran harus PDF.'); }
        if (file.size > 5 * 1024 * 1024) { e.preventDefault(); return this._err('Ukuran PDF maksimal 5 MB.'); }

        const useTpl = document.getElementById('use_template')?.checked ?? true;
        if (!useTpl) {
            const subject = (form.querySelector('[name="subject"]')?.value || '').trim();
            const message = (form.querySelector('[name="message"]')?.value || '').trim();
            if (!subject || !message) {
            e.preventDefault();
            return this._err('Subjek dan pesan wajib diisi bila template dimatikan.');
            }
        }
        // submit lanjut
        },

        /* ================== CV MODAL ================== */
        openCvModal(url, name) {
        if (!url) return this._err('CV tidak tersedia.');
        this.cvUrl = url;
        this.cvName = name || 'CV';
        this.cvModalOpen = true;
        },
        closeCvModal() {
        this.cvModalOpen = false;
        this.cvUrl = null;
        this.cvName = '';
        },

        /* ================== EDIT MODAL ================== */
        openEditModal(data) {
        this.form = Object.assign({}, this.form, data || {});
        this.editModalOpen = true;
        },
        closeEditModal() { this.editModalOpen = false; },
        updateUrl() {
        const base = this.updateBase || '/admin/applicant/__ID__';
        return base.replace('__ID__', this.form?.id ?? '');
        },

        /* ================== HELPERS ================== */
        _err(msg) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Oops', text: msg });
        else alert(msg);
        },
        _alertErr(msg) { this._err(msg); }, // alias kompatibilitas
    }));
    });
    </script>



    <footer class="bg-gray-900 text-gray-200 py-10 px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-3xl font-bold mb-4">ReQuiz Admin</h3>
                <h4 class="text-lg font-semibold">Kontak</h4>
                <p class="text-sm text-gray-400">Email: support@namabrand.com</p>
                <p class="text-sm text-gray-400">Telepon: +62 812 3456 7890</p>
            </div>
        </div>
        <div class="mt-10 border-t border-gray-700 pt-6 text-center text-sm text-gray-500">
            © {{ now()->year }} ReQuiz. All rights reserved.
        </div>

        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: @json(session('success')),
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif
    </footer>

    {{-- JS libs --}}
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>
