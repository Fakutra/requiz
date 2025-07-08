<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Quiz') }}
            </h2>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahTest">Create New
                Quiz</a>
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

        <!-- Modal Create Quiz -->
        <div class="modal fade" id="tambahTest" tabindex="-1" aria-labelledby="modalTambahTest" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalTambahTest">Create Quiz</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="formTambahTest" action="{{ route('test.store') }}" class="mb-5"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3 col-md-12">
                                    <label for="name" class="form-label">Title</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="nameTest" name="name" required autofocus value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- <div class="mb-3 col-md-12">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control" id="slugTest" name="slug" readonly>
                                </div> --}}

                                <div class="mb-3 col-md-12">
                                    <label for="name" class="form-label">Posisi</label>
                                    <select name="position_id" class="form-select mb-2" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="formTambahTest" class="btn btn-primary">Simpan</button>
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
                                <th>Title</th>
                                <th>Posisi</th>
                                <th>Section</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tests as $test)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $test->name }}</td>
                                    <td>{{ $test->position->name }}</td>
                                    <td>
                                        <a href="/admin/{{ $test->slug }}" class="btn btn-sm btn-outline-primary">Preview</a>
                                    </td>
                                    {{-- <td>{{ $test->status }}</td> --}}
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#edittest{{ $test->id }}">Edit</a>
                                        <form action="/admin/test/{{ $test->id }}" method="post" class="d-inline">
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
    </div>

    <script>
        const nameTest = document.querySelector('#nameTest');
        const slugTest = document.querySelector('#slugTest');

        nameTest.addEventListener('change', function() {
            fetch('/admin/test/checkSlug?name=' + nameTest.value)
                .then(response => response.json())
                .then(data => slugTest.value = data.slug)
        });
    </script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        </script>
    @endif

</x-app-layout>
