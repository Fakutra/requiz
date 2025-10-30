{{-- resources/views/admin/batch/show.blade.php --}}
<x-app-admin>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg py-4">
        <div class = "relative flex items-center gap-2 mb-4 sm:px-6 lg:px-8">
            <a href="{{ route('batch.index') }}" 
                class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="text-lg font-semibold leading-none m-0">
                Kelola Posisi: <span class="fw-bold">{{ $batch->name }}</span>
            </h2>

            <button class="btn btn-primary ml-auto" data-bs-toggle="modal" data-bs-target="#tambahPosisi{{ $batch->id }}">
                <i class="bi bi-plus-circle me-2"></i>Tambah Posisi Baru
            </button>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if ($batch->position->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="bg-gray-100 text-gray-800">
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

    {{-- Modal Tambah Posisi --}}
    @include('admin.batch.partials.modal-add-position', ['batch' => $batch])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk menampilkan modal sukses jika ada session 'success'
            @if (session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif

            // Script untuk membuka kembali modal yang relevan jika ada error validasi
            @if ($errors->any())
                // Cek jika error berasal dari form 'tambahBatch'
                @if ($errors->has('name') || $errors->has('status') || $errors->has('start_date') || $errors->has('end_date'))
                    @if (!old('batch_id_edit')) // Hanya buka jika bukan dari edit batch
                        var tambahBatchModal = new bootstrap.Modal(document.getElementById('tambahBatch'));
                        tambahBatchModal.show();
                    @endif
                @endif

                // Cek jika error berasal dari form 'editBatch'
                var editBatchId = '{{ old('batch_id_edit') }}';
                if (editBatchId) {
                    var editBatchModal = new bootstrap.Modal(document.getElementById('editBatch' + editBatchId));
                    editBatchModal.show();
                }

                // Logika serupa bisa ditambahkan untuk modal posisi jika diperlukan
            @endif
        });
    </script>
</x-app-admin>
