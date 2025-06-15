<!-- resources/views/client/messages/index.blade.php -->
<x-layouts.client title="Messages" :unreadMessages="$statistics['unread'] ?? 0" :pendingQuotations="0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Messages</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your communication with our support team</p>
        </div>

        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <x-admin.button href="{{ route('client.messages.create') }}" color="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Message
            </x-admin.button>

            @if (($statistics['unread'] ?? 0) > 0)
                <button type="button" onclick="markAllAsRead()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
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
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unread</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="unread-count">
                        {{ $statistics['unread'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Reply</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $statistics['pending_replies'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
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
    <x-admin.filter-section :action="route('client.messages.index')" :searchValue="request('search')" searchPlaceholder="Search messages..." :hasActiveFilters="request()->hasAny(['search', 'type', 'priority', 'read'])"
        :clearFiltersRoute="route('client.messages.index')" :filters="[
            [
                'name' => 'type',
                'label' => 'Type',
                'allLabel' => 'All Types',
                'options' => ['types'] ?? [],
            ],
            [
                'name' => 'priority',
                'label' => 'Priority',
                'allLabel' => 'All Priorities',
                'options' => ['priorities'] ?? [],
            ],
            [
                'name' => 'read',
                'label' => 'Read Status',
                'allLabel' => 'All Status',
                'options' => [
                    'unread' => 'Unread',
                    'read' => 'Read',
                ],
            ],
        ]" :sortOptions="[
            'created_at' => 'Date Created',
            'updated_at' => 'Last Updated',
        ]" />


    <!-- Messages List -->
    <x-admin.card>
    @if($messages->count() > 0)
        <!-- Table Header Actions -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Message List
                </h3>
                
                <div class="flex items-center gap-3">
                    <!-- Pagination Info -->
                    <div class="text-sm text-gray-700 dark:text-neutral-400">
                        Showing 
                        <span class="font-medium text-gray-900 dark:text-white">{{ $messages->firstItem() }}</span> 
                        to 
                        <span class="font-medium text-gray-900 dark:text-white">{{ $messages->lastItem() }}</span> 
                        of 
                        <span class="font-medium text-gray-900 dark:text-white">{{ $messages->total() }}</span> messages
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-6 py-3">
                            <input type="checkbox" onclick="toggleAllMessageCheckboxes(this)">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Received</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach($messages as $message)
                        <tr class="{{ $message->is_read ? '' : 'bg-blue-50 dark:bg-blue-900/20' }} hover:bg-gray-50 dark:hover:bg-neutral-700">
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                    class="message-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    value="{{ $message->id }}" onchange="updateBulkActions()">
                            </td>
                            <!-- Subject & Preview -->
                            <td class="px-6 py-4 whitespace-nowrap max-w-xs">
                                <div class="flex items-center">
                                    @if($message->priority === 'urgent')
                                        <span class="inline-flex items-center mr-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Urgent</span>
                                    @endif
                                    <a href="{{ route('client.messages.show', $message) }}"
                                       class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $message->subject }}
                                    </a>
                                    @if(!$message->is_read)
                                        <span class="ml-2 w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-300 line-clamp-2">
                                    {{ Str::limit(strip_tags($message->message), 70) }}
                                </div>
                            </td>
                            <!-- Type -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                    {{ $message->type === 'admin_to_client' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ ucfirst(str_replace('_', ' ', $message->type)) }}
                                </span>
                            </td>
                            <!-- Priority -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <x-admin.badge :priority="$message->priority" />
                            </td>
                            <!-- Project -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @if($message->project)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $message->project->title }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <!-- Received -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                <div>{{ $message->created_at->format('M j, Y') }}</div>
                                <div class="text-xs">{{ $message->created_at->diffForHumans() }}</div>
                            </td>
                            <!-- Status (Read/Unread + Attachments) -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @if($message->is_read)
                                    <span class="text-green-600 dark:text-green-400 font-medium">Read</span>
                                @else
                                    <span class="text-blue-600 dark:text-blue-400 font-medium">Unread</span>
                                @endif
                                @if($message->attachments_count > 0)
                                    <span class="ml-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        {{ $message->attachments_count }}
                                    </span>
                                @endif
                            </td>
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Reply -->
                                    @if(!$message->is_replied && $message->type !== 'admin_to_client')
                                        <x-admin.button 
                                            href="{{ route('client.messages.reply', $message) }}" 
                                            size="sm" 
                                            color="gray"
                                            title="Reply"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h7V6a1 1 0 011-1h4m0 0l5 5m-5-5v12" />
                                            </svg>
                                        </x-admin.button>
                                    @endif
                                    <!-- View Details -->
                                    <x-admin.button 
                                        href="{{ route('client.messages.show', $message) }}" 
                                        size="sm" 
                                        color="light"
                                        title="View Details"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </x-admin.button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
            {{ $messages->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                </path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No messages found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                @if(!empty(array_filter($filters ?? [])))
                    No messages match your current filters.
                @else
                    You haven't sent any messages yet.
                @endif
            </p>
            <div class="mt-6 flex justify-center gap-3">
                @if(!empty(array_filter($filters ?? [])))
                    <x-admin.button href="{{ route('client.messages.index') }}" color="light">
                        Clear Filters
                    </x-admin.button>
                @endif
                <x-admin.button href="{{ route('client.messages.create') }}" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Send Your First Message
                </x-admin.button>
            </div>
        </div>
    @endif
</x-admin.card>

</x-layouts.client>

<script>

    // Auto-refresh unread count
    setInterval(async () => {
        try {
            const response = await fetch('{{ route('api.client.messages.unread-count') }}');
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
            const response = await fetch('{{ route('api.client.messages.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
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
