{{-- 
    Chat Session List Component
    Reusable component untuk menampilkan list chat sessions
    Usage: <x-chat.session-list :sessions="$sessions" :current-filter="'all'" />
--}}

@props([
    'sessions' => [],
    'currentFilter' => 'all',
    'showFilters' => true,
    'selectable' => true,
    'selectedSessionId' => null,
    'theme' => 'default'
])

<div x-data="chatSessionList()" 
     x-init="init()"
     class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
    
    <!-- Filter Tabs -->
    @if($showFilters)
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button @click="currentFilter = 'all'" 
                        :class="currentFilter === 'all' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-colors">
                    All (<span x-text="allSessions.length"></span>)
                </button>
                <button @click="currentFilter = 'waiting'" 
                        :class="currentFilter === 'waiting' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-colors">
                    Waiting (<span x-text="waitingSessions.length"></span>)
                </button>
                <button @click="currentFilter = 'active'" 
                        :class="currentFilter === 'active' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-colors">
                    Active (<span x-text="activeSessions.length"></span>)
                </button>
                <button @click="currentFilter = 'closed'" 
                        :class="currentFilter === 'closed' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                        class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-colors">
                    Closed (<span x-text="closedSessions.length"></span>)
                </button>
            </div>
        </div>
    @endif

    <!-- Search Box -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <div class="relative">
            <input x-model="searchQuery"
                   @input="filterSessions()"
                   type="text" 
                   placeholder="Search by visitor name, email, or message..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <!-- Clear Search -->
            <button x-show="searchQuery.length > 0" 
                    @click="searchQuery = ''; filterSessions()"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Sessions List -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
        <!-- Loading State -->
        <div x-show="isLoading" class="p-4 text-center">
            <div class="flex items-center justify-center space-x-2 text-gray-500">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                <span class="text-sm">Loading sessions...</span>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="!isLoading && filteredSessions.length === 0" class="p-8 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No chat sessions found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="getEmptyStateMessage()"></p>
        </div>

        <!-- Session Items -->
        <template x-for="session in filteredSessions" :key="session.session_id">
            <div @click="selectSession(session)" 
                 :class="selectedSessionId === session.session_id ? 'bg-blue-50 dark:bg-blue-900/20 border-r-2 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                 class="p-4 cursor-pointer transition-colors relative">
                
                <!-- Priority/Status Indicators -->
                <div class="absolute top-3 right-3 flex items-center space-x-1">
                    <!-- High Priority -->
                    <div x-show="session.priority === 'high'" 
                         class="w-2 h-2 bg-red-500 rounded-full"
                         title="High Priority"></div>
                    
                    <!-- Waiting Animation -->
                    <div x-show="session.status === 'waiting'" 
                         class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"
                         title="Waiting for Operator"></div>
                    
                    <!-- Active Indicator -->
                    <div x-show="session.status === 'active'" 
                         class="w-2 h-2 bg-green-500 rounded-full"
                         title="Active Session"></div>
                </div>
                
                <!-- Session Content -->
                <div class="flex items-start space-x-3 pr-8">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm" x-text="getInitials(session.visitor_name)"></span>
                        </div>
                    </div>
                    
                    <!-- Session Info -->
                    <div class="flex-1 min-w-0">
                        <!-- Name and Status -->
                        <div class="flex items-center space-x-2 mb-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate" 
                                x-text="session.visitor_name || 'Anonymous User'"></h4>
                            
                            <!-- Status Badge -->
                            <span :class="getStatusBadgeClass(session.status)" 
                                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                  x-text="session.status"></span>
                        </div>
                        
                        <!-- Email -->
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 truncate" 
                           x-text="session.visitor_email || 'No email provided'"></p>
                        
                        <!-- Last Message Preview -->
                        <div class="mb-2">
                            <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2" 
                               x-text="session.last_message || 'No messages yet'"></p>
                        </div>
                        
                        <!-- Session Metadata -->
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <!-- Start Time -->
                            <span x-text="formatRelativeTime(session.started_at)"></span>
                            
                            <!-- Message Count -->
                            <span x-show="session.messages_count" 
                                  x-text="session.messages_count + ' messages'"></span>
                        </div>
                        
                        <!-- Operator Info (if assigned) -->
                        <div x-show="session.operator" class="mt-2 flex items-center space-x-1">
                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-xs text-blue-600 dark:text-blue-400" 
                                  x-text="'Handled by ' + (session.operator?.name || 'Unknown')"></span>
                        </div>
                    </div>
                    
                    <!-- Unread Count -->
                    <div x-show="session.unread_count > 0" class="flex-shrink-0">
                        <span x-text="session.unread_count > 99 ? '99+' : session.unread_count"
                              class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full"></span>
                    </div>
                </div>
                
                <!-- Session Tags (if any) -->
                <div x-show="session.tags && session.tags.length > 0" class="mt-2 flex flex-wrap gap-1">
                    <template x-for="tag in session.tags" :key="tag">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
                              x-text="tag"></span>
                    </template>
                </div>
                
                <!-- Quick Actions (visible on hover) -->
                <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="flex items-center space-x-1">
                        <!-- Take Session (for waiting sessions) -->
                        <button x-show="session.status === 'waiting'" 
                                @click.stop="takeSession(session.session_id)"
                                class="p-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                                title="Take Session">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        
                        <!-- Session Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click.stop="open = !open" 
                                    class="p-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors"
                                    title="Session Options">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 bottom-full mb-1 w-32 bg-white dark:bg-gray-700 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <button @click.stop="transferSession(session.session_id); open = false" 
                                            class="block w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Transfer
                                    </button>
                                    <button @click.stop="flagSession(session.session_id); open = false" 
                                            class="block w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Flag
                                    </button>
                                    <button x-show="session.status !== 'closed'" 
                                            @click.stop="closeSession(session.session_id); open = false" 
                                            class="block w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Load More Button -->
    <div x-show="hasMore && !isLoading" class="p-4 border-t border-gray-200 dark:border-gray-700">
        <button @click="loadMore()" 
                class="w-full px-4 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
            Load More Sessions
        </button>
    </div>
</div>

<script>
function chatSessionList() {
    return {
        // State
        allSessions: @json($sessions),
        filteredSessions: [],
        currentFilter: '{{ $currentFilter }}',
        selectedSessionId: '{{ $selectedSessionId }}',
        searchQuery: '',
        isLoading: false,
        hasMore: false,
        page: 1,

        init() {
            this.filterSessions();
            
            // Listen for session updates
            this.$watch('allSessions', () => {
                this.filterSessions();
            });
            
            this.$watch('currentFilter', () => {
                this.filterSessions();
            });
        },

        // Computed properties
        get waitingSessions() {
            return this.allSessions.filter(s => s.status === 'waiting');
        },

        get activeSessions() {
            return this.allSessions.filter(s => s.status === 'active');
        },

        get closedSessions() {
            return this.allSessions.filter(s => s.status === 'closed');
        },

        // Methods
        filterSessions() {
            let sessions = this.allSessions;

            // Filter by status
            if (this.currentFilter !== 'all') {
                sessions = sessions.filter(s => s.status === this.currentFilter);
            }

            // Filter by search query
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                sessions = sessions.filter(s => 
                    (s.visitor_name && s.visitor_name.toLowerCase().includes(query)) ||
                    (s.visitor_email && s.visitor_email.toLowerCase().includes(query)) ||
                    (s.last_message && s.last_message.toLowerCase().includes(query))
                );
            }

            // Sort by activity (waiting first, then by last activity)
            sessions.sort((a, b) => {
                if (a.status === 'waiting' && b.status !== 'waiting') return -1;
                if (b.status === 'waiting' && a.status !== 'waiting') return 1;
                
                const aTime = new Date(a.updated_at || a.started_at);
                const bTime = new Date(b.updated_at || b.started_at);
                return bTime - aTime;
            });

            this.filteredSessions = sessions;
        },

        selectSession(session) {
            if (!{{ $selectable ? 'true' : 'false' }}) return;
            
            this.selectedSessionId = session.session_id;
            this.$dispatch('session:selected', { session });
        },

        async takeSession(sessionId) {
            try {
                this.$dispatch('session:take', { sessionId });
            } catch (error) {
                console.error('Failed to take session:', error);
            }
        },

        async transferSession(sessionId) {
            this.$dispatch('session:transfer', { sessionId });
        },

        async flagSession(sessionId) {
            this.$dispatch('session:flag', { sessionId });
        },

        async closeSession(sessionId) {
            if (!confirm('Are you sure you want to close this session?')) {
                return;
            }
            
            this.$dispatch('session:close', { sessionId });
        },

        async loadMore() {
            this.isLoading = true;
            this.page++;
            
            try {
                this.$dispatch('sessions:load-more', { page: this.page });
            } catch (error) {
                console.error('Failed to load more sessions:', error);
            } finally {
                this.isLoading = false;
            }
        },

        // Utility methods
        getInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        },

        getStatusBadgeClass(status) {
            const classes = {
                'waiting': 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                'active': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'closed': 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                'transferred': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
            };
            return classes[status] || classes['closed'];
        },

        formatRelativeTime(timestamp) {
            if (!timestamp) return '';
            
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            return date.toLocaleDateString();
        },

        getEmptyStateMessage() {
            if (this.searchQuery.trim()) {
                return 'No sessions match your search criteria';
            }
            
            switch (this.currentFilter) {
                case 'waiting':
                    return 'No sessions are waiting for operators';
                case 'active':
                    return 'No active chat sessions';
                case 'closed':
                    return 'No closed sessions found';
                default:
                    return 'No chat sessions found';
            }
        },

        // External methods for parent components
        addSession(session) {
            this.allSessions.unshift(session);
        },

        updateSession(updatedSession) {
            const index = this.allSessions.findIndex(s => s.session_id === updatedSession.session_id);
            if (index >= 0) {
                this.allSessions[index] = { ...this.allSessions[index], ...updatedSession };
            }
        },

        removeSession(sessionId) {
            const index = this.allSessions.findIndex(s => s.session_id === sessionId);
            if (index >= 0) {
                this.allSessions.splice(index, 1);
            }
        },

        refreshSessions(sessions) {
            this.allSessions = sessions;
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>