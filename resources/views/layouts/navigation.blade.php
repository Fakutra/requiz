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
                    @endif
                </div>
                
                <!-- User Nav -->
                @if (Auth::user()->role === 'user')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
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
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
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
                        <!-- Authentication -->
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
