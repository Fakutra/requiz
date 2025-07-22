{{-- MODAL UNTUK EDIT BATCH (Tempatkan di dalam loop batch) --}}
<div class="modal fade" id="editBatch{{ $batch->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Batch: {{ $batch->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
