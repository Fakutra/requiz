<x-app-layout>
    <!-- Header Logo dan Judul -->
    <div class="min-h-max flex flex-col justify-center items-center mt-8 sm:mt-12">
        <x-login-logo class="w-20 h-20 fill-current text-gray-500" />
        <p class="font-semibold text-[20px] sm:text-[24px] mt-2">Login To Quiz</p>
    </div>

    <!-- Status Session -->
    <x-auth-session-status class="mb-4 text-sm text-center sm:text-base" :status="session('status')" />

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="w-full max-w-md mx-auto px-4 sm:px-6 mt-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Email / Username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4 gap-2">
            <!-- Remember Me -->
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember">
                <span class="ms-2 font-gabarito font-semibold text-sm text-gray-700">{{ __('Remember me?') }}</span>
            </label>

            <!-- Forgot Password -->
            @if (Route::has('password.request'))
                <a class="font-gabarito text-sm text-[#3406FF] hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot Password?') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="flex justify-center mt-6">
            <x-primary-button
                class="flex items-center justify-center bg-[#3406FF] hover:bg-[#2d05e6] w-[153px] h-[54px] text-white text-base font-medium rounded-[30px] shadow-md">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Register Link -->
    <div class="flex flex-col sm:flex-row items-center justify-center mt-6 gap-2 text-center">
        <p class="font-gabarito font-semibold text-sm text-gray-700">Don't have any account?</p>
        <a class="font-gabarito text-sm text-[#3406FF] hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            href="{{ route('register') }}">
            {{ __('Sign Up!') }}
        </a>
    </div>
</x-app-layout>
