@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 text-[30px] text-[#3BBFF4] font-medium leading-5 text-gray-900 underline underline-offset-[5px] focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 text-[30px] font-medium leading-5 text-gray-700 hover:text-indigo-500 focus:outline-none focus:text-gray-700 focus:border-gray-300 no-underline transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
<!-- font-gabarito font-normal text-[30px] leading-[100%] tracking-[0] underline underline-offset-[0px] decoration-[0px] decoration-solid -->