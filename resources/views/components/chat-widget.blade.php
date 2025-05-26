{{-- resources/views/components/chat-widget.blade.php --}}
@props(['position' => 'bottom-right'])

<div id="chat-widget" class="fixed {{ $position === 'bottom-right' ? 'bottom-4 right-4' : 'bottom-4 left-4' }} z-50">
    <!-- Chat Toggle Button -->
    <button id="chat-toggle" 
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
        <svg id="chat-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <!-- Notification Badge -->
        <span id="chat-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">
            0
        </span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="hidden bg-white rounded-lg shadow-2xl w-80 h-96 flex flex-col overflow-hidden border border-gray-200">
        <!-- Chat Header -->
        <div class="bg-blue-600 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-sm">Live Support</h3>
                    <p id="chat-status" class="text-xs opacity-75">We're here to help!</p>
                </div>
            </div>
            <button id="minimize-chat" class="text-white hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <!-- Messages will be inserted here -->
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="hidden px-4 py-2 bg-gray-50">
            <div class="flex items-center space-x-2 text-gray-500 text-sm">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
                <span>Assistant is typing...</span>
            </div>
        </div>

        <!-- Chat Input Area -->
        <div class="border-t border-gray-200 p-4 bg-white">
            <div id="visitor-info-form" class="hidden mb-3 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800 mb-2">Please provide your details for better assistance:</p>
                <div class="space-y-2">
                    <input type="text" id="visitor-name" placeholder="Your name" 
                           class="w-full px-3 py-2 text-sm border border-blue-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="email" id="visitor-email" placeholder="Your email" 
                           class="w-full px-3 py-2 text-sm border border-blue-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="tel" id="visitor-phone" placeholder="Your phone (optional)" 
                           class="w-full px-3 py-2 text-sm border border-blue-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button id="submit-visitor-info" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded text-sm hover:bg-blue-700 transition-colors">
                        Continue Chat
                    </button>
                </div>
            </div>

            <div class="flex space-x-2">
                <input type="text" id="chat-input" placeholder="Type your message..." 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       maxlength="1000">
                <button id="send-message" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
            
            <!-- Powered by -->
            <div class="mt-2 text-center">
                <span class="text-xs text-gray-500">Powered by {{ config('app.name') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Chat Widget Styles -->
<style>
#chat-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
}

#chat-messages {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

#chat-messages::-webkit-scrollbar {
    width: 4px;
}

#chat-messages::-webkit-scrollbar-track {
    background: #f7fafc;
}

#chat-messages::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 2px;
}

#chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.chat-message {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chat-message.visitor {
    align-self: flex-end;
}

.chat-message.bot,
.chat-message.operator {
    align-self: flex-start;
}

@media (max-width: 640px) {
    #chat-window {
        width: calc(100vw - 2rem);
        height: calc(100vh - 2rem);
        position: fixed;
        top: 1rem;
        left: 1rem;
        right: 1rem;
        bottom: 1rem;
    }
}
</style>

<!-- Chat Widget JavaScript -->
<script>
class ChatWidget {
    constructor() {
        this.sessionId = null;
        this.isOpen = false;
        this.isConnected = false;
        this.lastMessageId = 0;
        this.pollingInterval = null;
        this.retryCount = 0;
        this.maxRetries = 3;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.checkExistingSession();
    }
    
    bindEvents() {
        // Toggle chat
        document.getElementById('chat-toggle').addEventListener('click', () => {
            this.toggleChat();
        });
        
        // Minimize chat
        document.getElementById('minimize-chat').addEventListener('click', () => {
            this.closeChat();
        });
        
        // Send message
        document.getElementById('send-message').addEventListener('click', () => {
            this.sendCurrentMessage();
        });
        
        // Enter key to send
        document.getElementById('chat-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendCurrentMessage();
            }
        });
        
        // Submit visitor info
        document.getElementById('submit-visitor-info').addEventListener('click', () => {
            this.submitVisitorInfo();
        });
        
        // Auto-resize chat input
        const chatInput = document.getElementById('chat-input');
        chatInput.addEventListener('input', () => {
            // Could implement auto-resize here
        });
    }
    
    async checkExistingSession() {
        try {
            const response = await fetch('/api/chat/session', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.sessionId = data.session_id;
                this.displayMessages(data.messages);
                this.updateStatus(data.status);
                this.showNewMessageBadge();
            }
        } catch (error) {
            console.log('No existing session found');
        }
    }
    
    async toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            await this.openChat();
        }
    }
    
    async openChat() {
        this.isOpen = true;
        
        document.getElementById('chat-window').classList.remove('hidden');
        document.getElementById('chat-icon').classList.add('hidden');
        document.getElementById('close-icon').classList.remove('hidden');
        this.hideBadge();
        
        if (!this.sessionId) {
            await this.startNewSession();
        } else {
            this.startPolling();
        }
        
        this.scrollToBottom();
        document.getElementById('chat-input').focus();
    }
    
    closeChat() {
        this.isOpen = false;
        
        document.getElementById('chat-window').classList.add('hidden');
        document.getElementById('chat-icon').classList.remove('hidden');
        document.getElementById('close-icon').classList.add('hidden');
        
        this.stopPolling();
    }
    
    async startNewSession() {
        try {
            this.showTyping();
            
            const response = await fetch('/api/chat/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({})
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.sessionId = data.session_id;
                this.displayMessages(data.messages);
                this.startPolling();
                this.retryCount = 0;
            } else {
                this.showError('Failed to start chat session');
            }
        } catch (error) {
            this.showError('Connection failed. Please try again.');
        } finally {
            this.hideTyping();
        }
    }
    
    async sendCurrentMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        
        if (!message || !this.sessionId) return;
        
        // Clear input and disable send button
        input.value = '';
        this.disableSend();
        
        // Add message to UI immediately
        this.addMessage({
            sender_type: 'visitor',
            sender_name: 'You',
            message: message,
            created_at: new Date().toISOString(),
            formatted_time: new Date().toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            }),
            is_from_visitor: true
        });
        
        try {
            const response = await this.sendMessage(message);
            
            if (response.success) {
                // Update messages with server response
                this.displayMessages(response.messages);
                this.lastMessageId = Math.max(...response.messages.map(m => m.id));
            } else {
                this.showError('Failed to send message');
            }
        } catch (error) {
            this.showError('Message send failed');
        } finally {
            this.enableSend();
        }
    }
    
    async sendMessage(message) {
        const response = await fetch('/api/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                session_id: this.sessionId,
                message: message
            })
        });
        
        return await response.json();
    }
    
    async submitVisitorInfo() {
        const name = document.getElementById('visitor-name').value.trim();
        const email = document.getElementById('visitor-email').value.trim();
        const phone = document.getElementById('visitor-phone').value.trim();
        
        if (!name || !email) {
            alert('Please provide at least your name and email');
            return;
        }
        
        try {
            const response = await fetch('/api/chat/visitor-info', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    name: name,
                    email: email,
                    phone: phone
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('visitor-info-form').classList.add('hidden');
                // Refresh messages
                await this.pollForNewMessages();
            }
        } catch (error) {
            console.error('Failed to submit visitor info:', error);
        }
    }
    
    displayMessages(messages) {
        const container = document.getElementById('chat-messages');
        container.innerHTML = '';
        
        messages.forEach(message => this.addMessage(message));
        this.scrollToBottom();
    }
    
    addMessage(message) {
        const container = document.getElementById('chat-messages');
        const messageEl = document.createElement('div');
        messageEl.className = `chat-message flex ${message.is_from_visitor ? 'justify-end' : 'justify-start'}`;
        
        const bubbleClass = message.is_from_visitor 
            ? 'bg-blue-600 text-white ml-8' 
            : message.is_from_bot 
                ? 'bg-white border border-gray-200 mr-8'
                : 'bg-green-100 text-green-800 mr-8';
        
        const senderIcon = message.is_from_visitor ? '👤' : 
                          message.is_from_bot ? '🤖' : '👨‍💼';
        
        messageEl.innerHTML = `
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${bubbleClass}">
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-xs">${senderIcon}</span>
                    <span class="text-xs font-medium opacity-75">${message.sender_name}</span>
                    <span class="text-xs opacity-50">${message.formatted_time}</span>
                </div>
                <div class="text-sm whitespace-pre-wrap">${this.escapeHtml(message.message)}</div>
            </div>
        `;
        
        container.appendChild(messageEl);
        this.scrollToBottom();
        
        // Check if we need to show visitor info form
        if (message.is_from_bot && message.message.includes('provide your information')) {
            document.getElementById('visitor-info-form').classList.remove('hidden');
        }
    }
    
    startPolling() {
        this.stopPolling();
        this.pollingInterval = setInterval(() => {
            this.pollForNewMessages();
        }, 3000); // Poll every 3 seconds
    }
    
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
    
    async pollForNewMessages() {
        if (!this.sessionId) return;
        
        try {
            const response = await fetch('/api/chat/messages?' + new URLSearchParams({
                session_id: this.sessionId,
                last_message_id: this.lastMessageId
            }));
            
            const data = await response.json();
            
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(message => {
                    this.addMessage(message);
                    this.lastMessageId = Math.max(this.lastMessageId, message.id);
                });
                
                // Show badge if chat is closed
                if (!this.isOpen) {
                    this.showNewMessageBadge();
                }
            }
            
            this.updateStatus(data.session_status);
            this.retryCount = 0;
            
        } catch (error) {
            this.retryCount++;
            if (this.retryCount >= this.maxRetries) {
                this.showError('Connection lost');
                this.stopPolling();
            }
        }
    }
    
    updateStatus(status) {
        const statusEl = document.getElementById('chat-status');
        const statusText = {
            'active': 'We\'re here to help!',
            'waiting': 'We\'ll be with you shortly',
            'closed': 'Chat ended'
        };
        
        statusEl.textContent = statusText[status] || 'Available';
    }
    
    showTyping() {
        document.getElementById('typing-indicator').classList.remove('hidden');
        this.scrollToBottom();
    }
    
    hideTyping() {
        document.getElementById('typing-indicator').classList.add('hidden');
    }
    
    showNewMessageBadge() {
        const badge = document.getElementById('chat-badge');
        const currentCount = parseInt(badge.textContent) || 0;
        badge.textContent = currentCount + 1;
        badge.classList.remove('hidden');
    }
    
    hideBadge() {
        document.getElementById('chat-badge').classList.add('hidden');
        document.getElementById('chat-badge').textContent = '0';
    }
    
    disableSend() {
        document.getElementById('send-message').disabled = true;
    }
    
    enableSend() {
        document.getElementById('send-message').disabled = false;
    }
    
    showError(message) {
        this.addMessage({
            sender_type: 'system',
            sender_name: 'System',
            message: `⚠️ ${message}`,
            created_at: new Date().toISOString(),
            formatted_time: new Date().toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            }),
            is_from_visitor: false,
            is_from_bot: false,
            is_from_operator: false
        });
    }
    
    scrollToBottom() {
        const container = document.getElementById('chat-messages');
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize chat widget when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.chatWidget = new ChatWidget();
});
</script>