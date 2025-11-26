<x-app-admin>
    <div
        x-data="{
            showVendorModal: @json($errors->any() && old('modal') === 'create'),
            showEditModal: false,
            editVendor: {
                id: null,
                nama_vendor: '',
                alamat: '',
                email: '',
                nomor_telepon: ''
            },
            openEdit(data) {
                this.editVendor.id            = data.id;
                this.editVendor.nama_vendor   = data.nama_vendor;
                this.editVendor.alamat        = data.alamat ?? '';
                this.editVendor.email         = data.email ?? '';
                this.editVendor.nomor_telepon = data.nomor_telepon ?? '';
                this.showEditModal = true;
            }
        }"
        class="bg-white rounded-lg shadow-sm p-5"
    >
        {{-- Flash message --}}
        @if (session('status'))
            <div class="mb-4 text-xs rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-2">
                {{ session('status') }}
            </div>
        @endif

        {{-- Error (global) --}}
        @if ($errors->any())
            <div class="mb-4 text-xs rounded-lg bg-rose-50 text-rose-700 border border-rose-200 px-3 py-2">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Daftar Vendor</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola vendor yang bekerja sama dengan perusahaan.
                </p>
            </div>

            <button
                type="button"
                @click="showVendorModal = true"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                + Tambah Vendor
            </button>
        </div>

        {{-- Tabel Vendor --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-gray-600 bg-gray-50 border-b">
                        <th class="px-4 py-2 text-left font-medium">Nama Vendor</th>
                        <th class="px-4 py-2 text-left font-medium">Alamat</th>
                        <th class="px-4 py-2 text-left font-medium">Email</th>
                        <th class="px-4 py-2 text-left font-medium">Nomor Telepon</th>
                        <th class="px-4 py-2 text-left font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($vendors as $vendor)
                        <tr>
                            <td class="px-4 py-3 text-gray-800 font-medium">
                                {{ $vendor->nama_vendor }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $vendor->alamat ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $vendor->email ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $vendor->nomor_telepon ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    {{-- Tombol Edit (buka modal) --}}
                                    <button
                                        type="button"
                                        x-data
                                        @click="openEdit($el.dataset)"
                                        data-id="{{ $vendor->id }}"
                                        data-nama_vendor="{{ $vendor->nama_vendor }}"
                                        data-alamat="{{ $vendor->alamat }}"
                                        data-email="{{ $vendor->email }}"
                                        data-nomor_telepon="{{ $vendor->nomor_telepon }}"
                                        class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 text-xs"
                                    >
                                        Edit
                                    </button>

                                    {{-- Hapus --}}
                                    <form
                                        action="{{ route('admin.vendor.destroy', $vendor) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus vendor ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 rounded-lg border text-red-600 hover:bg-red-50 text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                Belum ada vendor terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 flex justify-between items-center text-xs text-gray-500">
            <div>
                @if ($vendors->total() > 0)
                    Menampilkan
                    <span class="font-semibold">{{ $vendors->firstItem() }}–{{ $vendors->lastItem() }}</span>
                    dari
                    <span class="font-semibold">{{ $vendors->total() }}</span>
                    vendor
                @else
                    Tidak ada data vendor
                @endif
            </div>
            <div>
                {{ $vendors->links() }}
            </div>
        </div>

        {{-- MODAL Tambah Vendor --}}
        <div
            x-show="showVendorModal"
            x-cloak
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/40"
        >
            <div
                @click.away="showVendorModal = false"
                class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4"
            >
                <div class="border-b px-5 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Vendor</h3>
                    <button
                        type="button"
                        class="text-gray-400 hover:text-gray-600"
                        @click="showVendorModal = false"
                    >
                        ✕
                    </button>
                </div>

                <form action="{{ route('admin.vendor.store') }}" method="POST" class="px-5 py-4 space-y-3">
                    @csrf
                    <input type="hidden" name="modal" value="create">

                    {{-- Nama Vendor --}}
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="nama_vendor_modal">Nama Vendor</label>
                        <input
                            type="text"
                            name="nama_vendor"
                            id="nama_vendor_modal"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="PT Contoh Vendor"
                            value="{{ old('nama_vendor') }}"
                            required
                        >
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="alamat_modal">Alamat</label>
                        <textarea
                            name="alamat"
                            id="alamat_modal"
                            rows="2"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Jl. Contoh No. 123, Jakarta"
                        >{{ old('alamat') }}</textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        {{-- Email --}}
                        <div>
                            <label class="block text-xs text-gray-600 mb-1" for="email_modal">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email_modal"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="example@vendor.com"
                                value="{{ old('email') }}"
                            >
                        </div>

                        {{-- Nomor Telepon --}}
                        <div>
                            <label class="block text-xs text-gray-600 mb-1" for="nomor_modal">Nomor Telepon</label>
                            <input
                                type="text"
                                name="nomor_telepon"
                                id="nomor_modal"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0812xxxxxxx"
                                value="{{ old('nomor_telepon') }}"
                            >
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end gap-2 border-t mt-2">
                        <button
                            type="button"
                            class="px-4 py-2 text-xs rounded-lg border hover:bg-gray-50"
                            @click="showVendorModal = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700"
                        >
                            Simpan Vendor
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL Edit Vendor --}}
        <div
            x-show="showEditModal"
            x-cloak
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/40"
        >
            <div
                @click.away="showEditModal = false"
                class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4"
            >
                <div class="border-b px-5 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Edit Vendor</h3>
                    <button
                        type="button"
                        class="text-gray-400 hover:text-gray-600"
                        @click="showEditModal = false"
                    >
                        ✕
                    </button>
                </div>

                <form
                    method="POST"
                    x-bind:action="'{{ url('admin/vendor') }}' + '/' + editVendor.id"
                    class="px-5 py-4 space-y-3"
                >
                    @csrf
                    @method('PUT')

                    {{-- Nama Vendor --}}
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Nama Vendor</label>
                        <input
                            type="text"
                            name="nama_vendor"
                            x-model="editVendor.nama_vendor"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            required
                        >
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Alamat</label>
                        <textarea
                            name="alamat"
                            x-model="editVendor.alamat"
                            rows="2"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-3">
                        {{-- Email --}}
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Email</label>
                            <input
                                type="email"
                                name="email"
                                x-model="editVendor.email"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        {{-- Nomor Telepon --}}
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Nomor Telepon</label>
                            <input
                                type="text"
                                name="nomor_telepon"
                                x-model="editVendor.nomor_telepon"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end gap-2 border-t mt-2">
                        <button
                            type="button"
                            class="px-4 py-2 text-xs rounded-lg border hover:bg-gray-50"
                            @click="showEditModal = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-admin>