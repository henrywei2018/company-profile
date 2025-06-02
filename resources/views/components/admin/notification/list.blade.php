@props([
    'notifications',
    'variant' => 'default',
    'showPagination' => true,
    'showFilters' => false,
    'showBulkActions' => false
])

<div class="bg-white dark:bg-neutral-800 shadow rounded-lg">
    @if($showFilters)
        <x-admin.notification.filters :variant="$variant" />
    @endif

    @if($showBulkActions && $notifications->count() > 0)
        <x-admin.notification.bulk-actions :variant="$variant" />
    @endif

    <div class="divide-y divide-gray-200 dark:divide-neutral-700">
        @forelse($notifications as $notification)
            <x-admin.notification.item 
                :notification="$notification" 
                :variant="$variant"
                :show-actions="true"
                class="px-6 py-4"
            />
        @empty
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-5 5-5-5h5V8a1 1 0 011-1h3a1 1 0 011 1v9z"/>
                </svg>
                <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No notifications</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">You're all caught up! No new notifications at this time.</p>
            </div>
        @endforelse
    </div>

    @if($showPagination && method_exists($notifications, 'links'))
        <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
            {{ $notifications->links() }}
        </div>
    @endif
</div>