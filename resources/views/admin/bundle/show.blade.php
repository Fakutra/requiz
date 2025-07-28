<x-app-layout>
    {{-- HEADER HALAMAN --}}
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Bundle
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('bundle.index') }}">Semua Bundle</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $bundle->name }}</li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row">

                {{-- ============================================= --}}
                {{-- KOLOM KIRI: INFORMASI BUNDLE --}}
                {{-- ============================================= --}}
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                            <h5 class="mb-0">Informasi Bundle</h5>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $bundle->name }}</h4>
                            <p class="card-text text-muted">
                                {{ $bundle->description ?? 'Tidak ada deskripsi untuk bundle ini.' }}
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
                                                                onclick="return confirm('Yakin hapus soal ini dari bundle?')"><i
                                                                    class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <h5 class="text-muted">Belum ada soal</h5>
                                                    <p>Tambahkan soal pertama ke dalam bundle ini.</p>
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
        'availableQuestions' => $availableQuestions, // ✅ PERBAIKAN
    ])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi ini akan dijalankan untuk setiap modal tambah soal yang ada di halaman
            document.querySelectorAll('[id^="addQuestionModal-"]').forEach(modalElement => {
                const questionList = modalElement.querySelector('.question-list');
                const searchInput = modalElement.querySelector('.question-search-input');
                const selectAllSwitch = modalElement.querySelector('.select-all-questions');
                const counter = modalElement.querySelector('.selection-counter');

                if (!questionList) return;

                // Fungsi untuk mengupdate jumlah soal yang terpilih
                const updateCounter = () => {
                    const count = questionList.querySelectorAll('input[type="checkbox"]:checked')
                        .length;
                    counter.textContent = `${count} soal terpilih`;
                };

                // 1. Logika untuk PENCARIAN SOAL
                searchInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    const labels = questionList.getElementsByTagName('label');

                    Array.from(labels).forEach(label => {
                        const text = label.textContent.toLowerCase();
                        label.style.display = text.includes(filter) ? '' : 'none';
                    });
                });

                // 2. Logika untuk "PILIH SEMUA"
                selectAllSwitch.addEventListener('change', function() {
                    const checkboxes = questionList.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        // Hanya pengaruhi checkbox yang terlihat (tidak terfilter oleh pencarian)
                        if (checkbox.closest('label').style.display !== 'none') {
                            checkbox.checked = this.checked;
                        }
                    });
                    updateCounter();
                });

                // 3. Logika untuk mengupdate counter saat checkbox individu diklik
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
</x-app-layout>
