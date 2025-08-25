<div class="modal fade" id="tambahSection{{ $test->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('section.store', $test) }}" method="POST">
            @csrf
            <input type="hidden" name="test_id" value="{{ $test->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Section Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Section</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="order" class="form-label">Urutan</label>
                            <input type="number" class="form-control" name="order" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="duration_minutes" class="form-label">Durasi (menit)</label>
                            <input type="number" class="form-control" name="duration_minutes" required min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="question_bundle_id" class="form-label">Paket Soal (Opsional)</label>
                        <select name="question_bundle_id" class="form-select">
                            <option value="">-- Tidak ada --</option>
                            @foreach ($question_bundles as $bundle)
                                <option value="{{ $bundle->id }}">{{ $bundle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1"
                            id="add_shuffle_questions">
                        <label class="form-check-label" for="add_shuffle_questions">Acak Urutan Soal</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="shuffle_options" value="1"
                            id="add_shuffle_options">
                        <label class="form-check-label" for="add_shuffle_options">Acak Urutan Opsi Jawaban</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
