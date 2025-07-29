<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pelamar') }}
        </h2>
    </x-slot>

    <!-- Modal Sukses -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="text-success">✅ {{ session('success') }}</h5>
                    <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan pesan sukses setelah update/delete --}}
                    {{-- @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif --}}

                    <!-- Baris Atas: Export, Search, dan Filter -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            {{-- Perbaikan: Tombol Export sekarang membawa semua parameter filter --}}
                            <a href="{{ route('admin.applicant.export', request()->query()) }}" class="btn btn-success">
                                Export Excel
                            </a>
                        </div>
                        <div class="d-flex align-items-center">
                            <form action="{{ route('applicant.index') }}" method="GET" class="d-flex">
                                <input type="text" class="form-control me-2" name="search" placeholder="Search.."
                                    value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">Cari</button>
                            </form>
                            <button type="button" class="btn btn-light ms-3" data-bs-toggle="modal"
                                data-bs-target="#filterModal">
                                <i class="bi bi-filter fs-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Data Pelamar -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>Umur</th>
                                    <th>Pendidikan</th>
                                    <th>Jurusan</th>
                                    {{-- <th>CV</th> --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applicants as $applicant)
                                    <tr>
                                        <td>{{ ($applicants->currentPage() - 1) * $applicants->perPage() + $loop->iteration }}
                                        </td>
                                        <td>{{ $applicant->name }}</td>
                                        <td>{{ $applicant->position->name }}</td>
                                        <td>{{ $applicant->age }} tahun</td>
                                        <td>{{ $applicant->pendidikan }} - {{ $applicant->universitas }}</td>
                                        <td>{{ $applicant->jurusan }}</td>
                                        {{-- <td>
                                            @if ($applicant->cv_document)
                                                <a href="{{ asset('storage/' . $applicant->cv_document) }}"
                                                    target="_blank" class="btn btn-sm btn-outline-primary">Lihat CV</a>
                                            @else
                                                <span class="text-muted">Tidak ada CV</span>
                                            @endif
                                        </td> --}}
                                        <td>
                                            {{-- Tombol Edit yang memicu modal --}}
                                            <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                data-bs-target="#editApplicantModal{{ $applicant->id }}">Edit</a>

                                            {{-- Perbaikan: Form Delete menggunakan named route yang benar --}}
                                            <form action="{{ route('admin.applicant.destroy', $applicant->id) }}"
                                                method="post" class="d-inline">
                                                @method('delete')
                                                @csrf
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- =================================================================== -->
                                    <!-- KODE MODAL EDIT DARI ARTIFACT DIMASUKKAN DI SINI (DALAM LOOP) -->
                                    <!-- =================================================================== -->
                                    <div class="modal fade" id="editApplicantModal{{ $applicant->id }}" tabindex="-1"
                                        aria-labelledby="editModalLabel{{ $applicant->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.applicant.update', $applicant->id) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editModalLabel{{ $applicant->id }}">Edit Data:
                                                            {{ $applicant->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Nama</label>
                                                                <input type="text" name="name"
                                                                    class="form-control" value="{{ $applicant->name }}"
                                                                    required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" name="email"
                                                                    class="form-control"
                                                                    value="{{ $applicant->email }}" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">NIK</label>
                                                                <input type="text" name="nik"
                                                                    class="form-control" value="{{ $applicant->nik }}"
                                                                    maxlength="16" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">No. Telepon</label>
                                                                <input type="text" name="no_telp"
                                                                    class="form-control"
                                                                    value="{{ $applicant->no_telp }}" maxlength="14"
                                                                    required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Tempat Lahir</label>
                                                                <input type="text" name="tpt_lahir"
                                                                    class="form-control"
                                                                    value="{{ $applicant->tpt_lahir }}" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Tanggal Lahir</label>
                                                                <input type="date" name="tgl_lahir"
                                                                    class="form-control"
                                                                    value="{{ $applicant->tgl_lahir }}" required>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label class="form-label">Alamat</label>
                                                                <textarea name="alamat" class="form-control" required>{{ $applicant->alamat }}</textarea>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Pendidikan</label>
                                                                <select name="pendidikan" class="form-select"
                                                                    required>
                                                                    @foreach (['SMA/Sederajat', 'Diploma', 'S1', 'S2', 'S3'] as $jenjang)
                                                                        <option value="{{ $jenjang }}"
                                                                            {{ $applicant->pendidikan == $jenjang ? 'selected' : '' }}>
                                                                            {{ $jenjang }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Universitas</label>
                                                                <input type="text" name="universitas"
                                                                    class="form-control"
                                                                    value="{{ $applicant->universitas }}" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Jurusan</label>
                                                                <input type="text" name="jurusan"
                                                                    class="form-control"
                                                                    value="{{ $applicant->jurusan }}" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Tahun Lulus</label>
                                                                <input type="text" name="thn_lulus"
                                                                    class="form-control"
                                                                    value="{{ $applicant->thn_lulus }}" required>
                                                            </div>

                                                            <div class="col-md-6 mb-3"><label
                                                                    for="position_id-{{ $applicant->id }}"
                                                                    class="form-label">Posisi</label>
                                                                <select class="form-select"
                                                                    id="position_id-{{ $applicant->id }}"
                                                                    name="position_id" required>
                                                                    @foreach ($positions as $position)
                                                                        <option value="{{ $position->id }}"
                                                                            {{ old('position_id', $applicant->position_id) == $position->id ? 'selected' : '' }}>
                                                                            {{ $position->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    @foreach (['Seleksi Administrasi', 'Tidak Lolos Seleksi Administrasi', 'Seleksi Tes Tulis', 'Lolos Seleksi Tes Tulis', 'Tidak Lolos Seleksi Tes Tulis'] as $status)
                                                                        <option value="{{ $status }}"
                                                                            {{ $applicant->status == $status ? 'selected' : '' }}>
                                                                            {{ $status }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label class="form-label">Skills</label>
                                                                <textarea type="text" name="skills" class="form-control" required readonly>{{ $applicant->skills ?? '-' }}</textarea>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label class="form-label">CV dan Dokumen Pendukung
                                                                    (PDF, Max 3MB)
                                                                </label>
                                                                <input type="file" name="cv_document"
                                                                    class="form-control">
                                                                @if ($applicant->cv_document)
                                                                    <small class="text-muted">File saat ini: <a
                                                                            href="{{ asset('storage/' . $applicant->cv_document) }}"
                                                                            target="_blank">Lihat CV</a></small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan
                                                            Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- =================================================================== -->

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Data tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $applicants->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filter (tetap di sini) -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('applicant.index') }}" method="GET">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="Seleksi Administrasi"
                                    {{ request('status') == 'Seleksi Administrasi' ? 'selected' : '' }}>Seleksi
                                    Administrasi</option>
                                <option value="Tidak Lolos Seleksi Administrasi"
                                    {{ request('status') == 'Tidak Lolos Seleksi Administrasi' ? 'selected' : '' }}>
                                    Tidak Lolos Seleksi Administrasi</option>
                                <option value="Seleksi Tes Tulis"
                                    {{ request('status') == 'Seleksi Tes Tulis' ? 'selected' : '' }}>Seleksi Tes Tulis
                                </option>
                                <option value="Lolos Seleksi Tes Tulis"
                                    {{ request('status') == 'Lolos Seleksi Tes Tulis' ? 'selected' : '' }}>Lolos
                                    Seleksi Tes Tulis</option>
                                <option value="Tidak Lolos Seleksi Tes Tulis"
                                    {{ request('status') == 'Tidak Lolos Seleksi Tes Tulis' ? 'selected' : '' }}>Tidak
                                    Lolos Seleksi Tes Tulis</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="positionFilter" class="form-label">Posisi</label>
                            <select class="form-select" name="position" id="positionFilter">
                                <option value="">Semua Posisi</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}"
                                        {{ request('position') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('applicant.index') }}" class="btn btn-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
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
</x-app-layout>
