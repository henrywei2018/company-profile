<!-- resources/views/components/admin/list-item.blade.php -->
@props([
    'hover' => true,
    'striped' => false,
    'active' => false,
    'disabled' => false,
    'href' => null
])

@php
    $hoverClass = $hover ? 'hover:bg-gray-50 dark:hover:bg-neutral-700' : '';
    $stripedClass = $striped ? 'odd:bg-white even:bg-gray-50 dark:odd:bg-neutral-800 dark:even:bg-neutral-750' : '';
    $activeClass = $active ? 'bg-blue-50 dark:bg-blue-900/20' : '';
    $disabledClass = $disabled ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<li class="relative {{ $hoverClass }} {{ $stripedClass }} {{ $activeClass }} {{ $disabledClass }}">
    @if($href && !$disabled)
        <a href="{{ $href }}" class="block p-4 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 dark:focus:ring-blue-500">
            {{ $slot }}
        </a>
    @else
        <div class="p-4">
            {{ $slot }}
        </div>
    @endif
</li>