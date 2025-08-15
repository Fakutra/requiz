<x-app-admin>
    <h1 class="text-2xl font-bold text-blue-950 mb-6">Rekapitulasi Seleksi TAD Per Tahap</h1>
    <div class="bg-white shadow-zinc-400/50 rounded-lg p-6">
        <!-- Table Section for Selection Stages -->
        <table class="min-w-full">
            <thead class="bg-gray-100 text-left text-sm font-medium text-gray-700">
                <tr>
                    <th class="px-4 py-2"></th>
                    <th class="px-4 py-2">Jumlah Peserta Lolos</th>
                    <th class="px-4 py-2">Jumlah Peserta Tidak Lolos</th>
                    <th class="px-4 py-2">Aksi</th>
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
                    <td class="px-4 py-2">{{ $label }}</td>
                    <td class="text-green-500 px-4 py-2">{{ $passedCount }}</td>
                    <td class="text-red-600 px-4 py-2">{{ $failedCount }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.applicant.seleksi.process', ['stage' => $statusName]) }}" class="text-blue-400 flex gap-2 items-center hover:text-underline">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            Lihat Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-admin>