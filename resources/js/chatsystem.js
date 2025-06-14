/**
 * Centralized Chat System JavaScript
 * Dapat digunakan untuk client dan admin interface
 * Laravel 12 + Alpine.js + Echo integration
 */

class ChatSystem {
    constructor(config = {}) {
        this.config = {
            baseUrl: config.baseUrl || '/api/chat',
            echo: config.echo || null,
            userId: config.userId || null,
            userType: config.userType || 'visitor', // 'visitor', 'operator', 'admin'
            csrfToken: config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            pusherConfig: config.pusherConfig || {},
            ...config
        };

        this.session = null;
        this.messages = [];
        this.isConnected = false;
        this.isTyping = false;
        this.typingTimer = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.eventListeners = new Map();
        this.messageQueue = [];
        this.connectionState = 'disconnected'; // 'disconnected', 'connecting', 'connected'

        this.init();
    }

    /**
     * Initialize chat system
     */
    async init() {
        try {
            await this.setupEcho();
            await this.loadSession();
            this.setupGlobalListeners();
            this.bindEvents();
            
            console.log('Chat system initialized successfully');
        } catch (error) {
            console.error('Failed to initialize chat system:', error);
            this.emit('error', { type: 'initialization', error });
        }
    }

    /**
     * Setup Laravel Echo for real-time communication
     */
    async setupEcho() {
        if (!window.Echo && this.config.pusherConfig.key) {
            try {
                // Dynamic import untuk Echo jika belum ada
                if (typeof window.Pusher === 'undefined') {
                    await this.loadScript('https://js.pusher.com/8.2.0/pusher.min.js');
                }

                const { default: Echo } = await import('laravel-echo');
                
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: this.config.pusherConfig.key,
                    cluster: this.config.pusherConfig.cluster,
                    forceTLS: true,
                    encrypted: true,
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': this.config.csrfToken,
                        },
                    },
                });

                this.echo = window.Echo;
            } catch (error) {
                console.warn('Echo setup failed, using polling fallback:', error);
                this.setupPolling();
            }
        } else {
            this.echo = window.Echo || this.config.echo;
        }
    }

    /**
     * Load external script dinamically
     */
    loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Setup polling sebagai fallback jika Echo gagal
     */
    setupPolling() {
        this.pollingInterval = setInterval(() => {
            if (this.session?.session_id) {
                this.pollMessages();
            }
        }, 3000);
    }

    /**
     * Load atau create chat session
     */
    async loadSession() {
        try {
            let sessionData = this.getStoredSession();
            
            if (!sessionData || this.isSessionExpired(sessionData)) {
                sessionData = await this.createSession();
            }

            this.session = sessionData;
            await this.subscribeToChannel();
            
            // Load existing messages
            if (this.session.session_id) {
                await this.loadMessages();
            }

            this.emit('session:loaded', this.session);
        } catch (error) {
            console.error('Failed to load session:', error);
            this.emit('error', { type: 'session_load', error });
        }
    }

    /**
     * Create new chat session
     */
    async createSession() {
        const response = await this.apiCall('POST', '/session', {
            user_type: this.config.userType,
            user_id: this.config.userId,
            visitor_info: this.getVisitorInfo()
        });

        if (response.success) {
            this.storeSession(response.data);
            return response.data;
        }

        throw new Error('Failed to create session');
    }

    /**
     * Get visitor information
     */
    getVisitorInfo() {
        return {
            user_agent: navigator.userAgent,
            screen_resolution: `${screen.width}x${screen.height}`,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            language: navigator.language,
            referrer: document.referrer,
            current_url: window.location.href
        };
    }

    /**
     * Subscribe to real-time channel
     */
    async subscribeToChannel() {
        if (!this.echo || !this.session?.session_id) return;

        try {
            const channelName = `chat.${this.session.session_id}`;
            this.channel = this.echo.private(channelName);

            this.channel
                .listen('MessageSent', (e) => {
                    this.handleIncomingMessage(e.message);
                })
                .listen('OperatorJoined', (e) => {
                    this.handleOperatorJoined(e.operator);
                })
                .listen('OperatorLeft', (e) => {
                    this.handleOperatorLeft(e.operator);
                })
                .listen('TypingIndicator', (e) => {
                    this.handleTypingIndicator(e);
                })
                .listen('SessionClosed', (e) => {
                    this.handleSessionClosed(e);
                });

            this.connectionState = 'connected';
            this.isConnected = true;
            this.emit('connection:established');

        } catch (error) {
            console.error('Failed to subscribe to channel:', error);
            this.setupPolling();
        }
    }

    /**
     * Send message
     */
    async sendMessage(content, type = 'text', attachments = null) {
        if (!content.trim() && !attachments) return false;

        const message = {
            content: content.trim(),
            type,
            attachments,
            session_id: this.session?.session_id,
            timestamp: new Date().toISOString()
        };

        // Add to local messages immediately (optimistic update)
        const localMessage = {
            ...message,
            id: 'temp_' + Date.now(),
            sender_type: this.config.userType,
            sender_name: this.config.userName || 'You',
            status: 'sending',
            is_temp: true
        };

        this.addMessage(localMessage);
        this.emit('message:sending', localMessage);

        try {
            const response = await this.apiCall('POST', '/messages', message);
            
            if (response.success) {
                // Replace temporary message dengan yang sebenarnya
                this.updateMessage(localMessage.id, {
                    ...response.data,
                    status: 'sent',
                    is_temp: false
                });
                
                this.emit('message:sent', response.data);
                return response.data;
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            this.updateMessage(localMessage.id, { status: 'failed' });
            this.emit('message:failed', { message: localMessage, error });
        }

        return false;
    }

    /**
     * Upload file attachment
     */
    async uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('session_id', this.session?.session_id);

        try {
            const response = await fetch(`${this.config.baseUrl}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                return data.data;
            }
            
            throw new Error(data.message || 'Upload failed');
        } catch (error) {
            console.error('File upload error:', error);
            throw error;
        }
    }

    /**
     * Handle incoming message dari real-time
     */
    handleIncomingMessage(message) {
        // Jangan tambahkan jika message sudah ada (avoid duplicates)
        if (!this.messages.find(m => m.id === message.id)) {
            this.addMessage(message);
            this.emit('message:received', message);
            
            // Play notification sound jika bukan dari user sendiri
            if (message.sender_type !== this.config.userType) {
                this.playNotificationSound();
            }
        }
    }

    /**
     * Handle operator joined
     */
    handleOperatorJoined(operator) {
        this.session.operator = operator;
        this.emit('operator:joined', operator);
    }

    /**
     * Handle operator left
     */
    handleOperatorLeft(operator) {
        this.session.operator = null;
        this.emit('operator:left', operator);
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(data) {
        if (data.user_type !== this.config.userType) {
            this.emit('typing:indicator', data);
        }
    }

    /**
     * Send typing indicator
     */
    sendTypingIndicator(isTyping = true) {
        if (!this.echo || !this.channel) return;

        clearTimeout(this.typingTimer);
        
        if (isTyping) {
            this.channel.whisper('typing', {
                user_type: this.config.userType,
                user_name: this.config.userName,
                is_typing: true
            });

            // Auto stop typing after 3 seconds
            this.typingTimer = setTimeout(() => {
                this.sendTypingIndicator(false);
            }, 3000);
        } else {
            this.channel.whisper('typing', {
                user_type: this.config.userType,
                user_name: this.config.userName,
                is_typing: false
            });
        }
    }

    /**
     * Load messages from server
     */
    async loadMessages(page = 1) {
        if (!this.session?.session_id) return;

        try {
            const response = await this.apiCall('GET', `/messages/${this.session.session_id}?page=${page}`);
            
            if (response.success) {
                if (page === 1) {
                    this.messages = response.data.data || [];
                } else {
                    this.messages = [...(response.data.data || []), ...this.messages];
                }
                
                this.emit('messages:loaded', this.messages);
                return response.data;
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
            this.emit('error', { type: 'load_messages', error });
        }
    }

    /**
     * Poll messages (fallback method)
     */
    async pollMessages() {
        if (!this.session?.session_id) return;

        try {
            const lastMessageId = this.messages.length > 0 ? 
                Math.max(...this.messages.map(m => parseInt(m.id) || 0)) : 0;

            const response = await this.apiCall('GET', 
                `/messages/${this.session.session_id}?since=${lastMessageId}`);
            
            if (response.success && response.data.data) {
                response.data.data.forEach(message => {
                    this.handleIncomingMessage(message);
                });
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }

    /**
     * Add message to local collection
     */
    addMessage(message) {
        // Cek duplicate
        const existingIndex = this.messages.findIndex(m => m.id === message.id);
        
        if (existingIndex >= 0) {
            this.messages[existingIndex] = message;
        } else {
            this.messages.push(message);
            // Sort by timestamp
            this.messages.sort((a, b) => new Date(a.created_at || a.timestamp) - new Date(b.created_at || b.timestamp));
        }
        
        this.emit('messages:updated', this.messages);
    }

    /**
     * Update existing message
     */
    updateMessage(messageId, updates) {
        const index = this.messages.findIndex(m => m.id === messageId);
        if (index >= 0) {
            this.messages[index] = { ...this.messages[index], ...updates };
            this.emit('message:updated', this.messages[index]);
            this.emit('messages:updated', this.messages);
        }
    }

    /**
     * Close chat session
     */
    async closeSession() {
        if (!this.session?.session_id) return;

        try {
            await this.apiCall('POST', `/session/${this.session.session_id}/close`);
            this.cleanup();
            this.emit('session:closed');
        } catch (error) {
            console.error('Failed to close session:', error);
            this.emit('error', { type: 'close_session', error });
        }
    }

    /**
     * Cleanup resources
     */
    cleanup() {
        if (this.channel) {
            this.echo?.leave(`chat.${this.session?.session_id}`);
        }
        
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
        
        clearTimeout(this.typingTimer);
        this.clearStoredSession();
        
        this.session = null;
        this.messages = [];
        this.isConnected = false;
        this.connectionState = 'disconnected';
    }

    /**
     * Setup global event listeners
     */
    setupGlobalListeners() {
        // Handle page visibility
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.handlePageHidden();
            } else {
                this.handlePageVisible();
            }
        });

        // Handle before unload
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });

        // Handle online/offline
        window.addEventListener('online', () => {
            this.handleOnline();
        });

        window.addEventListener('offline', () => {
            this.handleOffline();
        });
    }

    /**
     * Generic API call method
     */
    async apiCall(method, endpoint, data = null) {
        const url = `${this.config.baseUrl}${endpoint}`;
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken
            }
        };

        if (data && ['POST', 'PUT', 'PATCH'].includes(method)) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    /**
     * Session storage management
     */
    getStoredSession() {
        try {
            const stored = localStorage.getItem('chat_session');
            return stored ? JSON.parse(stored) : null;
        } catch (error) {
            console.error('Failed to get stored session:', error);
            return null;
        }
    }

    storeSession(session) {
        try {
            localStorage.setItem('chat_session', JSON.stringify(session));
        } catch (error) {
            console.error('Failed to store session:', error);
        }
    }

    clearStoredSession() {
        try {
            localStorage.removeItem('chat_session');
        } catch (error) {
            console.error('Failed to clear stored session:', error);
        }
    }

    isSessionExpired(session) {
        if (!session.expires_at) return false;
        return new Date(session.expires_at) < new Date();
    }

    /**
     * Event system
     */
    on(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(callback);
    }

    off(event, callback) {
        if (this.eventListeners.has(event)) {
            const listeners = this.eventListeners.get(event);
            const index = listeners.indexOf(callback);
            if (index > -1) {
                listeners.splice(index, 1);
            }
        }
    }

    emit(event, data = null) {
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`Error in event listener for ${event}:`, error);
                }
            });
        }
    }

    /**
     * Utility methods
     */
    playNotificationSound() {
        if ('Audio' in window) {
            try {
                const audio = new Audio('/sounds/notification.mp3');
                audio.volume = 0.3;
                audio.play().catch(() => {
                    // Ignore errors if autoplay is blocked
                });
            } catch (error) {
                // Ignore audio errors
            }
        }
    }

    handlePageHidden() {
        this.emit('page:hidden');
    }

    handlePageVisible() {
        this.emit('page:visible');
        // Reconnect jika perlu
        if (!this.isConnected && this.session) {
            this.reconnect();
        }
    }

    handleOnline() {
        this.emit('connection:online');
        this.reconnect();
    }

    handleOffline() {
        this.emit('connection:offline');
        this.isConnected = false;
    }

    async reconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            this.emit('connection:failed');
            return;
        }

        this.reconnectAttempts++;
        this.emit('connection:reconnecting', this.reconnectAttempts);

        try {
            await this.subscribeToChannel();
            this.reconnectAttempts = 0;
            this.emit('connection:reconnected');
        } catch (error) {
            console.error('Reconnection failed:', error);
            setTimeout(() => this.reconnect(), 5000 * this.reconnectAttempts);
        }
    }

    /**
     * Get session info
     */
    getSession() {
        return this.session;
    }

    /**
     * Get messages
     */
    getMessages() {
        return this.messages;
    }

    /**
     * Get connection state
     */
    getConnectionState() {
        return this.connectionState;
    }

    /**
     * Check if connected
     */
    isConnectedToRealtime() {
        return this.isConnected;
    }
}

// Export untuk digunakan global
window.ChatSystem = ChatSystem;

// Auto-initialize untuk Alpine.js integration
document.addEventListener('alpine:init', () => {
    Alpine.data('chatSystem', (config = {}) => ({
        chatSystem: null,
        isInitialized: false,
        
        async init() {
            try {
                this.chatSystem = new ChatSystem({
                    baseUrl: this.$el.dataset.apiUrl || '/api/chat',
                    userId: this.$el.dataset.userId,
                    userType: this.$el.dataset.userType || 'visitor',
                    userName: this.$el.dataset.userName,
                    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    ...config
                });

                await this.chatSystem.init();
                this.isInitialized = true;
                
                // Setup event listeners
                this.setupEventListeners();
                
            } catch (error) {
                console.error('Failed to initialize chat system:', error);
            }
        },

        setupEventListeners() {
            // Override ini di component masing-masing
        },

        // Wrapper methods
        async sendMessage(content, type = 'text', attachments = null) {
            return await this.chatSystem.sendMessage(content, type, attachments);
        },

        async uploadFile(file) {
            return await this.chatSystem.uploadFile(file);
        },

        getMessages() {
            return this.chatSystem.getMessages();
        },

        getSession() {
            return this.chatSystem.getSession();
        },

        isConnected() {
            return this.chatSystem.isConnectedToRealtime();
        }
    }));
});

// Export default
export default ChatSystem;