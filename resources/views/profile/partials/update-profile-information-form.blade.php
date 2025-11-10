<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Lengkapi data profil kamu di bawah ini.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- NIK --}}
        <div>
            <x-input-label for="identity_num" :value="__('Nomor Identitas (NIK)')" />
            <x-text-input id="identity_num" name="identity_num" type="text" class="mt-1 block w-full"
                :value="old('identity_num', $profile->identity_num ?? '')"
                maxlength="16"
                pattern="[0-9]{1,16}"
                inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16)"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('identity_num')" />
        </div>

        {{-- Nama --}}
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Nomor Telepon --}}
        <div>
            <x-input-label for="phone_number" :value="__('Nomor Telepon')" />
            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full"
                :value="old('phone_number', $profile->phone_number ?? '')"
                maxlength="15"
                pattern="[0-9]{1,15}"
                inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15)"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        {{-- Tempat & Tanggal Lahir --}}
        <div class="flex flex-row mt-4 gap-3 w-full">
            <div class="block w-full">
                <x-input-label for="birthplace" :value="__('Tempat Lahir')" />
                <x-text-input id="birthplace" name="birthplace" type="text" class="mt-1 block w-full"
                    :value="old('birthplace', $profile->birthplace ?? '')" required />
                <x-input-error class="mt-2" :messages="$errors->get('birthplace')" />
            </div>
            <div class="block w-full">
                <x-input-label for="birthdate" :value="__('Tanggal Lahir')" />
                <x-text-input
                    id="birthdate"
                    name="birthdate"
                    type="date"
                    class="mt-1 block w-full"
                    value="{{ old('birthdate', optional($profile?->birthdate)->format('Y-m-d')) }}"
                    required
                />
                <x-input-error class="mt-2" :messages="$errors->get('birthdate')" />
            </div>
        </div>

        {{-- Alamat --}}
        <div>
            <x-input-label for="address" :value="__('Alamat sesuai KTP')" />
            <textarea rows="4" name="address"
                class="mt-1 w-full rounded-lg border-gray-300 focus:border-[#009DA9] focus:ring-[#009DA9]"
                placeholder="Alamat KTP">{{ old('address', $profile->address ?? '') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
