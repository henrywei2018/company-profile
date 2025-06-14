/**
 * Clean ChatSystem Class
 * Compatible with Clean Routes & Controller
 * Laravel 12 + Alpine.js + Echo integration
 */

class ChatSystem {
    constructor(config = {}) {
        this.config = {
            baseUrl: config.baseUrl || '/api/chat',
            adminBaseUrl: config.adminBaseUrl || '/api/admin/chat',
            userId: config.userId || null,
            userType: config.userType || 'visitor', // 'visitor', 'operator', 'admin'
            userName: config.userName || 'Anonymous',
            csrfToken: config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            debug: config.debug || false,
            ...config
        };

        // State management
        this.session = null;
        this.messages = [];
        this.isConnected = false;
        this.connectionState = 'disconnected';
        this.eventListeners = new Map();
        
        // Timers
        this.typingTimer = null;
        this.pollTimer = null;
        this.reconnectTimer = null;
        
        // Settings
        this.pollingInterval = 3000; // 3 seconds
        this.maxReconnectAttempts = 5;
        this.reconnectAttempts = 0;

        // Initialize
        this.init();
    }

    // =======================
    // INITIALIZATION
    // =======================

    async init() {
        try {
            this.log('🚀 Initializing ChatSystem...');
            
            this.setupEcho();
            this.setupGlobalListeners();
            await this.loadExistingSession();
            
            this.log('✅ ChatSystem initialized successfully');
            this.emit('initialized');
        } catch (error) {
            this.logError('❌ ChatSystem initialization failed:', error);
            this.emit('error', { type: 'initialization', error });
        }
    }

    setupEcho() {
        if (window.Echo) {
            this.echo = window.Echo;
            this.setupEchoListeners();
            this.log('📡 Echo integration ready');
        } else {
            this.logWarn('⚠️ Echo not available - using polling fallback');
        }
    }

    setupEchoListeners() {
        if (!this.echo) return;

        try {
            // Global connection status
            this.echo.connector.pusher.connection.bind('connected', () => {
                this.connectionState = 'connected';
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.log('🟢 WebSocket Connected');
                this.emit('connection:established');
            });

            this.echo.connector.pusher.connection.bind('disconnected', () => {
                this.connectionState = 'disconnected';
                this.isConnected = false;
                this.log('🔴 WebSocket Disconnected');
                this.emit('connection:lost');
                this.handleReconnect();
            });

            this.echo.connector.pusher.connection.bind('error', (error) => {
                this.connectionState = 'error';
                this.isConnected = false;
                this.logError('❌ WebSocket Error:', error);
                this.emit('connection:error', error);
            });

        } catch (error) {
            this.logError('❌ Echo listeners setup failed:', error);
        }
    }

    setupGlobalListeners() {
        // Page visibility handling
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.handlePageHidden();
            } else {
                this.handlePageVisible();
            }
        });

        // Network status
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => this.cleanup());
    }

    // =======================
    // SESSION MANAGEMENT  
    // =======================

    async startSession() {
        try {
            this.log('🔄 Starting chat session...');
            
            const endpoint = this.config.userType === 'visitor' ? '/start' : '/start';
            const response = await this.apiCall('POST', endpoint);
            
            if (response.success) {
                this.session = {
                    session_id: response.session_id,
                    status: response.status,
                    started_at: new Date(),
                    operator: null
                };
                
                this.storeSession();
                this.setupSessionListeners();
                
                this.log('✅ Session started:', response.session_id);
                this.emit('session:started', this.session);
                
                return this.session;
            } else {
                throw new Error(response.message || 'Failed to start session');
            }
        } catch (error) {
            this.logError('❌ Failed to start session:', error);
            this.emit('error', { type: 'start_session', error });
            throw error;
        }
    }

    async loadExistingSession() {
        try {
            // Try to get session from server first
            const response = await this.apiCall('GET', '/session');
            
            if (response.success) {
                this.session = {
                    session_id: response.session_id,
                    status: response.status,
                    operator: response.operator || null,
                    queue_position: response.queue_position || 0
                };
                
                this.storeSession();
                this.setupSessionListeners();
                await this.loadMessages();
                
                this.log('📂 Loaded existing session:', response.session_id);
                this.emit('session:loaded', this.session);
                
                return this.session;
            }
        } catch (error) {
            // Silent fail - no existing session is fine
            this.log('ℹ️ No existing session found');
        }
        
        // Fallback: try localStorage
        const stored = this.getStoredSession();
        if (stored && stored.session_id) {
            this.session = stored;
            this.log('📱 Restored session from storage');
        }
        
        return null;
    }

    async closeSession() {
        if (!this.session?.session_id) return;

        try {
            await this.apiCall('POST', '/close', {
                session_id: this.session.session_id
            });
            
            this.cleanup();
            this.log('✅ Session closed');
            this.emit('session:closed');
        } catch (error) {
            this.logError('❌ Failed to close session:', error);
            this.emit('error', { type: 'close_session', error });
        }
    }

    // =======================
    // MESSAGE HANDLING
    // =======================

    async sendMessage(content, type = 'text') {
        if (!content.trim()) {
            throw new Error('Message cannot be empty');
        }

        if (!this.session?.session_id) {
            throw new Error('No active session');
        }

        try {
            const endpoint = this.config.userType === 'admin' || this.config.userType === 'operator' 
                ? `/${this.session.session_id}/reply` 
                : '/send-message';
            
            const baseUrl = this.config.userType === 'admin' || this.config.userType === 'operator'
                ? this.config.adminBaseUrl
                : this.config.baseUrl;

            const payload = this.config.userType === 'admin' || this.config.userType === 'operator'
                ? { message: content }
                : { session_id: this.session.session_id, message: content };

            const response = await this.apiCall('POST', endpoint, payload, baseUrl);
            
            if (response.success) {
                this.addMessage(response.message);
                this.log('✅ Message sent successfully');
                this.emit('message:sent', response.message);
                return response.message;
            } else {
                throw new Error(response.message || 'Failed to send message');
            }
        } catch (error) {
            this.logError('❌ Failed to send message:', error);
            this.emit('error', { type: 'send_message', error });
            throw error;
        }
    }

    async loadMessages(since = null) {
        if (!this.session?.session_id) return [];

        try {
            const endpoint = this.config.userType === 'admin' || this.config.userType === 'operator'
                ? `/${this.session.session_id}/messages`
                : '/messages';
            
            const baseUrl = this.config.userType === 'admin' || this.config.userType === 'operator'
                ? this.config.adminBaseUrl
                : this.config.baseUrl;

            const params = new URLSearchParams();
            
            if (this.config.userType === 'visitor') {
                params.append('session_id', this.session.session_id);
            }
            
            if (since) {
                params.append('since', since);
            }

            const url = `${endpoint}?${params.toString()}`;
            const response = await this.apiCall('GET', url, null, baseUrl);
            
            if (response.success) {
                if (since) {
                    // Append new messages
                    response.messages.forEach(msg => this.addMessage(msg));
                } else {
                    // Replace all messages
                    this.messages = response.messages || [];
                }
                
                this.emit('messages:loaded', this.messages);
                return response.messages || [];
            }
        } catch (error) {
            this.logError('❌ Failed to load messages:', error);
            this.emit('error', { type: 'load_messages', error });
        }
        
        return [];
    }

    addMessage(message) {
        if (!message || !message.id) return;

        // Check for duplicates
        const existingIndex = this.messages.findIndex(m => m.id === message.id);
        
        if (existingIndex >= 0) {
            // Update existing message
            this.messages[existingIndex] = { ...this.messages[existingIndex], ...message };
        } else {
            // Add new message
            this.messages.push(message);
            
            // Sort by timestamp
            this.messages.sort((a, b) => {
                const timeA = new Date(a.created_at || 0).getTime();
                const timeB = new Date(b.created_at || 0).getTime();
                return timeA - timeB;
            });
        }
        
        this.emit('message:added', message);
        this.emit('messages:updated', this.messages);
    }

    // =======================
    // TYPING INDICATORS
    // =======================

    sendTypingIndicator(isTyping = true) {
        if (!this.session?.session_id) return;

        // Clear existing timer
        clearTimeout(this.typingTimer);

        // Send typing indicator via API
        this.apiCall('POST', '/typing', {
            session_id: this.session.session_id,
            is_typing: isTyping
        }).catch(error => {
            // Silent fail for typing indicators
            this.log('⚠️ Typing indicator failed:', error.message);
        });

        // Auto-stop typing after 3 seconds
        if (isTyping) {
            this.typingTimer = setTimeout(() => {
                this.sendTypingIndicator(false);
            }, 3000);
        }
    }

    // =======================
    // REAL-TIME LISTENERS
    // =======================

    setupSessionListeners() {
        if (!this.echo || !this.session?.session_id) return;

        try {
            const channelName = `chat-session.${this.session.session_id}`;
            this.channel = this.echo.channel(channelName);
            
            this.channel
                .listen('.message.sent', (e) => {
                    this.log('📨 Real-time message received:', e);
                    if (e.message) {
                        this.addMessage(e.message);
                        this.emit('message:received', e.message);
                    }
                })
                .listen('.user.typing', (e) => {
                    this.log('⌨️ Typing indicator:', e);
                    this.emit('typing:indicator', e);
                })
                .listen('.session.status.changed', (e) => {
                    this.log('🔄 Session status changed:', e);
                    if (e.session && this.session) {
                        this.session.status = e.session.status;
                        this.session.operator = e.session.assigned_operator || null;
                        this.storeSession();
                        this.emit('session:updated', this.session);
                    }
                });

            this.log('👂 Listening to channel:', channelName);
            this.emit('channel:joined', channelName);
            
        } catch (error) {
            this.logError('❌ Failed to setup session listeners:', error);
        }
    }

    // =======================
    // ADMIN-SPECIFIC METHODS
    // =======================

    async getAdminSessions(filter = 'all', page = 1) {
        if (this.config.userType !== 'admin' && this.config.userType !== 'operator') {
            throw new Error('Admin access required');
        }

        try {
            const params = new URLSearchParams({
                filter,
                page: page.toString(),
                per_page: '50'
            });

            const response = await this.apiCall('GET', `/sessions?${params}`, null, this.config.adminBaseUrl);
            
            if (response.success) {
                this.emit('admin:sessions:loaded', response.sessions);
                return response;
            }
        } catch (error) {
            this.logError('❌ Failed to load admin sessions:', error);
            this.emit('error', { type: 'load_admin_sessions', error });
            throw error;
        }
    }

    async assignSessionToMe(sessionId) {
        if (this.config.userType !== 'admin' && this.config.userType !== 'operator') {
            throw new Error('Admin access required');
        }

        try {
            const response = await this.apiCall('POST', `/${sessionId}/assign`, {}, this.config.adminBaseUrl);
            
            if (response.success) {
                this.emit('admin:session:assigned', response.operator);
                return response.operator;
            }
        } catch (error) {
            this.logError('❌ Failed to assign session:', error);
            this.emit('error', { type: 'assign_session', error });
            throw error;
        }
    }

    async setOperatorStatus(status) {
        if (this.config.userType !== 'admin' && this.config.userType !== 'operator') {
            throw new Error('Admin access required');
        }

        try {
            const response = await this.apiCall('POST', '/operator/status', { status }, this.config.adminBaseUrl);
            
            if (response.success) {
                this.emit('admin:status:updated', response.operator);
                return response.operator;
            }
        } catch (error) {
            this.logError('❌ Failed to set operator status:', error);
            this.emit('error', { type: 'operator_status', error });
            throw error;
        }
    }

    // =======================
    // API UTILITIES
    // =======================

    async apiCall(method, endpoint, data = null, customBaseUrl = null) {
        const baseUrl = customBaseUrl || this.config.baseUrl;
        const url = `${baseUrl}${endpoint}`;
        
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken
            }
        };

        // Add authentication header if needed
        if (this.config.authToken) {
            options.headers['Authorization'] = `Bearer ${this.config.authToken}`;
        }

        if (data && ['POST', 'PUT', 'PATCH'].includes(method)) {
            options.body = JSON.stringify(data);
        }

        this.log(`🌐 API Call: ${method} ${url}`, data);

        const response = await fetch(url, options);
        
        if (!response.ok) {
            const errorText = await response.text();
            this.logError(`HTTP ${response.status}:`, errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        this.log(`✅ API Response:`, result);
        
        return result;
    }

    // =======================
    // SESSION STORAGE
    // =======================

    storeSession() {
        if (this.session) {
            try {
                localStorage.setItem('chat_session', JSON.stringify(this.session));
            } catch (error) {
                this.logWarn('⚠️ Failed to store session:', error.message);
            }
        }
    }

    getStoredSession() {
        try {
            const stored = localStorage.getItem('chat_session');
            return stored ? JSON.parse(stored) : null;
        } catch (error) {
            this.logWarn('⚠️ Failed to parse stored session:', error.message);
            return null;
        }
    }

    clearStoredSession() {
        try {
            localStorage.removeItem('chat_session');
        } catch (error) {
            this.logWarn('⚠️ Failed to clear stored session:', error.message);
        }
    }

    // =======================
    // CONNECTION MANAGEMENT
    // =======================

    handleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            this.logError('❌ Max reconnection attempts reached');
            this.emit('connection:failed');
            return;
        }

        this.reconnectAttempts++;
        const delay = Math.min(1000 * this.reconnectAttempts, 10000); // Max 10 seconds

        this.log(`🔄 Reconnecting in ${delay}ms (attempt ${this.reconnectAttempts})`);
        
        this.reconnectTimer = setTimeout(() => {
            if (this.echo) {
                this.echo.connector.pusher.connect();
            }
        }, delay);
    }

    handlePageHidden() {
        this.log('📴 Page hidden - pausing activity');
        clearTimeout(this.pollTimer);
        this.emit('page:hidden');
    }

    handlePageVisible() {
        this.log('📱 Page visible - resuming activity');
        if (!this.isConnected) {
            this.handleReconnect();
        }
        this.emit('page:visible');
    }

    handleOnline() {
        this.log('🌐 Back online');
        if (!this.isConnected && this.echo) {
            this.echo.connector.pusher.connect();
        }
        this.emit('network:online');
    }

    handleOffline() {
        this.log('📵 Gone offline');
        this.emit('network:offline');
    }

    // =======================
    // EVENT SYSTEM
    // =======================

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
        this.log(`📢 Event: ${event}`, data);
        
        if (this.eventListeners.has(event)) {
            this.eventListeners.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    this.logError(`❌ Event callback error for ${event}:`, error);
                }
            });
        }
    }

    // =======================
    // UTILITIES
    // =======================

    getSession() {
        return this.session;
    }

    getMessages() {
        return this.messages;
    }

    isConnectedToRealtime() {
        return this.isConnected;
    }

    getConnectionState() {
        return this.connectionState;
    }

    // =======================
    // CLEANUP
    // =======================

    cleanup() {
        this.log('🧹 Cleaning up ChatSystem...');
        
        // Clear timers
        clearTimeout(this.typingTimer);
        clearTimeout(this.pollTimer);
        clearTimeout(this.reconnectTimer);
        
        // Leave channels
        if (this.channel && this.echo) {
            this.echo.leave(`chat-session.${this.session?.session_id}`);
        }
        
        // Clear state
        this.session = null;
        this.messages = [];
        this.isConnected = false;
        this.connectionState = 'disconnected';
        
        // Clear storage
        this.clearStoredSession();
        
        this.emit('cleanup:complete');
    }

    // =======================
    // LOGGING
    // =======================

    log(message, data = null) {
        if (this.config.debug) {
            console.log(`[ChatSystem] ${message}`, data || '');
        }
    }

    logWarn(message, data = null) {
        console.warn(`[ChatSystem] ${message}`, data || '');
    }

    logError(message, data = null) {
        console.error(`[ChatSystem] ${message}`, data || '');
    }
}

// =======================
// ALPINE.JS INTEGRATION
// =======================

// Auto-initialize untuk Alpine.js integration
document.addEventListener('alpine:init', () => {
    Alpine.data('chatSystem', (config = {}) => ({
        chatSystem: null,
        isInitialized: false,
        
        async init() {
            try {
                this.chatSystem = new ChatSystem({
                    baseUrl: this.$el.dataset.apiUrl || '/api/chat',
                    adminBaseUrl: this.$el.dataset.adminApiUrl || '/api/admin/chat',
                    userId: this.$el.dataset.userId,
                    userType: this.$el.dataset.userType || 'visitor',
                    userName: this.$el.dataset.userName,
                    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    debug: this.$el.dataset.debug === 'true',
                    ...config
                });

                this.isInitialized = true;
                
                // Setup event listeners
                this.setupEventListeners();
                
            } catch (error) {
                console.error('Failed to initialize chat system:', error);
            }
        },

        setupEventListeners() {
            // Override this in specific components
        },

        // Wrapper methods for easy Alpine.js usage
        async sendMessage(content, type = 'text') {
            return await this.chatSystem.sendMessage(content, type);
        },

        async startSession() {
            return await this.chatSystem.startSession();
        },

        async closeSession() {
            return await this.chatSystem.closeSession();
        },

        sendTyping(isTyping = true) {
            this.chatSystem.sendTypingIndicator(isTyping);
        },

        getMessages() {
            return this.chatSystem.getMessages();
        },

        getSession() {
            return this.chatSystem.getSession();
        },

        isConnected() {
            return this.chatSystem.isConnectedToRealtime();
        },

        // Admin methods
        async getAdminSessions(filter = 'all') {
            return await this.chatSystem.getAdminSessions(filter);
        },

        async assignToMe(sessionId) {
            return await this.chatSystem.assignSessionToMe(sessionId);
        },

        async setStatus(status) {
            return await this.chatSystem.setOperatorStatus(status);
        }
    }));
});

// Export default
export default ChatSystem;