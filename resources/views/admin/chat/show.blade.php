{{-- resources/views/admin/chat/show.blade.php - FIXED VERSION with Takeover & Status Sync --}}
<x-layouts.admin title="Chat Session">
    @push('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="chat-session-id" content="{{ $chatSession->session_id }}">
        <meta name="chat-session-db-id" content="{{ $chatSession->id }}">
        <meta name="current-operator-id" content="{{ auth()->id() }}">
        <meta name="assigned-operator-id" content="{{ $chatSession->assigned_operator_id ?? '' }}">
        <meta name="session-status" content="{{ $chatSession->status }}">
    @endpush

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Dashboard' => route('admin.dashboard'),
        'Live Chat' => route('admin.chat.index'), 
        'Chat with ' . $chatSession->getVisitorName() => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Chat with {{ $chatSession->getVisitorName() }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Started {{ $chatSession->started_at->diffForHumans() }}
                @if($chatSession->ended_at)
                    • Ended {{ $chatSession->ended_at->diffForHumans() }}
                @endif
                • Session ID: {{ $chatSession->session_id }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Connection Status -->
            <div id="connection-status" class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium">Connected</span>
            </div>

            <!-- Session Status -->
            <x-admin.badge 
                :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')" 
                id="session-status-badge">
                {{ ucfirst($chatSession->status) }}
            </x-admin.badge>

            <!-- Takeover Button (if not assigned to current user) -->
            @if($chatSession->assigned_operator_id !== auth()->id() && $chatSession->status !== 'closed')
                <button id="takeover-btn" 
                        onclick="takeOverSession()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Take Over Chat
                </button>
            @elseif($chatSession->assigned_operator_id === auth()->id() && $chatSession->status === 'waiting')
                <!-- If assigned to current user but status is still 'waiting', show activate button -->
                <button id="activate-btn" 
                        onclick="activateSession()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Activate Session
                </button>
            @endif

            <!-- Actions Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-1 px-3 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" 
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-10">
                    <div class="py-1">
                        @if($chatSession->status !== 'closed')
                            <button onclick="transferSession()" 
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Transfer to Another Operator
                            </button>
                            <button onclick="updatePriority()" 
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Change Priority
                            </button>
                            <hr class="my-1 border-gray-200 dark:border-gray-600">
                            <button onclick="closeSession()" 
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                Close Session
                            </button>
                        @endif
                        <button onclick="exportTranscript()" 
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Export Transcript
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Chat Messages Column (3/4 width on large screens) -->
        <div class="lg:col-span-3">
            <x-admin.card class="h-full">
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Conversation
                        </h3>
                        <div class="flex items-center gap-3">
                            <!-- Typing Indicator -->
                            <div id="visitor-typing" class="hidden flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex gap-1">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                                <span>{{ $chatSession->getVisitorName() }} is typing...</span>
                            </div>

                            <!-- Auto Scroll Toggle -->
                            <button id="auto-scroll-toggle" 
                                    onclick="toggleAutoScroll()"
                                    class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors"
                                    title="Toggle auto-scroll">
                                Auto-scroll: ON
                            </button>

                            <!-- Scroll to Bottom Button -->
                            <button id="scroll-to-bottom" 
                                    onclick="scrollToBottom()"
                                    class="hidden px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors"
                                    title="Scroll to bottom">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </x-slot>
                
                <!-- Messages Container -->
                <div id="messages-container" 
                     class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900" 
                     style="scroll-behavior: smooth;">
                    
                    <!-- Loading Indicator -->
                    <div id="loading-messages" class="hidden text-center py-4">
                        <div class="inline-flex items-center space-x-2">
                            <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce"></div>
                            <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-4 h-4 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Loading messages...</p>
                    </div>
                    
                    <!-- Messages List -->
                    <div id="messages-list" class="space-y-4">
                        @foreach ($chatSession->messages as $message)
                            <div class="message-item flex {{ $message->sender_type === 'operator' ? 'justify-end' : 'justify-start' }}" 
                                 data-message-id="{{ $message->id }}">
                                <div class="max-w-xs lg:max-w-md">
                                    <div class="flex items-start gap-3 {{ $message->sender_type === 'operator' ? 'flex-row-reverse' : '' }}">
                                        <!-- Avatar -->
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                            {{ $message->sender_type === 'operator' 
                                                ? 'bg-blue-100 dark:bg-blue-900' 
                                                : ($message->sender_type === 'system' 
                                                    ? 'bg-gray-100 dark:bg-gray-700' 
                                                    : 'bg-green-100 dark:bg-green-900') }}">
                                            @if($message->sender_type === 'operator')
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            @elseif($message->sender_type === 'system')
                                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <!-- Message Content -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                                    {{ $message->sender_type === 'operator' ? ($message->sender->name ?? 'Operator') : 
                                                       ($message->sender_type === 'system' ? 'System' : $chatSession->getVisitorName()) }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-500">
                                                    {{ $message->created_at->format('H:i') }}
                                                </span>
                                            </div>
                                            <div class="p-3 rounded-lg text-sm
                                                {{ $message->sender_type === 'operator' 
                                                    ? 'bg-blue-600 text-white' 
                                                    : ($message->sender_type === 'system' 
                                                        ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 italic' 
                                                        : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600') }}">
                                                {!! nl2br(e($message->message)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                @if($chatSession->status !== 'closed')
                    <!-- Dynamic Reply Form Container -->
                    <div id="reply-form-container" class="border-t border-gray-200 dark:border-gray-700 p-4">
                        <!-- This content will be dynamically updated -->
                        <div id="reply-form-content">
                            @if($chatSession->assigned_operator_id === auth()->id())
                                <!-- Reply Form for Assigned Operator -->
                                <form id="reply-form" onsubmit="sendMessage(event)" class="space-y-3">
                                    @csrf
                                    <div class="flex gap-3">
                                        <div class="flex-1">
                                            <textarea 
                                                id="message-input"
                                                name="message" 
                                                placeholder="Type your message..." 
                                                rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none dark:bg-gray-700 dark:text-white"
                                                onkeydown="handleKeyDown(event)"
                                                oninput="handleTyping()"
                                                required></textarea>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <button type="submit" 
                                                    id="send-btn"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                            </button>
                                            <button type="button" 
                                                    onclick="showTemplates()"
                                                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors"
                                                    title="Quick Templates">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @elseif($chatSession->assigned_operator_id && $chatSession->assigned_operator_id !== auth()->id())
                                <!-- Not Assigned to Current User -->
                                <div id="not-assigned-message" class="text-center py-6">
                                    <div class="max-w-md mx-auto">
                                        <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Chat Not Assigned to You</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            This chat is currently assigned to <span id="current-operator-name" class="font-medium">{{ $chatSession->operator->name ?? 'another operator' }}</span>.
                                        </p>
                                        <button onclick="takeOverSession()" 
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Take Over Chat
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- Unassigned Session -->
                                <div id="unassigned-message" class="text-center py-6">
                                    <div class="max-w-md mx-auto">
                                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Unassigned Chat Session</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            This chat is waiting for an operator to take over.
                                        </p>
                                        <button onclick="takeOverSession()" 
                                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Assign to Me
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Closed Session -->
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4 text-center">
                        <div class="max-w-md mx-auto py-6">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Session Closed</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                This chat session has been closed and is no longer active.
                            </p>
                        </div>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <!-- Sidebar (1/4 width on large screens) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Visitor Information -->
            <x-admin.card title="Visitor Information">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $chatSession->getVisitorName() }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $chatSession->getVisitorEmail() ?? 'No email provided' }}</p>
                        </div>
                    </div>

                    @if($chatSession->visitor_info['phone'] ?? false)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->visitor_info['phone'] }}</span>
                        </div>
                    @endif
                    
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                        </svg>
                        <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($chatSession->source ?? 'website') }}</span>
                    </div>
                    
                    @if($chatSession->user)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">Registered User</span>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Session Details -->
            <x-admin.card title="Session Details">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                        <x-admin.badge 
                            :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')" 
                            id="sidebar-session-status">
                            {{ ucfirst($chatSession->status) }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Priority</span>
                        <x-admin.badge 
                            :type="$chatSession->priority === 'urgent' ? 'danger' : ($chatSession->priority === 'high' ? 'warning' : 'info')" 
                            id="sidebar-session-priority">
                            {{ ucfirst($chatSession->priority ?? 'normal') }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Assigned Operator</span>
                        <span id="assigned-operator" class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $chatSession->operator ? $chatSession->operator->name : 'Unassigned' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Started</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $chatSession->started_at->format('M d, H:i') }}
                        </span>
                    </div>
                    
                    @if($chatSession->ended_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Duration</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $chatSession->getDuration() ?? 0 }} minutes
                            </span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Messages</span>
                        <span id="message-count" class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $chatSession->messages->count() }}
                        </span>
                    </div>
                </div>
            </x-admin.card>

            <!-- Session Notes -->
            <x-admin.card title="Session Notes">
                <div class="space-y-3">
                    <textarea 
                        id="session-notes"
                        placeholder="Add notes about this session..."
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none dark:bg-gray-700 dark:text-white text-sm">{{ $chatSession->summary ?? '' }}</textarea>
                    <button onclick="saveNotes()" 
                            class="w-full px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                        Save Notes
                    </button>
                </div>
            </x-admin.card>

            @if($availableOperators->count() > 0)
                <!-- Transfer Session -->
                <x-admin.card title="Transfer Session">
                    <div class="space-y-3">
                        <select id="transfer-operator" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Select operator...</option>
                            @foreach($availableOperators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                            @endforeach
                        </select>
                        <button onclick="transferToOperator()" 
                                class="w-full px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm">
                            Transfer Session
                        </button>
                    </div>
                </x-admin.card>
            @endif
        </div>
    </div>

    <!-- Templates Modal -->
    <div id="templates-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Templates</h3>
                <button onclick="hideTemplates()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="templates-list" class="space-y-2 max-h-64 overflow-y-auto">
                <!-- Templates will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Debug meta tags first
        console.log('🔍 Meta tags check:', {
            'chat-session-id': document.querySelector('meta[name="chat-session-id"]')?.getAttribute('content'),
            'chat-session-db-id': document.querySelector('meta[name="chat-session-db-id"]')?.getAttribute('content'),
            'current-operator-id': document.querySelector('meta[name="current-operator-id"]')?.getAttribute('content'),
            'assigned-operator-id': document.querySelector('meta[name="assigned-operator-id"]')?.getAttribute('content'),
            'session-status': document.querySelector('meta[name="session-status"]')?.getAttribute('content')
        });

        // Global variables - Initialize immediately with more debugging
        let chatSession = {
            sessionId: document.querySelector('meta[name="chat-session-id"]')?.getAttribute('content') || '',
            sessionDbId: document.querySelector('meta[name="chat-session-db-id"]')?.getAttribute('content') || '',
            currentOperatorId: parseInt(document.querySelector('meta[name="current-operator-id"]')?.getAttribute('content') || '0'),
            assignedOperatorId: parseInt(document.querySelector('meta[name="assigned-operator-id"]')?.getAttribute('content') || '0') || null,
            status: document.querySelector('meta[name="session-status"]')?.getAttribute('content') || 'waiting',
            lastMessageId: null,
            autoScroll: true,
            pollInterval: null,
            isTyping: false,
            typingTimer: null
        };

        // Fallback: Extract from URL if meta tags fail
        if (!chatSession.sessionDbId) {
            const pathParts = window.location.pathname.split('/');
            const chatIndex = pathParts.indexOf('chat');
            if (chatIndex !== -1 && pathParts[chatIndex + 1]) {
                chatSession.sessionDbId = pathParts[chatIndex + 1];
                console.log('🔄 Extracted session DB ID from URL:', chatSession.sessionDbId);
            }
        }

        // Ensure chatSession is available globally immediately
        window.chatSession = chatSession;

        console.log('🚀 Chat Session globals initialized:', chatSession);
        console.log('🔗 URL path:', window.location.pathname);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Chat Session DOMContentLoaded:', window.chatSession);
            initializeChatSession();
            setupWebSocketListeners();
            startMessagePolling();
            scrollToBottom();
            updateTakeoverButtonVisibility();
            
            // Make functions globally available immediately
            setupGlobalFunctions();
        });

        // Initialize chat session
        function initializeChatSession() {
            // Get last message ID
            const messages = document.querySelectorAll('.message-item');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                chatSession.lastMessageId = parseInt(lastMessage.getAttribute('data-message-id'));
            }

            // Log initial session state for debugging
            console.log('🔍 Initial session state:', {
                sessionId: chatSession.sessionId,
                status: chatSession.status,
                assignedOperatorId: chatSession.assignedOperatorId,
                currentOperatorId: chatSession.currentOperatorId,
                isAssignedToCurrentUser: chatSession.assignedOperatorId === chatSession.currentOperatorId
            });

            // Setup scroll monitoring with null checks
            setupScrollMonitoring();

            // Focus message input if assigned to current user and status is active
            if (chatSession.assignedOperatorId === chatSession.currentOperatorId && 
                chatSession.status === 'active') {
                const messageInput = document.getElementById('message-input');
                if (messageInput) {
                    messageInput.focus();
                }
            }
        }

        // Separate scroll monitoring setup function
        function setupScrollMonitoring() {
            const container = document.getElementById('messages-container');
            if (!container) {
                console.warn('Messages container not found');
                return;
            }

            // Remove existing scroll listeners to prevent duplicates
            container.removeEventListener('scroll', handleScroll);
            
            // Add new scroll listener
            container.addEventListener('scroll', handleScroll);
            
            console.log('✅ Scroll monitoring setup complete');
        }

        // Scroll handler function with null checks
        function handleScroll() {
            const container = document.getElementById('messages-container');
            const scrollButton = document.getElementById('scroll-to-bottom');
            
            if (!container) {
                console.warn('Messages container not found in scroll handler');
                return;
            }
            
            try {
                const isAtBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 5;
                
                if (!isAtBottom && chatSession.autoScroll) {
                    chatSession.autoScroll = false;
                    updateAutoScrollToggle();
                }
                
                // Only update scroll button if it exists
                if (scrollButton) {
                    scrollButton.classList.toggle('hidden', isAtBottom);
                }
            } catch (error) {
                console.error('Scroll handler error:', error);
            }
        }

        // Setup WebSocket listeners
        function setupWebSocketListeners() {
            if (!window.Echo) {
                console.warn('Echo not available for chat session');
                updateConnectionStatus('error');
                return;
            }

            try {
                // Listen to session-specific channel
                window.Echo.channel(`chat-session.${chatSession.sessionId}`)
                    .listen('.message.sent', (e) => {
                        console.log('New message received:', e);
                        handleNewMessage(e.message);
                    })
                    .listen('.session.updated', (e) => {
                        console.log('Session updated:', e);
                        handleSessionUpdate(e);
                    })
                    .listen('.session.closed', (e) => {
                        console.log('Session closed:', e);
                        handleSessionClosed(e);
                    })
                    .listen('.operator.typing', (e) => {
                        console.log('Operator typing:', e);
                        if (e.sender_type !== 'operator' && e.is_typing) {
                            showVisitorTyping();
                        } else {
                            hideVisitorTyping();
                        }
                    });

                // Listen to admin chat channel for takeovers
                window.Echo.channel('admin-chat-notifications')
                    .listen('.session.transferred', (e) => {
                        console.log('Session transferred:', e);
                        if (e.session_id === chatSession.sessionId) {
                            handleSessionTransfer(e);
                        }
                    });

                updateConnectionStatus('connected');
            } catch (error) {
                console.error('WebSocket setup failed:', error);
                updateConnectionStatus('error');
            }
        }

        // Message polling (fallback for WebSocket)
        function startMessagePolling() {
            chatSession.pollInterval = setInterval(async function() {
                try {
                    const response = await fetch(`/admin/chat/${chatSession.sessionDbId}/poll-messages?last_id=${chatSession.lastMessageId || 0}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) throw new Error('Polling failed');

                    const data = await response.json();
                    
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(message => handleNewMessage(message));
                    }

                    if (data.session_update) {
                        handleSessionUpdate(data.session_update);
                    }
                } catch (error) {
                    console.error('Message polling error:', error);
                }
            }, 2000); // Poll every 2 seconds
        }

        function stopMessagePolling() {
            if (chatSession.pollInterval) {
                clearInterval(chatSession.pollInterval);
                chatSession.pollInterval = null;
            }
        }

        // Handle new message
        function handleNewMessage(message) {
            // Avoid duplicates
            if (document.querySelector(`[data-message-id="${message.id}"]`)) {
                return;
            }

            // Update last message ID
            if (message.id > chatSession.lastMessageId) {
                chatSession.lastMessageId = message.id;
            }

            // Add message to UI
            addMessageToUI(message);

            // Update message count
            updateMessageCount();

            // Play sound if message is from visitor
            if (message.sender_type === 'visitor') {
                playNotificationSound();
            }

            // Auto scroll if enabled
            if (chatSession.autoScroll) {
                scrollToBottom();
            }
        }

        // Add message to UI
        function addMessageToUI(message) {
            const messagesList = document.getElementById('messages-list');
            if (!messagesList) return;

            const messageHtml = createMessageHTML(message);
            messagesList.insertAdjacentHTML('beforeend', messageHtml);
        }

        // Create message HTML
        function createMessageHTML(message) {
            const isOperator = message.sender_type === 'operator';
            const isSystem = message.sender_type === 'system';
            
            const avatarColor = isOperator ? 'bg-blue-100 dark:bg-blue-900' : 
                               isSystem ? 'bg-gray-100 dark:bg-gray-700' : 
                               'bg-green-100 dark:bg-green-900';
            
            const avatarIcon = isOperator ? 
                '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>' :
                isSystem ? 
                '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>' :
                '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>';
            
            const avatarIconColor = isOperator ? 'text-blue-600 dark:text-blue-400' :
                                   isSystem ? 'text-gray-600 dark:text-gray-400' :
                                   'text-green-600 dark:text-green-400';
            
            const messageClass = isOperator ? 'bg-blue-600 text-white' :
                               isSystem ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 italic' :
                               'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600';
            
            const senderName = isOperator ? (message.sender_name || 'Operator') :
                             isSystem ? 'System' :
                             (message.sender_name || 'Visitor');
            
            const timestamp = new Date(message.created_at).toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });

            return `
                <div class="message-item flex ${isOperator ? 'justify-end' : 'justify-start'}" data-message-id="${message.id}">
                    <div class="max-w-xs lg:max-w-md">
                        <div class="flex items-start gap-3 ${isOperator ? 'flex-row-reverse' : ''}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${avatarColor}">
                                <svg class="w-4 h-4 ${avatarIconColor}" fill="currentColor" viewBox="0 0 20 20">
                                    ${avatarIcon}
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">${senderName}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-500">${timestamp}</span>
                                </div>
                                <div class="p-3 rounded-lg text-sm ${messageClass}">
                                    ${message.message.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Handle session updates
        function handleSessionUpdate(sessionData) {
            if (sessionData.status) {
                updateSessionStatus(sessionData.status);
                chatSession.status = sessionData.status;
            }

            if (sessionData.assigned_operator_id !== undefined) {
                chatSession.assignedOperatorId = sessionData.assigned_operator_id;
                updateAssignedOperator(sessionData.operator_name);
                updateTakeoverButtonVisibility();
            }
            updateReplyFormContainer({
                assigned_operator_id: sessionData.assigned_operator_id,
                operator_name: sessionData.operator_name,
                status: sessionData.status || window.chatSession?.status
            });

            if (sessionData.priority) {
                updateSessionPriority(sessionData.priority);
            }
        }

        // Dynamic form update functions
        function updateReplyFormContainer(sessionData) {
            const container = document.getElementById('reply-form-content');
            if (!container) return;

            const currentUserId = window.chatSession?.currentOperatorId;
            const assignedOperatorId = sessionData.assigned_operator_id;
            const operatorName = sessionData.operator_name;
            const status = sessionData.status;

            console.log('🔄 Updating reply form:', {
                currentUserId,
                assignedOperatorId, 
                operatorName,
                status
            });

            if (status === 'closed') {
                container.innerHTML = getClosedSessionHTML();
            } else if (assignedOperatorId === currentUserId) {
                container.innerHTML = getAssignedReplyFormHTML();
                setupReplyFormEventListeners();
            } else if (assignedOperatorId && assignedOperatorId !== currentUserId) {
                container.innerHTML = getNotAssignedHTML(operatorName);
            } else {
                container.innerHTML = getUnassignedHTML();
            }
        }

        function getAssignedReplyFormHTML() {
            return `
                <form id="reply-form" onsubmit="sendMessage(event)" class="space-y-3">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <textarea 
                                id="message-input"
                                name="message" 
                                placeholder="Type your message..." 
                                rows="2"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none dark:bg-gray-700 dark:text-white"
                                onkeydown="handleKeyDown(event)"
                                oninput="handleTyping()"
                                required></textarea>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button type="submit" 
                                    id="send-btn"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                            <button type="button" 
                                    onclick="showTemplates()"
                                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors"
                                    title="Quick Templates">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            `;
        }

        function getNotAssignedHTML(operatorName) {
            return `
                <div id="not-assigned-message" class="text-center py-6">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Chat Not Assigned to You</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            This chat is currently assigned to <span id="current-operator-name" class="font-medium">${operatorName || 'another operator'}</span>.
                        </p>
                        <button onclick="takeOverSession()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Take Over Chat
                        </button>
                    </div>
                </div>
            `;
        }

        function getUnassignedHTML() {
            return `
                <div id="unassigned-message" class="text-center py-6">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Unassigned Chat Session</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            This chat is waiting for an operator to take over.
                        </p>
                        <button onclick="takeOverSession()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Assign to Me
                        </button>
                    </div>
                </div>
            `;
        }

        function getClosedSessionHTML() {
            return `
                <div class="text-center py-6">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Session Closed</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            This chat session has been closed and is no longer active.
                        </p>
                    </div>
                </div>
            `;
        }

        function setupReplyFormEventListeners() {
            // Re-setup event listeners for the newly created form
            const messageInput = document.getElementById('message-input');
            if (messageInput) {
                // Focus the input
                messageInput.focus();
                
                // Re-setup event listeners if needed
                console.log('✅ Reply form event listeners setup complete');
            }
            
            // Re-setup scroll monitoring after form changes
            setupScrollMonitoring();
        }

        // Enhanced error handling for DOM operations
        function safeElementOperation(elementId, operation, operationName = 'operation') {
            try {
                const element = document.getElementById(elementId);
                if (element) {
                    return operation(element);
                } else {
                    console.warn(`Element '${elementId}' not found for ${operationName}`);
                    return null;
                }
            } catch (error) {
                console.error(`Error in ${operationName} for element '${elementId}':`, error);
                return null;
            }
        }
        function handleSessionTransfer(transferData) {
            chatSession.assignedOperatorId = transferData.new_operator_id;
            updateAssignedOperator(transferData.new_operator_name);
            updateTakeoverButtonVisibility();
            updateReplyFormContainer({
                assigned_operator_id: sessionData.assigned_operator_id,
                operator_name: sessionData.operator_name,
                status: sessionData.status || window.chatSession?.status
            });
            if (transferData.new_operator_id === chatSession.currentOperatorId) {
                showNotification('Session transferred to you', 'success');
                // Enable reply form
                const replyForm = document.getElementById('reply-form');
                if (replyForm) {
                    replyForm.style.display = 'block';
                }
            } else {
                showNotification('Session transferred to ' + transferData.new_operator_name, 'info');
                // Disable reply form
                const replyForm = document.getElementById('reply-form');
                if (replyForm) {
                    replyForm.style.display = 'none';
                }
            }
        }

        // Handle session closed
        function handleSessionClosed(sessionData) {
            chatSession.status = 'closed';
            updateSessionStatus('closed');
            stopMessagePolling();
            showNotification('Session has been closed', 'info');
            
            // Hide reply form
            const replyForm = document.getElementById('reply-form');
            if (replyForm) {
                replyForm.style.display = 'none';
            }
        }

        async function activateSession() {
            const activateBtn = document.getElementById('activate-btn');
            if (!activateBtn) return;

            // Multiple fallback methods to get session DB ID
            let sessionDbId = window.chatSession?.sessionDbId || 
                             chatSession?.sessionDbId ||
                             document.querySelector('meta[name="chat-session-db-id"]')?.getAttribute('content');
            
            // Extract from URL as final fallback
            if (!sessionDbId) {
                const pathParts = window.location.pathname.split('/');
                const chatIndex = pathParts.indexOf('chat');
                if (chatIndex !== -1 && pathParts[chatIndex + 1]) {
                    sessionDbId = pathParts[chatIndex + 1];
                }
            }
            
            console.log('🔍 Activate - Session DB ID:', sessionDbId);
            console.log('🔍 Activate - Window location:', window.location.pathname);
            
            if (!sessionDbId) {
                showNotification('Session ID not found - please refresh the page', 'error');
                console.error('❌ Session DB ID not found in any fallback method');
                return;
            }

            try {
                activateBtn.disabled = true;
                activateBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline"></div>Activating...';

                // First try the takeover endpoint since we know it works
                console.log('🔗 Activate request URL:', `/admin/chat/${sessionDbId}/take-over`);
                
                const response = await fetch(`/admin/chat/${sessionDbId}/take-over`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        note: 'Session activated from chat view'
                    })
                });

                if (!response.ok) throw new Error('Activation failed');

                const data = await response.json();
                
                if (data.success) {
                    if (window.chatSession) {
                        window.chatSession.status = data.session.status;
                    }
                    if (chatSession) {
                        chatSession.status = data.session.status;
                    }
                    updateSessionStatus(data.session.status);
                    showNotification(`Session activated successfully! Status: ${data.session.status}`, 'success');
                    updateReplyFormContainer({
                        assigned_operator_id: chatSession.currentOperatorId,
                        operator_name: data.session.operator_name,
                        status: data.session.status
                    });
                    // Hide activate button and show reply form
                    activateBtn.style.display = 'none';
                    const replyForm = document.getElementById('reply-form');
                    if (replyForm) {
                        replyForm.style.display = 'block';
                        const messageInput = document.getElementById('message-input');
                        if (messageInput) {
                            messageInput.focus();
                        }
                    }
                } else {
                    throw new Error(data.message || 'Failed to activate session');
                }
            } catch (error) {
                console.error('Activate session failed:', error);
                showNotification('Failed to activate session: ' + error.message, 'error');
            } finally {
                if (activateBtn && activateBtn.style.display !== 'none') {
                    activateBtn.disabled = false;
                    activateBtn.innerHTML = `
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Activate Session
                    `;
                }
            }
        }
        async function takeOverSession() {
            const takeoverBtn = document.getElementById('takeover-btn');
            if (!takeoverBtn) return;

            try {
                takeoverBtn.disabled = true;
                takeoverBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2 inline"></div>Taking over...';

                const response = await fetch(`/admin/chat/${chatSession.sessionDbId}/take-over`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        note: 'Taken over from chat session view'
                    })
                });

                if (!response.ok) throw new Error('Takeover failed');

                const data = await response.json();
                
                if (data.success) {
                    // Update session assignment
                    chatSession.assignedOperatorId = chatSession.currentOperatorId;
                    chatSession.status = data.session.status; // Should now be 'active'
                    
                    // Update UI immediately
                    updateSessionStatus(data.session.status);
                    updateAssignedOperator(data.session.operator_name);
                    updateTakeoverButtonVisibility();
                    
                    // Show reply form
                    updateReplyFormContainer({
                        assigned_operator_id: chatSession.currentOperatorId,
                        operator_name: data.session.operator_name,
                        status: data.session.status
                    });
                    
                    showNotification('Session taken over successfully! Status changed to active.', 'success');
                    
                    console.log('Takeover successful:', {
                        old_status: 'waiting',
                        new_status: data.session.status,
                        assigned_to: data.session.operator_name
                    });
                } else {
                    throw new Error(data.message || 'Takeover failed');
                }
            } catch (error) {
                console.error('Takeover failed:', error);
                showNotification('Failed to take over session: ' + error.message, 'error');
            } finally {
                if (takeoverBtn) {
                    takeoverBtn.disabled = false;
                    takeoverBtn.innerHTML = `
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Take Over Chat
                    `;
                }
            }
        }

        // Send message
        async function sendMessage(event) {
            event.preventDefault();
            
            const messageInput = document.getElementById('message-input');
            const sendBtn = document.getElementById('send-btn');
            const message = messageInput.value.trim();
            
            if (!message) return;

            try {
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

                const response = await fetch(`/admin/chat/${chatSession.sessionDbId}/reply`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                });

                if (!response.ok) throw new Error('Failed to send message');

                const data = await response.json();
                
                if (data.success) {
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    stopTyping();
                    
                    // Message will be added via WebSocket or polling
                } else {
                    throw new Error(data.message || 'Failed to send message');
                }
            } catch (error) {
                console.error('Send message failed:', error);
                showNotification('Failed to send message', 'error');
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                `;
            }
        }

        // Handle keyboard shortcuts
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage(event);
            }
        }

        // Typing indicators
        function handleTyping() {
            if (!chatSession.isTyping) {
                chatSession.isTyping = true;
                sendTypingIndicator(true);
            }

            clearTimeout(chatSession.typingTimer);
            chatSession.typingTimer = setTimeout(() => {
                stopTyping();
            }, 3000);
        }

        function stopTyping() {
            if (chatSession.isTyping) {
                chatSession.isTyping = false;
                sendTypingIndicator(false);
            }
            clearTimeout(chatSession.typingTimer);
        }

        async function sendTypingIndicator(isTyping) {
            try {
                await fetch(`/admin/chat/${chatSession.sessionDbId}/typing`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ is_typing: isTyping })
                });
            } catch (error) {
                console.error('Send typing indicator failed:', error);
            }
        }

        function showVisitorTyping() {
            const typingIndicator = document.getElementById('visitor-typing');
            if (typingIndicator) {
                typingIndicator.classList.remove('hidden');
            }
        }

        function hideVisitorTyping() {
            const typingIndicator = document.getElementById('visitor-typing');
            if (typingIndicator) {
                typingIndicator.classList.add('hidden');
            }
        }

        // UI Updates with null checks
        function updateSessionStatus(status) {
            safeElementOperation('session-status-badge', (badge) => {
                badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                
                // Update badge colors
                badge.className = badge.className.replace(/bg-\w+-\d+|text-\w+-\d+/g, '');
                
                if (status === 'active') {
                    badge.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400');
                } else if (status === 'waiting') {
                    badge.classList.add('bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400');
                } else {
                    badge.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400');
                }
            }, 'updateSessionStatus');

            // Also update sidebar status badge
            safeElementOperation('sidebar-session-status', (badge) => {
                badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                // Apply same color logic
                badge.className = badge.className.replace(/bg-\w+-\d+|text-\w+-\d+/g, '');
                if (status === 'active') {
                    badge.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400');
                } else if (status === 'waiting') {
                    badge.classList.add('bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400');
                } else {
                    badge.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400');
                }
            }, 'updateSidebarSessionStatus');
        }

        // Enhanced UI Updates to include main content area
        function updateAssignedOperator(operatorName) {
            // Update sidebar assigned operator
            safeElementOperation('assigned-operator', (element) => {
                element.textContent = operatorName || 'Unassigned';
            }, 'updateAssignedOperator');

            // Update current operator name in dynamic form messages
            safeElementOperation('current-operator-name', (element) => {
                element.textContent = operatorName || 'another operator';
            }, 'updateCurrentOperatorName');

            // Update main content area - add new system message about assignment change
            if (operatorName && operatorName !== 'Unassigned') {
                addSystemMessageToUI({
                    message: `Chat now assigned to ${operatorName}`,
                    created_at: new Date().toISOString(),
                    id: Date.now() // temporary ID for UI
                });
            }
        }

        // Add system message to chat UI
        function addSystemMessageToUI(messageData) {
            const messagesList = document.getElementById('messages-list');
            if (!messagesList) return;

            const systemMessageHtml = createSystemMessageHTML(messageData);
            messagesList.insertAdjacentHTML('beforeend', systemMessageHtml);

            // Auto scroll if enabled
            if (chatSession.autoScroll) {
                scrollToBottom();
            }

            // Update message count
            updateMessageCount();
        }

        // Create system message HTML
        function createSystemMessageHTML(message) {
            const timestamp = new Date(message.created_at).toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });

            return `
                <div class="message-item flex justify-center" data-message-id="${message.id}">
                    <div class="max-w-xs lg:max-w-md">
                        <div class="flex items-center justify-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-gray-700">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 text-center">
                                <div class="flex items-center gap-2 mb-1 justify-center">
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">System</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-500">${timestamp}</span>
                                </div>
                                <div class="p-3 rounded-lg text-sm bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 italic inline-block">
                                    ${message.message}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Enhanced session update handler
        function handleSessionUpdate(sessionData) {
            console.log('🔄 Handling session update:', sessionData);
            
            if (sessionData.status) {
                updateSessionStatus(sessionData.status);
                if (window.chatSession) window.chatSession.status = sessionData.status;
                if (chatSession) chatSession.status = sessionData.status;
            }

            if (sessionData.assigned_operator_id !== undefined) {
                const oldOperatorId = window.chatSession?.assignedOperatorId;
                const newOperatorId = sessionData.assigned_operator_id;
                const operatorName = sessionData.operator_name;
                
                if (window.chatSession) window.chatSession.assignedOperatorId = newOperatorId;
                if (chatSession) chatSession.assignedOperatorId = newOperatorId;
                
                // Update all UI elements including main content area
                updateAssignedOperator(operatorName);
                updateTakeoverButtonVisibility();
                
                // Update the reply form dynamically
                updateReplyFormContainer({
                    assigned_operator_id: newOperatorId,
                    operator_name: operatorName,
                    status: sessionData.status || window.chatSession?.status
                });
                
                console.log('🔄 Operator assignment changed:', {
                    from: oldOperatorId,
                    to: newOperatorId,
                    operator_name: operatorName
                });
            }

            if (sessionData.priority) {
                updateSessionPriority(sessionData.priority);
            }
        }

        // Enhanced transfer handler
        function handleSessionTransfer(transferData) {
            console.log('🔄 Handling session transfer:', transferData);
            
            const oldOperatorId = window.chatSession?.assignedOperatorId;
            const newOperatorId = transferData.new_operator_id;
            const newOperatorName = transferData.new_operator_name;
            
            if (window.chatSession) window.chatSession.assignedOperatorId = newOperatorId;
            if (chatSession) chatSession.assignedOperatorId = newOperatorId;
            
            // Update all UI elements including main content area
            updateAssignedOperator(newOperatorName);
            updateTakeoverButtonVisibility();
            
            // Update the reply form dynamically
            updateReplyFormContainer({
                assigned_operator_id: newOperatorId,
                operator_name: newOperatorName,
                status: window.chatSession?.status || 'active'
            });

            // Add visual feedback about the transfer
            if (newOperatorId === window.chatSession?.currentOperatorId) {
                showNotification('Session transferred to you', 'success');
                addSystemMessageToUI({
                    message: `You have taken over this chat session`,
                    created_at: new Date().toISOString(),
                    id: Date.now()
                });
            } else {
                showNotification('Session transferred to ' + newOperatorName, 'info');
                addSystemMessageToUI({
                    message: `Chat transferred to ${newOperatorName}`,
                    created_at: new Date().toISOString(),
                    id: Date.now()
                });
            }
        }

        function updateSessionPriority(priority) {
            safeElementOperation('sidebar-session-priority', (element) => {
                element.textContent = priority.charAt(0).toUpperCase() + priority.slice(1);
                
                // Update priority colors
                element.className = element.className.replace(/bg-\w+-\d+|text-\w+-\d+/g, '');
                
                if (priority === 'urgent') {
                    element.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400');
                } else if (priority === 'high') {
                    element.classList.add('bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400');
                } else {
                    element.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
                }
            }, 'updateSessionPriority');
        }

        function updateMessageCount() {
            safeElementOperation('message-count', (element) => {
                const messages = document.querySelectorAll('.message-item');
                element.textContent = messages.length;
            }, 'updateMessageCount');
        }

        function updateTakeoverButtonVisibility() {
            safeElementOperation('takeover-btn', (takeoverBtn) => {
                // Show takeover button only if:
                // 1. Session is not assigned to current user
                // 2. Session is not closed
                // 3. Session is in 'waiting' status OR assigned to someone else
                const shouldShow = (chatSession.assignedOperatorId !== chatSession.currentOperatorId || 
                                   chatSession.assignedOperatorId === null) && 
                                 chatSession.status !== 'closed';
                
                takeoverBtn.style.display = shouldShow ? 'inline-flex' : 'none';
                
                console.log('Takeover button visibility:', {
                    shouldShow,
                    assignedOperatorId: chatSession.assignedOperatorId,
                    currentOperatorId: chatSession.currentOperatorId,
                    status: chatSession.status
                });
            }, 'updateTakeoverButtonVisibility');
        }

        function updateConnectionStatus(status) {
            safeElementOperation('connection-status', (connectionElement) => {
                const statusConfig = {
                    'connected': {
                        class: 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300',
                        dot: 'bg-green-500 animate-pulse',
                        text: 'Connected'
                    },
                    'connecting': {
                        class: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300',
                        dot: 'bg-yellow-500 animate-pulse',
                        text: 'Connecting...'
                    },
                    'error': {
                        class: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300',
                        dot: 'bg-red-500',
                        text: 'Connection Error'
                    }
                };

                const config = statusConfig[status] || statusConfig.error;
                
                connectionElement.className = `flex items-center gap-2 px-3 py-2 rounded-lg ${config.class}`;
                connectionElement.innerHTML = `
                    <div class="w-2 h-2 rounded-full ${config.dot}"></div>
                    <span class="text-sm font-medium">${config.text}</span>
                `;
            }, 'updateConnectionStatus');
        }

        // Scroll functionality with null checks
        function scrollToBottom() {
            const container = document.getElementById('messages-container');
            if (container) {
                try {
                    container.scrollTop = container.scrollHeight;
                    chatSession.autoScroll = true;
                    updateAutoScrollToggle();
                } catch (error) {
                    console.error('Scroll to bottom error:', error);
                }
            } else {
                console.warn('Messages container not found for scrollToBottom');
            }
        }

        function toggleAutoScroll() {
            chatSession.autoScroll = !chatSession.autoScroll;
            updateAutoScrollToggle();
            
            if (chatSession.autoScroll) {
                scrollToBottom();
            }
        }

        function updateAutoScrollToggle() {
            const toggle = document.getElementById('auto-scroll-toggle');
            if (toggle) {
                toggle.textContent = `Auto-scroll: ${chatSession.autoScroll ? 'ON' : 'OFF'}`;
                toggle.className = chatSession.autoScroll 
                    ? 'px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors'
                    : 'px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors';
            }
        }

        // Session Actions
        async function closeSession() {
            if (!confirm('Are you sure you want to close this chat session?')) {
                return;
            }

            // Use window.chatSession to ensure access
            const sessionDbId = window.chatSession?.sessionDbId || chatSession?.sessionDbId;
            
            if (!sessionDbId) {
                showNotification('Session ID not found', 'error');
                return;
            }

            try {
                const response = await fetch(`/admin/chat/${sessionDbId}/close-session`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) throw new Error('Failed to close session');

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Session closed successfully', 'success');
                    if (window.chatSession) {
                        window.chatSession.status = 'closed';
                    }
                    if (chatSession) {
                        chatSession.status = 'closed';
                    }
                    updateSessionStatus('closed');
                    
                    // Disable reply form
                    const replyForm = document.getElementById('reply-form');
                    if (replyForm) {
                        replyForm.style.display = 'none';
                    }
                    
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.chat.index") }}';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to close session');
                }
            } catch (error) {
                console.error('Close session failed:', error);
                showNotification('Failed to close session', 'error');
            }
        }

        async function transferToOperator() {
            const select = document.getElementById('transfer-operator');
            const operatorId = select.value;
            
            if (!operatorId) {
                showNotification('Please select an operator', 'warning');
                return;
            }

            // Multiple fallback methods to get session DB ID
            let sessionDbId = window.chatSession?.sessionDbId || 
                             chatSession?.sessionDbId ||
                             document.querySelector('meta[name="chat-session-db-id"]')?.getAttribute('content');
            
            // Extract from URL as final fallback
            if (!sessionDbId) {
                const pathParts = window.location.pathname.split('/');
                const chatIndex = pathParts.indexOf('chat');
                if (chatIndex !== -1 && pathParts[chatIndex + 1]) {
                    sessionDbId = pathParts[chatIndex + 1];
                }
            }
            
            if (!sessionDbId) {
                showNotification('Session ID not found - please refresh the page', 'error');
                return;
            }

            try {
                const response = await fetch(`/admin/chat/${sessionDbId}/transfer`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'  // Ensures Laravel recognizes as AJAX
                    },
                    body: JSON.stringify({ 
                        operator_id: operatorId,
                        note: 'Transferred from chat session view'
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Session transferred successfully!', 'success');
                    select.value = '';
                    
                    // Update local session state if transferred away from current user
                    if (data.session.assigned_operator_id !== window.chatSession?.currentOperatorId) {
                        // Session transferred to someone else
                        if (window.chatSession) {
                            window.chatSession.assignedOperatorId = data.session.assigned_operator_id;
                        }
                        updateAssignedOperator(data.session.operator_name);
                        updateTakeoverButtonVisibility();
                        
                        updateReplyFormContainer({
                            assigned_operator_id: data.session.assigned_operator_id,
                            operator_name: data.session.operator_name,
                            status: data.session.status
                        });
                        // Hide reply form since no longer assigned to current user
                        const replyForm = document.getElementById('reply-form');
                        if (replyForm) {
                            replyForm.style.display = 'none';
                        }
                    }
                    
                    console.log('✅ Transfer successful:', {
                        session_id: data.session.session_id,
                        new_operator: data.session.operator_name,
                        assigned_to_id: data.session.assigned_operator_id
                    });
                } else {
                    throw new Error(data.message || 'Transfer failed');
                }
            } catch (error) {
                console.error('❌ Transfer failed:', error);
                showNotification('Failed to transfer session: ' + error.message, 'error');
            }
        }

        async function saveNotes() {
            const notesTextarea = document.getElementById('session-notes');
            const notes = notesTextarea.value;
            
            // Use window.chatSession to ensure access
            const sessionDbId = window.chatSession?.sessionDbId || chatSession?.sessionDbId;
            
            if (!sessionDbId) {
                showNotification('Session ID not found', 'error');
                return;
            }
            
            try {
                const response = await fetch(`/admin/chat/${sessionDbId}/notes`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ summary: notes })
                });
                
                if (!response.ok) throw new Error('Failed to save notes');

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Notes saved successfully', 'success');
                } else {
                    throw new Error(data.message || 'Failed to save notes');
                }
            } catch (error) {
                console.error('Save notes failed:', error);
                showNotification('Failed to save notes', 'error');
            }
        }

        // Templates functionality
        async function showTemplates() {
            const modal = document.getElementById('templates-modal');
            const templatesList = document.getElementById('templates-list');
            
            try {
                const response = await fetch('/admin/chat/quick-templates', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) throw new Error('Failed to load templates');

                const data = await response.json();
                
                if (data.templates && data.templates.length > 0) {
                    templatesList.innerHTML = data.templates.map(template => `
                        <button onclick="useTemplate('${template.content.replace(/'/g, "\\'")}'); hideTemplates();"
                                class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="font-medium text-gray-900 dark:text-white">${template.name}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">${template.content.substring(0, 100)}...</div>
                        </button>
                    `).join('');
                } else {
                    templatesList.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No templates available</p>';
                }
                
                modal.classList.remove('hidden');
            } catch (error) {
                console.error('Load templates failed:', error);
                showNotification('Failed to load templates', 'error');
            }
        }

        function hideTemplates() {
            const modal = document.getElementById('templates-modal');
            modal.classList.add('hidden');
        }

        function useTemplate(content) {
            const messageInput = document.getElementById('message-input');
            if (messageInput) {
                messageInput.value = content;
                messageInput.focus();
            }
        }

        // Export transcript
        async function exportTranscript() {
            try {
                showNotification('Preparing transcript...', 'info');
                
                const response = await fetch(`/admin/chat/${chatSession.sessionDbId}/export`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (!response.ok) throw new Error('Export failed');

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `chat_transcript_${chatSession.sessionId}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                showNotification('Transcript exported successfully', 'success');
            } catch (error) {
                console.error('Export failed:', error);
                showNotification('Failed to export transcript', 'error');
            }
        }

        // Utility functions
        function playNotificationSound() {
            try {
                const audio = new Audio('/sounds/notification.mp3');
                audio.volume = 0.3;
                audio.play().catch(e => console.log('Sound play failed:', e));
            } catch (error) {
                console.log('Sound notification failed:', error);
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
            
            const typeColors = {
                'success': 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800',
                'error': 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                'warning': 'bg-yellow-100 text-yellow-800 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800',
                'info': 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800'
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
            stopMessagePolling();
            stopTyping();
            
            if (window.Echo) {
                try {
                    window.Echo.leaveChannel(`chat-session.${chatSession.sessionId}`);
                    window.Echo.leaveChannel('admin-chat-notifications');
                } catch (error) {
                    console.log('Error leaving channels:', error);
                }
            }
        });

        // Setup global functions
        function setupGlobalFunctions() {
            // Global functions for Alpine.js dropdowns and inline onclick handlers
            window.takeOverSession = takeOverSession;
            window.activateSession = activateSession;
            window.transferSession = transferToOperator;
            window.closeSession = closeSession;
            window.updatePriority = function() {
                // This would open a priority selection modal
                const priorities = ['low', 'normal', 'high', 'urgent'];
                const currentPriority = window.chatSession?.priority || 'normal';
                
                const newPriority = prompt(`Current priority: ${currentPriority}\nEnter new priority (${priorities.join(', ')}):`, currentPriority);
                
                if (newPriority && priorities.includes(newPriority.toLowerCase())) {
                    updateSessionPriority(newPriority.toLowerCase());
                    showNotification(`Priority updated to ${newPriority}`, 'success');
                }
            };
            window.exportTranscript = exportTranscript;
            window.scrollToBottom = scrollToBottom;
            window.toggleAutoScroll = toggleAutoScroll;
            window.sendMessage = sendMessage;
            window.handleKeyDown = handleKeyDown;
            window.handleTyping = handleTyping;
            window.showTemplates = showTemplates;
            window.hideTemplates = hideTemplates;
            window.useTemplate = useTemplate;
            window.saveNotes = saveNotes;
            window.transferToOperator = transferToOperator;
            
            console.log('✅ Global functions setup complete');
        }

        // Debug helper function
        window.debugSessionInfo = function() {
            console.log('🐛 Current Session Debug Info:');
            console.log('=====================================');
            console.log('🔗 URL:', window.location.pathname);
            console.log('📝 Meta tags:', {
                'chat-session-id': document.querySelector('meta[name="chat-session-id"]')?.getAttribute('content'),
                'chat-session-db-id': document.querySelector('meta[name="chat-session-db-id"]')?.getAttribute('content'),
                'current-operator-id': document.querySelector('meta[name="current-operator-id"]')?.getAttribute('content'),
                'assigned-operator-id': document.querySelector('meta[name="assigned-operator-id"]')?.getAttribute('content'),
                'session-status': document.querySelector('meta[name="session-status"]')?.getAttribute('content')
            });
            console.log('🏠 window.chatSession:', window.chatSession);
            console.log('📦 local chatSession:', typeof chatSession !== 'undefined' ? chatSession : 'undefined');
            
            // Extract session ID from URL
            const pathParts = window.location.pathname.split('/');
            const chatIndex = pathParts.indexOf('chat');
            const urlSessionId = chatIndex !== -1 && pathParts[chatIndex + 1] ? pathParts[chatIndex + 1] : null;
            console.log('🔍 URL extracted session ID:', urlSessionId);
            console.log('=====================================');
        };

        // Also setup functions immediately for early access
        setupGlobalFunctions();

        console.log('💡 Debug tip: Run debugSessionInfo() in console to check session state');
    </script>
</x-layouts.admin>