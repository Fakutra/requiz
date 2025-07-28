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
        <table class="table table-hover table-sm align-middle">
            <thead class="table-light border-bottom">
                <tr>
                    <th></th>
                    <th>Jumlah Peserta Lolos</th>
                    <th>Jumlah Peserta Tidak Lolos</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
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
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="text-success">{{ $passedCount }}</td>
                        <td class="text-danger">{{ $failedCount }}</td>
                        <td>
                            <a href="{{ route('admin.applicant.seleksi.process', ['stage' => $statusName]) }}" class="btn btn-primary btn-sm">
                                Proses
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-app-layout>