<x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-4"
         x-data="{
            showCreate: false,
            showEdit: false,
            editId: null,
            editName: '',
            baseEditUrl: '{{ url('admin/jobs') }}'
         }"
         x-cloak>

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-blue-950">Master Jabatan</h1>
            <button type="button"
               @click="showCreate = true"
               class="inline-flex items-center px-4 py-2 rounded-full bg-blue-600 text-white text-sm hover:bg-blue-700">
                + Tambah Jabatan
            </button>
        </div>

        @if (session('success'))
            <div class="mb-3 text-sm text-green-700 bg-green-100 border border-green-200 rounded-md px-3 py-2">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-3 text-sm text-red-700 bg-red-100 border border-red-200 rounded-md px-3 py-2">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 border-b text-left w-16">#</th>
                        <th class="px-3 py-2 border-b text-left">Nama Jabatan</th>
                        <th class="px-3 py-2 border-b text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobs as $i => $job)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border-b">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 border-b">{{ $job->name }}</td>
                            <td class="px-3 py-2 border-b text-center">
                                <div class="inline-flex gap-2">
                                    <button type="button"
                                       @click="
                                           showEdit = true;
                                           editId = {{ $job->id }};
                                           editName = '{{ e($job->name) }}';
                                       "
                                       class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')">
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
                            <td colspan="3" class="px-3 py-4 text-center text-gray-500">
                                Belum ada data jabatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL CREATE --}}
        <div x-show="showCreate"
             x-transition.opacity
             class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
            <div @click.away="showCreate = false"
                 class="bg-white rounded-2xl shadow-lg w-full max-w-md mx-4 p-6 relative">

                <button type="button"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
                        @click="showCreate = false">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Tambah Jabatan
                </h2>

                <form action="{{ route('admin.jobs.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Jabatan
                        </label>
                        <input type="text" name="name" id="name"
                               class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button"
                                @click="showCreate = false"
                                class="px-4 py-2 rounded-full text-sm border border-gray-300 text-gray-700 hover:bg-gray-100">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-full text-sm bg-blue-600 text-white hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div x-show="showEdit"
             x-transition.opacity
             class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
            <div @click.away="showEdit = false"
                 class="bg-white rounded-2xl shadow-lg w-full max-w-md mx-4 p-6 relative">

                <button type="button"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
                        @click="showEdit = false">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Edit Jabatan
                </h2>

                <form :action="baseEditUrl + '/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Jabatan
                        </label>
                        <input type="text" name="name" id="edit_name"
                               x-model="editName"
                               class="w-full border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button"
                                @click="showEdit = false"
                                class="px-4 py-2 rounded-full text-sm border border-gray-300 text-gray-700 hover:bg-gray-100">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-full text-sm bg-blue-600 text-white hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-admin>
