@props(['active' => false, 'href' => '#', 'class' => ''])

@php
$classes = ($active ?? false)
    ? 'border-amber-600 text-amber-600 dark:text-amber-400 border-b-2 font-semibold ' . $class
    : 'border-transparent text-gray-700 hover:text-amber-600 dark:text-gray-300 dark:hover:text-amber-400 hover:border-gray-300 border-b-2 ' . $class;
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes . ' px-3 py-2 transition']) }}>
    {{ $slot }}
</a>
