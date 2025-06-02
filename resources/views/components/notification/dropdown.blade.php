{{-- resources/views/components/admin/notification/dropdown.blade.php --}}
@props([
    'notifications' => collect(),
    'unreadCount' => 0,
    'variant' => 'admin', // admin or client
    'maxDisplay' => 10,
    'showAll' => false
])

<div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
    <!-- Notification Toggle Button -->
    <button type="button"
        class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative"
        data-hs-dropdown-toggle 
        id="notification-dropdown-toggle">
        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-blue-500 rounded-full animate-pulse" 
                  id="notification-badge">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
        <span class="sr-only">Notifications ({{ $unreadCount }} unread)</span>
    </button>

    <!-- Dropdown Panel -->
    <div class="hs-dropdown-menu hidden z-50 mt-2 w-96 bg-white shadow-xl rounded-lg border dark:bg-neutral-800 dark:border-neutral-700 max-h-[32rem] flex flex-col"
        aria-labelledby="notification-dropdown-toggle">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-medium text-gray-800 dark:text-white">Notifications</h3>
                @if($unreadCount > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                        {{ $unreadCount }} new
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                <button type="button" 
                    onclick="markAllNotificationsAsRead('{{ $variant }}')"
                    class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    Mark all read
                </button>
                @endif
                <a href="{{ $variant === 'admin' ? route('admin.notifications.index') : route('client.notifications.index') }}" 
                   class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium">
                    View all
                </a>
            </div>
        </div>

        <!-- Loading State -->
        <div id="notification-loading" class="hidden px-4 py-6 text-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Loading notifications...</p>
        </div>

        <!-- Notification List -->
        <div class="flex-1 overflow-y-auto" id="notification-list">
            @forelse($notifications->take($maxDisplay) as $notification)
                <x-notification.item 
                    :notification="$notification" 
                    :variant="$variant" 
                />
            @empty
                <x-notification.empty-state />
            @endforelse
        </div>

        <!-- Footer Actions -->
        @if($notifications->count() > $maxDisplay || $showAll)
        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700 flex items-center justify-between flex-shrink-0 bg-gray-50 dark:bg-neutral-700/50">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Showing {{ min($maxDisplay, $notifications->count()) }} of {{ $notifications->count() }}
            </span>
            <div class="flex items-center gap-2">
                @if($variant === 'admin')
                <button type="button" 
                    onclick="refreshNotifications('{{ $variant }}')"
                    class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="size-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
                @endif
                <a href="{{ $variant === 'admin' ? route('admin.notifications.index') : route('client.notifications.index') }}" 
                   class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    View all →
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Global notification management functions
    window.notificationManager = {
        variant: '{{ $variant }}',
        
        markAsRead: function(notificationId) {
            const url = this.variant === 'admin' 
                ? '{{ route("admin.notifications.mark-read") }}'
                : '{{ route("client.dashboard.mark-notification-read") }}';
                
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ notification_id: notificationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateNotificationUI(notificationId);
                    this.updateBadgeCount();
                }
                return data;
            });
        },
        
        markAllAsRead: function() {
            return this.markAsRead('all');
        },
        
        updateNotificationUI: function(notificationId) {
            if (notificationId === 'all') {
                // Update all notification items
                const items = document.querySelectorAll('[data-notification-id]');
                items.forEach(item => {
                    item.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                    const unreadDot = item.querySelector('.notification-unread-dot');
                    if (unreadDot) unreadDot.remove();
                });
                
                // Hide mark all button
                const markAllBtn = document.querySelector('button[onclick*="markAllNotificationsAsRead"]');
                if (markAllBtn) markAllBtn.style.display = 'none';
            } else {
                // Update single notification
                const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                    const unreadDot = item.querySelector('.notification-unread-dot');
                    if (unreadDot) unreadDot.remove();
                }
            }
        },
        
        updateBadgeCount: function() {
            const statsUrl = this.variant === 'admin' 
                ? '{{ route("admin.dashboard.stats") }}'
                : '{{ route("client.dashboard.realtime-stats") }}';
                
            fetch(statsUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('notification-badge');
                    const unreadCount = this.variant === 'admin' 
                        ? data.data.unread_database_notifications 
                        : data.data.notifications.unread;
                    
                    if (unreadCount > 0) {
                        if (badge) {
                            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                        }
                    } else {
                        if (badge) badge.remove();
                    }
                }
            })
            .catch(error => console.error('Failed to update badge count:', error));
        },
        
        refresh: function() {
            const loadingEl = document.getElementById('notification-loading');
            const listEl = document.getElementById('notification-list');
            
            if (loadingEl) loadingEl.classList.remove('hidden');
            if (listEl) listEl.style.opacity = '0.5';
            
            const refreshUrl = this.variant === 'admin'
                ? '{{ route("admin.notifications.recent") }}'
                : '{{ route("client.dashboard.notifications") }}';
                
            fetch(refreshUrl + '?limit={{ $maxDisplay }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && listEl) {
                    listEl.innerHTML = this.renderNotifications(data.data);
                    this.updateBadgeCount();
                }
            })
            .catch(error => console.error('Failed to refresh notifications:', error))
            .finally(() => {
                if (loadingEl) loadingEl.classList.add('hidden');
                if (listEl) listEl.style.opacity = '1';
            });
        },
        
        renderNotifications: function(notifications) {
            if (!notifications.length) {
                return `
                    <div class="px-4 py-8 text-center">
                        <svg class="mx-auto size-12 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3" />
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">No new notifications</p>
                        <p class="text-xs text-gray-400 dark:text-neutral-500">You're all caught up!</p>
                    </div>
                `;
            }
            
            return notifications.map(notification => this.renderNotificationItem(notification)).join('');
        },
        
        renderNotificationItem: function(notification) {
            const unreadClass = !notification.is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '';
            const unreadDot = !notification.is_read ? 
                '<span class="notification-unread-dot flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></span>' : '';
            
            return `
                <div class="px-4 py-3 border-b border-gray-100 dark:border-neutral-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer transition-colors ${unreadClass}"
                     onclick="handleNotificationClick('${notification.id}', '${notification.url || '#'}')"
                     data-notification-id="${notification.id}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            ${this.getNotificationIcon(notification.type, notification.color)}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    ${notification.title}
                                </p>
                                <div class="flex items-center space-x-1">
                                    ${unreadDot}
                                    <span class="text-xs text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                                        ${notification.formatted_time}
                                    </span>
                                </div>
                            </div>
                            ${notification.message ? `<p class="mt-1 text-xs text-gray-600 dark:text-neutral-300 line-clamp-2">${notification.message}</p>` : ''}
                        </div>
                    </div>
                </div>
            `;
        },
        
        getNotificationIcon: function(type, color = 'gray') {
            const iconMap = {
                'project.created': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'project.updated': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'quotation.created': 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                'message.created': 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                'user.welcome': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
            };
            
            const path = iconMap[type] || 'M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3';
            
            return `
                <div class="size-8 bg-${color}-100 dark:bg-${color}-900/30 rounded-lg flex items-center justify-center">
                    <svg class="size-4 text-${color}-600 dark:text-${color}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${path}" />
                    </svg>
                </div>
            `;
        }
    };
    
    // Global functions for backward compatibility
    function handleNotificationClick(notificationId, url) {
        window.notificationManager.markAsRead(notificationId);
        if (url && url !== '#') {
            setTimeout(() => window.location.href = url, 100);
        }
    }
    
    function markAllNotificationsAsRead(variant) {
        window.notificationManager.markAllAsRead();
    }
    
    function refreshNotifications(variant) {
        window.notificationManager.refresh();
    }
    
    // Auto-refresh every 30 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setInterval(() => {
            window.notificationManager.updateBadgeCount();
        }, 30000);
        
        // Auto-refresh notifications every 2 minutes
        setInterval(() => {
            window.notificationManager.refresh();
        }, 120000);
    });
</script>
@endpush