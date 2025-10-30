@props(['active' => false])

@php
$base = 'inline-flex items-center px-1 pt-1 text-xl font-medium transition duration-150 ease-in-out';
$classes = $active
    ? $base.' !underline underline-offset-8 font-semibold text-[#009DA9]'
    : $base.' !no-underline text-gray-600 hover:text-gray-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
