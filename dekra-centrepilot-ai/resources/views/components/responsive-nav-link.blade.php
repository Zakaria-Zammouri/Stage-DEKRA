@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-2 w-full px-4 py-2.5 rounded-lg text-start text-base font-semibold text-white transition duration-150 ease-in-out'
            : 'flex items-center gap-2 w-full px-4 py-2.5 rounded-lg text-start text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-white transition duration-150 ease-in-out';
$style = ($active ?? false) ? 'background:#16233A;' : '';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $style]) }}>
    {{ $slot }}
</a>