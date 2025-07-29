<x-guest-layout>
    <div class="bg-[#EDF7FB] py-10 flex justify-center items-center">
        <div class="w-full max-w-lg bg-white overflow-hidden rounded-[16px] shadow-md p-6 sm:p-8 lg:p-12">
            <div class="min-h-max flex flex-col justify-center items-center mt-8 sm:mt-12">
                <x-application-logo />
                <x-login-logo class="w-20 h-20 fill-current text-gray-500" />
                <p class="font-gabarito font-bold text-[20px] sm:text-[24px] mt-2">Login To ReQuiz</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
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
                    <x-input-label for="password" :value="__('Password')" />
                    <div>
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

                    <!-- Remember Me -->
                    <div class="block mt-4">
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
                        <div class="flex justify-center mt-6">
                            <x-primary-button
                                class="flex items-center justify-center bg-[#1F2855] hover:bg-[#2d05e6] w-[153px] h-[54px] text-white text-base font-medium rounded-[30px] shadow-md">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>