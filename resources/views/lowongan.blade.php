<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lowongan') }}
        </h2>
    </x-slot> --}}

    <!-- Modal Sukses -->
    @if (session()->has('success'))
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
    @endif

    <div class="alert alert-warning d-flex justify-content-center text-center align-items-center mt-3" role="alert">
        <div>
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Perhatian!</strong> Anda hanya dapat <u>melamar satu kali.</u>
            Pastikan Anda sudah <strong>mempertimbangkan dengan matang</strong> sebelum memilih posisi yang ingin
            dilamar.
        </div>
    </div>


    <div class="py-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        @foreach ($positions as $position)
                            @php
                                $sudahDaftar = in_array($position->batch_id, $appliedBatchIds);
                            @endphp
                            <div class="col-sm-6 col-md-4 mb-3 d-flex">
                                <div class="card w-100 d-flex flex-column">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div>
                                            <h5 class="card-title">{{ $position->name }}</h5>

                                            {{-- Batasi tinggi deskripsi agar tidak terlalu panjang --}}
                                            <p class="card-text" style="max-height: 150px; overflow-y: auto;">
                                                {!! $position->description !!}
                                            </p>
                                        </div>

                                        {{-- Tombol & kuota di bawah --}}
                                        <div class="mt-auto pt-2 d-flex justify-content-between align-items-center">
                                            <p class="m-0 text-muted">
                                                Kuota: {{ $position->applicants_count ?? 0 }} / {{ $position->quota }}
                                            </p>

                                            @if (!$sudahDaftar)
                                                @if ($position->applicants_count >= $position->quota)
                                                    <button class="btn btn-secondary btn-sm" disabled>Penuh</button>
                                                @else
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#applyPosisi{{ $position->id }}"
                                                        class="btn btn-primary btn-sm">Daftar</a>
                                                @endif
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>Sudah Melamar</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Apply Position -->
                            <div class="modal fade" id="applyPosisi{{ $position->id }}" tabindex="-1"
                                aria-labelledby="modalApplyLabel{{ $position->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('apply.store', $position->slug) }}" method="POST"
                                        id="formApplyPosisi{{ $position->id }}" enctype="multipart/form-data"
                                        class="modal-content">
                                        @csrf
                                        @php
                                            $skills = [
                                                'MySQL',
                                                'PostgreSQL',
                                                'SQL Server',
                                                'MongoDB',
                                                'PHP',
                                                'C',
                                                'C#',
                                                'C++',
                                                'Java',
                                                'Python',
                                                'Lainnya',
                                            ];
                                        @endphp
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalApplyLabel{{ $position->id }}">
                                                Daftar: {{ $position->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        {{-- Modal akan ditampilkan jika ada error validasi --}}
                                        @if ($errors->any() && old('position_id') == $position->id)
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    let modal = new bootstrap.Modal(document.getElementById('applyPosisi{{ $position->id }}'));
                                                    modal.show();
                                                });
                                            </script>
                                        @endif
                                        @if ($errors->any() && old('position_id') == $position->id)
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $err)
                                                        <li>{{ $err }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="modal-body">
                                            <input type="hidden" name="position_id" value="{{ $position->id }}">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Nama Lengkap</label>
                                                    <input type="text" name="name"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        value="{{ old('name', auth()->user()->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        value="{{ old('name', auth()->user()->email) }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">NIK</label>
                                                    <input type="text" name="nik"
                                                        class="form-control @error('nik') is-invalid @enderror"
                                                        value="{{ old('nik') }}" inputmode="numeric" maxlength="16"
                                                        minlength="16" pattern="\d{16}" required>
                                                    @error('nik')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Nomor Telepon</label>
                                                    <input type="tel" name="no_telp"
                                                        class="form-control @error('no_telp') is-invalid @enderror"
                                                        value="{{ old('no_telp') }}" required>
                                                    @error('no_telp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Tempat Lahir</label>
                                                    <input type="text" name="tpt_lahir"
                                                        class="form-control @error('tpt_lahir') is-invalid @enderror"
                                                        value="{{ old('tpt_lahir') }}" required>
                                                    @error('tpt_lahir')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Tanggal Lahir</label>
                                                    <input type="date" name="tgl_lahir"
                                                        class="form-control @error('tgl_lahir') is-invalid @enderror"
                                                        value="{{ old('tgl_lahir') }}" required>
                                                    @error('tgl_lahir')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">Alamat</label>
                                                    <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
                                                    @error('alamat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Pendidikan</label>
                                                    <select name="pendidikan"
                                                        class="form-select @error('pendidikan') is-invalid @enderror"
                                                        required>
                                                        <option value="">--- Pilih ---</option>
                                                        @foreach (['SMA/Sederajat', 'Diploma', 'S1', 'S2', 'S3'] as $jenjang)
                                                            <option value="{{ $jenjang }}"
                                                                {{ old('pendidikan') == $jenjang ? 'selected' : '' }}>
                                                                {{ $jenjang }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('pendidikan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Universitas</label>
                                                    <input type="text" name="universitas"
                                                        class="form-control @error('universitas') is-invalid @enderror"
                                                        value="{{ old('universitas') }}" required>
                                                    @error('universitas')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Jurusan</label>
                                                    <input type="text" name="jurusan"
                                                        class="form-control @error('jurusan') is-invalid @enderror"
                                                        value="{{ old('jurusan') }}" required>
                                                    @error('jurusan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Tahun Lulus</label>
                                                    <input type="text" name="thn_lulus"
                                                        class="form-control @error('thn_lulus') is-invalid @enderror"
                                                        value="{{ old('thn_lulus') }}" maxlength="4"
                                                        pattern="\d{4}" required>
                                                    @error('thn_lulus')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Tahun Lulus</label>
                                                    <input type="text" name="thn_lulus"
                                                        class="form-control @error('thn_lulus') is-invalid @enderror"
                                                        value="{{ old('thn_lulus') }}" maxlength="4"
                                                        pattern="\d{4}" required>
                                                    @error('thn_lulus')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Ekspektasi Gaji (Rp)</label>
                                                    <input type="number" name="ekspektasi_gaji"
                                                        class="form-control @error('ekspektasi_gaji') is-invalid @enderror"
                                                        value="{{ old('ekspektasi_gaji') }}"
                                                        min="0" placeholder="Contoh: 5500000" required>
                                                    @error('ekspektasi_gaji')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">Skills</label>
                                                    <div class="d-flex flex-wrap gap-3">
                                                        @foreach ($skills as $skill)
                                                            @if ($skill === 'Lainnya')
                                                                <div
                                                                    class="form-check d-flex align-items-center gap-2">
                                                                    <input type="checkbox" name="skills[]"
                                                                        value="Lainnya"
                                                                        id="skill_other_{{ $position->id }}"
                                                                        class="form-check-input"
                                                                        onchange="toggleOtherSkill(this, '{{ $position->id }}')">
                                                                    <label class="form-check-label mb-0"
                                                                        for="skill_other_{{ $position->id }}">Lainnya</label>
                                                                </div>
                                                                <div id="other_skill_wrapper_{{ $position->id }}"
                                                                    class="mt-2"
                                                                    style="display: none; width: 100%;">
                                                                    <input type="text" name="other_skill"
                                                                        id="other_skill_input_{{ $position->id }}"
                                                                        class="form-control"
                                                                        placeholder="Isi skill lainnya...">
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="form-check d-flex align-items-center gap-2">
                                                                    <input type="checkbox" name="skills[]"
                                                                        value="{{ $skill }}"
                                                                        id="skill_{{ $position->id }}_{{ $loop->index }}"
                                                                        class="form-check-input">
                                                                    <label class="form-check-label mb-0"
                                                                        for="skill_{{ $position->id }}_{{ $loop->index }}">{{ $skill }}</label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">CV dan Dokumen Tambahan (PDF, Max
                                                        3MB)</label>
                                                    <input type="file" name="cv_document"
                                                        class="form-control @error('cv_document') is-invalid @enderror"
                                                        accept=".pdf">
                                                    @error('cv_document')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Kirim Lamaran</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
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
    <script>
        function toggleOtherSkill(checkbox, positionId) {
            const inputWrapper = document.getElementById(`other_skill_wrapper_${positionId}`);
            if (inputWrapper) {
                inputWrapper.style.display = checkbox.checked ? 'block' : 'none';
            }
        }
    </script>

</x-app-layout>
