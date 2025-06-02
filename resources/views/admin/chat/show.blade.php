{{-- resources/views/admin/chat/show.blade.php - Updated with Real-time Polling --}}
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
                Started {{ $chatSession->started_at->diffForHumans() }}
                @if($chatSession->ended_at)
                    • Ended {{ $chatSession->ended_at->diffForHumans() }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Connection Status Indicator -->
            <div id="connection-status" class="flex items-center space-x-2" style="display: none;">
                <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                <span class="text-xs text-yellow-600 dark:text-yellow-400">Connecting...</span>
            </div>
            
            <x-admin.badge :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')" id="session-status">
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
            <x-admin.card>
                <x-slot name="title">
                    <div class="flex items-center justify-between w-full px-2 pt-2">
                        <span>Chat Messages </span>
                        <div class="flex items-center space-x-3">
                            <!-- Live Indicator -->
                            @if($chatSession->status === 'active')
                                <div class="flex items-center space-x-1 ml-1 text-green-600 dark:text-green-400" id="live-indicator">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                </div>
                            @endif
                            
                            <!-- Auto-scroll Toggle -->
                            <button id="auto-scroll-toggle" 
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xs px-2 py-1 rounded border"
                                    title="Toggle auto-scroll">
                                <span id="auto-scroll-text">Auto-scroll: ON</span>
                            </button>
                            
                            <!-- Scroll to Bottom -->
                            <button onclick="scrollToBottom()" 
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    title="Scroll to bottom">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </x-slot>
                
                <!-- Messages Container -->
                <div id="messages-container" 
                     class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900" 
                     style="scroll-behavior: smooth;">
                    
                    <!-- Loading Indicator -->
                    <div id="loading-indicator" class="text-center py-4" style="display: none;">
                        <div class="inline-flex items-center space-x-2">
                            <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce"></div>
                            <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                    
                    <!-- Messages will be populated here -->
                    <div id="messages-list">
                        <x-admin.chat-message :messages="$chatSession->messages" />
                        
                    </div>
                    
                    <!-- Typing Indicator -->
                    <div id="typing-indicator" class="flex items-start space-x-3" style="display: none;">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg rounded-tl-none px-3 py-2 max-w-xs shadow-sm">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="typing-user">Visitor is typing...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Message Input (only if session is not closed) -->
                @if($chatSession->status !== 'closed')
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                        <form id="reply-form" class="space-y-3">
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
                                    <button type="submit" 
                                            id="send-button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                            disabled>
                                        <svg id="send-icon" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        <div id="send-spinner" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1" style="display: none;"></div>
                                        <span id="send-text">Send</span>
                                    </button>
                                </div>
                            </div>
                            
                            <x-admin.chat-quick-response />
                            
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
                        <x-admin.badge :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')" id="sidebar-session-status">
                            {{ ucfirst($chatSession->status) }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Priority</span>
                        <x-admin.badge :type="$chatSession->priority === 'urgent' ? 'danger' : ($chatSession->priority === 'high' ? 'warning' : 'info')" id="session-priority">
                            {{ ucfirst($chatSession->priority) }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Started</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->started_at->format('M j, H:i') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Last Activity</span>
                        <span class="text-sm text-gray-900 dark:text-white" id="last-activity">
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
                        <span class="text-sm text-gray-900 dark:text-white" id="message-count">{{ $chatSession->messages->count() }}</span>
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
                        <button id="assign-to-me-btn" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Assign to Me
                        </button>
                    @endif
                    
                    @if($chatSession->status !== 'closed')
                        <button id="close-session-btn" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Close Chat
                        </button>
                    @endif
                    
                    <!-- Priority controls -->
                    @if($chatSession->status !== 'closed')
                        <div class="border-t pt-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                            <select id="priority-select" 
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                <option value="low" {{ $chatSession->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ $chatSession->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ $chatSession->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $chatSession->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Session Notes -->
            <x-admin.card title="Session Notes">
                <form id="notes-form">
                    @csrf
                    <textarea 
                        name="summary" 
                        id="session-notes"
                        rows="4" 
                        class="w-full border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white text-sm"
                        placeholder="Add notes about this chat session...">{{ $chatSession->summary }}</textarea>
                    <div class="mt-2">
                        <button type="submit" id="save-notes-btn" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                            Save Notes
                        </button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>

    @push('styles')
    <style>
        .quick-response-btn {
            @apply inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600;
        }
        
        .message-fade-in {
            animation: fadeInUp 0.3s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .notification-flash {
            animation: flash 0.5s ease-in-out;
        }
        
        @keyframes flash {
            0%, 100% { background-color: transparent; }
            50% { background-color: rgba(59, 130, 246, 0.1); }
        }
    </style>
    @endpush

     @push('styles')
    <style>
        .quick-response-btn {
            @apply inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600;
        }
        
        .message-fade-in {
            animation: fadeInUp 0.3s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .notification-flash {
            animation: flash 0.5s ease-in-out;
        }
        
        @keyframes flash {
            0%, 100% { background-color: transparent; }
            50% { background-color: rgba(59, 130, 246, 0.1); }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Real-time Chat Handler for Admin
        class AdminChatHandler {
            constructor() {
                this.sessionId = '{{ $chatSession->session_id }}';
                this.chatSessionDbId = {{ $chatSession->id }};
                this.lastMessageId = {{ $chatSession->messages->max('id') ?? 0 }};
                this.isPolling = false;
                this.pollingInterval = null;
                this.currentPollingDelay = 2000; // Start with 2 seconds
                this.maxPollingDelay = 10000; // Max 10 seconds
                this.minPollingDelay = 1000; // Min 1 second
                this.connectionErrors = 0;
                this.maxConnectionErrors = 5;
                this.autoScroll = true;
                this.isTyping = false;
                this.typingTimer = null;
                this.isSending = false;
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.startPolling();
                this.scrollToBottom();
                
                // Mark messages as read on load
                this.markMessagesAsRead();
                
                // Setup page visibility handling
                this.setupVisibilityHandling();
            }
            
            setupEventListeners() {
                // Form submission
                const replyForm = document.getElementById('reply-form');
                const messageInput = document.getElementById('message-input');
                const sendButton = document.getElementById('send-button');
                
                if (replyForm) {
                    replyForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.sendMessage();
                    });
                }
                
                if (messageInput) {
                    // Enable send button when there's text
                    messageInput.addEventListener('input', () => {
                        const hasText = messageInput.value.trim().length > 0;
                        sendButton.disabled = !hasText || this.isSending;
                        
                        // Handle typing indicator
                        this.handleTyping();
                    });
                    
                    // Enter key handling
                    messageInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            if (!sendButton.disabled) {
                                this.sendMessage();
                            }
                        }
                    });
                    
                    // Auto-resize textarea
                    messageInput.addEventListener('input', () => {
                        messageInput.style.height = 'auto';
                        messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
                    });
                    
                    // Stop typing when input loses focus
                    messageInput.addEventListener('blur', () => {
                        this.stopTyping();
                    });
                }
                
                // Auto-scroll toggle
                const autoScrollToggle = document.getElementById('auto-scroll-toggle');
                if (autoScrollToggle) {
                    autoScrollToggle.addEventListener('click', () => {
                        this.autoScroll = !this.autoScroll;
                        document.getElementById('auto-scroll-text').textContent = 
                            `Auto-scroll: ${this.autoScroll ? 'ON' : 'OFF'}`;
                        autoScrollToggle.classList.toggle('bg-blue-100', this.autoScroll);
                    });
                }
                
                // Quick action buttons
                this.setupQuickActions();
                
                // Priority change
                const prioritySelect = document.getElementById('priority-select');
                if (prioritySelect) {
                    prioritySelect.addEventListener('change', () => {
                        this.updatePriority(prioritySelect.value);
                    });
                }
                
                // Notes form
                const notesForm = document.getElementById('notes-form');
                if (notesForm) {
                    notesForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.saveNotes();
                    });
                }
            }
            
            setupQuickActions() {
                // Assign to me
                const assignBtn = document.getElementById('assign-to-me-btn');
                if (assignBtn) {
                    assignBtn.addEventListener('click', () => {
                        this.assignToMe();
                    });
                }
                
                // Close session
                const closeBtn = document.getElementById('close-session-btn');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        if (confirm('Are you sure you want to close this chat session?')) {
                            this.closeSession();
                        }
                    });
                }
            }
            
            setupVisibilityHandling() {
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        this.reducePollingFrequency();
                    } else {
                        this.increasePollingFrequency();
                        this.pollMessages(); // Immediate poll when page becomes visible
                    }
                });
                
                // Handle online/offline events
                window.addEventListener('online', () => {
                    this.connectionErrors = 0;
                    this.currentPollingDelay = this.minPollingDelay;
                    this.hideConnectionStatus();
                    this.pollMessages();
                });
                
                window.addEventListener('offline', () => {
                    this.showConnectionStatus('Offline', 'red');
                });
            }
            
            startPolling() {
                if (this.isPolling) return;
                
                this.isPolling = true;
                this.pollingInterval = setInterval(() => {
                    this.pollMessages();
                }, this.currentPollingDelay);
                
                console.log('Started polling with delay:', this.currentPollingDelay);
            }
            
            stopPolling() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                    this.pollingInterval = null;
                }
                this.isPolling = false;
                console.log('Stopped polling');
            }
            
            async pollMessages() {
                try {
                    const url = new URL(`/admin/chat/${this.chatSessionDbId}/poll-messages`, window.location.origin);
                    if (this.lastMessageId) {
                        url.searchParams.append('last_message_id', this.lastMessageId);
                    }
                    
                    const response = await fetch(url, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.connectionErrors = 0;
                        this.hideConnectionStatus();
                        
                        // Process new messages
                        if (data.has_new_messages && data.messages.length > 0) {
                            this.processNewMessages(data.messages);
                            this.lastMessageId = data.last_message_id;
                            this.adaptivePolling(true); // Increase frequency for active chats
                        } else {
                            this.adaptivePolling(false); // Decrease frequency for inactive chats
                        }
                        
                        // Update session status if changed
                        if (data.session_status) {
                            this.updateSessionStatus(data.session_status);
                        }
                    } else {
                        throw new Error(data.message || 'Polling failed');
                    }
                    
                } catch (error) {
                    console.error('Polling error:', error);
                    this.handlePollingError();
                }
            }
            
            processNewMessages(messages) {
                const messagesList = document.getElementById('messages-list');
                let hasNewOperatorMessages = false;
                let newMessagesAdded = 0;
                
                messages.forEach(message => {
                    // Check if message already exists to prevent duplicates
                    if (!this.messageExists(message.id)) {
                        const messageElement = this.createMessageElement(message);
                        messageElement.setAttribute('data-message-id', message.id);
                        messagesList.appendChild(messageElement);
                        
                        // Add animation
                        messageElement.classList.add('message-fade-in');
                        
                        // Check for visitor messages for sound notification
                        if (message.sender_type === 'visitor') {
                            hasNewOperatorMessages = true;
                            this.flashNotification();
                        }
                        
                        newMessagesAdded++;
                    }
                });
                
                // Only update UI if new messages were actually added
                if (newMessagesAdded > 0) {
                    // Update message count
                    this.updateMessageCount();
                    
                    // Auto-scroll if enabled
                    if (this.autoScroll) {
                        this.scrollToBottom();
                    }
                    
                    // Play notification sound for new visitor messages
                    if (hasNewOperatorMessages) {
                        this.playNotificationSound();
                    }
                }
            }
            
            messageExists(messageId) {
                return document.querySelector(`[data-message-id="${messageId}"]`) !== null;
            }
            
            createMessageElement(message) {
                const div = document.createElement('div');
                div.className = `flex ${message.is_from_visitor ? 'justify-end' : 'justify-start'}`;
                div.setAttribute('data-message-id', message.id);
                
                const isFromBot = message.sender_type === 'bot';
                const isFromSystem = message.sender_type === 'system';
                
                let bubbleClass, senderIcon;
                
                if (message.is_from_visitor) {
                    bubbleClass = 'bg-blue-600 text-white rounded-br-none';
                    senderIcon = '👤';
                } else if (isFromBot) {
                    bubbleClass = 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-bl-none';
                    senderIcon = '🤖';
                } else if (isFromSystem) {
                    bubbleClass = 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-lg italic text-center text-sm';
                    senderIcon = 'ℹ️';
                } else {
                    bubbleClass = 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-bl-none';
                    senderIcon = '👨‍💼';
                }
                
                div.innerHTML = `
                    <div class="max-w-xs lg:max-w-md">
                        <div class="px-4 py-2 rounded-lg ${bubbleClass}">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-xs">${senderIcon}</span>
                                <span class="text-xs font-medium ${message.is_from_visitor ? 'text-blue-100' : 'opacity-75'}">
                                    ${message.sender_name}
                                </span>
                                <span class="text-xs ${message.is_from_visitor ? 'text-blue-200' : 'opacity-50'}">
                                    ${message.formatted_time}
                                </span>
                            </div>
                            <div class="text-sm">
                                ${isFromSystem ? `<em>${message.message}</em>` : message.message}
                            </div>
                            ${message.is_from_visitor && message.is_read ? '<div class="text-right mt-1"><span class="text-xs text-blue-200">✓ Read</span></div>' : ''}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 ${message.is_from_visitor ? 'text-right' : 'text-left'}">
                            ${new Date(message.created_at).toLocaleString()}
                        </div>
                    </div>
                `;
                
                return div;
            }
            
            adaptivePolling(isActive) {
                const oldDelay = this.currentPollingDelay;
                
                if (isActive) {
                    // Increase frequency for active conversations
                    this.currentPollingDelay = Math.max(this.minPollingDelay, this.currentPollingDelay * 0.8);
                } else {
                    // Gradually decrease frequency for inactive conversations
                    this.currentPollingDelay = Math.min(this.maxPollingDelay, this.currentPollingDelay * 1.2);
                }
                
                // Restart polling with new interval if changed significantly
                if (Math.abs(oldDelay - this.currentPollingDelay) > 500) {
                    this.stopPolling();
                    this.startPolling();
                }
            }
            
            reducePollingFrequency() {
                this.currentPollingDelay = this.maxPollingDelay;
                this.stopPolling();
                this.startPolling();
            }
            
            increasePollingFrequency() {
                this.currentPollingDelay = this.minPollingDelay;
                this.stopPolling();
                this.startPolling();
            }
            
            handlePollingError() {
                this.connectionErrors++;
                
                if (this.connectionErrors >= this.maxConnectionErrors) {
                    this.showConnectionStatus('Connection lost', 'red');
                    this.stopPolling();
                    
                    // Try to reconnect after delay
                    setTimeout(() => {
                        if (this.connectionErrors >= this.maxConnectionErrors) {
                            this.connectionErrors = 0;
                            this.startPolling();
                        }
                    }, 5000);
                } else {
                    this.showConnectionStatus('Reconnecting...', 'yellow');
                    // Exponential backoff
                    this.currentPollingDelay = Math.min(this.maxPollingDelay, this.currentPollingDelay * 2);
                }
            }
            
            showConnectionStatus(message, color) {
                const statusEl = document.getElementById('connection-status');
                if (statusEl) {
                    statusEl.style.display = 'flex';
                    statusEl.querySelector('span').textContent = message;
                    statusEl.querySelector('div').className = `w-2 h-2 rounded-full animate-pulse bg-${color}-400`;
                }
            }
            
            hideConnectionStatus() {
                const statusEl = document.getElementById('connection-status');
                if (statusEl) {
                    statusEl.style.display = 'none';
                }
            }
            
            async sendMessage() {
                const messageInput = document.getElementById('message-input');
                const message = messageInput.value.trim();
                
                if (!message || this.isSending) return;
                
                this.isSending = true;
                this.updateSendButton(true);
                
                try {
                    const response = await fetch(`/admin/chat/${this.chatSessionDbId}/reply`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ message })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        messageInput.value = '';
                        messageInput.style.height = 'auto';
                        this.stopTyping();
                        
                        // If the response includes the message, add it immediately
                        if (data.message) {
                            // Check if message doesn't already exist before adding
                            if (!this.messageExists(data.message.id)) {
                                const messageElement = this.createMessageElement(data.message);
                                messageElement.setAttribute('data-message-id', data.message.id);
                                document.getElementById('messages-list').appendChild(messageElement);
                                
                                // Update last message ID to prevent duplication in polling
                                this.lastMessageId = Math.max(this.lastMessageId, data.message.id);
                                
                                // Update UI
                                this.updateMessageCount();
                                if (this.autoScroll) {
                                    this.scrollToBottom();
                                }
                            }
                        }
                        
                        // Still poll for any other messages that might have come in
                        setTimeout(() => this.pollMessages(), 500);
                        
                        // Increase polling frequency temporarily
                        this.adaptivePolling(true);
                    } else {
                        throw new Error(data.message || 'Failed to send message');
                    }
                    
                } catch (error) {
                    console.error('Send message error:', error);
                    this.showError('Failed to send message');
                } finally {
                    this.isSending = false;
                    this.updateSendButton(false);
                    messageInput.focus();
                }
            }
            
            updateSendButton(sending) {
                const sendButton = document.getElementById('send-button');
                const sendIcon = document.getElementById('send-icon');
                const sendSpinner = document.getElementById('send-spinner');
                const sendText = document.getElementById('send-text');
                const messageInput = document.getElementById('message-input');
                
                if (sending) {
                    sendButton.disabled = true;
                    sendIcon.style.display = 'none';
                    sendSpinner.style.display = 'block';
                    sendText.textContent = 'Sending...';
                } else {
                    sendIcon.style.display = 'block';
                    sendSpinner.style.display = 'none';
                    sendText.textContent = 'Send';
                    sendButton.disabled = !messageInput.value.trim();
                }
            }
            
            handleTyping() {
                if (!this.isTyping) {
                    this.isTyping = true;
                    this.sendTypingIndicator(true);
                }
                
                // Clear existing timer
                if (this.typingTimer) {
                    clearTimeout(this.typingTimer);
                }
                
                // Stop typing after 3 seconds
                this.typingTimer = setTimeout(() => {
                    this.stopTyping();
                }, 3000);
            }
            
            stopTyping() {
                if (this.isTyping) {
                    this.isTyping = false;
                    this.sendTypingIndicator(false);
                }
                
                if (this.typingTimer) {
                    clearTimeout(this.typingTimer);
                    this.typingTimer = null;
                }
            }
            
            async sendTypingIndicator(isTyping) {
                try {
                    await fetch(`/admin/chat/${this.chatSessionDbId}/typing`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ is_typing: isTyping })
                    });
                } catch (error) {
                    console.error('Failed to send typing indicator:', error);
                }
            }
            
            async markMessagesAsRead() {
                try {
                    await fetch(`/admin/chat/${this.chatSessionDbId}/mark-messages-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    });
                } catch (error) {
                    console.error('Failed to mark messages as read:', error);
                }
            }
            
            async assignToMe() {
                try {
                    const response = await fetch(`/admin/chat/${this.chatSessionDbId}/assign-to-me`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showSuccess('Chat session assigned to you');
                        // Refresh the page or update UI
                        window.location.reload();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error('Assign error:', error);
                    this.showError('Failed to assign chat session');
                }
            }
            
            async closeSession() {
                try {
                    const response = await fetch(`/admin/chat/${this.chatSessionDbId}/close-session`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    });
                    
                    if (response.ok) {
                        this.showSuccess('Chat session closed');
                        this.stopPolling();
                        // Redirect after short delay
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.chat.index") }}';
                        }, 1000);
                    } else {
                        throw new Error('Failed to close session');
                    }
                } catch (error) {
                    console.error('Close session error:', error);
                    this.showError('Failed to close chat session');
                }
            }
            
            async updatePriority(priority) {
                try {
                    const response = await fetch(`/admin/chat/${this.chatSessionDbId}/priority`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ priority })
                    });
                    
                    if (response.ok) {
                        this.showSuccess('Priority updated');
                        // Update UI elements
                        this.updatePriorityDisplay(priority);
                    } else {
                        throw new Error('Failed to update priority');
                    }
                } catch (error) {
                    console.error('Update priority error:', error);
                    this.showError('Failed to update priority');
                }
            }
            
            async saveNotes() {
                const notesTextarea = document.getElementById('session-notes');
                const notes = notesTextarea.value;
                
                try {
                    const response = await fetch(`/admin/chat/${this.chatSessionDbId}/notes`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ summary: notes })
                    });
                    
                    if (response.ok) {
                        this.showSuccess('Notes saved');
                    } else {
                        throw new Error('Failed to save notes');
                    }
                } catch (error) {
                    console.error('Save notes error:', error);
                    this.showError('Failed to save notes');
                }
            }
            
            updateSessionStatus(status) {
                const statusElements = document.querySelectorAll('#session-status, #sidebar-session-status');
                statusElements.forEach(el => {
                    el.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    // Update badge colors based on status
                    el.className = el.className.replace(/bg-\w+-\d+/g, '');
                    if (status === 'active') {
                        el.classList.add('bg-green-100', 'text-green-800');
                    } else if (status === 'waiting') {
                        el.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else {
                        el.classList.add('bg-red-100', 'text-red-800');
                    }
                });
                
                // Update live indicator
                const liveIndicator = document.getElementById('live-indicator');
                if (liveIndicator) {
                    liveIndicator.style.display = status === 'active' ? 'flex' : 'none';
                }
            }
            
            updatePriorityDisplay(priority) {
                const priorityElement = document.getElementById('session-priority');
                if (priorityElement) {
                    priorityElement.textContent = priority.charAt(0).toUpperCase() + priority.slice(1);
                    // Update badge colors
                    priorityElement.className = priorityElement.className.replace(/bg-\w+-\d+/g, '');
                    if (priority === 'urgent') {
                        priorityElement.classList.add('bg-red-100', 'text-red-800');
                    } else if (priority === 'high') {
                        priorityElement.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else {
                        priorityElement.classList.add('bg-blue-100', 'text-blue-800');
                    }
                }
            }
            
            updateMessageCount() {
                const messageElements = document.querySelectorAll('#messages-list > div');
                const countElement = document.getElementById('message-count');
                if (countElement) {
                    countElement.textContent = messageElements.length;
                }
            }
            
            scrollToBottom() {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
            
            flashNotification() {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.classList.add('notification-flash');
                    setTimeout(() => {
                        container.classList.remove('notification-flash');
                    }, 500);
                }
            }
            
            playNotificationSound() {
                try {
                    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvGUeBT2U2fPGdSYELYHM89yJOQcZZ7zs5Z9NEAxPqOTvt2MdBjiR2O/NeSsFJHfI8N+QQAoUXrPq66hWFAlFnt/xvWYfBT2U2/PHdSUELYDL89uKOQgZZ7vs5qBOEAxOpuPwuGQdBTiP2PDPeSsFJHbH8OCSQgoTXbPq7KlXFAlFnt/wvmcfBTyU3PLIdCUELYDK89uLOggZZrvr56BOEQxOpuLvuWUdBTiP2fDQeSoFJHbH8OGTRQ==');
                    audio.volume = 0.3;
                    audio.play().catch(() => {}); // Ignore errors
                } catch (error) {
                    // Silently ignore audio errors
                }
            }
            
            showSuccess(message) {
                this.showNotification(message, 'success');
            }
            
            showError(message) {
                this.showNotification(message, 'error');
            }
            
            showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-3 rounded-lg shadow-lg text-white max-w-sm ${
                    type === 'error' ? 'bg-red-500' : 
                    type === 'success' ? 'bg-green-500' : 'bg-blue-500'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
            
            destroy() {
                this.stopPolling();
                if (this.typingTimer) {
                    clearTimeout(this.typingTimer);
                }
            }
        }
        
        // Global functions for quick responses and scroll
        function insertQuickResponse(text) {
            const textarea = document.getElementById('message-input');
            if (textarea) {
                textarea.value = text;
                textarea.focus();
                // Trigger input event to enable send button
                textarea.dispatchEvent(new Event('input'));
            }
        }
        
        function scrollToBottom() {
            if (window.adminChatHandler) {
                window.adminChatHandler.scrollToBottom();
            }
        }
        
        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize if session is not closed
            @if($chatSession->status !== 'closed')
            window.adminChatHandler = new AdminChatHandler();
            @endif
            
            // Focus message input
            const messageInput = document.getElementById('message-input');
            if (messageInput) {
                setTimeout(() => messageInput.focus(), 500);
            }
        });
        
        // Cleanup when page unloads
        window.addEventListener('beforeunload', () => {
            if (window.adminChatHandler) {
                window.adminChatHandler.destroy();
            }
        });
    </script>
    @endpush
</x-layouts.admin>