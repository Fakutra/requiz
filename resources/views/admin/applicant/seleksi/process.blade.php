<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Process Selection: ') }} {{ $stage }}
            </h2>
        </div>
    </x-slot>

    <div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="text-center mb-4">Daftar Peserta {{ $stage }}</h3>

            <!-- Filter dan Aksi -->
            <div class="d-flex justify-content-between mb-3">
                <input type="text" class="form-control w-25" placeholder="Search..." id="searchInput">
                <div>
                    <button type="submit" class="btn btn-success me-1" onclick="submitWithStatus('lolos')">Lolos</button>
                    <button type="submit" class="btn btn-danger me-1" onclick="submitWithStatus('tidak_lolos')">Gagal</button>
                </div>
            </div>

            <form action="{{ route('admin.applicant.seleksi.update-status') }}" method="POST" id="statusForm">
                @csrf
                <input type="hidden" name="stage" value="{{ $stage }}">
                <input type="hidden" name="submit_action" id="submitAction">

                <div class="table-responsive">
                    <table class="table table-striped" id="applicantTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>
                                    <div class="dropdown">
                                        <span>Jurusan</span>
                                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown"></a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage]) }}">Semua</a></li>
                                            @foreach ($allJurusan as $jurusan)
                                                <li>
                                                    <a class="dropdown-item {{ request('jurusan') == $jurusan ? 'active' : '' }}"
                                                    href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage, 'jurusan' => $jurusan, 'status' => request('status')]) }}">
                                                        {{ $jurusan }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </th>
                                <th>Posisi</th>
                                <th>Umur</th>
                                <th>
                                    <div class="dropdown">
                                        <span>Status Seleksi</span>
                                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown"></a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage, 'jurusan' => request('jurusan')]) }}">Semua</a></li>
                                            @foreach ($filteredStatuses as $status)
                                                <li>
                                                    <a class="dropdown-item {{ request('status') == $status ? 'active' : '' }}"
                                                    href="{{ route('admin.applicant.seleksi.process', ['stage' => $stage, 'jurusan' => request('jurusan'), 'status' => $status]) }}">
                                                        {{ $status }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($applicants as $applicant)
                            <tr>
                                <td>
                                    <input type="checkbox" class="applicant-checkbox" name="selected_applicants[]"
                                        value="{{ $applicant->id }}">
                                </td>
                                <td>{{ $applicant->name }}</td>
                                <td>{{ $applicant->email }}</td>
                                <td>{{ $applicant->jurusan }}</td>
                                <td>{{ $applicant->position->name ?? '-' }}</td>
                                <td>{{ $applicant->age }} tahun</td>
                                <td>
                                    {{ $stage }}
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#viewModal{{ $applicant->id }}">View</a>
                                    <!-- Tombol Edit -->
                                    <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $applicant->id }}">Edit</a>

                                    <!-- Modal Edit Profil Peserta -->
                                    <div class="modal fade" id="editModal{{ $applicant->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $applicant->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form action="{{ route('applicant.update', $applicant->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel{{ $applicant->id }}">Edit Profil - {{ $applicant->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>Nama</label>
                                                            <input type="text" name="name" value="{{ $applicant->name }}" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Email</label>
                                                            <input type="email" name="email" value="{{ $applicant->user->email ?? '' }}" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>No. Telp</label>
                                                            <input type="text" name="nik" value="{{ $applicant->no_telp }}" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>NIK</label>
                                                            <input type="text" name="nik" value="{{ $applicant->nik }}" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Tempat Lahir</label>
                                                            <input type="text" name="tpt_lahir" value="{{ $applicant->tpt_lahir }}" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Tanggal Lahir</label>
                                                            <input type="date" name="tgl_lahir" value="{{ $applicant->tgl_lahir }}" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Pendidikan</label>
                                                            <input type="text" name="pendidikan" value="{{ $applicant->pendidikan }}" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Universitas</label>
                                                            <input type="text" name="instansi" value="{{ $applicant->instansi }}" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Jurusan</label>
                                                            <input type="text" name="jurusan" value="{{ $applicant->jurusan ?? '' }}" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Posisi Dilamar</label>
                                                            <input type="text" value="{{ $applicant->position->name ?? '-' }}" class="form-control" disabled>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>CV (PDF)</label><br>
                                                            @if ($applicant->cv)
                                                                <a href="{{ asset('storage/' . $applicant->cv) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2">Lihat CV Saat Ini</a>
                                                            @endif
                                                            <input type="file" name="cv" accept="application/pdf" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.applicant.destroySeleksi', $applicant->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal View -->
                            <div class="modal fade" id="viewModal{{ $applicant->id }}" tabindex="-1"
                                aria-labelledby="modalViewLabel{{ $applicant->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Peserta: {{ $applicant->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Email:</strong> {{ $applicant->email }}</p>
                                            <p><strong>NIK:</strong> {{ $applicant->nik }}</p>
                                            <p><strong>No. Telp:</strong> {{ $applicant->no_telp }}</p>
                                            <p><strong>Tempat, Tanggal Lahir:</strong> {{ $applicant->tpt_lahir }}, {{ \Carbon\Carbon::parse($applicant->tgl_lahir)->format('d-m-Y') }}</p>
                                            <p><strong>Pendidikan:</strong> {{ $applicant->pendidikan }}</p>
                                            <p><strong>Universitas:</strong> {{ $applicant->universitas }}</p>
                                            <p><strong>Jurusan:</strong> {{ $applicant->jurusan }}</p>
                                            <p><strong>Posisi:</strong> {{ $applicant->position->name ?? '-' }}</p>
                                            <p><strong>Status Seleksi:</strong> {{ $applicant->status }}</p>
                                            <p><strong>CV:</strong>
                                                @if ($applicant->cv_document)
                                                    <a href="{{ asset('storage/' . $applicant->cv_document) }}" target="_blank">Lihat CV</a>
                                                @else
                                                    Tidak tersedia
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>


    <script>
        // Select All
        document.getElementById('selectAll').addEventListener('click', function (e) {
            document.querySelectorAll('.applicant-checkbox').forEach(cb => cb.checked = e.target.checked);
        });

        // Search Filter
        document.getElementById('searchInput').addEventListener('input', function () {
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
        }
    </script>
</x-app-layout>
