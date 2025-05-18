<!-- resources/views/components/admin/empty-state.blade.php -->
@props([
    'title' => 'No data available',
    'description' => 'There are no items to display at this time.',
    'icon' => null, 
    'actionText' => null,
    'actionUrl' => null,
    'secondaryActionText' => null,
    'secondaryActionUrl' => null
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8 text-center']) }}>
    @if($icon)
        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 dark:bg-neutral-800">
            {!! $icon !!}
        </div>
    @else
        <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 dark:bg-neutral-800">
            <svg class="h-8 w-8 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        </div>
    @endif

    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
        {{ $title }}
    </h3>
    
    <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400 max-w-md">
        {{ $description }}
    </p>
    
    @if($actionText)
    <div class="mt-6">
        <a href="{{ $actionUrl }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-neutral-800">
            {{ $actionText }}
        </a>
        
        @if($secondaryActionText)
        <a href="{{ $secondaryActionUrl }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600 dark:focus:ring-offset-neutral-800">
            {{ $secondaryActionText }}
        </a>
        @endif
    </div>
    @endif
</div>