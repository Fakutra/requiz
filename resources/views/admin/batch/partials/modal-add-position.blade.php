{{-- MODAL UNTUK TAMBAH POSISI (Tempatkan di dalam loop batch) --}}
<div class="modal fade" id="tambahPosisi{{ $batch->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Posisi untuk Batch: {{ $batch->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <input type="hidden" id="description{{ $batch->id }}" name="description"
                            value="{{ old('description') }}" required>
                        <trix-editor input="description{{ $batch->id }}" required></trix-editor>
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
