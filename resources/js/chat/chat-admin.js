// resources/js/admin-chat.js
function adminChatDashboard() {
    return {
        // State
        sessions: [],
        selectedSession: null,
        selectedSessionMessages: [],
        currentMessage: '',
        filter: 'all',
        operatorStatus: 'offline',
        stats: {
            waiting_sessions: 0,
            active_sessions: 0,
            operators_online: 0,
            avg_wait_time: 0
        },
        isSending: false,
        
        // Timers
        refreshTimer: null,
        typingTimer: null,

        async init() {
            await this.loadOperatorStatus();
            await this.refreshDashboard();
            this.startAutoRefresh();
        },

        async refreshDashboard() {
            try {
                // Load sessions
                const sessionsResponse = await fetch('/admin/chat/api/sessions');
                const sessionsData = await sessionsResponse.json();
                
                if (sessionsData.success) {
                    this.sessions = sessionsData.sessions;
                }

                // Load stats
                const statsResponse = await fetch('/admin/chat/api/statistics');
                const statsData = await statsResponse.json();
                
                if (statsData.success) {
                    this.stats = statsData.stats;
                }

                // Refresh selected session messages
                if (this.selectedSession) {
                    await this.loadSessionMessages(this.selectedSession.id);
                }

            } catch (error) {
                console.error('Failed to refresh dashboard:', error);
            }
        },

        async selectSession(session) {
            this.selectedSession = session;
            await this.loadSessionMessages(session.id);
            await this.markMessagesAsRead(session.id);
        },

        async loadSessionMessages(sessionId) {
            try {
                const response = await fetch(`/admin/chat/api/${sessionId}/messages`);
                const data = await response.json();
                
                if (data.success) {
                    this.selectedSessionMessages = data.messages;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                }
            } catch (error) {
                console.error('Failed to load messages:', error);
            }
        },

        async assignToMe() {
            if (!this.selectedSession) return;

            try {
                const response = await fetch(`/admin/chat/api/${this.selectedSession.id}/assign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.selectedSession.status = 'active';
                    this.selectedSession.assigned_operator = data.operator;
                    await this.refreshDashboard();
                } else {
                    alert('Failed to assign session: ' + data.message);
                }
            } catch (error) {
                console.error('Failed to assign session:', error);
                alert('Failed to assign session. Please try again.');
            }
        },

        async sendMessage() {
            if (!this.currentMessage.trim() || this.isSending || !this.selectedSession) return;

            const messageText = this.currentMessage;
            this.currentMessage = '';
            this.isSending = true;

            try {
                const response = await fetch(`/admin/chat/api/${this.selectedSession.id}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: messageText })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.selectedSessionMessages.push(data.message);
                    this.scrollToBottom();
                } else {
                    this.currentMessage = messageText; // Restore message on failure
                    alert('Failed to send message: ' + data.message);
                }
            } catch (error) {
                this.currentMessage = messageText; // Restore message on failure
                console.error('Failed to send message:', error);
            } finally {
                this.isSending = false;
            }
        },

        async toggleOperatorStatus() {
            const newStatus = this.operatorStatus === 'online' ? 'offline' : 'online';
            
            try {
                const response = await fetch('/admin/chat/api/operator/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.operatorStatus = newStatus;
                    await this.refreshDashboard();
                }
            } catch (error) {
                console.error('Failed to update operator status:', error);
            }
        },

        // Utility methods
        get filteredSessions() {
            if (this.filter === 'all') return this.sessions;
            return this.sessions.filter(session => session.status === this.filter);
        },

        setFilter(filter) {
            this.filter = filter;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString();
        },

        startAutoRefresh() {
            this.refreshTimer = setInterval(() => {
                this.refreshDashboard();
            }, 5000); // Refresh every 5 seconds
        }
    }
}