<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ReQuiz') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    {{-- Tambahkan CSS untuk tab dan chart dummy --}}
    <style>
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

    <!-- Trix -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    {{-- Datatables --}}
    {{-- <link rel="stylesheet" href="resources/css/dataTables.css" /> --}}

</head>

<body x-data="{ sidebarOpen: false }" class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r shadow-md transform transition-transform duration-300 ease-in-out sm:relative sm:translate-x-0"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
            
            <div class="p-6 flex flex-col gap-6">
                <!-- Logo -->
                <a href="{{ route('admin.dashboard') }}" class="flex justify-center">
                    <x-application-logo class="h-10 w-auto fill-current text-gray-800" />
                </a>

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        // document.addEventListener('DOMContentLoaded', function() {
        //     const selectAll = document.getElementById('selectAll');
        //     const checkboxes = document.querySelectorAll('.applicant-checkbox');
        //     if (selectAll) {
        //         selectAll.addEventListener('change', function() {
        //             checkboxes.forEach(cb => cb.checked = this.checked);
        //         });
        //         checkboxes.forEach(cb => {
        //             cb.addEventListener('change', function() {
        //                 selectAll.checked = [...checkboxes].every(i => i.checked);
        //             });
        //         });
        //     }
        // });
    </script>

                        <div x-show="open" x-cloak class="mt-2 space-y-1 pl-4 text-sm transition-all duration-300">
                            <a href="{{ route('applicant.index') }}" class="block text-gray-600 hover:text-blue-600">Pelamar</a>
                            <a href="{{ route('admin.applicant.seleksi.index') }}" class="block text-gray-600 hover:text-blue-600">Seleksi</a>
                            <a href="#" class="block text-gray-600 hover:text-blue-600">Jadwal Wawancara</a>
                            <a href="#" class="block text-gray-600 hover:text-blue-600">Hasil Akhir</a>
                        </div>
                    </div>

                    <x-nav-link :href="route('test.index')" :active="request()->routeIs('test.index')">
                        {{ __('Quiz') }}
                    </x-nav-link>
                    <x-nav-link :href="route('question.index')" :active="request()->routeIs('question.index')">
                        {{ __('Questions') }}
                    </x-nav-link>
                    <x-nav-link :href="route('bundle.index')" :active="request()->routeIs('bundle.index')">
                        {{ __('Bundles') }}
                    </x-nav-link>
                </nav>
            </div>
        </aside>
        <!-- Overlay untuk mobile -->
<div 
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 sm:hidden"
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak>
</div>


        <!-- Konten Utama -->
        <div class="flex-1 flex flex-col">
            <!-- Toggle Sidebar Button (Mobile Only) -->
            <div class="bg-white shadow px-4 py-2 sm:hidden">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div class="hidden sm:block">
                @include('layouts.navigation')
            </div>

            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>


</html>
