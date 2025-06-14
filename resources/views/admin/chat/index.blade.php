{{-- resources/views/admin/chat/index.blade.php --}}
<x-layouts.admin :title="'Chat Management'" :enableCharts="true">

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Chat Management' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Management</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Manage live chat sessions and support requests</p>
        </div>

        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('admin.chat.settings') }}" color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>'>
                Settings
            </x-admin.button>

            <x-admin.button href="{{ route('admin.chat.reports') }}" color="info"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'>
                Reports
            </x-admin.button>
        </div>
    </div>
   


    <!-- Chat Statistics Cards -->
    {{-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card title="Active Sessions" :value="$statistics['active_sessions'] ?? 0" id="active-sessions-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>'
            iconColor="text-green-500" iconBg="bg-green-100 dark:bg-green-800/30" />

        <x-admin.stat-card title="Waiting Sessions" :value="$statistics['waiting_sessions'] ?? 0" id="waiting-sessions-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-yellow-500" iconBg="bg-yellow-100 dark:bg-yellow-800/30" />

        <x-admin.stat-card title="Online Operators" :value="$statistics['online_operators'] ?? 0" id="online-operators-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'
            iconColor="text-blue-500" iconBg="bg-blue-100 dark:bg-blue-800/30" />

        <x-admin.stat-card title="Messages Today" :value="$statistics['messages_today'] ?? 0" id="messages-today-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'
            iconColor="text-purple-500" iconBg="bg-purple-100 dark:bg-purple-800/30" />
    </div> --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Sessions -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Chats</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="active-count">
                                {{ $stats['active'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Waiting Queue</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="waiting-count">
                                {{ $stats['waiting'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Sessions -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Today's Total</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="today-count">
                                {{ $stats['today'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Response Time -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Avg Response</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="avg-response">
                                {{ $stats['avgResponse'] ?? '-' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Grid -->
    <x-admin-chat-dashboard />

    <!-- Quick Actions Panel -->
    <div class="mt-8 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <button onclick="refreshChatDashboard()"
                class="flex items-center justify-center px-4 py-3 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Data
            </button>

            <a href="{{ route('admin.chat.templates.index') }}"
                class="flex items-center justify-center px-4 py-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                Manage Templates
            </a>

            <button onclick="exportChatData()"
                class="flex items-center justify-center px-4 py-3 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Data
            </button>

            <button onclick="showBulkActions()"
                class="flex items-center justify-center px-4 py-3 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Bulk Actions
            </button>
        </div>
    </div>

    @push('scripts')
        <script>
            // Chat Dashboard State Management
            let operatorOnline = false;
            let operatorAvailable = false;
            let chatRefreshInterval;
            let notificationSound;

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                initializeChatDashboard();
                setupWebSocketListeners();
                loadOperatorStatus();
                startAutoRefresh();
                setupNotificationSound();
            });

            // Initialize chat dashboard
            function initializeChatDashboard() {
                console.log('Initializing chat dashboard...');

                // Set up periodic updates every 30 seconds
                chatRefreshInterval = setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        refreshChatStatistics();
                    }
                }, 30000);

                // Handle page visibility change
                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'visible') {
                        refreshChatDashboard();
                    }
                });
            }

            // Setup WebSocket listeners
            function setupWebSocketListeners() {
                if (!window.Echo) {
                    console.warn('Echo not available for chat dashboard');
                    return;
                }

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
                        updateOperatorCounts(e);
                    });

                console.log('WebSocket listeners set up for chat dashboard');
            }

            // Load current operator status
            async function loadOperatorStatus() {
                try {
                    const response = await fetch('/admin/chat/operator/status');
                    const data = await response.json();

                    if (data.success) {
                        operatorOnline = data.is_online;
                        operatorAvailable = data.is_available;
                        updateOperatorStatusUI();
                    }
                } catch (error) {
                    console.error('Failed to load operator status:', error);
                }
            }

            // Toggle operator status
            async function toggleOperatorStatus() {
                const button = document.getElementById('toggle-operator-status');
                button.disabled = true;
                button.textContent = 'Updating...';

                try {
                    const newStatus = !operatorOnline;
                    const response = await fetch('/admin/chat/operator/status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            is_online: newStatus,
                            is_available: newStatus
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        operatorOnline = data.is_online;
                        operatorAvailable = data.is_available;
                        updateOperatorStatusUI();
                        showNotification(
                            operatorOnline ? 'You are now online and available for chat' : 'You are now offline',
                            'success'
                        );
                    } else {
                        throw new Error(data.message || 'Failed to update status');
                    }
                } catch (error) {
                    console.error('Failed to toggle operator status:', error);
                    showNotification('Failed to update operator status', 'error');
                } finally {
                    button.disabled = false;
                }
            }

            // Update operator status UI
            function updateOperatorStatusUI() {
                const indicator = document.getElementById('operator-status-indicator');
                const text = document.getElementById('operator-status-text');
                const button = document.getElementById('toggle-operator-status');

                if (operatorOnline && operatorAvailable) {
                    indicator.className = 'w-3 h-3 rounded-full bg-green-500 animate-pulse';
                    text.textContent = 'Online & Available';
                    button.textContent = 'Go Offline';
                    button.className =
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-red-600 text-white hover:bg-red-700';
                } else if (operatorOnline && !operatorAvailable) {
                    indicator.className = 'w-3 h-3 rounded-full bg-yellow-500';
                    text.textContent = 'Online but Unavailable';
                    button.textContent = 'Go Available';
                    button.className =
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-green-600 text-white hover:bg-green-700';
                } else {
                    indicator.className = 'w-3 h-3 rounded-full bg-gray-400';
                    text.textContent = 'Offline';
                    button.textContent = 'Go Online';
                    button.className =
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-green-600 text-white hover:bg-green-700';
                }
            }

            // Refresh chat dashboard data
            async function refreshChatDashboard() {
                try {
                    await Promise.all([
                        refreshChatStatistics(),
                        refreshChatSessions(),
                        loadOperatorStatus()
                    ]);
                    console.log('Chat dashboard refreshed successfully');
                } catch (error) {
                    console.error('Failed to refresh chat dashboard:', error);
                }
            }

            // Refresh chat statistics
            async function refreshChatStatistics() {
                try {
                    const response = await fetch('/admin/chat/api/statistics');
                    const data = await response.json();

                    if (data.success) {
                        updateStatisticElements(data.data);
                    }
                } catch (error) {
                    console.error('Failed to refresh chat statistics:', error);
                }
            }

            // Refresh chat sessions
            async function refreshChatSessions() {
                try {
                    const response = await fetch('/api/admin/chat/sessions');
                    const data = await response.json();

                    if (data.success) {
                        updateSessionContainers(data.data);
                    }
                } catch (error) {
                    console.error('Failed to refresh chat sessions:', error);
                }
            }

            // Update statistic elements
            function updateStatisticElements(stats) {
                const elements = {
                    'current-chats-count': stats.active_sessions || 0,
                    'todays-chats-count': stats.sessions_today || 0,
                    'avg-response-time': (stats.avg_response_time || 0).toFixed(1) + 'm',
                    'satisfaction-rate': (stats.satisfaction_rate || 0).toFixed(1) + '%',
                    'active-sessions-stat': stats.active_sessions || 0,
                    'waiting-sessions-stat': stats.waiting_sessions || 0,
                    'online-operators-stat': stats.online_operators || 0,
                    'messages-today-stat': stats.messages_today || 0
                };

                Object.entries(elements).forEach(([id, value]) => {
                    const element = document.getElementById(id);
                    if (element) {
                        if (element.querySelector('.stat-value')) {
                            element.querySelector('.stat-value').textContent = value;
                        } else {
                            element.textContent = value;
                        }
                    }
                });

                // Update badges
                updateBadgeElement('waiting-count-badge', stats.waiting_sessions || 0);
                updateBadgeElement('active-count-badge', stats.active_sessions || 0);
            }

            // Update badge element
            function updateBadgeElement(id, value) {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                }
            }

            // Handle new chat session
            function handleNewChatSession(sessionData) {
                // Add to waiting sessions container
                const waitingContainer = document.getElementById('waiting-sessions-container');
                const noWaitingMessage = document.getElementById('no-waiting-sessions');

                if (noWaitingMessage) {
                    noWaitingMessage.remove();
                }

                // Create session element (you would implement this based on your session partial template)
                const sessionElement = createSessionElement(sessionData, 'waiting');
                waitingContainer.insertBefore(sessionElement, waitingContainer.firstChild);

                // Update counts
                refreshChatStatistics();
            }

            // Handle new message
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

            // Handle session closed
            function handleSessionClosed(sessionData) {
                // Remove from active or waiting containers
                const sessionElement = document.querySelector(`[data-session-id="${sessionData.session_id}"]`);
                if (sessionElement) {
                    sessionElement.remove();
                }

                // Add to recent closed sessions if not already there
                const closedContainer = document.querySelector('.space-y-4:last-child');
                if (closedContainer) {
                    const closedElement = createSessionElement(sessionData, 'closed');
                    closedContainer.insertBefore(closedElement, closedContainer.firstChild);

                    // Keep only last 10 closed sessions
                    const closedSessions = closedContainer.querySelectorAll('[data-session-id]');
                    if (closedSessions.length > 10) {
                        closedSessions[closedSessions.length - 1].remove();
                    }
                }

                // Update counts
                refreshChatStatistics();
            }

            // Handle session updated
            function handleSessionUpdated(sessionData) {
                const sessionElement = document.querySelector(`[data-session-id="${sessionData.session_id}"]`);
                if (sessionElement) {
                    // Update session status, priority, operator assignment etc.
                    const statusElement = sessionElement.querySelector('.session-status');
                    if (statusElement) {
                        statusElement.textContent = sessionData.status;
                        statusElement.className = `session-status badge badge-${getStatusColor(sessionData.status)}`;
                    }

                    const operatorElement = sessionElement.querySelector('.session-operator');
                    if (operatorElement) {
                        operatorElement.textContent = sessionData.operator_name || 'Unassigned';
                    }

                    // If status changed from waiting to active, move to active container
                    if (sessionData.status === 'active') {
                        const activeContainer = document.getElementById('active-sessions-container');
                        const noActiveMessage = document.getElementById('no-active-sessions');

                        if (noActiveMessage) {
                            noActiveMessage.remove();
                        }

                        sessionElement.remove();
                        activeContainer.appendChild(sessionElement);
                    }
                }
            }

            // Update operator counts
            function updateOperatorCounts(data) {
                const onlineOperatorsElement = document.getElementById('online-operators-stat');
                if (onlineOperatorsElement) {
                    const valueElement = onlineOperatorsElement.querySelector('.stat-value') || onlineOperatorsElement;
                    valueElement.textContent = data.total_online_operators || 0;
                }
            }

            // Update session containers
            function updateSessionContainers(sessionsData) {
                updateSessionContainer('waiting-sessions-container', sessionsData.waiting_sessions, 'waiting');
                updateSessionContainer('active-sessions-container', sessionsData.active_sessions, 'active');
                updateRecentClosedSessions(sessionsData.recent_closed);
            }

            // Update specific session container
            function updateSessionContainer(containerId, sessions, type) {
                const container = document.getElementById(containerId);
                if (!container) return;

                // Clear existing sessions
                container.innerHTML = '';

                if (sessions.length === 0) {
                    const emptyMessage = createEmptyMessage(type);
                    container.appendChild(emptyMessage);
                } else {
                    sessions.forEach(session => {
                        const sessionElement = createSessionElement(session, type);
                        container.appendChild(sessionElement);
                    });
                }
            }

            // Update recent closed sessions
            function updateRecentClosedSessions(sessions) {
                const container = document.querySelector('.space-y-4:last-child');
                if (!container) return;

                container.innerHTML = '';

                if (sessions.length === 0) {
                    const emptyMessage = createEmptyMessage('closed');
                    container.appendChild(emptyMessage);
                } else {
                    sessions.forEach(session => {
                        const sessionElement = createSessionElement(session, 'closed');
                        container.appendChild(sessionElement);
                    });
                }
            }

            // Create session element
            function createSessionElement(session, type) {
                const div = document.createElement('div');
                div.setAttribute('data-session-id', session.session_id);
                div.className =
                    'p-4 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors';

                let content = '';
                if (type === 'waiting') {
                    content = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    ${session.visitor_name || 'Anonymous'}
                                </h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    ${session.priority || 'normal'}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                ${session.visitor_email || 'No email provided'}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                Waiting ${session.waiting_time || 0} minutes
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="assignSessionToMe('${session.session_id}')" 
                                    class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors">
                                Assign to Me
                            </button>
                        </div>
                    </div>
                `;
                } else if (type === 'active') {
                    content = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    ${session.visitor_name || 'Anonymous'}
                                </h4>
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                with ${session.operator?.name || 'Unassigned'}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 last-message">
                                ${session.latest_message?.message?.substring(0, 50) || 'No messages yet'}...
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 last-activity-time">
                                ${timeAgo(session.last_activity_at)}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="/admin/chat/${session.id}" 
                               class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 transition-colors">
                                Join Chat
                            </a>
                        </div>
                    </div>
                `;
                } else if (type === 'closed') {
                    content = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    ${session.visitor_name || 'Anonymous'}
                                </h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400">
                                    ${session.duration || 0}m
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                with ${session.operator?.name || 'Unassigned'}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                Ended ${timeAgo(session.ended_at)}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="/admin/chat/${session.id}" 
                               class="text-xs bg-gray-600 text-white px-2 py-1 rounded hover:bg-gray-700 transition-colors">
                                View History
                            </a>
                        </div>
                    </div>
                `;
                }

                div.innerHTML = content;
                return div;
            }

            // Create empty message
            function createEmptyMessage(type) {
                const div = document.createElement('div');
                div.className = 'text-center py-8';

                let content = '';
                if (type === 'waiting') {
                    content = `
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No waiting sessions</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All sessions are being handled or no new requests.</p>
                `;
                } else if (type === 'active') {
                    content = `
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No active sessions</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No ongoing chat sessions at the moment.</p>
                `;
                } else if (type === 'closed') {
                    content = `
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No recent sessions</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No recently closed chat sessions.</p>
                `;
                }

                div.innerHTML = content;
                return div;
            }

            // Assign session to current user
            async function assignSessionToMe(sessionId) {
                try {
                    const response = await fetch(`/admin/chat/${sessionId}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showNotification('Chat session assigned to you successfully', 'success');
                        // Redirect to chat page
                        window.location.href = `/admin/chat/${sessionId}`;
                    } else {
                        throw new Error(data.message || 'Failed to assign session');
                    }
                } catch (error) {
                    console.error('Failed to assign session:', error);
                    showNotification('Failed to assign chat session', 'error');
                }
            }

            // Utility functions
            function timeAgo(timestamp) {
                if (!timestamp) return 'Unknown';

                const now = new Date();
                const time = new Date(timestamp);
                const diffInMinutes = Math.floor((now - time) / (1000 * 60));

                if (diffInMinutes < 1) return 'Just now';
                if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
                if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
                return `${Math.floor(diffInMinutes / 1440)}d ago`;
            }

            function getStatusColor(status) {
                const colors = {
                    'waiting': 'warning',
                    'active': 'success',
                    'closed': 'info',
                    'urgent': 'danger'
                };
                return colors[status] || 'info';
            }

            // Setup notification sound
            function setupNotificationSound() {
                try {
                    notificationSound = new Audio(
                        'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvGUeBT2U2fPGdSYELYHM89yJOQcZZ7zs5Z9NEAxPqOTvt2MdBjiR2O/NeSsFJHfI8N+QQAoUXrPq66hWFAlFnt/xvWYfBT2U2/PHdSUELYDL89uKOQgZZ7vs5qBOEAxOpuPwuGQdBTiP2PDPeSsFJHbH8OCSQgoTXbPq7KlXFAlFnt/wvmcfBTyU3PLIdCUELYDK89uLOggZZrvr56BOEQxOpuLvuWUdBTiP2fDQeSoFJHbH8OGTRQ=='
                        );
                    notificationSound.volume = 0.3;
                } catch (error) {
                    console.warn('Could not set up notification sound:', error);
                }
            }

            // Play notification sound
            function playNotificationSound() {
                if (notificationSound) {
                    notificationSound.play().catch(e => {
                        console.warn('Could not play notification sound:', e);
                    });
                }
            }

            // Show notification
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className =
                    `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg border transition-all duration-300 ${getNotificationClasses(type)}`;
                notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

                document.body.appendChild(notification);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            }

            function getNotificationClasses(type) {
                const classes = {
                    'success': 'bg-green-50 border-green-200 text-green-800',
                    'error': 'bg-red-50 border-red-200 text-red-800',
                    'warning': 'bg-yellow-50 border-yellow-200 text-yellow-800',
                    'info': 'bg-blue-50 border-blue-200 text-blue-800'
                };
                return classes[type] || classes.info;
            }

            function getNotificationIcon(type) {
                const icons = {
                    'success': '<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                    'error': '<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                    'warning': '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                    'info': '<svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                };
                return icons[type] || icons.info;
            }

            // Start auto refresh
            function startAutoRefresh() {
                // Refresh every 30 seconds when page is visible
                setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        refreshChatStatistics();
                    }
                }, 30000);
            }

            // Export chat data
            function exportChatData() {
                const startDate = prompt('Enter start date (YYYY-MM-DD) or leave blank for last 30 days:');
                const endDate = prompt('Enter end date (YYYY-MM-DD) or leave blank for today:');

                let url = '/admin/chat/reports/export?';
                if (startDate) url += `date_from=${startDate}&`;
                if (endDate) url += `date_to=${endDate}&`;

                window.open(url, '_blank');
            }

            // Show bulk actions
            function showBulkActions() {
                showNotification('Bulk actions feature coming soon!', 'info');
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (chatRefreshInterval) {
                    clearInterval(chatRefreshInterval);
                }
            })
            document.addEventListener('DOMContentLoaded', () => {
                const toggleBtn = document.getElementById('toggle-operator-btn');
                const statusText = document.getElementById('status-text');
                const indicator = document.getElementById('status-indicator');
                const label = document.getElementById('btn-label');

                let operatorOnlineStatus = toggleBtn.dataset.initialStatus === 'online';

                toggleBtn.addEventListener('click', async () => {
                    const action = operatorOnlineStatus ? 'offline' : 'online';
                    const url = `{{ route('admin.chat.operator.offline') }}`.replace('offline', action);

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            operatorOnlineStatus = data.status === 'online';

                            label.textContent = operatorOnlineStatus ? 'Go Offline' : 'Go Online';
                            statusText.textContent = operatorOnlineStatus ? 'Online' : 'Offline';

                            indicator.classList.toggle('bg-green-500', operatorOnlineStatus);
                            indicator.classList.toggle('animate-pulse', operatorOnlineStatus);
                            indicator.classList.toggle('bg-gray-400', !operatorOnlineStatus);

                            showSidebarNotification(
                                operatorOnlineStatus ? 'You are now online for chat support' :
                                'You are now offline',
                                'success'
                            );

                            if (window.location.pathname.includes('/admin/chat')) {
                                setTimeout(() => location.reload(), 1000);
                            }

                        } else {
                            showSidebarNotification('Failed to update operator status', 'error');
                        }

                    } catch (err) {
                        console.error(err);
                        showSidebarNotification('Error updating operator status', 'error');
                    }
                });
            });
        </script>
    @endpush
</x-layouts.admin>
