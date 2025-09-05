{{-- resources/views/admin/test/partials/modal-edit-test.blade.php --}}
<div class="modal fade" id="editTest{{ $test->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Quiz: {{ $test->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('test.update', $test) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Quiz</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $test->name) }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="position_id" class="form-label">Posisi</label>
                        <select name="position_id" class="form-select" required>
                            <option value="">-- Pilih Posisi --</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}"
                                    {{ old('position_id', $test->position_id) == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }} | {{ $position->batch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="test_date" class="form-label">Test Date (Tombol dibuka)</label>
                        <input type="datetime-local" class="form-control" name="test_date"
                            value="{{ old('test_date', optional($test->test_date)->format('Y-m-d\TH:i')) }}">
                    </div>

                    <div class="mb-3">
                        <label for="test_closed" class="form-label">Tombol ditutup (Opsional)</label>
                        <input type="datetime-local" class="form-control" name="test_closed"
                            value="{{ old('test_closed', optional($test->test_closed)->format('Y-m-d\TH:i')) }}">
                    </div>

                    <div class="mb-3">
                        <label for="test_end" class="form-label">Hard End (Waktu berakhirnya Quiz)</label>
                        <input type="datetime-local" class="form-control" name="test_end"
                            value="{{ old('test_end', optional($test->test_end)->format('Y-m-d\TH:i')) }}">
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
