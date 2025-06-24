<x-guest-layout>
    <div >
        <a href="{{ route('login') }}">
            <x-back-button/>
        </a>
    </div>
    <div class="flex items-center justify-center">
        <p class="font-gabarito font-bold text-[40px] text-black700 whitespace-nowrap">
            Reset Your Password
        </p>
        <x-forgot-password-logo/>
    </div>
    <div class="font-gabarito mb-4 text-[14px] text-black-400">
        {{ __('Enter the username that you use to log in to your ReQuiz account. We will send you an email with a link to reset your password.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Email"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
    <div class="flex flex-col sm:flex-row items-center justify-center mt-6 gap-2 text-center">
        <p class="font-gabarito font-semibold text-sm text-gray-700">Don't have any account?</p>
        <a class="font-gabarito text-sm text-[#3406FF] hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            href="{{ route('register') }}">
            {{ __('Sign Up!') }}
        </a>
    </div>
</x-guest-layout>
