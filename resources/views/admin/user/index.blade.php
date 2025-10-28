<x-app-admin>
    <div 
        x-data="{ openAdd:false, openEdit:false, openDelete:false, userToEdit:null, userToDelete:null }" 
        class="bg-white rounded-lg shadow-sm p-4">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Kelola Admin</h2>
            <button 
                @click="openAdd = true" 
                class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700">
                + Tambah Admin
            </button>
        </div>

        {{-- Search --}}
        <form method="GET" class="mb-3">
            <input type="text" name="search" value="{{ $search }}" 
                placeholder="Cari nama atau email..." 
                class="border px-3 py-2 rounded text-sm w-64">
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Nama</th>
                        <th class="p-2 text-left">Email</th>
                        <th class="p-2 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t">
                            <td class="p-2">{{ $user->name }}</td>
                            <td class="p-2">{{ $user->email }}</td>
                            <td class="p-2 text-center space-x-2">
                                <button 
                                    @click="openEdit=true; userToEdit={{ $user }}" 
                                    class="text-blue-500 hover:underline">Edit</button>
                                <button 
                                    @click="openDelete=true; userToDelete={{ $user }}" 
                                    class="text-red-500 hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-3">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $users->links() }}</div>

        {{-- ========== MODAL TAMBAH ========== --}}
        <div x-show="openAdd" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Tambah Admin</h3>
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
                    <div class="mb-3">
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

        {{-- ========== MODAL EDIT ========== --}}
        <template x-if="userToEdit">
            <div x-show="openEdit" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold mb-4">Edit Admin</h3>
                    <form :action="'/admin/user/' + userToEdit.id" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="block text-sm">Nama</label>
                            <input type="text" name="name" class="border rounded w-full px-3 py-2 text-sm" 
                                   :value="userToEdit.name" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm">Email</label>
                            <input type="email" name="email" class="border rounded w-full px-3 py-2 text-sm" 
                                   :value="userToEdit.email" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm">Password Baru (opsional)</label>
                            <input type="password" name="password" class="border rounded w-full px-3 py-2 text-sm">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="border rounded w-full px-3 py-2 text-sm">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="openEdit=false" class="text-gray-600 text-sm">Batal</button>
                            <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded text-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ========== MODAL HAPUS ========== --}}
        <template x-if="userToDelete">
            <div x-show="openDelete" 
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 px-4">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xs sm:max-w-sm md:max-w-md text-center">
                    <h3 class="text-lg font-semibold mb-3">Hapus Admin?</h3>
                    <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                        Apakah Anda yakin ingin menghapus 
                        <span class="font-semibold" x-text="userToDelete.name"></span>?
                    </p>
                    <form :action="'/admin/user/' + userToDelete.id" method="POST" class="flex justify-center gap-3">
                        @csrf @method('DELETE')
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
