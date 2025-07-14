<x-app-layout>
    <!-- Header -->
    <div class="min-h-max flex flex-col justify-center items-center mt-6 sm:mt-10 text-center">
        <x-signup-logo class="w-20 h-20 text-gray-500 mt-4" />
        <p class="font-gabarito font-semibold text-xl sm:text-2xl mt-2">Create Account</p>
    </div>

    <!-- Form Container -->
    <form method="POST" action="{{ route('register') }}" class="w-full max-w-md mx-auto px-4 sm:px-6 mt-6">
        @csrf

        <!-- Name -->
        <div>
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="Username"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                placeholder="Email"
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
                autocomplete="new-password"
                placeholder="Password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirm Password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms -->
        <div class="mt-6 text-center px-2 text-sm text-gray-600">
            <p>
                By creating an account, you agree to our
                <span class="text-[#3406FF] font-semibold underline">Terms</span> &
                <span class="text-[#3406FF] font-semibold underline">Privacy Policy</span>.
            </p>
        </div>

        <!-- Register Button -->
        <div class="flex justify-center mt-6">
            <x-primary-button
                class="flex items-center justify-center bg-[#3406FF] hover:bg-[#2d05e6] w-[153px] h-[54px] text-white text-base font-medium rounded-[30px] shadow-md">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <!-- Already Registered -->
        <div class="flex flex-col sm:flex-row items-center justify-center mt-6 gap-2 text-center text-sm">
            <p class="text-gray-700">Already registered?</p>
            <a class="text-[#3406FF] hover:text-indigo-800 underline font-medium" href="{{ route('login') }}">
                {{ __('Login') }}
            </a>
        </div>
    </form>
</x-app-layout>
