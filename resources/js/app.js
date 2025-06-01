// Load Laravel's bootstrap and Alpine.js
import "./bootstrap";
import './echo';
import Alpine from "alpinejs";

// Preline UI v2 import
import "preline/dist/preline.js";

window.Alpine = Alpine;
window.authUserId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
window.isAdmin = document.querySelector('meta[name="is-admin"]')?.getAttribute('content') === 'true';

Alpine.start();


window.WebSocketUtils = {
    // Send notification test
    sendTestNotification() {
        fetch('/client/dashboard/test-notification', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Test notification sent');
            }
        })
        .catch(error => {
            console.error('❌ Failed to send test notification:', error);
        });
    },

    // Get connection status
    getConnectionStatus() {
        return window.Echo.connector.pusher.connection.state;
    },

    // Force reconnect
    reconnect() {
        window.Echo.connector.pusher.connect();
    }
};

// Auto-reconnect on page visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && window.Echo.connector.pusher.connection.state === 'disconnected') {
        console.log('🔄 Page visible, attempting to reconnect...');
        window.WebSocketUtils.reconnect();
    }
});
// Initialize Preline and dark mode once DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    initializeTheme();         // Setup dark/light mode toggle
    initializePreline();       // Core Preline components
});

// === PRELINE INITIALIZATION ===

function initializePreline() {
    try {
        // Preline v2 recommended way
        if (
            typeof window.HSStaticMethods !== "undefined" &&
            typeof window.HSStaticMethods.autoInit === "function"
        ) {
            window.HSStaticMethods.autoInit();
        } else {
            console.warn("⚠️ Preline autoInit not found. Using manual fallback.");
            initializePrelineManually();
        }
    } catch (error) {
        console.error("❌ Error initializing Preline:", error);
        initializePrelineManually();
    }
}

function initializePrelineManually() {
    const components = [
        { name: "HSDropdown", selector: "[data-hs-dropdown]" },
        { name: "HSAccordion", selector: "[data-hs-accordion]" },
        { name: "HSTooltip", selector: "[data-hs-tooltip]" },
        { name: "HSTab", selector: "[data-hs-tab]" },
        { name: "HSOverlay", selector: "[data-hs-overlay]" },
    ];

    components.forEach(({ name, selector }) => {
        const Constructor = window[name];
        if (Constructor) {
            document.querySelectorAll(selector).forEach(el => {
                try {
                    new Constructor(el);
                } catch (e) {
                    console.error(`Failed to init ${name} on`, el, e);
                }
            });
        }
    });
}

// === THEME TOGGLING (LIGHT / DARK) ===

function initializeTheme() {
    const theme = localStorage.getItem("hs_theme") || "auto";
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;

    if (theme === "dark" || (theme === "auto" && prefersDark)) {
        document.documentElement.classList.add("dark");
        document.documentElement.classList.remove("light");
    } else {
        document.documentElement.classList.remove("dark");
        document.documentElement.classList.add("light");
    }

    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            const isDark = document.documentElement.classList.contains("dark");
            if (isDark) {
                document.documentElement.classList.remove("dark");
                document.documentElement.classList.add("light");
                localStorage.setItem("hs_theme", "light");
            } else {
                document.documentElement.classList.remove("light");
                document.documentElement.classList.add("dark");
                localStorage.setItem("hs_theme", "dark");
            }
        });
    }

    // Listen to system preference change
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", e => {
        if ((localStorage.getItem("hs_theme") || "auto") === "auto") {
            if (e.matches) {
                document.documentElement.classList.add("dark");
                document.documentElement.classList.remove("light");
            } else {
                document.documentElement.classList.remove("dark");
                document.documentElement.classList.add("light");
            }
        }
    });
}

// Utility: call this after dynamic content load
window.refreshPreline = function () {
    setTimeout(() => {
        initializePreline();
    }, 100);
};

window.chatWidget = function() {
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
        operatorName: 'Support Team',
        typingUser: '',
        typingTimer: null,
        
        // Initialize with error handling
        init() {
            try {
                console.log('🚀 Initializing chat widget...');
                this.checkOnlineStatus();
                this.setupWebSocketListeners();
                
                // Auto-open if configured
                if (this.$el?.dataset?.autoOpen === 'true') {
                    setTimeout(() => this.openChat(), 1000);
                }
                
                // Check for existing session
                this.loadExistingSession();
                
                // Periodic status checks
                setInterval(() => this.checkOnlineStatus(), 30000);
                
                console.log('✅ Chat widget initialized successfully');
            } catch (error) {
                console.error('❌ Chat widget initialization failed:', error);
                this.handleError(error, 'Chat Widget Init');
            }
        },

        // Enhanced WebSocket setup with fallbacks
        setupWebSocketListeners() {
            if (!window.Echo) {
                console.warn('⚠️ Echo not available - WebSocket features disabled');
                this.connectionStatus = 'unavailable';
                return;
            }

            try {
                // Connection status handlers
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    this.connectionStatus = 'connected';
                    this.isConnected = true;
                    console.log('🟢 Chat WebSocket Connected');
                });

                window.Echo.connector.pusher.connection.bind('disconnected', () => {
                    this.connectionStatus = 'disconnected';
                    this.isConnected = false;
                    console.log('🔴 Chat WebSocket Disconnected');
                });

                window.Echo.connector.pusher.connection.bind('error', (error) => {
                    console.error('❌ Chat WebSocket Error:', error);
                    this.connectionStatus = 'error';
                    this.isConnected = false;
                });

                // Listen for operator status changes
                window.Echo.channel('public-chat-status')
                    .listen('.operator.status.changed', (e) => {
                        this.isOnline = e.total_online_operators > 0;
                        console.log('👥 Operators online:', e.total_online_operators);
                    })
                    .error((error) => {
                        console.error('❌ Failed to listen to public chat status:', error);
                    });
                    
            } catch (error) {
                console.error('❌ WebSocket setup failed:', error);
                this.handleError(error, 'WebSocket Setup');
            }
        },

        // Chat Session Management with improved error handling
        async openChat() {
            try {
                this.isOpen = true;
                this.unreadCount = 0;
                
                if (!this.sessionId) {
                    await this.startChatSession();
                }
                
                this.$nextTick(() => {
                    this.$refs.messageInput?.focus();
                    this.scrollToBottom();
                });
                
                console.log('💬 Chat opened');
            } catch (error) {
                this.handleError(error, 'Open Chat');
            }
        },

        closeChat() {
            this.isOpen = false;
            console.log('💬 Chat closed');
        },

        async startChatSession() {
            try {
                console.log('🔄 Starting chat session...');
                
                const response = await fetch('/api/chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    this.sessionId = data.session_id;
                    this.sessionStatus = data.status;
                    this.messages = data.messages || [];
                    
                    // Start listening to this session
                    this.listenToSession(`chat-session.${data.session_id}`);
                    
                    this.scrollToBottom();
                    console.log('✅ Chat session started:', data.session_id);
                } else {
                    throw new Error(data.message || 'Failed to start chat session');
                }
            } catch (error) {
                console.error('❌ Failed to start chat session:', error);
                this.showError('Unable to start chat session. Please try again.');
            }
        },

        async loadExistingSession() {
            try {
                const response = await fetch('/api/chat/session');
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success) {
                        this.sessionId = data.session_id;
                        this.sessionStatus = data.status;
                        this.messages = data.messages || [];
                        
                        if (data.operator) {
                            this.operatorName = data.operator.name;
                        }
                        
                        // Start listening to this session
                        this.listenToSession(`chat-session.${data.session_id}`);
                        console.log('📂 Loaded existing session:', data.session_id);
                    }
                }
            } catch (error) {
                // Silent fail - no existing session is fine
                console.log('ℹ️ No existing chat session found');
            }
        },

        listenToSession(channel) {
            if (!window.Echo || !channel) {
                console.warn('⚠️ Cannot listen to session - Echo or channel unavailable');
                return;
            }

            try {
                console.log('👂 Listening to channel:', channel);
                
                window.Echo.channel(channel)
                    .listen('.message.sent', (e) => {
                        console.log('📨 New message received:', e);
                        this.handleNewMessage(e);
                    })
                    .listen('.typing.indicator', (e) => {
                        this.handleTypingIndicator(e);
                    })
                    .listen('.session.closed', (e) => {
                        console.log('🔒 Session closed:', e);
                        this.handleSessionClosed(e);
                    })
                    .error((error) => {
                        console.error('❌ Channel listening error:', error);
                    });
                    
            } catch (error) {
                this.handleError(error, 'Session Listening');
            }
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        message: message
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Message will be added via WebSocket
                    this.scrollToBottom();
                    console.log('📤 Message sent successfully');
                } else {
                    throw new Error(data.message || 'Failed to send message');
                }
            } catch (error) {
                console.error('❌ Failed to send message:', error);
                this.showError('Failed to send message');
                this.currentMessage = message; // Restore message
            }
        },

        handleNewMessage(event) {
            try {
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
            } catch (error) {
                this.handleError(error, 'Handle New Message');
            }
        },

        handleTypingIndicator(event) {
            try {
                if (event.user_id !== parseInt(window.authUserId || 0)) {
                    this.isTyping = event.is_typing;
                    this.typingUser = event.user_name;
                    
                    if (event.is_typing) {
                        this.scrollToBottom();
                    }
                }
            } catch (error) {
                this.handleError(error, 'Typing Indicator');
            }
        },

        handleSessionClosed(event) {
            this.sessionStatus = 'closed';
            this.showInfo('Chat session has been closed');
        },

        // Utility Methods
        async checkOnlineStatus() {
            try {
                const response = await fetch('/api/chat/online-status');
                if (response.ok) {
                    const data = await response.json();
                    this.isOnline = data.is_online;
                }
            } catch (error) {
                // Silent fail for status checks
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
            if (!message) return '';
            // Simple URL detection and linking
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return message.replace(urlRegex, '<a href="$1" target="_blank" class="underline">$1</a>');
        },

        formatTime(timestamp) {
            try {
                const date = new Date(timestamp);
                const now = new Date();
                const diff = now - date;

                if (diff < 60000) return 'now';
                if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
                if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
                return date.toLocaleDateString();
            } catch (error) {
                return 'now';
            }
        },

        getConnectionStatusText() {
            switch (this.connectionStatus) {
                case 'connecting': return 'Connecting...';
                case 'connected': return 'Connected';
                case 'disconnected': return 'Disconnected';
                case 'unavailable': return 'WebSocket unavailable';
                case 'error': return 'Connection error';
                default: return 'Connecting...';
            }
        },

        playNotificationSound() {
            try {
                if (this.$el?.dataset?.enableSound === 'true') {
                    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvGUeBT2U2fPGdSYELYHM89yJOQcZZ7zs5Z9NEAxPqOTvt2MdBjiR2O/NeSsFJHfI8N+QQAoUXrPq66hWFAlFnt/xvWYfBT2U2/PHdSUELYDL89uKOQgZZ7vs5qBOEAxOpuPwuGQdBTiP2PDPeSsFJHbH8OCSQgoTXbPq7KlXFAlFnt/wvmcfBTyU3PLIdCUELYDK89uLOggZZrvr56BOEQxOpuLvuWUdBTiP2fDQeSoFJHbH8OGTRQ==');
                    audio.volume = 0.3;
                    audio.play().catch(() => {});
                }
            } catch (error) {
                // Silent fail for audio
            }
        },

        showError(message) {
            this.showNotification('error', message);
        },

        showInfo(message) {
            this.showNotification('info', message);
        },

        showNotification(type, message) {
            try {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-3 rounded-lg shadow-lg text-white max-w-sm ${
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 3000);
            } catch (error) {
                console.error('Failed to show notification:', error);
            }
        },

        handleError(error, context = 'Unknown') {
            console.error(`❌ Chat Error in ${context}:`, error);
            // Could implement more sophisticated error handling here
        },

        async startNewSession() {
            this.sessionId = null;
            this.sessionStatus = null;
            this.messages = [];
            await this.startChatSession();
        },

        // Typing indicators and other methods remain the same...
        handleTyping() {
            if (!this.sessionId) return;
            this.sendTypingIndicator(true);
            
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
            }
            
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        is_typing: isTyping
                    })
                });
            } catch (error) {
                // Silent fail for typing indicators
            }
        }
    }
};
