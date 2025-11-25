<x-app-admin>
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-semibold mb-1">Periksa input:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 p-md-5 text-gray-900">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                            {{ __('Penjadwalan Interview') }}
                        </h2>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateSchedule">
                            Create New Schedule
                        </a>
                    </div>
                    <div class="list-group">
                        @forelse ($schedules as $sch)
                            <div
                                class="list-group-item list-group-item-action d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2">
                                <div class="me-md-3">
                                    <h5 class="mb-1 fw-bold">{{ $sch->position->name ?? '—' }}</h5>

                                    <div class="text-muted small d-flex flex-column gap-1">

                                        {{-- Start - End --}}
                                        <div>
                                            <i class="bi bi-calendar-range"></i>
                                            {{ optional($sch->schedule_start)->translatedFormat('d F Y H:i') }}
                                            —
                                            {{ optional($sch->schedule_end)->translatedFormat('d F Y H:i') }}
                                        </div>

                                        {{-- Meeting Link --}}
                                        @if ($sch->zoom_link)
                                            <div>
                                                <i class="bi bi-link-45deg"></i>
                                                <a href="{{ $sch->zoom_link }}" target="_blank">
                                                    {{ \Illuminate\Support\Str::limit($sch->zoom_link, 60) }}
                                                </a>
                                            </div>
                                        @endif

                                        {{-- Meeting ID + Passcode --}}
                                        @if ($sch->zoom_id || $sch->zoom_passcode)
                                            <div>
                                                <i class="bi bi-shield-lock"></i>
                                                ID: {{ $sch->zoom_id ?? '—' }}, 
                                                Pwd: {{ $sch->zoom_passcode ?? '—' }}
                                            </div>
                                        @endif

                                    </div>

                                    {{-- Notes --}}
                                    @if ($sch->keterangan)
                                        <div class="mt-2 text-secondary small">
                                            <i class="bi bi-card-text"></i>
                                            {{ \Illuminate\Support\Str::limit($sch->keterangan, 120) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="btn-group mt-3 mt-md-0" role="group">
                                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                        data-bs-target="#editSchedule{{ $sch->id }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <form action="{{ route('interview-schedule.destroy', $sch) }}" method="post"
                                        class="d-inline" onsubmit="return confirm('Hapus schedule ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal Edit --}}
                            <div class="modal fade" id="editSchedule{{ $sch->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5">Edit Interview Schedule</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="post" action="{{ route('interview-schedule.update', $sch) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                                    <select name="position_id"
                                                        class="form-select position-select"
                                                        style="max-height: 250px; overflow-y: auto;"
                                                        required
                                                    >
                                                        <option value="">-- Pilih --</option>
                                                        @foreach ($positions->groupBy(fn($p) => $p->batch->name ?? 'Tanpa Batch') as $batchName => $batchPositions)
                                                            <optgroup label="{{ $batchName }}">
                                                                @foreach ($batchPositions as $pos)
                                                                    <option
                                                                        value="{{ $pos->id }}"
                                                                        data-batch="{{ $batchName }}"
                                                                        data-pos-name="{{ $pos->name }}"
                                                                        @selected($pos->id == $sch->position_id)
                                                                    >
                                                                        {{ $pos->name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Start <span class="text-danger">*</span></label>
                                                        <input type="datetime-local" name="schedule_start" class="form-control"
                                                            value="{{ optional($sch->schedule_start)?->format('Y-m-d\TH:i') }}" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">End <span class="text-danger">*</span></label>
                                                        <input type="datetime-local" name="schedule_end" class="form-control"
                                                            value="{{ optional($sch->schedule_end)?->format('Y-m-d\TH:i') }}" required>
                                                    </div>
                                                </div>

                                                {{-- ZOOM LINK WAJIB --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Meeting Link (Zoom/Meet) <span class="text-danger">*</span></label>
                                                    <input type="url" name="zoom_link" class="form-control"
                                                        value="{{ $sch->zoom_link }}" required>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Meeting ID</label>
                                                        <input type="text" name="zoom_id" class="form-control"
                                                            value="{{ $sch->zoom_id }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Passcode</label>
                                                        <input type="text" name="zoom_passcode"
                                                            class="form-control" value="{{ $sch->zoom_passcode }}">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Keterangan</label>
                                                    <textarea name="keterangan" rows="3" class="form-control">{{ $sch->keterangan }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item">
                                <p class="text-center text-muted my-3">Belum ada Jadwal Interview.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($schedules->hasPages())
                        <div class="mt-3">{{ $schedules->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    <div class="modal fade" id="modalCreateSchedule" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Create Interview Schedule</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="post" action="{{ route('interview-schedule.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Posisi <span class="text-danger">*</span></label>
                            <select 
                                name="position_id" 
                                class="form-select position-select"
                                required
                                style="max-height: 250px; overflow-y: auto;"
                            >
                                <option value="">-- Pilih --</option>
                                @foreach ($positions->groupBy(fn($p) => $p->batch->name ?? 'Tanpa Batch') as $batchName => $batchPositions)
                                    <optgroup label="{{ $batchName }}">
                                        @foreach ($batchPositions as $pos)
                                            <option
                                                value="{{ $pos->id }}"
                                                data-batch="{{ $batchName }}"
                                                data-pos-name="{{ $pos->name }}"
                                            >
                                                {{ $pos->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="schedule_start" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">End <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="schedule_end" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Meeting Link (Zoom/Meet) <span class="text-danger">*</span></label>
                            <input type="url" name="zoom_link" class="form-control" placeholder="https://..." required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meeting ID</label>
                                <input type="text" name="zoom_id" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Passcode</label>
                                <input type="text" name="zoom_passcode" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var sm = new bootstrap.Modal(document.getElementById('successModal'));
                sm.show();
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function applyBatchLabel(selectEl) {
                if (!selectEl) return;

                const options = Array.from(selectEl.options);

                // reset semua option ke text awal
                options.forEach(function (opt) {
                    // skip placeholder
                    if (!opt.value) return;

                    const original = opt.getAttribute('data-original-text');
                    if (original) {
                        opt.textContent = original;
                    } else {
                        opt.setAttribute('data-original-text', opt.textContent.trim());
                    }
                });

                const selected = selectEl.options[selectEl.selectedIndex];
                if (!selected || !selected.value) return;

                const batch = selected.getAttribute('data-batch') || '';
                const originalText = selected.getAttribute('data-original-text') || selected.textContent.trim();

                if (batch && originalText) {
                    selected.textContent = batch + ' - ' + originalText;
                }
            }

            document.querySelectorAll('.position-select').forEach(function (selectEl) {
                // initial (buat modal edit biar langsung rapi)
                applyBatchLabel(selectEl);

                selectEl.addEventListener('change', function () {
                    applyBatchLabel(this);
                });
            });
        });
    </script>
</x-app-admin>
