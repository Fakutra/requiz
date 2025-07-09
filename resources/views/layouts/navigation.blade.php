<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow text-sm">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="flex justify-between h-[80px] items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-12 w-12 fill-current text-gray-800" />
                </a>
            </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('lowongan.index')" :active="request()->routeIs('lowongan.index')">
                            {{ __('Lowongan') }}
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

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
<<<<<<< HEAD
                        <x-nav-link :href="route('batch.index')" :active="request()->routeIs('batch.index')">
                            {{ __('Batch') }}
=======
                        <x-nav-link :href="route('position.index')" :active="request()->routeIs('position.index')">
                            {{ __('Positions') }}
>>>>>>> 5edb203afa770275ef2e40bed93c1ed0b4c570df
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('applicant.index')" :active="request()->routeIs('applicant.index')">
                            {{ __('Applicant') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>

<<<<<<< HEAD
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
=======
            <!-- Right Side -->
            <div class="hidden sm:flex items-center space-x-4">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-800">
                                {{ Auth::user()->name }}
                                <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
>>>>>>> 5edb203afa770275ef2e40bed93c1ed0b4c570df
                                </svg>
                            </button>
                        </x-slot>

                    <x-slot name="content">
<<<<<<< HEAD
                        {{-- @if (Auth::user()->role === 'user')
=======
                        @if(Auth::user()->role === 'user')
>>>>>>> 5edb203afa770275ef2e40bed93c1ed0b4c570df
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

<<<<<<< HEAD
            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
=======
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
>>>>>>> 5edb203afa770275ef2e40bed93c1ed0b4c570df
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
<<<<<<< HEAD
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        @if (Auth::user()->role === 'admin')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('position.index')" :active="request()->routeIs('position.index')">
                    {{ __('Position') }}
                </x-responsive-nav-link>
            </div>
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('applicant.index')" :active="request()->routeIs('applicant.index')">
                    {{ __('Applicant') }}
                </x-responsive-nav-link>
            </div>
        @endif
=======
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>
>>>>>>> 5edb203afa770275ef2e40bed93c1ed0b4c570df

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if (Auth::user()->role === 'user')
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

<<<<<<< HEAD
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
=======
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
>>>>>>> 5edb203afa770275ef2e40bed93c1ed0b4c570df
    </div>
</nav>