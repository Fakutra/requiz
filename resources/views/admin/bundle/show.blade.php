<x-app-admin>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg py-4">
        <div class = "relative flex items-center gap-2 mb-4 sm:px-6 lg:px-8">
            <a href="{{ route('bundle.index') }}" 
                class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="text-lg font-semibold leading-none m-0">
                Kelola Section
            </h2>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">

                {{-- ============================================= --}}
                {{-- KOLOM KIRI: INFORMASI BUNDLE --}}
                {{-- ============================================= --}}
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                            <h5 class="mb-0">Informasi Section</h5>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $bundle->name }}</h4>
                            <p class="card-text text-muted">
                                {{ $bundle->description ?? 'Tidak ada deskripsi untuk section ini.' }}
                            </p>
                            <hr>
                            <ul class="list-unstyled">
                                <li class="mb-2 d-flex justify-content-between">
                                    <strong><i class="bi bi-card-checklist me-2"></i>Jumlah Soal</strong>
                                    <span class="badge bg-primary">{{ $questionsInBundle->total() }}</span>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <strong><i class="bi bi-calendar-event me-2"></i>Dibuat Pada</strong>
                                    <span>{{ $bundle->created_at->format('d M Y') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ============================================= --}}
                {{-- KOLOM KANAN: DAFTAR SOAL --}}
                {{-- ============================================= --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div
                            class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h5 class="mb-0">Daftar Soal</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addQuestionModal-{{ $bundle->id }}">
                                    <i class="bi bi-plus-lg"></i> Tambah Soal
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 5%;">#</th>
                                            <th scope="col">Soal</th>
                                            <th scope="col">Tipe</th>
                                            <th scope="col">Category</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($questionsInBundle as $question)
                                            <tr>
                                                {{-- Penomoran yang benar untuk paginasi --}}
                                                <th scope="row">{{ $questionsInBundle->firstItem() + $loop->index }}
                                                </th>
                                                <td>{{ Str::limit($question->question, 70) }}</td>
                                                <td><span class="badge bg-info text-dark">{{ $question->type }}</span>
                                                <td><span
                                                        class="badge bg-warning text-dark">{{ $question->category }}</span>
                                                </td>
                                                <td class="text-center">
                                                    {{-- Tombol Aksi dengan Dropdown --}}
                                                    <div class="btn-group">
                                                        {{-- <a href="#" class="btn btn-light btn-sm"
                                                            title="Lihat Detail Soal"><i class="bi bi-eye"></i></a> --}}
                                                        <form
                                                            action="{{ route('bundle.questions.remove', ['bundle' => $bundle, 'question' => $question->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-light btn-sm text-danger"
                                                                title="Hapus dari Bundle"
                                                                onclick="return confirm('Yakin hapus soal ini dari section?')"><i
                                                                    class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <h5 class="text-muted">Belum ada soal</h5>
                                                    <p>Tambahkan soal pertama ke dalam section ini.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- Link Paginasi di Footer Kartu --}}
                        @if ($questionsInBundle->hasPages())
                            <div class="card-footer bg-white">
                                {{ $questionsInBundle->links() }}
                            </div>
                        @endif
                    </div>
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

    {{-- Modal untuk Tambah Soal --}}
    @include('admin.bundle.partials.add-question-modal', [
        'bundle' => $bundle,
        'availableQuestions' => $availableQuestions,
        'categories' => $categories, // <-- TAMBAHKAN INI
    ])

    {{-- MODIFIKASI: Ganti seluruh blok script ini dengan yang baru --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi ini akan dijalankan untuk setiap modal tambah soal yang ada di halaman
            document.querySelectorAll('[id^="addQuestionModal-"]').forEach(modalElement => {
                const questionList = modalElement.querySelector('.question-list');
                const searchInput = modalElement.querySelector('.question-search-input');
                // BARU: Ambil elemen filter kategori
                const categoryFilter = modalElement.querySelector('.question-category-filter');
                const selectAllSwitch = modalElement.querySelector('.select-all-questions');
                const counter = modalElement.querySelector('.selection-counter');

                if (!questionList || !categoryFilter) return; // Pastikan filter kategori ada

                // FUNGSI BARU: Menggabungkan logika filter pencarian dan kategori
                const applyFilters = () => {
                    const searchText = searchInput.value.toLowerCase();
                    const selectedCategory = categoryFilter.value;
                    const labels = questionList.getElementsByTagName('label');

                    Array.from(labels).forEach(label => {
                        const questionText = label.textContent.toLowerCase();
                        const questionCategory = label.dataset
                            .category; // Ambil dari data-category

                        // Cek kedua kondisi
                        const textMatch = questionText.includes(searchText);
                        const categoryMatch = (selectedCategory === "" || questionCategory ===
                            selectedCategory);

                        // Tampilkan hanya jika kedua kondisi terpenuhi
                        label.style.display = (textMatch && categoryMatch) ? '' : 'none';
                    });
                };

                // Fungsi untuk mengupdate jumlah soal yang terpilih
                const updateCounter = () => {
                    const count = questionList.querySelectorAll('input[type="checkbox"]:checked')
                        .length;
                    counter.textContent = `${count} soal terpilih`;
                };

                // 1. Event listener untuk PENCARIAN dan FILTER KATEGORI
                searchInput.addEventListener('input', applyFilters);
                categoryFilter.addEventListener('change', applyFilters);

                // 2. Logika untuk "PILIH SEMUA" (tidak perlu diubah)
                selectAllSwitch.addEventListener('change', function() {
                    const checkboxes = questionList.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        if (checkbox.closest('label').style.display !== 'none') {
                            checkbox.checked = this.checked;
                        }
                    });
                    updateCounter();
                });

                // 3. Logika untuk counter (tidak perlu diubah)
                questionList.addEventListener('change', function(event) {
                    if (event.target.matches('input[type="checkbox"]')) {
                        updateCounter();
                    }
                });

                // Inisialisasi counter saat modal dibuka
                modalElement.addEventListener('show.bs.modal', updateCounter);
            });
        });
    </script>
</x-app-admin>
