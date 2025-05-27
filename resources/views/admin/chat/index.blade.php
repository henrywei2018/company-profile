{{-- resources/views/admin/chat/index.blade.php --}}
<x-layouts.admin title="Live Chat Dashboard" :enableCharts="true">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Live Chat Dashboard</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Monitor and manage customer chat sessions</p>
        </div>
        <div class="flex gap-3">
            <x-admin.button color="light" href="{{ route('admin.chat.settings') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </x-admin.button>
            
            <x-admin.button color="info" onclick="refreshDashboard()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </x-admin.button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-admin.card noPadding>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Chats</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $statistics['active_sessions'] ?? 0 }}
                                </div>
                                @if(($statistics['active_sessions'] ?? 0) > 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <svg class="self-center flex-shrink-0 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="sr-only">Increased by</span>
                                        Active
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </x-admin.card>
        
        <x-admin.card noPadding>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Waiting Queue</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $statistics['waiting_sessions'] ?? 0 }}
                                </div>
                                @if(($statistics['waiting_sessions'] ?? 0) > 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-red-600">
                                        <svg class="self-center flex-shrink-0 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="sr-only">Needs attention</span>
                                        Waiting
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </x-admin.card>
        
        <x-admin.card noPadding>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Online Operators</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $statistics['online_operators'] ?? 0 }}
                                </div>
                                <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    / {{ $statistics['available_operators'] ?? 0 }} available
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </x-admin.card>
        
        <x-admin.card noPadding>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Today's Chats</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $statistics['today_sessions'] ?? 0 }}
                                </div>
                                @if(($statistics['avg_response_time'] ?? 0) > 0)
                                    <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($statistics['avg_response_time'], 1) }}min avg
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </x-admin.card>
    </div>

    <!-- Active Chat Sessions -->
    @if($activeSessions->count() > 0)
        <x-admin.card title="Active Chat Sessions" class="mb-6">
            <div class="space-y-4">
                @foreach($activeSessions as $session)
                    <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex items-center space-x-4">
                            <!-- Avatar -->
                            <div class="relative">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-medium">
                                    {{ substr($session->getVisitorName(), 0, 1) }}
                                </div>
                                <!-- Online indicator -->
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></div>
                            </div>
                            
                            <!-- Session Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $session->getVisitorName() }}</h4>
                                    @if($session->priority === 'urgent')
                                        <x-admin.badge type="danger" size="sm">Urgent</x-admin.badge>
                                    @elseif($session->priority === 'high')
                                        <x-admin.badge type="warning" size="sm">High</x-admin.badge>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    @if($session->getVisitorEmail())
                                        <span>📧 {{ $session->getVisitorEmail() }}</span>
                                    @endif
                                    <span>⏰ Started {{ $session->started_at->diffForHumans() }}</span>
                                    @if($session->operator)
                                        <span>👨‍💼 {{ $session->operator->name }}</span>
                                    @endif
                                </div>
                                
                                @if($session->latestMessage)
                                    <div class="mt-2 p-2 bg-white dark:bg-gray-800 rounded border">
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <span class="font-medium">{{ $session->latestMessage->getSenderName() }}:</span>
                                            "{{ \Illuminate\Support\Str::limit($session->latestMessage->message, 80) }}"
                                        </p>
                                        <span class="text-xs text-gray-400">{{ $session->latestMessage->created_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <x-admin.badge type="success" size="sm">Active</x-admin.badge>
                            <x-admin.button size="sm" href="{{ route('admin.chat.show', $session) }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Join Chat
                            </x-admin.button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif

    <!-- Waiting Queue -->
    @if($waitingSessions->count() > 0)
        <x-admin.card title="Waiting Queue" class="mb-6">
            <x-slot name="title">
                <div class="flex items-center justify-between w-full">
                    <span>Waiting Queue</span>
                    <x-admin.badge type="warning">{{ $waitingSessions->count() }} waiting</x-admin.badge>
                </div>
            </x-slot>
            
            <div class="space-y-4">
                @foreach($waitingSessions as $session)
                    <div class="flex items-center justify-between p-4 {{ $session->created_at->diffInHours() > 1 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800' }} rounded-lg border">
                        <div class="flex items-center space-x-4">
                            <!-- Avatar with timer -->
                            <div class="relative">
                                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-medium">
                                    {{ substr($session->getVisitorName(), 0, 1) }}
                                </div>
                                <!-- Waiting indicator -->
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 border-2 border-white rounded-full animate-pulse"></div>
                            </div>
                            
                            <!-- Session Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $session->getVisitorName() }}</h4>
                                    @if($session->created_at->diffInHours() > 1)
                                        <x-admin.badge type="danger" size="sm">⚠️ Long Wait</x-admin.badge>
                                    @endif
                                    @if($session->priority === 'urgent')
                                        <x-admin.badge type="danger" size="sm">Urgent</x-admin.badge>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    @if($session->getVisitorEmail())
                                        <span>📧 {{ $session->getVisitorEmail() }}</span>
                                    @endif
                                    <span class="{{ $session->created_at->diffInHours() > 1 ? 'text-red-600 font-medium' : 'text-yellow-600 font-medium' }}">
                                        ⏰ Waiting {{ $session->started_at->diffForHumans() }}
                                    </span>
                                </div>
                                
                                @if($session->latestMessage)
                                    <div class="mt-2 p-2 bg-white dark:bg-gray-800 rounded border">
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <span class="font-medium">{{ $session->latestMessage->getSenderName() }}:</span>
                                            "{{ \Illuminate\Support\Str::limit($session->latestMessage->message, 80) }}"
                                        </p>
                                        <span class="text-xs text-gray-400">{{ $session->latestMessage->created_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <x-admin.badge type="warning" size="sm">Waiting</x-admin.badge>
                            <x-admin.button size="sm" color="primary" href="{{ route('admin.chat.show', $session) }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Respond Now
                            </x-admin.button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif

    <!-- Recent Closed Sessions -->
    @if(isset($recentClosedSessions) && $recentClosedSessions->count() > 0)
        <x-admin.card title="Recent Closed Sessions" class="mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visitor</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Messages</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operator</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ended</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentClosedSessions as $session)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                            {{ substr($session->getVisitorName(), 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $session->getVisitorName() }}</div>
                                            @if($session->getVisitorEmail())
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $session->getVisitorEmail() }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $session->getDuration() ? $session->getDuration() . ' min' : '-' }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $session->messages_count ?? $session->messages->count() }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $session->operator ? $session->operator->name : 'Bot' }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $session->ended_at ? $session->ended_at->diffForHumans() : '-' }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <x-admin.button size="sm" color="light" href="{{ route('admin.chat.show', $session) }}">
                                        View
                                    </x-admin.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    @endif

    <!-- Empty State -->
    @if($activeSessions->count() === 0 && $waitingSessions->count() === 0)
        <x-admin.card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No active chats</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    All caught up! No customers are currently chatting.
                </p>
                <div class="mt-6">
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        This page refreshes automatically every 30 seconds
                    </p>
                </div>
            </div>
        </x-admin.card>
    @endif

    @push('scripts')
    <script>
        // Auto-refresh dashboard every 30 seconds
        let refreshInterval = setInterval(function() {
            refreshDashboard();
        }, 30000);
        
        function refreshDashboard() {
            // Show loading state
            const refreshBtn = document.querySelector('[onclick="refreshDashboard()"]');
            if (refreshBtn) {
                refreshBtn.disabled = true;
                refreshBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Refreshing...';
            }
            
            // Reload the page to get fresh data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
        
        // Play notification sound for urgent chats
        function playUrgentNotification() {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvGUeBT2U2fPGdSYELYHM89yJOQcZZ7zs5Z9NEAxPqOTvt2MdBjiR2O/NeSsFJHfI8N+QQAoUXrPq66hWFAlFnt/xvWYfBT2U2/PHdSUELYDL89uKOQgZZ7vs5qBOEAxOpuPwuGQdBTiP2PDPeSsFJHbH8OCSQgoTXbPq7KlXFAlFnt/wvmcfBTyU3PLIdCUELYDK89uLOggZZrvr56BOEQxOpuLvuWUdBTiP2fDQeSoFJHbH8OGTRQ==');
                audio.volume = 0.5;
                audio.play().catch(() => {}); // Ignore errors for sound
            } catch (error) {
                // Silently ignore audio errors
            }
        }
        
        // Check for urgent sessions on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urgentSessions = document.querySelectorAll('[data-priority="urgent"]');
            if (urgentSessions.length > 0) {
                playUrgentNotification();
            }
            
            // Show desktop notification if supported
            if (Notification.permission === 'granted' && urgentSessions.length > 0) {
                new Notification('Urgent Chat Alert', {
                    body: `${urgentSessions.length} urgent chat session(s) need immediate attention`,
                    icon: '/favicon.ico'
                });
            }
        });
        
        // Request notification permission on first visit
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        // Cleanup interval on page unload
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
    @endpush
</x-layouts.admin>