{{-- resources/views/admin/batch/partials/modal-edit-position.blade.php --}}
<div class="modal fade" id="editPosisiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Posisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- action di-set dinamis via JS --}}
            <form method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Posisi *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    {{-- Kuota --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kuota *</label>
                        <input type="number" name="quota" class="form-control" min="1" required>
                    </div>

                    {{-- Pendidikan Minimum --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pendidikan Minimum *</label>
                        <select name="pendidikan_minimum" class="form-select" required>
                            <option value="">--- Pilih ---</option>
                            <option value="SMA/Sederajat">SMA / Sederajat</option>
                            <option value="D1">D1</option>
                            <option value="D2">D2</option>
                            <option value="D3">D3</option>
                            <option value="D4">D4</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>

                    {{-- Deadline --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Batas Lamaran</label>
                        <input type="date" name="deadline" class="form-control">
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- Descriptions (multiline) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="descriptions" rows="4" class="form-control" placeholder="baris per poin"></textarea>
                        <small class="text-muted d-block mt-1">Gunakan Enter untuk pisah tiap poin.</small>
                    </div>

                    {{-- Skills --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Skills</label>
                        <textarea name="skills" rows="3" class="form-control" placeholder="PHP&#10;MySQL&#10;Communication"></textarea>
                    </div>

                    {{-- Requirements --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persyaratan Umum</label>
                        <textarea name="requirements" rows="3" class="form-control" placeholder="Min. IPK 3.0"></textarea>
                    </div>

                    {{-- Majors --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jurusan Yang Dapat Melamar</label>
                        <textarea name="majors" rows="3" class="form-control" placeholder="Teknik Informatika&#10;Sistem Informasi"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
