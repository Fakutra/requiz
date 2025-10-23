{{-- MODAL UNTUK TAMBAH POSISI (Tempatkan di dalam loop batch) --}}
<div class="modal fade" id="tambahPosisi{{ $batch->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Posisi untuk Batch: {{ $batch->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('position.store', $batch) }}" method="POST"
                  onsubmit="return validateDescription('{{ $batch->id }}')">

                @csrf
                <div class="modal-body">
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">

                    {{-- NAMA POSISI --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Posisi</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    {{-- KUOTA --}}
                    <div class="mb-3">
                        <label class="form-label">Kuota</label>
                        <input type="number" class="form-control" name="quota" min="1" required>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- DESKRIPSI (TRIX EDITOR) --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi *</label>
                        <input type="hidden" id="description{{ $batch->id }}" name="description"
                            value="{{ old('description') }}">
                        <trix-editor input="description{{ $batch->id }}"></trix-editor>

                        {{-- ERROR MESSAGE DARI BACKEND (opsional) --}}
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
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

{{-- VALIDASI FRONTEND UNTUK TRIX (WAJIB) --}}
<script>
function validateDescription(batchId) {
    let desc = document.querySelector(`#description${batchId}`).value.trim();

    if (desc === '') {
        alert('Deskripsi wajib diisi!');
        return false; // blok submit
    }

    return true;
}
</script>
