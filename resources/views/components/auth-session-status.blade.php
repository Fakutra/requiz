@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-md font-medium text-center text-green-800 bg-green-100 border border-green-300 rounded-lg px-4 py-2']) }}>
        {{ $status }}
    </div>
@endif
