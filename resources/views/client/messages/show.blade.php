<!-- resources/views/client/messages/show.blade.php -->
<x-layouts.client title="Message Details" :unreadMessages="0" :pendingQuotations="0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('client.messages.index'),
            'Message Details' => '#',
        ]" />

        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <!-- Quick Actions -->
            <button type="button"
                onclick="toggleMessageRead({{ $message->id }}, {{ $message->is_read ? 'false' : 'true' }})"
                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                @if ($message->is_read)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    Mark Unread
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mark Read
                @endif
            </button>

            @if ($canEscalate)
                <form action="{{ route('client.messages.mark-urgent', $message) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to mark this message as urgent?')"
                        class="inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 text-sm font-medium rounded-md text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        Mark Urgent
                    </button>
                </form>
            @endif

            @if ($message->project)
                <x-admin.button href="{{ route('client.messages.project', $message->project) }}" color="light"
                    size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    View Project Messages
                </x-admin.button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Message Header -->
            <x-admin.card>
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $message->subject }}
                            </h1>

                            @if ($message->priority === 'urgent')
                                <span
                                    class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                    Urgent
                                </span>
                            @elseif($message->priority === 'high')
                                <span
                                    class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 rounded-full">
                                    High Priority
                                </span>
                            @endif

                            @if (!$message->is_read)
                                <span
                                    class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                    Unread
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ ucfirst(str_replace('_', ' ', $message->type)) }}</span>
                            <span>•</span>
                            <span>{{ $message->created_at->format('M j, Y \a\t g:i A') }}</span>
                            @if ($message->project)
                                <span>•</span>
                                <a href="{{ route('client.projects.show', $message->project) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    {{ $message->project->title }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Conversation Thread -->
            <x-admin.card>
                <x-slot name="title">Conversation</x-slot>

                <div class="space-y-6">
                    @foreach ($thread as $threadMessage)
                        <div class="flex space-x-4 {{ $threadMessage->type === 'admin_to_client' ? 'ml-8' : '' }}">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $threadMessage->type === 'admin_to_client' ? 'bg-green-100 dark:bg-green-900' : 'bg-blue-100 dark:bg-blue-900' }}">
                                    @if ($threadMessage->type === 'admin_to_client')
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div class="flex-1 min-w-0">
                                <div
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                                    <!-- Message Header -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $threadMessage->type === 'admin_to_client' ? 'Support Team' : 'You' }}
                                            </span>
                                            @if ($threadMessage->type === 'client_reply')
                                                <span
                                                    class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                                    Reply
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $threadMessage->created_at->format('M j, Y \a\t g:i A') }}
                                        </span>
                                    </div>

                                    <!-- Message Body -->
                                    <div class="prose prose-sm max-w-none dark:prose-invert">
                                        {!! nl2br(e($threadMessage->message)) !!}
                                    </div>

                                    <!-- Attachments -->
                                    @if ($threadMessage->attachments->count() > 0)
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                                Attachments:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach ($threadMessage->attachments as $attachment)
                                                    <a href="{{ route('client.messages.attachment.download', [$threadMessage, $attachment]) }}"
                                                        class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                            </path>
                                                        </svg>
                                                        <div class="flex-1 min-w-0">
                                                            <p
                                                                class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                                {{ $attachment->file_name }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $attachment->file_size_formatted }}
                                                            </p>
                                                        </div>
                                                        <svg class="w-4 h-4 text-gray-400 ml-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>

            <!-- Reply Form -->
            @if ($canReply)
                @include('client.messages.partials.reply-form', ['message' => $message])
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Message Info -->
            <x-admin.card>
                <x-slot name="title">Message Info</x-slot>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                        <span
                            class="font-medium {{ $message->is_replied ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">
                            {{ $message->is_replied ? 'Replied' : 'Pending' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Priority:</span>
                        <span
                            class="font-medium 
                            {{ $message->priority === 'urgent' ? 'text-red-600 dark:text-red-400' : '' }}
                            {{ $message->priority === 'high' ? 'text-orange-600 dark:text-orange-400' : '' }}
                            {{ $message->priority === 'normal' ? 'text-green-600 dark:text-green-400' : '' }}
                            {{ $message->priority === 'low' ? 'text-gray-600 dark:text-gray-400' : '' }}">
                            {{ ucfirst($message->priority) }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ ucfirst(str_replace('_', ' ', $message->type)) }}
                        </span>
                    </div>

                    @if ($message->project)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Project:</span>
                            <a href="{{ route('client.projects.show', $message->project) }}"
                                class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 truncate">
                                {{ $message->project->title }}
                            </a>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Created:</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $message->created_at->format('M j, Y') }}
                        </span>
                    </div>

                    @if ($message->replied_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Last Reply:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $message->replied_at->format('M j, Y') }}
                            </span>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Thread Stats -->
            @if ($thread->count() > 1)
                <x-admin.card>
                    <x-slot name="title">Thread Stats</x-slot>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Total Messages:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $thread->count() }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Your Messages:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $thread->whereIn('type', ['general', 'support', 'project_inquiry', 'complaint', 'feedback', 'client_reply'])->count() }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Team Replies:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $thread->where('type', 'admin_to_client')->count() }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Last Activity:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $thread->last()->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </x-admin.card>
            @endif

            <!-- Related Messages -->
            @if ($relatedMessages->count() > 0)
                <x-admin.card>
                    <x-slot name="title">Other Project Messages</x-slot>

                    <div class="space-y-3">
                        @foreach ($relatedMessages as $related)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                <a href="{{ route('client.messages.show', $related) }}"
                                    class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    {{ Str::limit($related->subject, 40) }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $related->created_at->format('M j, Y') }} •
                                    {{ ucfirst(str_replace('_', ' ', $related->type)) }}
                                    @if (!$related->is_read)
                                        <span class="text-blue-600 dark:text-blue-400">• Unread</span>
                                    @endif
                                </p>
                            </div>
                        @endforeach

                        @if ($relatedMessages->count() === 5)
                            <div class="text-center pt-2">
                                <a href="{{ route('client.messages.project', $message->project) }}"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    View all project messages →
                                </a>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endif

            <!-- Quick Actions -->
            <x-admin.card>
                <x-slot name="title">Quick Actions</x-slot>

                <div class="space-y-2">
                    <x-admin.button
                        href="{{ route('client.messages.create', ['project_id' => $message->project_id, 'subject' => 'Re: ' . $message->subject]) }}"
                        color="primary" size="sm" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        New Message
                    </x-admin.button>

                    @if ($message->project)
                        <x-admin.button href="{{ route('client.projects.show', $message->project) }}" color="light"
                            size="sm" class="w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            View Project
                        </x-admin.button>
                    @endif

                    <x-admin.button href="{{ route('client.messages.index') }}" color="light" size="sm"
                        class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        All Messages
                    </x-admin.button>
                </div>
            </x-admin.card>
        </div>
    </div>
    </x-layouts.client>

    <script>
        // Toggle message read status
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
                    // Refresh the page to show updated status
                    window.location.reload();
                } else {
                    console.error('Failed to toggle read status');
                }
            } catch (error) {
                console.error('Error toggling read status:', error);
            }
        }

        // Auto-scroll to latest message in thread
        document.addEventListener('DOMContentLoaded', function() {
            const threadMessages = document.querySelectorAll('.space-y-6 > div');
            if (threadMessages.length > 1) {
                const lastMessage = threadMessages[threadMessages.length - 1];
                lastMessage.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });

        // Auto-refresh for new replies (every 30 seconds)
        let refreshInterval;

        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                // Check for new messages in thread via API
                fetch(`/api/client/messages/{{ $message->id }}/check-updates`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.has_updates) {
                            // Show notification about new replies
                            showUpdateNotification();
                        }
                    })
                    .catch(error => console.error('Auto-refresh error:', error));
            }, 30000);
        }

        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }

        function showUpdateNotification() {
            const notification = document.createElement('div');
            notification.className =
                'fixed top-4 right-4 p-4 bg-blue-100 border border-blue-200 text-blue-800 rounded-md shadow-lg z-50';
            notification.innerHTML = `
        <div class="flex items-center justify-between">
            <span>New reply received!</span>
            <button onclick="window.location.reload()" class="ml-3 text-blue-600 hover:text-blue-800 font-medium">
                Refresh
            </button>
        </div>
    `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 10000);
        }

        // Start auto-refresh when page loads
        window.addEventListener('load', startAutoRefresh);

        // Stop auto-refresh when page unloads
        window.addEventListener('beforeunload', stopAutoRefresh);

        // Stop auto-refresh when user is typing a reply
        const replyTextarea = document.querySelector('textarea[name="message"]');
        if (replyTextarea) {
            replyTextarea.addEventListener('focus', stopAutoRefresh);
            replyTextarea.addEventListener('blur', () => {
                setTimeout(startAutoRefresh, 5000); // Resume after 5 seconds
            });
        }
    </script>
