<!-- resources/views/components/admin/filter.blade.php -->
@props([
    'action' => '#',
    'method' => 'GET',
    'resetRoute' => null,
    'collapsed' => true
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm mb-6']) }} 
    x-data="{ open: !{{ $collapsed ? 'true' : 'false' }} }">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Filters
            </h3>
            <button type="button" @click="open = !open" class="ml-2 inline-flex items-center p-1 text-sm font-medium text-gray-700 bg-white rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:bg-neutral-800 dark:hover:bg-neutral-700">
                <span x-show="!open">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
                <span x-show="open">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </span>
            </button>
        </div>

        <div class="flex items-center gap-2">
            @if($resetRoute)
            <a href="{{ $resetRoute }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-800">
                <svg class="-ml-0.5 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
            </a>
            @endif
        </div>
    </div>
    
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <form action="{{ $action }}" method="{{ $method }}" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {{ $slot }}
            </div>
            
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-neutral-700">
                @if($resetRoute)
                <a href="{{ $resetRoute }}" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:focus:ring-offset-neutral-800">
                    Clear Filters
                </a>
                @endif
                <button type="submit" class="inline-flex justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-neutral-800">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>