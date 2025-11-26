<x-app-admin>
    <div x-data="{ showAddBatch: false, showEditBatch: null }">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-blue-950">Kelola Batch</h1>

            <button @click="showAddBatch = true; showEditBatch = null"
                class="bg-blue-600 rounded py-2 px-3 flex text-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 me-2">
                    <path fill-rule="evenodd"
                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z"
                        clip-rule="evenodd" />
                </svg>
                <span>Create New Batch</span>
            </button>
        </div>

        {{-- Modal Create --}}
        <div x-show="showAddBatch" x-transition.opacity x-cloak
            class="fixed inset-0 backdrop-blur-md bg-black/20 flex items-center justify-center z-50">
            <div @click.away="showAddBatch = false" class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
                <button @click="showAddBatch = false" class="absolute top-3 right-3">✕</button>
                <h2 class="text-lg font-semibold mb-4">Tambah Batch</h2>
                @include('admin.batch.partials.modal-add-batch')
            </div>
        </div>

        <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
            <div class="space-y-2">
                @forelse ($batchs as $batch)
                    <div class="flex flex-col md:flex-row justify-between items-center bg-white rounded-lg border border-zinc-300 p-4 mb-4">
                        <div class="w-full md:w-auto md:me-3 mb-2 md:mb-0">
                            <h5 class="mb-0 font-bold flex items-center">
                                {{ $batch->name }}
                                <span class="ms-3 px-2 py-1 rounded text-white text-xs font-medium
                                    {{ $batch->status == 'Active' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ $batch->status }}
                                </span>
                            </h5>
                            <small class="text-gray-500 block mt-1 flex items-center gap-1">
                                <i class="bi bi-calendar-range"></i>
                                {{ \Carbon\Carbon::parse($batch->start_date)->translatedFormat('d F Y') }} -
                                {{ \Carbon\Carbon::parse($batch->end_date)->translatedFormat('d F Y') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-briefcase"></i>
                                {{ $batch->position_count }} Posisi
                            </small>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            {{-- TOMBOL SHOW --}}
                            <a href="{{ route('batch.show', $batch) }}" class="text-blue-500 px-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 32 32" class="size-6">
                                    <path fill="currentColor"
                                        d="M14 3c-1.094 0-2 .906-2 2v1H9V5H7v1H5c-1.094 0-2 .906-2 2v16c0 1.094.906 2 2 2h22c1.094 0 2-.906 2-2V8c0-1.094-.906-2-2-2h-2V5h-2v1h-3V5c0-1.094-.906-2-2-2zm0 2h4v1h-4zM5 8h22v16h-2V9h-2v15H9V9H7v15H5z" />
                                </svg>
                            </a>

                            {{-- TOMBOL EDIT --}}
                            <button @click="showEditBatch = {{ $batch->id }}; showAddBatch = false"
                                class="text-yellow-400 px-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.0" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </button>

                            {{-- MODAL EDIT --}}
                            <div x-show="showEditBatch === {{ $batch->id }}" x-transition.opacity x-cloak
                                class="fixed inset-0 backdrop-blur-md bg-black/20 flex items-center justify-center z-50">
                                <div @click.away="showEditBatch = null"
                                    class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
                                    <button @click="showEditBatch = null" class="absolute top-3 right-3">✕</button>
                                    <h2 class="text-lg font-semibold mb-4">Edit Batch</h2>

                                    @include('admin.batch.partials.modal-edit-batch', ['batch' => $batch])
                                </div>
                            </div>

                            {{-- TOMBOL DELETE --}}
                            <form action="{{ route('batch.destroy', $batch->id) }}" method="post" class="inline"
                                onsubmit="return confirm('Anda yakin ingin menghapus batch ini?')">
                                @method('delete')
                                @csrf
                                <button type="submit" class="text-red-600 px-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.0" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p>Tidak ada batch.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-admin>
