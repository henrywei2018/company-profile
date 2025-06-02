@props([
    'notifications' => [],
    'unreadCount' => 0,
    'maxDisplay' => 5,
    'showAll' => true,
    'variant' => 'default'
])

<div
    x-data="{
        notifications: @js($notifications),
        unreadCount: {{ $unreadCount }},
        markAllUrl: '{{ $variant === 'admin' ? route('admin.notifications.mark-all-as-read') : route('client.notifications.mark-all-read') }}',
        async markAllRead() {
            try {
                const res = await fetch(this.markAllUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });

                const data = await res.json();
                if (data.success) {
                    this.unreadCount = 0;
                    this.notifications = this.notifications.map(n => ({ ...n, read_at: new Date().toISOString() }));
                }
            } catch (err) {
                console.error(err);
            }
        }
    }"
    class="hs-dropdown relative inline-block"
    data-hs-dropdown
    data-hs-dropdown-placement="bottom-end"
>
    <!-- Trigger -->
    <button
        type="button"
        class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative"
        data-hs-dropdown-toggle
        id="notification-dropdown-{{ $variant }}"
    >
        <!-- Bell Icon -->
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
        </svg>

        <!-- Badge -->
        <template x-if="unreadCount > 0">
            <span class="absolute -top-1 -end-1 size-4 text-xs font-medium text-white bg-red-500 rounded-full flex items-center justify-center">
                <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
            </span>
        </template>
    </button>

    <!-- Dropdown Panel -->
    <div class="hs-dropdown-menu hidden z-50 mt-2 min-w-80 max-w-96 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700" 
         aria-labelledby="notification-dropdown-{{ $variant }}">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                Notifications
                <template x-if="unreadCount > 0">
                    <span class="ml-1 text-xs text-gray-500 dark:text-neutral-400">(<span x-text="unreadCount"></span> unread)</span>
                </template>
            </h3>
            <template x-if="unreadCount > 0">
                <button
                    type="button"
                    @click="markAllRead"
                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    Mark all read
                </button>
            </template>
        </div>

        <!-- List -->
        <div class="max-h-80 overflow-y-auto">
            <template x-if="notifications.length > 0">
                <template x-for="(notification, index) in notifications.slice(0, {{ $maxDisplay }})" :key="index">
                    <div class="border-b border-gray-100 dark:border-neutral-700 last:border-b-0 px-4 py-3">
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="notification.title || 'Notification'"></p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400" x-text="notification.body || 'No details'"></p>
                    </div>
                </template>
            </template>
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-5 5-5-5h5V8a1 1 0 011-1h3a1 1 0 011 1v9z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">No notifications yet</p>
                    <p class="text-xs text-gray-400 dark:text-neutral-500">We'll notify you when something arrives!</p>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <template x-if="{{ $showAll ? 'true' : 'false' }} && notifications.length > 0">
            <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700">
                <a 
                    x-bind:href="'{{ $variant === 'admin' ? route('admin.notifications.index') : route('client.notifications.index') }}'" 
                    class="block text-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
                >
                    View all notifications
                    <template x-if="notifications.length > {{ $maxDisplay }}">
                        (<span x-text="notifications.length - {{ $maxDisplay }}"></span> more)
                    </template>
                </a>
            </div>
        </template>
    </div>
</div>
