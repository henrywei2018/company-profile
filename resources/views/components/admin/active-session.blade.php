@props(['session'])
<div class="p-4 group border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors" 
     data-session-id="{{ $session->session_id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ $session->getVisitorName() }}
                </h4>
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" title="Active chat"></div>
                @if($session->assigned_operator_id === auth()->id())
                    <x-admin.badge type="success" size="sm">Your Chat</x-admin.badge>
                @endif
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-xs text-gray-500 dark:text-gray-400 session-operator">
                    with {{ $session->operator->name ?? 'Unassigned' }}
                </p>
            </div>
            @if($session->latestMessage)
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 last-message bg-gray-50 dark:bg-neutral-800 p-2 rounded">
                    <span class="font-medium">
                        @if($session->latestMessage->sender_type === 'visitor')
                            {{ $session->getVisitorName() }}:
                        @elseif($session->latestMessage->sender_type === 'operator')
                            {{ $session->latestMessage->sender->name ?? 'Operator' }}:
                        @else
                            System:
                        @endif
                    </span>
                    "{{ Str::limit($session->latestMessage->message, 50) }}"
                </p>
            @endif
            <div class="flex items-center space-x-4 mt-2">
                <p class="text-xs text-gray-600 dark:text-gray-300 last-activity-time">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Last activity {{ $session->last_activity_at->diffForHumans() }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ $session->messages->count() }} messages
                </p>
            </div>
        </div>
        <!-- Hover-revealed actions -->
<div class="ml-4 flex flex-col items-end justify-start group relative">
    <div class="absolute top-0 right-0 hidden group-hover:flex flex-col space-y-2 z-10">
        <a href="{{ route('admin.chat.show', $session) }}"
           class="px-2 py-1 text-xs rounded bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
            Open
        </a>

        @if($session->assigned_operator_id !== auth()->id())
            <button onclick="takeOverSession('{{ $session->session_id }}')"
                    class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-800 transition-colors">
                Take Over
            </button>
        @endif
    </div>
</div>

    </div>
    
    <!-- Unread message indicator -->
    @if($session->messages()->where('sender_type', 'visitor')->where('is_read', false)->exists())
        <div class="mt-2 flex items-center space-x-2">
            <div class="w-2 h-2 bg-red-500 rounded-full animate-bounce unread-indicator"></div>
            <span class="text-xs text-red-600 dark:text-red-400 font-medium">New messages</span>
        </div>
    @endif
</div>