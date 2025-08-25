<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Application') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                {{-- Loop untuk setiap lamaran --}}
                @forelse ($applicants as $applicant)
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 border border-gray-200">
                        <!-- Bagian Header Kartu Lamaran -->
                        <div
                            class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $applicant->position->name ?? 'Posisi Dihapus' }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Dilamar pada: {{ $applicant->created_at->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
                            <div class="mt-3 sm:mt-0 flex items-center">
                                @php
                                    $status = $applicant->status;
                                    $badgeColor = '';

                                    switch ($status) {
                                        // Status berhasil atau lolos
                                        // case 'Offering':
                                        case 'Menerima Offering':
                                        case 'Lolos Seleksi Administrasi': // Perlu diubah agar sesuai dengan status DB
                                        case 'Lolos Tes Tulis': // Perlu diubah agar sesuai dengan status DB
                                        case 'Lolos Technical Test':
                                        case 'Lolos Interview':
                                            $badgeColor = 'bg-green-100 text-green-800';
                                            break;
                                        // Status gagal atau tidak lolos
                                        case 'Tidak Lolos Seleksi Administrasi':
                                        case 'Tidak Lolos Tes Tulis':
                                        case 'Tidak Lolos Technical Test':
                                        case 'Tidak Lolos Interview':
                                        case 'Menolak Offering':
                                            $badgeColor = 'bg-red-100 text-red-800';
                                            break;
                                        // Status proses
                                        default:
                                            $badgeColor = 'bg-blue-100 text-blue-800';
                                            break;
                                    }
                                @endphp
                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $badgeColor }}">
                                    {{ $status }}
                                </span>
                            </div>
                        </div>

                        <!-- Bagian Progress Tracker -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-700 mb-4">PROSES SELEKSI</h4>
                            <div class="flex items-center justify-between">
                                @php
                                    // Peta status database ke stage progress tracker dan hasilnya
                                    $stageMapping = [
                                        'Seleksi Administrasi' => ['index' => 0, 'result' => 'pending'],
                                        'Tidak Lolos Seleksi Administrasi' => ['index' => 0, 'result' => 'failed'],
                                        'Tes Tulis' => ['index' => 1, 'result' => 'pending'],
                                        'Tidak Lolos Tes Tulis' => ['index' => 1, 'result' => 'failed'],
                                        'Technical Test' => ['index' => 2, 'result' => 'pending'],
                                        'Tidak Lolos Technical Test' => ['index' => 2, 'result' => 'failed'],
                                        'Interview' => ['index' => 3, 'result' => 'pending'],
                                        'Tidak Lolos Interview' => ['index' => 3, 'result' => 'failed'],
                                        'Offering' => ['index' => 4, 'result' => 'pending'],
                                        'Menolak Offering' => ['index' => 4, 'result' => 'failed'],
                                        'Menerima Offering' => ['index' => 4, 'result' => 'passed'],
                                    ];

                                    // Mengambil informasi status saat ini
                                    $currentStatusInfo = $stageMapping[$applicant->status] ?? [
                                        'index' => -1,
                                        'result' => 'unknown',
                                    ];
                                    $currentStageIndex = $currentStatusInfo['index'];
                                    $currentResult = $currentStatusInfo['result'];

                                    // Daftar tahapan seleksi untuk progress tracker
                                    $stages = [
                                        'Seleksi Administrasi',
                                        'Tes Tulis',
                                        'Technical Test',
                                        'Interview',
                                        'Offering',
                                    ];
                                @endphp

                                <!-- Looping untuk setiap tahap seleksi -->
                                @foreach ($stages as $index => $stageName)
                                    @php
                                        // Menentukan kondisi untuk styling
                                        $isPassed =
                                            $index < $currentStageIndex ||
                                            ($index == $currentStageIndex && $currentResult == 'passed');
                                        $isCurrent = $index == $currentStageIndex && $currentResult == 'pending';
                                        $isFailed = $index == $currentStageIndex && $currentResult == 'failed';
                                        $isActive = $isPassed || $isCurrent;
                                    @endphp

                                    <!-- Step Item -->
                                    <div class="flex flex-col items-center flex-1 min-w-0">
                                        <!-- Icon -->
                                        <div
                                            class="relative flex items-center justify-center w-10 h-10 rounded-full
                                            @if ($isFailed) bg-red-500 text-white
                                            @elseif ($isPassed) bg-green-500 text-white
                                            @elseif ($isCurrent) bg-blue-500 text-white ring-4 ring-blue-200
                                            @else bg-gray-200 text-gray-500 @endif">

                                            @if ($isFailed)
                                                <!-- Icon Silang -->
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @elseif ($isPassed)
                                                <!-- Icon Centang -->
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @else
                                                <!-- Nomor Tahap -->
                                                <span>{{ $index + 1 }}</span>
                                            @endif
                                        </div>

                                        <!-- Label -->
                                        <p
                                            class="text-center text-xs mt-2 w-24 whitespace-normal
                                            @if ($isFailed) text-red-600 font-semibold
                                            @elseif ($isPassed) text-gray-600
                                            @elseif ($isCurrent) text-blue-600 font-semibold
                                            @else text-gray-400 @endif">
                                            {{ $stageName }}
                                        </p>
                                    </div>

                                    <!-- Garis Penghubung (kecuali untuk item terakhir) -->
                                    @if (!$loop->last)
                                        <div
                                            class="flex-auto border-t-2
                                            @if ($isActive) border-green-500
                                            @else border-gray-200 @endif">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Bagian Tambahan untuk Tes Tulis -->
                        @if ($applicant->status === 'Tes Tulis' && $applicant->position && $applicant->position->test)
                            <div class="mt-4 pt-4 border-t border-gray-100 text-center space-y-4">
                                <p class="text-sm text-gray-600">Tes akan dimulai pada <span
                                        class="font-semibold text-blue-600">
                                        {{ \Carbon\Carbon::parse($applicant->position->test->test_date)->translatedFormat('l, d F Y, H:i') }}
                                    </span>
                                </p>
                                <a href="{{ route('quiz.start', ['slug' => $applicant->position->test->slug]) }}"
                                    class="inline-block px-6 py-2 bg-blue-600 text-white font-medium text-sm leading-tight rounded-full shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 transition duration-150 ease-in-out">
                                    Mulai Tes
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    {{-- Tampilan jika tidak ada lamaran --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 text-center">
                            <p>Anda belum pernah melamar pekerjaan apapun.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>


{{-- {{ route('test.start', ['slug' => $applicant->position->test->slug]) }} --}}
