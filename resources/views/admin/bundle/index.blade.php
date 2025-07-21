<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Bundle') }}
            </h2>
            {{-- Tombol ini sekarang akan membuka modal --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBundle">
                Create Bundle
            </button>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 p-md-5 text-gray-900">

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
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus bundle ini?')) { document.getElementById('delete-form-{{ $bundle->id }}').submit(); }">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $bundle->id }}"
                                                        action="{{ route('bundle.destroy', $bundle->id) }}"
                                                        method="post" class="d-none">
                                                        @method('delete')
                                                        @csrf
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
                                                Bundle</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('bundle.update', $bundle) }}" method="POST">
                                            @csrf
                                            @method('PUT') {{-- Gunakan method PUT untuk update --}}
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama Bundle</label>
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
                                <p class="text-muted">Data bundle belum tersedia.</p>
                                <p>Silakan buat bundle baru dengan menekan tombol "Create Bundle" di atas.</p>
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

    <!-- Modal Create Bundle -->
    <div class="modal fade" id="tambahBundle" tabindex="-1" aria-labelledby="modalTambahBundle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahBundle">Buat Bundle Baru</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form mengarah ke route 'bundles.store' --}}
                    <form action="{{ route('bundle.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            {{-- Input Nama Bundle --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Bundle</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
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

</x-app-layout>
