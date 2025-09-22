{{-- resources/views/admin/batch/show.blade.php --}}
<x-app-admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                    Kelola Posisi: <span class="fw-bold">{{ $batch->name }}</span>
                </h2>
                <a href="{{ route('batch.index') }}" class="text-primary text-decoration-none mt-2 d-inline-block">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Semua Batch
                </a>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPosisi{{ $batch->id }}">
                <i class="bi bi-plus-circle me-2"></i>Tambah Posisi Baru
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
                    @if ($batch->position->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" class="text-center" style="width: 5%;">No.</th>
                                        <th scope="col">Nama Posisi</th>
                                        <th scope="col" class="text-center">Kuota</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center" style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($batch->position as $position)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $position->name }}</td>
                                            <td class="text-center">{{ $position->quota }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $position->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $position->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#editPosisi{{ $position->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('position.destroy', $position->id) }}"
                                                    method="post" class="d-inline"
                                                    onsubmit="return confirm('Anda yakin?')">
                                                    @method('delete') @csrf
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        {{-- Modal Edit Posisi --}}
                                        @include('admin.batch.partials.modal-edit-position', [
                                            'position' => $position,
                                        ])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <p class="text-muted mb-0">Belum ada posisi yang ditambahkan untuk batch ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Posisi --}}
    @include('admin.batch.partials.modal-add-position', ['batch' => $batch])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk menampilkan modal sukses jika ada session 'success'
            @if (session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif

            // Script untuk membuka kembali modal yang relevan jika ada error validasi
            @if ($errors->any())
                // Cek jika error berasal dari form 'tambahBatch'
                @if ($errors->has('name') || $errors->has('status') || $errors->has('start_date') || $errors->has('end_date'))
                    @if (!old('batch_id_edit')) // Hanya buka jika bukan dari edit batch
                        var tambahBatchModal = new bootstrap.Modal(document.getElementById('tambahBatch'));
                        tambahBatchModal.show();
                    @endif
                @endif

                // Cek jika error berasal dari form 'editBatch'
                var editBatchId = '{{ old('batch_id_edit') }}';
                if (editBatchId) {
                    var editBatchModal = new bootstrap.Modal(document.getElementById('editBatch' + editBatchId));
                    editBatchModal.show();
                }

                // Logika serupa bisa ditambahkan untuk modal posisi jika diperlukan
            @endif
        });
    </script>
</x-app-admin>
