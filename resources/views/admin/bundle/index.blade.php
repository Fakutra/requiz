<x-app-admin>
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 p-md-5 text-gray-900">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                            {{ __('Section') }}
                        </h2>
                        {{-- Tombol ini sekarang akan membuka modal --}}
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBundle">
                            Create Section
                        </button>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- MULAI: Tampilan Kartu (Pengganti Akordeon) --}}
                    {{-- ====================================================== --}}

                    <div class="row">
                        @forelse ($bundles as $bundle)
                            {{-- Setiap kartu akan mengambil 1/3 lebar di layar besar, 1/2 di layar sedang --}}
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold">{{ $bundle->name }}</h5>
                                        <p class="card-text text-muted flex-grow-1">
                                            {{ Str::limit($bundle->description, 100, '...') }}
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                Dibuat: {{ $bundle->created_at->format('d M Y') }}
                                            </small>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $bundle->questions_count }} Soal
                                            </span>
                                        </div>

                                        {{-- Tombol Aksi --}}
                                        <div class="mt-auto pt-3 border-top">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('bundle.show', $bundle) }}"
                                                    class="btn btn-primary btn-sm flex-grow-1">
                                                    <i class="bi bi-card-list"></i> Kelola Soal
                                                </a>
                                                <div class="btn-group">
                                                    <button class="btn btn-secondary btn-sm" type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editBundle{{ $bundle->id }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form action="{{ route('bundle.destroy', $bundle) }}" method="post"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Anda yakin ingin menghapus section ini?')">
                                                        @method('delete')
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal mengedit bundle --}}
                            <div class="modal fade" id="editBundle{{ $bundle->id }}" tabindex="-1"
                                aria-labelledby="modalEditBundle{{ $bundle->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="modalEditBundle{{ $bundle->id }}">Edit
                                                Section</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('bundle.update', $bundle) }}" method="POST">
                                            @csrf
                                            @method('PUT') {{-- Gunakan method PUT untuk update --}}
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama Section</label>
                                                    {{-- Isi value dengan data yang ada --}}
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ old('name', $bundle->name) }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Deskripsi
                                                        (Opsional)
                                                    </label>
                                                    {{-- Isi textarea dengan data yang ada --}}
                                                    <textarea class="form-control" name="description" rows="3">{{ old('description', $bundle->description) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">Data section belum tersedia.</p>
                                <p>Silakan buat section baru dengan menekan tombol "Create Bundle" di atas.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Link Paginasi untuk Bundle --}}
                    <div class="mt-4">
                        {{ $bundles->links() }}
                    </div>

                    {{-- ====================================================== --}}
                    {{-- SELESAI: Tampilan Kartu --}}
                    {{-- ====================================================== --}}

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Bundle -->
    <div class="modal fade" id="tambahBundle" tabindex="-1" aria-labelledby="modalTambahBundle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahBundle">Buat Section Baru</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form mengarah ke route 'bundles.store' --}}
                    <form action="{{ route('bundle.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            {{-- Input Nama Bundle --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Section</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Deskripsi --}}
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi (Opsional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
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

</x-app-admin>
