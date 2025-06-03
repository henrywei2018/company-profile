
<x-layouts.client title="Notifications">
    
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Stay updated with your projects and activities.
                </p>
            </div>
            
            <div class="flex items-center space-x-3">
                @if($statistics['unread'] > 0)
                <button onclick="markAllAsRead()" 
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800">
                    Mark All Read ({{ $statistics['unread'] }})
                </button>
                @endif
                
                <a href="{{ route('client.notifications.preferences') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    Preferences
                </a>
            </div>
        </div>
    </div>

    <!-- Simple Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $statistics['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <span class="w-2 h-2 bg-red-600 rounded-full"></span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Unread</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $statistics['unread'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">This Week</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $statistics['this_week'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Today</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $statistics['today'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Notifications List -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">All Notifications</h2>
        </div>
        
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $isRead = !is_null($notification->read_at);
                    $unreadClass = !$isRead ? 'bg-blue-50 dark:bg-blue-900/10' : '';
                @endphp
                
                <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer {{ $unreadClass }}"
                     onclick="handleNotificationClick('{{ $notification->id }}", '{{ $data['action_url'] ?? '#' }}')"
                     data-notification-id="{{ $notification->id }}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if(!$isRead)
                                <span class="w-2 h-2 bg-blue-600 rounded-full mt-2"></span>
                            @else
                                <span class="w-2 h-2 bg-gray-300 rounded-full mt-2"></span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $data['title'] ?? 'Notification' }}
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap ml-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            @if(!empty($data['message']))
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ $data['message'] }}
                                </p>
                            @endif
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ ucwords(str_replace(['.', '_'], ' ', class_basename($notification->type))) }}
                                </span>
                                @if(!$isRead)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        New
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3"/>
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No notifications</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You're all caught up! No notifications at this time.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    function markAllAsRead() {
        if (!confirm('Mark all notifications as read?')) return;
        
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
                location.reload();
            } else {
                alert('Failed to mark notifications as read');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    function handleNotificationClick(notificationId, url) {
        // Mark as read
        fetch(`{{ route("client.notifications.mark-as-read", ":id") }}`.replace(':id', notificationId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Navigate to URL
            if (url && url !== '#') {
                window.location.href = url;
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }
    </script>
    @endpush

</x-layouts.client>