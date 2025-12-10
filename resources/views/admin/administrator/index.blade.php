{{-- resources/views/admin/user/index.blade.php --}}
<x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-4">
        {{-- Header halaman --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-blue-950">Manajemen User Admin</h1>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-4 border-b mb-4 text-sm">
            <a href="{{ route('admin.administrator.index', ['tab' => 'admin'] + request()->except('page')) }}"
                class="pb-2 border-b-2 {{ $tab === 'admin' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                Admin Internal
            </a>
            <a href="{{ route('admin.administrator.index', ['tab' => 'vendor'] + request()->except('page')) }}"
                class="pb-2 border-b-2 {{ $tab === 'vendor' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                Admin Eksternal
            </a>
        </div>

        {{-- ====================== TAB: ADMIN INTERNAL ====================== --}}
        @if ($tab === 'admin')
        <div
            x-data="{
                    openAdd:false,
                    openEdit:false,
                    openDelete:false,
                    userToEdit:null,
                    userToDelete:null
                }">
            {{-- Bar atas: Search + Tambah Admin --}}
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
                            <x-search-button />
                        </span>
                    </div>
                </form>

                {{-- Tombol Tambah Admin --}}
                <button
                    type="button"
                    @click="openAdd = true"
                    class="h-10 flex items-center gap-2 px-3 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
                    + Tambah Admin Internal
                </button>
            </div>

            {{-- Tabel Admin Internal --}}
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
                                'admin' => 'bg-orange-100 text-orange-700',
                                'vendor' => 'bg-blue-100 text-blue-700',
                                default => 'bg-green-100 text-green-700',
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
                                        <x-edit-button />
                                    </button>

                                    {{-- Delete --}}
                                    <button type="button"
                                        class="p-2 rounded hover:bg-gray-100"
                                        title="Hapus"
                                        @click="openDelete = true; userToDelete = @js([
                                                    'id'   => $user->id,
                                                    'name' => $user->name,
                                                ])">
                                        <x-delete-button />
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

            {{-- =============== MODAL TAMBAH ADMIN =============== --}}
            <div x-show="openAdd" x-cloak
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Tambah Admin
                        </h3>
                        <button class="text-gray-500 hover:text-gray-700 text-xl" @click="openAdd=false">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('admin.user.store') }}">
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

            {{-- =============== MODAL EDIT USER =============== --}}
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

            {{-- =============== MODAL HAPUS USER =============== --}}
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
        @endif

        {{-- ====================== TAB: ADMIN EKSTERNAL (VENDOR) ====================== --}}
        @if ($tab === 'vendor')

        <div
            x-data="{
                    openAdd:false,
                    openEdit:false,
                    openDelete:false,
                    userToEdit:null,
                    userToDelete:null
                }">
            {{-- Bar atas: Search + Tambah Admin --}}
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
                            <x-search-button />
                        </span>
                    </div>
                </form>

                {{-- Tombol Tambah Admin --}}
                <button
                    type="button"
                    @click="openAdd = true"
                    class="h-10 flex items-center gap-2 px-3 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
                    + Tambah Admin Eksternal
                </button>
            </div>

            {{-- Tabel Admin Eksternal --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left whitespace-nowrap">No.</th>
                            <th class="px-4 py-2 text-left whitespace-nowrap">Nama</th>
                            <th class="px-4 py-2 text-left whitespace-nowrap">Email</th>
                            <th class="px-4 py-2 text-left whitespace-nowrap">Nomor Telepon</th>
                            <th class="px-4 py-2 text-left whitespace-nowrap">Asal Vendor</th>
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
                            <td class="px-4 py-2">{{ $user->phone_number }}</td>
                            <td class="px-4 py-2">{{ $user->vendor->nama_vendor ?? '-' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center items-center gap-2">

                                    {{-- Edit --}}
                                    <button type="button"
                                        class="p-2 rounded hover:bg-gray-100"
                                        title="Edit"
                                        @click="openEdit = true; userToEdit = @js([
                                                    'id'           => $user->id,
                                                    'name'         => $user->name,
                                                    'email'        => $user->email,
                                                    'phone_number' => $user->phone_number,
                                                    'vendor_id'    => $user->vendor_id,
                                                ])">
                                        <x-edit-button />
                                    </button>

                                    {{-- Delete --}}
                                    <button type="button"
                                        class="p-2 rounded hover:bg-gray-100"
                                        title="Hapus"
                                        @click="openDelete = true; userToDelete = @js([
                                                    'id'   => $user->id,
                                                    'name' => $user->name,
                                                ])">
                                        <x-delete-button />
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

            {{-- =============== MODAL TAMBAH ADMIN EKSTERNAL =============== --}}
            <div x-show="openAdd" x-cloak
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Tambah Admin Eksternal
                        </h3>
                        <button class="text-gray-500 hover:text-gray-700 text-xl" @click="openAdd=false">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('admin.user.storeVendor') }}">
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
                            <label class="block text-sm">Nomor Telepon</label>
                            <input type="number" name="phone_number" class="border rounded w-full px-3 py-2 text-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm">Asal Vendor</label>
                            <select name="vendor_id" class="border rounded w-full px-3 py-2 text-sm" required>
                                <option value="">-- Pilih Vendor --</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                                @endforeach
                            </select>
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

            {{-- =============== MODAL EDIT USER =============== --}}
            <template x-if="userToEdit">
                <div x-show="openEdit"
                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Edit Admin Eksternal</h3>
                            <button class="text-gray-500 hover:text-gray-700 text-xl" @click="openEdit=false">&times;</button>
                        </div>

                        <form :action="'{{ url('/admin/user/vendor') }}/' + userToEdit.id" method="POST">
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

                            {{-- Nomor Telepon --}}
                            <div class="mb-3">
                                <label class="block text-sm">Nomor Telepon</label>
                                <input type="text" name="phone_number"
                                    class="border rounded w-full px-3 py-2 text-sm"
                                    :value="userToEdit.phone_number ?? ''" required>
                            </div>

                            {{-- Asal Vendor --}}
                            <div class="mb-3">
                                <label class="block text-sm">Asal Vendor</label>
                                <select name="vendor_id"
                                        class="border rounded w-full px-3 py-2 text-sm"
                                        required>
                                    <option value="">-- Pilih Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}"
                                            :selected="userToEdit.vendor_id == {{ $vendor->id }}">
                                            {{ $vendor->nama_vendor }}
                                        </option>
                                    @endforeach
                                </select>
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

            {{-- =============== MODAL HAPUS USER =============== --}}
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
        @endif
    </div>
</x-app-admin>