<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Quiz') }}
            </h2>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahTest">Create New
                Quiz</a>
        </div>

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
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="list-group">
                        @forelse ($tests as $test)
                            <div
                                class="list-group-item list-group-item-action d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                                <div class="me-md-3 mb-2 mb-md-0">
                                    <h5 class="mb-0 fw-bold d-inline-flex align-items-center">
                                        {{ $test->name }}
                                    </h5>
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-calendar-range"></i>
                                        {{-- Asumsi kolom tanggal adalah 'created_at' jika 'test_date' tidak ada --}}
                                        {{ \Carbon\Carbon::parse($test->test_date ?? $test->created_at)->translatedFormat('d F Y H:i') }}
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-briefcase"></i>
                                        {{ $test->section_count }} Section
                                    </small>
                                </div>
                                <div class="btn-group" role="group">
                                    {{-- FIXED: Tambahkan route untuk kelola section, sesuaikan nama route --}}
                                    <a href="{{ route('test.show', $test) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-gear"></i> Kelola Section
                                    </a>

                                    {{-- FIXED: Target modal edit harus dinamis --}}
                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                        data-bs-target="#editTest{{ $test->id }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    {{-- FIXED: Tambahkan action untuk form hapus, sesuaikan nama route --}}
                                    <form action="{{ route('test.destroy', $test) }}" method="post" class="d-inline"
                                        onsubmit="return confirm('Anda yakin ingin menghapus test ini?')">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- ADDED: Include modal edit di dalam loop agar setiap item punya modalnya sendiri --}}
                            {{-- Anda perlu membuat file partials ini: 'admin.test.partials.modal-edit-test' --}}
                            @include('admin.test.partials.modal-edit-test', [
                                'test' => $test,
                                'positions' => $positions,
                            ])

                        @empty
                            <div class="list-group-item">
                                <p class="text-center text-muted my-3">Belum ada Test yang dibuat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahTest" tabindex="-1" aria-labelledby="modalTambahTest" aria-hidden="true">   
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahTest">Create Quiz</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                {{-- Form dipindahkan untuk membungkus seluruh modal body dan footer --}}
                <form method="post" id="formTambahTest" action="{{ route('test.store') }}"
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

                        <div class="mb-3 col-md-12">
                            <label for="position_id" class="form-label">Posisi</label>
                            <select name="position_id" id="position_id" class="form-select mb-2" required>
                                <option value="">-- Pilih --</option>
                                {{-- Variabel $positions sekarang sudah tersedia --}}
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }} |
                                        {{ $position->batch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="test_date" class="form-label">Test Date</label>
                            <input type="datetime-local" class="form-control @error('test_date') is-invalid @enderror"
                                id="test_date" name="test_date" required autofocus value="{{ old('test_date') }}">
                            @error('test_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
