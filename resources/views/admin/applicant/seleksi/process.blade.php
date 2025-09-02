{{-- resources/views/admin/applicant/seleksi/process.blade.php --}}
<x-app-admin>
    <div x-data="seleksiPage()" x-init="init()" x-cloak>
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
            {{-- Filter (GET) + Aksi --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
                <form method="GET" action="{{ route('admin.applicant.seleksi.process', ['stage' => $stage]) }}"
                      class="flex flex-wrap items-end gap-2">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="border rounded px-3 py-2 w-60" placeholder="Search...">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jurusan</label>
                        <select name="jurusan" class="border rounded px-3 py-2 w-56">
                            <option value="">Semua Jurusan</option>
                            @foreach($allJurusan as $jurusan)
                                <option value="{{ $jurusan }}" {{ request('jurusan')==$jurusan ? 'selected' : '' }}>
                                    {{ $jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Status (tahap ini)</label>
                        <select name="status" class="border rounded px-3 py-2 w-56">
                            <option value="">Semua Status</option>
                            <option value="{{ $stage }}" {{ request('status') == $stage ? 'selected' : '' }}>
                                {{ $stage }}
                            </option>
                            <option value="Lolos {{ $stage }}" {{ request('status') == 'Lolos '.$stage ? 'selected' : '' }}>
                                Lolos {{ $stage }}
                            </option>
                            <option value="Tidak Lolos {{ $stage }}" {{ request('status') == 'Tidak Lolos '.$stage ? 'selected' : '' }}>
                                Tidak Lolos {{ $stage }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Filter
                        </button>
                    </div>
                </form>

                <div class="flex items-center gap-2">
                    {{-- Manual: centang dulu barisnya, lalu klik salah satu --}}
                    <button type="button"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                            @click="submitStatus('lolos')">
                        Lolos
                    </button>
                    <button type="button"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                            @click="submitStatus('tidak_lolos')">
                        Gagal
                    </button>

                    {{-- Email: otomatis pilih semua yg statusnya LOLOS di halaman ini --}}
                    <button type="button"
                            @click="openEmailModalAuto()"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
                        Email
                    </button>
                </div>
            </div>

            {{-- Form UPDATE STATUS --}}
            <form id="statusForm" method="POST" action="{{ route('admin.applicant.seleksi.update-status') }}">
                @csrf
                <input type="hidden" name="stage" value="{{ $stage }}">
                <div id="statusInputs"></div>

                <div class="overflow-x-auto">
                    <table id="applicantTable" class="w-full text-sm text-left border-collapse">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border-b w-10">
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
                        @forelse ($applicants as $applicant)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 border-b align-top">
                                    <input type="checkbox"
                                           class="applicant-checkbox"
                                           name="selected_applicants[]"
                                           value="{{ $applicant->id }}"
                                           data-email="{{ $applicant->email }}"
                                           data-stage-state="{{ $applicant->_stage_state ?? 'current' }}">
                                </td>
                                <td class="p-3 border-b align-top">{{ $applicant->name }}</td>
                                <td class="p-3 border-b align-top">{{ $applicant->email }}</td>
                                <td class="p-3 border-b align-top">{{ $applicant->jurusan }}</td>
                                <td class="p-3 border-b align-top">{{ $applicant->position->name ?? '-' }}</td>
                                <td class="p-3 border-b align-top">{{ $applicant->age }} tahun</td>
                                <td class="p-3 border-b align-top">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                 {{ $applicant->_stage_badge ?? 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                                        {{ $applicant->_stage_status ?? $stage }}
                                    </span>
                                </td>
                                <td class="p-3 border-b align-top">
                                    <div class="flex items-center gap-3 text-gray-500">
                                        <a href="#" @click.prevent class="text-blue-400" title="Lihat">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="2" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            </svg>
                                        </a>
                                        <a href="#" @click.prevent class="text-amber-400" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="2" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.862 4.487M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-6 text-center text-gray-500">Tidak ada data untuk kriteria ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $applicants->withQueryString()->links() }}
                </div>
            </form>
        </div>

        {{-- Modal Email --}}
        <div x-show="emailModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6" @click.away="closeEmailModal()">
                <h2 class="text-lg font-semibold text-orange-600 mb-2">Kirim Email ke Peserta</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Penerima dipilih: <strong x-text="selectedEmails.length"></strong>
                </p>

                <form action="{{ route('admin.applicant.seleksi.sendEmail') }}"
                      method="POST" enctype="multipart/form-data"
                      @submit="validateAndSubmit">
                    @csrf
                    <input type="hidden" name="recipients" id="recipients">
                    <input type="hidden" name="recipient_ids" id="recipient_ids">
                    <input type="hidden" name="stage" value="{{ $stage }}">

                    <label class="inline-flex items-center gap-2 mb-3">
                        <input type="checkbox" name="use_template" id="use_template" value="1" checked
                               @change="toggleManualRequired($event)">
                        <span>Gunakan template otomatis (nama + tahap)</span>
                    </label>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Subjek</label>
                        <input type="text" name="subject" class="w-full border rounded-lg px-3 py-2"
                               placeholder="Subjek (opsional jika pakai template)">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">Pesan</label>
                        <textarea name="message" rows="6" class="w-full border rounded-lg px-3 py-2"
                                  placeholder="Pesan (opsional jika pakai template)"></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-1">Lampiran Jadwal (PDF, max 5MB)</label>
                        <input type="file" name="attachment" accept="application/pdf,.pdf"
                               class="w-full border rounded-lg px-3 py-2" required>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="closeEmailModal()"
                                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-admin>
