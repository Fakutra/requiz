<x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-4"
         x-data="{
            showCreate:false,
            showEdit:false,
            editId:null,
            editName:'',
            editFieldId:null,
            baseEditUrl:'{{ route('admin.subfields.update',['subfield'=>'__ID__']) }}'
         }"
         x-cloak>

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-blue-950">Master Sub Bidang</h1>

            <button type="button"
                @click="showCreate = true"
                {{ $fields->isEmpty() ? 'disabled' : '' }}
                class="inline-flex items-center px-4 py-2 rounded-full text-sm
                    {{ $fields->isEmpty()
                        ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                        : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                + Tambah Sub Bidang
            </button>
        </div>

        {{-- EMPTY STATE --}}
        @if ($fields->isEmpty())
            <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-800">
                ⚠️ <strong>Data Bidang masih kosong.</strong><br>
                Silakan isi <b>Bidang</b> terlebih dahulu sebelum menambahkan Sub Bidang.
            </div>
        @endif

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b w-16">#</th>
                        <th class="px-3 py-2 border-b text-left">Sub Bidang</th>
                        <th class="px-3 py-2 border-b text-left">Bidang</th>
                        <th class="px-3 py-2 border-b text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subfields as $i => $subfield)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border-b">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 border-b">{{ $subfield->name }}</td>
                            <td class="px-3 py-2 border-b">
                                {{ optional($subfield->field)->name }}
                            </td>
                            <td class="px-3 py-2 border-b text-center">
                                <div class="inline-flex gap-2">
                                    <button type="button"
                                        @click="
                                            showEdit = true;
                                            editId = {{ $subfield->id }};
                                            editName = '{{ e($subfield->name) }}';
                                            editFieldId = {{ $subfield->field_id ?? 'null' }};
                                        "
                                        class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.subfields.destroy',$subfield) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus sub bidang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700 hover:bg-red-200">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-500">
                                Belum ada data Sub Bidang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL CREATE --}}
        <div x-show="showCreate" class="fixed inset-0 bg-black/40 flex items-center justify-center z-40">
            <div @click.away="showCreate=false"
                 class="bg-white rounded-xl p-6 w-full max-w-md">

                <h2 class="text-lg font-semibold mb-4">Tambah Sub Bidang</h2>

                <form method="POST" action="{{ route('admin.subfields.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm">Bidang</label>
                        <select name="field_id" required class="w-full rounded border px-3 py-2">
                            @forelse ($fields as $field)
                                <option value="{{ $field->id }}">{{ $field->name }}</option>
                            @empty
                                <option value="">Data Bidang belum tersedia</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="text-sm">Nama Sub Bidang</label>
                        <input type="text" name="name" required
                               class="w-full rounded border px-3 py-2">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showCreate=false"
                                class="px-4 py-2 rounded border">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded bg-blue-600 text-white">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT SUB BIDANG --}}
        <div x-show="showEdit"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-40">

            <div @click.away="showEdit=false"
                class="bg-white rounded-xl p-6 w-full max-w-md">

                <h2 class="text-lg font-semibold mb-4">Edit Sub Bidang</h2>

                <form :action="baseEditUrl.replace('__ID__', editId)"
                    method="POST"
                    class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- BIDANG --}}
                    <div>
                        <label class="text-sm">Bidang</label>
                        <select name="field_id"
                                x-model="editFieldId"
                                required
                                class="w-full rounded border px-3 py-2">
                            @foreach ($fields as $field)
                                <option value="{{ $field->id }}">
                                    {{ $field->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- NAMA SUB BIDANG --}}
                    <div>
                        <label class="text-sm">Nama Sub Bidang</label>
                        <input type="text"
                            name="name"
                            x-model="editName"
                            required
                            class="w-full rounded border px-3 py-2">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button"
                                @click="showEdit=false"
                                class="px-4 py-2 rounded border">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded bg-blue-600 text-white">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-admin>
