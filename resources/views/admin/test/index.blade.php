<x-app-admin>
    <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
        <div class="max-w-7xl mx-auto">             
            <div class="p-6 text-gray-900">
                <div class="d-flex justify-content-between align-items-center mb-3 ">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                        {{ __('Quiz') }}
                    </h2>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahTest">Create New Quiz</a>
                </div>
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
                                    {{ \Carbon\Carbon::parse($test->test_date ?? $test->created_at)->translatedFormat('d F Y H:i') }}
                                    <span class="mx-2">|</span>
                                    <i class="bi bi-briefcase"></i>
                                    {{ $test->sections_count }} Section
                                    @if ($test->nilai_minimum)
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-graph-down"></i>
                                        Nilai Min: <strong>{{ number_format($test->nilai_minimum, 2) }}</strong>
                                    @endif
                                </small>
                            </div>

                            <div class="btn-group" role="group">
                                <a href="{{ route('test.show', $test) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-gear"></i> Kelola Section
                                </a>

                                <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                    data-bs-target="#editTest{{ $test->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>

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

                        {{-- Include modal edit --}}
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

    {{-- Modal Create Test --}}
    <div class="modal fade" id="tambahTest" tabindex="-1" aria-labelledby="modalTambahTest" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahTest">Create Quiz</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="post" id="formTambahTest" action="{{ route('test.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {{-- Nama Test --}}
                        <div class="mb-3 col-md-12">
                            <label for="name" class="form-label">Title</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="nameTest" name="name" required autofocus value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Posisi --}}
                        <div class="mb-3 col-md-12">
                            <label for="position_id" class="form-label">Posisi</label>
                            <select name="position_id" id="position_id" class="form-select mb-2" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}">
                                        {{ $position->name }} | {{ $position->batch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nilai Minimum --}}
                        <div class="mb-3 col-md-12">
                            <label for="nilai_minimum" class="form-label">Nilai Minimum (Opsional)</label>
                            <input type="number" step="0.01" min="0" max="9999.99"
                                class="form-control @error('nilai_minimum') is-invalid @enderror"
                                id="nilai_minimum" name="nilai_minimum"
                                value="{{ old('nilai_minimum') }}" placeholder="Contoh: 123.45">
                            @error('nilai_minimum')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jadwal Buka Test --}}
                        <div class="mb-3 col-md-12">
                            <label for="test_date" class="form-label">Test Date (Buka Tombol)</label>
                            <input type="datetime-local" class="form-control @error('test_date') is-invalid @enderror"
                                id="test_date" name="test_date" value="{{ old('test_date') }}">
                            @error('test_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tutup Test --}}
                        <div class="mb-3 col-md-12">
                            <label for="test_closed" class="form-label">Tutup Tombol (Opsional)</label>
                            <input type="datetime-local"
                                class="form-control @error('test_closed') is-invalid @enderror" id="test_closed"
                                name="test_closed" value="{{ old('test_closed') }}">
                            @error('test_closed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hard End --}}
                        <div class="mb-3 col-md-12">
                            <label for="test_end" class="form-label">Hard End (Opsional)</label>
                            <input type="datetime-local" class="form-control @error('test_end') is-invalid @enderror"
                                id="test_end" name="test_end" value="{{ old('test_end') }}">
                            @error('test_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="rounded w-full h-64 overflow-y-auto">
                            <label class="block text-sm font-medium">Isi Intro</label>
                            <div class="mb-3 flex items-center gap-2">
                                <input type="checkbox" class="rounded">
                                <label class="text-sm font-medium">Gunakan template</label>
                            </div>
                            <input id="messageLolos" type="hidden" name="message">
                            <trix-editor input="messageLolos" class="trix-content border rounded w-full h-full"></trix-editor>
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

    {{-- Modal success --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        </script>
    @endif
</x-app-admin>
