{{-- resources/views/history.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Lamaran Saya') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                {{-- Loop untuk setiap lamaran --}}
                @forelse ($applicants as $applicant)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                        <!-- Bagian Header Kartu Lamaran -->
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 pb-4 border-b">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $applicant->position->name ?? 'Posisi Dihapus' }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Dilamar pada: {{ $applicant->created_at->format('l, d F Y') }}
                                </p>
                            </div>
                            <div class="mt-3 sm:mt-0">
                                @php
                                    $status = $applicant->status;
                                    $badgeColor = '';
                                    if (str_contains($status, 'Lolos')) {
                                        $badgeColor = 'bg-green-100 text-green-800';
                                    } elseif (str_contains($status, 'Tidak Lolos')) {
                                        $badgeColor = 'bg-red-100 text-red-800';
                                    } else {
                                        $badgeColor = 'bg-blue-100 text-blue-800';
                                    }
                                @endphp
                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $badgeColor }}">
                                    {{ $status }}
                                </span>
                            </div>
                        </div>

                        <!-- Bagian Progress Tracker -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-700 mb-4">Progres Seleksi</h4>
                            <div class="flex items-center">
                                @php
                                    $stages = ['Seleksi Administrasi', 'Seleksi Tes Tulis'];

                                    $currentStageIndex = -1;
                                    $isFailed = str_contains($status, 'Tidak Lolos');
                                    $failedStageIndex = -1;

                                    if ($isFailed) {
                                        foreach ($stages as $index => $stageName) {
                                            if (str_contains($status, $stageName)) {
                                                $failedStageIndex = $index;
                                                break;
                                            }
                                        }
                                    } else {
                                        foreach ($stages as $index => $stageName) {
                                            if (str_contains($status, $stageName)) {
                                                $currentStageIndex = $index;
                                                break;
                                            }
                                        }
                                        // Jika sudah lolos tahap terakhir, anggap semua selesai
                                        if ($status == 'Lolos Seleksi Tes Tulis') {
                                            $currentStageIndex = count($stages);
                                        }
                                    }
                                @endphp

                                <!-- Looping untuk setiap tahap seleksi -->
                                @foreach ($stages as $index => $stageName)
                                    @php
                                        $isCompleted = !$isFailed && $index < $currentStageIndex;
                                        $isCurrent = !$isFailed && $index == $currentStageIndex;
                                        $isUpcoming = !$isFailed && $index > $currentStageIndex;
                                        $isStageFailed = $isFailed && $index == $failedStageIndex;
                                    @endphp

                                    <!-- Step Item -->
                                    <div class="flex flex-col items-center">
                                        <!-- Icon -->
                                        <div
                                            class="relative flex items-center justify-center w-10 h-10 rounded-full
                                            @if ($isCompleted) bg-green-500 text-white
                                            @elseif($isCurrent) bg-blue-500 text-white ring-4 ring-blue-200
                                            @elseif($isStageFailed) bg-red-500 text-white
                                            @else bg-gray-200 text-gray-500 @endif">

                                            @if ($isCompleted)
                                                <!-- Icon Centang -->
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @elseif($isStageFailed)
                                                <!-- Icon Silang -->
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @else
                                                <!-- Nomor Tahap -->
                                                <span>{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <!-- Label -->
                                        <p
                                            class="text-center text-xs mt-2 w-24 
                                            @if ($isCompleted) text-gray-600
                                            @elseif($isCurrent) text-blue-600 font-semibold
                                            @elseif($isStageFailed) text-red-600 font-semibold
                                            @else text-gray-400 @endif">
                                            {{ $stageName }}
                                        </p>
                                    </div>

                                    <!-- Garis Penghubung (kecuali untuk item terakhir) -->
                                    @if (!$loop->last)
                                        <div
                                            class="flex-auto border-t-2 
                                            @if ($isCompleted) border-green-500
                                            @elseif($isCurrent || $isUpcoming || $isStageFailed) border-gray-200
                                            @else border-gray-200 @endif">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
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
