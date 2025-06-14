{{-- resources/views/components/chat-widget.blade.php - Improved Real-time Version --}}
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
    'offsetFromQuickAction' => true,
    'pollingInterval' => 1000,
    'maxPollingInterval' => 5000
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
     data-polling-interval="{{ $pollingInterval }}"
     data-max-polling-interval="{{ $maxPollingInterval }}"
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
             x-text="unreadCount > 99 ? '99+' : unreadCount"
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
                <!-- Connection Status -->
                <div x-show="connectionStatus !== 'connected'" class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                </div>
                
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
        
        <!-- Connection Status Bar -->
        <div x-show="connectionStatus !== 'connected'" 
             class="px-4 py-2 bg-yellow-50 border-b border-yellow-200 text-yellow-800 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                <span x-text="getConnectionStatusText()">Connecting...</span>
                <span x-show="isRetrying" class="text-xs">(Retrying...)</span>
            </div>
        </div>
        
        <!-- Chat Messages -->
        <div id="chat-messages" 
             class="flex-1 overflow-y-auto px-4 py-3 space-y-4 bg-gray-50 dark:bg-gray-900"
             x-ref="messagesContainer"
             @scroll="handleScroll($event)">
            
            <!-- Welcome Message -->
            <div x-show="messages.length === 0 && !isLoading" class="text-center py-8">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ $welcomeMessage }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Our support team is here to help you</p>
            </div>
            
            <!-- Loading Indicator -->
            <div x-show="isLoading" class="text-center py-4">
                <div class="inline-flex items-center space-x-2">
                    <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce"></div>
                    <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
            
            <!-- Messages -->
            <template x-for="message in messages" :key="message.id">
                <div class="flex items-start space-x-3" x-bind:class="message.sender_type === 'visitor' ? 'flex-row-reverse space-x-reverse' : ''">
                    <!-- Avatar -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         x-bind:class="message.sender_type === 'visitor' ? 'bg-blue-100 dark:bg-blue-900' : 
                                 message.sender_type === 'system' ? 'bg-gray-200 dark:bg-gray-700' : 
                                 'bg-gray-300 dark:bg-gray-600'">
                        <svg class="w-4 h-4" 
                             x-bind:class="message.sender_type === 'visitor' ? 'text-blue-600 dark:text-blue-400' : 
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
                             x-bind:class="message.sender_type === 'visitor' ? 
                                'bg-blue-600 text-white rounded-tr-none ml-auto' : 
                                message.sender_type === 'system' ? 
                                'bg-gray-200 text-gray-700 rounded-lg italic text-center text-sm' :
                                'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white rounded-tl-none'">
                            <p class="text-sm" x-html="formatMessage(message.message)"></p>
                            
                            <!-- Message status for sent messages -->
                            <div x-show="message.sender_type === 'visitor' && message.status" 
                                 class="text-xs opacity-75 mt-1" 
                                 x-text="message.status">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" 
                           x-bind:class="message.sender_type === 'visitor' ? 'text-right' : 
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
                                  @keydown.enter.prevent="handleEnterKey($event)"
                                  @input="handleTyping()"
                                  @focus="handleFocus()"
                                  @blur="stopTyping()"
                                  @paste="handlePaste($event)"
                                  x-bind::disabled="!isConnected || sessionStatus === 'closed' || isSending"
                                  placeholder="Type your message..."
                                  rows="1"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm disabled:opacity-50"
                                  style="max-height: 80px; overflow-y: auto;"></textarea>
                        
                        <!-- Character Count -->
                        <div x-show="currentMessage.length > 800" 
                             class="absolute bottom-1 right-1 text-xs text-gray-400"
                             x-text="currentMessage.length + '/1000'">
                        </div>
                    </div>
                    
                    <!-- Send Button -->
                    <button type="submit" 
                            x-bind::disabled="!currentMessage.trim() || !isConnected || sessionStatus === 'closed' || isSending"
                            class="w-8 h-8 mb-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-gray-600 text-white rounded-full flex items-center justify-center transition-colors disabled:cursor-not-allowed">
                        <svg x-show="!isSending" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <div x-show="isSending" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    </button>
                </div>
                
                <!-- Quick Actions -->
                <div x-show="messages.length === 0 && !isLoading" class="mt-3 flex flex-wrap gap-2">
                    <button type="button" 
                            @click="sendQuickMessage('Hello, I need help with your services')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full transition-colors">
                        Need help with services
                    </button>
                    <button type="button" 
                            @click="sendQuickMessage('I want to request a quotation')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full transition-colors">
                        Request quotation
                    </button>
                </div>
            </form>
            
            <!-- Session Closed Message -->
            <div x-show="sessionStatus === 'closed'" class="text-center py-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">This chat session has ended.</p>
                <button @click="startNewSession()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
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
// Set global variable for authenticated user ID
window.authUserId = {{ auth()->id() }};

function chatWidget() {
    return {
        // State
        isOpen: false,
        isOnline: false,
        isTyping: false,
        isConnected: false,
        isLoading: false,
        isSending: false,
        isRetrying: false,
        unreadCount: 0,
        currentMessage: '',
        messages: [],
        sessionId: null,
        sessionStatus: null,
        connectionStatus: 'disconnected',
        operatorName: '{{ $operatorName }}',
        typingUser: '',
        
        // Timers
        typingTimer: null,
        pollingTimer: null,
        connectionTimer: null,
        retryTimer: null,
        
        // Settings
        pollingInterval: parseInt(this.$el?.dataset?.pollingInterval) || 1000,
        maxPollingInterval: parseInt(this.$el?.dataset?.maxPollingInterval) || 5000,
        currentPollingInterval: null,
        lastMessageId: null,
        maxRetries: 5,
        retryCount: 0,
        
        // Initialize
        init() {
            this.currentPollingInterval = this.pollingInterval;
            this.checkOnlineStatus();
            this.setupEventListeners();
            
            // Auto-open if configured
            if (this.$el.dataset.autoOpen === 'true') {
                setTimeout(() => this.openChat(), 1000);
            }
            
            // Check for existing session
            this.loadExistingSession();
            
            // Start connection monitoring
            this.startConnectionMonitoring();
        },

        // Event Listeners
        setupEventListeners() {
            // Listen for visibility changes to adjust polling
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.reducePollingFrequency();
                } else {
                    this.increasePollingFrequency();
                    if (this.sessionId) {
                        this.pollMessages();
                    }
                }
            });

            // Listen for online/offline events
            window.addEventListener('online', () => {
                this.connectionStatus = 'connecting';
                this.retryConnection();
            });

            window.addEventListener('offline', () => {
                this.connectionStatus = 'disconnected';
                this.isConnected = false;
                this.stopPolling();
            });
        },

        // Connection Management
        startConnectionMonitoring() {
            this.connectionTimer = setInterval(() => {
                if (this.isOpen && this.sessionId) {
                    this.checkConnectionHealth();
                }
                this.checkOnlineStatus();
            }, 10000); // Check every 10 seconds
        },

        async checkConnectionHealth() {
            try {
                const response = await fetch('/api/chat/online-status', {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                
                if (response.ok) {
                    this.connectionStatus = 'connected';
                    this.isConnected = true;
                    this.retryCount = 0;
                    this.isRetrying = false;
                } else {
                    throw new Error('Connection check failed');
                }
            } catch (error) {
                this.handleConnectionError();
            }
        },

        handleConnectionError() {
            this.connectionStatus = 'disconnected';
            this.isConnected = false;
            this.retryConnection();
        },

        retryConnection() {
            if (this.retryCount < this.maxRetries && !this.isRetrying) {
                this.isRetrying = true;
                this.retryCount++;
                
                const delay = Math.min(1000 * Math.pow(2, this.retryCount), 30000);
                
                this.retryTimer = setTimeout(() => {
                    this.checkConnectionHealth();
                }, delay);
            }
        },

        // Chat Session Management
        async openChat() {
            this.isOpen = true;
            this.unreadCount = 0;
            
            if (!this.sessionId) {
                await this.startChatSession();
            } else {
                this.startPolling();
            }
            
            this.$nextTick(() => {
                this.$refs.messageInput?.focus();
                this.scrollToBottom();
            });
        },

        closeChat() {
            this.isOpen = false;
            this.stopPolling();
        },

        async startChatSession() {
            this.isLoading = true;
            
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
                    this.lastMessageId = this.getLastMessageId();
                    
                    this.connectionStatus = 'connected';
                    this.isConnected = true;
                    this.startPolling();
                    
                    this.scrollToBottom();
                } else {
                    this.showError('Failed to start chat session');
                }
            } catch (error) {
                console.error('Failed to start chat:', error);
                this.showError('Unable to connect to chat service');
                this.handleConnectionError();
            } finally {
                this.isLoading = false;
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
                    this.lastMessageId = this.getLastMessageId();
                    
                    if (data.operator) {
                        this.operatorName = data.operator.name;
                    }
                    
                    this.connectionStatus = 'connected';
                    this.isConnected = true;
                }
            } catch (error) {
                // No existing session, which is fine
                console.log('No existing chat session');
            }
        },

        // Polling System
        startPolling() {
            if (this.pollingTimer) {
                clearInterval(this.pollingTimer);
            }
            
            this.pollingTimer = setInterval(() => {
                this.pollMessages();
            }, this.currentPollingInterval);
            
            // Initial poll
            this.pollMessages();
        },

        stopPolling() {
            if (this.pollingTimer) {
                clearInterval(this.pollingTimer);
                this.pollingTimer = null;
            }
        },

        async pollMessages() {
            if (!this.sessionId || !this.isConnected) return;

            try {
                const url = new URL('/api/chat/messages', window.location.origin);
                url.searchParams.append('session_id', this.sessionId);
                if (this.lastMessageId) {
                    url.searchParams.append('last_message_id', this.lastMessageId);
                }

                const response = await fetch(url, {
                    headers: {
                        'Cache-Control': 'no-cache',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.connectionStatus = 'connected';
                    this.isConnected = true;
                    this.retryCount = 0;
                    this.isRetrying = false;
                    
                    // Update session status
                    this.sessionStatus = data.session_status;
                    
                    // Process new messages
                    if (data.has_new_messages && data.messages.length > 0) {
                        this.processNewMessages(data.messages);
                        this.adaptivePolling(true); // Increase frequency when active
                    } else {
                        this.adaptivePolling(false); // Decrease frequency when inactive
                    }
                } else {
                    throw new Error('Polling failed');
                }
            } catch (error) {
                console.error('Polling error:', error);
                this.handleConnectionError();
            }
        },

        processNewMessages(newMessages) {
            let hasNewOperatorMessages = false;
            
            newMessages.forEach(message => {
                // Avoid duplicates
                if (!this.messages.find(m => m.id === message.id)) {
                    this.messages.push(message);
                    this.lastMessageId = Math.max(this.lastMessageId || 0, message.id);
                    
                    // Check for operator messages when chat is closed
                    if (!this.isOpen && message.sender_type === 'operator') {
                        this.unreadCount++;
                        hasNewOperatorMessages = true;
                    }
                }
            });
            
            if (hasNewOperatorMessages) {
                this.playNotificationSound();
                this.showDesktopNotification();
            }
            
            this.scrollToBottom();
        },

        adaptivePolling(isActive) {
            if (isActive) {
                // Increase polling frequency during active conversations
                this.currentPollingInterval = Math.max(500, this.pollingInterval);
            } else {
                // Gradually decrease frequency during inactivity
                this.currentPollingInterval = Math.min(
                    this.currentPollingInterval * 1.2, 
                    this.maxPollingInterval
                );
            }
            
            // Restart polling with new interval
            if (this.pollingTimer) {
                this.stopPolling();
                this.startPolling();
            }
        },

        reducePollingFrequency() {
            this.currentPollingInterval = this.maxPollingInterval;
            if (this.pollingTimer) {
                this.stopPolling();
                this.startPolling();
            }
        },

        increasePollingFrequency() {
            this.currentPollingInterval = this.pollingInterval;
            if (this.pollingTimer) {
                this.stopPolling();
                this.startPolling();
            }
        },

        // Message Handling
        // Perbaikan fungsi sendMessage di chat widget
async sendMessage() {
    if (!this.currentMessage.trim() || !this.sessionId || this.isSending) return;

    const message = this.currentMessage.trim();
    this.currentMessage = '';
    this.isSending = true;

    // Optimistic UI update - tambahkan pesan sementara
    const optimisticMessage = {
        id: `temp-${Date.now()}`,
        message: message,
        sender_type: 'visitor',
        sender_name: 'You',
        created_at: new Date().toISOString(),
        formatted_time: new Date().toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit', 
            hour12: false 
        }),
        status: 'sending'
    };
    
    this.messages.push(optimisticMessage);
    this.scrollToBottom();

    try {
        // Get fresh CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }

        const response = await fetch('/api/chat/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                // Tambahkan Authorization header jika menggunakan Sanctum
                ...(window.authToken && { 'Authorization': `Bearer ${window.authToken}` })
            },
            body: JSON.stringify({
                session_id: this.sessionId,
                message: message
            })
        });

        // Cek status response secara detail
        if (!response.ok) {
            const errorText = await response.text();
            console.error(`HTTP ${response.status}:`, errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        
        // Pastikan response sukses
        if (data.success) {
            // Remove optimistic message dan replace dengan data real
            const optimisticIndex = this.messages.findIndex(m => m.id === optimisticMessage.id);
            if (optimisticIndex !== -1) {
                this.messages.splice(optimisticIndex, 1);
            }

            // Process real messages dari server
            if (data.messages && data.messages.length > 0) {
                this.processNewMessages(data.messages);
            }
            
            console.log('✅ Message sent successfully');
            
            // Trigger immediate poll untuk response
            setTimeout(() => this.pollMessages(), 100);
        } else {
            throw new Error(data.message || 'Server returned success: false');
        }
    } catch (error) {
        console.error('❌ Failed to send message:', error);
        
        // Update optimistic message status
        const optimisticIndex = this.messages.findIndex(m => m.id === optimisticMessage.id);
        if (optimisticIndex !== -1) {
            this.messages[optimisticIndex].status = 'failed';
            this.messages[optimisticIndex].retry = () => {
                this.currentMessage = message;
                this.messages.splice(optimisticIndex, 1);
                this.sendMessage();
            };
        }
        
        // Show error dengan informasi lebih detail
        if (error.message.includes('HTTP 419')) {
            this.showError('Session expired. Please refresh the page.');
        } else if (error.message.includes('HTTP 422')) {
            this.showError('Invalid message format.');
        } else if (error.message.includes('HTTP 401')) {
            this.showError('Authentication required. Please login again.');
        } else {
            this.showError('Failed to send message. Please try again.');
        }
        
        // Restore message in input for retry
        this.currentMessage = message;
    } finally {
        this.isSending = false;
    }
},

// Fungsi helper untuk process messages
processNewMessages(messages) {
    if (!Array.isArray(messages)) return;
    
    messages.forEach(message => {
        // Cek apakah message sudah ada
        const exists = this.messages.find(m => m.id === message.id);
        if (!exists) {
            this.messages.push(message);
        }
    });
    
    // Sort messages by created_at
    this.messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    this.scrollToBottom();
},

        handleSendError(optimisticMessage, originalMessage) {
            // Update optimistic message to show error
            const messageIndex = this.messages.findIndex(m => m.id === optimisticMessage.id);
            if (messageIndex !== -1) {
                this.messages[messageIndex].status = 'failed';
                this.messages[messageIndex].retry = () => {
                    this.currentMessage = originalMessage;
                    this.messages.splice(messageIndex, 1);
                    this.sendMessage();
                };
            }
            this.showError('Failed to send message');
        },

        sendQuickMessage(message) {
            this.currentMessage = message;
            this.sendMessage();
        },

        // Input Handling
        handleEnterKey(event) {
            if (event.shiftKey) {
                return; // Allow new line with Shift+Enter
            }
            this.sendMessage();
        },

        handleFocus() {
            this.unreadCount = 0;
            if (this.sessionId) {
                this.adaptivePolling(true);
            }
        },

        handlePaste(event) {
            // Handle pasted content
            const paste = (event.clipboardData || window.clipboardData).getData('text');
            if (paste.length > 1000) {
                event.preventDefault();
                this.currentMessage += paste.substring(0, 1000);
                this.showWarning('Message truncated to 1000 characters');
            }
        },

        handleScroll(event) {
            // Auto-load more messages when scrolling to top (if needed)
            const container = event.target;
            if (container.scrollTop === 0 && this.messages.length > 0) {
                this.loadOlderMessages();
            }
        },

        // Typing Indicators
        handleTyping() {
            if (!this.sessionId) return;

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
            if (!this.sessionId || !this.isConnected) return;

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

        getLastMessageId() {
            if (this.messages.length === 0) return null;
            return Math.max(...this.messages.map(m => m.id));
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

        // Notifications
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

        showDesktopNotification() {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New message from support', {
                    body: 'You have received a new message from our support team',
                    icon: '/favicon.ico',
                    tag: 'chat-notification'
                });
            } else if ('Notification' in window && Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        },

        showError(message) {
            this.showNotification('error', message);
        },

        showWarning(message) {
            this.showNotification('warning', message);
        },

        showInfo(message) {
            this.showNotification('info', message);
        },

        showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-3 rounded-lg shadow-lg text-white max-w-sm ${
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
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
            this.lastMessageId = null;
            this.stopPolling();
            await this.startChatSession();
        },

        async loadOlderMessages() {
            // Implement if needed for chat history
            console.log('Load older messages - implement if needed');
        },

        // Cleanup
        destroy() {
            this.stopPolling();
            if (this.connectionTimer) {
                clearInterval(this.connectionTimer);
            }
            if (this.retryTimer) {
                clearTimeout(this.retryTimer);
            }
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
            }
        }
    }
}

// Cleanup when page unloads
window.addEventListener('beforeunload', () => {
    // Cleanup any timers
    const widget = document.querySelector('#chat-widget');
    if (widget && widget.__x) {
        widget.__x.destroy();
    }
});
</script>
@endpush
@endauth