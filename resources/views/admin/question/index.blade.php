<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Question Banks') }}
            </h2>
            {{-- Tombol ini sekarang akan membuka modal --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuestionModal">
                Create Question
            </button>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Tombol Import dan Form Filter/Search --}}
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-3">
                        {{-- Tombol Import --}}
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#importQuestionModal">
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i> Import from Excel
                        </button>

                        {{-- Form untuk Search dan Filter --}}
                        <form action="{{ route('question.index') }}" method="GET" class="flex-grow-1">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <div class="flex-grow-1" style="min-width: 200px;">
                                    <input type="text" class="form-control" name="search" placeholder="Search.."
                                        value="{{ request('search') }}">
                                </div>
                                <div>
                                    <select name="type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="PG" {{ request('type') == 'PG' ? 'selected' : '' }}>PG
                                        </option>
                                        <option value="Multiple" {{ request('type') == 'Multiple' ? 'selected' : '' }}>
                                            Multiple</option>
                                        <option value="Poin" {{ request('type') == 'Poin' ? 'selected' : '' }}>Poin
                                        </option>
                                        <option value="Essay" {{ request('type') == 'Essay' ? 'selected' : '' }}>Essay
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        <option value="Umum" {{ request('category') == 'Umum' ? 'selected' : '' }}>
                                            Umum</option>
                                        <option value="Teknis" {{ request('category') == 'Teknis' ? 'selected' : '' }}>
                                            Teknis</option>
                                        <option value="Psikologi"
                                            {{ request('category') == 'Psikologi' ? 'selected' : '' }}>Psikologi
                                        </option>
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                                    <a href="{{ route('question.index') }}" class="btn btn-secondary"><i
                                            class="bi bi-arrow-counterclockwise"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tabel Pertanyaan --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Pertanyaan</th>
                                    <th scope="col">Tipe</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($questions as $question)
                                    <tr>
                                        <th scope="row">
                                            {{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->iteration }}
                                        </th>
                                        <td>{{ Str::limit($question->question, 80) }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $question->type }}</span></td>
                                        <td><span class="badge bg-secondary">{{ $question->category }}</span></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- Tombol Edit yang sudah ada --}}
                                                <button type="button" class="btn btn-sm btn-warning edit-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editQuestionModal"
                                                    data-question='{{ $question->toJson() }}'>
                                                    Edit
                                                </button>

                                                {{-- Form untuk Hapus --}}
                                                <form method="POST"
                                                    action="{{ route('question.destroy', $question->id) }}"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Data pertanyaan tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link paginasi --}}
                    <div class="mt-4">
                        {{ $questions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk notifikasi sukses --}}
    @if (session('success'))
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <h5 class="text-success mb-3">✅ {{ session('success') }}</h5>
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Import Question -->
    <div class="modal fade" id="importQuestionModal" tabindex="-1" aria-labelledby="importQuestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importQuestionModalLabel">Import Questions from Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('question.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <p>
                                Silakan unduh template di bawah ini untuk memastikan format file Excel Anda benar.
                            </p>
                            <a href="{{ route('question.template') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i> Download Template
                            </a>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Upload Excel File</label>
                            <input class="form-control @error('excel_file') is-invalid @enderror" type="file"
                                id="excel_file" name="excel_file" required accept=".xlsx, .xls">
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Import Questions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Question -->
    <div class="modal fade" id="createQuestionModal" tabindex="-1" aria-labelledby="createQuestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createQuestionModalLabel">Create New Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form untuk membuat pertanyaan baru --}}
                    <form action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                    <option value="PG" {{ old('type') == 'PG' ? 'selected' : '' }}>PG</option>
                                    <option value="Essay" {{ old('type') == 'Essay' ? 'selected' : '' }}>Essay
                                    </option>
                                    <option value="Poin" {{ old('type') == 'Poin' ? 'selected' : '' }}>Poin</option>
                                    <option value="Multiple" {{ old('type') == 'Multiple' ? 'selected' : '' }}>
                                        Multiple
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category"
                                    name="category" required>
                                    {{-- <option value="Umum" {{ old('category') == 'Umum' ? 'selected' : '' }}>Umum
                                    </option> --}}
                                    <option value="Umum" {{ old('category') == 'Umum' ? 'selected' : '' }}>Umum
                                    </option>
                                    <option value="Teknis" {{ old('category') == 'Teknis' ? 'selected' : '' }}>Teknis
                                    </option>
                                    <option value="Psikologi" {{ old('category') == 'Psikologi' ? 'selected' : '' }}>
                                        Psikologi</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="question" class="form-label">Question</label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question" rows="3"
                                required>{{ old('question') }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Container untuk Opsi Jawaban (A-E) --}}
                        <div id="options-container">
                            <hr>
                            <p class="fw-bold">Options</p>
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $option)
                                <div class="row mb-2">
                                    {{-- Input Option --}}
                                    <div class="col-md-8">
                                        <label for="option_{{ $option }}" class="form-label">Option
                                            {{ strtoupper($option) }}</label>
                                        <input type="text"
                                            class="form-control @error('option_' . $option) is-invalid @enderror"
                                            id="option_{{ $option }}" name="option_{{ $option }}"
                                            value="{{ old('option_' . $option) }}">
                                    </div>
                                    {{-- Input Point (khusus untuk tipe Poin) --}}
                                    <div class="col-md-4 point-input-wrapper">
                                        <label for="point_{{ $option }}" class="form-label">Point
                                            {{ strtoupper($option) }}</label>
                                        <input type="number"
                                            class="form-control @error('point_' . $option) is-invalid @enderror"
                                            id="point_{{ $option }}" name="point_{{ $option }}"
                                            value="{{ old('point_' . $option) ?? 0 }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        {{-- Jawaban untuk Tipe PG (Single Choice) --}}
                        <div class="mb-3" id="pg-answer-container">
                            <label for="answer" class="form-label">Correct Answer</label>
                            <select class="form-select @error('answer') is-invalid @enderror" id="answer"
                                name="answer">
                                <option value="">Select Correct Answer</option>
                                <option value="A" {{ old('answer') == 'A' ? 'selected' : '' }}>Option A</option>
                                <option value="B" {{ old('answer') == 'B' ? 'selected' : '' }}>Option B</option>
                                <option value="C" {{ old('answer') == 'C' ? 'selected' : '' }}>Option C</option>
                                <option value="D" {{ old('answer') == 'D' ? 'selected' : '' }}>Option D</option>
                                <option value="E" {{ old('answer') == 'E' ? 'selected' : '' }}>Option E</option>
                            </select>
                        </div>

                        {{-- Jawaban untuk Tipe Multiple (Multiple Choice) --}}
                        <div class="mb-3" id="multiple-answer-container">
                            <label class="form-label">Correct Answers</label>
                            @foreach (['A', 'B', 'C', 'D', 'E'] as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answer[]"
                                        value="{{ $option }}" id="answer_{{ $option }}"
                                        {{ is_array(old('answer')) && in_array($option, old('answer')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="answer_{{ $option }}">
                                        Option {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image (Optional)</label>
                            <input class="form-control" type="file" id="image" name="image">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Question -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form untuk mengedit pertanyaan --}}
                    <form id="editQuestionForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Konten form sama seperti modal create, tapi dengan ID yang berbeda --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_type" class="form-label">Type</label>
                                <select class="form-select" id="edit_type" name="type" required>
                                    <option value="PG">PG</option>
                                    <option value="Essay">Essay</option>
                                    <option value="Poin">Poin</option>
                                    <option value="Multiple">Multiple</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_category" name="category" required>
                                    <option value="Umum">Umum</option>
                                    <option value="Teknis">Teknis</option>
                                    <option value="Psikologi">Psikologi</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_question" class="form-label">Question</label>
                            <textarea class="form-control" id="edit_question" name="question" rows="3" required></textarea>
                        </div>

                        <div id="edit_options_container">
                            <hr>
                            <p class="fw-bold">Options & Points</p>
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $option)
                                <div class="row mb-2">
                                    <div class="col-md-8">
                                        <label for="edit_option_{{ $option }}" class="form-label">Option
                                            {{ strtoupper($option) }}</label>
                                        <input type="text" class="form-control"
                                            id="edit_option_{{ $option }}" name="option_{{ $option }}">
                                    </div>
                                    <div class="col-md-4 edit-point-input-wrapper">
                                        <label for="edit_point_{{ $option }}" class="form-label">Point
                                            {{ strtoupper($option) }}</label>
                                        <input type="number" class="form-control"
                                            id="edit_point_{{ $option }}" name="point_{{ $option }}"
                                            value="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        <div class="mb-3" id="edit_pg_answer_container">
                            <label for="edit_answer" class="form-label">Correct Answer</label>
                            <select class="form-select" id="edit_answer" name="answer">
                                <option value="">Select Correct Answer</option>
                                <option value="A">Option A</option>
                                <option value="B">Option B</option>
                                <option value="C">Option C</option>
                                <option value="D">Option D</option>
                                <option value="E">Option E</option>
                            </select>
                        </div>

                        <div class="mb-3" id="edit_multiple_answer_container">
                            <label class="form-label">Correct Answers</label>
                            @foreach (['A', 'B', 'C', 'D', 'E'] as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answer[]"
                                        value="{{ $option }}" id="edit_answer_check_{{ $option }}">
                                    <label class="form-check-label"
                                        for="edit_answer_check_{{ $option }}">Option
                                        {{ $option }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Image (Optional)</label>
                            <input class="form-control" type="file" id="edit_image" name="image">
                            <small class="form-text text-muted">Current Image: <span
                                    id="current_image_text">None</span></small>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk menampilkan modal sukses secara otomatis --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        </script>
    @endif

    <script>
        // Menunggu seluruh konten halaman dimuat sebelum menjalankan skrip
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editQuestionModal');
            if (editModal) {
                // Fungsi untuk mengatur tampilan field di MODAL EDIT
                const toggleEditFields = () => {
                    const selectedType = document.getElementById('edit_type').value;
                    const optionsContainer = document.getElementById('edit_options_container');
                    const pgAnswerContainer = document.getElementById('edit_pg_answer_container');
                    const multipleAnswerContainer = document.getElementById('edit_multiple_answer_container');
                    const pointInputs = document.querySelectorAll('.edit-point-input-wrapper');

                    // Sembunyikan semua elemen kondisional
                    optionsContainer.style.display = 'none';
                    pgAnswerContainer.style.display = 'none';
                    multipleAnswerContainer.style.display = 'none';
                    pointInputs.forEach(input => input.style.display = 'none');

                    // Tampilkan berdasarkan tipe
                    if (['PG', 'Multiple', 'Poin'].includes(selectedType)) {
                        optionsContainer.style.display = 'block';
                    }
                    if (selectedType === 'PG') {
                        pgAnswerContainer.style.display = 'block';
                    } else if (selectedType === 'Multiple') {
                        multipleAnswerContainer.style.display = 'block';
                    } else if (selectedType === 'Poin') {
                        pointInputs.forEach(input => input.style.display = 'block');
                    }
                };

                // Tambahkan event listener ke select type di modal edit
                document.getElementById('edit_type').addEventListener('change', toggleEditFields);

                // Event listener saat modal edit ditampilkan
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget; // Tombol yang memicu modal
                    const question = JSON.parse(button.getAttribute(
                        'data-question')); // Ambil data dan parse dari JSON

                    // 1. Set action form
                    const form = document.getElementById('editQuestionForm');
                    const baseUrl = "{{ url('admin/question') }}";
                    form.action = `${baseUrl}/${question.id}`;

                    // 2. Isi semua field form dengan data question
                    document.getElementById('edit_type').value = question.type;
                    document.getElementById('edit_category').value = question.category;
                    document.getElementById('edit_question').value = question.question;

                    // Isi Opsi Jawaban
                    const options = ['a', 'b', 'c', 'd', 'e'];
                    options.forEach(opt => {
                        document.getElementById(`edit_option_${opt}`).value = question[
                            `option_${opt}`] || '';
                        document.getElementById(`edit_point_${opt}`).value = question[
                            `point_${opt}`] || 0;
                    });

                    // Reset jawaban sebelumnya
                    document.getElementById('edit_answer').value = '';
                    document.querySelectorAll('#edit_multiple_answer_container input[type="checkbox"]')
                        .forEach(chk => chk.checked = false);

                    // 3. Set jawaban yang benar berdasarkan tipe
                    if (question.type === 'PG') {
                        document.getElementById('edit_answer').value = question.answer;
                    } else if (question.type === 'Multiple') {
                        const answers = question.answer.split(',');
                        answers.forEach(ans => {
                            const checkbox = document.getElementById(`edit_answer_check_${ans}`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }

                    // 4. Tampilkan path gambar saat ini
                    document.getElementById('current_image_text').textContent = question.image_path ?
                        question.image_path : 'None';

                    // 5. Panggil fungsi untuk menyesuaikan tampilan field
                    toggleEditFields();
                });
            }

            const typeSelect = document.getElementById('type');
            const optionsContainer = document.getElementById('options-container');
            const pointInputs = document.querySelectorAll('.point-input-wrapper');
            const pgAnswerContainer = document.getElementById('pg-answer-container');
            const multipleAnswerContainer = document.getElementById('multiple-answer-container');

            // Fungsi untuk mengatur tampilan field berdasarkan tipe soal
            function toggleFields() {
                const selectedType = typeSelect.value;

                // Sembunyikan semua elemen kondisional terlebih dahulu
                optionsContainer.style.display = 'none';
                pgAnswerContainer.style.display = 'none';
                multipleAnswerContainer.style.display = 'none';
                pointInputs.forEach(input => input.style.display = 'none');

                // Tampilkan elemen berdasarkan tipe yang dipilih
                if (selectedType === 'PG') {
                    optionsContainer.style.display = 'block';
                    pgAnswerContainer.style.display = 'block';
                } else if (selectedType === 'Poin') {
                    optionsContainer.style.display = 'block';
                    pointInputs.forEach(input => input.style.display = 'block');
                } else if (selectedType === 'Multiple') {
                    optionsContainer.style.display = 'block';
                    multipleAnswerContainer.style.display = 'block';
                }
                // Untuk 'Essay', semua elemen di atas akan tetap tersembunyi
            }

            // Tambahkan event listener untuk memanggil fungsi saat pilihan berubah
            typeSelect.addEventListener('change', toggleFields);

            // Panggil fungsi sekali saat halaman dimuat untuk mengatur state awal
            toggleFields();
        });
    </script>

    {{-- Script untuk menampilkan modal jika terjadi error validasi file --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek jika ada error validasi khusus untuk file import
            @if ($errors->has('excel_file'))
                var importModal = new bootstrap.Modal(document.getElementById('importQuestionModal'), {
                    keyboard: false
                });
                importModal.show();
            @endif
        });
    </script>
</x-app-layout>
