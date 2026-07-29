@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-semibold text-white transition duration-150 ease-in-out'
            : 'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out';
$style = ($active ?? false) ? 'background:#16233A;' : '';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $style]) }}>
    {{ $slot }}
</a>