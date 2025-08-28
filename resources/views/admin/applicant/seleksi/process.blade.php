<x-app-admin>
    {{-- Pastikan AlpineJS sudah di-include di layout. Kalau belum, aktifkan ini: --}}
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <div x-data="seleksiPage()" x-cloak>
        <h1 class="text-2xl font-bold text-blue-950 mb-6">Daftar Peserta {{ $stage }}</h1>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-3 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-3 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-3 p-3 rounded bg-red-100 text-red-800">
                <strong>Gagal:</strong>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
            <!-- Filter dan Aksi -->
            <div class="flex justify-between mb-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="border rounded px-3 py-2 w-1/4"
                       placeholder="Search...">

                <div class="flex gap-2">
                    <!-- Filter Jurusan -->
                    <select name="jurusan" class="border rounded px-3 py-2">
                        <option value="">Semua Jurusan</option>
                        @foreach(\App\Models\Applicant::select('jurusan')->distinct()->pluck('jurusan') as $jurusan)
                            <option value="{{ $jurusan }}" {{ request('jurusan')==$jurusan ? 'selected' : '' }}>
                                {{ $jurusan }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Status -->
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">Semua Status</option>
                        <option value="{{ $stage }}" {{ request('status') == $stage ? 'selected' : '' }}>
                            Sedang Tahap Ini
                        </option>
                        <option value="Tidak Lolos {{ $stage }}" {{ request('status') == 'Tidak Lolos '.$stage ? 'selected' : '' }}>
                            Tidak Lolos
                        </option>
                    </select>

                    <!-- Tombol Filter -->
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Filter
                    </button>

                    <!-- Ubah Status -->
                    <button type="button"
                            class="bg-green-500 text-white px-4 py-2 rounded mr-2 hover:bg-green-600"
                            @click="submitStatus('lolos')">
                        Lolos
                    </button>
                    <button type="button"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                            @click="submitStatus('tidak_lolos')">
                        Gagal
                    </button>

                    <!-- Tombol buka modal Email -->
                    <button type="button"
                            @click="openEmailModal()"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded">
                        Email
                    </button>
                </div>
            </div>

            {{-- Form update status --}}
            <form id="statusForm" method="POST" action="{{ route('admin.applicant.seleksi.update-status') }}">
                @csrf
                <input type="hidden" name="stage" value="{{ $stage }}">
                <div id="statusInputs"></div>

                <div class="overflow-x-auto">
                    <table id="applicantTable" class="w-full text-sm text-left border-collapse">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border-b">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="p-3 border-b">Nama</th>
                            <th class="p-3 border-b">Email</th>
                            <th class="p-3 border-b">Jurusan</th>
                            <th class="p-3 border-b">Posisi</th>
                            <th class="p-3 border-b">Umur</th>
                            <th class="p-3 border-b">Status Seleksi</th>
                            <th class="p-3 border-b">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($applicants as $applicant)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 border-b">
                                    {{-- NOTE: value=id untuk update status; email disimpan di data-email --}}
                                    <input type="checkbox"
                                           class="applicant-checkbox"
                                           name="selected_applicants[]"
                                           value="{{ $applicant->id }}"
                                           data-email="{{ $applicant->email }}">
                                </td>
                                <td class="p-3 border-b">{{ $applicant->name }}</td>
                                <td class="p-3 border-b">{{ $applicant->email }}</td>
                                <td class="p-3 border-b">{{ $applicant->jurusan }}</td>
                                <td class="p-3 border-b">{{ $applicant->position->name ?? '-' }}</td>
                                <td class="p-3 border-b">{{ $applicant->age }} tahun</td>
                                <td class="p-3 border-b">{{ $stage }}</td>
                                <td class="p-3 border-b space-x-2">
                                    <div class="flex items-center gap-3">
                                        <a @click.prevent="/* openView({...}) */" class="text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="2.0" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </a>
                                        <a @click.prevent="/* openEdit({...}) */" class="text-amber-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="2.0" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <!-- Modal Email -->
        <div x-show="emailModalOpen"
             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6" @click.away="closeEmailModal()">
                <h2 class="text-lg font-semibold text-orange-600 mb-2">Kirim Email ke Peserta</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Penerima dipilih: <strong x-text="selectedEmails.length"></strong>
                </p>

                <form action="{{ route('admin.applicant.seleksi.sendEmail') }}" method="POST">
                    @csrf
                    {{-- Hidden recipients diisi otomatis --}}
                    <input type="hidden" name="recipients" id="recipients">

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Subjek</label>
                        <input type="text" name="subject" class="w-full border rounded-lg px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Pesan</label>
                        <textarea name="message" rows="6" class="w-full border rounded-lg px-3 py-2" required></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="closeEmailModal()"
                                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div> {{-- /x-data --}}

    <script>
        // Komponen Alpine untuk halaman ini
        function seleksiPage() {
            return {
                emailModalOpen: false,
                selectedEmails: [],

                // Toggle semua checkbox
                init() {
                    const selectAll = document.getElementById('selectAll');
                    if (selectAll) {
                        selectAll.addEventListener('change', (e) => {
                            document.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = e.target.checked);
                        });
                    }
                },

                // Ubah status massal
                submitStatus(status) {
                    const form = document.getElementById('statusForm');
                    const selected = Array.from(document.querySelectorAll('.applicant-checkbox:checked'));

                    if (selected.length === 0) {
                        alert("Pilih minimal satu peserta terlebih dahulu.");
                        return;
                    }

                    // Bersihkan hidden lama
                    const statusBox = document.getElementById('statusInputs');
                    statusBox.innerHTML = '';

                    // Buat hidden untuk setiap peserta
                    selected.forEach(cb => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `status[${cb.value}]`; // cb.value = ID peserta
                        hidden.value = status;
                        statusBox.appendChild(hidden);
                    });

                    form.submit();
                },

                // Buka modal email dan isi recipients dari checkbox yang dipilih
                openEmailModal() {
                    const selected = Array.from(document.querySelectorAll('.applicant-checkbox:checked'))
                        .map(cb => cb.getAttribute('data-email'))
                        .filter(Boolean);

                    if (selected.length === 0) {
                        alert('Pilih minimal satu peserta terlebih dahulu.');
                        return;
                    }

                    // Unik + isi hidden input
                    this.selectedEmails = [...new Set(selected)];
                    const input = document.getElementById('recipients');
                    if (input) input.value = this.selectedEmails.join(',');

                    this.emailModalOpen = true;
                },

                closeEmailModal() {
                    this.emailModalOpen = false;
                },
            }
        }
    </script>
</x-app-admin>
