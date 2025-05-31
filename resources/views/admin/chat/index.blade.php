{{-- resources/views/admin/chat/index.blade.php --}}
<x-layouts.admin title="Live Chat Dashboard" :breadcrumbs="[
    ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['name' => 'Live Chat', 'url' => null]
]">

    <!-- Page Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Live Chat Dashboard</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                Manage customer conversations and provide real-time support
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex gap-3">
            <button type="button" 
                id="operator-status-toggle"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                onclick="toggleOperatorStatus()">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                <span id="status-text">Go Online</span>
            </button>
            <a href="{{ route('admin.chat.settings') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Active Sessions
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="active-sessions-count">
                                {{ $statistics['active_sessions'] }}
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
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Waiting Queue
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="waiting-sessions-count">
                                {{ $statistics['waiting_sessions'] }}
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
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Online Operators
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="online-operators-count">
                                {{ $statistics['online_operators'] ?? 0 }}
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
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Today's Sessions
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white" id="today-sessions-count">
                                {{ $statistics['closed_sessions_today'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chat Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Session Lists -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Waiting Sessions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Waiting for Response
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                            {{ count($waitingSessions) }}
                        </span>
                    </h3>
                </div>
                <div class="max-h-96 overflow-y-auto" id="waiting-sessions-list">
                    @forelse($waitingSessions as $session)
                        <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer session-item"
                             data-session-id="{{ $session->session_id }}"
                             onclick="openChatSession('{{ $session->session_id }}')">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $session->getVisitorName() }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        {{ $session->getVisitorEmail() ?: 'No email provided' }}
                                    </p>
                                    @if($session->latestMessage)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate">
                                            {{ Str::limit($session->latestMessage->message, 50) }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $session->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                           ($session->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400' : 
                                           'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400') }}">
                                        {{ ucfirst($session->priority) }}
                                    </span>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ $session->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No sessions waiting</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Active Conversations
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            {{ count($activeSessions) }}
                        </span>
                    </h3>
                </div>
                <div class="max-h-96 overflow-y-auto" id="active-sessions-list">
                    @forelse($activeSessions as $session)
                        <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer session-item"
                             data-session-id="{{ $session->session_id }}"
                             onclick="openChatSession('{{ $session->session_id }}')">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $session->getVisitorName() }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        Operator: {{ $session->operator->name ?? 'Unassigned' }}
                                    </p>
                                    @if($session->latestMessage)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 truncate">
                                            {{ Str::limit($session->latestMessage->message, 50) }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Active</span>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ $session->last_activity_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No active conversations</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Chat Interface -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg h-96 lg:h-[600px] flex flex-col" id="chat-interface">
                <!-- Chat Header -->
                <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-t-lg" id="chat-header" style="display: none;">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white" id="chat-visitor-name">Select a conversation</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400" id="chat-visitor-email"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" id="assign-to-me-btn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-400 dark:hover:bg-indigo-900/50">
                                Assign to Me
                            </button>
                            <button type="button" id="close-session-btn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                                Close Session
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900" id="chat-messages">
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No conversation selected</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Choose a chat session from the list to start responding to customers.</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-b-lg" id="chat-input-area" style="display: none;">
                    <form id="chat-form" class="flex space-x-2">
                        <div class="flex-1">
                            <textarea 
                                id="message-input" 
                                rows="2" 
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm resize-none"
                                placeholder="Type your response..."
                                maxlength="1000"></textarea>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Send
                            </button>
                            <button type="button" id="template-btn" class="inline-flex items-center px-4 py-1 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Templates
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Closed Sessions -->
    <div class="mt-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Recently Closed Sessions
                    </h3>
                    <a href="{{ route('admin.chat.reports') }}" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                        View Reports →
                    </a>
                </div>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Visitor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Operator
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Duration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Messages
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Closed
                            </th>
                            <th class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentClosedSessions as $session)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($session->getVisitorName(), 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $session->getVisitorName() }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $session->getVisitorEmail() ?: 'No email' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $session->messages->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $session->ended_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.chat.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No closed sessions today
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentSessionId = null;
        let operatorStatus = false;
        let chatMessages = [];
        let chatPollingInterval = null;

        // WebSocket connection (if available)
        let ws = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeChat();
            checkOperatorStatus();
            setupWebSocket();
            
            // Auto-refresh statistics every 30 seconds
            setInterval(refreshStatistics, 30000);
        });

        function initializeChat() {
            const chatForm = document.getElementById('chat-form');
            if (chatForm) {
                chatForm.addEventListener('submit', handleSendMessage);
            }

            const assignBtn = document.getElementById('assign-to-me-btn');
            if (assignBtn) {
                assignBtn.addEventListener('click', assignToMe);
            }

            const closeBtn = document.getElementById('close-session-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeSession);
            }
        }

        function setupWebSocket() {
            // Try to connect to WebSocket if available
            try {
                const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
                const wsUrl = `${wsProtocol}//${window.location.host}/ws/admin-chat`;
                
                ws = new WebSocket(wsUrl);
                
                ws.onopen = function() {
                    console.log('Admin chat WebSocket connected');
                };
                
                ws.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    handleWebSocketMessage(data);
                };
                
                ws.onclose = function() {
                    console.log('Admin chat WebSocket disconnected');
                    // Try to reconnect after 5 seconds
                    setTimeout(setupWebSocket, 5000);
                };
                
                ws.onerror = function(error) {
                    console.error('WebSocket error:', error);
                };
            } catch (error) {
                console.log('WebSocket not available, using polling');
                // Fallback to polling
                startPolling();
            }
        }

        function handleWebSocketMessage(data) {
            switch (data.type) {
                case 'new_session':
                    handleNewSession(data.session);
                    break;
                case 'new_message':
                    handleNewMessage(data.message, data.session_id);
                    break;
                case 'session_closed':
                    handleSessionClosed(data.session_id);
                    break;
                case 'operator_status_changed':
                    updateOperatorCounts();
                    break;
            }
        }

        function startPolling() {
            // Poll for new messages every 5 seconds if a session is active
            chatPollingInterval = setInterval(() => {
                if (currentSessionId) {
                    fetchChatMessages(currentSessionId);
                }
                refreshStatistics();
            }, 5000);
        }

        async function toggleOperatorStatus() {
            try {
                const action = operatorStatus ? 'offline' : 'online';
                const response = await fetch(`/admin/chat/operator/${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    operatorStatus = data.status === 'online';
                    updateOperatorStatusUI();
                    showNotification(
                        operatorStatus ? 'You are now online for chat support' : 'You are now offline',
                        'success'
                    );
                } else {
                    showNotification('Failed to update status', 'error');
                }
            } catch (error) {
                console.error('Error toggling operator status:', error);
                showNotification('Error updating status', 'error');
            }
        }

        function updateOperatorStatusUI() {
            const button = document.getElementById('operator-status-toggle');
            const statusText = document.getElementById('status-text');
            
            if (operatorStatus) {
                button.className = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
                statusText.textContent = 'Go Offline';
            } else {
                button.className = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
                statusText.textContent = 'Go Online';
            }
        }

        async function checkOperatorStatus() {
            try {
                const response = await fetch('/admin/chat/operator/status');
                const data = await response.json();
                
                if (data.success) {
                    operatorStatus = data.is_online;
                    updateOperatorStatusUI();
                }
            } catch (error) {
                console.error('Error checking operator status:', error);
            }
        }

        async function openChatSession(sessionId) {
            currentSessionId = sessionId;
            
            try {
                // Fetch session details
                const response = await fetch(`/admin/chat/${sessionId}/messages`);
                const data = await response.json();
                
                if (data.success) {
                    displayChatSession(data.session, data.messages);
                } else {
                    showNotification('Failed to load chat session', 'error');
                }
            } catch (error) {
                console.error('Error loading chat session:', error);
                showNotification('Error loading chat session', 'error');
            }
        }

        function displayChatSession(session, messages) {
            // Update chat header
            document.getElementById('chat-header').style.display = 'block';
            document.getElementById('chat-input-area').style.display = 'block';
            document.getElementById('chat-visitor-name').textContent = session.visitor_name || 'Anonymous';
            document.getElementById('chat-visitor-email').textContent = session.visitor_email || 'No email provided';
            
            // Display messages
            const messagesContainer = document.getElementById('chat-messages');
            messagesContainer.innerHTML = '';
            
            messages.forEach(message => {
                addMessageToChat(message);
            });
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Update active session highlight
            document.querySelectorAll('.session-item').forEach(item => {
                item.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            });
            
            const activeItem = document.querySelector(`[data-session-id="${session.session_id}"]`);
            if (activeItem) {
                activeItem.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
            }
        }

        function addMessageToChat(message) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageElement = document.createElement('div');
            
            const isFromOperator = message.sender_type === 'operator';
            const isFromSystem = message.sender_type === 'system';
            
            if (isFromSystem) {
                messageElement.className = 'text-center';
                messageElement.innerHTML = `
                    <div class="inline-block px-3 py-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 rounded-full">
                        ${message.message}
                    </div>
                `;
            } else {
                messageElement.className = `flex ${isFromOperator ? 'justify-end' : 'justify-start'}`;
                messageElement.innerHTML = `
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                        isFromOperator 
                            ? 'bg-indigo-600 text-white' 
                            : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600'
                    }">
                        <p class="text-sm">${escapeHtml(message.message)}</p>
                        <p class="text-xs mt-1 ${isFromOperator ? 'text-indigo-200' : 'text-gray-500 dark:text-gray-400'}">
                            ${message.sender_name || (isFromOperator ? 'Operator' : 'Visitor')} • ${formatTime(message.created_at)}
                        </p>
                    </div>
                `;
            }
            
            messagesContainer.appendChild(messageElement);
        }

        async function handleSendMessage(event) {
            event.preventDefault();
            
            if (!currentSessionId) {
                showNotification('Please select a chat session first', 'error');
                return;
            }
            
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            
            if (!message) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/chat/${currentSessionId}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message })
                });
                
                if (response.ok) {
                    messageInput.value = '';
                    
                    // Add message to chat immediately for better UX
                    const operatorMessage = {
                        message: message,
                        sender_type: 'operator',
                        sender_name: 'You',
                        created_at: new Date().toISOString()
                    };
                    addMessageToChat(operatorMessage);
                    
                    // Scroll to bottom
                    const messagesContainer = document.getElementById('chat-messages');
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                } else {
                    showNotification('Failed to send message', 'error');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                showNotification('Error sending message', 'error');
            }
        }

        async function assignToMe() {
            if (!currentSessionId) return;
            
            try {
                const response = await fetch(`/admin/chat/${currentSessionId}/assign`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    showNotification('Session assigned to you', 'success');
                    // Refresh the session to update operator info
                    openChatSession(currentSessionId);
                } else {
                    showNotification('Failed to assign session', 'error');
                }
            } catch (error) {
                console.error('Error assigning session:', error);
                showNotification('Error assigning session', 'error');
            }
        }

        async function closeSession() {
            if (!currentSessionId) return;
            
            if (!confirm('Are you sure you want to close this chat session?')) {
                return;
            }
            
            try {
                const response = await fetch(`/admin/chat/${currentSessionId}/close-session`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    showNotification('Session closed successfully', 'success');
                    
                    // Reset chat interface
                    document.getElementById('chat-header').style.display = 'none';
                    document.getElementById('chat-input-area').style.display = 'none';
                    document.getElementById('chat-messages').innerHTML = `
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Session Closed</h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Select another session to continue.</p>
                            </div>
                        </div>
                    `;
                    
                    currentSessionId = null;
                    
                    // Remove session from lists and refresh
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification('Failed to close session', 'error');
                }
            } catch (error) {
                console.error('Error closing session:', error);
                showNotification('Error closing session', 'error');
            }
        }

        async function refreshStatistics() {
            try {
                const response = await fetch('/admin/chat/api/statistics');
                const data = await response.json();
                
                document.getElementById('active-sessions-count').textContent = data.active_sessions || 0;
                document.getElementById('waiting-sessions-count').textContent = data.waiting_sessions || 0;
                document.getElementById('online-operators-count').textContent = data.online_operators || 0;
                document.getElementById('today-sessions-count').textContent = data.closed_sessions_today || 0;
            } catch (error) {
                console.error('Error refreshing statistics:', error);
            }
        }

        function handleNewSession(session) {
            showNotification(`New chat session from ${session.visitor_name || 'Anonymous'}`, 'info');
            
            // Add to waiting sessions list
            const waitingList = document.getElementById('waiting-sessions-list');
            const sessionElement = createSessionListItem(session);
            waitingList.insertBefore(sessionElement, waitingList.firstChild);
            
            // Update counters
            refreshStatistics();
        }

        function handleNewMessage(message, sessionId) {
            // If this is the current session, add the message
            if (currentSessionId === sessionId) {
                addMessageToChat(message);
                
                // Scroll to bottom
                const messagesContainer = document.getElementById('chat-messages');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            
            // Show notification if message is from visitor
            if (message.sender_type === 'visitor') {
                showNotification(`New message from ${message.sender_name || 'visitor'}`, 'info');
            }
        }

        function handleSessionClosed(sessionId) {
            const sessionItem = document.querySelector(`[data-session-id="${sessionId}"]`);
            if (sessionItem) {
                sessionItem.remove();
            }
            
            if (currentSessionId === sessionId) {
                // Reset chat interface if current session was closed
                document.getElementById('chat-header').style.display = 'none';
                document.getElementById('chat-input-area').style.display = 'none';
                currentSessionId = null;
            }
            
            refreshStatistics();
        }

        function createSessionListItem(session) {
            const div = document.createElement('div');
            div.className = 'px-4 py-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer session-item';
            div.setAttribute('data-session-id', session.session_id);
            div.onclick = () => openChatSession(session.session_id);
            
            div.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            ${session.visitor_name || 'Anonymous'}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                            ${session.visitor_email || 'No email provided'}
                        </p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                            ${session.priority || 'Normal'}
                        </span>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            Just now
                        </p>
                    </div>
                </div>
            `;
            
            return div;
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 transition-all duration-300 transform translate-x-full`;
            
            const bgColor = {
                'success': 'border-green-200 dark:border-green-700',
                'error': 'border-red-200 dark:border-red-700',
                'info': 'border-blue-200 dark:border-blue-700',
                'warning': 'border-yellow-200 dark:border-yellow-700'
            }[type] || 'border-gray-200 dark:border-gray-700';
            
            notification.className += ` ${bgColor}`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button type="button" class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
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

        function getNotificationIcon(type) {
            const icons = {
                'success': '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                'error': '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                'info': '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
                'warning': '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons['info'];
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Auto-resize textarea
        document.getElementById('message-input').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Send message with Ctrl+Enter
        document.getElementById('message-input').addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'Enter') {
                event.preventDefault();
                document.getElementById('chat-form').dispatchEvent(new Event('submit'));
            }
        });
    </script>
    @endpush

</x-layouts.admin>