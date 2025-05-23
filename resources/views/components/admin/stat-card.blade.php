<!-- resources/views/components/admin/stat-card.blade.php -->
@props([
    'title', 
    'value', 
    'icon', 
    'iconColor' => 'text-blue-500', 
    'iconBg' => 'bg-blue-100 dark:bg-blue-800/30', 
    'change' => null, 
    'href' => null,
    'footer' => null
])

<div class="p-4 md:p-5 min-h-24 flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    <!-- Header -->
    <div class="flex flex-wrap justify-between items-center gap-2">
        <div>
            <h2 class="text-sm text-gray-500 dark:text-neutral-400">
                {{ $title }}
            </h2>
            <p class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
                {{ $value }}
            </p>
        </div>

        <div class="flex-shrink-0 rounded-md p-3 {{ $iconBg }}">
            <svg class="h-5 w-5 {{ $iconColor }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $icon !!}
            </svg>
        </div>
    </div>
    
    @if($change !== null)
    <div class="mt-2">
        <div class="flex items-center gap-x-1">
            @if($change > 0)
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
            </svg>
            <span class="text-green-600 dark:text-green-400">{{ $change }}% increase</span>
            @elseif($change < 0)
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            <span class="text-red-600 dark:text-red-400">{{ abs($change) }}% decrease</span>
            @else
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
            </svg>
            <span class="text-gray-600 dark:text-gray-400">No change</span>
            @endif
            <span class="text-xs text-gray-500 dark:text-gray-400">from last month</span>
        </div>
    </div>
    @endif
    
    @if($footer)
    <div class="mt-auto pt-2">
        {{ $footer }}
    </div>
    @elseif($href)
    <div class="mt-auto pt-2">
        <a href="{{ $href }}" class="inline-flex items-center gap-x-1 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            View details
            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </a>
    </div>
    @endif
</div>