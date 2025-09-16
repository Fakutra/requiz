{{-- resources/views/components/app-admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Trix --}}
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js" defer></script>

    {{-- AlpineJS --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ApexCharts --}}
    <script defer src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] {
            display: none !important
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
                        quizOpen: {{ request()->is('quiz') ? 'true' : 'false' }}
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
                                Mengelola User
                                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': userOpen }"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Submenu -->
                            <div
                                x-show="userOpen"
                                x-transition
                                class="mt-3 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('applicant.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/applicant') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Applicant</a>
                                <a href="{{ route('admin.applicant.seleksi.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/applicant/seleksi') || request()->is('admin/applicant/seleksi/*') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Selection</a>
                            </div>
                        </div>

                        {{-- Mengelola Batch --}}
                        <div class="py-1">
                            <button @click="jobOpen = !jobOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25M3 14.15a2.18 2.18 0 0 1-.75-1.661V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3A2.25 2.25 0 0 0 8.25 5.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                Mengelola Batch
                                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': jobOpen }" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Submenu -->
                            <div
                                x-show="jobOpen"
                                x-transition
                                class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('batch.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/batch*') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Batch</a>
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
                                Mengelola Kuis
                                <svg class="w-4 h-4 ml-auto transform" :class="{ 'rotate-180': quizOpen }"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Submenu -->
                            <div
                                x-show="quizOpen"
                                x-transition
                                class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('question.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/question') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Question</a>
                                <a href="{{ route('bundle.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/bundle') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Bundle</a>
                            </div>
                        </div>

                        <a href="#"
                            class="flex items-center no-underline {{ request()->is('admin/report') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-3 px-3' : 'text-gray-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625A1.125 1.125 0 0 0 4.5 3.375v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Mengelola Report
                        </a>
                    </nav>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="mt-6 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                            <div class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 me-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6A2.25 2.25 0 0 1 18.75 5.25v13.5A2.25 2.25 0 0 1 16.5 21h-6A2.25 2.25 0 0 1 8.25 18.75V15M5.25 12H3l3-3m0 0 3 3" />
                                </svg>
                                Logout
                            </div>
                        </button>
                    </form>
                </aside>

                {{-- Overlay mobile --}}
                <div class="fixed inset-0 bg-black bg-opacity-0 z-20 lg:hidden" x-show="sidebarOpen"
                    @click="sidebarOpen = false" x-transition.opacity></div>

                {{-- Page Content --}}
                <main class="flex-1 p-8 md:p-8 max-w-7xl mx-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    {{-- Helpers untuk halaman seleksi --}}
    <script>
        function seleksiPage() {
            return {
                emailModalOpen: false,
                selectedEmails: [],

                init() {
                    const selectAll = document.getElementById('selectAll');
                    if (selectAll) {
                        selectAll.addEventListener('change', (e) => {
                            document.querySelectorAll('.applicant-checkbox')
                                .forEach(cb => cb.checked = e.target.checked);
                        });
                    }
                },

                // Kirim status massal: 'lolos' atau 'tidak_lolos'
                submitStatus(status, opts = {}) {
                    const form = document.getElementById('statusForm');

                    // (Opsional) auto select semua baris yang tampak
                    if (opts.autoSelectAll) {
                        document.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = true);
                    }

                    const selected = Array.from(document.querySelectorAll('.applicant-checkbox:checked'));
                    if (selected.length === 0) return this.swalError('Pilih minimal satu peserta.');

                    const box = document.getElementById('statusInputs');
                    box.innerHTML = '';

                    selected.forEach(cb => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `status[${cb.value}]`; // id => 'lolos'|'tidak_lolos'
                        hidden.value = status;
                        box.appendChild(hidden);
                    });

                    form.submit();
                },

                // Tombol Email: otomatis pilih semua yang _stage_state="lolos" di halaman saat ini
                openEmailModalAuto() {
                    const lolosCbs = Array.from(document.querySelectorAll('.applicant-checkbox[data-stage-state="lolos"]'));
                    if (lolosCbs.length === 0) return this.swalError('Belum ada peserta berstatus LOLOS di tahap ini.');

                    lolosCbs.forEach(cb => cb.checked = true);

                    const emails = lolosCbs.map(cb => cb.getAttribute('data-email')).filter(Boolean);
                    this.selectedEmails = [...new Set(emails)];

                    const recipientsInput = document.getElementById('recipients');
                    const idsInput = document.getElementById('recipient_ids');

                    if (recipientsInput) recipientsInput.value = this.selectedEmails.join(',');
                    if (idsInput) idsInput.value = lolosCbs.map(cb => cb.value).join(',');

                    this.emailModalOpen = true;
                },

                // (Jika mau tetap ada versi manual: pakai checkbox dulu, lalu buka modal)
                openEmailModalManual() {
                    const selectedCbs = Array.from(document.querySelectorAll('.applicant-checkbox:checked'));
                    const emails = selectedCbs.map(cb => cb.getAttribute('data-email')).filter(Boolean);
                    if (emails.length === 0) return this.swalError('Pilih minimal satu peserta.');

                    this.selectedEmails = [...new Set(emails)];
                    const recipients = document.getElementById('recipients');
                    const idsInput = document.getElementById('recipient_ids');
                    if (recipients) recipients.value = this.selectedEmails.join(',');
                    if (idsInput) idsInput.value = selectedCbs.map(cb => cb.value).join(',');

                    this.emailModalOpen = true;
                },

                closeEmailModal() {
                    this.emailModalOpen = false;
                },

                // Validasi modal email (PDF wajib, <=5MB, dan jika tidak pakai template maka subjek+pesan wajib)
                validateAndSubmit(e) {
                    e.preventDefault();
                    const form = e.target;

                    const file = form.querySelector('input[type="file"][name="attachment"]')?.files?.[0];
                    if (!file) return this.swalError('Wajib unggah lampiran PDF.');
                    const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                    if (!isPdf) return this.swalError('Lampiran harus berformat PDF.');
                    if (file.size > 5 * 1024 * 1024) return this.swalError('Ukuran PDF maksimal 5 MB.');

                    const useTpl = form.querySelector('#use_template')?.checked ?? true;
                    const subject = form.querySelector('[name="subject"]')?.value?.trim() ?? '';
                    const message = form.querySelector('[name="message"]')?.value?.trim() ?? '';
                    if (!useTpl && (!subject || !message)) {
                        return this.swalError('Subjek dan pesan wajib diisi bila template dimatikan.');
                    }

                    form.submit();
                },

                toggleManualRequired(ev) {
                    const on = ev.target.checked; // true = pakai template
                    const subject = document.querySelector('[name="subject"]');
                    const message = document.querySelector('[name="message"]');
                    if (subject) subject.required = !on;
                    if (message) message.required = !on;
                },

                swalError(msg) {
                    if (window.Swal) Swal.fire({
                        icon: 'error',
                        title: 'Validasi gagal',
                        text: msg
                    });
                    else alert(msg);
                }
            }
        }
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
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'sukses',
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
</body>

</html>