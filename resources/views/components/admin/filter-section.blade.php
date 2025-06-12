{{-- resources/views/components/admin/filter-section.blade.php --}}
@props([
    'action',
    'searchValue' => null,
    'searchPlaceholder' => 'Search...',
    'filters' => [],
    'hasActiveFilters' => false,
    'clearFiltersRoute' => null
])

<x-admin.card class="mb-6">
    <form method="GET" action="{{ $action }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-end sm:gap-4">
        <!-- Search -->
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                Search
            </label>
            <input type="text" 
                   name="search" 
                   id="search" 
                   value="{{ $searchValue }}"
                   placeholder="{{ $searchPlaceholder }}"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
        </div>

        <!-- Dynamic Filters -->
        @foreach($filters as $filter)
            <div class="w-full sm:w-48">
                <label for="{{ $filter['name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    {{ $filter['label'] }}
                </label>
                <select name="{{ $filter['name'] }}" 
                        id="{{ $filter['name'] }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <option value="">{{ $filter['allLabel'] ?? 'All' }}</option>
                    @foreach($filter['options'] as $value => $label)
                        <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endforeach

        <!-- Filter Actions -->
        <div class="flex gap-2">
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filter
            </button>

            @if($hasActiveFilters && $clearFiltersRoute)
                <a href="{{ $clearFiltersRoute }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    Clear
                </a>
            @endif
        </div>
    </form>
</x-admin.card>