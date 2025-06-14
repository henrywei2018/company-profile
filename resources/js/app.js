// Load Laravel's bootstrap and Alpine.js
import "./bootstrap";
import './echo';
import Alpine from "alpinejs";

// Preline UI v2 import
import "preline/dist/preline.js";
import ChatSystem from './chat/chat-system.js';

window.ChatSystem = ChatSystem;
window.Alpine = Alpine;
window.authUserId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
window.isAdmin = document.querySelector('meta[name="is-admin"]')?.getAttribute('content') === 'true';

Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize chat widgets
    const chatWidgets = document.querySelectorAll('[data-chat-widget]');
    chatWidgets.forEach(widget => {
        if (!widget.chatSystemInstance) {
            const config = {
                baseUrl: widget.dataset.apiUrl || '/api/chat',
                userId: widget.dataset.userId,
                userType: widget.dataset.userType || 'visitor',
                userName: widget.dataset.userName,
                theme: widget.dataset.theme || 'blue'
            };
            
            widget.chatSystemInstance = new ChatSystem(config);
        }
    });
});

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
            
            // Manual fallback initialization for common components
            if (typeof window.HSDropdown !== "undefined") window.HSDropdown.autoInit();
            if (typeof window.HSCollapse !== "undefined") window.HSCollapse.autoInit();
            if (typeof window.HSOverlay !== "undefined") window.HSOverlay.autoInit();
            if (typeof window.HSNavbar !== "undefined") window.HSNavbar.autoInit();
            if (typeof window.HSAccordion !== "undefined") window.HSAccordion.autoInit();
            if (typeof window.HSCarousel !== "undefined") window.HSCarousel.autoInit();
            if (typeof window.HSTab !== "undefined") window.HSTab.autoInit();
            if (typeof window.HSModal !== "undefined") window.HSModal.autoInit();
            if (typeof window.HSTooltip !== "undefined") window.HSTooltip.autoInit();
        }
        
        console.log("✅ Preline components initialized");
    } catch (error) {
        console.error("❌ Preline initialization failed:", error);
    }
}

// === THEME MANAGEMENT ===

function initializeTheme() {
    try {
        const themeToggle = document.getElementById("theme-toggle");
        
        if (themeToggle) {
            themeToggle.addEventListener("click", toggleTheme);
        }

        // Apply initial theme
        applyStoredTheme();
        
        console.log("✅ Theme system initialized");
    } catch (error) {
        console.error("❌ Theme initialization failed:", error);
    }
}

function toggleTheme() {
    const currentTheme = localStorage.getItem("hs_theme") || "auto";
    
    const newTheme = currentTheme === "dark" ? "light" : 
                    currentTheme === "light" ? "auto" : "dark";
    
    localStorage.setItem("hs_theme", newTheme);
    applyTheme(newTheme);
}

function applyStoredTheme() {
    const storedTheme = localStorage.getItem("hs_theme") || "auto";
    applyTheme(storedTheme);
}

function applyTheme(theme) {
    const html = document.documentElement;
    
    if (theme === "auto") {
        const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
        html.classList.toggle("dark", prefersDark);
        html.classList.toggle("light", !prefersDark);
    } else {
        html.classList.toggle("dark", theme === "dark");
        html.classList.toggle("light", theme === "light");
    }
}

// Listen to system preference changes
if (window.matchMedia) {
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", e => {
        if ((localStorage.getItem("hs_theme") || "auto") === "auto") {
            applyTheme("auto");
        }
    });
}

// Utility: call this after dynamic content load
window.refreshPreline = function () {
    setTimeout(() => {
        initializePreline();
    }, 100);
};

// =======================
// CHAT WIDGET - UPDATED FOR CLEAN ROUTES
// =======================

window.chatWidget = function() {
    return {
        // State
        isOpen: false,
        isOnline: false,
        isTyping: false,
        isConnected: false,
        isLoading: false,
        isSending: false,
        unreadCount: 0,
        currentMessage: '',
        messages: [],
        sessionId: null,
        sessionStatus: null,
        connectionStatus: 'disconnected',
        operatorName: 'Support Team',
        typingUser: '',
        typingTimer: null,
        pollingTimer: null,
        pollingInterval: 2000,
        lastMessageTime: null,
        
        // API Endpoints - UPDATED FOR CLEAN ROUTES
        endpoints: {
            start: '/api/chat/start',                    // ✅ Updated
            session: '/api/chat/session',                // ✅ Updated  
            sendMessage: '/api/chat/send-message',       // ✅ Already correct
            getMessages: '/api/chat/messages',           // ✅ Already correct
            typing: '/api/chat/typing',                  // ✅ Already correct
            close: '/api/chat/close',                    // ✅ Updated
            onlineStatus: '/api/chat/online-status'      // ✅ Already correct
        },
        
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

        // UPDATED: Check online status
        async checkOnlineStatus() {
            try {
                const response = await fetch(this.endpoints.onlineStatus);
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.isOnline = data.online;
                    }
                }
            } catch (error) {
                console.warn('⚠️ Failed to check online status:', error);
                this.isOnline = false;
            }
        },

        // UPDATED: Load existing session
        async loadExistingSession() {
            try {
                const response = await fetch(this.endpoints.session);
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success) {
                        this.sessionId = data.session_id;
                        this.sessionStatus = data.status;
                        
                        if (data.operator) {
                            this.operatorName = data.operator.name;
                        }
                        
                        // Load messages for this session
                        await this.loadMessages();
                        
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

        // UPDATED: Start chat session
        async startChatSession() {
            try {
                console.log('🔄 Starting chat session...');
                this.isLoading = true;
                
                const response = await fetch(this.endpoints.start, {
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
                    
                    // Load welcome messages
                    await this.loadMessages();
                    
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
            } finally {
                this.isLoading = false;
            }
        },

        // UPDATED: Send message
        async sendMessage() {
            if (!this.currentMessage.trim() || !this.sessionId || this.isSending) return;

            const message = this.currentMessage.trim();
            this.currentMessage = '';
            this.isSending = true;

            // Optimistic UI update
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
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }

                const response = await fetch(this.endpoints.sendMessage, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        message: message
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error(`HTTP ${response.status}:`, errorText);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Remove optimistic message
                    const optimisticIndex = this.messages.findIndex(m => m.id === optimisticMessage.id);
                    if (optimisticIndex !== -1) {
                        this.messages.splice(optimisticIndex, 1);
                    }

                    // Add real message
                    this.messages.push(data.message);
                    this.scrollToBottom();
                    
                    console.log('✅ Message sent successfully');
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
                
                // Show user-friendly error
                if (error.message.includes('HTTP 419')) {
                    this.showError('Session expired. Please refresh the page.');
                } else if (error.message.includes('HTTP 422')) {
                    this.showError('Invalid message format.');
                } else if (error.message.includes('HTTP 401')) {
                    this.showError('Authentication required. Please login again.');
                } else {
                    this.showError('Failed to send message. Please try again.');
                }
            } finally {
                this.isSending = false;
            }
        },

        // UPDATED: Load messages  
        async loadMessages() {
            if (!this.sessionId) return;

            try {
                const url = new URL(this.endpoints.getMessages, window.location.origin);
                url.searchParams.append('session_id', this.sessionId);
                
                if (this.lastMessageTime) {
                    url.searchParams.append('since', Math.floor(this.lastMessageTime / 1000));
                }

                const response = await fetch(url);
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.success) {
                        if (this.lastMessageTime) {
                            // Append new messages
                            this.messages.push(...data.messages);
                        } else {
                            // Initial load
                            this.messages = data.messages;
                        }
                        
                        this.sessionStatus = data.session_status;
                        this.lastMessageTime = data.last_message_time;
                        this.scrollToBottom();
                    }
                }
            } catch (error) {
                console.warn('⚠️ Failed to load messages:', error);
            }
        },

        // UPDATED: Send typing indicator
        async sendTypingIndicator(isTyping) {
            if (!this.sessionId) return;

            try {
                await fetch(this.endpoints.typing, {
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
                console.warn('⚠️ Failed to send typing indicator:', error);
            }
        },

        // UPDATED: Close session
        async closeSession() {
            if (!this.sessionId) return;

            try {
                const response = await fetch(this.endpoints.close, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.sessionStatus = 'closed';
                        console.log('✅ Chat session closed');
                    }
                }
            } catch (error) {
                console.warn('⚠️ Failed to close session:', error);
            }
        },

        // Open chat interface
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

        // Close chat interface
        closeChat() {
            this.isOpen = false;
            console.log('💬 Chat closed');
        },

        // Listen to WebSocket channel
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
                        if (e.message && e.message.sender_type !== 'visitor') {
                            this.messages.push(e.message);
                            this.scrollToBottom();
                            
                            if (!this.isOpen) {
                                this.unreadCount++;
                                this.showNotification('New message received');
                            }
                        }
                    })
                    .listen('.user.typing', (e) => {
                        if (e.user_type === 'operator' && e.is_typing) {
                            this.typingUser = e.user_name;
                            this.isTyping = true;
                            
                            clearTimeout(this.typingTimer);
                            this.typingTimer = setTimeout(() => {
                                this.isTyping = false;
                                this.typingUser = '';
                            }, 3000);
                        } else if (e.user_type === 'operator') {
                            this.isTyping = false;
                            this.typingUser = '';
                        }
                    })
                    .listen('.session.status.changed', (e) => {
                        if (e.session && e.session.session_id === this.sessionId) {
                            this.sessionStatus = e.session.status;
                            
                            if (e.session.assigned_operator) {
                                this.operatorName = e.session.assigned_operator.name;
                            }
                        }
                    });
                    
            } catch (error) {
                console.error('❌ Failed to listen to session:', error);
            }
        },

        // Handle typing events
        handleTyping() {
            if (!this.sessionId) return;
            this.sendTypingIndicator(true);
            
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
            }
            
            this.typingTimer = setTimeout(() => {
                this.sendTypingIndicator(false);
            }, 3000);
        },

        // Start new session
        async startNewSession() {
            this.sessionId = null;
            this.sessionStatus = null;
            this.messages = [];
            this.lastMessageTime = null;
            await this.startChatSession();
        },

        // Send quick message
        async sendQuickMessage(message) {
            this.currentMessage = message;
            await this.sendMessage();
        },

        // Scroll to bottom
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        // Show notification
        showNotification(message, type = 'info') {
            try {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white ${
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

        // Show error
        showError(message) {
            this.showNotification(message, 'error');
        },

        // Handle errors
        handleError(error, context = 'Unknown') {
            console.error(`❌ Chat Error in ${context}:`, error);
            // Could implement more sophisticated error handling here
        }
    }
};
// Global variables
let selectedFiles = new Set();
let currentView = 'grid';
let deleteFileId = null;
let currentFilters = {
    search: '',
    category: '',
    type: ''
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('File Manager JavaScript loaded');
    
    // Initialize view mode
    const savedView = localStorage.getItem('fileManagerView') || 'grid';
    setViewMode(savedView);
    
    // Set default active filter
    const allFilesBtn = document.querySelector('[data-category=""]');
    if (allFilesBtn) {
        allFilesBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }
    
    // Bind events
    bindEvents();
});

function bindEvents() {
    // Search input
    const searchInput = document.getElementById('file-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentFilters.search = this.value.toLowerCase();
            applyFilters();
        });
    }
    
    // Sort select
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortFiles();
        });
    }
    
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleSelectAll();
        });
    }
    
    // Modal click outside to close
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        previewModal.addEventListener('click', function(e) {
            if (e.target === previewModal) {
                closePreview();
            }
        });
    }
    
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePreview();
            closeDeleteModal();
        }
        
        if (e.ctrlKey && e.key === 'a' && !e.target.matches('input')) {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
                toggleSelectAll();
            }
        }
        
        if (e.key === 'Delete' && selectedFiles.size > 0) {
            deleteSelected();
        }
    });
}

// View Mode Functions
function setViewMode(mode) {
    console.log('Setting view mode to:', mode);
    currentView = mode;
    
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');

    if (!gridView || !listView || !gridBtn || !listBtn) {
        console.error('View elements not found');
        return;
    }

    if (mode === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        gridBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
        listBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        listBtn.classList.add('text-gray-500', 'dark:text-gray-400');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        listBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
        gridBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        gridBtn.classList.add('text-gray-500', 'dark:text-gray-400');
    }

    localStorage.setItem('fileManagerView', mode);
    console.log('View mode set successfully');
}

// Filter Functions
function applyFilters() {
    const items = document.querySelectorAll('.file-item, .file-item-list');
    let visibleCount = 0;

    items.forEach(item => {
        const fileName = (item.dataset.fileName || '').toLowerCase();
        const fileCategory = item.dataset.fileCategory || '';
        const fileType = item.dataset.fileType || '';
        
        const matchesSearch = !currentFilters.search || fileName.includes(currentFilters.search);
        const matchesCategory = !currentFilters.category || fileCategory === currentFilters.category;
        const matchesType = !currentFilters.type || fileType === currentFilters.type;
        
        const isVisible = matchesSearch && matchesCategory && matchesType;
        
        if (isVisible) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    updateEmptyState(visibleCount);
    updateFileCount(visibleCount);
}

function filterByCategory(category) {
    console.log('Filtering by category:', category);
    
    currentFilters.category = category;
    
    // Update active filter button
    document.querySelectorAll('.filter-category-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    const activeBtn = document.querySelector(`[data-category="${category}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }

    // Clear type filter when changing category
    currentFilters.type = '';
    document.querySelectorAll('.filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });

    applyFilters();
}

function filterByType(type) {
    console.log('Filtering by type:', type);
    
    currentFilters.type = type;
    
    // Update active filter button
    document.querySelectorAll('.filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    const activeBtn = document.querySelector(`[data-type="${type}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }

    // Clear category filter when changing type
    currentFilters.category = '';
    document.querySelectorAll('.filter-category-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });

    applyFilters();
}

function clearFilters() {
    console.log('Clearing all filters');
    
    // Reset filters
    currentFilters = { search: '', category: '', type: '' };
    
    // Clear search
    const searchInput = document.getElementById('file-search');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Clear active filters
    document.querySelectorAll('.filter-category-btn, .filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    // Set "All Files" as active
    const allFilesBtn = document.querySelector('[data-category=""]');
    if (allFilesBtn) {
        allFilesBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }
    
    applyFilters();
}

function updateEmptyState(visibleCount) {
    const emptyState = document.getElementById('empty-state');
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');

    if (!emptyState) return;

    if (visibleCount === 0) {
        emptyState.classList.remove('hidden');
        if (gridView) gridView.classList.add('hidden');
        if (listView) listView.classList.add('hidden');
    } else {
        emptyState.classList.add('hidden');
        if (currentView === 'grid' && gridView) {
            gridView.classList.remove('hidden');
        } else if (currentView === 'list' && listView) {
            listView.classList.remove('hidden');
        }
    }
}

function updateFileCount(visibleCount) {
    const fileCountElement = document.getElementById('file-count');
    if (fileCountElement) {
        fileCountElement.textContent = `${visibleCount} files`;
    }
}

// Sort Functions
function sortFiles() {
    const sortSelect = document.getElementById('sort-select');
    if (!sortSelect) return;

    const sortBy = sortSelect.value;
    const [field, direction] = sortBy.split('-');
    
    const gridContainer = document.getElementById('grid-view');
    const listContainer = document.querySelector('#list-view tbody');
    
    if (gridContainer) {
        const gridItems = Array.from(gridContainer.children);
        sortItems(gridItems, gridContainer, field, direction);
    }
    
    if (listContainer) {
        const listItems = Array.from(listContainer.children);
        sortItems(listItems, listContainer, field, direction);
    }
}

function sortItems(items, container, field, direction) {
    items.sort((a, b) => {
        const aVal = getSortValue(a, field);
        const bVal = getSortValue(b, field);
        
        let comparison = 0;
        if (typeof aVal === 'string') {
            comparison = aVal.localeCompare(bVal);
        } else {
            comparison = aVal - bVal;
        }
        
        return direction === 'desc' ? -comparison : comparison;
    });

    items.forEach(item => container.appendChild(item));
}

function getSortValue(item, field) {
    switch (field) {
        case 'name':
            return item.dataset.fileName || '';
        case 'size':
            return parseInt(item.dataset.fileSize) || 0;
        case 'date':
            return parseInt(item.dataset.fileDate) || 0;
        case 'type':
            return item.dataset.fileType || '';
        default:
            return '';
    }
}

// Selection Functions
function selectFile(fileId, event) {
    const isMulti = event.ctrlKey || event.metaKey || event.shiftKey;

    if (!isMulti) {
        // Clear all previous selections
        selectedFiles.clear();
        document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = false);
    }

    toggleSelection(fileId);
}

function toggleSelection(fileId) {
    const item = document.querySelector(`[data-file-id="${fileId}"]`);
    const checkbox = item?.querySelector('.file-checkbox');

    if (!item || !checkbox) return;

    if (selectedFiles.has(fileId)) {
        selectedFiles.delete(fileId);
        checkbox.checked = false;
        item.classList.remove('ring-2', 'ring-blue-500');
    } else {
        selectedFiles.add(fileId);
        checkbox.checked = true;
        item.classList.add('ring-2', 'ring-blue-500');
    }

    updateBulkActions();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (!selectAllCheckbox) return;
    
    const isChecked = selectAllCheckbox.checked;
    
    document.querySelectorAll('.file-checkbox').forEach(checkbox => {
        const fileItem = checkbox.closest('[data-file-id]');
        if (fileItem && fileItem.style.display !== 'none') { // Only select visible items
            checkbox.checked = isChecked;
            const fileId = parseInt(fileItem.dataset.fileId);
            
            if (isChecked) {
                selectedFiles.add(fileId);
            } else {
                selectedFiles.delete(fileId);
            }
        }
    });

    updateBulkActions();
}

function updateBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (selectedFiles.size > 0) {
        if (bulkActions) bulkActions.style.display = 'flex';
        if (selectedCount) selectedCount.textContent = `${selectedFiles.size} selected`;
    } else {
        if (bulkActions) bulkActions.style.display = 'none';
    }
}

// File Actions
function downloadFile(fileId) {
    const projectId = document.querySelector('meta[name="project-slug"]')?.content;
    if (!projectId) {
        console.error('Project ID not found');
        return;
    }
    
    window.location.href = `/admin/projects/${projectId}/files/${fileId}/download`;
}

function downloadSelected() {
    if (selectedFiles.size === 0) {
        showNotification('Please select files to download.', 'warning');
        return;
    }

    const projectId = document.querySelector('meta[name="project-slug"]')?.content;
    if (!projectId) {
        console.error('Project ID not found');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/projects/${projectId}/files/bulk-download`;
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
    }

    selectedFiles.forEach(fileId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'file_ids[]';
        input.value = fileId;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function deleteFile(fileId, fileName) {
    deleteFileId = fileId;
    const deleteFileNameElement = document.getElementById('delete-file-name');
    if (deleteFileNameElement) {
        deleteFileNameElement.textContent = fileName;
    }
    
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) {
        deleteModal.classList.remove('hidden');
    }
}

function deleteSelected() {
    if (selectedFiles.size === 0) {
        showNotification('Please select files to delete.', 'warning');
        return;
    }

    if (confirm(`Are you sure you want to delete ${selectedFiles.size} selected file(s)? This action cannot be undone.`)) {
        const projectId = document.querySelector('meta[name="project-slug"]')?.content;
        if (!projectId) {
            console.error('Project ID not found');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/projects/${projectId}/files/bulk-delete`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        selectedFiles.forEach(fileId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'file_ids[]';
            input.value = fileId;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDelete() {
    if (deleteFileId) {
        const projectId = document.querySelector('meta[name="project-slug"]')?.content;
        if (!projectId) {
            console.error('Project ID not found');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/projects/${projectId}/files/${deleteFileId}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
    closeDeleteModal();
}

function closeDeleteModal() {
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) {
        deleteModal.classList.add('hidden');
    }
    deleteFileId = null;
}

function previewFile(fileId) {
    const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
    const fileName = fileElement?.dataset.fileName || 'File';
    
    const previewTitle = document.getElementById('preview-title');
    if (previewTitle) {
        previewTitle.textContent = fileName;
    }
    
    const projectId = document.querySelector('meta[name="project-slug"]')?.content;
    if (!projectId) {
        console.error('Project ID not found');
        return;
    }
    
    const previewUrl = `/admin/projects/${projectId}/files/${fileId}/preview`;
    
    // Show loading state
    const previewContent = document.getElementById('preview-content');
    if (previewContent) {
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Loading preview...</span>
            </div>
        `;
    }
    
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        previewModal.classList.remove('hidden');
    }
    
    fetch(previewUrl)
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Preview not available');
        })
        .then(html => {
            if (previewContent) {
                previewContent.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Preview error:', error);
            if (previewContent) {
                previewContent.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Preview not available for this file type.</p>
                    </div>
                `;
            }
        });
}

function closePreview() {
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        previewModal.classList.add('hidden');
    }
}

// Utility Functions
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    const icons = {
        success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        error: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
        warning: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        info: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg border p-4 ${colors[type]} transform transition-all duration-300 ease-in-out`;
    notification.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    ${icons[type]}
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

window.selectFile = selectFile;
window.toggleSelection = toggleSelection;
window.deleteFile = deleteFile;
window.previewFile = previewFile;
window.confirmDelete = confirmDelete;
window.closePreview = closePreview;
window.closeDeleteModal = closeDeleteModal;
window.downloadFile = downloadFile;
window.downloadSelected = downloadSelected;
window.deleteSelected = deleteSelected;
window.filterByCategory = filterByCategory;
window.filterByType = filterByType;
window.clearFilters = clearFilters;
window.setViewMode = setViewMode;