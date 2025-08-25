{{-- resources/views/admin/quiz-results/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Quiz Pelamar
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                <div class="table-responsive">
                    <table class="table w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 text-left">Pelamar</th>
                                <th class="py-2 text-left">Posisi</th>
                                <th class="py-2 text-left">Nama Tes</th>
                                <th class="py-2 text-left">Mulai</th>
                                <th class="py-2 text-left">Selesai</th>
                                <th class="py-2 text-left">Skor Total</th>
                                <th class="py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $r)
                                <tr class="border-b">
                                    <td class="py-2">
                                        {{ $r->applicant->name ?? '—' }}
                                    </td>
                                    <td class="py-2">
                                        {{ optional($r->applicant->position)->name ?? (optional($r->test->position)->name ?? '—') }}
                                    </td>
                                    <td class="py-2">{{ $r->test->name ?? '—' }}</td>
                                    <td class="py-2">{{ optional($r->started_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="py-2">{{ optional($r->finished_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="py-2">{{ $r->score ?? '—' }}</td>
                                    <td class="py-2">
                                        <a class="text-blue-600 hover:underline"
                                            href="{{ route('quiz_results.show', $r->id) }}">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-4 text-center" colspan="7">Belum ada hasil.</td>
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
