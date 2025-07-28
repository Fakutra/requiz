{{-- resources/views/admin/batch/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Batch Management') }}
            </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBatch">
                <i class="bi bi-plus-circle me-2"></i>Create New Batch
            </button>
        </div>

        {{-- Modal Notifikasi Sukses --}}
        @if (session('success'))
            <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
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
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="list-group">
                @forelse ($batchs as $batch)
                    <div
                        class="list-group-item list-group-item-action d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                        <div class="me-md-3 mb-2 mb-md-0">
                            <h5 class="mb-0 fw-bold d-inline-flex align-items-center">
                                {{ $batch->name }}
                                <span
                                    class="badge {{ $batch->status == 'Active' ? 'bg-success' : 'bg-danger' }} ms-3">{{ $batch->status }}</span>
                            </h5>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-calendar-range"></i>
                                {{ \Carbon\Carbon::parse($batch->start_date)->translatedFormat('d F Y') }} -
                                {{ \Carbon\Carbon::parse($batch->end_date)->translatedFormat('d F Y') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-briefcase"></i>
                                {{ $batch->position_count }} Posisi
                            </small>
                        </div>
                        <div class="btn-group" role="group">
                            {{-- TOMBOL BARU UNTUK KE HALAMAN SHOW --}}
                            <a href="{{ route('batch.show', $batch) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-gear"></i> Kelola Posisi
                            </a>
                            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                data-bs-target="#editBatch{{ $batch->id }}">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <form action="{{ route('batch.destroy', $batch->id) }}" method="post" class="d-inline"
                                onsubmit="return confirm('Anda yakin ingin menghapus batch ini?')">
                                @method('delete')
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- Modal Edit Batch masih diperlukan di sini --}}
                    @include('admin.batch.partials.modal-edit-batch', ['batch' => $batch])
                @empty
                    <div class="list-group-item">
                        <p class="text-center text-muted my-3">Belum ada batch yang dibuat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ... (Modal Tambah Batch & script JS Anda) ... --}}
    {{-- Modal Create Batch (Tidak ada perubahan, tetap di sini) --}}
    <div class="modal fade" id="tambahBatch" tabindex="-1" aria-labelledby="modalTambahBatch" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahBatch">Tambah Batch Baru</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formTambahBatch" action="{{ route('batch.store') }}">
                    <div class="modal-body">
                        @csrf
                        {{-- Nama Batch --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Batch</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="nameBatch" name="name" required autofocus value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="" selected disabled>--- Pilih Status ---</option>
                                <option value="Active" @selected(old('status') == 'Active')>Active</option>
                                <option value="Closed" @selected(old('status') == 'Closed')>Closed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Tanggal --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date" required value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                    id="end_date" name="end_date" required value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- @push('scripts') --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk menampilkan modal sukses jika ada session 'success'
            @if (session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif

            // Script untuk membuka kembali modal yang relevan jika ada error validasi
            @if ($errors->any())
                // Cek jika error berasal dari form 'tambahBatch'
                @if ($errors->has('name') || $errors->has('status') || $errors->has('start_date') || $errors->has('end_date'))
                    @if (!old('batch_id_edit')) // Hanya buka jika bukan dari edit batch
                        var tambahBatchModal = new bootstrap.Modal(document.getElementById('tambahBatch'));
                        tambahBatchModal.show();
                    @endif
                @endif

                // Cek jika error berasal dari form 'editBatch'
                var editBatchId = '{{ old('batch_id_edit') }}';
                if (editBatchId) {
                    var editBatchModal = new bootstrap.Modal(document.getElementById('editBatch' + editBatchId));
                    editBatchModal.show();
                }

                // Logika serupa bisa ditambahkan untuk modal posisi jika diperlukan
            @endif
        });
    </script>
    {{-- @endpush --}}
</x-app-layout>
