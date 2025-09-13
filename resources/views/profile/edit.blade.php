<x-guest-layout>
    <div class="py-12 max-w-7xl mx-auto px-6" x-data="{ tab: 'info' }">
        <div>
            <h1 class="mb-4 font-bold text-4xl">Profil Saya</h1>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-stretch">
            {{-- Sidebar --}}
            <aside class="bg-white shadow rounded-lg p-4 md:col-span-1 h-full flex flex-col">
                <nav class="space-y-2">
                    <button @click="tab = 'info'"
                        :class="tab === 'info' ? 'bg-[#009DA9] text-white' : 'text-gray-700 hover:bg-gray-100'"
                        class="w-full text-left px-4 py-2 rounded-lg font-medium">
                        Update Info
                    </button>
                    <button @click="tab = 'password'"
                        :class="tab === 'password' ? 'bg-[#009DA9] text-white' : 'text-gray-700 hover:bg-gray-100'"
                        class="w-full text-left px-4 py-2 rounded-lg font-medium">
                        Change Password
                    </button>
                </nav>
            </aside>

            {{-- Content --}}
            <section class="md:col-span-3">
                <div x-show="tab === 'info'" x-transition>
                    <div class="p-6 bg-white shadow rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'password'" x-transition>
                    <div class="p-6 bg-white shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @if (session('status') === 'profile-updated')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Profil Anda berhasil diperbarui.',
                showConfirmButton: true,
                timer: null
            });
        });
    </script>
    @endif
</x-guest-layout>