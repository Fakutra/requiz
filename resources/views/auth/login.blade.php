<x-guest-layout>
    <!-- Session Status -->
     <div class="min-h-max flex flex-col sm:justify-center items-center">
                    <x-login-logo class="w-20 h-20 fill-current text-gray-500" />
                    <p class="font-gabarito font-semibold text-[24px] ">Login To Quiz</p>
                </div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email / Username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="Password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
    <!-- Remember Me -->
    <label for="remember_me" class="inline-flex items-center">
        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
        <span class="ms-2 font-gabarito font-semibold text-[14px]">{{ __('Remember me?') }}</span>
    </label>

    <!-- Forgot Password -->
    @if (Route::has('password.request'))
        <a class="font-gabarito text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-[#3406FF]" href="{{ route('password.request') }}">
            {{ __('Forgot Password?') }}
        </a>
    @endif
</div>

<div class="flex justify-center mt-4">
    <x-primary-button>
        {{ __('Log in') }}
    </x-primary-button>
</div>
 

    </form>
    <div class="flex items-center justify-center mt-4">
            <p class="font-gabarito font-semibold text-[14px] ">Don't have account?</p>
            <a class="font-gabarito text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-[#3406FF]" href="{{ route('register') }}">
                {{ __('Sign Up!') }}
            </a>
        </div>
</x-guest-layout>
