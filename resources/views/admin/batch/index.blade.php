{{-- resources/views/admin/batch/index.blade.php --}}
<x-app-admin>
    <div x-data="{ showAddBatch: false, showEditBatch: false }">
<<<<<<< HEAD
        <div class="flex flex-col gap-3 items-center justify-between sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-blue-950 w-full sm:w-auto">Kelola Batch</h1>
            <button @click="showAddBatch = true, showEditBatch = false" class="w-full sm:w-auto inline-flex items-center justify-center gap-1 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" data-bs-toggle="modal" data-bs-target="#tambahBatch">
=======
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-blue-950">Kelola Batch</h1>
            <button @click="showAddBatch = true, showEditBatch = false" class="bg-blue-600 rounded py-2 px-3 flex text-white" data-bs-toggle="modal" data-bs-target="#tambahBatch">
>>>>>>> origin/main
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 me-2">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
                </svg>
                <span>Create New Batch</span>
            </button>
        </div>
        <!-- Modal Create -->
        <div x-show="showAddBatch" x-transition.opacity x-cloak class="fixed inset-0 backdrop-blur-md bg-black/20 flex items-center justify-center z-50">
            <div @click.away="showAddBatch = false" class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
                <button @click="showAddBatch = false" class="absolute top-3 right-3">✕</button>
                <h2 class="text-lg font-semibold mb-4">Tambah Batch</h2>
                @include('admin.batch.partials.modal-add-batch')
            </div>
        </div>

        <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
            <div class="space-y-2">
                @forelse ($batchs as $batch)
                <div>
                    <div class="flex flex-col md:flex-row justify-between items-center bg-white rounded-lg border border-zinc-300 p-4 mb-4">
<<<<<<< HEAD
                        <div class="w-full md:w-auto md:me-3 mb-2 md:mb-0">
=======
                        <div class="md:me-3 mb-2 md:mb-0">
>>>>>>> origin/main
                            <h5 class="mb-0 font-bold flex items-center">
                                {{ $batch->name }}
                                <span class="ms-3 px-2 py-1 rounded text-white text-xs font-medium 
                        {{ $batch->status == 'Active' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ $batch->status }}
                                </span>
                            </h5>
                            <small class="text-gray-500 block mt-1 flex items-center">
                                <i class="bi bi-calendar-range"></i>
                                {{ \Carbon\Carbon::parse($batch->start_date)->translatedFormat('d F Y') }} -
                                {{ \Carbon\Carbon::parse($batch->end_date)->translatedFormat('d F Y') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-briefcase"></i>
                                {{ $batch->position_count }} Posisi
                            </small>
                        </div>
<<<<<<< HEAD
                        <div class="flex flex-wrap gap-2 w-full justify-between sm:w-auto mt-1">
=======
                        <div class="flex flex-wrap gap-2">
                            {{-- TOMBOL BARU UNTUK KE HALAMAN SHOW --}}
>>>>>>> origin/main
                            <a href="{{ route('batch.show', $batch) }}"
                                class="text-blue-500 px-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>

                            <button @click="showEditBatch = true, showAddBatch = false" class="text-yellow-400 px-1 flex items-center"
                                data-bs-toggle="modal"
                                data-bs-target="#editBatch{{ $batch->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </button>

                            <!-- Modal Edit -->
                            <div x-show="showEditBatch" x-transition.opacity x-cloak class="fixed inset-0 backdrop-blur-md bg-black/20 flex items-center justify-center z-50">
                                <div @click.away="showEditBatch = false" class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
                                    <button @click="showEditBatch = false" class="absolute top-3 right-3">✕</button>
                                    <h2 class="text-lg font-semibold mb-4">Edit Batch</h2>
                                    @include('admin.batch.partials.modal-edit-batch', ['batch' => $batch])
                                </div>
                            </div>

                            <form action="{{ route('batch.destroy', $batch->id) }}" method="post" class="inline"
                                onsubmit="return confirm('Anda yakin ingin menghapus batch ini?')">
                                @method('delete')
                                @csrf
                                <button type="submit" class="text-red-600 px-1 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p>Tidak ada batch.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-admin>