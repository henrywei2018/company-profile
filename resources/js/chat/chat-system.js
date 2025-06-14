/**
 * Enhanced Chat System for Laravel 12
 * Supports both visitor and admin chat functionality
 * Fixed for proper authentication and API routing
 */
class ChatSystem {
    constructor(config = {}) {
        // Default configuration
        this.config = {
            baseUrl: '/api/chat',
            adminBaseUrl: '/api/admin/chat',
            userId: null,
            userType: 'visitor', // 'visitor', 'user', 'admin'
            userName: 'Guest',
            csrfToken: null,
            authToken: null,
            debug: false,
            autoReconnect: true,
            maxReconnectAttempts: 5,
            reconnectInterval: 3000,
            heartbeatInterval: 30000,
            ...config
        };

        // State management
        this.session = null;
        this.messages = [];
        this.isConnected = false;
        this.connectionState = 'disconnected';
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = this.config.maxReconnectAttempts;

        // Event system
        this.eventListeners = {};
        this.echo = null;
        this.channel = null;

        // Timers
        this.reconnectTimer = null;
        this.heartbeatTimer = null;
        this.pollTimer = null;

        // Auto-initialize CSRF token if not provided
        if (!this.config.csrfToken) {
            this.config.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        }

        // Auto-initialize user info
        if (!this.config.userId) {
            this.config.userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
        }

        // Initialize
        this.init();
    }

    // =======================
    // INITIALIZATION
    // =======================

    async init() {
        try {
            this.log('🚀 Initializing ChatSystem...');
            
            // Setup Echo if available
            if (typeof window.Echo !== 'undefined') {
                this.echo = window.Echo;
                this.setupEchoListeners();
            } else {
                this.logWarn('⚠️ Laravel Echo not available - using polling fallback');
            }

            // Setup global listeners
            this.setupGlobalListeners();

            // Auto-restore session if exists
            const storedSession = this.getStoredSession();
            if (storedSession) {
                this.log('🔄 Restoring previous session...');
                await this.loadExistingSession();
            }

            this.log('✅ ChatSystem initialized successfully');
            this.emit('init:complete');

        } catch (error) {
            this.logError('❌ Failed to initialize ChatSystem:', error);
            this.emit('init:error', error);
        }
    }

    // =======================
    // EVENT SYSTEM
    // =======================

    on(event, callback) {
        if (!this.eventListeners[event]) {
            this.eventListeners[event] = [];
        }
        this.eventListeners[event].push(callback);
    }

    off(event, callback = null) {
        if (!this.eventListeners[event]) return;
        
        if (callback) {
            this.eventListeners[event] = this.eventListeners[event].filter(cb => cb !== callback);
        } else {
            delete this.eventListeners[event];
        }
    }

    emit(event, data = null) {
        if (this.eventListeners[event]) {
            this.eventListeners[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    this.logError(`Error in event listener for ${event}:`, error);
                }
            });
        }
    }

    // =======================
    // REALTIME CONNECTION
    // =======================

    setupEchoListeners() {
        try {
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
            
            if (response.success && response.session) {
                this.session = response.session;
                this.messages = response.messages || [];
                this.setupSessionListeners();
                
                this.log('✅ Existing session loaded:', response.session.session_id);
                this.emit('session:restored', this.session);
                
                return this.session;
            } else {
                // Clear invalid stored session
                this.clearStoredSession();
                return null;
            }
        } catch (error) {
            this.logWarn('⚠️ Could not load existing session:', error.message);
            this.clearStoredSession();
            return null;
        }
    }

    setupSessionListeners() {
        if (!this.session || !this.echo) return;

        const channelName = `chat-session.${this.session.session_id}`;
        this.channel = this.echo.channel(channelName);

        this.channel
            .listen('.message.sent', (e) => {
                this.log('📨 New message received:', e);
                if (e.message) {
                    this.messages.push(e.message);
                    this.emit('message:received', e.message);
                }
            })
            .listen('.user.typing', (e) => {
                this.log('⌨️ User typing:', e);
                this.emit('typing:indicator', e);
            })
            .listen('.session.status.changed', (e) => {
                this.log('📊 Session status changed:', e);
                if (this.session) {
                    this.session.status = e.status;
                    this.storeSession();
                }
                this.emit('session:status:changed', e);
            })
            .listen('.operator.assigned', (e) => {
                this.log('👤 Operator assigned:', e);
                if (this.session) {
                    this.session.operator = e.operator;
                    this.storeSession();
                }
                this.emit('operator:assigned', e.operator);
            });

        this.log(`👂 Listening to channel: ${channelName}`);
    }

    async closeSession() {
        try {
            if (!this.session) {
                this.logWarn('⚠️ No session to close');
                return;
            }

            this.log('🔴 Closing chat session...');
            
            const response = await this.apiCall('POST', '/close', {
                session_id: this.session.session_id
            });

            if (response.success) {
                this.session.status = 'closed';
                this.emit('session:closed', this.session);
                this.log('✅ Session closed successfully');
            }

            // Cleanup regardless of API response
            this.cleanup();

        } catch (error) {
            this.logError('❌ Failed to close session:', error);
            this.emit('error', { type: 'close_session', error });
            // Still cleanup locally
            this.cleanup();
        }
    }

    // =======================
    // MESSAGE HANDLING
    // =======================

    async sendMessage(content, type = 'text', attachments = null) {
        try {
            if (!this.session) {
                throw new Error('No active chat session');
            }

            this.log(`📤 Sending message: ${content}`);
            
            const messageData = {
                session_id: this.session.session_id,
                content: content,
                type: type
            };

            if (attachments) {
                messageData.attachments = attachments;
            }

            // FIXED: Use correct endpoint
            const response = await this.apiCall('POST', '/messages', messageData);
            
            if (response.success) {
                this.messages.push(response.message);
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

    async getMessages() {
        try {
            if (!this.session) {
                return [];
            }

            const response = await this.apiCall('GET', `/messages/${this.session.session_id}`);
            
            if (response.success) {
                this.messages = response.messages || [];
                this.emit('messages:loaded', this.messages);
                return this.messages;
            } else {
                throw new Error(response.message || 'Failed to get messages');
            }
        } catch (error) {
            this.logError('❌ Failed to get messages:', error);
            return this.messages; // Return cached messages
        }
    }

    async sendTypingIndicator(isTyping = true) {
        try {
            if (!this.session) return;

            await this.apiCall('POST', '/typing', {
                session_id: this.session.session_id,
                is_typing: isTyping
            });

            this.log(`⌨️ Typing indicator sent: ${isTyping}`);
        } catch (error) {
            // Silent fail for typing indicators
            this.logWarn('⚠️ Failed to send typing indicator:', error.message);
        }
    }

    // =======================
    // ADMIN METHODS
    // =======================

    async getAdminSessions(filter = 'all') {
        try {
            const response = await this.apiCall('GET', '/sessions', null, this.config.adminBaseUrl);
            
            if (response.success) {
                return response.sessions || [];
            } else {
                throw new Error(response.message || 'Failed to get sessions');
            }
        } catch (error) {
            this.logError('❌ Failed to get admin sessions:', error);
            this.emit('error', { type: 'admin_sessions', error });
            throw error;
        }
    }

    async assignSessionToMe(sessionId) {
        try {
            const response = await this.apiCall('POST', `/${sessionId}/assign`, null, this.config.adminBaseUrl);
            
            if (response.success) {
                this.log('✅ Session assigned successfully');
                this.emit('admin:session:assigned', response);
                return response;
            } else {
                throw new Error(response.message || 'Failed to assign session');
            }
        } catch (error) {
            this.logError('❌ Failed to assign session:', error);
            this.emit('error', { type: 'assign_session', error });
            throw error;
        }
    }

    async setOperatorStatus(status) {
        try {
            const response = await this.apiCall('POST', '/operator/status', { status }, this.config.adminBaseUrl);
            
            if (response.success) {
                this.log(`✅ Operator status set to: ${status}`);
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
        
        // FIXED: Proper session-based authentication
        const options = {
            method,
            credentials: 'same-origin', // CRITICAL for session auth
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest', // Required for Laravel
            }
        };

        // Add CSRF token for Laravel
        const csrfToken = this.config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            options.headers['X-CSRF-TOKEN'] = csrfToken;
        }

        // Add authentication header if using token auth (fallback)
        if (this.config.authToken) {
            options.headers['Authorization'] = `Bearer ${this.config.authToken}`;
        }

        if (data && ['POST', 'PUT', 'PATCH'].includes(method)) {
            options.body = JSON.stringify(data);
        }

        this.log(`🌐 API Call: ${method} ${url}`, data);

        try {
            const response = await fetch(url, options);
            
            if (!response.ok) {
                let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                
                try {
                    const errorText = await response.text();
                    this.logError(`${errorMessage}:`, errorText);
                    
                    // Try to parse JSON error
                    try {
                        const errorJson = JSON.parse(errorText);
                        if (errorJson.message) {
                            errorMessage = errorJson.message;
                        }
                    } catch (e) {
                        // Keep original error message
                    }
                } catch (e) {
                    this.logError('Failed to read error response:', e.message);
                }
                
                throw new Error(errorMessage);
            }

            const result = await response.json();
            this.log(`✅ API Response:`, result);
            
            return result;
        } catch (error) {
            this.logError('API Call Failed:', error.message);
            throw error;
        }
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
        this.log('📴 Network offline');
        this.emit('network:offline');
    }

    // =======================
    // UTILITY METHODS
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
    // DEBUGGING METHODS
    // =======================

    debugAuth() {
        console.group('🔍 Chat System Auth Debug');
        
        // Check CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('CSRF Token:', csrfToken ? `${csrfToken.substring(0, 10)}...` : 'NOT FOUND');
        
        // Check user data
        const userId = this.config.userId || document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
        console.log('User ID:', userId || 'NOT FOUND');
        
        // Check if user is authenticated
        console.log('Is Admin:', window.isAdmin || 'UNKNOWN');
        
        // Test API endpoint
        fetch('/api/chat/online-status', {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('API Test Status:', response.status, response.statusText);
            return response.json();
        })
        .then(data => {
            console.log('API Test Response:', data);
        })
        .catch(error => {
            console.error('API Test Failed:', error);
        });
        
        console.groupEnd();
    }

    async testAuth() {
        console.log('🧪 Testing Chat Authentication...');
        
        try {
            // Test 1: Check online status (should work)
            const statusResponse = await this.apiCall('GET', '/online-status');
            console.log('✅ Online Status Test:', statusResponse);
            
            // Test 2: Try to start session (requires auth)
            const sessionResponse = await this.apiCall('POST', '/start');
            console.log('✅ Start Session Test:', sessionResponse);
            
            return true;
        } catch (error) {
            console.error('❌ Auth Test Failed:', error);
            return false;
        }
    }

    // =======================
    // CLEANUP
    // =======================

    cleanup() {
        // Clear timers
        clearTimeout(this.reconnectTimer);
        clearTimeout(this.heartbeatTimer);
        clearTimeout(this.pollTimer);
        
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