<!-- Admin Chat Dashboard Component -->
<div x-data="adminChatDashboard()" 
     x-init="init()"
     data-api-url="/api/admin/chat"
     data-user-type="admin"
     data-user-id="{{ auth()->id() }}"
     data-user-name="{{ auth()->user()->name }}"
     class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">

    <!-- Dashboard Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Live Chat Dashboard</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage active chat sessions and respond to customer inquiries
                </p>
            </div>
            
            <!-- Real-time Status Indicator -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div x-show="connectionState === 'connected'" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <div x-show="connectionState === 'connecting'" class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                    <div x-show="connectionState === 'disconnected'" class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="getConnectionStatusText()"></span>
                </div>
                
                <!-- Refresh Button -->
                <button @click="refreshSessions()" 
                        :disabled="isLoading"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <svg x-show="!isLoading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <svg x-show="isLoading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Active Sessions -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Chats</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.active || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Waiting Sessions -->
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Waiting</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.waiting || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Today's Sessions -->
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Today</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.today || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Average Response Time -->
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Response</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.avgResponse || '-'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Sessions List -->
    <div class="flex flex-col lg:flex-row h-96">
        <!-- Sessions Sidebar -->
        <div class="w-full lg:w-1/3 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
            <!-- Filter Tabs -->
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex space-x-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button @click="currentFilter = 'all'" 
                            :class="currentFilter === 'all' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                            class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-colors">
                        All (<span x-text="sessions.length"></span>)
                    </button>
                    <button @click="currentFilter = 'waiting'" 
                            :class="currentFilter === 'waiting' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                            class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-colors">
                        Waiting (<span x-text="getFilteredSessions('waiting').length"></span>)
                    </button>
                    <button @click="currentFilter = 'active'" 
                            :class="currentFilter === 'active' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                            class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md transition-colors">
                        Active (<span x-text="getFilteredSessions('active').length"></span>)
                    </button>
                </div>
            </div>

            <!-- Sessions List -->
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <!-- Loading State -->
                <div x-show="isLoading && sessions.length === 0" class="p-4 text-center">
                    <div class="flex items-center justify-center space-x-2 text-gray-500">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                        <span class="text-sm">Loading sessions...</span>
                    </div>
                </div>

                <!-- Empty State -->
                <div x-show="!isLoading && getFilteredSessions(currentFilter).length === 0" class="p-8 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No chat sessions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="getEmptyStateMessage()"></p>
                </div>

                <!-- Session Items -->
                <template x-for="session in getFilteredSessions(currentFilter)" :key="session.session_id">
                    <div @click="selectSession(session)" 
                         :class="selectedSession?.session_id === session.session_id ? 'bg-blue-50 dark:bg-blue-900/20 border-r-2 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                         class="p-4 cursor-pointer transition-colors relative">
                        
                        <!-- Priority/Urgency Indicator -->
                        <div x-show="session.priority === 'high'" class="absolute top-2 right-2 w-3 h-3 bg-red-500 rounded-full"></div>
                        <div x-show="session.status === 'waiting'" class="absolute top-2 right-2 w-3 h-3 bg-orange-500 rounded-full animate-pulse"></div>
                        
                        <!-- Session Info -->
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="session.visitor_name || 'Anonymous User'"></h4>
                                    
                                    <!-- Status Badge -->
                                    <span :class="getStatusBadgeClass(session.status)" 
                                          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                          x-text="session.status"></span>
                                </div>
                                
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1" x-text="session.visitor_email || 'No email provided'"></p>
                                
                                <!-- Last Message Preview -->
                                <p class="text-sm text-gray-600 dark:text-gray-300 truncate" x-text="session.last_message || 'No messages yet'"></p>
                                
                                <!-- Timestamp -->
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-400" x-text="formatRelativeTime(session.started_at)"></span>
                                    
                                    <!-- Unread Count -->
                                    <span x-show="session.unread_count > 0" 
                                          x-text="session.unread_count > 99 ? '99+' : session.unread_count"
                                          class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Chat Interface -->
        <div class="flex-1 flex flex-col">
            <!-- No Session Selected -->
            <div x-show="!selectedSession" class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Select a Chat Session</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                        Choose a chat session from the left sidebar to start responding to customer inquiries.
                    </p>
                </div>
            </div>

            <!-- Active Chat Interface -->
            <div x-show="selectedSession" class="flex-1 flex flex-col">
                <!-- Chat Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Customer Avatar -->
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-sm" x-text="getInitials(selectedSession?.visitor_name)"></span>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="selectedSession?.visitor_name || 'Anonymous User'"></h3>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedSession?.visitor_email || 'No email'"></span>
                                    <span :class="getStatusBadgeClass(selectedSession?.status)" 
                                          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                          x-text="selectedSession?.status"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Actions -->
                        <div class="flex items-center space-x-2">
                            <!-- Take Session -->
                            <button x-show="selectedSession?.status === 'waiting'" 
                                    @click="takeSession(selectedSession.session_id)"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Take Session
                            </button>

                            <!-- Close Session -->
                            <button x-show="selectedSession?.status === 'active'" 
                                    @click="closeSession(selectedSession.session_id)"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Close Session
                            </button>

                            <!-- Session Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1">
                                        <button @click="transferSession(selectedSession.session_id); open = false" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Transfer Session
                                        </button>
                                        <button @click="flagSession(selectedSession.session_id); open = false" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            Flag as Important
                                        </button>
                                        <button @click="viewSessionHistory(selectedSession.session_id); open = false" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            View History
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900" 
                     x-ref="messagesContainer">
                    
                    <!-- Session Info Banner -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-blue-700 dark:text-blue-400">
                                Session started <span x-text="formatDateTime(selectedSession?.started_at)"></span>
                                <span x-show="selectedSession?.operator">
                                    - Handled by <strong x-text="selectedSession.operator.name"></strong>
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Messages -->
                    <template x-for="message in currentSessionMessages" :key="message.id">
                        <div class="flex" :class="message.sender_type === 'visitor' ? 'justify-start' : 'justify-end'">
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg"
                                 :class="message.sender_type === 'visitor' ? 
                                         'bg-white dark:bg-gray-800 border shadow-sm' : 
                                         'bg-blue-500 text-white'">
                                
                                <!-- Message Content -->
                                <div class="break-words">
                                    <!-- Text Message -->
                                    <div x-show="message.type === 'text'" x-text="message.content"></div>
                                    
                                    <!-- File Attachment -->
                                    <div x-show="message.type === 'file'" class="space-y-2">
                                        <div class="flex items-center space-x-2 p-2 bg-black bg-opacity-10 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <a :href="message.attachments?.[0]?.url" 
                                               target="_blank"
                                               class="text-sm underline" 
                                               x-text="message.attachments?.[0]?.original_name || 'File'"></a>
                                        </div>
                                        <div x-show="message.content" x-text="message.content"></div>
                                    </div>
                                    
                                    <!-- Image Attachment -->
                                    <div x-show="message.type === 'image'" class="space-y-2">
                                        <img :src="message.attachments?.[0]?.url" 
                                             :alt="message.attachments?.[0]?.original_name"
                                             class="max-w-full rounded cursor-pointer"
                                             @click="openImagePreview(message.attachments[0])">
                                        <div x-show="message.content" x-text="message.content"></div>
                                    </div>
                                </div>
                                
                                <!-- Message Meta -->
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs opacity-70" x-text="formatTime(message.created_at)"></span>
                                    <span x-show="message.sender_type !== 'visitor'" class="text-xs opacity-70" x-text="message.sender_name"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Typing Indicator -->
                    <div x-show="visitorTyping" class="flex justify-start">
                        <div class="bg-white dark:bg-gray-800 border shadow-sm max-w-xs px-4 py-2 rounded-lg rounded-bl-none">
                            <div class="flex items-center space-x-1">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                                <span class="text-xs text-gray-500 ml-2">Customer is typing...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reply Area -->
                <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <!-- Quick Responses -->
                    <div x-show="showQuickResponses" class="mb-4 space-y-2">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Quick Responses:</h4>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="response in quickResponses" :key="response.id">
                                <button @click="newMessage = response.content; $refs.messageInput.focus()" 
                                        class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full transition-colors"
                                        x-text="response.title"></button>
                            </template>
                        </div>
                    </div>

                    <form @submit.prevent="sendAdminMessage()" class="space-y-3">
                        <!-- File Preview Area -->
                        <div x-show="selectedFiles.length > 0" class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <span>Attached files:</span>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(file, index) in selectedFiles" :key="index">
                                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-600 rounded border">
                                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="text-sm truncate" x-text="file.name"></span>
                                            <span class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></span>
                                        </div>
                                        <button @click="removeFile(index)" type="button" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Main Input Row -->
                        <div class="flex items-end space-x-3">
                            <!-- File Upload Button -->
                            <label class="flex-shrink-0 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <input type="file" 
                                       multiple 
                                       class="hidden" 
                                       @change="handleFileSelect($event)"
                                       accept="image/*,.pdf,.doc,.docx,.txt">
                            </label>

                            <!-- Quick Responses Toggle -->
                            <button type="button" 
                                    @click="showQuickResponses = !showQuickResponses"
                                    :class="showQuickResponses ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                                    class="flex-shrink-0 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </button>

                            <!-- Message Input -->
                            <div class="flex-1 relative">
                                <textarea x-model="newMessage"
                                        @keydown="handleKeyDown($event)"
                                        @input="handleAdminTyping()"
                                        :disabled="!selectedSession || selectedSession.status !== 'active' || isLoading"
                                        placeholder="Type your reply..."
                                        rows="1"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        style="min-height: 3rem; max-height: 8rem;"
                                        x-ref="messageInput"></textarea>
                            </div>

                            <!-- Send Button -->
                            <button type="submit" 
                                    :disabled="(!newMessage.trim() && selectedFiles.length === 0) || !selectedSession || selectedSession.status !== 'active' || isLoading"
                                    class="flex-shrink-0 px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium">
                                <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <svg x-show="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Session Status Message -->
                        <div x-show="selectedSession && selectedSession.status !== 'active'" class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                <span x-show="selectedSession?.status === 'waiting'">
                                    This session is waiting for an operator. Click "Take Session" to start responding.
                                </span>
                                <span x-show="selectedSession?.status === 'closed'">
                                    This session has been closed and is read-only.
                                </span>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Component Script -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminChatDashboard', () => ({
        // Chat system integration
        chatSystem: null,
        
        // State management
        sessions: [],
        selectedSession: null,
        currentSessionMessages: [],
        stats: {
            active: 0,
            waiting: 0,
            today: 0,
            avgResponse: '-'
        },
        
        // UI state
        isLoading: false,
        connectionState: 'disconnected',
        currentFilter: 'all',
        newMessage: '',
        selectedFiles: [],
        showQuickResponses: false,
        visitorTyping: false,
        
        // Quick responses
        quickResponses: [
            { id: 1, title: 'Hello', content: 'Hello! How can I help you today?' },
            { id: 2, title: 'Thanks', content: 'Thank you for contacting us. Is there anything else I can help you with?' },
            { id: 3, title: 'Check Info', content: 'Let me check that information for you. Please give me a moment.' },
            { id: 4, title: 'Transfer', content: 'I\'m going to transfer you to a specialist who can better assist you.' },
            { id: 5, title: 'Follow Up', content: 'I\'ll follow up with you via email within 24 hours.' }
        ],

        async init() {
            // Initialize chat system for admin
            this.chatSystem = new ChatSystem({
                baseUrl: this.$el.dataset.apiUrl,
                userId: this.$el.dataset.userId,
                userType: this.$el.dataset.userType,
                userName: this.$el.dataset.userName
            });

            // Setup event listeners
            this.setupEventListeners();
            
            // Load initial data
            await this.loadSessions();
            await this.loadStats();
            
            // Setup auto-refresh
            this.setupAutoRefresh();
        },

        setupEventListeners() {
            if (!this.chatSystem) return;

            // Connection events
            this.chatSystem.on('connection:established', () => {
                this.connectionState = 'connected';
            });

            this.chatSystem.on('connection:reconnecting', () => {
                this.connectionState = 'connecting';
            });

            this.chatSystem.on('connection:failed', () => {
                this.connectionState = 'disconnected';
            });

            // Session events
            this.chatSystem.on('session:new', (session) => {
                this.sessions.unshift(session);
                this.updateStats();
                this.playNotificationSound();
            });

            this.chatSystem.on('session:updated', (session) => {
                const index = this.sessions.findIndex(s => s.session_id === session.session_id);
                if (index >= 0) {
                    this.sessions[index] = session;
                    if (this.selectedSession?.session_id === session.session_id) {
                        this.selectedSession = session;
                    }
                }
            });

            // Message events
            this.chatSystem.on('message:received', (message) => {
                if (this.selectedSession && message.session_id === this.selectedSession.session_id) {
                    this.currentSessionMessages.push(message);
                    this.$nextTick(() => this.scrollToBottom());
                }
                
                // Update session unread count
                const session = this.sessions.find(s => s.session_id === message.session_id);
                if (session && message.sender_type === 'visitor') {
                    session.unread_count = (session.unread_count || 0) + 1;
                    session.last_message = message.content;
                }

                this.playNotificationSound();
            });

            // Typing events
            this.chatSystem.on('typing:indicator', (data) => {
                if (this.selectedSession && data.session_id === this.selectedSession.session_id) {
                    this.visitorTyping = data.is_typing;
                    if (data.is_typing) {
                        setTimeout(() => {
                            this.visitorTyping = false;
                        }, 3000);
                    }
                }
            });
        },

        // Data loading methods
        async loadSessions() {
            this.isLoading = true;
            try {
                const response = await this.apiCall('GET', '/sessions');
                if (response.success) {
                    this.sessions = response.data || [];
                }
            } catch (error) {
                console.error('Failed to load sessions:', error);
                this.showError('Failed to load chat sessions');
            } finally {
                this.isLoading = false;
            }
        },

        async loadStats() {
            try {
                const response = await this.apiCall('GET', '/statistics');
                if (response.success) {
                    this.stats = response.data;
                }
            } catch (error) {
                console.error('Failed to load stats:', error);
            }
        },

        async refreshSessions() {
            await this.loadSessions();
            await this.loadStats();
            if (this.selectedSession) {
                await this.loadSessionMessages(this.selectedSession.session_id);
            }
        },

        // Session management
        async selectSession(session) {
            this.selectedSession = session;
            this.currentSessionMessages = [];
            
            // Mark as read
            if (session.unread_count > 0) {
                session.unread_count = 0;
                this.markSessionAsRead(session.session_id);
            }
            
            // Load messages
            await this.loadSessionMessages(session.session_id);
            
            this.$nextTick(() => {
                this.scrollToBottom();
                this.$refs.messageInput?.focus();
            });
        },

        async loadSessionMessages(sessionId) {
            try {
                const response = await this.apiCall('GET', `/sessions/${sessionId}/messages`);
                if (response.success) {
                    this.currentSessionMessages = response.data || [];
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                console.error('Failed to load messages:', error);
                this.showError('Failed to load messages');
            }
        },

        async takeSession(sessionId) {
            try {
                const response = await this.apiCall('POST', `/sessions/${sessionId}/take`);
                if (response.success) {
                    // Update session status
                    const session = this.sessions.find(s => s.session_id === sessionId);
                    if (session) {
                        session.status = 'active';
                        session.operator = response.data.operator;
                        if (this.selectedSession?.session_id === sessionId) {
                            this.selectedSession = session;
                        }
                    }
                    this.updateStats();
                    this.showSuccess('Session taken successfully');
                }
            } catch (error) {
                console.error('Failed to take session:', error);
                this.showError('Failed to take session');
            }
        },

        async closeSession(sessionId) {
            if (!confirm('Are you sure you want to close this chat session?')) {
                return;
            }

            try {
                const response = await this.apiCall('POST', `/sessions/${sessionId}/close`);
                if (response.success) {
                    // Update session status
                    const session = this.sessions.find(s => s.session_id === sessionId);
                    if (session) {
                        session.status = 'closed';
                        if (this.selectedSession?.session_id === sessionId) {
                            this.selectedSession = session;
                        }
                    }
                    this.updateStats();
                    this.showSuccess('Session closed successfully');
                }
            } catch (error) {
                console.error('Failed to close session:', error);
                this.showError('Failed to close session');
            }
        },

        async markSessionAsRead(sessionId) {
            try {
                await this.apiCall('POST', `/sessions/${sessionId}/read`);
            } catch (error) {
                console.error('Failed to mark session as read:', error);
            }
        },

        // Message handling
        async sendAdminMessage() {
            if ((!this.newMessage.trim() && this.selectedFiles.length === 0) || 
                !this.selectedSession || this.selectedSession.status !== 'active' || this.isLoading) {
                return;
            }

            let message = this.newMessage.trim();
            let attachments = null;

            // Handle file uploads
            if (this.selectedFiles.length > 0) {
                try {
                    attachments = [];
                    for (const file of this.selectedFiles) {
                        const uploadedFile = await this.uploadFile(file);
                        attachments.push(uploadedFile);
                    }
                } catch (error) {
                    this.showError('Failed to upload files');
                    return;
                }
            }

            // Send message
            const messageType = attachments?.some(a => a.type.startsWith('image/')) ? 'image' : 
                               attachments?.length > 0 ? 'file' : 'text';

            try {
                const response = await this.apiCall('POST', `/sessions/${this.selectedSession.session_id}/messages`, {
                    content: message,
                    type: messageType,
                    attachments: attachments
                });

                if (response.success) {
                    this.currentSessionMessages.push(response.data);
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                this.showError('Failed to send message');
            }

            // Clear input
            this.newMessage = '';
            this.selectedFiles = [];
            this.autoResizeTextarea();
        },

        async uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('session_id', this.selectedSession.session_id);

            try {
                const response = await fetch(`${this.$el.dataset.apiUrl}/upload`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    return data.data;
                }
                
                throw new Error(data.message || 'Upload failed');
            } catch (error) {
                console.error('File upload error:', error);
                throw error;
            }
        },

        // File handling
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['image/', 'application/pdf', 'text/', 'application/msword', 'application/vnd.openxmlformats-officedocument'];

            for (const file of files) {
                if (file.size > maxSize) {
                    this.showError(`File ${file.name} is too large. Maximum size is 10MB.`);
                    continue;
                }

                if (!allowedTypes.some(type => file.type.startsWith(type))) {
                    this.showError(`File type ${file.type} is not allowed.`);
                    continue;
                }

                this.selectedFiles.push(file);
            }

            event.target.value = '';
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },

        // Input handling
        handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                this.sendAdminMessage();
            }
        },

        handleAdminTyping() {
            this.autoResizeTextarea();
            // Send typing indicator to customer
            if (this.selectedSession) {
                this.chatSystem?.sendTypingIndicator(true);
            }
        },

        autoResizeTextarea() {
            const textarea = this.$refs.messageInput;
            if (textarea) {
                textarea.style.height = 'auto';
                textarea.style.height = Math.min(textarea.scrollHeight, 128) + 'px';
            }
        },

        // Filtering and display
        getFilteredSessions(filter) {
            switch (filter) {
                case 'waiting':
                    return this.sessions.filter(s => s.status === 'waiting');
                case 'active':
                    return this.sessions.filter(s => s.status === 'active');
                default:
                    return this.sessions;
            }
        },

        getEmptyStateMessage() {
            switch (this.currentFilter) {
                case 'waiting':
                    return 'No sessions are waiting for operators';
                case 'active':
                    return 'No active chat sessions';
                default:
                    return 'No chat sessions found';
            }
        },

        // Utility methods
        setupAutoRefresh() {
            setInterval(() => {
                this.refreshSessions();
            }, 30000); // Refresh every 30 seconds
        },

        updateStats() {
            this.stats.active = this.sessions.filter(s => s.status === 'active').length;
            this.stats.waiting = this.sessions.filter(s => s.status === 'waiting').length;
            this.stats.today = this.sessions.filter(s => this.isToday(s.started_at)).length;
        },

        isToday(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            return date.toDateString() === today.toDateString();
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
        },

        formatDateTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        },

        formatRelativeTime(timestamp) {
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

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        getInitials(name) {
            if (!name) return '?';
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        },

        getStatusBadgeClass(status) {
            switch (status) {
                case 'waiting':
                    return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
                case 'active':
                    return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                case 'closed':
                    return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
                default:
                    return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
            }
        },

        getConnectionStatusText() {
            switch (this.connectionState) {
                case 'connected': return 'Connected';
                case 'connecting': return 'Connecting...';
                case 'disconnected': return 'Disconnected';
                default: return 'Unknown';
            }
        },

        // API helper
        async apiCall(method, endpoint, data = null) {
            const url = `${this.$el.dataset.apiUrl}${endpoint}`;
            const options = {
        method,
        headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin' // ✅ FOR SESSION AUTH
            };

            if (data && ['POST', 'PUT', 'PATCH'].includes(method)) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        },

        // Session actions
        async transferSession(sessionId) {
            // Implementation for transferring session to another operator
            this.showInfo('Transfer functionality not implemented yet');
        },

        async flagSession(sessionId) {
            // Implementation for flagging session as important
            this.showInfo('Flag functionality not implemented yet');
        },

        async viewSessionHistory(sessionId) {
            // Implementation for viewing session history
            this.showInfo('History functionality not implemented yet');
        },

        // Notification methods
        playNotificationSound() {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvGUeBT2U2fPGdSYELYHM89yJOQcZZ7zs5Z9NEAxPqOTvt2MdBjiR2O/NeSsFJHfI8N+QQAoUXrPq66hWFAlFnt/xvWYfBT2U2/PHdSUELYDL89uKOQgZZ7vs5qBOEAxOpuPwuGQdBTiP2PDPeSsFJHbH8OGTRQ==');
                audio.volume = 0.3;
                audio.play().catch(() => {});
            } catch (error) {
                // Ignore audio errors
            }
        },

        showSuccess(message) {
            this.showNotification('success', message);
        },

        showError(message) {
            this.showNotification('error', message);
        },

        showInfo(message) {
            this.showNotification('info', message);
        },

        showNotification(type, message) {
            const notification = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 
                           type === 'success' ? 'bg-green-500' : 'bg-blue-500';
            
            notification.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 max-w-sm`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        },

        openImagePreview(attachment) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="relative max-w-4xl max-h-4xl p-4">
                    <img src="${attachment.url}" alt="${attachment.original_name}" class="max-w-full max-h-full">
                    <button class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-2" onclick="this.closest('.fixed').remove()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(modal);
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }
    }));
});
</script>