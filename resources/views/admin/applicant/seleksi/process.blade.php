<x-app-admin>
    <h1 class="text-2xl font-bold text-blue-950 mb-6">Daftar Peserta {{ $stage }}</h1>

    <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
        <!-- Filter dan Aksi -->
        <div class="flex justify-between mb-3">
            <input type="text" name="search" value="{{ request('search') }}"
            class="border rounded px-3 py-2 w-1/4"
            placeholder="Search...">

        <div class="flex gap-2">
            <!-- Filter Jurusan -->
            <select name="jurusan" class="border rounded px-3 py-2">
                <option value="">Semua Jurusan </option>
                @foreach(\App\Models\Applicant::select('jurusan')->distinct()->pluck('jurusan') as $jurusan)
                    <option value="{{ $jurusan }}" {{ request('jurusan')==$jurusan ? 'selected' : '' }}>
                        {{ $jurusan }}
                    </option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select name="status" class="border rounded px-3 py-2">
                <option value="">Semua Status</option>

                {{-- Sedang di tahap ini --}}
                <option value="{{ $stage }}" {{ request('status') == $stage ? 'selected' : '' }}>
                    Sedang Tahap Ini
                </option>

                {{-- Tidak lolos di tahap ini --}}
                <option value="Tidak Lolos {{ $stage }}" {{ request('status') == 'Tidak Lolos '.$stage ? 'selected' : '' }}>
                    Tidak Lolos
                </option>
            </select>

            <!-- Tombol Filter -->
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Filter
            </button>
                <!-- 🔹 Selesai Tambahan Filter -->
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mr-2 hover:bg-green-600" onclick="submitWithStatus('lolos')">Lolos</button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" onclick="submitWithStatus('tidak_lolos')">Gagal</button>
                <!-- Tombol buka modal -->
                <div x-data="{ open: false }">
                    <!-- Tombol buka modal -->
                    <button type="button" @click="open = true"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded">
                        Email
                    </button>

                    <!-- Modal -->
                    <div x-show="open" 
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                        x-cloak>
                        <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6">
                            <h2 class="text-lg font-semibold text-orange-600 mb-4">Kirim Email ke Peserta</h2>
                            <form action="{{ route('admin.applicant.seleksi.sendEmail') }}" method="POST">
                                @csrf
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
                                    <button type="button" @click="open = false"
                                            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="statusForm" method="POST" action="{{ route('admin.applicant.seleksi.update-status') }}" >
            @csrf
            <input type="hidden" name="stage" value="{{ $stage }}">
            <div id="statusInputs"></div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border-b"><input type="checkbox" id="selectAll"></th>
                            <th class="p-3 border-b">Nama</th>
                            <th class="p-3 border-b">Email</th>
                            <th class="p-3 border-b">Jurusan
                                {{-- <div x-data="{ open: false }" class="relative">
                                    <span>Jurusan</span>
                                    <button @click.prevent="open = !open" class="ml-2 text-gray-600 hover:text-gray-800">&#9662;</button>
                                    <ul x-show="open" @click.away="open = false" class="absolute mt-1 bg-white shadow-lg rounded w-40 z-50">
                                        <li>
                                            <a class="block px-3 py-2 hover:bg-gray-100" href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage]) }}">Semua</a>
                                        </li>
                                        @foreach ($allJurusan as $jurusan)
                                        <li>
                                            <a class="block px-3 py-2 hover:bg-gray-100 {{ request('jurusan') == $jurusan ? 'bg-blue-100 font-semibold' : '' }}"
                                                href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage, 'jurusan' => $jurusan, 'status' => request('status')]) }}">
                                                {{ $jurusan }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div> --}}
                            </th>
                            <th class="p-3 border-b">Posisi</th>
                            <th class="p-3 border-b">Umur</th>
                            <th class="p-3 border-b">Status Seleksi
                                {{-- <div x-data="{ open: false }" class="relative">
                                    <span>Status Seleksi</span>
                                    <button @click.prevent="open = !open" class="ml-2 text-gray-600 hover:text-gray-800">&#9662;</button>
                                    <ul x-show="open" @click.away="open = false" class="absolute mt-1 bg-white shadow-lg rounded w-48 z-50">
                                        <li>
                                            <a class="block px-3 py-2 hover:bg-gray-100" href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage, 'jurusan' => request('jurusan')]) }}">Semua</a>
                                        </li>
                                        @foreach ($filteredStatuses as $status)
                                        <li>
                                            <a class="block px-3 py-2 hover:bg-gray-100 {{ request('status') == $status ? 'bg-blue-100 font-semibold' : '' }}"
                                                href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage, 'jurusan' => request('jurusan'), 'status' => $status]) }}">
                                                {{ $status }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div> --}}
                            </th>
                            <th class="p-3 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applicants as $applicant)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">
                                <input type="checkbox" class="applicant-checkbox" name="selected_applicants[]" value="{{ $applicant->id }}"data-email="{{ $applicant->email }}">
                            </td>
                            <td class="p-3 border-b">{{ $applicant->name }}</td>
                            <td class="p-3 border-b">{{ $applicant->email }}</td>
                            <td class="p-3 border-b">{{ $applicant->jurusan }}</td>
                            <td class="p-3 border-b">{{ $applicant->position->name ?? '-' }}</td>
                            <td class="p-3 border-b">{{ $applicant->age }} tahun</td>
                            <td class="p-3 border-b">{{ $stage }}</td>
                            <td class="p-3 border-b space-x-2">
                                <div class="flex items-center gap-3">
                                    <a @click.prevent="openView({
                                })"
                                        class="text-blue-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    <a @click.prevent="openEdit({
                                })"
                                        class="text-amber-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                    {{-- <form action="{{ route('admin.applicant.destroy', $applicant->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf @method('delete')
                                        <button type="submit" class="text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <div x-data="{ open: false }">
    

    <!-- Modal Email-->
    {{-- <div x-show="open"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-cloak>
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-orange-600 mb-4">Kirim Email ke Peserta</h2>

        <form action="{{ route('admin.applicant.seleksi.sendEmail') }}" method="POST">
            @csrf
            <input type="hidden" name="recipients" id="recipients">

            <p id="recipientList" class="text-sm text-gray-500 mb-3"></p>

            <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Subjek</label>
            <input type="text" name="subject" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-orange-300" required>
            </div>

            <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Pesan</label>
            <textarea name="message" rows="6" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-orange-300" required></textarea>
            </div>

            <div class="flex justify-end space-x-2">
            <button type="button" @click="open = false"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
            <button type="submit"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Kirim</button>
            </div>
        </form>
        </div>
    </div> --}}
    </div>




    {{-- <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-1 text-gray-700 hover:text-gray-900">
            <span>Jurusan</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <ul x-show="open" @click.away="open = false"
            x-transition
            class="absolute mt-2 bg-white shadow-lg rounded w-48 z-50 border border-gray-200">
            <li>
                <a class="block px-4 py-2 hover:bg-gray-100" href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage]) }}">
                    Semua
                </a>
            </li>
            @foreach ($allJurusan as $jurusan)
            <li>
                <a class="block px-4 py-2 hover:bg-gray-100 {{ request('jurusan') == $jurusan ? 'bg-blue-100 font-semibold' : '' }}"
                    href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage, 'jurusan' => $jurusan, 'status' => request('status')]) }}">
                    {{ $jurusan }}
                </a>
            </li>
            @endforeach
        </ul>
    </div> --}}

    {{-- <!-- Trigger -->
    <button @click="editModal = true" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs hover:bg-yellow-600">
        Edit
    </button> --}}

    <!-- Modal -->
    {{-- <div x-show="editModal" x-cloak
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div @click.away="editModal = false"
            class="bg-white rounded-lg w-full max-w-3xl p-6 shadow-lg">

            <h2 class="text-lg font-semibold mb-4">Edit Profil - {{ $applicant->name }}</h2>

            <form action="{{ route('applicant.update', $applicant->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama</label>
                        <input type="text" name="name" value="{{ $applicant->name }}" class="border rounded px-3 py-2 w-full" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ $applicant->user->email ?? '' }}" class="border rounded px-3 py-2 w-full" required>
                    </div>
                    <!-- ...lanjutan form lainnya -->
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                    <button type="button" @click="editModal = false" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
                </div>
            </form>
        </div>
    </div> --}}

    <script>
        // Select All
        document.getElementById('selectAll').addEventListener('click', function(e) {
            document.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = e.target.checked);
        });

        // Search Filter
        document.getElementById('searchInput').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('#applicantTable tbody tr').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        });

        // Dynamic submit
        function submitWithStatus(status) {
            const form = document.getElementById('statusForm');
            const statusInputs = document.querySelectorAll('.applicant-checkbox');

            // Cek apakah ada yang dicentang
            let isAnyChecked = false;
            statusInputs.forEach(cb => {
                if (cb.checked) isAnyChecked = true;
            });

            if (!isAnyChecked) {
                alert("Pilih minimal satu peserta terlebih dahulu.");
                return;
            }

            // Tambahkan input hidden per peserta
            const hiddenStatuses = document.querySelectorAll('.dynamic-status-input');
            hiddenStatuses.forEach(input => input.remove()); // bersihkan input lama

            statusInputs.forEach(cb => {
                if (cb.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `status[${cb.value}]`;
                    input.value = status;
                    input.classList.add('dynamic-status-input');
                    form.appendChild(input);
                }
            });

            form.submit();
            // Check all
            document.getElementById('checkAll').addEventListener('click', function(e) {
                const checkboxes = document.querySelectorAll('input[name="selected_applicants[]"]');
                checkboxes.forEach(cb => cb.checked = e.target.checked);
            });

            // Submit form dengan status
            function submitWithStatus(status) {
                const selectedApplicants = document.querySelectorAll('input[name="selected_applicants[]"]:checked');
                if (selectedApplicants.length === 0) {
                    alert("Pilih minimal satu peserta!");
                    return;
                }

                const statusInputs = document.getElementById('statusInputs');
                statusInputs.innerHTML = '';

                selectedApplicants.forEach(el => {
                    let hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = `status[${el.value}]`;
                    hidden.value = status;
                    statusInputs.appendChild(hidden);
                });

                document.getElementById('statusForm').submit();
            }
            function openEmailModal() {
                const selected = Array.from(document.querySelectorAll('.applicant-checkbox:checked'))
                    .map(cb => cb.getAttribute('data-email') || cb.value);

                if (selected.length === 0) {
                    alert('Pilih minimal satu peserta terlebih dahulu.');
                    return;
                }

                document.getElementById('recipients').value = selected.join(',');
                document.getElementById('recipientList').innerText = "Dikirim ke: " + selected.join(', ');

                var emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
                emailModal.show();
            }
            document.getElementById('btnEmail').addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('.applicant-checkbox:checked'))
                .map(cb => cb.getAttribute('data-email') || cb.value);

            if (selected.length === 0) {
                alert('Pilih minimal satu peserta terlebih dahulu.');
                // Batalkan buka modal
                var modalEl = document.getElementById('emailModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                return false;
            }

            document.getElementById('recipients').value = selected.join(',');
            document.getElementById('recipientList').innerText = "Dikirim ke: " + selected.join(', ');
        });
        }
    </script>
</x-app-admin>