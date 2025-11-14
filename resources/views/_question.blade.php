@php
    $q = $currentQ;
    $qid = $q['id'];
    $type = $q['type'];
    $checked = $q['checked'] ?? [];
    if (!is_array($checked)) {
        $checked = strlen((string)$checked) ? [$checked] : [];
    }
@endphp

<article
    id="q-{{ $currentNo }}"
    class="mb-4 rounded-xl border border-gray-200 p-4 {{ !empty($isActive) ? '' : 'hidden' }}"
    data-question-no="{{ $currentNo }}"
    data-question-id="{{ $qid }}"
    data-question-type="{{ strtolower($type) }}"
    data-option-map='@json($q["option_map"])'
>

    <div class="mb-2 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-900">
            Nomor {{ $currentNo }} / {{ $totalQuestions ?? $total ?? '' }}
        </p>
        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
            {{ $type }}
        </span>
    </div>

    <div class="mb-3 text-gray-900">
        {!! nl2br(e($q['question'])) !!}
    </div>

    {{-- ==== Opsi PG, Poin, Pilihan Ganda ==== --}}
    @if (in_array($type, ['PG','Poin','Pilihan Ganda']))
        @foreach (['A','B','C','D','E'] as $L)
            @if (!empty($q['options'][$L] ?? null))
                <label class="flex items-start gap-2 mb-2 cursor-pointer">
                    <input
                        type="radio"
                        name="answers[{{ $qid }}]"
                        value="{{ $L }}"
                        class="mt-1"
                        @checked(in_array($L,$checked,true))
                    >
                    <span>{{ $L }}. {{ $q['options'][$L] }}</span>
                </label>
            @endif
        @endforeach

    {{-- ==== Multiple choice ==== --}}
    @elseif ($type === 'Multiple')
        @foreach (['A','B','C','D','E'] as $L)
            @if (!empty($q['options'][$L] ?? null))
                <label class="flex items-start gap-2 mb-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="answers[{{ $qid }}][]"
                        value="{{ $L }}"
                        class="mt-1"
                        @checked(in_array($L,$checked,true))
                    >
                    <span>{{ $L }}. {{ $q['options'][$L] }}</span>
                </label>
            @endif
        @endforeach

    {{-- ==== Essay ==== --}}
    @elseif ($type === 'Essay')
        <textarea
            name="answers[{{ $qid }}]"
            rows="5"
            class="w-full border p-2 rounded-lg"
        >{{ is_string($q['checked'] ?? '') ? $q['checked'] : '' }}</textarea>

    @endif

</article>
