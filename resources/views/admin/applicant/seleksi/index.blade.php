<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Selection Stages') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mt-4">
        <h3 class="text-center mb-4">UPDATE SELEKSI TAD</h3>

        <!-- Table Section for Selection Stages -->
        <div class="accordion" id="selectionAccordion">
            @php
                $stages = [
                    'Administrasi' => 'Seleksi Administrasi',
                    'Tes Tulis' => 'Seleksi Tes Tulis',
                    'Technical Test' => 'Seleksi Technical Test',
                    'Interview' => 'Seleksi Interview',
                ];
            @endphp

            @foreach ($stages as $label => $statusName)
                @php
                    $passedCount = \App\Models\Applicant::where('status', 'Lolos ' . $statusName)->count();
                    $failedCount = \App\Models\Applicant::where('status', 'Tidak Lolos ' . $statusName)->count();
                @endphp

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading_{{ Str::slug($label) }}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse_{{ Str::slug($label) }}" aria-expanded="true"
                            aria-controls="collapse_{{ Str::slug($label) }}">
                            {{ $label }} -
                            <span class="me-4 text-success">Jumlah Peserta Lolos: {{ $passedCount }}</span>
                            <span class="text-danger">Jumlah Peserta Tidak Lolos: {{ $failedCount }}</span>
                        </button>
                    </h2>
                    <div id="collapse_{{ Str::slug($label) }}" class="accordion-collapse collapse"
                        aria-labelledby="heading_{{ Str::slug($label) }}" data-bs-parent="#selectionAccordion">
                        <div class="accordion-body">
                            <a href="{{ route('admin.applicant.seleksi.process', ['stage' => $statusName]) }}" class="btn btn-primary">
                                Proses
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>
</x-app-layout>