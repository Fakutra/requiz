<nav x-data="{ open: false, notifyOpen: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow text-sm">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="flex justify-between h-[80px] items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-10 w-10 text-gray-800" />
                </a>
            </div>

            <!-- Main Nav -->
            <div class="hidden sm:flex space-x-6 items-center">
                <x-nav-link class="text-xl font-medium" href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">Home</x-nav-link>
                <x-nav-link class="text-xl font-medium" href="{{ route('joblist') }}" :active="request()->routeIs('joblist') || request()->routeIs('jobdetail')">Job</x-nav-link>
                @auth
                @if(Auth::user()->role === 'user')
                <x-nav-link class="no-underline text-xl font-medium" :active="request()->routeIs('history.index')" href="{{ route('history.index') }}">Riwayat Lamaran</x-nav-link>
                @endif
                @endauth
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex items-center space-x-4">
                @auth
                {{-- Notif button --}}
                <div class="relative">
                    <button
                        @click="notifyOpen = !notifyOpen"
                        @keydown.escape.window="notifyOpen = false"
                        class="relative p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#009DA9]"
                        aria-haspopup="true" :aria-expanded="notifyOpen.toString()">
                        {{-- icon bell --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>

                    <div
                        x-cloak
                        x-show="notifyOpen"
                        x-transition.origin.top.right
                        @click.outside="notifyOpen = false"
                        class="absolute right-0 top-full mt-2 w-80 max-w-[min(20rem,calc(100vw-1rem))] z-50 bg-white rounded-xl shadow-lg ring-1 ring-black/5"
                        role="menu" tabindex="-1">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-xl font-semibold">Notifikasi Saya</h2>
                            {{-- tombol "tandai semua dibaca" (aktifkan kalau sudah ada route-nya) --}}
                            {{-- <form method="POST" action="#">@csrf
                        <button class="text-xs text-teal-600 hover:underline">Tandai semua</button>
                        </form> --}}
                        </div>

                        <ul class="max-h-80 overflow-auto divide-y divide-gray-100">
                            <li class="px-4 py-3 hover:bg-gray-50">
                                <a href="#" class="block">
                                    <p class="text-md text-gray-800">Judul Notifikasi</p>
                                    <p class="text-sm text-gray-500 mt-0.5">Lorem ipsum dolor sit amet</p>
                                </a>
                            </li>
                            <li class="px-4 py-3 hover:bg-gray-50">
                                <a href="#" class="block">
                                    <p class="text-md text-gray-800">Judul Notifikasi</p>
                                    <p class="text-sm text-gray-500 mt-0.5">Lorem ipsum dolor sit amet</p>
                                </a>
                            </li>
                            <li class="px-4 py-3 hover:bg-gray-50">
                                <a href="#" class="block">
                                    <p class="text-md text-gray-800">Judul Notifikasi</p>
                                    <p class="text-sm text-gray-500 mt-0.5">Lorem ipsum dolor sit amet Lorem ipsum dolor sit ame Lorem ipsum dolor sit ame Lorem ipsum dolor sit ame</p>
                                </a>
                            </li>
                            <li class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi</li>
                            {{-- @forelse(auth()->user()?->notifications()->latest()->take(8)->get() ?? [] as $n)
                        <li class="px-4 py-3 hover:bg-gray-50">
                            <a href="{{ $n->data['url'] ?? '#' }}" class="block">
                            <p class="text-sm text-gray-800">{{ $n->data['title'] ?? 'Notifikasi' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                            </a>
                            </li>
                            @empty
                            <li class="px-4 py-6 text-center text-md text-gray-500">Belum ada notifikasi</li>
                            @endforelse --}}
                        </ul>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center border border-gray-300 text-sm font-medium text-gray-600 px-3 py-2 rounded-lg">
                            {{ Auth::user()->name }}
                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(Auth::user()->role === 'user')
                        <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="flex items-center gap-2 text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                    </svg>
                                    <span>Log Out</span>
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <x-nav-link class="text-xl font-medium" :active="request()->routeIs('login')" href="{{ route('login') }}">Login</x-nav-link>
                <x-nav-link class="text-xl font-medium" :active="request()->routeIs('register')" href="{{ route('register') }}">Register</x-nav-link>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="sm:hidden flex items-center">
                @auth
                @if(Auth::user()->role === 'user')
                <div class="relative me-1">
                    <button
                        @click="notifyOpen = !notifyOpen"
                        @keydown.escape.window="notifyOpen = false"
                        class="relative p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#009DA9] me-3 sm:me-0"
                        aria-haspopup="true" :aria-expanded="notifyOpen.toString()">
                        {{-- icon bell --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>

                    {{-- POPUP / DROPDOWN --}}
                    <div
                        x-cloak
                        x-show="notifyOpen"
                        x-transition.origin.top.right
                        @click.outside="notifyOpen = false"
                        class="absolute right-0 top-full mt-2 w-80 max-w-[min(20rem,calc(100vw-1rem))] z-50 bg-white rounded-xl shadow-lg ring-1 ring-black/5"
                        role="menu" tabindex="-1">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-xl font-semibold">Notifikasi Saya</h2>
                            {{-- tombol "tandai semua dibaca" (aktifkan kalau sudah ada route-nya) --}}
                            {{-- <form method="POST" action="#">@csrf
                        <button class="text-xs text-teal-600 hover:underline">Tandai semua</button>
                        </form> --}}
                        </div>

                        <ul class="max-h-80 overflow-auto divide-y divide-gray-100">
                            <li class="px-4 py-3 hover:bg-gray-50">
                                <a href="#" class="block">
                                    <p class="text-md text-gray-800">Judul Notifikasi</p>
                                    <p class="text-sm text-gray-500 mt-0.5">Lorem ipsum dolor sit amet</p>
                                </a>
                            </li>
                            <li class="px-4 py-3 hover:bg-gray-50">
                                <a href="#" class="block">
                                    <p class="text-md text-gray-800">Judul Notifikasi</p>
                                    <p class="text-sm text-gray-500 mt-0.5">Lorem ipsum dolor sit amet</p>
                                </a>
                            </li>
                            <li class="px-4 py-3 hover:bg-gray-50">
                                <a href="#" class="block">
                                    <p class="text-md text-gray-800">Judul Notifikasi</p>
                                    <p class="text-sm text-gray-500 mt-0.5">Lorem ipsum dolor sit amet Lorem ipsum dolor sit ame Lorem ipsum dolor sit ame Lorem ipsum dolor sit ame</p>
                                </a>
                            </li>
                            <li class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi</li>
                            {{-- @forelse(auth()->user()?->notifications()->latest()->take(8)->get() ?? [] as $n)
                        <li class="px-4 py-3 hover:bg-gray-50">
                            <a href="{{ $n->data['url'] ?? '#' }}" class="block">
                            <p class="text-sm text-gray-800">{{ $n->data['title'] ?? 'Notifikasi' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                            </a>
                            </li>
                            @empty
                            <li class="px-4 py-6 text-center text-md text-gray-500">Belum ada notifikasi</li>
                            @endforelse --}}
                        </ul>
                    </div>
                </div>

                @endif
                @endauth
                <button @click="open = ! open" class="text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Mobile Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="sm:hidden hidden px-4 pb-4 text-sm">
        <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">Home</x-responsive-nav-link>
        <x-responsive-nav-link href="{{ route('joblist') }}" :active="request()->routeIs('joblist') || request()->routeIs('jobdetail')">Job</x-responsive-nav-link>
        @auth
        @if(Auth::user()->role === 'user')
        <x-responsive-nav-link href="{{ route('history.index') }}" :active="request()->routeIs('history.index')">Riwayat Lamaran</x-responsive-nav-link>
        @endif
        @endauth

        @auth
        @if (Auth::user()->role === 'admin')
        <x-responsive-nav-link :href="route('admin.dashboard')">Dashboard</x-responsive-nav-link>
        {{-- <x-responsive-nav-link :href="route('position.index')">Positions</x-responsive-nav-link> --}}
        <x-responsive-nav-link :href="route('admin.applicant.index')">Applicant</x-responsive-nav-link>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                Log Out
            </x-responsive-nav-link>
        </form>
        @else
        <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">Login</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">Register</x-responsive-nav-link>
        @endauth
    </div>
</nav>