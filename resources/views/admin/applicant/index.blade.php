<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pelamar') }}
        </h2>
    </x-slot>


    {{-- Card Tab Section --}}
    <div class="row max-w-7xl mx-auto py-4">
        <div class="col-12">
            <div class="card">
                <input type="radio" class="d-none" name="summary-tab" id="tab-chart">
                <input type="radio" class="d-none" name="summary-tab" id="tab-text">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-header-action">
                        <label for="tab-text" class="btn btn-outline-primary tab-label">Seluruh Pelamar</label>
                        <label for="tab-chart" class="btn btn-outline-primary tab-label">Screening</label>
                    </div>
                </div>

                <div class="card-body">
                    <div class="summary">
                        {{-- Tab All Pelamar --}}
                        <div class="summary-info" id="summary-text">
                            <div class="row justify-content-end mb-3">
                                <div class="col-md-6">
                                    <form action="/admin/applicant">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Search.."
                                                name="search_all" value="{{ request('search_all') }}">
                                            <button class="btn btn-info" type="submit">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
                                            <th>CV</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="applicant-table">
                                        @foreach ($applicantAll as $all)
                                            <tr>
                                                <td>{{ ($applicantAll->currentPage() - 1) * $applicantAll->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $all->name }}</td>
                                                <td>{{ $all->position->name }}</td>
                                                <td>{{ $all->age }} tahun</td>
                                                <td>{{ $all->pendidikan }} -
                                                    {{ $all->universitas }}</td>
                                                <td>{{ $all->jurusan }}</td>
                                                <td>
                                                    @if ($all->cv_document)
                                                        <a href="{{ asset('storage/' . $all->cv_document) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">Lihat
                                                            CV</a>
                                                    @else
                                                        <span class="text-muted">Tidak ada
                                                            CV</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="" class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEdit{{ $all->id }}">Edit</a>
                                                    <form action="/admin/applicant/{{ $all->id }}" method="post"
                                                        class="d-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $applicantAll->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                        @foreach ($applicantAll as $all)
                            <!-- Modal Edit Applicant -->
                            <div class="modal fade" id="modalEdit{{ $all->id }}" tabindex="-1"
                                aria-labelledby="modalEditLabel{{ $all->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('applicant.update', $all->id) }}"
                                        id="formEditApplicant{{ $all->id }}" method="POST"
                                        enctype="multipart/form-data" class="modal-content">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalEditLabel{{ $all->id }}">
                                                Edit Data Pelamar : {{ $all->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $all->name }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ $all->email }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">NIK</label>
                                                    <input type="text" name="nik" class="form-control"
                                                        value="{{ $all->nik }}" maxlength="16" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">No. Telepon</label>
                                                    <input type="text" name="no_telp" class="form-control"
                                                        value="{{ $all->no_telp }}" maxlength="14" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Tempat Lahir</label>
                                                    <input type="text" name="tpt_lahir" class="form-control"
                                                        value="{{ $all->tpt_lahir }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Tanggal Lahir</label>
                                                    <input type="date" name="tgl_lahir" class="form-control"
                                                        value="{{ $all->tgl_lahir }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Pendidikan</label>
                                                    <select name="pendidikan" class="form-select" required>
                                                        @foreach (['SMA/Sederajat', 'Diploma', 'S1', 'S2', 'S3'] as $jenjang)
                                                            <option value="{{ $jenjang }}"
                                                                {{ $all->pendidikan == $jenjang ? 'selected' : '' }}>
                                                                {{ $jenjang }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Universitas</label>
                                                    <input type="text" name="universitas" class="form-control"
                                                        value="{{ $all->universitas }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Jurusan</label>
                                                    <input type="text" name="jurusan" class="form-control"
                                                        value="{{ $all->jurusan }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        @foreach (['Seleksi Administrasi', 'Tidak Lolos Seleksi Administrasi', 'Seleksi Tes Tulis'] as $status)
                                                            <option value="{{ $status }}"
                                                                {{ $all->status == $status ? 'selected' : '' }}>
                                                                {{ $status }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">CV dan Dokumen Tambahan (PDF, Max
                                                        3MB)</label>
                                                    <input type="file" name="cv_document" class="form-control">
                                                    @if ($all->cv_document)
                                                        <small class="text-muted">File saat ini: <a
                                                                href="{{ asset('storage/' . $all->cv_document) }}"
                                                                target="_blank">Lihat CV</a></small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary"
                                                id="formEditApplicant{{ $all->id }}">Simpan
                                                Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        {{-- Tab Screening --}}
                        <div class="summary-chart" id="summary-chart">
                            @foreach ($applicants as $positionName => $applicantsGroup)
                                @php
                                    $slugId = Str::slug($positionName, '_');
                                @endphp

                                <div class="accordion mb-3" id="accordion_{{ $slugId }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading_{{ $slugId }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse_{{ $slugId }}" aria-expanded="false"
                                                aria-controls="collapse_{{ $slugId }}">
                                                {{ $positionName }} -
                                                (
                                                {{ $applicantsGroup->where('status', 'Seleksi Administrasi')->count() }}
                                                / {{ $applicantsGroup->count() }}
                                                Pelamar )
                                            </button>
                                        </h2>
                                        <div id="collapse_{{ $slugId }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading_{{ $slugId }}"
                                            data-bs-parent="#accordion_{{ $slugId }}">
                                            <div class="accordion-body">
                                                <form action="{{ route('applicant.update.status') }}" method="POST">
                                                    @csrf
                                                    <div
                                                        class="mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">

                                                        {{-- Tombol Aksi --}}
                                                        <div class="d-flex gap-2">
                                                            <form method="POST"
                                                                action="{{ route('applicant.update.status') }}">
                                                                @csrf
                                                                <button type="submit" name="status"
                                                                    value="Tidak Lolos Seleksi Administrasi"
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Apakah Anda Yakin ?')">
                                                                    Tidak Lolos
                                                                </button>
                                                            </form>

                                                            <form method="POST"
                                                                action="{{ route('applicant.update.status') }}">
                                                                @csrf
                                                                <button type="submit" name="status"
                                                                    value="Seleksi Tes Tulis"
                                                                    class="btn btn-success btn-sm"
                                                                    onclick="return confirm('Apakah Anda Yakin ?')">
                                                                    Lolos
                                                                </button>
                                                            </form>
                                                        </div>

                                                        {{-- Form Pencarian --}}
                                                        <form action="/admin/applicant" method="GET"
                                                            class="d-flex gap-2">
                                                            <input type="text" class="form-control form-control-sm"
                                                                name="search_screening" placeholder="Search.."
                                                                value="{{ request('search_screening') }}">
                                                            <button class="btn btn-info btn-sm"
                                                                type="submit">Search</button>
                                                        </form>

                                                    </div>


                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="selectAll"></th>
                                                                    <th>Nama</th>
                                                                    <th>Umur</th>
                                                                    <th>Pendidikan</th>
                                                                    <th>Jurusan</th>
                                                                    <th>CV</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($applicantsGroup->where('status', 'Seleksi Administrasi') as $index => $applicant)
                                                                    <tr>
                                                                        <td><input type="checkbox"
                                                                                class="applicant-checkbox"
                                                                                name="selected_applicants[]"
                                                                                value="{{ $applicant->id }}"></td>
                                                                        <td>{{ $applicant->name }}</td>
                                                                        <td>{{ $applicant->age }} tahun</td>
                                                                        <td>{{ $applicant->pendidikan }} -
                                                                            {{ $applicant->universitas }}</td>
                                                                        <td>{{ $applicant->jurusan }}</td>
                                                                        <td>
                                                                            @if ($applicant->cv_document)
                                                                                <a href="{{ asset('storage/' . $applicant->cv_document) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-sm btn-outline-primary">Lihat
                                                                                    CV</a>
                                                                            @else
                                                                                <span class="text-muted">Tidak ada
                                                                                    CV</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.applicant-checkbox');
            checkboxes.forEach(cb => cb.checked = isChecked);
        });
    </script>
    <script>
        // Set tab saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const lastTab = localStorage.getItem('activeTab') || 'tab-text'; // default: tab All Pelamar
            document.getElementById(lastTab).checked = true;
        });

        // Simpan pilihan tab saat diklik
        document.querySelectorAll('input[name="summary-tab"]').forEach(radio => {
            radio.addEventListener('change', function() {
                localStorage.setItem('activeTab', this.id);
            });
        });
    </script>

</x-app-layout>
