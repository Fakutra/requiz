<x-app-admin>


    <div class="bg-white rounded-lg shadow-sm p-4 mb-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center mb-3 ">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0 **bg-gray-100 p-2 rounded**">
                         {{ __('Question Banks') }}
                    </h2>
                    {{-- Tombol ini sekarang akan membuka modal --}}
                     <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuestionModal">
                         Create Question
                    </button>
                    </div>
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

                                        {{-- Lakukan perulangan untuk setiap kategori dari database --}}
                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}"
                                                {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
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
                                                <button type="button" class="btn btn-warning btn-sm edit-question-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editQuestionModal"
                                                    data-id="{{ $question->id }}"
                                                    data-action="{{ route('question.update', $question->id) }}"
                                                    data-type="{{ $question->type }}"
                                                    data-category="{{ $question->category }}"
                                                    data-question="{{ $question->question }}"
                                                    data-option_a="{{ $question->option_a }}"
                                                    data-option_b="{{ $question->option_b }}"
                                                    data-option_c="{{ $question->option_c }}"
                                                    data-option_d="{{ $question->option_d }}"
                                                    data-option_e="{{ $question->option_e }}"
                                                    data-point_a="{{ $question->point_a }}"
                                                    data-point_b="{{ $question->point_b }}"
                                                    data-point_c="{{ $question->point_c }}"
                                                    data-point_d="{{ $question->point_d }}"
                                                    data-point_e="{{ $question->point_e }}"
                                                    data-answer="{{ $question->answer }}"
                                                    data-image_path="{{ $question->image_path ? asset($question->image_path) : 'None' }}">
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
                    <form action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data"
                        id="createQuestionForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_type" class="form-label">Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="create_type"
                                    name="type" required>
                                    <option value="PG" {{ old('type') == 'PG' ? 'selected' : '' }}>PG</option>
                                    <option value="Essay" {{ old('type') == 'Essay' ? 'selected' : '' }}>Essay
                                    </option>
                                    <option value="Poin" {{ old('type') == 'Poin' ? 'selected' : '' }}>Poin</option>
                                    <option value="Multiple" {{ old('type') == 'Multiple' ? 'selected' : '' }}>
                                        Multiple</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="create_category" class="form-label">Category</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                    id="create_category" name="category" value="{{ old('category') }}" required>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="create_question" class="form-label">Question</label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="create_question" name="question"
                                rows="3" required>{{ old('question') }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ganti id menjadi 'create_options_container' --}}
                        <div id="create_options_container">
                            <hr>
                            <p class="fw-bold">Options & Points</p>
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $option)
                                <div class="row mb-2">
                                    <div class="col-md-8">
                                        <label for="create_option_{{ $option }}" class="form-label">Option
                                            {{ strtoupper($option) }}</label>
                                        <input type="text"
                                            class="form-control @error('option_' . $option) is-invalid @enderror"
                                            id="create_option_{{ $option }}" name="option_{{ $option }}"
                                            value="{{ old('option_' . $option) }}">
                                    </div>
                                    {{-- Ganti class menjadi 'create-point-input-wrapper' --}}
                                    <div class="col-md-4 create-point-input-wrapper">
                                        <label for="create_point_{{ $option }}" class="form-label">Point
                                            {{ strtoupper($option) }}</label>
                                        <input type="number"
                                            class="form-control @error('point_' . $option) is-invalid @enderror"
                                            id="create_point_{{ $option }}" name="point_{{ $option }}"
                                            value="{{ old('point_' . $option) ?? 0 }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        {{-- Ganti id menjadi 'create_pg_answer_container' --}}
                        <div class="mb-3" id="create_pg_answer_container">
                            <label for="create_answer_pg" class="form-label">Correct Answer</label>
                            <select class="form-select @error('answer') is-invalid @enderror" id="create_answer_pg"
                                name="answer">
                                <option value="">Select Correct Answer</option>
                                <option value="A" {{ old('answer') == 'A' ? 'selected' : '' }}>Option A</option>
                                <option value="B" {{ old('answer') == 'B' ? 'selected' : '' }}>Option B</option>
                                <option value="C" {{ old('answer') == 'C' ? 'selected' : '' }}>Option C</option>
                                <option value="D" {{ old('answer') == 'D' ? 'selected' : '' }}>Option D</option>
                                <option value="E" {{ old('answer') == 'E' ? 'selected' : '' }}>Option E</option>
                            </select>
                        </div>

                        {{-- Ganti id menjadi 'create_multiple_answer_container' --}}
                        <div class="mb-3" id="create_multiple_answer_container">
                            <label class="form-label">Correct Answers</label>
                            @foreach (['A', 'B', 'C', 'D', 'E'] as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answer[]"
                                        value="{{ $option }}" id="create_answer_check_{{ $option }}"
                                        {{ is_array(old('answer')) && in_array($option, old('answer')) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="create_answer_check_{{ $option }}">Option
                                        {{ $option }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label for="create_image" class="form-label">Image (Optional)</label>
                            <input class="form-control" type="file" id="create_image" name="image">
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
                                {{-- Atribut 'value' akan diisi oleh JavaScript saat modal muncul --}}
                                <input type="text" class="form-control" id="edit_category" name="category"
                                    required>
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

    {{-- Script untuk menangani modal edit pertanyaan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap event saat modal akan ditampilkan
            var editQuestionModal = document.getElementById('editQuestionModal');
            editQuestionModal.addEventListener('show.bs.modal', function(event) {
                // Dapatkan tombol yang memicu modal
                var button = event.relatedTarget;

                // Ekstrak data dari atribut data-*
                var action = button.getAttribute('data-action');
                var type = button.getAttribute('data-type');
                var category = button.getAttribute('data-category');
                var questionText = button.getAttribute('data-question');
                var answer = button.getAttribute('data-answer');
                var imagePath = button.getAttribute('data-image_path');

                // Dapatkan elemen form di dalam modal
                var form = document.getElementById('editQuestionForm');
                var modal = this; // 'this' merujuk ke modal

                // Set action form
                form.action = action;

                // Isi nilai form
                modal.querySelector('#edit_type').value = type;
                modal.querySelector('input[name="category"]').value = category;
                modal.querySelector('#edit_question').value = questionText;

                // Isi nilai options dan points
                ['a', 'b', 'c', 'd', 'e'].forEach(function(opt) {
                    var optionValue = button.getAttribute('data-option_' + opt);
                    var pointValue = button.getAttribute('data-point_' + opt);
                    modal.querySelector('#edit_option_' + opt).value = optionValue;
                    modal.querySelector('#edit_point_' + opt).value = pointValue ||
                        0; // Default ke 0 jika null
                });

                // Reset semua jawaban sebelum diisi
                modal.querySelector('#edit_answer').value = '';
                modal.querySelectorAll('input[name="answer[]"]').forEach(function(chk) {
                    chk.checked = false;
                });

                // Atur jawaban berdasarkan tipe
                if (type === 'PG') {
                    modal.querySelector('#edit_answer').value = answer;
                } else if (type === 'Multiple' && answer) {
                    var answers = answer.split(',');
                    answers.forEach(function(ans) {
                        var checkbox = modal.querySelector('#edit_answer_check_' + ans);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                }

                // Tampilkan path gambar saat ini
                modal.querySelector('#current_image_text').textContent = imagePath;

                // Panggil fungsi untuk menampilkan/menyembunyikan field berdasarkan tipe soal
                // (Anda mungkin sudah punya fungsi ini, panggil lagi di sini)
                toggleEditFields(type);
            });

            // Fungsi untuk menampilkan/menyembunyikan field (wajib ada)
            function toggleEditFields(type) {
                const optionsContainer = document.getElementById('edit_options_container');
                const pgAnswerContainer = document.getElementById('edit_pg_answer_container');
                const multipleAnswerContainer = document.getElementById('edit_multiple_answer_container');

                // Sembunyikan semua kontainer dulu
                optionsContainer.style.display = 'none';
                pgAnswerContainer.style.display = 'none';
                multipleAnswerContainer.style.display = 'none';

                if (type === 'PG' || type === 'Multiple' || type === 'Poin') {
                    optionsContainer.style.display = 'block';
                }
                if (type === 'PG') {
                    pgAnswerContainer.style.display = 'block';
                }
                if (type === 'Multiple') {
                    multipleAnswerContainer.style.display = 'block';
                }
            }

            // Listener untuk select type di modal edit
            document.getElementById('edit_type').addEventListener('change', function() {
                toggleEditFields(this.value);
            });

        });
    </script>

    {{-- Script untuk menangani field dinamis di modal CREATE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk menampilkan/menyembunyikan field di modal CREATE
            function toggleCreateFields(type) {
                const optionsContainer = document.getElementById('create_options_container');
                const pgAnswerContainer = document.getElementById('create_pg_answer_container');
                const multipleAnswerContainer = document.getElementById('create_multiple_answer_container');
                const pointInputs = optionsContainer.querySelectorAll('.create-point-input-wrapper');

                // 1. Atur visibilitas utama
                optionsContainer.style.display = 'none';
                pgAnswerContainer.style.display = 'none';
                multipleAnswerContainer.style.display = 'none';
                pointInputs.forEach(input => input.style.display = 'none'); // Sembunyikan input poin by default

                // 2. Tampilkan field berdasarkan tipe
                switch (type) {
                    case 'PG':
                        optionsContainer.style.display = 'block';
                        pgAnswerContainer.style.display = 'block';
                        break;
                    case 'Multiple':
                        optionsContainer.style.display = 'block';
                        multipleAnswerContainer.style.display = 'block';
                        break;
                    case 'Poin':
                        optionsContainer.style.display = 'block';
                        pointInputs.forEach(input => input.style.display = 'block'); // Tampilkan hanya untuk Poin
                        break;
                    case 'Essay':
                        // Semua field opsi dan jawaban tetap tersembunyi
                        break;
                }
            }

            // Listener untuk select type di modal CREATE
            const createTypeSelect = document.getElementById('create_type');
            createTypeSelect.addEventListener('change', function() {
                toggleCreateFields(this.value);
            });

            // Panggil fungsi saat DOM selesai dimuat untuk mengatur state awal
            // Ini penting jika ada validation error dan form di-populate ulang dengan `old('type')`
            toggleCreateFields(createTypeSelect.value);
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
</x-app-admin>
