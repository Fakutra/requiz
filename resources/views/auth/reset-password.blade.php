<x-guest-layout>
    <div class="min-h-[85vh] flex items-center justify-center bg-[#EDF7FB] py-10 px-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-md border border-gray-200 p-8">

            {{-- Header --}}
            <div class="flex flex-col items-center text-center mb-6">
                {{-- Gambar ilustrasi di tengah --}}
                <x-login-logo class="w-16 h-16 mx-auto text-[#009DA9]" />

                {{-- Judul dan deskripsi --}}
                <h2 class="text-2xl font-bold text-gray-800">Atur Ulang Password</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Masukkan password baru untuk akun kamu.
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <!-- Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" type="email" name="email"
                        class="block w-full mt-1 rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]"
                        :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- New Password -->
                <div>
                    <x-input-label for="password" :value="__('Password Baru')" />
                    <x-text-input id="password" type="password" name="password"
                        class="block w-full mt-1 rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                        class="block w-full mt-1 rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit -->
                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-[#1F2855] text-white font-medium py-3 rounded-full shadow-md hover:bg-[#27316B] transition duration-200">
                        Reset Password
                    </button>
                </div>
            </form>

            {{-- Footer --}}
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>
                    Ingat password kamu?
                    <a href="{{ route('login') }}" class="text-[#009DA9] font-medium hover:underline">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
