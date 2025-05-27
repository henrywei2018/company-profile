<!-- resources/views/components/quick-action.blade.php -->
@props(['title', 'description', 'icon', 'href', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'text-blue-600 bg-blue-100 border-blue-200 hover:bg-blue-200 hover:border-blue-300 dark:text-blue-400 dark:bg-blue-900/30 dark:border-blue-800 dark:hover:bg-blue-900/40',
        'green' => 'text-green-600 bg-green-100 border-green-200 hover:bg-green-200 hover:border-green-300 dark:text-green-400 dark:bg-green-900/30 dark:border-green-800 dark:hover:bg-green-900/40',
        'amber' => 'text-amber-600 bg-amber-100 border-amber-200 hover:bg-amber-200 hover:border-amber-300 dark:text-amber-400 dark:bg-amber-900/30 dark:border-amber-800 dark:hover:bg-amber-900/40',
        'red' => 'text-red-600 bg-red-100 border-red-200 hover:bg-red-200 hover:border-red-300 dark:text-red-400 dark:bg-red-900/30 dark:border-red-800 dark:hover:bg-red-900/40',
        'purple' => 'text-purple-600 bg-purple-100 border-purple-200 hover:bg-purple-200 hover:border-purple-300 dark:text-purple-400 dark:bg-purple-900/30 dark:border-purple-800 dark:hover:bg-purple-900/40',
        'gray' => 'text-gray-600 bg-gray-100 border-gray-200 hover:bg-gray-200 hover:border-gray-300 dark:text-gray-400 dark:bg-gray-900/30 dark:border-gray-800 dark:hover:bg-gray-900/40',
    ];
@endphp

<a href="{{ $href }}" 
   class="group flex flex-col items-center text-center p-5 rounded-xl border {{ $colorClasses[$color] }} shadow-sm transition-all duration-200 transform hover:scale-105 hover:shadow-md">
    <div class="mb-3 mr-4 p-3 rounded-full {{ $colorClasses[$color] }} transition-all duration-200 group-hover:-translate-y-1"> 
        <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            {!! $icon !!}
        </svg>
    </div>
    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $title }}</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
</a>