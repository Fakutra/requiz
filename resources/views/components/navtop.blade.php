<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow text-sm">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="flex justify-between h-[80px] items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('welcome') }}">
                    <x-application-logo class="block h-10 w-10 text-gray-800" />
                </a>
            </div>

            <!-- Main Nav -->
            <div class="hidden sm:flex space-x-6 items-center">
                <x-nav-link class="text-xl font-medium {{ request()->is('/') ? 'font-semibold text-[#009DA9] underline underline-offset-8' : 'text-gray-600' }}" href="{{ route('welcome') }}">Home</x-nav-link>
                <x-nav-link class="text-xl font-medium {{ request()->is('joblist') ? 'font-semibold text-[#009DA9] underline underline-offset-8' : 'text-gray-600' }}" href="{{ route('joblist') }}">Job</x-nav-link>

                @auth
                    @if (Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                        <x-nav-link href="#" :active="request()->routeIs('position.index')">Positions</x-nav-link>
                        <x-nav-link href="#" :active="request()->routeIs('applicant.index')">Applicant</x-nav-link>
                    @endif
                @endauth
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex items-center space-x-4">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-800">
                                {{ Auth::user()->name }}
                                <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(Auth::user()->role === 'user')
                                <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <x-nav-link class="text-xl font-medium {{ request()->is('login') ? 'font-semibold text-[#009DA9] underline underline-offset-8' : 'text-gray-600' }}" href="{{ route('login') }}">Login</x-nav-link>
                    <x-nav-link class="text-xl font-medium {{ request()->is('register') ? 'font-semibold text-[#009DA9] underline underline-offset-8' : 'text-gray-600' }}" href="{{ route('register') }}">Register</x-nav-link>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="sm:hidden flex items-center">
                <button @click="open = ! open" class="text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="sm:hidden hidden px-4 pb-4 text-sm">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Home</x-responsive-nav-link>
        <x-responsive-nav-link href="#about">About</x-responsive-nav-link>
        <x-responsive-nav-link href="#faq">FAQ</x-responsive-nav-link>

        @auth
            @if (Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('position.index')">Positions</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('applicant.index')">Applicant</x-responsive-nav-link>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    Log Out
                </x-responsive-nav-link>
            </form>
        @else
            <x-responsive-nav-link :href="route('login')">Login</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
        @endauth
    </div>
</nav>