<nav class="bg-white border-b border-gray-100">
    <!-- Desktop Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @if (Auth::user()->role === 'user')
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    </div>

                    <!-- Navigation Links (User) -->
                    <div class="hidden sm:flex sm:items-center sm:ms-10 space-x-8">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('lowongan.index')" :active="request()->routeIs('lowongan.index')">
                            {{ __('Lowongan') }}
                        </x-nav-link>

                        <x-nav-link :href="route('history.index')" :active="request()->routeIs('history.index')">
                            {{ __('Lamaran Saya') }}
                        </x-nav-link>
                    </div>
                @endif

                @if (Auth::user()->role === 'admin')
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('admin.dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    </div>

                    @php
                        $activeApplicant = request()->routeIs('admin.applicant.*') || request()->routeIs('admin.applicant.seleksi.*');
                        $quizActive      = request()->routeIs('test.*') || request()->routeIs('question.*') || request()->routeIs('bundle.*');
                        $scheduleActive  = request()->routeIs('tech-schedule.*') || request()->routeIs('interview-schedule.*');
                        $activePenilaian = request()->routeIs('quiz_results.*') || request()->routeIs('essay_grading.*');
                    @endphp

                    <!-- Navigation Links (Admin) -->
                    <div class="hidden sm:flex sm:items-center sm:ms-10 space-x-8">
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('batch.index')" :active="request()->routeIs('batch.index')">
                            {{ __('Batch') }}
                        </x-nav-link>

                        <!-- Applicant Dropdown -->
                        <div x-data="{ ddOpen: false }" class="relative">
                            <button type="button"
                                    @click="ddOpen = !ddOpen"
                                    @keydown.escape.window="ddOpen = false"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ $activeApplicant ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                {{ __('Applicant') }}
                                <svg class="ms-2 h-4 w-4 transform transition" :class="{ 'rotate-180': ddOpen }" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="ddOpen"
                                 x-transition.origin.top.left
                                 @click.outside="ddOpen = false"
                                 class="absolute z-50 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black/5"
                                 style="display: none;">
                                <a href="{{ route('admin.applicant.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('admin.applicant.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Pelamar') }}
                                </a>
                                <a href="{{ route('admin.applicant.seleksi.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('admin.applicant.seleksi.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Seleksi') }}
                                </a>
                            </div>
                        </div>

                        <!-- Quiz Dropdown -->
                        <div x-data="{ ddOpen: false }" class="relative">
                            <button type="button"
                                    @click="ddOpen = !ddOpen"
                                    @keydown.escape.window="ddOpen = false"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ $quizActive ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                {{ __('Quiz') }}
                                <svg class="ms-2 h-4 w-4 transform transition" :class="{ 'rotate-180': ddOpen }" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="ddOpen"
                                 x-transition.origin.top.left
                                 @click.outside="ddOpen = false"
                                 class="absolute z-50 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black/5"
                                 style="display: none;">
                                <a href="{{ route('test.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('test.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Quiz') }}
                                </a>
                                <a href="{{ route('question.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('question.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Questions') }}
                                </a>
                                <a href="{{ route('bundle.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('bundle.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Bundles') }}
                                </a>
                            </div>
                        </div>

                        <!-- Schedule Dropdown -->
                        <div x-data="{ ddOpen: false }" class="relative">
                            <button type="button"
                                    @click="ddOpen = !ddOpen"
                                    @keydown.escape.window="ddOpen = false"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ $scheduleActive ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                {{ __('Schedule') }}
                                <svg class="ms-2 h-4 w-4 transform transition" :class="{ 'rotate-180': ddOpen }" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="ddOpen"
                                 x-transition.origin.top.left
                                 @click.outside="ddOpen = false"
                                 class="absolute z-50 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black/5"
                                 style="display: none;">
                                <a href="{{ route('tech-schedule.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('tech-schedule.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Technical Test') }}
                                </a>
                                <a href="{{ route('interview-schedule.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('interview-schedule.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Interview') }}
                                </a>
                            </div>
                        </div>

                        <!-- Penilaian Dropdown -->
                        <div x-data="{ ddOpen: false }" class="relative">
                            <button type="button"
                                    @click="ddOpen = !ddOpen"
                                    @keydown.escape.window="ddOpen = false"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                                           {{ $activePenilaian ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                {{ __('Penilaian') }}
                                <svg class="ms-2 h-4 w-4 transform transition" :class="{ 'rotate-180': ddOpen }" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="ddOpen"
                                 x-transition.origin.top.left
                                 @click.outside="ddOpen = false"
                                 class="absolute z-50 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black/5"
                                 style="display: none;">
                                <a href="{{ route('quiz_results.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('quiz_results.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Quiz Result') }}
                                </a>
                                <a href="{{ route('essay_grading.index') }}"
                                   class="block px-4 py-2 text-sm {{ request()->routeIs('essay_grading.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                                    {{ __('Penilaian Essay Quiz') }}
                                </a>
                                <a href="{{ route('tech-answers.index') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                    {{ __('Penilaian Technical Test') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Settings + Notifikasi -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-x-4">
                {{-- 🔔 Notifikasi hanya untuk USER --}}
                @if(Auth::user()->role === 'user')
                    <div class="relative" x-data="{ openNotif: false }">
                        <button @click="openNotif = !openNotif" class="relative focus:outline-none flex items-center">
                            <i class="fas fa-bell text-gray-600 text-xl"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center 
                                            px-1.5 py-0.5 text-xs font-bold leading-none text-white 
                                            bg-red-600 rounded-full">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <!-- Dropdown Notifikasi -->
                        <div x-show="openNotif" @click.outside="openNotif = false"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50">
                            <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                                @forelse(auth()->user()->notifications as $notif)
                                    <li class="p-3 {{ $notif->read_at ? 'bg-white' : 'bg-gray-50' }}">
                                        <p class="text-sm font-medium">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                        <p class="text-xs text-gray-500">{{ $notif->data['message'] ?? '' }}</p>
                                        <span class="text-[10px] text-gray-400">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="p-3 text-center text-gray-500 text-sm">Tidak ada notifikasi</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent 
                                    text-sm leading-4 font-medium rounded-md 
                                    text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        {{-- USER MOBILE --}}
        @if (Auth::user()->role === 'user')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('lowongan.index')" :active="request()->routeIs('lowongan.index')">
                    {{ __('Lowongan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('history.index')" :active="request()->routeIs('history.index')">
                    {{ __('Lamaran Saya') }}
                </x-responsive-nav-link>
            </div>
        @endif

        {{-- ADMIN MOBILE --}}
        @if (Auth::user()->role === 'admin')
            @php
                $activeApplicant_m = request()->routeIs('admin.applicant.*') || request()->routeIs('admin.applicant.seleksi.*');
                $quizActive_m      = request()->routeIs('test.*') || request()->routeIs('question.*') || request()->routeIs('bundle.*');
                $scheduleActive_m  = request()->routeIs('tech-schedule.*') || request()->routeIs('interview-schedule.*');
                $activePenilaian_m = request()->routeIs('quiz_results.*') || request()->routeIs('essay_grading.*');
            @endphp

            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('batch.index')" :active="request()->routeIs('batch.index')">
                    {{ __('Batch') }}
                </x-responsive-nav-link>

                <!-- Applicant (collapsible) -->
                <div x-data="{ openA: {{ $activeApplicant_m ? 'true' : 'false' }} }" class="mt-1">
                    <button type="button" @click="openA = !openA"
                            class="w-full flex items-center justify-between px-3 py-2 text-left text-sm font-medium
                                   text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                        <span>{{ __('Applicant') }}</span>
                        <svg class="h-4 w-4 transform transition" :class="{ 'rotate-180': openA }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="openA" x-transition class="mt-1 space-y-1">
                        <x-responsive-nav-link :href="route('admin.applicant.index')" :active="request()->routeIs('admin.applicant.index')">
                            {{ __('Pelamar') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.applicant.seleksi.index')" :active="request()->routeIs('admin.applicant.seleksi.*')">
                            {{ __('Seleksi') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <!-- Quiz (collapsible) -->
                <div x-data="{ openQ: {{ $quizActive_m ? 'true' : 'false' }} }" class="mt-1">
                    <button type="button" @click="openQ = !openQ"
                            class="w-full flex items-center justify-between px-3 py-2 text-left text-sm font-medium
                                   text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                        <span>{{ __('Quiz') }}</span>
                        <svg class="h-4 w-4 transform transition" :class="{ 'rotate-180': openQ }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="openQ" x-transition class="mt-1 space-y-1">
                        <x-responsive-nav-link :href="route('test.index')" :active="request()->routeIs('test.*')">
                            {{ __('Quiz') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('question.index')" :active="request()->routeIs('question.*')">
                            {{ __('Questions') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('bundle.index')" :active="request()->routeIs('bundle.*')">
                            {{ __('Bundles') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <!-- Schedule (collapsible) -->
                <div x-data="{ openS: {{ $scheduleActive_m ? 'true' : 'false' }} }" class="mt-1">
                    <button type="button" @click="openS = !openS"
                            class="w-full flex items-center justify-between px-3 py-2 text-left text-sm font-medium
                                   text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                        <span>{{ __('Schedule') }}</span>
                        <svg class="h-4 w-4 transform transition" :class="{ 'rotate-180': openS }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="openS" x-transition class="mt-1 space-y-1">
                        <x-responsive-nav-link :href="route('tech-schedule.index')" :active="request()->routeIs('tech-schedule.*')">
                            {{ __('Technical Test') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('interview-schedule.index')" :active="request()->routeIs('interview-schedule.*')">
                            {{ __('Interview') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <!-- Penilaian (collapsible) -->
                <div x-data="{ openP: {{ $activePenilaian_m ? 'true' : 'false' }} }" class="mt-1">
                    <button type="button" @click="openP = !openP"
                            class="w-full flex items-center justify-between px-3 py-2 text-left text-sm font-medium
                                   text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                        <span>{{ __('Penilaian') }}</span>
                        <svg class="h-4 w-4 transform transition" :class="{ 'rotate-180': openP }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="openP" x-transition class="mt-1 space-y-1">
                        <x-responsive-nav-link :href="route('quiz_results.index')" :active="request()->routeIs('quiz_results.*')">
                            {{ __('Quiz Result') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('essay_grading.index')" :active="request()->routeIs('essay_grading.*')">
                            {{ __('Penilaian Essay Quiz') }}
                        </x-responsive-nav-link>
                        {{-- TODO: Ganti route jika ada halaman khusus --}}
                        <x-responsive-nav-link :href="route('essay_grading.index')">
                            {{ __('Penilaian Technical Test') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
            </div>
        @endif

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- SIDEBAR for Admin -->
@if (Auth::user()->role === 'admin')
    <aside
        x-show="sidebarOpen"
        @click.away="sidebarOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r shadow-md transform sm:translate-x-0 sm:static sm:block"
    >
        <div class="p-6 flex flex-col gap-6">
            <a href="{{ route('admin.dashboard') }}" class="flex justify-center">
                <x-application-logo class="h-10 w-auto fill-current text-gray-800" />
            </a>

            <nav class="flex flex-col gap-3">
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-nav-link>
                <x-nav-link :href="route('batch.index')" :active="request()->routeIs('batch.index')">
                    {{ __('Batch') }}
                </x-nav-link>

                <!-- Applicant Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex justify-between items-center w-full px-3 py-2 text-sm font-medium text-left text-gray-700 hover:text-blue-600">
                        {{ __('Applicant') }}
                        <svg class="h-4 w-4 transform transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                  clip-rule="evenodd" />
                        </svg>
                    </button>
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
@endif
