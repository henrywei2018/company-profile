{{-- resources/views/components/admin/data-table.blade.php --}}
@props([
    'headers' => [],
    'items',
    'checkboxName' => 'item_ids',
    'emptyTitle' => 'No items found',
    'emptyDescription' => 'No items to display.',
    'emptyActionText' => null,
    'emptyActionRoute' => null,
    'hasActiveFilters' => false,
    'clearFiltersRoute' => null,
    'enableSelection' => true
])

<x-admin.card>
    @if($items->count() > 0)
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        @if($enableSelection)
                            <th scope="col" class="w-8 px-6 py-3">
                                <input type="checkbox" id="select-all" 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                        @endif
                        
                        @foreach($headers as $header)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider {{ $header['class'] ?? '' }}">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                        
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {{ $emptyIcon ?? '' }}
                @if(!isset($emptyIcon))
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                @endif
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $emptyTitle }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($hasActiveFilters)
                    Try adjusting your search criteria or filters.
                @else
                    {{ $emptyDescription }}
                @endif
            </p>
            <div class="mt-6">
                @if($hasActiveFilters && $clearFiltersRoute)
                    <a href="{{ $clearFiltersRoute }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Clear filters
                    </a>
                @elseif($emptyActionRoute && $emptyActionText)
                    <a href="{{ $emptyActionRoute }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ $emptyActionText }}
                    </a>
                @endif
            </div>
        </div>
    @endif
</x-admin.card>