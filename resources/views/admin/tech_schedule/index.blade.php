<x-app-admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Penjadwalan Technical Test') }}
            </h2>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateSchedule">
                Create New Schedule
            </a>
        </div>

        {{-- Success modal (seperti di Quiz) --}}
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <h5 class="text-success">✅ {{ session('success') }}</h5>
                        <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Errors --}}
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

                    {{-- List schedules (mirip list-group di Quiz) --}}
                    <div class="list-group">

                        @forelse ($schedules as $sch)
                            <div
                                class="list-group-item list-group-item-action d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2">

                                <div class="me-md-3">
                                    <h5 class="mb-1 fw-bold">{{ $sch->position->name ?? '—' }}</h5>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar-range"></i>
                                        {{ optional($sch->schedule_date)->translatedFormat('d F Y H:i') }}
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-link-45deg"></i>
                                        <a href="{{ $sch->zoom_link }}" target="_blank">
                                            {{ \Illuminate\Support\Str::limit($sch->zoom_link, 60) }}
                                        </a>
                                        @if ($sch->zoom_id || $sch->zoom_passcode)
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-shield-lock"></i>
                                            ID: {{ $sch->zoom_id ?? '—' }}, Pwd: {{ $sch->zoom_passcode ?? '—' }}
                                        @endif
                                        @if ($sch->upload_deadline)
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-hourglass-split"></i>
                                            Deadline:
                                            {{ optional($sch->upload_deadline)->translatedFormat('d F Y H:i') }}
                                        @endif
                                    </div>
                                    @if ($sch->keterangan)
                                        <div class="mt-1 text-secondary small">
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

                                    <form action="{{ route('tech-schedule.destroy', $sch) }}" method="post"
                                        class="d-inline" onsubmit="return confirm('Hapus schedule ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal Edit (per item, simple seperti Quiz) --}}
                            <div class="modal fade" id="editSchedule{{ $sch->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5">Edit Schedule</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form method="post" action="{{ route('tech-schedule.update', $sch) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Posisi</label>
                                                    <select name="position_id" class="form-select" required>
                                                        @foreach ($positions as $pos)
                                                            <option value="{{ $pos->id }}"
                                                                @selected($pos->id == $sch->position_id)>
                                                                {{ $pos->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Schedule Date</label>
                                                    <input type="datetime-local" name="schedule_date"
                                                        class="form-control"
                                                        value="{{ optional($sch->schedule_date)?->format('Y-m-d\TH:i') }}"
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Upload Deadline</label>
                                                    <input type="datetime-local" name="upload_deadline"
                                                        class="form-control"
                                                        value="{{ optional($sch->upload_deadline)?->format('Y-m-d\TH:i') }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Zoom Link</label>
                                                    <input type="url" name="zoom_link" class="form-control"
                                                        value="{{ $sch->zoom_link }}" required>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Zoom ID</label>
                                                        <input type="text" name="zoom_id" class="form-control"
                                                            value="{{ $sch->zoom_id }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Zoom Passcode</label>
                                                        <input type="text" name="zoom_passcode"
                                                            class="form-control" value="{{ $sch->zoom_passcode }}">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Note / Keterangan</label>
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
                                <p class="text-center text-muted my-3">Belum ada Schedule.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if ($schedules->hasPages())
                        <div class="mt-3">
                            {{ $schedules->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create Schedule --}}
    <div class="modal fade" id="modalCreateSchedule" tabindex="-1" aria-labelledby="modalCreateScheduleLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalCreateScheduleLabel">Create Schedule</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="post" action="{{ route('tech-schedule.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Posisi</label>
                            <select name="position_id" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Schedule Date</label>
                            <input type="datetime-local" name="schedule_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Deadline</label>
                            <input type="datetime-local" name="upload_deadline" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Zoom Link</label>
                            <input type="url" name="zoom_link" class="form-control" placeholder="https://..."
                                required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Zoom ID</label>
                                <input type="text" name="zoom_id" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Zoom Passcode</label>
                                <input type="text" name="zoom_passcode" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note / Keterangan</label>
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

    {{-- Auto show success modal (seperti Quiz) --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var sm = new bootstrap.Modal(document.getElementById('successModal'));
                sm.show();
            });
        </script>
    @endif
</x-app-admin>
