<x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-4"
        x-data="{
            showCreate: false,
            showEdit: false,

            editId: null,
            editName: '',

            selectedSubField: null,
            selectedBidang: '',

            subFields: @js($subfields),
            baseEditUrl: '{{ route('admin.seksi.update', ['seksi' => '__ID__']) }}',

            updateBidang() {
                const sf = this.subFields.find(s => s.id == this.selectedSubField);
                this.selectedBidang = sf?.field?.name ?? '';
            }
        }"
        x-cloak
    >

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-blue-950">Master Seksi</h1>

            <button type="button"
                @click="showCreate = true"
                {{ $subfields->isEmpty() ? 'disabled' : '' }}
                class="inline-flex items-center px-4 py-2 rounded-full text-sm
                {{ $subfields->isEmpty()
                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                + Tambah Seksi
            </button>
        </div>

        {{-- EMPTY STATE --}}
        @if ($subfields->isEmpty())
            <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-800">
                ⚠️ <strong>Data Sub Bidang masih kosong.</strong><br>
                Silakan isi <b>Sub Bidang</b> terlebih dahulu.
            </div>
        @endif

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b w-16">#</th>
                        <th class="px-3 py-2 border-b">Seksi</th>
                        <th class="px-3 py-2 border-b">Sub Bidang</th>
                        <th class="px-3 py-2 border-b">Bidang</th>
                        <th class="px-3 py-2 border-b text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($seksi as $i => $row)
                        <tr>
                            <td class="px-3 py-2 border-b">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 border-b">{{ $row->name }}</td>
                            <td class="px-3 py-2 border-b">{{ $row->subField?->name }}</td>
                            <td class="px-3 py-2 border-b">{{ $row->subField?->field?->name }}</td>
                            <td class="px-3 py-2 border-b text-center">
                                <div class="inline-flex gap-2">

                                    {{-- EDIT --}}
                                    <button type="button"
                                        @click="
                                            showEdit = true;
                                            editId = {{ $row->id }};
                                            editName = @js($row->name);
                                            selectedSubField = {{ $row->sub_field_id }};
                                            updateBidang();
                                        "
                                        class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                        Edit
                                    </button>

                                    {{-- DELETE --}}
                                    <form method="POST"
                                        action="{{ route('admin.seksi.destroy', $row) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus seksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                                Belum ada data Seksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL CREATE --}}
        <div x-show="showCreate" class="fixed inset-0 bg-black/40 flex items-center justify-center z-40">
            <div @click.away="showCreate=false" class="bg-white rounded-xl p-6 w-full max-w-md">

                <h2 class="text-lg font-semibold mb-4">Tambah Seksi</h2>

                <form method="POST" action="{{ route('admin.seksi.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm">Bidang</label>
                        <input type="text" x-model="selectedBidang" readonly
                            class="w-full rounded border px-3 py-2 bg-gray-100">
                    </div>

                    <div>
                        <label class="text-sm">Sub Bidang</label>
                        <select name="sub_field_id" x-model="selectedSubField" @change="updateBidang"
                            class="w-full rounded border px-3 py-2" required>
                            <option value="">-- Pilih Sub Bidang --</option>
                            @foreach ($subfields as $sf)
                                <option value="{{ $sf->id }}">{{ $sf->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm">Nama Seksi</label>
                        <input type="text" name="name" required
                            class="w-full rounded border px-3 py-2">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showCreate=false" class="px-4 py-2 border rounded">
                            Batal
                        </button>
                        <button type="submit"
                            :disabled="!selectedSubField"
                            class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50">
                            Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div x-show="showEdit" class="fixed inset-0 bg-black/40 flex items-center justify-center z-40">
            <div @click.away="showEdit=false" class="bg-white rounded-xl p-6 w-full max-w-md">

                <h2 class="text-lg font-semibold mb-4">Edit Seksi</h2>

                <form :action="baseEditUrl.replace('__ID__', editId)" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-sm">Bidang</label>
                        <input type="text" x-model="selectedBidang" readonly
                            class="w-full rounded border px-3 py-2 bg-gray-100">
                    </div>

                    <div>
                        <label class="text-sm">Sub Bidang</label>
                        <select name="sub_field_id" x-model="selectedSubField" @change="updateBidang"
                            class="w-full rounded border px-3 py-2" required>
                            @foreach ($subfields as $sf)
                                <option value="{{ $sf->id }}">{{ $sf->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm">Nama Seksi</label>
                        <input type="text" name="name" x-model="editName" required
                            class="w-full rounded border px-3 py-2">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showEdit=false" class="px-4 py-2 border rounded">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-app-admin>
