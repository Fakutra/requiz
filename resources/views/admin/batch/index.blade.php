<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Batch & Position') }}
            </h2>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBatch">Create New
                Batch</a>
        </div>

        @if (session('success'))
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center p-4">
                            <h5 class="text-success mb-3">✅ {{ session('success') }}</h5>
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Create Batch -->
        <div class="modal fade" id="tambahBatch" tabindex="-1" aria-labelledby="modalTambahBatch" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalTambahBatch">Tambah Batch Baru</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="formTambahBatch" action="{{ route('batch.store') }}" class="mb-5"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 col-md-12">
                                <label for="name" class="form-label">Nama Batch</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="nameBatch" name="name" required autofocus value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-12">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option selected>--- Pilih ---</option>
                                    <option value="Active">Active</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date" required value="">
                                @error('start_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                    id="end_date" name="end_date" required value="">
                                @error('end_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="formTambahBatch" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @foreach ($batchs as $batch)
                        <div class="accordion mb-3" id="accordion-{{ $batch->id }}">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $batch->id }}" aria-expanded="false"
                                        aria-controls="collapse-{{ $batch->id }}">
                                        <span class="fw-bold me-4">{{ $batch->name }}</span>
                                        <span
                                            class="badge {{ $batch->status == 'Active' ? 'bg-success' : 'bg-secondary' }} me-4">Status:
                                            {{ $batch->status }}</span>
                                        <span>
                                            {{ \Carbon\Carbon::parse($batch->start_date)->translatedFormat('d F Y') }}
                                            s/d
                                            {{ \Carbon\Carbon::parse($batch->end_date)->translatedFormat('d F Y') }}
                                        </span>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $batch->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordion-{{ $batch->id }}">
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <a href="#" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editBatch{{ $batch->id }}">Edit Batch</a>
                                            <form action="{{ route('batch.destroy', $batch->id) }}" method="post"
                                                class="d-inline">
                                                @method('delete')
                                                @csrf
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Apakah Anda Yakin ?')">Delete
                                                    Batch</button>
                                            </form>
                                            <button class="btn btn-sm btn-primary ms-3" data-bs-toggle="modal"
                                                data-bs-target="#tambahPosisi{{ $batch->id }}">+ Tambah
                                                Posisi</button>
                                        </div>

                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Nama Posisi</th>
                                                    <th>Kuota</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($batch->position as $position)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $position->name }}</td>
                                                        <td>{{ $position->quota }}</td>
                                                        <td>{{ $position->status }}</td>
                                                        <td>
                                                            <a href="#" class="btn btn-sm btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editPosisi{{ $position->id }}">Edit</a>
                                                            <form
                                                                action="{{ route('position.destroy', $position->id) }}"
                                                                method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>

                                                    {{-- Letakkan ini di dalam loop @forelse ($batch->position as $position) --}}

                                                    {{-- MODAL UNTUK EDIT POSISI --}}
                                                    <div class="modal fade" id="editPosisi{{ $position->id }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Posisi:
                                                                        {{ $position->name }}</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <form
                                                                    action="{{ route('position.update', $position) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-body">
                                                                        {{-- Nama Posisi --}}
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Nama
                                                                                Posisi</label>
                                                                            <input type="text" class="form-control"
                                                                                name="name"
                                                                                value="{{ old('name', $position->name) }}"
                                                                                required>
                                                                        </div>

                                                                        {{-- Kuota --}}
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Kuota</label>
                                                                            <input type="number" class="form-control"
                                                                                name="quota"
                                                                                value="{{ old('quota', $position->quota) }}"
                                                                                required>
                                                                        </div>

                                                                        {{-- Status --}}
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select class="form-select" name="status"
                                                                                required>
                                                                                <option value="Active"
                                                                                    @selected(old('status', $position->status) == 'Active')>Active
                                                                                </option>
                                                                                <option value="Inactive"
                                                                                    @selected(old('status', $position->status) == 'Inactive')>
                                                                                    Inactive</option>
                                                                            </select>
                                                                        </div>

                                                                        {{-- Deskripsi dengan Trix Editor --}}
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Deskripsi</label>
                                                                            <input type="hidden"
                                                                                id="description-edit-{{ $position->id }}"
                                                                                name="description"
                                                                                value="{{ old('description', $position->description) }}">
                                                                            <trix-editor
                                                                                input="description-edit-{{ $position->id }}"></trix-editor>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button"
                                                                            class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Simpan
                                                                            Perubahan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">Belum ada posisi untuk
                                                            batch ini.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL UNTUK EDIT BATCH (Tempatkan di dalam loop batch) --}}
                        <div class="modal fade" id="editBatch{{ $batch->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Batch: {{ $batch->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('batch.update', $batch->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            {{-- Isi form dengan data batch yang ada --}}
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama Batch</label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name', $batch->name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="Active" @selected(old('status', $batch->status) == 'Active')>Active</option>
                                                    <option value="Closed" @selected(old('status', $batch->status) == 'Closed')>Closed</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" name="start_date"
                                                    value="{{ old('start_date', $batch->start_date) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control" name="end_date"
                                                    value="{{ old('end_date', $batch->end_date) }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL UNTUK TAMBAH POSISI (Tempatkan di dalam loop batch) --}}
                        <div class="modal fade" id="tambahPosisi{{ $batch->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Posisi untuk Batch: {{ $batch->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('position.store', $batch) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="batch_id" value="{{ $batch->id }}">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Posisi</label>
                                                <input type="text" class="form-control" name="name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kuota</label>
                                                <input type="number" class="form-control" name="quota" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status" required>
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <input type="hidden" id="description{{ $batch->id }}"
                                                    name="description" value="{{ old('description') }}">
                                                <trix-editor input="description{{ $batch->id }}"></trix-editor>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- @push('scripts') --}}
    <script>
        // Script untuk menampilkan modal sukses
        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        @endif

        // Script untuk menangani modal error validasi (opsional, tapi sangat direkomendasikan)
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                // Logika untuk menemukan modal mana yang seharusnya terbuka saat ada error
                // Ini bisa menjadi kompleks. Menggunakan AJAX adalah pendekatan yang lebih bersih.
                // Contoh sederhana: jika ada error 'name', buka modal #tambahBatch.
                @if ($errors->has('name') || $errors->has('status') || $errors->has('start_date'))
                    var myModal = new bootstrap.Modal(document.getElementById('tambahBatch'))
                    myModal.show()
                @endif
            });
        @endif

        // Script Slug Dinamis yang lebih baik (tidak lagi diperlukan jika tidak ada input slug)
        // Jika Anda masih memerlukan slug, pastikan ada inputnya di form Anda.
        // const nameBatch = document.querySelector('#nameBatch');
        // const slugBatch = document.querySelector('#slugBatch');
        // if(nameBatch) {
        //     nameBatch.addEventListener('change', function() {
        //         fetch('/admin/batch/checkSlug?name=' + nameBatch.value)
        //             .then(response => response.json())
        //             .then(data => slugBatch.value = data.slug);
        //     });
        // }
    </script>
    {{-- @endpush --}}
</x-app-layout>
