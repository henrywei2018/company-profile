<!-- resources/views/client/messages/index.blade.php -->
<x-layouts.admin title="Messages" :unreadMessages="$statistics['unread'] ?? 0" :pendingQuotations="0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Messages</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your communication with our support team</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <x-admin.button
                href="{{ route('client.messages.create') }}"
                color="primary"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Message
            </x-admin.button>
            
            @if(($statistics['unread'] ?? 0) > 0)
            <button
                type="button"
                onclick="markAllAsRead()"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Mark All Read
            </button>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Messages</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unread</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="unread-count">{{ $statistics['unread'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Reply</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['pending_replies'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgent</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['urgent'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
            <div class="flex-1">
                <x-admin.input
                    name="search"
                    placeholder="Search messages..."
                    value="{{ $filters['search'] ?? '' }}"
                    class="w-full"
                />
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-2">
                <select name="type" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Types</option>
                    @foreach($filterOptions['types'] ?? [] as $value => $label)
                        <option value="{{ $value }}" {{ ($filters['type'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                
                <select name="priority" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Priorities</option>
                    @foreach($filterOptions['priorities'] ?? [] as $value => $label)
                        <option value="{{ $value }}" {{ ($filters['priority'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                
                <select name="read" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="unread" {{ ($filters['read'] ?? '') === 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ ($filters['read'] ?? '') === 'read' ? 'selected' : '' }}>Read</option>
                </select>
                
                <x-admin.button type="submit" color="light" size="sm" class="w-full">
                    Filter
                </x-admin.button>
                
                @if(!empty(array_filter($filters ?? [])))
                    <x-admin.button 
                        href="{{ route('client.messages.index') }}" 
                        color="light" 
                        size="sm"
                        class="w-full"
                    >
                        Clear
                    </x-admin.button>
                @endif
            </div>
        </form>
    </x-admin.card>

    <!-- Bulk Actions Bar (Hidden by default) -->
    @include('client.messages.partials.bulk-actions')

    <!-- Messages List -->
    <x-admin.card>
        <x-slot name="title">
            <div class="flex items-center justify-between">
                <span>Your Messages</span>
                <div class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        id="select-all"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        onchange="selectAll()"
                        title="Select all messages"
                    >
                    <label for="select-all" class="text-sm text-gray-500 dark:text-gray-400">Select All</label>
                </div>
            </div>
        </x-slot>
        
        @if($messages->count() > 0)
            <div class="message-list space-y-1">
                @foreach($messages as $message)
                    <div class="message-item flex items-start space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors
                        {{ $message->is_read ? 'bg-white dark:bg-gray-800' : 'bg-blue-50 dark:bg-blue-900/20' }}"
                        data-message-id="{{ $message->id }}">
                        
                        <!-- Checkbox -->
                        <div class="flex-shrink-0">
                            <input 
                                type="checkbox" 
                                class="message-checkbox mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                value="{{ $message->id }}"
                                onchange="updateBulkActions()"
                            >
                        </div>
                        
                        <!-- Message Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                @if($message->type === 'admin_to_client') bg-green-100 dark:bg-green-900
                                @elseif($message->priority === 'urgent') bg-red-100 dark:bg-red-900
                                @else bg-gray-100 dark:bg-gray-700
                                @endif">
                                @if($message->type === 'admin_to_client')
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($message->priority === 'urgent')
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Message Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    <a href="{{ route('client.messages.show', $message) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $message->subject }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 ml-2">
                                    @if($message->priority === 'urgent')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                            Urgent
                                        </span>
                                    @endif
                                    
                                    @if($message->project)
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                            {{ $message->project->title }}
                                        </span>
                                    @endif
                                    
                                    @if(!$message->is_read)
                                        <span class="unread-indicator w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                {{ Str::limit(strip_tags($message->message), 120) }}
                            </p>
                            
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ ucfirst(str_replace('_', ' ', $message->type)) }}</span>
                                    <span>{{ $message->created_at->diffForHumans() }}</span>
                                    @if($message->attachments_count > 0)
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ $message->attachments_count }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @if($message->is_replied)
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Replied</span>
                                    @elseif($message->type !== 'admin_to_client')
                                        <span class="text-xs text-orange-600 dark:text-orange-400">Pending</span>
                                    @endif
                                    
                                    <!-- Quick Actions -->
                                    <div class="flex items-center space-x-1">
                                        <button
                                            type="button"
                                            onclick="toggleMessageRead({{ $message->id }}, {{ $message->is_read ? 'false' : 'true' }})"
                                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                            title="{{ $message->is_read ? 'Mark as unread' : 'Mark as read' }}"
                                        >
                                            @if($message->is_read)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No messages found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    @if(!empty(array_filter($filters ?? [])))
                        No messages match your current filters.
                    @else
                        You haven't sent any messages yet.
                    @endif
                </p>
                <div class="space-x-3">
                    @if(!empty(array_filter($filters ?? [])))
                        <x-admin.button 
                            href="{{ route('client.messages.index') }}" 
                            color="light"
                        >
                            Clear Filters
                        </x-admin.button>
                    @endif
                    <x-admin.button
                        href="{{ route('client.messages.create') }}"
                        color="primary"
                    >
                        Send Your First Message
                    </x-admin.button>
                </div>
            </div>
        @endif
    </x-admin.card>
</x-layouts.admin>

<script>
// Include bulk actions script
@include('client.messages.partials.bulk-actions-script')

// Auto-refresh unread count
setInterval(async () => {
    try {
        const response = await fetch('{{ route("api.client.messages.unread-count") }}');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('unread-count').textContent = data.count;
        }
    } catch (error) {
        console.error('Failed to refresh unread count:', error);
    }
}, 30000);

// Mark all as read function
async function markAllAsRead() {
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
            showNotification('success', data.message || 'All messages marked as read');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('error', 'Failed to mark messages as read');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'An error occurred');
    }
}

// Toggle individual message read status
async function toggleMessageRead(messageId, markAsRead) {
    try {
        const response = await fetch(`/client/messages/${messageId}/toggle-read`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            // Update UI
            const messageItem = document.querySelector(`[data-message-id="${messageId}"]`);
            const unreadIndicator = messageItem.querySelector('.unread-indicator');
            
            if (markAsRead) {
                messageItem.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                messageItem.classList.add('bg-white', 'dark:bg-gray-800');
                if (unreadIndicator) unreadIndicator.remove();
            } else {
                messageItem.classList.remove('bg-white', 'dark:bg-gray-800');
                messageItem.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                if (!unreadIndicator) {
                    const indicator = document.createElement('span');
                    indicator.className = 'unread-indicator w-2 h-2 bg-blue-500 rounded-full';
                    messageItem.querySelector('.flex.items-center.space-x-2.ml-2').appendChild(indicator);
                }
            }
            
            // Update unread count
            const unreadCountEl = document.getElementById('unread-count');
            const currentCount = parseInt(unreadCountEl.textContent);
            unreadCountEl.textContent = markAsRead ? currentCount - 1 : currentCount + 1;
        }
    } catch (error) {
        console.error('Error toggling read status:', error);
    }
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
        'bg-red-100 text-red-800 border border-red-200'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>