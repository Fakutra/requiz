<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Position') }}
            </h2>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPosition">Create New
                Position</a>
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

        <!-- Modal Create Position -->
        <div class="modal fade" id="tambahPosition" tabindex="-1" aria-labelledby="modalTambahPosition"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalTambahPosition">Tambah Posisi Baru</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="formTambahPosition" action="{{ route('position.store') }}"
                            class="mb-5" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 col-md-12">
                                <label for="name" class="form-label">Nama Position</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="namePosition" name="name" required autofocus value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            {{-- <div class="mb-3 col-md-12">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control" id="slugPosition" name="slug" readonly>
                            </div> --}}
                            <div class="mb-3 col-lg-8">
                                <label for="quota" class="form-label">Kuota</label>
                                <input type="number" class="form-control @error('quota') is-invalid @enderror"
                                    id="quota" name="quota" required value="{{ old('quota') }}">
                                @error('quota')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status"
                                    id="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>
                                        Inactive</option>
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
                                <input id="description" type="hidden" name="description">
                                <trix-editor input="description"></trix-editor>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="formTambahPosition" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Kuota</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($positions as $position)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $position->name }}</td>
                                    <td>{{ $position->quota }}</td>
                                    <td>{{ $position->status }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#editPosition{{ $position->id }}">Edit</a>
                                        <form action="/admin/position/{{ $position->id }}" method="post"
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
                </div>
            </div>
        </div>

        @foreach ($positions as $position)
            <!-- Modal Edit Position -->
            <div class="modal fade" id="editPosition{{ $position->id }}" tabindex="-1"
                aria-labelledby="modalEditPosition{{ $position->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalEditPosition{{ $position->id }}">Edit
                                {{ $position->name }}</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('position.update', $position->id) }}"
                                id="formEditPosition{{ $position->id }}" class="mb-5"
                                enctype="multipart/form-data">
                                @method('put')
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Posisi</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="namePosition" name="name" required autofocus
                                        value="{{ old('name', $position->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="quota" class="form-label">Kuota</label>
                                    <input type="number" class="form-control @error('quota') is-invalid @enderror"
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
                                    <select class="form-select @error('status') is-invalid @enderror" name="status"
                                        id="status" required>
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
                                    <textarea id="description{{ $position->id }}" name="description" hidden>{!! old('description', $position->description) !!}</textarea>
                                    <trix-editor input="description{{ $position->id }}"></trix-editor>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="formEditPosition{{ $position->id }}"
                                class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
        const namePosition = document.querySelector('#namePosition');
        const slugPosition = document.querySelector('#slugPosition');

        namePosition.addEventListener('change', function() {
            fetch('/admin/position/checkSlug?name=' + namePosition.value)
                .then(response => response.json())
                .then(data => slugPosition.value = data.slug)
        });
    </script>
</x-app-layout>
