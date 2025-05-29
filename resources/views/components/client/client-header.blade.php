{{-- resources/views/components/client/client-header.blade.php --}}
@props(['unreadMessagesCount' => 0, 'pendingApprovalsCount' => 0])

@php
    // Use services instead of direct queries
    $user = auth()->user();
    
    // Get notification summary from service
    $notificationSummary = app(\App\Services\NotificationAlertService::class)->getNotificationSummary($user);
    
    // Get recent notifications from service  
    $notifications = $user->notifications()->latest()->limit(10)->get();
    $unreadNotifications = $notificationSummary['unread_count'] ?? 0;
    
    // Calculate total badge count
    $totalBadgeCount = $unreadNotifications + $unreadMessagesCount + $pendingApprovalsCount;
@endphp

<header class="sticky top-0 inset-x-0 z-50 w-full bg-white border-b border-gray-200 text-sm dark:bg-gray-800 dark:border-gray-700 lg:ps-64">
    <nav class="w-full mx-auto px-4 py-2.5 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
        <!-- Left: Logo for mobile -->
        <div class="flex items-center lg:hidden">
            <a href="{{ route('client.dashboard') }}" aria-label="{{ config('app.name') }}"
                class="text-xl font-bold text-blue-600 dark:text-white">
                @if(isset($companyProfile) && $companyProfile->logo)
                    <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" class="h-8 md:h-10">
                @else
                    {{ config('app.name') }}
                @endif
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

            <!-- Notifications Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative"
                    data-hs-dropdown-toggle>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                        <path d="m13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @if($totalBadgeCount > 0)
                        <span class="absolute -top-1 -end-1 h-4 w-4 bg-red-500 text-white text-xs flex items-center justify-center rounded-full animate-pulse">
                            {{ $totalBadgeCount > 99 ? '99+' : $totalBadgeCount }}
                        </span>
                    @endif
                    <span class="sr-only">Notifications</span>
                </button>

                <div class="hs-dropdown-menu hidden z-50 min-w-80 max-w-sm mt-2 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
                    aria-labelledby="hs-dropdown-toggle">
                    <!-- Header -->
                    <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-700 rounded-t-lg border-b border-gray-200 dark:border-neutral-600">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifications</h3>
                            @if($unreadNotifications > 0)
                                <button onclick="markAllNotificationsRead()" 
                                        class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Mark all read
                                </button>
                            @endif
                        </div>
                        @if($totalBadgeCount > 0)
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ $totalBadgeCount }} new notification{{ $totalBadgeCount > 1 ? 's' : '' }}
                            </p>
                        @endif
                        
                        <!-- Notification Summary -->
                        @if(isset($notificationSummary['recent_notifications']) && count($notificationSummary['recent_notifications']) > 0)
                            <div class="flex items-center space-x-2 mt-2">
                                @if(($notificationSummary['unread_messages'] ?? 0) > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        {{ $notificationSummary['unread_messages'] }} messages
                                    </span>
                                @endif
                                @if(($notificationSummary['pending_approvals'] ?? 0) > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                        {{ $notificationSummary['pending_approvals'] }} approvals
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Notifications List -->
                    <div class="max-h-80 overflow-y-auto">
                        @if($notifications->count() > 0)
                            @foreach($notifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $isUnread = is_null($notification->read_at);
                                    $type = $notification->type;
                                    
                                    // Use service helper method to get notification details
                                    $notificationDetails = app(\App\Services\NotificationAlertService::class)->getNotificationDetails($notification);
                                    $iconColor = $notificationDetails['icon_color'];
                                    $icon = $notificationDetails['icon'];
                                @endphp
                                
                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-100 dark:border-neutral-600 last:border-b-0 {{ $isUnread ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}"
                                     onclick="handleNotificationClick('{{ $notification->id }}', '{{ $this->getNotificationUrl($notification) }}')">
                                    <div class="flex items-start space-x-3">
                                        <!-- Icon -->
                                        <div class="flex-shrink-0 mt-0.5">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $iconColor }}">
                                                @if($icon === 'folder')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                    </svg>
                                                @elseif($icon === 'document-text')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                @elseif($icon === 'mail')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $data['title'] ?? $notificationDetails['title'] }}
                                                </p>
                                                @if($isUnread)
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 ml-2"></div>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                {{ $data['message'] ?? $notificationDetails['message'] }}
                                            </p>
                                            <div class="flex items-center justify-between mt-2">
                                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                                @if(isset($data['type']))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ ucfirst(str_replace('_', ' ', $data['type'])) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notifications</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    @if($notifications->count() > 0)
                        <div class="px-4 py-3 bg-gray-50 dark:bg-neutral-700 rounded-b-lg border-t border-gray-200 dark:border-neutral-600">
                            <a href="{{ route('client.notifications.index') }}" 
                               class="block text-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                View all notifications
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Messages Icon -->
            <a href="{{ route('client.messages.index') }}"
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                @if($unreadMessagesCount > 0)
                    <span class="absolute -top-1 -end-1 h-4 w-4 bg-red-500 text-white text-xs flex items-center justify-center rounded-full">
                        {{ $unreadMessagesCount > 9 ? '9+' : $unreadMessagesCount }}
                    </span>
                @endif
                <span class="sr-only">Messages</span>
            </a>

            <!-- Quotations/Approvals Icon -->
            <a href="{{ route('client.quotations.index') }}"
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                @if($pendingApprovalsCount > 0)
                    <span class="absolute -top-1 -end-1 h-4 w-4 bg-amber-500 text-white text-xs flex items-center justify-center rounded-full">
                        {{ $pendingApprovalsCount > 9 ? '9+' : $pendingApprovalsCount }}
                    </span>
                @endif
                <span class="sr-only">Quotations</span>
            </a>

            <!-- User Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 inline-flex justify-center items-center rounded-full text-sm font-semibold text-gray-800 dark:text-white"
                    data-hs-dropdown-toggle>
                    @if(auth()->user()->avatar)
                        <img class="size-8 rounded-full object-cover"
                            src="{{ Storage::url(auth()->user()->avatar) }}"
                            alt="{{ auth()->user()->name }}">
                    @else
                        <div class="size-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </button>

                <div class="hs-dropdown-menu hidden z-50 min-w-60 mt-2 bg-white shadow-md rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
                    aria-labelledby="hs-dropdown-toggle">
                    <div class="px-5 py-3 bg-gray-100 dark:bg-neutral-700 rounded-t-lg">
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Signed in as</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-1.5 space-y-0.5">
                        <a href="{{ route('client.dashboard') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('client.profile.edit') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            My Profile
                        </a>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 dark:border-neutral-700 my-1"></div>

                        <a href="{{ route('home') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                <polyline points="15 3 21 3 21 9" />
                                <line x1="10" y1="14" x2="21" y2="3" />
                            </svg>
                            View Website
                        </a>

                        @if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'manager']))
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-amber-700 hover:bg-amber-100 dark:text-amber-300 dark:hover:bg-amber-900/20">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                    <path d="m7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                Admin Panel
                            </a>
                        @endif

                        <!-- Divider -->
                        <div class="border-t border-gray-200 dark:border-neutral-700 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" y1="12" x2="9" y2="12" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End User Dropdown -->
        </div>
    </nav>
</header>

<script>
// Notification handling functions
function handleNotificationClick(notificationId, url) {
    // Mark notification as read
    fetch(`/client/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    }).then(() => {
        // Navigate to the URL if provided
        if (url && url !== '#') {
            window.location.href = url;
        }
        // Update badge count
        updateNotificationBadge();
    });
}

function markAllNotificationsRead() {
    fetch('/client/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    }).then(() => {
        // Refresh the page to update notification list
        window.location.reload();
    });
}

function updateNotificationBadge() {
    fetch('/client/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
}

// Auto-refresh notifications using service endpoint
setInterval(() => {
    fetch('/client/notifications/summary')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.data.unread_count || 0);
            }
        });
}, 30000);
</script>

@php
// We'll add helper methods to NotificationAlertService instead of inline functions
@endphp