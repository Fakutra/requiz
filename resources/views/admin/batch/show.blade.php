{{-- resources/views/admin/batch/show.blade.php --}}
<x-app-admin>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg py-4">
        <div class="relative flex items-center gap-2 mb-4 sm:px-6 lg:px-8">
            <a href="{{ route('batch.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
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
                                        <th class="px-4 py-2 text-center">No.</th>
                                        <th class="px-4 py-2">Nama Posisi</th>
                                        <th class="px-4 py-2 text-center">Kuota</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                        <th class="px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($batch->position as $position)
                                        <tr>
                                            <td class="px-4 py-2 text-center text-sm text-gray-700">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $position->name }}</td>
                                            <td class="px-4 py-2 text-center text-sm text-gray-900">{{ $position->quota }}</td>
                                            <td class="px-4 py-2 text-center">
                                                @php $active = ($position->status === 'Active'); @endphp
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                    {{ $active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $position->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    {{-- EDIT --}}
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center rounded-lg px-3 py-1.5 text-yellow-400"
                                                        onclick="openEditModal({{ json_encode([
                                                            'id'                 => $position->id,
                                                            'name'               => $position->name,
                                                            'quota'              => $position->quota,
                                                            'status'             => $position->status,
                                                            'pendidikan_minimum' => $position->pendidikan_minimum ?? '',
                                                            'deadline'           => optional($position->deadline)->format('Y-m-d'),
                                                            // ini sekarang udah array dari casts JSON, jadi cukup pakai ?? []
                                                            'description'        => $position->description ?? [],
                                                            'skills'             => $position->skills ?? [],
                                                            'requirements'       => $position->requirements ?? [],
                                                            'majors'             => $position->majors ?? [],
                                                        ]) }}, '{{ route('position.update', $position) }}')"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor"
                                                            class="size-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                        </svg>
                                                    </button>

                                                    {{-- DELETE --}}
                                                    <form action="{{ route('position.destroy', $position->id) }}"
                                                          method="post"
                                                          class="inline"
                                                          onsubmit="return confirm('Anda yakin?')">
                                                        @method('delete')
                                                        @csrf
                                                        <button class="inline-flex items-center px-3 py-1.5 text-red-600">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="2.0"
                                                                 stroke="currentColor" class="size-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
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

        {{-- modal add position --}}
        @include('admin.batch.partials.modal-add-position', ['batch' => $batch])

        {{-- modal edit position --}}
        @include('admin.batch.partials.modal-edit-position')
    </div>

    {{-- Script: buka modal edit + isi form --}}
    <script>
        function openEditModal(data, updateUrl) {
            const modalEl = document.getElementById('editPosisiModal');
            if (!modalEl) {
                console.error('editPosisiModal not found');
                return;
            }

            const form = modalEl.querySelector('form');
            form.action = updateUrl;

            // basic fields
            modalEl.querySelector('input[name="name"]').value  = data.name ?? '';
            modalEl.querySelector('input[name="quota"]').value = data.quota ?? 0;
            modalEl.querySelector('select[name="status"]').value = data.status ?? 'Active';
            modalEl.querySelector('select[name="pendidikan_minimum"]').value = data.pendidikan_minimum ?? '';
            modalEl.querySelector('input[name="deadline"]').value = data.deadline ?? '';

            // helper: array → multiline
            const arrToText = (v) => {
                if (Array.isArray(v)) return v.join('\n');
                if (typeof v === 'string') return v;
                return '';
            };

            modalEl.querySelector('textarea[name="descriptions"]').value  = arrToText(data.description);
            modalEl.querySelector('textarea[name="skills"]').value        = arrToText(data.skills);
            modalEl.querySelector('textarea[name="requirements"]').value  = arrToText(data.requirements);
            modalEl.querySelector('textarea[name="majors"]').value        = arrToText(data.majors);

            // show bootstrap modal
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    </script>
</x-app-admin>
