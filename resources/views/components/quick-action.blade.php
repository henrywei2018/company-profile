<!-- resources/views/components/quick-action.blade.php -->
@props(['title', 'description', 'icon', 'href', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-400 dark:bg-blue-800/30 dark:hover:bg-blue-800/40',
        'green' => 'text-green-700 bg-green-100 hover:bg-green-200 dark:text-green-400 dark:bg-green-800/30 dark:hover:bg-green-800/40',
        'amber' => 'text-amber-700 bg-amber-100 hover:bg-amber-200 dark:text-amber-400 dark:bg-amber-800/30 dark:hover:bg-amber-800/40',
        'red' => 'text-red-700 bg-red-100 hover:bg-red-200 dark:text-red-400 dark:bg-red-800/30 dark:hover:bg-red-800/40',
        'purple' => 'text-purple-700 bg-purple-100 hover:bg-purple-200 dark:text-purple-400 dark:bg-purple-800/30 dark:hover:bg-purple-800/40',
        'gray' => 'text-gray-700 bg-gray-100 hover:bg-gray-200 dark:text-gray-400 dark:bg-gray-800/30 dark:hover:bg-gray-800/40',
    ];
@endphp

<a href="{{ $href }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm transition hover:shadow-md">
    <div class="flex flex-col items-center text-center">
        <div class="{{ $colorClasses[$color] }} rounded-lg p-3 mb-4">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $icon !!}
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">{{ $title }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    </div>
</a>