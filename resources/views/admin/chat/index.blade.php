{{-- resources/views/admin/chat/index.blade.php - FIXED VERSION --}}
<x-layouts.admin :title="'Live Chat Management'" :enableCharts="true">
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="chat-config" content="{{ json_encode([
            'auto_refresh' => true,
            'refresh_interval' => 5000,
            'sound_enabled' => true,
            'notifications_enabled' => true
        ]) }}">
    </x-slot>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Dashboard' => route('admin.dashboard'),
        'Live Chat' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Live Chat Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage live chat sessions and support requests
                <span id="last-update" class="text-xs text-gray-500 ml-2"></span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Connection Status -->
            <div id="connection-status" class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium">Connected</span>
            </div>

            <x-admin.button href="{{ route('admin.chat.settings') }}" color="primary" size="sm"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>'>
                Settings
            </x-admin.button>
        </div>
    </div>

    <!-- Operator Status Panel -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ auth()->user()->name }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Operator Status: 
                        <span id="operator-status-text" class="font-medium {{ $isOperatorOnline ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $isOperatorOnline ? 'Online' : 'Offline' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button id="operator-toggle" 
                        class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ $isOperatorOnline ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                        onclick="toggleOperatorStatus()">
                    <div class="w-2 h-2 rounded-full {{ $isOperatorOnline ? 'bg-red-500' : 'bg-green-500' }}"></div>
                    <span id="operator-toggle-text">{{ $isOperatorOnline ? 'Go Offline' : 'Go Online' }}</span>
                </button>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Active chats: <span id="operator-chat-count" class="font-medium">{{ $statistics['active_sessions'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card 
            title="Active Sessions" 
            :value="$statistics['active_sessions'] ?? 0" 
            id="active-sessions-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>'
            iconColor="text-green-600" 
            iconBg="bg-green-100 dark:bg-green-900/30"
            trend="+5%" 
            trendColor="text-green-600" />

        <x-admin.stat-card 
            title="Waiting Sessions" 
            :value="$statistics['waiting_sessions'] ?? 0" 
            id="waiting-sessions-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-yellow-600" 
            iconBg="bg-yellow-100 dark:bg-yellow-900/30"
            trend="-2%" 
            trendColor="text-red-600" />

        <x-admin.stat-card 
            title="Online Operators" 
            :value="$statistics['online_operators'] ?? 0" 
            id="online-operators-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'
            iconColor="text-blue-600" 
            iconBg="bg-blue-100 dark:bg-blue-900/30"
            trend="+1" 
            trendColor="text-green-600" />

        <x-admin.stat-card 
            title="Messages Today" 
            :value="$statistics['messages_today'] ?? 0" 
            id="messages-today-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>'
            iconColor="text-purple-600" 
            iconBg="bg-purple-100 dark:bg-purple-900/30"
            trend="+12%" 
            trendColor="text-green-600" />
    </div>

    <!-- Chat Sessions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Active Sessions -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        Active Sessions
                    </h3>
                    <span id="active-count" class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ $activeSessions->count() }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <div id="active-sessions-container" class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($activeSessions as $session)
                        <div class="chat-session-card p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                             data-session-id="{{ $session->session_id }}"
                             onclick="openChatSession('{{ $session->id }}')">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $session->getVisitorName() }}
                                        </h4>
                                        @if($session->priority === 'urgent')
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                                Urgent
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $session->getVisitorEmail() ?? 'No email provided' }}
                                    </p>
                                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $session->started_at->diffForHumans() }}</span>
                                        <span>{{ $session->messages->count() }} messages</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    @if($session->assigned_operator_id === auth()->id())
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                            Assigned to you
                                        </span>
                                    @endif
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span class="text-xs text-green-600 dark:text-green-400">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="no-active-sessions" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-sm">No active chat sessions</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Waiting Sessions -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        Waiting Sessions
                    </h3>
                    <span id="waiting-count" class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ $waitingSessions->count() }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <div id="waiting-sessions-container" class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($waitingSessions as $session)
                        <div class="chat-session-card p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors cursor-pointer"
                             data-session-id="{{ $session->session_id }}"
                             onclick="openChatSession('{{ $session->id }}')">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $session->getVisitorName() }}
                                        </h4>
                                        @if($session->priority === 'urgent')
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                                Urgent
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $session->getVisitorEmail() ?? 'No email provided' }}
                                    </p>
                                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>Waiting {{ $session->started_at->diffForHumans() }}</span>
                                        <span>{{ $session->messages->count() }} messages</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <button class="px-3 py-1 text-xs font-medium bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors"
                                            onclick="event.stopPropagation(); takeOverSession('{{ $session->id }}')">
                                        Take Over
                                    </button>
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400">Waiting</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="no-waiting-sessions" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm">No waiting chat sessions</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Closed Sessions -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                        Recent Closed
                    </h3>
                    <span id="recent-count" class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ $recentClosedSessions->count() }}
                    </span>
                </div>
            </div>
            <div class="p-4">
                <div id="recent-sessions-container" class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($recentClosedSessions as $session)
                        <div class="chat-session-card p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                             data-session-id="{{ $session->session_id }}"
                             onclick="openChatSession('{{ $session->id }}')">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $session->getVisitorName() }}
                                        </h4>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $session->getVisitorEmail() ?? 'No email provided' }}
                                    </p>
                                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>Closed {{ $session->ended_at->diffForHumans() }}</span>
                                        <span>{{ $session->getDuration() ?? 0 }} min</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">Closed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="no-recent-sessions" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm">No recent closed sessions</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let chatDashboard = {
            refreshInterval: null,
            soundEnabled: true,
            notificationsEnabled: true,
            isOperatorOnline: {{ $isOperatorOnline ? 'true' : 'false' }},
            lastRefresh: new Date(),
            echo: null
        };

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Chat Dashboard initialized');
            initializeChatDashboard();
            setupWebSocketListeners();
            startAutoRefresh();
            updateLastRefreshTime();
        });

        // Initialize chat dashboard
        function initializeChatDashboard() {
            const config = JSON.parse(document.querySelector('meta[name="chat-config"]').getAttribute('content'));
            chatDashboard = { ...chatDashboard, ...config };
            
            // Setup visibility change handler
            if (chatDashboard.auto_refresh) {
                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'visible') {
                        refreshChatDashboard();
                    }
                });
            }
        }

        // Setup WebSocket listeners
        function setupWebSocketListeners() {
            if (!window.Echo) {
                console.warn('Echo not available for chat dashboard');
                return;
            }

            try {
                // Listen for new chat sessions
                window.Echo.channel('admin-chat-notifications')
                    .listen('.session.started', (e) => {
                        console.log('New chat session started:', e);
                        handleNewChatSession(e);
                        playNotificationSound();
                        showNotification('New chat session started from ' + e.visitor_name, 'info');
                    })
                    .listen('.message.sent', (e) => {
                        console.log('New chat message received:', e);
                        handleNewMessage(e);
                        if (e.sender_type === 'visitor') {
                            playNotificationSound();
                            showNotification('New message from ' + e.visitor_name, 'info');
                        }
                    })
                    .listen('.session.closed', (e) => {
                        console.log('Chat session closed:', e);
                        handleSessionClosed(e);
                    })
                    .listen('.session.updated', (e) => {
                        console.log('Chat session updated:', e);
                        handleSessionUpdated(e);
                    });

                // Listen for operator status changes
                window.Echo.channel('public-chat-status')
                    .listen('.operator.status.changed', (e) => {
                        console.log('Operator status changed:', e);
                        updateOperatorStatus(e);
                    });

                updateConnectionStatus('connected');
            } catch (error) {
                console.error('WebSocket setup failed:', error);
                updateConnectionStatus('disconnected');
            }
        }

        // Auto refresh functionality
        function startAutoRefresh() {
            if (chatDashboard.refresh_interval && chatDashboard.refresh_interval > 0) {
                chatDashboard.refreshInterval = setInterval(function() {
                    refreshChatDashboard();
                }, chatDashboard.refresh_interval);
            }
        }

        function stopAutoRefresh() {
            if (chatDashboard.refreshInterval) {
                clearInterval(chatDashboard.refreshInterval);
                chatDashboard.refreshInterval = null;
            }
        }

        // Refresh chat dashboard
        async function refreshChatDashboard() {
            try {
                updateConnectionStatus('connecting');
                
                const response = await fetch('{{ route("admin.chat.api.dashboard-metrics") }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch dashboard data');

                const data = await response.json();
                
                // Update statistics
                updateStatistics(data.statistics);
                
                // Update session lists
                updateSessionLists(data.sessions);
                
                // Update connection status
                updateConnectionStatus('connected');
                updateLastRefreshTime();
                
                console.log('Dashboard refreshed successfully');
            } catch (error) {
                console.error('Dashboard refresh failed:', error);
                updateConnectionStatus('error');
                showNotification('Failed to refresh dashboard data', 'error');
            }
        }

        // Update statistics cards
        function updateStatistics(stats) {
            const elements = {
                'active-sessions-stat': stats.active_sessions || 0,
                'waiting-sessions-stat': stats.waiting_sessions || 0,
                'online-operators-stat': stats.online_operators || 0,
                'messages-today-stat': stats.messages_today || 0
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    const valueElement = element.querySelector('[data-stat-value]') || element.querySelector('.text-2xl');
                    if (valueElement) {
                        valueElement.textContent = value;
                    }
                }
            });

            // Update counts in session headers
            updateElement('active-count', stats.active_sessions || 0);
            updateElement('waiting-count', stats.waiting_sessions || 0);
            updateElement('recent-count', stats.recent_closed || 0);
            updateElement('operator-chat-count', stats.active_sessions || 0);
        }

        // Update session lists
        function updateSessionLists(sessions) {
            if (sessions.active) {
                updateSessionContainer('active-sessions-container', sessions.active, 'active');
            }
            if (sessions.waiting) {
                updateSessionContainer('waiting-sessions-container', sessions.waiting, 'waiting');
            }
            if (sessions.recent) {
                updateSessionContainer('recent-sessions-container', sessions.recent, 'recent');
            }
        }

        // Update specific session container
        function updateSessionContainer(containerId, sessions, type) {
            const container = document.getElementById(containerId);
            if (!container) return;

            if (sessions.length === 0) {
                container.innerHTML = getEmptyStateHTML(type);
                return;
            }

            const html = sessions.map(session => createSessionHTML(session, type)).join('');
            container.innerHTML = html;
        }

        // Create session HTML
        function createSessionHTML(session, type) {
            const statusColors = {
                'active': 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600',
                'waiting': 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800',
                'recent': 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600'
            };

            const statusIndicators = {
                'active': '<div class="w-2 h-2 bg-green-500 rounded-full"></div><span class="text-xs text-green-600 dark:text-green-400">Active</span>',
                'waiting': '<div class="w-2 h-2 bg-yellow-500 rounded-full"></div><span class="text-xs text-yellow-600 dark:text-yellow-400">Waiting</span>',
                'recent': '<div class="w-2 h-2 bg-gray-500 rounded-full"></div><span class="text-xs text-gray-600 dark:text-gray-400">Closed</span>'
            };

            const actionButton = type === 'waiting' ? 
                `<button class="px-3 py-1 text-xs font-medium bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors"
                        onclick="event.stopPropagation(); takeOverSession('${session.id}')">
                    Take Over
                </button>` : '';

            return `
                <div class="chat-session-card p-4 ${statusColors[type]} rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer"
                     data-session-id="${session.session_id}"
                     onclick="openChatSession('${session.id}')">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                    ${session.visitor_name}
                                </h4>
                                ${session.priority === 'urgent' ? '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">Urgent</span>' : ''}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                ${session.visitor_email || 'No email provided'}
                            </p>
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <span>${session.time_info}</span>
                                <span>${session.messages_count} messages</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            ${actionButton}
                            <div class="flex items-center gap-1">
                                ${statusIndicators[type]}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Get empty state HTML
        function getEmptyStateHTML(type) {
            const emptyStates = {
                'active': {
                    icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                    text: 'No active chat sessions'
                },
                'waiting': {
                    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    text: 'No waiting chat sessions'
                },
                'recent': {
                    icon: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    text: 'No recent closed sessions'
                }
            };

            const state = emptyStates[type];
            return `
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${state.icon}"/>
                    </svg>
                    <p class="text-sm">${state.text}</p>
                </div>
            `;
        }

        // Event handlers
        function handleNewChatSession(sessionData) {
            // Add to waiting sessions container
            const waitingContainer = document.getElementById('waiting-sessions-container');
            const noWaitingMessage = document.getElementById('no-waiting-sessions');

            if (noWaitingMessage) {
                noWaitingMessage.remove();
            }

            if (waitingContainer) {
                const sessionElement = createSessionHTML(sessionData, 'waiting');
                waitingContainer.insertAdjacentHTML('afterbegin', sessionElement);
            }

            // Update counts
            refreshChatDashboard();
        }

        function handleNewMessage(messageData) {
            // Update session's last message indicator
            const sessionElement = document.querySelector(`[data-session-id="${messageData.session_id}"]`);
            if (sessionElement) {
                const lastMessageElement = sessionElement.querySelector('.last-message');
                if (lastMessageElement) {
                    lastMessageElement.textContent = messageData.message.substring(0, 50) + '...';
                }

                const timeElement = sessionElement.querySelector('.last-activity-time');
                if (timeElement) {
                    timeElement.textContent = 'Just now';
                }

                // Add unread indicator if message is from visitor
                if (messageData.sender_type === 'visitor') {
                    const unreadIndicator = sessionElement.querySelector('.unread-indicator');
                    if (unreadIndicator) {
                        unreadIndicator.classList.remove('hidden');
                    }
                }
            }
        }

        function handleSessionClosed(sessionData) {
            // Remove from active or waiting containers
            const sessionElement = document.querySelector(`[data-session-id="${sessionData.session_id}"]`);
            if (sessionElement) {
                sessionElement.remove();
            }

            // Add to recent closed sessions
            const closedContainer = document.getElementById('recent-sessions-container');
            if (closedContainer) {
                const closedElement = createSessionHTML(sessionData, 'recent');
                closedContainer.insertAdjacentHTML('afterbegin', closedElement);

                // Keep only last 10 closed sessions
                const closedSessions = closedContainer.querySelectorAll('.chat-session-card');
                if (closedSessions.length > 10) {
                    closedSessions[closedSessions.length - 1].remove();
                }
            }

            // Update counts
            refreshChatDashboard();
        }

        function handleSessionUpdated(sessionData) {
            const sessionElement = document.querySelector(`[data-session-id="${sessionData.session_id}"]`);
            if (sessionElement) {
                // Update session status, priority, operator assignment etc.
                // You could update specific elements here
                refreshChatDashboard();
            }
        }

        // Operator status management
        async function toggleOperatorStatus() {
            const button = document.getElementById('operator-toggle');
            const statusText = document.getElementById('operator-status-text');
            const toggleText = document.getElementById('operator-toggle-text');
            
            if (!button || !statusText || !toggleText) return;

            const isGoingOnline = !chatDashboard.isOperatorOnline;
            const url = isGoingOnline ? '{{ route("admin.chat.operator.online") }}' : '{{ route("admin.chat.operator.offline") }}';

            try {
                button.disabled = true;
                button.innerHTML = '<div class="w-4 h-4 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>';

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) throw new Error('Failed to update operator status');

                const data = await response.json();
                
                if (data.success) {
                    chatDashboard.isOperatorOnline = isGoingOnline;
                    updateOperatorStatusUI(isGoingOnline);
                    showNotification(`You are now ${isGoingOnline ? 'online' : 'offline'}`, 'success');
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Toggle operator status failed:', error);
                showNotification('Failed to update operator status', 'error');
            } finally {
                button.disabled = false;
                updateOperatorStatusUI(chatDashboard.isOperatorOnline);
            }
        }

        function updateOperatorStatusUI(isOnline) {
            const button = document.getElementById('operator-toggle');
            const statusText = document.getElementById('operator-status-text');
            const toggleText = document.getElementById('operator-toggle-text');

            if (button) {
                button.className = `flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors duration-200 ${
                    isOnline ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'
                }`;
                
                button.innerHTML = `
                    <div class="w-2 h-2 rounded-full ${isOnline ? 'bg-red-500' : 'bg-green-500'}"></div>
                    <span>${isOnline ? 'Go Offline' : 'Go Online'}</span>
                `;
            }

            if (statusText) {
                statusText.textContent = isOnline ? 'Online' : 'Offline';
                statusText.className = `font-medium ${isOnline ? 'text-green-600' : 'text-gray-600'}`;
            }
        }

        // Session management
        function openChatSession(sessionId) {
            window.location.href = `{{ route('admin.chat.index') }}/${sessionId}`;
        }

        async function takeOverSession(sessionId) {
            try {
                const response = await fetch(`{{ route('admin.chat.index') }}/${sessionId}/take-over`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        note: 'Taken over from dashboard'
                    })
                });

                if (!response.ok) throw new Error('Failed to take over session');

                const data = await response.json();
                
                if (data.success) {
                    showNotification(`Session taken over successfully! Status: ${data.session.status}`, 'success');
                    
                    // Log the status change for debugging
                    console.log('Takeover successful:', {
                        session_id: data.session.session_id,
                        old_status: 'waiting',
                        new_status: data.session.status,
                        assigned_to: data.session.operator_name,
                        url: data.session.url
                    });
                    
                    // Update the dashboard to reflect changes
                    refreshChatDashboard();
                    
                    // Redirect to the chat session show page after a brief delay
                    setTimeout(() => {
                        window.location.href = data.session.url || `{{ route('admin.chat.index') }}/${sessionId}`;
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to take over session');
                }
            } catch (error) {
                console.error('Take over session failed:', error);
                showNotification('Failed to take over session: ' + error.message, 'error');
            }
        }

        // Utility functions
        function updateElement(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        }

        function updateConnectionStatus(status) {
            const connectionElement = document.getElementById('connection-status');
            if (!connectionElement) return;

            const statusConfig = {
                'connected': {
                    class: 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300',
                    dot: 'bg-green-500',
                    text: 'Connected'
                },
                'connecting': {
                    class: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300',
                    dot: 'bg-yellow-500 animate-pulse',
                    text: 'Connecting...'
                },
                'disconnected': {
                    class: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300',
                    dot: 'bg-red-500',
                    text: 'Disconnected'
                },
                'error': {
                    class: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300',
                    dot: 'bg-red-500',
                    text: 'Connection Error'
                }
            };

            const config = statusConfig[status] || statusConfig.disconnected;
            
            connectionElement.className = `flex items-center gap-2 px-3 py-2 rounded-lg ${config.class}`;
            connectionElement.innerHTML = `
                <div class="w-2 h-2 rounded-full ${config.dot}"></div>
                <span class="text-sm font-medium">${config.text}</span>
            `;
        }

        function updateLastRefreshTime() {
            const lastUpdateElement = document.getElementById('last-update');
            if (lastUpdateElement) {
                const now = new Date();
                lastUpdateElement.textContent = `Last updated: ${now.toLocaleTimeString()}`;
                chatDashboard.lastRefresh = now;
            }
        }

        // Quick actions
        async function exportChatData() {
            try {
                showNotification('Preparing export...', 'info');
                
                const response = await fetch('{{ route("admin.chat.reports") }}?export=true', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (!response.ok) throw new Error('Export failed');

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `chat_data_${new Date().toISOString().split('T')[0]}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                showNotification('Export completed successfully', 'success');
            } catch (error) {
                console.error('Export failed:', error);
                showNotification('Export failed', 'error');
            }
        }

        async function clearOldSessions() {
            if (!confirm('Are you sure you want to clear old sessions? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('{{ route("admin.chat.archive-old") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) throw new Error('Failed to clear old sessions');

                const data = await response.json();
                
                if (data.success) {
                    showNotification(`${data.cleared_count} old sessions cleared`, 'success');
                    refreshChatDashboard();
                } else {
                    throw new Error(data.message || 'Failed to clear sessions');
                }
            } catch (error) {
                console.error('Clear old sessions failed:', error);
                showNotification('Failed to clear old sessions', 'error');
            }
        }

        function toggleSoundNotifications() {
            chatDashboard.soundEnabled = !chatDashboard.soundEnabled;
            const toggleText = document.getElementById('sound-toggle-text');
            if (toggleText) {
                toggleText.textContent = `Sound: ${chatDashboard.soundEnabled ? 'On' : 'Off'}`;
            }
            
            // Save preference to localStorage
            localStorage.setItem('chat_sound_enabled', chatDashboard.soundEnabled);
            
            showNotification(`Sound notifications ${chatDashboard.soundEnabled ? 'enabled' : 'disabled'}`, 'info');
        }

        // Sound notification
        function playNotificationSound() {
            if (!chatDashboard.soundEnabled) return;

            try {
                const audio = new Audio('/sounds/notification.mp3');
                audio.volume = 0.3;
                audio.play().catch(e => console.log('Sound play failed:', e));
            } catch (error) {
                console.log('Sound notification failed:', error);
            }
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
            
            const typeColors = {
                'success': 'bg-green-100 text-green-800 border border-green-200',
                'error': 'bg-red-100 text-red-800 border border-red-200',
                'warning': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                'info': 'bg-blue-100 text-blue-800 border border-blue-200'
            };

            notification.className += ` ${typeColors[type] || typeColors.info}`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-1">${message}</div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-current hover:opacity-70">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            stopAutoRefresh();
            if (chatDashboard.echo) {
                chatDashboard.echo.disconnect();
            }
        });

        // Load sound preference
        document.addEventListener('DOMContentLoaded', function() {
            const soundEnabled = localStorage.getItem('chat_sound_enabled');
            if (soundEnabled !== null) {
                chatDashboard.soundEnabled = soundEnabled === 'true';
                const toggleText = document.getElementById('sound-toggle-text');
                if (toggleText) {
                    toggleText.textContent = `Sound: ${chatDashboard.soundEnabled ? 'On' : 'Off'}`;
                }
            }
        });
    </script>
</x-layouts.admin>