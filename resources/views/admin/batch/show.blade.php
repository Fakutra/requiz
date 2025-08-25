{{-- resources/views/admin/batch/show.blade.php --}}
<x-app-admin>
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    <div x-data="{
      openAdd: {{ $errors->any() ? 'true' : 'false' }},
      openEdit: {{ old('position_id_edit') ? json_encode(old('position_id_edit')) : 'null' }}
    }">
        <div class="flex flex-col gap-3 items-center justify-between sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="w-full sm:w-auto">
                <a href="{{ route('batch.index') }}"
                    class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 no-underline">
                    < Kembali
                        </a>
                        <h2 class="text-2xl font-bold text-blue-950 mt-1">
                            Kelola Posisi untuk <span class="font-bold">{{ $batch->name }}</span>
                        </h2>
            </div>
            <button
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                @click="openAdd = true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5ZM12.75 8.25a.75.75 0 0 0-1.5 0v3h-3a.75.75 0 0 0 0 1.5h3v3a.75.75 0 0 0 1.5 0v-3h3a.75.75 0 0 0 0-1.5h-3v-3Z" clip-rule="evenodd" />
                </svg>
                Tambah Posisi
            </button>
        </div>
        {{-- Success Modal --}}
        @if (session('success'))
        <div x-data="{ show:true }" x-init="$nextTick(()=> setTimeout(()=> show = true, 0))">
            <div x-show="show" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                aria-modal="true" role="dialog">
                <div class="absolute inset-0 bg-black/40" @click="show=false" aria-hidden="true"></div>
                <div class="relative w-full max-w-sm rounded-xl bg-white p-6 shadow-lg">
                    <h5 class="mb-4 text-center font-semibold text-green-600">✅ {{ session('success') }}</h5>
                    <div class="text-center">
                        <button class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                            @click="show=false">OK</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
            <div class="mx-auto max-w-7xl">
                <div class="rounded-xl bg-white shadow-sm">
                    <div class="p-0">
                        @if ($batch->position->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100 text-left text-sm font-medium text-gray-700">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-center">No.</th>
                                        <th scope="col" class="px-4 py-2">Nama Posisi</th>
                                        <th scope="col" class="px-4 py-2 text-center">Kuota</th>
                                        <th scope="col" class="px-4 py-2 text-center">Status</th>
                                        <th scope="col" class="px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($batch->position as $position)
                                    <tr>
                                        <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $position->name }}</td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-900">{{ $position->quota }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @php
                                            $active = ($position->status === 'Active');
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                   {{ $active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                                {{ $position->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button"
                                                    class="inline-flex items-center rounded-lg px-3 py-1.5 text-yellow-400"
                                                    @click="openEdit = '{{ $position->id }}'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                    </svg>
                                                </button>

                                                <form action="{{ route('position.destroy', $position->id) }}" method="post"
                                                    class="inline"
                                                    onsubmit="return confirm('Anda yakin?')">
                                                    @method('delete')
                                                    @csrf
                                                    <button class="inline-flex items-center px-3 py-1.5 text-red-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- Modal Edit Posisi --}}
                                            <div x-show="openEdit === '{{ $position->id }}'" x-cloak x-transition.opacity
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
                                                aria-modal="true" role="dialog">
                                                <div class="absolute inset-0 bg-black/40" @click="openEdit = null" aria-hidden="true"></div>
                                                <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-lg">
                                                    <div class="mb-4 flex items-center justify-between">
                                                        <h3 class="text-lg font-semibold">Edit Posisi</h3>
                                                        <button class="rounded-md p-1 hover:bg-gray-100" @click="openEdit = null">✕</button>
                                                    </div>

                                                    {{-- form edit --}}
                                                    <form action="{{ route('position.update', ['batch' => $batch->id, 'position' => $position->id]) }}" method="POST" class="space-y-4 text-left">
                                                        @csrf
                                                        @method('PUT')
                                                        <div>
                                                            <label class="mb-1 text-sm font-medium text-gray-700">Nama Posisi</label>
                                                            <input type="text" name="name" value="{{ old('name', $position->name) }}"
                                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                                                        </div>

                                                        <div>
                                                            <label class="mb-1 text-sm font-medium text-gray-700">Kuota</label>
                                                            <input type="number" name="quota" value="{{ old('quota', $position->quota) }}"
                                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" min="0" required>
                                                        </div>

                                                        <div>
                                                            <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                                                            <select name="status"
                                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                                <option value="Active" {{ old('status', $position->status) === 'Active' ? 'selected' : '' }}>Active</option>
                                                                <option value="Inactive" {{ old('status', $position->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>

                                                        {{-- buat handle re-open saat validation error --}}
                                                        <input type="hidden" name="position_id_edit" value="{{ $position->id }}">

                                                        <div class="flex justify-end gap-2 pt-2">
                                                            <button type="button"
                                                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded"
                                                                @click="openEdit = null">Batal</button>
                                                            <button type="submit"
                                                                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            {{-- /Modal Edit --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="p-8 text-center">
                            <p class="text-gray-500">Belum ada posisi yang ditambahkan untuk batch ini.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Modal Tambah --}}
            <div x-show="openAdd" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" x-transition.opacity
                aria-modal="true" role="dialog">
                <div class="absolute inset-0 bg-black/40" @click="openAdd = false" aria-hidden="true"></div>
                <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-lg">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Tambah Posisi Baru</h3>
                        <button class="rounded-md p-1 hover:bg-gray-100" @click="openAdd = false">✕</button>
                    </div>

                    {{-- form tambah --}}
                    <form action="{{ route('position.store', ['batch' => $batch->id]) }}" method="POST" class="space-y-4 text-left">
                        @csrf
                        <input type="hidden" name="batch_id" value="{{ $batch->id }}">

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nama Posisi</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Posisi"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Kuota</label>
                            <input type="number" name="quota" value="{{ old('quota') }}" placeholder="Kuota"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" min="0" required>
                            @error('quota')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                            <select name="status"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded"
                                @click="openAdd = false">Batal</button>
                            <button type="submit"
                                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- /Modal Tambah --}}
        </div>
    </div>
</x-app-admin>