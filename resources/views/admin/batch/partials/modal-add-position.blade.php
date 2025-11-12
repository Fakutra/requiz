{{-- MODAL UNTUK TAMBAH POSISI (Tempatkan di dalam loop batch) --}}
<div class="modal fade" id="tambahPosisi{{ $batch->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Posisi untuk: {{ $batch->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('position.store', $batch) }}" method="POST" onsubmit="return validateDescriptions('{{ $batch->id }}')">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="batch_id" value="{{ $batch->id }}">

                    {{-- NAMA POSISI --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Posisi *</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>

                    {{-- KUOTA --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kuota *</label>
                        <input type="number" class="form-control" name="quota" value="{{ old('quota', 1) }}" min="1" required>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status *</label>
                        <select class="form-select" name="status" required>
                            <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- PENDIDIKAN MINIMUM --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pendidikan Minimum *</label>
                        <select class="form-select" name="pendidikan_minimum" required>
                            <option value="">--- Pilih ---</option>
                            <option value="SMA/Sederajat" {{ old('pendidikan_minimum') === 'SMA/Sederajat' ? 'selected' : '' }}>SMA / Sederajat</option>
                            <option value="D1" {{ old('pendidikan_minimum') === 'D1' ? 'selected' : '' }}>D1</option>
                            <option value="D2" {{ old('pendidikan_minimum') === 'D2' ? 'selected' : '' }}>D2</option>
                            <option value="D3" {{ old('pendidikan_minimum') === 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="D4" {{ old('pendidikan_minimum') === 'D4' ? 'selected' : '' }}>D4</option>
                            <option value="S1" {{ old('pendidikan_minimum') === 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('pendidikan_minimum') === 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('pendidikan_minimum') === 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                        @error('pendidikan_minimum')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- DESKRIPSI (enter per item) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi *</label>
                        <textarea class="form-control" name="descriptions" id="descriptions{{ $batch->id }}" rows="4"
                            placeholder="Contoh:
Mengelola operasional harian tim
Bertanggung jawab atas laporan mingguan
Berkoordinasi dengan divisi IT">{{ old('descriptions') }}</textarea>

                        <small class="text-muted d-block mt-1">Pisahkan tiap poin dengan <code>Enter</code>. Sistem menyimpan tiap baris sebagai item terpisah.</small>

                        @error('descriptions')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- SKILL YANG DIBUTUHKAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Skill yang Dibutuhkan</label>
                        <textarea class="form-control" name="skills" rows="3" placeholder="Contoh: SQL, Python, Data Visualization">{{ old('skills') }}</textarea>
                    </div>

                    {{-- PERSYARATAN UMUM --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persyaratan Umum</label>
                        <textarea class="form-control" name="requirements" rows="3"
                            placeholder="Contoh:
Min. IPK 3.00
Batas usia 35 tahun">{{ old('requirements') }}</textarea>
                    </div>

                    {{-- JURUSAN YANG DAPAT MELAMAR --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jurusan yang Dapat Melamar</label>
                        <textarea class="form-control" name="majors" rows="3"
                            placeholder="Contoh:
Teknik Informatika
Ilmu Komputer
Sistem Informasi">{{ old('majors') }}</textarea>
                    </div>

                    {{-- BATAS LAMARAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Batas Lamaran</label>
                        <input type="date" class="form-control" name="deadline" value="{{ old('deadline') }}">
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

{{-- Single JS validator (letakkan sekali di layout atau bawah page, jangan di-loop) --}}
<script>
function validateDescriptions(batchId) {
    // find textarea by ID
    const el = document.getElementById(`descriptions${batchId}`);
    if (!el) return true; // safety
    const v = el.value.trim();
    if (!v) {
        alert('Deskripsi wajib diisi! Tulis minimal 1 baris.');
        el.focus();
        return false;
    }
    // ensure at least one non-empty line
    const lines = v.split(/\r\n|\r|\n/).map(s => s.trim()).filter(s => s !== '');
    if (lines.length === 0) {
        alert('Deskripsi wajib diisi! Tulis minimal 1 baris.');
        el.focus();
        return false;
    }
    return true;
}
</script>
