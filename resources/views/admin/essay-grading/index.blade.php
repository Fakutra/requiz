<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Penilaian Essay</h2>
    </x-slot>

    {{-- Bootstrap 5 via CDN (pastikan layout punya @stack di <head> dan sebelum </body>) --}}
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        {{-- Auto-show status modal jika ada session status --}}
        @if (session('status'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var el = document.getElementById('statusModal');
                    if (el) new bootstrap.Modal(el).show();
                });
            </script>
        @endif
    @endpush

    {{-- Modal Notifikasi Status --}}
    @if (session('status'))
        <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="mb-2">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14" />
                            <path
                                d="m10.97 4.97-.02.022L7.477 8.46 5.384 6.364l-.708.707 2.5 2.5.007.007.707-.707 3.5-3.5-.707-.707z" />
                            </svg>
                        </div>
                        <div class="fw-semibold">{{ session('status') }}</div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- HAPUS alert biasa: diganti modal di atas --}}

            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                <div class="table-responsive">
                    <table class="table w-full align-middle">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 text-left">NAMA</th>
                                <th class="py-2 text-left">TEST</th>
                                <th class="py-2 text-left">SECTION</th>
                                <th class="py-2 text-left">TOTAL ESSAY</th>
                                <th class="py-2 text-left">PENDING</th>
                                <th class="py-2 text-left">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $r)
                                @php
                                    // Kumpulkan section yang punya essay
                                    $sectionsWithEssay = $r->sectionResults->filter(
                                        fn($sr) => $sr->answers->count() > 0,
                                    );
                                    $totalEssay = $sectionsWithEssay->sum(fn($sr) => $sr->answers->count());
                                    $pendingEssay = $sectionsWithEssay->sum(
                                        fn($sr) => $sr->answers->whereNull('score')->count(),
                                    );
                                    $essayAnswers = $sectionsWithEssay->flatMap(
                                        fn($sr) => $sr->answers->map(function ($a) use ($sr) {
                                            return [
                                                'answer' => $a,
                                                'section' => optional($sr->testSection)->name ?? 'Section',
                                                'question' => optional($a->question)->question,
                                            ];
                                        }),
                                    );
                                @endphp
                                <tr class="border-b align-top">
                                    <td class="py-2">{{ $r->applicant->name ?? '—' }}</td>
                                    <td class="py-2">{{ $r->test->name ?? '—' }}</td>
                                    <td class="py-2">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse ($sectionsWithEssay as $sr)
                                                <span
                                                    class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs">
                                                    {{ optional($sr->testSection)->name ?? 'Section' }}
                                                </span>
                                            @empty
                                                —
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="py-2">{{ $totalEssay }}</td>
                                    <td class="py-2">
                                        <span
                                            class="px-2 py-0.5 rounded-full
                                            {{ $pendingEssay > 0 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis' }}">
                                            {{ $pendingEssay }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        @if ($totalEssay > 0)
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#gradeModalTR-{{ $r->id }}">
                                                Nilai Essay
                                            </button>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal nilai untuk peserta ini --}}
                                @if ($totalEssay > 0)
                                    <div class="modal fade" id="gradeModalTR-{{ $r->id }}" tabindex="-1"
                                        aria-labelledby="gradeModalLabelTR-{{ $r->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <form method="POST"
                                                    action="{{ route('essay_grading.update_result', $r->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="gradeModalLabelTR-{{ $r->id }}">
                                                            Nilai Essay — {{ $r->applicant->name ?? 'Peserta' }}
                                                            ({{ $r->test->name ?? 'Tes' }})
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        @forelse ($essayAnswers as $row)
                                                            @php
                                                                /** @var \App\Models\Answer $ans */
                                                                $ans = $row['answer'];
                                                                $qText = $row['question'];
                                                                $sectionName = $row['section'];
                                                            @endphp
                                                            <div class="border rounded-3 p-3 mb-3">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                    <span
                                                                        class="badge text-bg-secondary">{{ $sectionName }}</span>
                                                                    <span class="small text-muted">Skor saat ini:
                                                                        <strong>{{ is_null($ans->score) ? '—' : $ans->score }}</strong>
                                                                    </span>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <div class="text-muted small mb-1">Soal</div>
                                                                    <div class="fw-medium">{!! nl2br(e($qText)) !!}</div>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <div class="text-muted small mb-1">Jawaban Peserta
                                                                    </div>
                                                                    <div class="form-control"
                                                                        style="white-space: pre-line; min-height: 80px; max-height: 220px; overflow:auto;"
                                                                        readonly>
                                                                        {{ $ans->answer ?? '—' }}
                                                                    </div>
                                                                </div>

                                                                <div class="mb-2">
                                                                    <label class="form-label">Nilai</label>
                                                                    <input type="number"
                                                                        name="scores[{{ $ans->id }}]"
                                                                        min="0" max="100" step="1"
                                                                        value="{{ old('scores.' . $ans->id, $ans->score) }}"
                                                                        class="form-control w-25">
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-muted">Tidak ada jawaban essay untuk
                                                                peserta ini.</div>
                                                        @endforelse
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan
                                                            Nilai</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td class="py-4 text-center" colspan="6">Tidak ada peserta dengan essay.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $results->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
