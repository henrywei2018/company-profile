{{-- resources/views/components/chat-widget.blade.php --}}
@props([
    'position' => 'bottom-right',
    'theme' => 'primary', 
    'size' => 'normal',
    'autoOpen' => false,
    'showOnlineStatus' => true,
    'enableSound' => true,
    'welcomeMessage' => 'Hello! How can we help you today?',
    'operatorName' => 'Support Team',
    'companyName' => 'CV Usaha Prima Lestari',
    'offsetFromQuickAction' => true
])

@php
    $positionClasses = [
        'bottom-right' => $offsetFromQuickAction ? 'bottom-6 right-3 sm:bottom-16 sm:right-3.5' : 'bottom-4 right-4 sm:bottom-6 sm:right-6',
        'bottom-left' => 'bottom-4 left-4 sm:bottom-6 sm:left-6',
        'top-right' => 'top-4 right-4 sm:top-6 sm:right-6',
        'top-left' => 'top-4 left-4 sm:top-6 sm:left-6',
    ];

    $themeClasses = [
        'primary' => [
            'button' => 'bg-blue-600 hover:bg-blue-700 text-white border-blue-600',
            'header' => 'bg-blue-600 text-white',
            'accent' => 'text-blue-600',
            'message_user' => 'bg-blue-600 text-white',
            'message_operator' => 'bg-gray-100 text-gray-900',
        ],
        'client' => [
            'button' => 'bg-green-600 hover:bg-green-700 text-white border-green-600',
            'header' => 'bg-green-600 text-white',
            'accent' => 'text-green-600',
            'message_user' => 'bg-green-600 text-white',
            'message_operator' => 'bg-gray-100 text-gray-900',
        ]
    ];

    $sizeClasses = [
        'compact' => [
            'button' => 'w-10 h-10',
            'window' => 'w-80 h-96',
            'icon' => 'w-4 h-4',
        ],
        'normal' => [
            'button' => 'w-14 h-14 sm:w-16 sm:h-16',
            'window' => 'w-80 sm:w-96 h-96 sm:h-[32rem]',
            'icon' => 'w-5 h-5',
        ]
    ];

    $currentTheme = $themeClasses[$theme] ?? $themeClasses['primary'];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
    $currentPosition = $positionClasses[$position] ?? $positionClasses['bottom-right'];
@endphp

<!-- Chat Widget Container -->
<div id="chat-widget" 
     class="fixed {{ $currentPosition }} z-40 font-sans"
     data-auto-open="{{ $autoOpen ? 'true' : 'false' }}"
     data-enable-sound="{{ $enableSound ? 'true' : 'false' }}"
     data-theme="{{ $theme }}"
     x-data="chatWidget()"
     x-init="init()">
    
    <!-- Chat Toggle Button -->
    <div class="relative" 
         x-show="!isOpen" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95">
         
        <!-- Notification Badge -->
        <div x-show="unreadCount > 0" 
             x-text="unreadCount"
             class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5 z-10 shadow-lg animate-pulse">
        </div>
        
        <!-- Online Status Indicator -->
        @if($showOnlineStatus)
        <div class="absolute -top-1 -right-1 z-10">
            <div x-show="isOnline" 
                 class="w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm">
                <div class="w-full h-full bg-green-500 rounded-full animate-ping opacity-75"></div>
            </div>
            <div x-show="!isOnline" 
                 class="w-4 h-4 bg-gray-400 border-2 border-white rounded-full shadow-sm">
            </div>
        </div>
        @endif
        
        <!-- Main Chat Button -->
        <button @click="openChat()" 
                class="{{ $currentSize['button'] }} {{ $currentTheme['button'] }} rounded-full border-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center justify-center group relative overflow-hidden hover:ring-2 hover:ring-blue-500/20 hover:ring-offset-1"
                title="Start chat conversation"
                aria-label="Open chat">
            
            <!-- Chat Icon -->
            <svg class="{{ $currentSize['icon'] }} transition-all duration-200" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            
            <!-- Ripple Effect -->
            <div class="absolute inset-0 rounded-full opacity-0 group-hover:opacity-20 bg-white transition-opacity duration-200"></div>
        </button>
    </div>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="{{ $currentSize['window'] }} bg-white rounded-t-2xl shadow-2xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 mb-4 flex flex-col overflow-hidden">
        
        <!-- Chat Header -->
        <div class="{{ $currentTheme['header'] }} px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Operator Avatar -->
                <div class="relative">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    @if($showOnlineStatus)
                    <div x-show="isOnline" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border border-white rounded-full"></div>
                    @endif
                </div>
                
                <!-- Operator Info -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm truncate" x-text="operatorName">{{ $operatorName }}</h3>
                    <p class="text-xs opacity-90">
                        <span x-show="isOnline">Online • Typically replies instantly</span>
                        <span x-show="!isOnline">Offline • We'll reply as soon as possible</span>
                    </p>
                </div>
            </div>
            
            <!-- Header Actions -->
            <div class="flex items-center space-x-2">
                <!-- Close Button -->
                <button @click="closeChat()" 
                        class="w-7 h-7 rounded-full hover:bg-white hover:bg-opacity-20 flex items-center justify-center transition-colors duration-200"
                        title="Close chat"
                        aria-label="Close chat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Connection Status -->
        <div x-show="connectionStatus !== 'connected'" 
             class="px-4 py-2 bg-yellow-50 border-b border-yellow-200 text-yellow-800 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                <span x-text="getConnectionStatusText()">Connecting...</span>
            </div>
        </div>
        
        <!-- Chat Messages -->
        <div id="chat-messages" 
             class="flex-1 overflow-y-auto px-4 py-3 space-y-4 bg-gray-50 dark:bg-gray-900"
             x-ref="messagesContainer">
            
            <!-- Messages will be populated by JavaScript -->
            <template x-for="message in messages" :key="message.id">
                <div class="flex items-start space-x-3" x-bind:class="message.sender_type === 'visitor' ? 'flex-row-reverse space-x-reverse' : ''">
                    <!-- Avatar -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         x-bind:class="message.sender_type === 'visitor' ? 'bg-blue-100 dark:bg-blue-900' : 
                                 message.sender_type === 'system' ? 'bg-gray-200 dark:bg-gray-700' : 
                                 'bg-gray-300 dark:bg-gray-600'">
                        <svg class="w-4 h-4" 
                             x-bind:class=="message.sender_type === 'visitor' ? 'text-blue-600 dark:text-blue-400' : 
                                     message.sender_type === 'system' ? 'text-gray-500' :
                                     'text-gray-600 dark:text-gray-300'" 
                             fill="currentColor" 
                             viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    
                    <!-- Message Bubble -->
                    <div class="flex-1">
                        <div class="rounded-lg px-3 py-2 max-w-xs shadow-sm"
                             x-bind:class=="message.sender_type === 'visitor' ? 
                                'bg-blue-600 text-white rounded-tr-none ml-auto' : 
                                message.sender_type === 'system' ? 
                                'bg-gray-200 text-gray-700 rounded-lg italic text-center text-sm' :
                                'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white rounded-tl-none'">
                            <p class="text-sm" x-html="formatMessage(message.message)"></p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" 
                           x-bind:class=="message.sender_type === 'visitor' ? 'text-right' : 
                                   message.sender_type === 'system' ? 'text-center' : ''"
                           x-text="formatTime(message.created_at)"></p>
                    </div>
                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex items-start space-x-3" x-transition>
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
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
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="typingUser + ' is typing...'"></p>
                </div>
            </div>
        </div>
        
        <!-- Chat Input -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
            <form @submit.prevent="sendMessage()" x-show="sessionStatus !== 'closed'">
                <div class="flex items-end space-x-2">
                    <!-- Message Input -->
                    <div class="flex-1 relative">
                        <textarea x-model="currentMessage"
                                  x-ref="messageInput"
                                  @keydown.enter.prevent="sendMessage()"
                                  @input="handleTyping()"
                                  @focus="handleTyping()"
                                  @blur="stopTyping()"
                                  :disabled="!isConnected || sessionStatus === 'closed'"
                                  placeholder="Type your message..."
                                  rows="1"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm disabled:opacity-50"
                                  style="max-height: 80px; overflow-y: auto;"></textarea>
                    </div>
                    
                    <!-- Send Button -->
                    <button type="submit" 
                            :disabled="!currentMessage.trim() || !isConnected || sessionStatus === 'closed'"
                            class="w-8 h-8 mb-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-gray-600 text-white rounded-full flex items-center justify-center transition-colors disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </form>
            
            <!-- Session Closed Message -->
            <div x-show="sessionStatus === 'closed'" class="text-center py-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">This chat session has ended.</p>
                <button @click="startNewSession()" 
                        class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                    Start New Chat
                </button>
            </div>
            
            <!-- Powered By -->
            <div class="mt-2 text-center">
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    Powered by <span class="font-medium {{ $currentTheme['accent'] }}">{{ $companyName }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

@auth
@push('scripts')
<script>
function chatWidget() {
    return {
        // State
        isOpen: false,
        isOnline: false,
        isTyping: false,
        isConnected: false,
        unreadCount: 0,
        currentMessage: '',
        messages: [],
        sessionId: null,
        sessionStatus: null,
        connectionStatus: 'disconnected',
        operatorName: '{{ $operatorName }}',
        typingUser: '',
        typingTimer: null,
        
        // Initialize
        init() {
            this.checkOnlineStatus();
            this.setupWebSocketListeners();
            
            // Auto-open if configured
            if (this.$el.dataset.autoOpen === 'true') {
                setTimeout(() => this.openChat(), 1000);
            }
            
            // Check for existing session
            this.loadExistingSession();
            
            // Periodic status checks
            setInterval(() => this.checkOnlineStatus(), 30000);
        },

        // WebSocket Setup
        setupWebSocketListeners() {
            if (!window.Echo) {
                console.warn('Echo not available for chat widget');
                return;
            }

            // Listen for connection status
            window.Echo.connector.pusher.connection.bind('connected', () => {
                this.connectionStatus = 'connected';
                this.isConnected = true;
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                this.connectionStatus = 'disconnected';
                this.isConnected = false;
            });

            // Listen for operator status changes
            window.Echo.channel('public-chat-status')
                .listen('.operator.status.changed', (e) => {
                    this.isOnline = e.total_online_operators > 0;
                });
        },

        // Chat Session Management
        async openChat() {
            this.isOpen = true;
            this.unreadCount = 0;
            
            if (!this.sessionId) {
                await this.startChatSession();
            }
            
            this.$nextTick(() => {
                this.$refs.messageInput?.focus();
                this.scrollToBottom();
            });
        },

        closeChat() {
            this.isOpen = false;
        },

        async startChatSession() {
            try {
                const response = await fetch('/api/chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.sessionId = data.session_id;
                    this.sessionStatus = data.status;
                    this.messages = data.messages || [];
                    
                    // Start listening to this session
                    this.listenToSession(data.channel);
                    
                    this.scrollToBottom();
                } else {
                    this.showError('Failed to start chat session');
                }
            } catch (error) {
                console.error('Failed to start chat:', error);
                this.showError('Unable to connect to chat service');
            }
        },

        async loadExistingSession() {
            try {
                const response = await fetch('/api/chat/session');
                const data = await response.json();
                
                if (data.success) {
                    this.sessionId = data.session_id;
                    this.sessionStatus = data.status;
                    this.messages = data.messages || [];
                    
                    if (data.operator) {
                        this.operatorName = data.operator.name;
                    }
                    
                    // Start listening to this session
                    this.listenToSession(data.channel);
                }
            } catch (error) {
                // No existing session, which is fine
                console.log('No existing chat session');
            }
        },

        listenToSession(channel) {
            if (!window.Echo || !channel) return;

            // Listen for new messages
            window.Echo.channel(channel)
                .listen('.message.sent', (e) => {
                    this.handleNewMessage(e);
                })
                .listen('.typing.indicator', (e) => {
                    this.handleTypingIndicator(e);
                })
                .listen('.session.closed', (e) => {
                    this.handleSessionClosed(e);
                });
        },

        // Message Handling
        async sendMessage() {
            if (!this.currentMessage.trim() || !this.sessionId) return;

            const message = this.currentMessage.trim();
            this.currentMessage = '';
            this.stopTyping();

            try {
                const response = await fetch('/api/chat/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        message: message
                    })
                });

                const data = await response.json();
                
                if (!data.success) {
                    this.showError('Failed to send message');
                    this.currentMessage = message; // Restore message
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                this.showError('Failed to send message');
                this.currentMessage = message; // Restore message
            }
        },

        handleNewMessage(event) {
            // Add message if not already in list
            if (!this.messages.find(m => m.id === event.id)) {
                this.messages.push(event);
                this.scrollToBottom();
                
                // Show notification if chat is closed and message is from operator
                if (!this.isOpen && event.sender_type === 'operator') {
                    this.unreadCount++;
                    this.playNotificationSound();
                }
            }
        },

        handleTypingIndicator(event) {
            if (event.user_id !== parseInt(window.authUserId)) {
                this.isTyping = event.is_typing;
                this.typingUser = event.user_name;
                
                if (event.is_typing) {
                    this.scrollToBottom();
                }
            }
        },

        handleSessionClosed(event) {
            this.sessionStatus = 'closed';
            this.showInfo('Chat session has been closed');
        },

        // Typing Indicators
        handleTyping() {
            if (!this.sessionId) return;

            // Send typing indicator
            this.sendTypingIndicator(true);

            // Clear existing timer
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
            }

            // Stop typing after 3 seconds
            this.typingTimer = setTimeout(() => {
                this.stopTyping();
            }, 3000);
        },

        stopTyping() {
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
                this.typingTimer = null;
            }
            this.sendTypingIndicator(false);
        },

        async sendTypingIndicator(isTyping) {
            if (!this.sessionId) return;

            try {
                await fetch('/api/chat/typing', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        is_typing: isTyping
                    })
                });
            } catch (error) {
                console.error('Failed to send typing indicator:', error);
            }
        },

        // Utility Methods
        async checkOnlineStatus() {
            try {
                const response = await fetch('/api/chat/online-status');
                const data = await response.json();
                this.isOnline = data.is_online;
            } catch (error) {
                console.error('Failed to check online status:', error);
                this.isOnline = false;
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        formatMessage(message) {
            // Simple URL detection and linking
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return message.replace(urlRegex, '<a href="$1" target="_blank" class="underline">$1</a>');
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) { // Less than 1 minute
                return 'now';
            } else if (diff < 3600000) { // Less than 1 hour
                return Math.floor(diff / 60000) + 'm ago';
            } else if (diff < 86400000) { // Less than 1 day
                return Math.floor(diff / 3600000) + 'h ago';
            } else {
                return date.toLocaleDateString();
            }
        },

        getConnectionStatusText() {
            switch (this.connectionStatus) {
                case 'connecting':
                    return 'Connecting...';
                case 'connected':
                    return 'Connected';
                case 'disconnected':
                    return 'Disconnected';
                default:
                    return 'Connecting...';
            }
        },

        playNotificationSound() {
            if (this.$el.dataset.enableSound === 'true') {
                try {
                    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvGUeBT2U2fPGdSYELYHM89yJOQcZZ7zs5Z9NEAxPqOTvt2MdBjiR2O/NeSsFJHfI8N+QQAoUXrPq66hWFAlFnt/xvWYfBT2U2/PHdSUELYDL89uKOQgZZ7vs5qBOEAxOpuPwuGQdBTiP2PDPeSsFJHbH8OCSQgoTXbPq7KlXFAlFnt/wvmcfBTyU3PLIdCUELYDK89uLOggZZrvr56BOEQxOpuLvuWUdBTiP2fDQeSoFJHbH8OGTRQ==');
                    audio.volume = 0.3;
                    audio.play().catch(() => {}); // Ignore errors
                } catch (error) {
                    // Silently ignore audio errors
                }
            }
        },

        showError(message) {
            this.showNotification('error', message);
        },

        showInfo(message) {
            this.showNotification('info', message);
        },

        showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-3 rounded-lg shadow-lg text-white max-w-sm ${
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        },

        async startNewSession() {
            this.sessionId = null;
            this.sessionStatus = null;
            this.messages = [];
            await this.startChatSession();
        }
    }
}
</script>
@endpush
@endauth