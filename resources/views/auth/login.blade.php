<x-guest-layout>
    <div class="bg-[#EDF7FB] py-10 flex justify-center items-center">
        <div class="w-full max-w-lg bg-white overflow-hidden rounded-[16px] shadow-md p-6 sm:p-8 lg:p-12">
            <div class="min-h-max flex flex-col justify-center items-center mt-8 sm:mt-12">
                <x-login-logo class="w-20 h-20 fill-current text-gray-500" />
                <p class="font-gabarito font-bold text-[20px] sm:text-[24px] mt-2">Login To ReQuiz</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input
                        id="email"
                        class="block mt-1 w-full rounded-[8px]"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="Email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input
                            id="password"
                            class="block mt-1 w-full rounded-[8px]"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="block mt-4">
                        <div class="flex justify-end sm:flex-row mt-4 gap-2">
                            <!-- Forgot Password -->
                            @if (Route::has('password.request'))
                            <a class="font-gabarito text-sm text-[#3406FF] hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot Password?') }}
                            </a>
                            @endif
                        </div>
                        <div class="flex justify-center mt-6">
                            <button type="submit" class="flex items-center justify-center bg-[#1F2855] w-[153px] h-[54px] text-white text-base font-medium rounded-[30px] shadow-md" :disabled="loading">
                                <span x-show="!loading">Login</span>
                                <svg x-show="loading" class="animate-spin h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>