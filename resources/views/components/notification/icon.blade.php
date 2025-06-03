{{-- resources/views/components/notification/icon.blade.php --}}
@props([
    'type' => 'bell',
    'color' => 'gray',
    'size' => 'sm'
])

@php
    $sizeClass = match($size) {
        'xs' => 'size-3',
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6',
        default => 'size-4'
    };
    
    $colorClass = match($color) {
        'red' => 'text-red-500',
        'green' => 'text-green-500',
        'blue' => 'text-blue-500',
        'yellow' => 'text-yellow-500',
        'purple' => 'text-purple-500',
        'gray' => 'text-gray-500',
        'orange' => 'text-orange-500',
        'indigo' => 'text-indigo-500',
        default => 'text-gray-500'
    };
    
    $bgColorClass = match($color) {
        'red' => 'bg-red-100 dark:bg-red-900/30',
        'green' => 'bg-green-100 dark:bg-green-900/30',
        'blue' => 'bg-blue-100 dark:bg-blue-900/30',
        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30',
        'purple' => 'bg-purple-100 dark:bg-purple-900/30',
        'gray' => 'bg-gray-100 dark:bg-gray-700',
        'orange' => 'bg-orange-100 dark:bg-orange-900/30',
        'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30',
        default => 'bg-gray-100 dark:bg-gray-700'
    };
@endphp

<div class="flex items-center justify-center w-8 h-8 {{ $bgColorClass }} rounded-full">
    @switch($type)
        @case('folder')
        @case('project')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            @break

        @case('document-text')
        @case('quotation')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            @break

        @case('mail')
        @case('message')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            @break

        @case('chat')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            @break

        @case('user')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            @break

        @case('cog')
        @case('system')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            @break

        @case('star')
        @case('testimonial')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            @break

        @case('exclamation-triangle')
        @case('warning')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            @break

        @default
        @case('bell')
            <svg class="{{ $sizeClass }} {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3"/>
            </svg>
    @endswitch
</div>