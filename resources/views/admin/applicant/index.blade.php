<x-app-admin>
    <h1 class="text-2xl font-bold text-blue-950 mb-6">Data Applicant</h1>
    <div class="bg-white shadow-zinc-400/50 rounded-lg p-6" x-data="{   showFilter: false, 
                                                                        showEdit: false, 
                                                                        editData: {},
                                                                        mode: 'edit', 
                                                                        openEdit(data) { 
                                                                            this.mode = 'edit';
                                                                            this.editData = data; 
                                                                            this.showEdit = true; 
                                                                        },
                                                                        openView(data) {
                                                                            this.mode = 'view';
                                                                            this.editData = data;
                                                                            this.showEdit = true;
                                                                        }
                                                                    }">
        @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="flex items-start justify-between bg-green-100 border border-green-300 text-green-800 text-sm px-4 py-3 rounded relative mb-4" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="ml-4 text-green-800 hover:text-green-600 focus:outline-none">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 8.586l4.95-4.95 1.414 1.414L11.414 10l4.95 4.95-1.414 1.414L10 11.414l-4.95 4.95-1.414-1.414L8.586 10 3.636 5.05l1.414-1.414L10 8.586z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        @endif

        <!-- Baris Atas: Export, Search, dan Filter -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-3 md:gap-0">
            {{-- Tombol Export --}}
            <div>
                <a href="{{ route('admin.applicant.export', request()->query()) }}"
                    class="inline-flex no-underline items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150">
                    Export Excel
                </a>
            </div>

            {{-- Form Search + Button Filter --}}
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.applicant.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" placeholder="Search..."
                        value="{{ request('search') }}"
                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" />
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150">
                        Cari
                    </button>
                </form>

                {{-- Tombol Filter pakai modal --}}
                <!-- Tombol Filter pakai Alpine -->
                <button @click="showFilter = true"
                    class="p-2 rounded-md border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 019 17v-3.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 text-left text-sm font-medium text-gray-700">
                    <tr>
                        <th class="px-4 py-2">No.</th>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Posisi</th>
                        <th class="px-4 py-2">Umur</th>
                        <th class="px-4 py-2">Pendidikan</th>
                        <th class="px-4 py-2">Jurusan</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-800">
                    @forelse ($applicants as $applicant)
                    <tr>
                        <td class="px-4 py-2">{{ ($applicants->currentPage() - 1) * $applicants->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $applicant->name }}</td>
                        <td class="px-4 py-2">{{ $applicant->position->name }}</td>
                        <td class="px-4 py-2">{{ $applicant->age }} tahun</td>
                        <td class="px-4 py-2">{{ $applicant->pendidikan }} - {{ $applicant->universitas }}</td>
                        <td class="px-4 py-2">{{ $applicant->jurusan }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-3">
                                <a @click.prevent="openView({
                                    id: {{ $applicant->id }},
                                    name: '{{ $applicant->name }}',
                                    email: '{{ $applicant->email }}',
                                    nik: '{{ $applicant->nik }}',
                                    no_telp: '{{ $applicant->no_telp }}',
                                    tpt_lahir: '{{ $applicant->tpt_lahir }}',
                                    tgl_lahir: '{{ $applicant->tgl_lahir }}',
                                    alamat: `{{ $applicant->alamat }}`,
                                    pendidikan: '{{ $applicant->pendidikan }}',
                                    universitas: '{{ $applicant->universitas }}',
                                    jurusan: '{{ $applicant->jurusan }}',
                                    thn_lulus: '{{ $applicant->thn_lulus }}',
                                    position_id: '{{ $applicant->position_id }}',
                                    status: '{{ $applicant->status }}',
                                    skills: `{{ $applicant->skills ?? '-' }}`
                                })"
                                    class="text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a @click.prevent="openEdit({
                                    id: {{ $applicant->id }},
                                    name: '{{ $applicant->name }}',
                                    email: '{{ $applicant->email }}',
                                    nik: '{{ $applicant->nik }}',
                                    no_telp: '{{ $applicant->no_telp }}',
                                    tpt_lahir: '{{ $applicant->tpt_lahir }}',
                                    tgl_lahir: '{{ $applicant->tgl_lahir }}',
                                    alamat: `{{ $applicant->alamat }}`,
                                    pendidikan: '{{ $applicant->pendidikan }}',
                                    universitas: '{{ $applicant->universitas }}',
                                    jurusan: '{{ $applicant->jurusan }}',
                                    thn_lulus: '{{ $applicant->thn_lulus }}',
                                    position_id: '{{ $applicant->position_id }}',
                                    status: '{{ $applicant->status }}',
                                    skills: `{{ $applicant->skills ?? '-' }}`
                                })"
                                    class="text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.applicant.destroy', $applicant->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('delete')
                                    <button type="submit" class="text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div x-show="showEdit" x-cloak class="fixed inset-0 flex items-center justify-center z-50 bg-black/40 backdrop-blur-md">
                        <div @click.away="showEdit = false"
                            class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg overflow-y-auto max-h-[90vh]">
                            <h2 class="text-lg font-semibold mb-4">Edit Pelamar</h2>

                            <form :action="`/admin/applicant/${editData.id}`" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium">Nama</label>
                                        <input type="text" name="name" x-model="editData.name"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Email</label>
                                        <input type="email" name="email" x-model="editData.email"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">NIK</label>
                                        <input type="text" name="nik" x-model="editData.nik"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">No. Telepon</label>
                                        <input type="text" name="no_telp" x-model="editData.no_telp"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Tempat Lahir</label>
                                        <input type="text" name="tpt_lahir" x-model="editData.tpt_lahir"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Tanggal Lahir</label>
                                        <input type="date" name="tgl_lahir" x-model="editData.tgl_lahir"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div class="col-span-2">
                                        <label class="text-sm font-medium">Alamat</label>
                                        <textarea name="alamat" x-model="editData.alamat"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Pendidikan</label>
                                        <select name="pendidikan" x-model="editData.pendidikan"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                            <option value="">-- Pilih --</option>
                                            <option value="SMA">SMA</option>
                                            <option value="D3">D3</option>
                                            <option value="S1">S1</option>
                                            <option value="S2">S2</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Universitas</label>
                                        <input type="text" name="universitas" x-model="editData.universitas"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Jurusan</label>
                                        <input type="text" name="jurusan" x-model="editData.jurusan"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Tahun Lulus</label>
                                        <input type="text" name="thn_lulus" x-model="editData.thn_lulus"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Status</label>
                                        <select name="status" x-model="editData.status"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                            <option value="Proses">Proses</option>
                                            <option value="Diterima">Diterima</option>
                                            <option value="Ditolak">Ditolak</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Skills</label>
                                        <textarea name="skills" x-model="editData.skills"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Posisi</label>
                                        <select name="position_id" x-model="editData.position_id"
                                            class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm">
                                            @foreach ($positions as $position)
                                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end gap-2">
                                    <button type="button" @click="showEdit = false"
                                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded">Batal</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center px-4 py-4 text-gray-500">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $applicants->withQueryString()->links() }}
            </div>
        </div>

        {{-- Modal Sukses --}}
        @if(session('success'))
        <div x-data="{ showSuccess: true }" x-show="showSuccess"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-md shadow-md p-6 max-w-sm w-full text-center">
                <h5 class="text-green-600 text-lg font-semibold">
                    ✅ {{ session('success') }}
                </h5>
                <button @click="showSuccess = false"
                    class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md">
                    OK
                </button>
            </div>
        </div>
        @endif

        <!-- Modal Filter pakai Alpine -->
        <div x-show="showFilter" x-cloak
            class="fixed inset-0 flex items-center justify-center z-50 bg-black/30 backdrop-blur-sm">
            <div @click.away="showFilter = false" class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto">
                <form action="{{ route('admin.applicant.index') }}" method="GET" class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Filter Options</h2>
                        <button type="button" @click="showFilter = false" class="text-gray-500 hover:text-gray-700 text-xl">
                            &times;
                        </button>
                    </div>
                    <div class="mb-4">
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="statusFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-500 text-sm">
                            <option value="">Semua Status</option>
                            <option value="Seleksi Administrasi"
                                {{ request('status') == 'Seleksi Administrasi' ? 'selected' : '' }}>Seleksi Administrasi</option>
                            <option value="Tidak Lolos Seleksi Administrasi"
                                {{ request('status') == 'Tidak Lolos Seleksi Administrasi' ? 'selected' : '' }}>Tidak Lolos Seleksi Administrasi</option>
                            <option value="Seleksi Tes Tulis"
                                {{ request('status') == 'Seleksi Tes Tulis' ? 'selected' : '' }}>Seleksi Tes Tulis</option>
                            <option value="Lolos Seleksi Tes Tulis"
                                {{ request('status') == 'Lolos Seleksi Tes Tulis' ? 'selected' : '' }}>Lolos Seleksi Tes Tulis</option>
                            <option value="Tidak Lolos Seleksi Tes Tulis"
                                {{ request('status') == 'Tidak Lolos Seleksi Tes Tulis' ? 'selected' : '' }}>Tidak Lolos Seleksi Tes Tulis</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="positionFilter" class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                        <select name="position" id="positionFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-500 text-sm">
                            <option value="">Semua Posisi</option>
                            @foreach ($positions as $position)
                            <option value="{{ $position->id }}"
                                {{ request('position') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('admin.applicant.index') }}"
                            class="inline-flex justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm font-medium text-gray-800 rounded-md">
                            Reset
                        </a>
                        <div class="flex gap-2">
                            <button type="button" @click="showFilter = false"
                                class="inline-flex justify-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-sm font-medium text-gray-800 rounded-md">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    </script>
    @endif
</x-app-admin>