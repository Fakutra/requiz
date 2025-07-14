<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Batch & Position') }}
            </h2>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBatch">Create New
                Batch</a>
        </div>

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
                        <div class="accordion mb-3">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $batch->id }}" aria-expanded="true"
                                        aria-controls="{{ $batch->id }}">
                                        {{ $batch->name }}
                                        <a class="text-reset text-decoration-none ms-5"> Status : {{ $batch->status }}
                                        </a>
                                        <a class="text-reset text-decoration-none ms-5">{{ \Carbon\Carbon::parse($batch->start_date)->translatedFormat('d F Y') }}
                                            s/d
                                            {{ \Carbon\Carbon::parse($batch->end_date)->translatedFormat('d F Y') }}</a>
                                    </button>
                                </h2>
                                <div id="{{ $batch->id }}" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <a href="#" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editBatch{{ $batch->id }}">Edit Batch</a>
                                        <form action="/admin/batch/{{ $batch->id }}" method="post"
                                            class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Apakah Anda Yakin ?')">
                                                Delete Batch
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-primary ms-5" data-bs-toggle="modal"
                                            data-bs-target="#tambahPosisi{{ $batch->id }}">
                                            + Tambah Posisi
                                        </button>
                                        <table class="table table-striped">
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
                                                @foreach ($batch->position as $position)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $position->name }}</td>
                                                        <td>{{ $position->quota }}</td>
                                                        <td>{{ $position->status }}</td>
                                                        <td>
                                                            <a href="" class="btn btn-sm btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editPosisi{{ $position->id }}">Edit</a>
                                                            <form action="/admin/batch/position/{{ $position->id }}"
                                                                method="post" class="d-inline">
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
                                        @foreach ($batch->position as $position)
                                            <!-- Modal Edit Position -->
                                            <div class="modal fade" id="editPosisi{{ $position->id }}"
                                                tabindex="-1" aria-labelledby="modalEditPosisi{{ $position->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5"
                                                                id="modalEditPosisi{{ $position->id }}">Edit
                                                                {{ $position->name }}</h1>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="post"
                                                                action="{{ route('position.update', $position->id) }}"
                                                                id="formEditPosisi{{ $position->id }}" class="mb-5"
                                                                enctype="multipart/form-data">
                                                                @method('put')
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label for="name" class="form-label">Nama
                                                                        Posisi</label>
                                                                    <input type="text"
                                                                        class="form-control @error('name') is-invalid @enderror"
                                                                        id="name" name="name" required
                                                                        autofocus
                                                                        value="{{ old('name', $position->name) }}">
                                                                    @error('name')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="quota"
                                                                        class="form-label">Kuota</label>
                                                                    <input type="number"
                                                                        class="form-control @error('quota') is-invalid @enderror"
                                                                        id="quota" name="quota" required
                                                                        value="{{ old('quota', $position->quota) }}">
                                                                    @error('quota')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="status"
                                                                        class="form-label">Status</label>
                                                                    <select
                                                                        class="form-select @error('status') is-invalid @enderror"
                                                                        name="status" id="status" required>
                                                                        @foreach (['Active', 'Inactive'] as $option)
                                                                            <option value="{{ $option }}"
                                                                                {{ old('status', $position->status) === $option ? 'selected' : '' }}>
                                                                                {{ $option }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('status')
                                                                        <div class="invalid-feedback">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="description"
                                                                        class="form-label">Description</label>
                                                                    @error('description')
                                                                        <p class="text-danger">{{ $message }}</p>
                                                                    @enderror
                                                                    <input id="description" type="hidden"
                                                                        name="description"
                                                                        value="{{ old('description', $position->description) }}">
                                                                    <trix-editor input="description"></trix-editor>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit"
                                                                form="formEditPosisi{{ $position->id }}"
                                                                class="btn btn-primary">Update</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit Batch -->
                        <div class="modal fade" id="editBatch{{ $batch->id }}" tabindex="-1"
                            aria-labelledby="modalEditBatch{{ $batch->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalEditBatch{{ $batch->id }}">Edit
                                            {{ $batch->name }}</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('batch.update', $batch->id) }}"
                                            id="formEditBatch{{ $batch->id }}" class="mb-5"
                                            enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            <div class="mb-3 col-md-12">
                                                <label for="name" class="form-label">Nama Batch</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" name="name" required
                                                    value="{{ old('name', $batch->name) }}">
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="mb-3 col-md-12">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" name="status" required>
                                                    <option value="Active"
                                                        {{ $batch->status === 'Active' ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="Closed"
                                                        {{ $batch->status === 'Closed' ? 'selected' : '' }}>Closed
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-sm-4">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" class="form-control" id="start_date"
                                                    name="start_date" value="{{ $batch->start_date }}">
                                            </div>

                                            <div class="mb-3 col-sm-4">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" class="form-control" id="end_date"
                                                    name="end_date" value="{{ $batch->end_date }}">
                                            </div>

                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" form="formEditBatch{{ $batch->id }}"
                                            class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Create Position -->
                        <div class="modal fade" id="tambahPosisi{{ $batch->id }}" tabindex="-1"
                            aria-labelledby="modalTambahPosisi{{ $batch->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalTambahPosisi{{ $batch->id }}">Tambah
                                            Posisi Untuk {{ $batch->name }}</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('position.store', $batch->id) }}"
                                            id="formTambahPosisi{{ $batch->id }}" enctype="multipart/form-data">
                                            @csrf

                                            <div class="mb-3">
                                                <label class="form-label">Nama Posisi</label>
                                                <input type="text" name="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="namePosisi{{ $batch->id }}" value="{{ old('name') }}"
                                                    required autofocus>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Kuota</label>
                                                <input type="number" name="quota"
                                                    class="form-control @error('quota') is-invalid @enderror"
                                                    value="{{ old('quota') }}" required>
                                                @error('quota')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status"
                                                    class="form-select @error('status') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="Active"
                                                        {{ old('status') == 'Active' ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="Inactive"
                                                        {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive
                                                    </option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                @error('description')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                                <input type="hidden" id="description{{ $batch->id }}"
                                                    name="description" value="{{ old('description') }}">
                                                <trix-editor input="description{{ $batch->id }}"></trix-editor>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" form="formTambahPosisi{{ $batch->id }}"
                                            class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="modal fade" id="tambahPosisi{{ $batch->id }}" tabindex="-1"
                            aria-labelledby="modalTambahPosisi{{ $batch->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalTambahPosisi{{ $batch->id }}">Tambah
                                            Posisi Untuk {{ $batch->name }}</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('position.store', $batch->id) }}"
                                            id="formTambahPosisi{{ $batch->id }}" class="mb-5"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3 col-md-12">
                                                <label for="name" class="form-label">Nama Posisi</label>
                                                <input type="text" id="namePosisi{{ $batch->id }}"
                                                    data-name-posisi data-slug-target="slugPosisi{{ $batch->id }}"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name" required autofocus value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="mb-3 col-md-12">
                                                <label for="quota" class="form-label">Kuota</label>
                                                <input type="number"
                                                    class="form-control @error('quota') is-invalid @enderror"
                                                    id="quota" name="quota" required
                                                    value="{{ old('quota') }}">
                                                @error('quota')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select @error('status') is-invalid @enderror"
                                                    name="status" id="status" required>
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="Active"
                                                        {{ old('status') == 'Active' ? 'selected' : '' }}>Active
                                                    </option>
                                                    <option value="Inactive"
                                                        {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive
                                                    </option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <label for="description" class="form-label">Description</label>
                                                @error('description')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                                <input id="description{{ $batch->id }}" type="hidden"
                                                    name="description">
                                                <trix-editor input="description{{ $batch->id }}"></trix-editor>

                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" form="formTambahPosisi{{ $batch->id }}"
                                            class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Modal Edit Position -->
                        <div class="modal fade" id="editPosisi{{ $position->id }}" tabindex="-1"
                            aria-labelledby="modalEditPosisi{{ $position->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalEditPosisi{{ $position->id }}">Edit
                                            {{ $position->name }}</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('position.update', $position->id) }}"
                                            id="formEditPosisi{{ $position->id }}" class="mb-5"
                                            enctype="multipart/form-data">
                                            @method('put')
                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama Posisi</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name" name="name" required autofocus
                                                    value="{{ old('name', $position->name) }}">
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="quota" class="form-label">Kuota</label>
                                                <input type="number"
                                                    class="form-control @error('quota') is-invalid @enderror"
                                                    id="quota" name="quota" required
                                                    value="{{ old('quota', $position->quota) }}">
                                                @error('quota')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select @error('status') is-invalid @enderror"
                                                    name="status" id="status" required>
                                                    @foreach (['Active', 'Inactive'] as $option)
                                                        <option value="{{ $option }}"
                                                            {{ old('status', $position->status) === $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                @error('description')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                                <input id="description" type="hidden" name="description"
                                                    value="{{ old('description', $position->description) }}">
                                                <trix-editor input="description"></trix-editor>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" form="formEditPosisi{{ $position->id }}"
                                            class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
        const nameBatch = document.querySelector('#nameBatch ');
        const slugBatch = document.querySelector('#slugBatch ');

        nameBatch.addEventListener('change', function() {
            fetch('/admin/batch/checkSlug?name=' + nameBatch.value)
                .then(response => response.json())
                .then(data => slugBatch.value = data.slug)
        });
    </script>
    <script>
        document.querySelectorAll('[data-name-posisi]').forEach(field => {
            field.addEventListener('change', function() {
                const targetId = this.dataset.slugTarget;
                const slugInput = document.getElementById(targetId);
                fetch('/admin/position/checkSlug?name=' + this.value)
                    .then(response => response.json())
                    .then(data => slugInput.value = data.slug);
            });
        });
    </script>
</x-app-layout>
