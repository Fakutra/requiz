@props([
'showNav' => true,
'showFooter' => true,
'title' => config('app.name', 'ReQuiz - All in One Recruitment Platform'),
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ReQuiz - All in One Recruitment Platform') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Trix -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    {{-- Datatables --}}
    {{-- <link rel="stylesheet" href="resources/css/dataTables.css" /> --}}

    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        @php
        $hideNavRoutes = ['quiz.intro', 'quiz.start']; // daftar halaman quiz yang mau disembunyiin navnya
        @endphp

        @if (!in_array(Route::currentRouteName(), $hideNavRoutes))
        @include('components.navtop')
        @else
        <div class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow text-sm flex items-center justify-center">
            <x-application-logo class="h-10 w-10 text-gray-800" />
        </div>
        @endif

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <footer class="bg-gray-900 text-gray-200 py-10 px-8">
            <div class="max-w-7xl mx-auto">
                <h3 class="text-3xl font-bold mb-6">ReQuiz</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @forelse($footerContacts as $c)
                    <div>
                        <div class="text-lg font-semibold">{{ $c->narahubung ?? 'Kontak' }}</div>

                        @if($c->email)
                        <a href="mailto:{{ $c->email }}" class="block text-sm text-gray-300 hover:text-white mt-1 no-underline">
                            ✉️ {{ $c->email }}
                        </a>
                        @endif

                        @php $wa = $c->wa_number; @endphp
                        @if($wa)
                        <a href="https://wa.me/{{ $wa }}" target="_blank" class="block text-sm text-gray-300 hover:text-white no-underline">
                            📱 {{ $c->phone }}
                        </a>
                        @endif

                        @if($c->jam_operasional)
                        <div class="text-xs text-gray-400 mt-1">{{ $c->jam_operasional }}</div>
                        @endif
                    </div>
                    @empty
                    <div class="text-gray-400">Kontak belum tersedia.</div>
                    @endforelse
                </div>

                <div class="mt-8 border-t border-gray-700 pt-6 text-center text-sm text-gray-500">
                    © {{ now()->year }} ReQuiz. All rights reserved.
                </div>
            </div>
        </footer>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    @stack('scripts')
</body>

<script>
    function toggleAccordion(button) {
        const content = button.nextElementSibling;
        const icon = button.querySelector('svg');

        const isOpen = !content.classList.contains('hidden');

        // Tutup semua accordion lain
        document.querySelectorAll('[onclick="toggleAccordion(this)"]').forEach(btn => {
            btn.nextElementSibling.classList.add('hidden');
            btn.querySelector('svg').classList.remove('rotate-180');
        });

        if (!isOpen) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        }
    }
</script>

</html>