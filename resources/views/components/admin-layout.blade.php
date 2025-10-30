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

</head>

<body class="font-sans antialiased bg-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white p-4 flex items-center">
                <!-- Hamburger -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden focus:outline-none">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <img src="logo.png" alt="Logo" class="w-10" />
            </div>

            <div class="flex">
                <!-- Sidebar -->
                <aside
                    class="w-64 bg-white p-6 2xl:mt-6 2xl:rounded-xl absolute z-30 inset-y-0 left-0 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0"
                    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }" x-data="{ openMenu: null }">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <div class="flex gap-3">
                                <div class="bg-orange-500 w-12 h-12 flex items-center justify-center rounded-full text-white">
                                    <span>GA</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">Gemma Adhatien</h4>
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

                    <nav class="space-y-4">
                        <a href="#" class="flex items-center text-blue-600 font-semibold py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            Dashboard
                        </a>
                        <!-- Menu dengan Submenu -->
                        <div class="py-1">
                            <button
                                @click="openMenu === 'user' ? openMenu = null : openMenu = 'user'"
                                class="flex items-center w-full text-gray-700 hover:text-blue-600 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                User
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
                                x-show="openMenu === 'user'"
                                x-transition
                                class="mt-4 ml-10 space-y-4 text-sm text-gray-600">
                                <a href="#" class="block hover:text-blue-600">Daftar User</a>
                                <a href="#" class="block hover:text-blue-600">Tambah User</a>
                            </div>
                        </div>
                        <a href="#" class="flex items-center text-gray-700 py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>
                            Mengelola Job
                        </a>
                        <a href="#" class="flex items-center text-gray-700 py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            Mengelola Kuis
                        </a>
                        <a href="#" class="flex items-center text-gray-700 py-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 me-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Mengelola Report
                        </a>
                    </nav>

                    <button class="mt-6 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Logout
                    </button>
                </aside>

                <!-- Overlay -->
                <div
                    class="fixed inset-0 bg-black bg-opacity-40 z-20 lg:hidden"
                    x-show="sidebarOpen"
                    @click="sidebarOpen = false"
                    x-transition.opacity></div>

                <!-- Page Content -->
                <main class="flex-1 px-4 py-6 max-w-7xl mx-auto">
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
                <p class="text-sm text-gray-400">Email: delanda.f@gmail.com</p>
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

</html>