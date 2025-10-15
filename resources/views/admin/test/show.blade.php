<x-app-admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                    Kelola Section: <span class="fw-bold">{{ $test->name }}</span>
                </h2>
                <a href="{{ route('test.index') }}" class="text-primary text-decoration-none mt-2 d-inline-block">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Semua Quiz
                </a>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahSection{{ $test->id }}">
                <i class="bi bi-plus-circle me-2"></i>Tambah Section Baru
            </button>
        </div>

        {{-- Modal Notifikasi Sukses --}}
        @if (session('success'))
            <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
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
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if ($test->sections->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Section</th>
                                        <th class="text-center">Urutan</th>
                                        <th class="text-center">Durasi</th>
                                        <th class="text-center">Acak Soal/Opsi</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($test->sections->sortBy('order') as $section)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $section->name }}</td>
                                            <td class="text-center">{{ $section->order }}</td>
                                            <td class="text-center">{{ $section->duration_minutes }} mnt</td>
                                            <td class="text-center">
                                                @if ($section->shuffle_questions)
                                                    <span class="badge bg-primary">Soal</span>
                                                @endif
                                                @if ($section->shuffle_options)
                                                    <span class="badge bg-secondary">Opsi</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{-- <a href="#" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-gear"></i>
                                                </a> --}}
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editSection{{ $section->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('section.destroy', $section) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus section ini?')"
                                                        title="Hapus Section">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        {{-- Passing variabel $question_bundles ke modal edit --}}
                                        @include('admin.test.partials.modal-edit-section', [
                                            'section' => $section,
                                            'question_bundles' => $question_bundles,
                                        ])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <p class="text-muted mb-0">Belum ada section yang ditambahkan untuk quiz ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- PERBAIKAN: Tambahkan 'question_bundles' => $question_bundles --}}
    {{-- ============================================================== --}}
    @include('admin.test.partials.modal-add-section', [
        'test' => $test,
        'question_bundles' => $question_bundles,
    ])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif
        });
    </script>
</x-app-admin>
