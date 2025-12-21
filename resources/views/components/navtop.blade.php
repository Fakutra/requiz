<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow text-sm" x-data="{ open:false }">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
    $notifications = auth()->check()
    ? auth()->user()->notifications()->latest()->take(8)->get()
    : collect();
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $markAllUrl = route('notifications.readAll');
    @endphp

    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="flex justify-between h-[80px] items-center">
            {{-- LOGO --}}
            <a href="{{ route('welcome') }}" class="flex items-center">
                <x-application-logo class="block h-10 w-10 text-gray-800" />
            </a>

            {{-- MENU DESKTOP --}}
            <div class="hidden sm:flex space-x-6 items-center">
                <x-nav-link href="{{ route('welcome') }}" class="text-xl font-medium" :active="request()->routeIs('welcome')">Home</x-nav-link>
                <x-nav-link href="{{ route('joblist') }}" class="text-xl font-medium" :active="request()->routeIs('joblist') || request()->routeIs('jobdetail')">Job</x-nav-link>
                @auth
                @if(Auth::user()->role === 'user')
                <x-nav-link href="{{ route('history.index') }}" class="text-xl font-medium" :active="request()->routeIs('history.index')">Riwayat Lamaran</x-nav-link>
                @endif
                @endauth
            </div>

            {{-- RIGHT SIDE --}}
            <div class="hidden sm:flex items-center space-x-4">
                @auth
                {{-- ======================== NOTIFICATION DESKTOP ======================== --}}
                <div
                    x-data="notifDropdown()"
                    x-init="init($el)"
                    data-notifications='@json($notifications)'
                    data-unread="{{ $unreadCount }}"
                    data-mark-all-url="{{ $markAllUrl }}"
                    class="relative">
                    <button
                        @click="notifyOpen = !notifyOpen; if(notifyOpen) markAllAndClear()"
                        class="relative p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        {{-- BADGE --}}
                        <span x-show="unread > 0"
                            x-text="unread"
                            class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                            style="display:none;">
                        </span>
                    </button>

                    {{-- DROPDOWN --}}
                    <div
                        x-cloak
                        x-show="notifyOpen"
                        @click.outside="notifyOpen = false"
                        class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg z-50 ring-1 ring-black/5">
                        <div class="px-4 py-3 border-b flex justify-between">
                            <h2 class="text-xl font-semibold">Notifikasi Saya</h2>
                            <button @click="markAllAndClear()" class="text-xs text-teal-600 hover:underline" x-show="unread > 0">
                                Tandai semua
                            </button>
                        </div>

                        <ul class="max-h-80 overflow-auto divide-y divide-gray-100">
                            <template x-if="notifications.length === 0">
                                <li class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi</li>
                            </template>
                            <template x-for="n in notifications" :key="n.id">
                                <li class="px-4 py-3 hover:bg-gray-50"
                                    :class="!n.read_at ? 'bg-teal-50' : ''"
                                    @click="openNotification(n)">
                                    <p class="text-sm font-medium" :class="!n.read_at ? 'text-teal-700' : 'text-gray-800'"
                                        x-text="n.data.title"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="n.data.message"></p>
                                    <p class="text-[10px] text-gray-400 mt-1" x-text="timeAgo(n.created_at)"></p>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- DROPDOWN NAMA USER --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center border border-gray-300 text-sm font-medium text-gray-600 px-3 py-2 rounded-lg">
                            {{ Auth::user()->name }}
                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link onclick="event.preventDefault(); this.closest('form').submit();">
                                <span class="text-red-600">Log Out</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endauth

                @guest
                @php
                // daftar route yang dianggap sebagai halaman auth
                $authPageRoutes = ['login', 'register'];
                @endphp

                {{-- Hanya tampilkan tombol Login/Register jika BUKAN di halaman login/register --}}
                @if (!in_array(Route::currentRouteName(), $authPageRoutes))
                <x-nav-link href="{{ route('login') }}" class="text-xl font-medium">Login</x-nav-link>
                <x-nav-link href="{{ route('register') }}" class="text-xl font-medium">Register</x-nav-link>
                @endif
                @endguest
            </div>

            <div class="flex flex-row gap-3 sm:hidden">
                {{-- ======================== NOTIFICATION DESKTOP ======================== --}}
                <div
                    x-data="notifDropdown()"
                    x-init="init($el)"
                    data-notifications='@json($notifications)'
                    data-unread="{{ $unreadCount }}"
                    data-mark-all-url="{{ $markAllUrl }}"
                    class="relative">
                    <button
                        @click="notifyOpen = !notifyOpen; if(notifyOpen) markAllAndClear()"
                        class="relative p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>

                        {{-- BADGE --}}
                        <span x-show="unread > 0"
                            x-text="unread"
                            class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                            style="display:none;">
                        </span>
                    </button>

                    {{-- DROPDOWN --}}
                    <div
                        x-cloak
                        x-show="notifyOpen"
                        @click.outside="notifyOpen = false"
                        class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg z-50 ring-1 ring-black/5">
                        <div class="px-4 py-3 border-b flex justify-between">
                            <h2 class="text-xl font-semibold">Notifikasi Saya</h2>
                            <button @click="markAllAndClear()" class="text-xs text-teal-600 hover:underline" x-show="unread > 0">
                                Tandai semua
                            </button>
                        </div>

                        <ul class="max-h-80 overflow-auto divide-y divide-gray-100">
                            <template x-if="notifications.length === 0">
                                <li class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi</li>
                            </template>

                            <template x-for="n in notifications" :key="n.id">
                                <li class="px-4 py-3 hover:bg-gray-50"
                                    :class="!n.read_at ? 'bg-teal-50' : ''"
                                    @click="openNotification(n)">
                                    <p class="text-sm font-medium" :class="!n.read_at ? 'text-teal-700' : 'text-gray-800'"
                                        x-text="n.data.title"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="n.data.message"></p>
                                    <p class="text-[10px] text-gray-400 mt-1" x-text="timeAgo(n.created_at)"></p>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- HAMBURGER MOBILE --}}
                <button @click="open = !open" class="text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div
        x-show="open"
        x-transition
        x-cloak
        class="sm:hidden bg-white border-t border-gray-100 shadow">

        <div class="flex flex-col px-6 py-4 space-y-4 text-base">

            <x-nav-link href="{{ route('welcome') }}" :active="request()->routeIs('welcome')">Home</x-nav-link>
            <x-nav-link href="{{ route('joblist') }}" :active="request()->routeIs('joblist')">Job</x-nav-link>

            @auth
            @if(Auth::user()->role === 'user')
            <x-nav-link href="{{ route('history.index') }}" :active="request()->routeIs('history.index')">
                Riwayat Lamaran
            </x-nav-link>
            @endif

            <div class="flex flex-row gap-4 justify-between items-center">
                <x-nav-link href="{{ route('profile.edit') }}" class="flex grow justify-between items-center border border-gray-300 text-sm font-medium text-gray-600 px-3 py-2 rounded-lg">
                    {{ Auth::user()->name }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </x-nav-link>

                <form method="POST" action="{{ route('logout') }}" class="px-3 py-2 rounded-lg bg-red-100">
                    @csrf
                    <div class="flex flex-row text-red-600 gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <button type="submit" class="text-md">
                            Logout
                        </button>
                    </div>
                </form>
            </div>
            @endauth

            @guest
            <x-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">Login</x-nav-link>
            <x-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">Register</x-nav-link>
            @endguest
        </div>
    </div>

</nav>

{{-- JS COMPONENT --}}
<script>
    function notifDropdown() {
        return {
            notifyOpen: false,
            notifications: [],
            unread: 0,
            markAllUrl: '',

            init(el) {
                this.notifications = JSON.parse(el.dataset.notifications);
                this.unread = Number(el.dataset.unread);
                this.markAllUrl = el.dataset.markAllUrl;
            },

            timeAgo(iso) {
                const d = new Date(iso);
                const diff = (Date.now() - d.getTime()) / 1000;
                if (diff < 60) return 'baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
                if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
                return Math.floor(diff / 86400) + ' hari lalu';
            },

            async markAllAndClear() {
                this.unread = 0;
                const now = new Date().toISOString();
                this.notifications = this.notifications.map(n => ({
                    ...n,
                    read_at: n.read_at ?? now
                }));

                await fetch(this.markAllUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
            },

            openNotification(n) {
                if (!n.read_at) {
                    n.read_at = new Date().toISOString();
                    this.unread--;
                }
                if (n.data?.link) window.location.href = n.data.link;
            }
        }
    }
</script>