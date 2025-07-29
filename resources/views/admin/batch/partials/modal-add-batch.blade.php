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
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameBatch"
                            name="name" required autofocus value="{{ old('name') }}">
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
