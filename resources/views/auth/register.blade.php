<x-guest-layout>
    <div class="bg-[#EDF7FB] py-10 flex justify-center items-center">
        <div class="w-full max-w-lg bg-white overflow-hidden rounded-[16px] shadow-md p-6 sm:p-8 lg:p-12">
            <div class="min-h-max flex flex-col justify-center items-center mt-8 sm:mt-12">
                <x-login-logo class="w-20 h-20 fill-current text-gray-500" />
                <p class="font-gabarito font-bold text-[20px] sm:text-[24px] mt-2">Register To ReQuiz</p>
            </div>
            <form method="POST" action="{{ route('register') }}" x-data="{ loading: false, modal:false, agreed:false }" x-ref="form" @submit.prevent="modal = true" class="mt-6">
                @csrf
                

                <!-- Name -->
                <div class="mt-4">
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Nama Lengkap" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" placeholder="Email Aktif" :value="old('email')" required autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input id="password" class="block w-full"
                        type="password"
                        name="password"
                        required autocomplete="new-password"
                        placeholder="Password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation"
                        required autocomplete="new-password"
                        placeholder="Confirm Password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex flex-col items-center mt-6">
                    <button type="submit" class="flex items-center justify-center bg-[#1F2855] w-[153px] h-[54px] text-white text-base font-medium rounded-[30px] shadow-md" :disabled="loading">
                        <span x-show="!loading">Register Akun</span>
                        <svg x-show="loading" class="animate-spin h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                    </button>
                    <a class="underline text-sm mt-3 text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        {{ __('Sudah registrasi? Login Sekarang') }}
                    </a>
                </div>

                {{-- MODAL T&C --}}
                <div x-show="modal" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center">
                    {{-- backdrop --}}
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="modal=false"></div>

                    {{-- dialog --}}
                    <div class="relative bg-white w-full max-w-lg mx-4 rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">
                            Syarat & Ketentuan
                        </h3>
                        <p class="font-regular">
                            Dengan melakukan registrasi pada website ini, Anda setuju untuk terikat oleh Syarat dan Ketentuan yang berlaku.
                            Harap membaca secara seksama sebelum melanjutkan proses registrasi. Ketentuan ini disusun untuk melindungi hak
                            dan kewajiban pengguna serta pihak pengelola website.
                        </p>

                        <div class="overflow-y-auto mt-4 max-h-64">
                            <ol class="list-decimal ml-5 font-regular">
                                <li><strong>Pendaftaran Akun</strong><br>
                                    Dengan melakukan registrasi, Anda menyatakan bahwa data yang diberikan adalah benar, lengkap, dan milik Anda sendiri.
                                </li>
                                <li><strong>Kerahasiaan Akun</strong><br>
                                    Anda bertanggung jawab atas kerahasiaan kredensial (email dan kata sandi) serta seluruh aktivitas
                                    yang terjadi melalui akun Anda.
                                </li>
                                <li><strong>Penggunaan yang Dilarang</strong><br>
                                    Dilarang menggunakan layanan untuk tujuan yang melanggar hukum, menipu, merugikan pihak lain, atau melanggar
                                    hak kekayaan intelektual.
                                </li>
                                <li><strong>Perubahan dan Ketersediaan Layanan</strong><br>
                                    Pengelola berhak melakukan pembaruan, perubahan, penangguhan, atau penghentian layanan sewaktu-waktu.
                                </li>
                                <li><strong>Privasi dan Data Pribadi</strong><br>
                                    Dengan mendaftar, Anda menyetujui pengumpulan dan pemrosesan data sesuai kebijakan privasi kami.
                                </li>
                                <li><strong>Pembatasan Tanggung Jawab</strong><br>
                                    Pengelola tidak bertanggung jawab atas kerugian langsung maupun tidak langsung yang timbul dari penggunaan layanan.
                                </li>
                                <li><strong>Persetujuan</strong><br>
                                    Dengan mencentang persetujuan dan menekan tombol “Registrasi Akun”, Anda dianggap telah membaca, memahami,
                                    dan menyetujui seluruh Syarat &amp; Ketentuan ini.
                                </li>
                            </ol>
                        </div>

                        {{-- Checkbox + hidden input buat server --}}
                        <div class="mt-4 flex items-start gap-3">
                            <input id="terms" type="checkbox" x-model="agreed"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="terms" class="text-sm text-gray-700">
                                Saya telah membaca dan menyetujui Syarat & Ketentuan.
                            </label>
                        </div>
                        {{-- Hidden input supaya terkirim ke server pas submit --}}
                        <input type="hidden" name="terms" :value="agreed ? 1 : ''">

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                                @click="modal=false">
                                Batal
                            </button>

                            <button type="button"
                                class="px-4 py-2 rounded-lg bg-[#1F2855] text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!agreed || loading"
                                @click="loading = true; $refs.form.submit();">
                                Registrasi Akun
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>