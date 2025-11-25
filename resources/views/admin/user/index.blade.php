<x-app-admin>
    <div
        x-data="{
            openAdd:false,
            openEdit:false,
            openDelete:false,
            userToEdit:null,
            userToDelete:null
        }"
        class="bg-white rounded-lg shadow-sm p-4"
    >
        {{-- Header --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-blue-950">Manajemen Akun</h1>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-4 border-b mb-4 text-sm">
            <a href="{{ route('admin.user.index', ['tab' => 'admin'] + request()->except('page')) }}"
            class="pb-2 border-b-2 {{ $tab === 'admin' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                Admin
            </a>
            <a href="{{ route('admin.user.index', ['tab' => 'vendor'] + request()->except('page')) }}"
            class="pb-2 border-b-2 {{ $tab === 'vendor' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                Vendor
            </a>
            <a href="{{ route('admin.user.index', ['tab' => 'user'] + request()->except('page')) }}"
            class="pb-2 border-b-2 {{ $tab === 'user' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                User
            </a>
        </div>

        {{-- Bar atas: Search + (Add Admin only on admin tab) --}}
        <div class="flex justify-between items-center mb-4 gap-3">
            {{-- Search --}}
            <form method="GET" class="flex-1 max-w-sm">
                {{-- keep tab on search --}}
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative flex items-center">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama atau email..."
                        class="w-full h-10 pl-3 pr-9 rounded text-sm 
                               border border-[#8B8B8B]
                               focus:outline-none focus:border-[#A0A0A0] 
                               focus:ring-1 focus:ring-[#A0A0A0]">
                    <span class="absolute right-3 text-gray-500">
                        <x-search-button/>
                    </span>
                </div>
            </form>

            {{-- Tombol Tambah: beda label per tab --}}
            @if ($tab === 'admin')
                <button 
                    type="button"
                    @click="openAdd = true"
                    class="h-10 flex items-center gap-2 px-3 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
                    + Tambah Admin
                </button>
            @elseif ($tab === 'vendor')
                <button 
                    type="button"
                    @click="openAdd = true"
                    class="h-10 flex items-center gap-2 px-3 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
                    + Tambah Vendor
                </button>
            @endif
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left whitespace-nowrap">No.</th>
                        <th class="px-4 py-2 text-left whitespace-nowrap">Nama</th>
                        <th class="px-4 py-2 text-left whitespace-nowrap">Email</th>
                        <th class="px-4 py-2 text-left whitespace-nowrap">Role</th>
                        <th class="px-4 py-2 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                {{ ($users->currentPage()-1)*$users->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $badgeClass = match ($user->role) {
                                        'admin'  => 'bg-orange-100 text-orange-700',
                                        'vendor' => 'bg-blue-100 text-blue-700',
                                        default  => 'bg-green-100 text-green-700',
                                    };
                                @endphp

                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center items-center gap-2">

                                    {{-- Edit --}}
                                    <button type="button"
                                        class="p-2 rounded hover:bg-gray-100"
                                        title="Edit"
                                        @click="openEdit = true; userToEdit = @js([
                                            'id'    => $user->id,
                                            'name'  => $user->name,
                                            'email' => $user->email,
                                        ])">
                                        <x-edit-button/>
                                    </button>

                                    {{-- Delete --}}
                                    <button type="button"
                                        class="p-2 rounded hover:bg-gray-100"
                                        title="Hapus"
                                        @click="openDelete = true; userToDelete = @js([
                                            'id'   => $user->id,
                                            'name' => $user->name,
                                        ])">
                                        <x-delete-button/>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                Tidak ada data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->withQueryString()->links() }}
        </div>

        {{-- ================= MODAL TAMBAH (Admin / Vendor) ================= --}}
        @if (in_array($tab, ['admin', 'vendor']))
            <div x-show="openAdd" x-cloak 
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            {{ $tab === 'admin' ? 'Tambah Admin' : 'Tambah Vendor' }}
                        </h3>
                        <button class="text-gray-500 hover:text-gray-700 text-xl" @click="openAdd=false">&times;</button>
                    </div>

                    <form method="POST" 
                        action="{{ $tab === 'admin' ? route('admin.user.store') : route('admin.user.storeVendor') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm">Nama</label>
                            <input type="text" name="name" class="border rounded w-full px-3 py-2 text-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm">Email</label>
                            <input type="email" name="email" class="border rounded w-full px-3 py-2 text-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm">Password</label>
                            <input type="password" name="password" class="border rounded w-full px-3 py-2 text-sm" required>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="border rounded w-full px-3 py-2 text-sm" required>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="openAdd=false" class="text-gray-600 text-sm">Batal</button>
                            <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ================= MODAL EDIT USER ================= --}}
        <template x-if="userToEdit">
            <div x-show="openEdit" 
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Edit User</h3>
                        <button class="text-gray-500 hover:text-gray-700 text-xl" @click="openEdit=false">&times;</button>
                    </div>

                    <form :action="'{{ url('/admin/user') }}/' + userToEdit.id" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="block text-sm">Nama</label>
                            <input type="text" name="name"
                                   class="border rounded w-full px-3 py-2 text-sm"
                                   :value="userToEdit.name" required>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm">Email</label>
                            <input type="email" name="email"
                                   class="border rounded w-full px-3 py-2 text-sm"
                                   :value="userToEdit.email" required>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm">Password Baru (opsional)</label>
                            <input type="password" name="password"
                                   class="border rounded w-full px-3 py-2 text-sm">
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                   class="border rounded w-full px-3 py-2 text-sm">
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="openEdit=false" class="text-gray-600 text-sm">
                                Batal
                            </button>
                            <button type="submit"
                                class="bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ================= MODAL HAPUS USER ================= --}}
        <template x-if="userToDelete">
            <div x-show="openDelete" 
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 px-4">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xs sm:max-w-sm md:max-w-md text-center">
                    <h3 class="text-lg font-semibold mb-3">Hapus User?</h3>
                    <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                        Apakah Anda yakin ingin menghapus 
                        <span class="font-semibold" x-text="userToDelete.name"></span>?
                    </p>
                    <form :action="'{{ url('/admin/user') }}/' + userToDelete.id"
                          method="POST"
                          class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                                @click="openDelete=false" 
                                class="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-100">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm rounded-md bg-red-600 text-white font-medium hover:bg-red-700">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-admin>
