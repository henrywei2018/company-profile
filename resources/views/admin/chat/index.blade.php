{{-- resources/views/admin/chat/index.blade.php --}}
<x-layouts.admin :title="'Live Chat Dashboard'" :enableCharts="true">

    {{-- Breadcrumb --}}
    <x-admin.breadcrumb :items="['Chat Management' => route('admin.chat.index')]" />

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Live Chat Dashboard</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage active chat sessions and respond to customer inquiries in real-time
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Operator Status Badge --}}
            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" id="operator-status-indicator"></div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300" id="operator-status-text">Online</span>
            </div>

            {{-- Quick Action Buttons --}}
            <x-admin.button href="{{ route('admin.chat.settings') }}" color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'>
                Settings
            </x-admin.button>

            <x-admin.button href="{{ route('admin.chat.templates.index') }}" color="success"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>'>
                Templates
            </x-admin.button>

            <x-admin.button href="{{ route('admin.chat.reports') }}" color="info"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'>
                Reports
            </x-admin.button>
        </div>
    </div>

    {{-- Real-time Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card 
            title="Active Sessions" 
            :value="$stats['active_sessions'] ?? 0" 
            id="active-sessions-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>'
            iconColor="text-green-500" 
            iconBg="bg-green-100 dark:bg-green-800/30" />

        <x-admin.stat-card 
            title="Waiting Queue" 
            :value="$stats['waiting_sessions'] ?? 0" 
            id="waiting-sessions-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-yellow-500" 
            iconBg="bg-yellow-100 dark:bg-yellow-800/30" />

        <x-admin.stat-card 
            title="Online Operators" 
            :value="$stats['online_operators'] ?? 0" 
            id="online-operators-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>'
            iconColor="text-blue-500" 
            iconBg="bg-blue-100 dark:bg-blue-800/30" />

        <x-admin.stat-card 
            title="Today's Sessions" 
            :value="$stats['sessions_today'] ?? 0" 
            id="sessions-today-stat"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
            iconColor="text-purple-500" 
            iconBg="bg-purple-100 dark:bg-purple-800/30" />
    </div>

    {{-- Enhanced Alert Notifications --}}
    @if(($stats['waiting_sessions'] ?? 0) > 0)
        <x-admin.alert type="warning" class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="animate-pulse w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="font-medium">
                        {{ $stats['waiting_sessions'] }} customer{{ $stats['waiting_sessions'] > 1 ? 's are' : ' is' }} waiting for assistance
                    </span>
                </div>
                <button onclick="focusWaitingQueue()" 
                        class="text-yellow-800 hover:text-yellow-900 dark:text-yellow-200 dark:hover:text-yellow-100 font-medium text-sm underline">
                    View Queue →
                </button>
            </div>
        </x-admin.alert>
    @endif

    @if(($stats['online_operators'] ?? 0) === 0)
        <x-admin.alert type="danger" class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                    <span class="font-medium">No operators are currently online</span>
                </div>
                <button onclick="goOnline()" 
                        class="text-red-800 hover:text-red-900 dark:text-red-200 dark:hover:text-red-100 font-medium text-sm underline">
                    Go Online →
                </button>
            </div>
        </x-admin.alert>
    @endif

    {{-- Main Chat Dashboard Component --}}
    <x-admin.chat-dashboard />

    {{-- Enhanced Quick Actions Panel --}}
    <div class="mt-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your chat operations efficiently</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Refresh Data --}}
                <button onclick="refreshChatDashboard()"
                    class="flex items-center justify-center px-4 py-3 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="font-medium">Refresh Data</span>
                </button>

                {{-- Manage Templates --}}
                <a href="{{ route('admin.chat.templates.index') }}"
                    class="flex items-center justify-center px-4 py-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors group">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <span class="font-medium">Templates</span>
                </a>

                {{-- Export Data --}}
                <button onclick="exportChatData()"
                    class="flex items-center justify-center px-4 py-3 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors group">
                    <svg class="w-5 h-5 mr-2 group-hover:translate-y-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium">Export Data</span>
                </button>

                {{-- Operator Status Toggle --}}
                <button onclick="toggleOperatorStatus()"
                    class="flex items-center justify-center px-4 py-3 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors group">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                    <span class="font-medium">Toggle Status</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Performance Metrics (Optional) --}}
    @if(auth()->user()->hasRole(['super-admin', 'admin']))
    <div class="mt-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Performance Metrics</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Today's chat performance overview</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Average Response Time --}}
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="avg-response-time">
                        {{ $stats['avg_response_time'] ?? '-' }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Avg Response Time</div>
                </div>

                {{-- Customer Satisfaction --}}
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="satisfaction-rate">
                        {{ $stats['satisfaction_rate'] ?? '-' }}%
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Customer Satisfaction</div>
                </div>

                {{-- Queue Time --}}
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="avg-queue-time">
                        {{ $stats['avg_queue_time'] ?? '-' }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Avg Queue Time</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Enhanced JavaScript for Real-time Updates --}}
    @push('scripts')
    <script>
        // Global chat dashboard functions
        let chatDashboard = null;
        let statsUpdateInterval = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeChatDashboard();
            startRealTimeUpdates();
        });

        function initializeChatDashboard() {
            // Initialize any additional dashboard functionality
            console.log('🚀 Chat Dashboard initialized');
        }

        function startRealTimeUpdates() {
            // Update statistics every 30 seconds
            statsUpdateInterval = setInterval(updateStats, 30000);
            console.log('📊 Real-time stats updates started');
        }

        async function updateStats() {
            try {
                const response = await fetch('/api/admin/chat/statistics', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        updateStatCards(data.stats);
                    }
                }
            } catch (error) {
                console.error('Failed to update stats:', error);
            }
        }

        function updateStatCards(stats) {
            // Update stat cards with new data
            const elements = {
                'active-sessions-stat': stats.active_sessions || 0,
                'waiting-sessions-stat': stats.waiting_sessions || 0,
                'online-operators-stat': stats.online_operators || 0,
                'sessions-today-stat': stats.sessions_today || 0
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    const valueElement = element.querySelector('[data-stat-value]') || 
                                       element.querySelector('.text-2xl') ||
                                       element.querySelector('.text-3xl');
                    if (valueElement) {
                        // Animate the number change
                        valueElement.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            valueElement.textContent = value;
                            valueElement.style.transform = 'scale(1)';
                        }, 150);
                    }
                }
            });

            // Update performance metrics if available
            if (stats.avg_response_time) {
                updateElement('avg-response-time', stats.avg_response_time);
            }
            if (stats.satisfaction_rate) {
                updateElement('satisfaction-rate', stats.satisfaction_rate + '%');
            }
            if (stats.avg_queue_time) {
                updateElement('avg-queue-time', stats.avg_queue_time);
            }
        }

        function updateElement(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    element.textContent = value;
                    element.style.transform = 'scale(1)';
                }, 100);
            }
        }

        // Quick action functions
        function refreshChatDashboard() {
            // Trigger refresh in the chat dashboard component
            if (window.Alpine && chatDashboard) {
                chatDashboard.refreshSessions();
            }
            
            // Update stats immediately
            updateStats();
            
            // Show feedback
            showNotification('Dashboard refreshed', 'success');
        }

        function exportChatData() {
            window.location.href = '{{ route("admin.chat.reports.export") }}';
        }

        async function toggleOperatorStatus() {
            try {
                const currentStatus = document.getElementById('operator-status-text').textContent.toLowerCase();
                const newStatus = currentStatus === 'online' ? 'away' : 'online';
                
                const response = await fetch('/api/admin/chat/operator/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ status: newStatus })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        updateOperatorStatus(newStatus);
                        showNotification(`Status changed to ${newStatus}`, 'success');
                    }
                }
            } catch (error) {
                console.error('Failed to toggle status:', error);
                showNotification('Failed to update status', 'error');
            }
        }

        function updateOperatorStatus(status) {
            const indicator = document.getElementById('operator-status-indicator');
            const text = document.getElementById('operator-status-text');
            
            if (indicator && text) {
                text.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                
                // Update indicator color
                indicator.className = 'w-2 h-2 rounded-full animate-pulse';
                if (status === 'online') {
                    indicator.classList.add('bg-green-500');
                } else if (status === 'away') {
                    indicator.classList.add('bg-yellow-500');
                } else {
                    indicator.classList.add('bg-red-500');
                }
            }
        }

        function focusWaitingQueue() {
            // Scroll to and highlight waiting queue in chat dashboard
            const waitingSection = document.querySelector('[data-filter="waiting"]');
            if (waitingSection) {
                waitingSection.scrollIntoView({ behavior: 'smooth' });
                waitingSection.click();
            }
        }

        function goOnline() {
            toggleOperatorStatus();
        }

        function showNotification(message, type = 'info') {
            // Simple notification system
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => notification.style.transform = 'translateX(0)', 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (statsUpdateInterval) {
                clearInterval(statsUpdateInterval);
            }
        });
    </script>
    @endpush

</x-layouts.admin>