@if ($currentQ)
<article id="q-{{ $currentNo }}"
    class="mb-4 rounded-xl border border-gray-200 p-4"
    data-question-id="{{ $currentQ['id'] }}"
    data-question-type="{{ $currentQ['type'] }}"
    data-option-map='@json($currentQ["option_map"])'>

    <div class="mb-2 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-900">
            Nomor {{ $currentNo }} / {{ $totalQuestions ?? $total ?? '' }}
        </p>
        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
            {{ $currentQ['type'] }}
        </span>
    </div>

    <div class="mb-3 text-gray-900">{!! nl2br(e($currentQ['question'])) !!}</div>

    @if (in_array($currentQ['type'], ['PG','Poin','Pilihan Ganda']))
    @foreach (['A','B','C','D','E'] as $L)
    @if (!empty($currentQ['options'][$L] ?? null))
    <label class="block mb-2">
        <input type="radio" name="answers[{{ $currentQ['id'] }}]" value="{{ $L }}"
            {{ in_array($L,(array)($currentQ['checked']??[]),true) ? 'checked' : '' }}>
        {{ $L }}. {{ $currentQ['options'][$L] }}
    </label>
    @endif
    @endforeach
    @elseif ($currentQ['type'] === 'Multiple')
    @foreach (['A','B','C','D','E'] as $L)
    @if (!empty($currentQ['options'][$L] ?? null))
    <label class="block mb-2">
        <input type="checkbox" name="answers[{{ $currentQ['id'] }}][]" value="{{ $L }}"
            {{ in_array($L,(array)($currentQ['checked']??[]),true) ? 'checked' : '' }}>
        {{ $L }}. {{ $currentQ['options'][$L] }}
    </label>
    @endif
    @endforeach
    @elseif ($currentQ['type'] === 'Essay')
    <textarea name="answers[{{ $currentQ['id'] }}]" rows="5" class="w-full border p-2">
    {{ is_string($currentQ['checked']??'')?$currentQ['checked']:'' }}
    </textarea>
    @endif
</article>
@endif