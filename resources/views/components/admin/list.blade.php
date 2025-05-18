<!-- resources/views/components/admin/list.blade.php -->
@props([
    'bordered' => true,
    'divided' => true,
    'hover' => true,
    'striped' => false
])

@php
    $borderedClass = $bordered ? 'border border-gray-200 dark:border-neutral-700 rounded-lg overflow-hidden' : '';
    $dividedClass = $divided ? 'divide-y divide-gray-200 dark:divide-neutral-700' : '';
    $hoverClass = $hover ? 'hover:bg-gray-50 dark:hover:bg-neutral-700' : '';
    $stripedClass = $striped ? 'odd:bg-white even:bg-gray-50 dark:odd:bg-neutral-800 dark:even:bg-neutral-750' : '';
@endphp

<ul {{ $attributes->merge(['class' => $borderedClass]) }}>
    <div class="{{ $dividedClass }}">
        {{ $slot }}
    </div>
</ul>

<!-- Individual List Item Component -->
@component('components.admin.list-item', ['hover' => $hover, 'striped' => $striped])
@endcomponent