{{-- resources/views/admin/chat/show.blade.php --}}
<x-layouts.admin title="Chat Session">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Live Chat' => route('admin.chat.index'), 
        'Chat with ' . $chatSession->getVisitorName() => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Chat with {{ $chatSession->getVisitorName() }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Session: {{ $chatSession->session_id }} • 
                Started {{ $chatSession->started_at->diffForHumans() }}
                @if($chatSession->ended_at)
                    • Ended {{ $chatSession->ended_at->diffForHumans() }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-admin.badge :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')">
                {{ ucfirst($chatSession->status) }}
            </x-admin.badge>
            
            @if($chatSession->priority === 'urgent')
                <x-admin.badge type="danger">🚨 Urgent</x-admin.badge>
            @elseif($chatSession->priority === 'high')
                <x-admin.badge type="warning">⚡ High Priority</x-admin.badge>
            @endif
            
            <x-admin.button color="light" href="{{ route('admin.chat.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </x-admin.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Chat Messages (Main Content) -->
        <div class="lg:col-span-3">
            <x-admin.card noPadding>
                <x-slot name="title">
                    <div class="flex items-center justify-between w-full">
                        <span>Chat Messages</span>
                        <div class="flex items-center space-x-2">
                            @if($chatSession->status === 'active')
                                <div class="flex items-center space-x-1 text-green-600 dark:text-green-400">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-medium">Live</span>
                                </div>
                            @endif
                            <button onclick="scrollToBottom()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </x-slot>
                
                <!-- Messages Container -->
                <div id="messages-container" class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900" 
                     style="scroll-behavior: smooth;">
                    @forelse($chatSession->messages as $message)
                        <div class="flex {{ $message->isFromVisitor() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md">
                                <!-- Message bubble -->
                                <div class="px-4 py-2 rounded-lg {{ $message->isFromVisitor() ? 'bg-blue-600 text-white rounded-br-none' : ($message->isFromBot() ? 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-bl-none' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-bl-none') }}">
                                    
                                    <!-- Sender info -->
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-xs">
                                            @if($message->isFromVisitor())
                                                👤
                                            @elseif($message->isFromBot())
                                                🤖
                                            @elseif($message->sender_type === 'system')
                                                ℹ️
                                            @else
                                                👨‍💼
                                            @endif
                                        </span>
                                        <span class="text-xs font-medium {{ $message->isFromVisitor() ? 'text-blue-100' : 'opacity-75' }}">
                                            {{ $message->getSenderName() }}
                                        </span>
                                        <span class="text-xs {{ $message->isFromVisitor() ? 'text-blue-200' : 'opacity-50' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                    
                                    <!-- Message content -->
                                    <div class="text-sm">
                                        @if($message->message_type === 'system')
                                            <em>{{ $message->message }}</em>
                                        @else
                                            {{ $message->message }}
                                        @endif
                                    </div>
                                    
                                    <!-- Message status -->
                                    @if($message->isFromVisitor() && $message->is_read)
                                        <div class="text-right mt-1">
                                            <span class="text-xs text-blue-200">✓ Read</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Timestamp -->
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message->isFromVisitor() ? 'text-right' : 'text-left' }}">
                                    {{ $message->created_at->format('M j, H:i:s') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                            <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p>No messages yet</p>
                            <p class="text-xs mt-1">Messages will appear here when the conversation starts</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Message Input (only if session is active) -->
                @if($chatSession->status !== 'closed')
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                        <form id="reply-form" action="{{ route('admin.chat.reply', $chatSession) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="flex space-x-3">
                                <textarea 
                                    name="message" 
                                    id="message-input"
                                    rows="2" 
                                    class="flex-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                                    placeholder="Type your message..."
                                    required></textarea>
                                <div class="flex flex-col space-y-2">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        Send
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Quick responses -->
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="insertQuickResponse('Thank you for contacting us. How can I help you today?')" class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    Quick: Greeting
                                </button>
                                <button type="button" onclick="insertQuickResponse('I understand your concern. Let me check that for you.')" class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    Quick: Acknowledge
                                </button>
                                <button type="button" onclick="insertQuickResponse('Is there anything else I can help you with today?')" class="inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    Quick: Follow-up
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">This chat session has been closed</p>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <!-- Session Info Sidebar -->
        <div class="space-y-6">
            <!-- Visitor Information -->
            <x-admin.card title="Visitor Information">
                <div class="space-y-4">
                    <!-- Avatar and basic info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($chatSession->getVisitorName(), 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $chatSession->getVisitorName() }}</h3>
                            @if($chatSession->getVisitorEmail())
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $chatSession->getVisitorEmail() }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Contact details -->
                    <div class="space-y-3">
                        @if($chatSession->visitor_info && isset($chatSession->visitor_info['phone']))
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->visitor_info['phone'] }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                            </svg>
                            <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($chatSession->source) }}</span>
                        </div>
                        
                        @if($chatSession->user)
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm text-green-600 dark:text-green-400 font-medium">Registered User</span>
                            </div>
                        @endif
                    </div>
                </div>
            </x-admin.card>

            <!-- Session Details -->
            <x-admin.card title="Session Details">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                        <x-admin.badge :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')">
                            {{ ucfirst($chatSession->status) }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Priority</span>
                        <x-admin.badge :type="$chatSession->priority === 'urgent' ? 'danger' : ($chatSession->priority === 'high' ? 'warning' : 'info')">
                            {{ ucfirst($chatSession->priority) }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Started</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->started_at->format('M j, H:i') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Last Activity</span>
                        <span class="text-sm text-gray-900 dark:text-white">
                            {{ $chatSession->last_activity_at ? $chatSession->last_activity_at->diffForHumans() : 'N/A' }}
                        </span>
                    </div>
                    
                    @if($chatSession->ended_at)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ended</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->ended_at->format('M j, H:i') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Duration</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->getDuration() }} minutes</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Messages</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->messages->count() }}</span>
                    </div>
                    
                    @if($chatSession->operator)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Assigned to</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->operator->name }}</span>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-3">
                    @if($chatSession->getVisitorEmail())
                        <x-admin.button color="primary" class="w-full" 
                            href="mailto:{{ $chatSession->getVisitorEmail() }}?subject=Follow-up from chat session {{ $chatSession->session_id }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Email
                        </x-admin.button>
                    @endif
                    
                    @if($chatSession->status === 'waiting')
                        <form action="{{ route('admin.chat.assign', $chatSession) }}" method="POST" class="w-full">
                            @csrf
                            <x-admin.button type="submit" color="success" class="w-full">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Assign to Me
                            </x-admin.button>
                        </form>
                    @endif
                    
                    @if($chatSession->status !== 'closed')
                        <form action="{{ route('admin.chat.close', $chatSession) }}" method="POST" class="w-full">
                            @csrf
                            <x-admin.button type="submit" color="warning" class="w-full"
                                onclick="return confirm('Are you sure you want to close this chat session?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Close Chat
                            </x-admin.button>
                        </form>
                    @endif
                    
                    <!-- Priority controls -->
                    @if($chatSession->status !== 'closed')
                        <div class="border-t pt-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                            <form action="{{ route('admin.chat.priority', $chatSession) }}" method="POST" class="flex space-x-2">
                                @csrf
                                <select name="priority" onchange="this.form.submit()" class="flex-1 text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                    <option value="low" {{ $chatSession->priority === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ $chatSession->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ $chatSession->priority === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ $chatSession->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </form>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Session Notes -->
            <x-admin.card title="Session Notes">
                <form action="{{ route('admin.chat.notes', $chatSession) }}" method="POST">
                    @csrf
                    <textarea 
                        name="summary" 
                        rows="4" 
                        class="w-full border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white text-sm"
                        placeholder="Add notes about this chat session...">{{ $chatSession->summary }}</textarea>
                    <div class="mt-2">
                        <x-admin.button type="submit" size="sm" color="light">
                            Save Notes
                        </x-admin.button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-scroll to bottom of messages
        function scrollToBottom() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
        
        // Scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
        });
        
        // Auto-refresh for active chats
        @if($chatSession->status === 'active')
            let refreshInterval = setInterval(function() {
                // Only refresh if the page is visible
                if (!document.hidden) {
                    const currentScrollTop = document.getElementById('messages-container').scrollTop;
                    const currentScrollHeight = document.getElementById('messages-container').scrollHeight;
                    
                    // Check for new messages via AJAX instead of full page reload
                    fetch(`{{ route('admin.chat.messages', $chatSession) }}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.new_messages) {
                                // Update messages without full reload
                                // Implementation would depend on your specific needs
                                window.location.reload();
                            }
                        })
                        .catch(error => console.log('Refresh failed:', error));
                }
            }, 10000); // Refresh every 10 seconds for active chats
            
            // Clear interval when page is unloaded
            window.addEventListener('beforeunload', function() {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            });
        @endif
        
        // Quick response insertion
        function insertQuickResponse(text) {
            const textarea = document.getElementById('message-input');
            textarea.value = text;
            textarea.focus();
        }
        
        // Handle Enter key for sending message
        document.getElementById('message-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('reply-form').submit();
            }
        });
        
        // Show typing indicator when admin is typing
        let typingTimer;
        document.getElementById('message-input').addEventListener('input', function() {
            clearTimeout(typingTimer);
            
            // Send typing indicator to visitor
            fetch(`{{ route('admin.chat.typing', $chatSession) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ typing: true })
            });
            
            // Stop typing indicator after 3 seconds of no typing
            typingTimer = setTimeout(() => {
                fetch(`{{ route('admin.chat.typing', $chatSession) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ typing: false })
                });
            }, 3000);
        });
        
        // Auto-resize textarea
        const textarea = document.getElementById('message-input');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        // Focus message input when page loads (if session is active)
        @if($chatSession->status !== 'closed')
            setTimeout(() => {
                document.getElementById('message-input').focus();
            }, 500);
        @endif
    </script>
    @endpush
</x-layouts.admin>