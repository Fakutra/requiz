<div class="modal fade" id="editSection{{ $section->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('section.update', $section) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Nama Section --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Section</label>
                        <input type="text" class="form-control" name="name"
                               value="{{ $section->name }}" required>
                    </div>

                    {{-- Kategori Section --}}
                    @php
                        $sectionCategories = [
                            'umum_pg'      => 'Umum PG',
                            'teknis_pg'    => 'Teknis PG',
                            'psikologi'    => 'Psikologi / Personality',
                            'umum_essay'   => 'Umum Essay',
                            'teknis_essay' => 'Teknis Essay',
                        ];
                    @endphp

                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori Section</label>
                        <select name="category" class="form-select" required>
                            @foreach ($sectionCategories as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $section->category === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Kategori ini menentukan posisi nilai pada kolom rekap (Umum PG / Teknis PG / Psikologi / Umum Essay / Teknis Essay).
                        </small>
                    </div>

                    <div class="row">
                        {{-- Urutan Section --}}
                        <div class="col-md-6 mb-3">
                            <label for="order{{ $section->id }}" class="form-label">Urutan</label>
                            <input type="number" class="form-control"
                                   id="order{{ $section->id }}" name="order"
                                   value="{{ old('order', $section->order) }}"
                                   min="1" required>
                        </div>

                        {{-- Durasi --}}
                        <div class="col-md-6 mb-3">
                            <label for="duration_minutes" class="form-label">Durasi (menit)</label>
                            <input type="number" class="form-control" name="duration_minutes"
                                   value="{{ $section->duration_minutes }}" required min="1">
                        </div>
                    </div>

                    {{-- Paket Soal --}}
                    <div class="mb-3">
                        <label for="question_bundle_id" class="form-label">Paket Soal (Opsional)</label>
                        <select name="question_bundle_id" class="form-select">
                            <option value="">-- Tidak ada --</option>
                            @foreach ($question_bundles as $bundle)
                                <option value="{{ $bundle->id }}"
                                    {{ $section->question_bundle_id == $bundle->id ? 'selected' : '' }}>
                                    {{ $bundle->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Shuffle --}}
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox"
                               name="shuffle_questions" value="1"
                               id="edit_shuffle_questions{{ $section->id }}"
                               @checked($section->shuffle_questions)>
                        <label class="form-check-label" for="edit_shuffle_questions{{ $section->id }}">
                            Acak Urutan Soal
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="shuffle_options" value="1"
                               id="edit_shuffle_options{{ $section->id }}"
                               @checked($section->shuffle_options)>
                        <label class="form-check-label" for="edit_shuffle_options{{ $section->id }}">
                            Acak Urutan Opsi Jawaban
                        </label>
                    </div>

                </div> {{-- modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
