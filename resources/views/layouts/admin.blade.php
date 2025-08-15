<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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


    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>[x-cloak] { display: none !important; }</style>

</head>

<body class="font-sans antialiased bg-white">
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white p-2 flex items-center">
                <!-- Hamburger -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden focus:outline-none">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <img src="{{ asset('/app-logo.svg') }}" alt="Logo" class="w-14 ms-2" />
            </div>

            <div class="flex">
                <!-- Sidebar -->
                @php
                $name = Auth::user()->name;
                $initials = collect(explode(' ', $name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->join('');
                @endphp
                <aside
                    class="w-64 bg-white p-6 2xl:mt-6 2xl:rounded-xl absolute z-30 inset-y-0 left-0 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
                    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }" x-data="{ userOpen: {{ request()->is('admin/applicant*') || request()->is('admin') || request()->is('selection') ? 'true' : 'false' }},
                            jobOpen: {{ request()->is('admin/batch') ? 'true' : 'false' }},
                            quizOpen: {{ request()->is('quiz') ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <div class="flex gap-3">
                                <div class="bg-orange-500 w-12 h-12 flex items-center justify-center rounded-full text-white">
                                    <span>{{ $initials }}</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">{{ Auth::user()->name }}</h4>
                                    <span class="text-sm text-slate-700">Administrator</span>
                                </div>
                            </div>
                        </div>
                        <!-- Close button (only on mobile) -->
                        <button @click="sidebarOpen = false" class="lg:hidden">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <nav class="space-y-5">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center no-underline {{ request()->is('admin/dashboard') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-2' : 'text-gray-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            Dashboard
                        </a>
                        <!-- Menu dengan Submenu User -->
                        <div class="py-1">
                            <button
                                @click="userOpen = !userOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                Mengelola User
                                <svg
                                    class="w-4 h-4 ml-auto transform"
                                    :class="{ 'rotate-180': openMenu === 'user' }"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24">
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
                                <a href="#" class="block hover:text-blue-600 no-underline {{ request()->is('admin/admin') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Admin List</a>
                            </div>
                        </div>
                        <!-- Menu Job dengan Submenu -->
                        <div class="py-1">
                            <button
                                @click="jobOpen = !jobOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                </svg>
                                Mengelola Batch
                                <svg
                                    class="w-4 h-4 ml-auto transform"
                                    :class="{ 'rotate-180': openMenu === 'job' }"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Submenu -->
                            <div
                                x-show="jobOpen"
                                x-transition
                                class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="{{ route('batch.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/batch') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Batch</a>
                            </div>
                        </div>
                        <!-- Menu dengan Submenu Kuis -->
                        <div class="py-1">
                            <button
                                @click="quizOpen = !quizOpen"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                                Mengelola Kuis
                                <svg
                                    class="w-4 h-4 ml-auto transform"
                                    :class="{ 'rotate-180': openMenu === 'quiz' }"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Submenu -->
                            <div
                                x-show="quizOpen"
                                x-transition
                                class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="#" class="block hover:text-blue-600 no-underline {{ request()->is('admin/quiz') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Quiz</a>
                                <a href="{{ route('question.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/question') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Question</a>
                                <a href="{{ route('bundle.index') }}" class="block hover:text-blue-600 no-underline {{ request()->is('admin/bundle') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-2 px-3' : 'text-gray-600' }}">Bundle</a>
                            </div>
                        </div>
                        <a href="#" class="flex items-center no-underline {{ request()->is('admin/report') ? 'font-semibold text-blue-500 bg-blue-50 rounded-md py-3 px-3' : 'text-gray-600' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Mengelola Report
                        </a>
                    </nav>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="mt-6 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                            <div class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 me-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                </svg>
                                Logout
                            </div>
                        </button>
                    </form>
                </aside>

                <!-- Overlay -->
                <div
                    class="fixed bg-black bg-opacity-0 z-20 lg:hidden"
                    x-show="sidebarOpen"
                    @click="sidebarOpen = false"
                    x-transition.opacity></div>

                <!-- Page Content -->
                <main class="flex-1 p-8 md:p-8 max-w-7xl mx-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
</body>

<footer class="bg-gray-900 text-gray-200 py-10 px-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
        <!-- Brand -->
        <div>
            <h3 class="text-3xl font-bold mb-4">ReQuiz Admin</h3>
            <div>
                <h4 class="text-lg font-semibold">Kontak</h4>
                <p class="text-sm text-gray-400">Email: support@namabrand.com</p>
                <p class="text-sm text-gray-400">Telepon: +62 812 3456 7890</p>
                <div class="flex space-x-4 mt-4">
                    <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-10 border-t border-gray-700 pt-6 text-center text-sm text-gray-500">
        © 2025 ReQuiz. All rights reserved.
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>