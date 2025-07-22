{{-- MODAL UNTUK EDIT POSISI --}}
<div class="modal fade" id="editPosisi{{ $position->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Posisi:
                    {{ $position->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('position.update', $position) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Nama Posisi --}}
                    <div class="mb-3">
                        <label class="form-label">Nama
                            Posisi</label>
                        <input type="text" class="form-control" name="name"
                            value="{{ old('name', $position->name) }}" required>
                    </div>

                    {{-- Kuota --}}
                    <div class="mb-3">
                        <label class="form-label">Kuota</label>
                        <input type="number" class="form-control" name="quota"
                            value="{{ old('quota', $position->quota) }}" required>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="Active" @selected(old('status', $position->status) == 'Active')>Active
                            </option>
                            <option value="Inactive" @selected(old('status', $position->status) == 'Inactive')>
                                Inactive</option>
                        </select>
                    </div>

                    {{-- Deskripsi dengan Trix Editor --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="hidden" id="description-edit-{{ $position->id }}" name="description"
                            value="{{ old('description', $position->description) }}">
                        <trix-editor input="description-edit-{{ $position->id }}"></trix-editor>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
