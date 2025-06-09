{{-- resources/views/components/client/client-header.blade.php - FIXED --}}
@props([
    'unreadMessagesCount' => 0, 
    'pendingQuotationsCount' => 0,
    'recentNotifications' => collect(),
    'unreadNotificationsCount' => 0,
    'pendingApprovalsCount' => 0,
    'overdueProjectsCount' => 0
])


<header class="sticky top-0 inset-x-0 z-50 w-full bg-white border-b border-gray-200 text-sm dark:bg-gray-800 dark:border-gray-700 lg:ps-64">
    <nav class="w-full mx-auto px-4 py-2.5 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
        <!-- Left: Logo and Mobile Menu Toggle -->
        <div class="flex items-center lg:hidden">
            <!-- Mobile Menu Toggle -->
            <button type="button" 
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full mr-2"
                data-hs-overlay="#hs-application-sidebar" 
                aria-controls="hs-application-sidebar" 
                aria-label="Toggle navigation">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <!-- Mobile Logo -->
            <a href="{{ route('client.dashboard') }}" aria-label="{{ config('app.name') }}"
                class="text-xl font-bold text-blue-600 dark:text-white">
                {{ config('app.name') }}
            </a>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center justify-end w-full gap-x-2">
            <!-- Theme Toggle -->
            <button id="theme-toggle"
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full">
                <!-- Dark mode: Sun icon -->
                <svg class="hidden dark:block size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5" />
                    <line x1="12" y1="1" x2="12" y2="3" />
                    <line x1="12" y1="21" x2="12" y2="23" />
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                    <line x1="1" y1="12" x2="3" y2="12" />
                    <line x1="21" y1="12" x2="23" y2="12" />
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                </svg>
                <!-- Light mode: Moon icon -->
                <svg class="block dark:hidden size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                </svg>
                <span class="sr-only">Toggle dark mode</span>
            </button>

            <!-- FIXED: Notification Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative"
                    data-hs-dropdown-toggle id="notification-dropdown-toggle">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                    </svg>
                    @if($unreadNotificationsCount > 0)
                        <span class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-blue-500 rounded-full" id="notification-badge">
                            {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                        </span>
                    @endif
                    <span class="sr-only">Notifications</span>
                </button>

                <!-- FIXED: Dropdown Panel -->
                <div class="hs-dropdown-menu hidden z-50 mt-2 w-80 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
                    aria-labelledby="notification-dropdown-toggle">
                    
                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-800 dark:text-white">Notifications</h3>
                        <div class="flex items-center gap-2">
                            @if($unreadNotificationsCount > 0)
                            <button type="button" 
                                onclick="markAllNotificationsAsRead()"
                                class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                Mark all read
                            </button>
                            @endif
                            <a href="{{ route('client.notifications.index') }}" 
                               class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                View all
                            </a>
                        </div>
                    </div>

                    <!-- FIXED: Notification List with Loading State -->
                    <div class="max-h-80 overflow-y-auto" id="notification-list">
                        <!-- Loading State -->
                        <div id="notification-loading" class="px-4 py-8 text-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Loading notifications...</p>
                        </div>
                        
                        <!-- Initial Content (will be replaced by JS) -->
                        <div id="notification-content">
                            @forelse($recentNotifications as $notification)
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-neutral-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer {{ !$notification['is_read'] ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}"
                                     onclick="handleNotificationClick('{{ $notification['id'] }}', '{{ $notification['url'] ?? '#' }}')"
                                     data-notification-id="{{ $notification['id'] }}">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <x-notification.icon :type="$notification['icon']" :color="$notification['color']" size="sm" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $notification['title'] }}
                                                </p>
                                                <div class="flex items-center space-x-1">
                                                    @if(!$notification['is_read'])
                                                        <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full notification-unread-dot"></span>
                                                    @endif
                                                    <span class="text-xs text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                                                        {{ $notification['formatted_time'] }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if(!empty($notification['message']))
                                            <p class="mt-1 text-xs text-gray-600 dark:text-neutral-300 line-clamp-2">
                                                {{ $notification['message'] }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center" id="empty-state">
                                    <svg class="mx-auto size-12 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3" />
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">No new notifications</p>
                                    <p class="text-xs text-gray-400 dark:text-neutral-500">You're all caught up!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 inline-flex justify-center items-center rounded-full text-sm font-semibold text-gray-800 dark:text-white"
                    data-hs-dropdown-toggle>
                    @if(auth()->user()->avatar)
                        <img class="size-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="size-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-white">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </span>
                        </div>
                    @endif
                </button>

                <div class="hs-dropdown-menu hidden z-50 min-w-60 mt-2 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="px-5 py-3 bg-gray-50 dark:bg-neutral-700 rounded-t-lg">
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Signed in as</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-1.5 space-y-0.5">
                        <a href="{{ route('client.dashboard') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Dashboard
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load notifications when page loads
    loadNotifications();
    
    // Auto-refresh every 30 seconds
    setInterval(updateNotificationCounts, 30000);
});

function loadNotifications() {
    const loadingEl = document.getElementById('notification-loading');
    const contentEl = document.getElementById('notification-content');
    
    if (loadingEl) loadingEl.style.display = 'block';
    if (contentEl) contentEl.style.display = 'none';
    
    // Use your simple route
    fetch('{{ route("client.notifications.recent") }}?limit=10')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderNotifications(data.notifications);
            updateNotificationBadge(data.unread_count);
        }
    })
    .catch(error => {
        console.error('Error loading notifications:', error);
        showNotificationError();
    })
    .finally(() => {
        if (loadingEl) loadingEl.style.display = 'none';
        if (contentEl) contentEl.style.display = 'block';
    });
}

function renderNotifications(notifications) {
    const contentEl = document.getElementById('notification-content');
    if (!contentEl) return;
    
    if (!notifications || notifications.length === 0) {
        contentEl.innerHTML = `
            <div class="px-4 py-8 text-center">
                <svg class="mx-auto size-12 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3" />
                </svg>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">No new notifications</p>
                <p class="text-xs text-gray-400 dark:text-neutral-500">You're all caught up!</p>
            </div>
        `;
        return;
    }
    
    // Render notifications in your existing dropdown structure
    const html = notifications.map(notification => {
        const unreadClass = !notification.is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '';
        const unreadDot = !notification.is_read ? 
            '<span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full notification-unread-dot"></span>' : '';
        
        return `
            <div class="px-4 py-3 border-b border-gray-100 dark:border-neutral-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer transition-colors ${unreadClass}"
                 onclick="handleNotificationClick('${notification.id}', '${notification.action_url || '#'}')"
                 data-notification-id="${notification.id}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="size-8 bg-${notification.color}-100 dark:bg-${notification.color}-900/30 rounded-lg flex items-center justify-center">
                            <svg class="size-4 text-${notification.color}-600 dark:text-${notification.color}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3" />
                            </svg>
                        </div>
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
    }).join('');
    
    contentEl.innerHTML = html;
}

function handleNotificationClick(notificationId, url) {
    // Mark as read using your simple route
    markNotificationAsRead(notificationId);
    
    // Navigate to URL if provided
    if (url && url !== '#') {
        setTimeout(() => {
            window.location.href = url;
        }, 100);
    }
}

function markNotificationAsRead(notificationId) {
    fetch(`{{ route("client.notifications.mark-as-read", ":id") }}`.replace(':id', notificationId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationElement) {
                notificationElement.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                const unreadDot = notificationElement.querySelector('.notification-unread-dot');
                if (unreadDot) {
                    unreadDot.remove();
                }
            }
            
            updateNotificationBadge(data.unread_count);
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllNotificationsAsRead() {
    fetch('{{ route("client.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload notifications
            loadNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateNotificationCounts() {
    fetch('{{ route("client.notifications.unread-count") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNotificationBadge(data.count);
        }
    })
    .catch(error => console.error('Error updating notification counts:', error));
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-badge');
    
    if (count > 0) {
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline-flex';
        } else {
            // Create badge if it doesn't exist
            const toggleBtn = document.getElementById('notification-dropdown-toggle');
            if (toggleBtn) {
                const newBadge = document.createElement('span');
                newBadge.id = 'notification-badge';
                newBadge.className = 'absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-blue-500 rounded-full';
                newBadge.textContent = count > 99 ? '99+' : count;
                toggleBtn.appendChild(newBadge);
            }
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
    }
}

function showNotificationError() {
    const contentEl = document.getElementById('notification-content');
    if (contentEl) {
        contentEl.innerHTML = `
            <div class="px-4 py-8 text-center">
                <svg class="mx-auto size-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-red-600 mt-2">Failed to load notifications</p>
                <button onclick="loadNotifications()" class="text-xs text-blue-600 hover:underline mt-1">Try again</button>
            </div>
        `;
    }
}
</script>
@endpush