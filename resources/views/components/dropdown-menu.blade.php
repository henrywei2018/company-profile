<!-- resources/views/components/dropdown-menu.blade.php -->
@props(['width' => 'w-40', 'align' => 'right'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'left-0 origin-top-left',
        'top' => 'origin-top',
        'right' => 'right-0 origin-top-right',
        default => 'right-0 origin-top-right',
    };
@endphp

<div class="hs-dropdown relative inline-flex">
    <button id="actions-dropdown-toggle" type="button" class="hs-dropdown-toggle py-1.5 px-2 inline-flex justify-center items-center gap-2 rounded-md text-gray-700 align-middle focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
        </svg>
    </button>

    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden z-10 mt-2 {{ $width }} bg-white shadow-md rounded-lg p-2 dark:bg-gray-800 dark:border dark:border-gray-700 {{ $alignmentClasses }}" aria-labelledby="actions-dropdown-toggle">
        {{ $slot }}
    </div>
</div>