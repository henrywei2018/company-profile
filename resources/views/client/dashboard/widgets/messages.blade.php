<!-- resources/views/client/dashboard/widgets/messages.blade.php -->
<x-admin.card>
    <x-slot name="title">
        <div class="flex items-center justify-between">
            <span class="flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Messages
            </span>
            <a href="{{ route('client.messages.index') }}" class="text-sm font-normal text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                View All
            </a>
        </div>
    </x-slot>
    
    <div class="space-y-6">
        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-lg font-bold text-gray-900 dark:text-white" id="widget-total-messages">
                    {{ $messageSummary['total'] ?? 0 }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
            </div>
            
            <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="text-lg font-bold text-blue-600 dark:text-blue-400" id="widget-unread-messages">
                    {{ $messageSummary['unread'] ?? 0 }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Unread</div>
            </div>
            
            <div class="text-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <div class="text-lg font-bold text-orange-600 dark:text-orange-400" id="widget-pending-messages">
                    {{ $messageSummary['awaiting_reply'] ?? 0 }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Pending</div>
            </div>
            
            <div class="text-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <div class="text-lg font-bold text-red-600 dark:text-red-400" id="widget-urgent-messages">
                    {{ $messageSummary['urgent'] ?? 0 }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Urgent</div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        @if(!empty($recentActivity) && count($recentActivity) > 0)
        <div>
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Recent Activity
            </h4>
            
            <div class="space-y-2 max-h-48 overflow-y-auto" id="recent-activity-list">
                @foreach(array_slice($recentActivity, 0, 5) as $activity)
                <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <div class="flex-shrink-0 mt-1">
                        @if($activity['priority'] === 'urgent')
                            <div class="w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>
                                </svg>
                            </div>
                        @elseif(str_contains($activity['action'], 'reply'))
                            <div class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            <a href="{{ $activity['url'] }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ Str::limit($activity['title'], 35) }}
                            </a>
                            @if(!$activity['is_read'])
                                <span class="ml-1 w-1.5 h-1.5 bg-blue-500 rounded-full inline-block"></span>
                            @endif
                        </p>
                        
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span>{{ $activity['action'] }}</span>
                            @if($activity['project'])
                                <span class="mx-1">•</span>
                                <span class="truncate">{{ Str::limit($activity['project']['title'], 15) }}</span>
                            @endif
                        </div>
                        
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if(count($recentActivity) > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('client.messages.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        View {{ count($recentActivity) - 5 }} more activities
                    </a>
                </div>
            @endif
        </div>
        @endif
        
        <!-- Quick Actions -->
        <div class="space-y-2">
            <x-admin.button
                href="{{ route('client.messages.create') }}"
                color="primary"
                size="sm"
                class="w-full"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Send New Message
            </x-admin.button>
            
            <div class="grid grid-cols-2 gap-2">
                @if(($messageSummary['unread'] ?? 0) > 0)
                <button
                    type="button"
                    onclick="markAllAsReadFromWidget()"
                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mark All Read
                </button>
                @endif
                
                <button
                    type="button"
                    onclick="refreshWidgetData()"
                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        
        <!-- Urgent Messages Alert -->
        @if(($messageSummary['urgent'] ?? 0) > 0)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">
                        You have {{ $messageSummary['urgent'] }} urgent message{{ $messageSummary['urgent'] > 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-red-600 dark:text-red-400">
                        <a href="{{ route('client.messages.index', ['priority' => 'urgent']) }}" class="underline hover:no-underline">
                            View urgent messages →
                        </a>
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-admin.card>

<script>
// Widget-specific JavaScript functions
async function markAllAsReadFromWidget() {
    try {
        const response = await fetch('{{ route("api.client.messages.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update widget counts
            document.getElementById('widget-unread-messages').textContent = '0';
            
            // Show success feedback
            showWidgetNotification('success', data.message || 'All messages marked as read');
            
            // Refresh widget data after a short delay
            setTimeout(refreshWidgetData, 1000);
        } else {
            showWidgetNotification('error', 'Failed to mark messages as read');
        }
    } catch (error) {
        console.error('Error:', error);
        showWidgetNotification('error', 'An error occurred');
    }
}

async function refreshWidgetData() {
    try {
        // Show loading state
        const refreshBtn = document.querySelector('button[onclick="refreshWidgetData()"]');
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<svg class="animate-spin w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...';
        
        const response = await fetch('{{ route("api.client.messages.summary") }}');
        const data = await response.json();
        
        if (data.success) {
            // Update all counts
            const summary = data.data;
            document.getElementById('widget-total-messages').textContent = summary.total || 0;
            document.getElementById('widget-unread-messages').textContent = summary.unread || 0;
            document.getElementById('widget-pending-messages').textContent = summary.awaiting_reply || 0;
            document.getElementById('widget-urgent-messages').textContent = summary.urgent || 0;
            
            showWidgetNotification('success', 'Widget refreshed');
        } else {
            showWidgetNotification('error', 'Failed to refresh data');
        }
        
        // Restore button
        refreshBtn.innerHTML = originalContent;
        
    } catch (error) {
        console.error('Error refreshing widget:', error);
        showWidgetNotification('error', 'Failed to refresh data');
        
        // Restore button
        const refreshBtn = document.querySelector('button[onclick="refreshWidgetData()"]');
        refreshBtn.innerHTML = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Refresh';
    }
}

function showWidgetNotification(type, message) {
    // Create a small notification within the widget
    const widget = document.querySelector('.messages-widget, [class*="messages"]').closest('.card, .bg-white, .dark\\:bg-gray-800');
    if (!widget) return;
    
    // Remove existing notifications
    const existing = widget.querySelectorAll('.widget-notification');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `widget-notification absolute top-2 right-2 px-3 py-1 text-xs rounded-md shadow-lg z-10 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
        'bg-red-100 text-red-800 border border-red-200'
    }`;
    notification.textContent = message;
    
    // Position relative to widget
    widget.style.position = 'relative';
    widget.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Auto-refresh widget data every 60 seconds
setInterval(async () => {
    try {
        const response = await fetch('{{ route("api.client.messages.unread-count") }}');
        const data = await response.json();
        
        if (data.success) {
            const currentCount = parseInt(document.getElementById('widget-unread-messages').textContent);
            const newCount = data.count;
            
            // Update count
            document.getElementById('widget-unread-messages').textContent = newCount;
            
            // Show notification if new messages arrived
            if (newCount > currentCount) {
                showWidgetNotification('success', `${newCount - currentCount} new message${newCount - currentCount > 1 ? 's' : ''}`);
            }
        }
    } catch (error) {
        console.error('Failed to auto-refresh widget:', error);
    }
}, 60000);

// Real-time updates when on messages page
if (window.location.pathname.includes('/messages')) {
    // More frequent updates when actively viewing messages
    setInterval(refreshWidgetData, 30000);
}
</script>