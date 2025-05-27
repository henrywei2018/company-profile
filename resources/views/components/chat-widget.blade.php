{{-- resources/views/components/chat-widget.blade.php --}}
@props([
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
    'theme' => 'primary', // primary, dark, light
    'size' => 'normal', // compact, normal, large
    'autoOpen' => false,
    'showOnlineStatus' => true,
    'enableSound' => true,
    'welcomeMessage' => 'Hello! How can we help you today?',
    'operatorName' => 'Support Team',
    'companyName' => 'CV Usaha Prima Lestari'
])

@php
    $positionClasses = [
        'bottom-right' => 'bottom-4 right-4 sm:bottom-6 sm:right-6',
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
        'dark' => [
            'button' => 'bg-gray-800 hover:bg-gray-900 text-white border-gray-800',
            'header' => 'bg-gray-800 text-white',
            'accent' => 'text-gray-800',
            'message_user' => 'bg-gray-800 text-white',
            'message_operator' => 'bg-gray-100 text-gray-900',
        ],
        'light' => [
            'button' => 'bg-white hover:bg-gray-50 text-gray-900 border-gray-300 shadow-lg',
            'header' => 'bg-white text-gray-900 border-b border-gray-200',
            'accent' => 'text-gray-900',
            'message_user' => 'bg-gray-900 text-white',
            'message_operator' => 'bg-gray-100 text-gray-900',
        ],
    ];

    $sizeClasses = [
        'compact' => [
            'button' => 'w-12 h-12',
            'window' => 'w-80 h-96',
            'icon' => 'w-4 h-4',
        ],
        'normal' => [
            'button' => 'w-14 h-14 sm:w-16 sm:h-16',
            'window' => 'w-80 sm:w-96 h-96 sm:h-[32rem]',
            'icon' => 'w-6 h-6',
        ],
        'large' => [
            'button' => 'w-16 h-16 sm:w-18 sm:h-18',
            'window' => 'w-96 sm:w-[28rem] h-[32rem] sm:h-[36rem]',
            'icon' => 'w-7 h-7',
        ],
    ];

    $currentTheme = $themeClasses[$theme] ?? $themeClasses['primary'];
    $currentSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
    $currentPosition = $positionClasses[$position] ?? $positionClasses['bottom-right'];
@endphp

<!-- Chat Widget Container -->
<div id="chat-widget" 
     class="fixed {{ $currentPosition }} z-50 font-sans"
     data-auto-open="{{ $autoOpen ? 'true' : 'false' }}"
     data-enable-sound="{{ $enableSound ? 'true' : 'false' }}"
     data-theme="{{ $theme }}"
     x-data="chatWidget()"
     x-init="init()">
    
    <!-- Minimized Chat Button (appears when minimized) -->
    <div x-show="isMinimized && !isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative">
        
        <!-- Unread Badge for Minimized State -->
        <div x-show="unreadCount > 0" 
             x-text="unreadCount"
             class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5 z-10 shadow-lg animate-pulse">
        </div>
        
        <!-- Minimized Button -->
        <button @click="openChat()" 
                class="{{ $currentSize['button'] }} {{ $currentTheme['button'] }} rounded-full border-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center justify-center group relative overflow-hidden hover:ring-2 hover:ring-blue-500/20 hover:ring-offset-1"
                title="Open chat"
                aria-label="Open minimized chat">
            
            <!-- Minimized Icon (chat with minus) -->
            <svg class="{{ $currentSize['icon'] }} transition-all duration-200" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            
            <!-- Small minimize indicator -->
            <div class="absolute bottom-1 right-1 w-2 h-2 bg-white rounded-full opacity-80"></div>
            
            <!-- Ripple Effect -->
            <div class="absolute inset-0 rounded-full opacity-0 group-hover:opacity-20 bg-white transition-opacity duration-200"></div>
        </button>
    </div>

    <!-- Chat Toggle Button (Hidden when chat is open) -->
    <div class="relative" x-show="!isOpen && !isMinimized" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
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
                    <h3 class="font-semibold text-sm truncate">{{ $operatorName }}</h3>
                    <p class="text-xs opacity-90">
                        <span x-show="isOnline">Online • Typically replies instantly</span>
                        <span x-show="!isOnline">Offline • We'll reply as soon as possible</span>
                    </p>
                </div>
            </div>
            
            <!-- Header Actions -->
            <div class="flex items-center space-x-2">
                <!-- Minimize Button -->
                <button @click="minimizeChat()" 
                        class="w-7 h-7 rounded-full hover:bg-white hover:bg-opacity-20 flex items-center justify-center transition-colors duration-200"
                        title="Minimize chat"
                        aria-label="Minimize chat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                
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
        
        <!-- Chat Messages -->
        <div id="chat-messages" 
             class="flex-1 overflow-y-auto px-4 py-3 space-y-4 bg-green-100 dark:bg-gray-900"
             x-ref="messagesContainer"
             @scroll="handleScroll">
            
            <!-- Welcome Message -->
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg rounded-tl-none px-3 py-2 max-w-xs shadow-sm">
                        <p class="text-sm">{{ $welcomeMessage }}</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $operatorName }} • now</p>
                </div>
            </div>
            
            <!-- Dynamic Messages -->
            <template x-for="message in messages" :key="message.id">
                <div class="flex items-start space-x-3" :class="message.sender === 'user' ? 'flex-row-reverse space-x-reverse' : ''">
                    <!-- Avatar -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                         :class="message.sender === 'user' ? 'bg-blue-100 dark:bg-blue-900' : 'bg-gray-300 dark:bg-gray-600'">
                        <svg class="w-4 h-4" 
                             :class="message.sender === 'user' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300'" 
                             fill="currentColor" 
                             viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    
                    <!-- Message Bubble -->
                    <div class="flex-1">
                        <div class="rounded-lg px-3 py-2 max-w-xs shadow-sm"
                             :class="message.sender === 'user' ? 
                                'bg-blue-600 text-white rounded-tr-none ml-auto' : 
                                'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white rounded-tl-none'">
                            <p class="text-sm" x-html="message.content"></p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" 
                           :class="message.sender === 'user' ? 'text-right' : ''"
                           x-text="formatTime(message.timestamp)"></p>
                    </div>
                </div>
            </template>
            
            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex items-start space-x-3">
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
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $operatorName }} is typing...</p>
                </div>
            </div>
        </div>
        
        <!-- Chat Input -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
            <form @submit.prevent="sendMessage()">
                <div class="flex items-end space-x-2">
                    
                    
                    <!-- Message Input -->
                    <div class="flex-1 relative">
                        <textarea x-model="currentMessage"
                                  x-ref="messageInput"
                                  @keydown.enter.prevent="sendMessage()"
                                  @input="handleTyping()"
                                  placeholder="Type your message..."
                                  rows="1"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm"
                                  style="max-height: 80px; overflow-y: auto;"></textarea>
                    </div>
                    
                    <!-- Send Button -->
                    <button type="submit" 
                            :disabled="!currentMessage.trim()"
                            class="w-8 h-8 mb-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 dark:disabled:bg-gray-600 text-white rounded-full flex items-center justify-center transition-colors disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </form>
            
            <!-- Powered By -->
            <div class="mt-2 text-center">
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    Powered by <span class="font-medium {{ $currentTheme['accent'] }}">{{ $companyName }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Chat Widget Styles -->
<style>
    #chat-widget textarea {
        min-height: 38px;
        line-height: 1.5;
    }
    
    #chat-widget .animate-bounce {
        animation: bounce 1.4s infinite ease-in-out;
    }
    
    #chat-widget .animate-bounce:nth-child(1) { animation-delay: -0.32s; }
    #chat-widget .animate-bounce:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    
            /* Enhanced button expansion effect */
        #chat-widget button:focus {
            outline: none;
            ring: 4px;
            ring-color: rgb(59 130 246 / 0.3);
            ring-offset: 2px;
        }
        
        /* Expanded state ring animation */
        #chat-widget .ring-pulse {
            animation: ringPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes ringPulse {
            0%, 100% {
                ring-color: rgb(59 130 246 / 0.3);
                transform: scale(1);
            }
            50% {
                ring-color: rgb(59 130 246 / 0.1);
                transform: scale(1.05);
            }
        }
        
        /* Quick action style expansion */
        #chat-widget .expanded-border {
            box-shadow: 
                0 0 0 4px rgb(59 130 246 / 0.3),
                0 0 0 8px rgb(59 130 246 / 0.1),
                0 20px 25px -5px rgb(0 0 0 / 0.1),
                0 10px 10px -5px rgb(0 0 0 / 0.04);
            transform: scale(1.1);
        }
        
        /* Button state transitions */
        #chat-widget button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Theme-specific ring colors */
        #chat-widget[data-theme="primary"] button.expanded-border {
            box-shadow: 
                0 0 0 4px rgb(59 130 246 / 0.3),
                0 0 0 8px rgb(59 130 246 / 0.1),
                0 20px 25px -5px rgb(0 0 0 / 0.1);
        }
        
        #chat-widget[data-theme="dark"] button.expanded-border {
            box-shadow: 
                0 0 0 4px rgb(55 65 81 / 0.3),
                0 0 0 8px rgb(55 65 81 / 0.1),
                0 20px 25px -5px rgb(0 0 0 / 0.1);
        }
        
        #chat-widget[data-theme="light"] button.expanded-border {
            box-shadow: 
                0 0 0 4px rgb(209 213 219 / 0.5),
                0 0 0 8px rgb(209 213 219 / 0.2),
                0 20px 25px -5px rgb(0 0 0 / 0.1);
        }
    #chat-messages::-webkit-scrollbar {
        width: 4px;
    }
    
    #chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #chat-messages::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 2px;
    }
    
    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }
</style>

<!-- Chat Widget JavaScript -->
<script>
function chatWidget() {
    return {
        isOpen: false,
        isMinimized: false,
        isOnline: true,
        isTyping: false,
        unreadCount: 0,
        currentMessage: '',
        messages: [],
        sessionId: null,
        wsConnection: null,
        typingTimer: null,
        
        init() {
            // Initialize chat session
            this.sessionId = this.generateSessionId();
            
            // Auto-open if configured
            if (this.$el.dataset.autoOpen === 'true') {
                setTimeout(() => this.openChat(), 1000);
            }
            
            // Initialize WebSocket connection
            this.initializeWebSocket();
            
            // Load chat history if available
            this.loadChatHistory();
            
            // Check online status periodically
            this.checkOnlineStatus();
            setInterval(() => this.checkOnlineStatus(), 30000);
        },
        
        generateSessionId() {
            return 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        },
        
        toggleChat() {
            // This function is now only used for programmatic access
            // UI interaction uses openChat() and closeChat() directly
            if (this.isOpen || this.isMinimized) {
                this.closeChat();
            } else {
                this.openChat();
            }
        },
        
        openChat() {
            this.isOpen = true;
            this.isMinimized = false;
            this.unreadCount = 0;
            
            // Focus message input after transition
            this.$nextTick(() => {
                setTimeout(() => {
                    this.$refs.messageInput?.focus();
                    this.scrollToBottom();
                }, 200); // Wait for transition to complete
            });
            
            // Track chat opened event
            this.trackEvent('chat_opened');
        },
        
        closeChat() {
            this.isOpen = false;
            this.isMinimized = false;
            
            // Track chat closed event
            this.trackEvent('chat_closed');
        },
        
        minimizeChat() {
            this.isOpen = false;
            this.isMinimized = true;
            
            // Show minimized state with unread count if there are new messages
            // The main button will reappear with transition
            
            // Track chat minimized event
            this.trackEvent('chat_minimized');
        },
        
        async sendMessage() {
            const message = this.currentMessage.trim();
            if (!message) return;
            
            // Add user message to UI immediately
            const userMessage = {
                id: Date.now(),
                content: message,
                sender: 'user',
                timestamp: new Date(),
                status: 'sending'
            };
            
            this.messages.push(userMessage);
            this.currentMessage = '';
            this.scrollToBottom();
            
            // Send message to server
            try {
                const response = await fetch('/api/chat/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        message: message,
                        sender_type: 'visitor'
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    userMessage.status = 'sent';
                    userMessage.id = data.message_id;
                    
                    // Show typing indicator
                    this.showTypingIndicator();
                    
                    this.trackEvent('message_sent', { message_length: message.length });
                } else {
                    userMessage.status = 'failed';
                    this.showErrorMessage('Failed to send message. Please try again.');
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                userMessage.status = 'failed';
                this.showErrorMessage('Network error. Please check your connection.');
            }
        },
        
        showTypingIndicator() {
            this.isTyping = true;
            this.scrollToBottom();
            
            // Hide typing indicator after a random delay (1-3 seconds)
            setTimeout(() => {
                this.isTyping = false;
                this.simulateOperatorResponse();
            }, Math.random() * 2000 + 1000);
        },
        
        simulateOperatorResponse() {
            // This would be replaced with real WebSocket messages
            const responses = [
                "Thank you for your message! How can I help you today?",
                "I'll be happy to assist you with that. Let me check the details for you.",
                "That's a great question! Let me connect you with the right person.",
                "I understand your concern. Let me provide you with the information you need."
            ];
            
            const response = {
                id: Date.now(),
                content: responses[Math.floor(Math.random() * responses.length)],
                sender: 'operator',
                timestamp: new Date(),
                status: 'received'
            };
            
            this.messages.push(response);
            this.scrollToBottom();
            
            // Play notification sound if enabled
            if (this.$el.dataset.enableSound === 'true') {
                this.playNotificationSound();
            }
            
            // Show unread count if chat is closed
            if (!this.isOpen) {
                this.unreadCount++;
            }
        },
        
        handleTyping() {
            // Clear existing timer
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
            }
            
            // Send typing indicator to server
            this.typingTimer = setTimeout(() => {
                // Stop typing indicator
            }, 1000);
        },
        
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                this.showErrorMessage('File size must be less than 5MB');
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                this.showErrorMessage('Only images, PDF, and Word documents are allowed');
                return;
            }
            
            // Upload file and add to messages
            this.uploadFile(file);
        },
        
        async uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('session_id', this.sessionId);
            
            try {
                const response = await fetch('/api/chat/upload-file', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    },
                    body: formData
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Add file message to chat
                    const fileMessage = {
                        id: data.message_id,
                        content: `<div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm">${file.name}</span>
                        </div>`,
                        sender: 'user',
                        timestamp: new Date(),
                        status: 'sent',
                        type: 'file'
                    };
                    
                    this.messages.push(fileMessage);
                    this.scrollToBottom();
                    
                    this.trackEvent('file_uploaded', { file_type: file.type, file_size: file.size });
                } else {
                    this.showErrorMessage('Failed to upload file. Please try again.');
                }
            } catch (error) {
                console.error('File upload failed:', error);
                this.showErrorMessage('Network error. Please try again.');
            }
        },
        
        showErrorMessage(message) {
            const errorMessage = {
                id: Date.now(),
                content: `<div class="text-red-600 text-sm">${message}</div>`,
                sender: 'system',
                timestamp: new Date(),
                type: 'error'
            };
            
            this.messages.push(errorMessage);
            this.scrollToBottom();
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
        
        handleScroll() {
            // Load more messages when scrolled to top
            const container = this.$refs.messagesContainer;
            if (container.scrollTop === 0 && this.messages.length > 0) {
                this.loadMoreMessages();
            }
        },
        
        async loadMoreMessages() {
            // Load more chat history from server
            try {
                const response = await fetch(`/api/chat/history/${this.sessionId}?before=${this.messages[0]?.id || 0}`);
                if (response.ok) {
                    const data = await response.json();
                    this.messages.unshift(...data.messages);
                }
            } catch (error) {
                console.error('Failed to load chat history:', error);
            }
        },
        
        async loadChatHistory() {
            // Load existing chat history for this session
            try {
                const response = await fetch(`/api/chat/history/${this.sessionId}`);
                if (response.ok) {
                    const data = await response.json();
                    this.messages = data.messages || [];
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Failed to load chat history:', error);
            }
        },
        
        initializeWebSocket() {
            // Initialize WebSocket connection for real-time messaging
            const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            const wsUrl = `${wsProtocol}//${window.location.host}/ws/chat/${this.sessionId}`;
            
            try {
                this.wsConnection = new WebSocket(wsUrl);
                
                this.wsConnection.onopen = () => {
                    console.log('WebSocket connected');
                    this.isOnline = true;
                };
                
                this.wsConnection.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    this.handleWebSocketMessage(data);
                };
                
                this.wsConnection.onclose = () => {
                    console.log('WebSocket disconnected');
                    this.isOnline = false;
                    // Attempt to reconnect after 3 seconds
                    setTimeout(() => this.initializeWebSocket(), 3000);
                };
                
                this.wsConnection.onerror = (error) => {
                    console.error('WebSocket error:', error);
                    this.isOnline = false;
                };
            } catch (error) {
                console.error('Failed to initialize WebSocket:', error);
                this.isOnline = false;
            }
        },
        
        handleWebSocketMessage(data) {
            switch (data.type) {
                case 'message':
                    const message = {
                        id: data.message.id,
                        content: data.message.content,
                        sender: data.message.sender_type,
                        timestamp: new Date(data.message.created_at),
                        status: 'received'
                    };
                    
                    this.messages.push(message);
                    this.scrollToBottom();
                    
                    // Play notification sound if enabled and chat is closed
                    if (!this.isOpen && this.$el.dataset.enableSound === 'true') {
                        this.playNotificationSound();
                        this.unreadCount++;
                    }
                    break;
                    
                case 'typing':
                    this.isTyping = data.is_typing;
                    if (data.is_typing) {
                        this.scrollToBottom();
                    }
                    break;
                    
                case 'operator_status':
                    this.isOnline = data.is_online;
                    break;
                    
                case 'session_ended':
                    this.handleSessionEnded();
                    break;
            }
        },
        
        handleSessionEnded() {
            const endMessage = {
                id: Date.now(),
                content: '<div class="text-center text-gray-500 text-sm">Chat session has ended. Thank you for contacting us!</div>',
                sender: 'system',
                timestamp: new Date(),
                type: 'system'
            };
            
            this.messages.push(endMessage);
            this.scrollToBottom();
        },
        
        checkOnlineStatus() {
            // Check if operators are online
            fetch('/api/chat/online-status')
                .then(response => response.json())
                .then(data => {
                    this.isOnline = data.is_online;
                })
                .catch(error => {
                    console.error('Failed to check online status:', error);
                });
        },
        
        playNotificationSound() {
            // Play a subtle notification sound
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvGUeBT2U2fPGdSYELYHM89yJOQcZZ7zs5Z9NEAxPqOTvt2MdBjiR2O/NeSsFJHfI8N+QQAoUXrPq66hWFAlFnt/xvWYfBT2U2/PHdSUELYDL89uKOQgZZ7vs5qBOEAxOpuPwuGQdBTiP2PDPeSsFJHbH8OCSQgoTXbPq7KlXFAlFnt/wvmcfBTyU3PLIdCUELYDK89uLOggZZrvr56BOEQxOpuLvuWUdBTiP2fDQeSoFJHbH8OGTRQ==');
                audio.volume = 0.3;
                audio.play().catch(() => {}); // Ignore errors for sound
            } catch (error) {
                // Silently ignore audio errors
            }
        },
        
        formatTime(timestamp) {
            const now = new Date();
            const diff = now - timestamp;
            
            if (diff < 60000) { // Less than 1 minute
                return 'now';
            } else if (diff < 3600000) { // Less than 1 hour
                return Math.floor(diff / 60000) + 'm ago';
            } else if (diff < 86400000) { // Less than 1 day
                return Math.floor(diff / 3600000) + 'h ago';
            } else {
                return timestamp.toLocaleDateString();
            }
        },
        
        trackEvent(eventName, properties = {}) {
            // Track chat widget events for analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, {
                    event_category: 'chat_widget',
                    session_id: this.sessionId,
                    ...properties
                });
            }
            
            // Also send to your custom analytics endpoint
            fetch('/api/analytics/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
                body: JSON.stringify({
                    event: eventName,
                    properties: {
                        session_id: this.sessionId,
                        timestamp: new Date().toISOString(),
                        ...properties
                    }
                })
            }).catch(() => {}); // Silently ignore tracking errors
        }
    }
}

// Initialize chat widget when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize chat widget if it exists on the page
    const chatWidget = document.querySelector('#chat-widget');
    if (chatWidget && typeof Alpine !== 'undefined') {
        // Chat widget will be initialized by Alpine.js
        console.log('Chat widget ready');
    }
    
    // Add CSS animations for better UX
    const style = document.createElement('style');
    style.textContent = `
        @keyframes chatSlideIn {
            from {
                transform: translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes chatBounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-5px);
            }
            60% {
                transform: translateY(-3px);
            }
        }
        
        #chat-widget .animate-slide-in {
            animation: chatSlideIn 0.3s ease-out;
        }
        
        #chat-widget .animate-chat-bounce {
            animation: chatBounce 2s infinite;
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            #chat-widget [class*="w-80"], #chat-widget [class*="w-96"] {
                width: calc(100vw - 2rem) !important;
                max-width: 380px;
            }
            
            #chat-widget [class*="h-96"], #chat-widget [class*="h-["] {
                height: calc(100vh - 8rem) !important;
                max-height: 500px;
            }
        }
        
        /* Dark mode improvements */
        @media (prefers-color-scheme: dark) {
            #chat-widget .bg-gray-50 {
                background-color: rgb(17 24 39);
            }
            
            #chat-widget .border-gray-200 {
                border-color: rgb(55 65 81);
            }
        }
    `;
    document.head.appendChild(style);
});
</script>