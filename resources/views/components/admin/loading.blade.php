<!-- resources/views/components/admin/loading.blade.php -->
@props([
    'text' => 'Loading...',
    'type' => 'spinner', // Options: spinner, dots, skeleton
    'color' => 'blue', // Options: blue, gray
    'size' => 'md' // Options: sm, md, lg
])

@php
    $sizeClasses = [
        'sm' => 'h-4 w-4',
        'md' => 'h-8 w-8',
        'lg' => 'h-12 w-12'
    ][$size] ?? 'h-8 w-8';
    
    $colorClasses = [
        'blue' => 'text-blue-600 dark:text-blue-500',
        'gray' => 'text-gray-600 dark:text-gray-400'
    ][$color] ?? 'text-blue-600 dark:text-blue-500';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-4']) }}>
    @if($type === 'spinner')
        <div class="animate-spin {{ $sizeClasses }} {{ $colorClasses }}" role="status">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    @elseif($type === 'dots')
        <div class="flex space-x-2 justify-center items-center {{ $colorClasses }}" role="status">
            <span class="animate-ping inline-flex h-2 w-2 rounded-full opacity-75 bg-current"></span>
            <span class="animate-ping inline-flex h-2 w-2 rounded-full opacity-75 delay-75 bg-current"></span>
            <span class="animate-ping inline-flex h-2 w-2 rounded-full opacity-75 delay-150 bg-current"></span>
        </div>
    @elseif($type === 'skeleton')
        <div class="animate-pulse w-full space-y-4">
            <div class="h-4 bg-gray-300 rounded dark:bg-neutral-700 w-3/4"></div>
            <div class="h-4 bg-gray-300 rounded dark:bg-neutral-700 w-1/2"></div>
            <div class="h-4 bg-gray-300 rounded dark:bg-neutral-700 w-5/6"></div>
            <div class="h-4 bg-gray-300 rounded dark:bg-neutral-700 w-1/4"></div>
            <div class="h-4 bg-gray-300 rounded dark:bg-neutral-700 w-2/3"></div>
        </div>
    @endif
    
    @if($text && $type !== 'skeleton')
        <p class="mt-2 text-sm {{ $colorClasses }}">
            {{ $text }}
        </p>
    @endif
</div>