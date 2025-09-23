{{-- resources/views/admin/quiz-results/index.blade.php --}}
<x-app-admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Quiz
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-200">
                <div class="table-responsive">
                    <table class="table w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2 text-left">NAMA</th>
                                <th class="py-2 text-left">POSISI</th>

                                {{-- Kolom dinamis: Section 1..N --}}
                                @for ($i = 1; $i <= $maxSections; $i++)
                                    <th class="py-2 text-left">SECTION {{ $i }}</th>
                                @endfor

                                <th class="py-2 text-left">NILAI TOTAL</th>
                                <th class="py-2 text-left">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $r)
                                <tr class="border-b align-top">
                                    <td class="py-2">
                                        {{ $r->applicant->name ?? '—' }}
                                    </td>
                                    <td class="py-2">
                                        {{ optional($r->applicant->position)->name ?? (optional($r->test->position)->name ?? '—') }}
                                    </td>

                                    {{-- Nilai per section sesuai urutan --}}
                                    @php
                                        $ordered = $r->orderedSectionResults ?? collect();
                                    @endphp
                                    @for ($i = 0; $i < $maxSections; $i++)
                                        @php
                                            $sr = $ordered->get($i);
                                            $val = $sr ? (is_null($sr->score) ? '—' : $sr->score) : '—';
                                        @endphp
                                        <td class="py-2">{{ $val }}</td>
                                    @endfor

                                    <td class="py-2">{{ $r->score ?? ($r->sectionResults->sum('score') ?? '—') }}</td>
                                    <td class="py-2">
                                        <a class="text-blue-600 hover:underline"
                                            href="{{ route('quiz_results.show', $r->id) }}">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-4 text-center" colspan="{{ 4 + max(1, $maxSections) }}">Belum ada
                                        hasil.</td>
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
</x-app-admin>
