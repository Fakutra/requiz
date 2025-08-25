{{-- MODAL UNTUK EDIT TEST --}}
{{-- File ini bisa Anda simpan sebagai: resources/views/admin/test/partials/modal-edit-test.blade.php --}}
<div class="modal fade" id="editTest{{ $test->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Quiz: {{ $test->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Menggunakan $test bukan $test->id karena Anda menggunakan slug (Route Model Binding) --}}
            <form action="{{ route('test.update', $test) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Input Nama Test --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Quiz</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $test->name) }}"
                            required>
                    </div>

                    {{-- =============================================== --}}
                    {{-- BARU: Dropdown untuk memilih posisi (position) --}}
                    {{-- =============================================== --}}
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Posisi</label>
                        <select name="position_id" class="form-select" required>
                            <option value="">-- Pilih Posisi --</option>
                            {{-- Loop semua data 'positions' yang sudah dikirim dari controller --}}
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" {{-- Pilih posisi yang sedang aktif sebagai default --}}
                                    {{ old('position_id', $test->position_id) == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }} | {{ $position->batch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Input Tanggal Test --}}
                    <div class="mb-3">
                        <label for="test_date" class="form-label">Test Date</label>
                        {{-- Format tanggal disesuaikan dengan input type="datetime-local" --}}
                        <input type="datetime-local" class="form-control" name="test_date"
                            value="{{ old('test_date', \Carbon\Carbon::parse($test->test_date)->format('Y-m-d\TH:i')) }}"
                            required>
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
