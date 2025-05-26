{{-- resources/views/emails/offline-chat-message.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline Chat Message</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; }
        .message-box { background: white; padding: 15px; border-left: 4px solid #3b82f6; margin: 15px 0; }
        .visitor-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Offline Chat Message</h1>
            <p>New message received while team was offline</p>
        </div>
        
        <div class="content">
            <h2>Visitor Information</h2>
            <div class="visitor-info">
                <p><strong>Name:</strong> {{ $visitorName }}</p>
                @if($visitorEmail)
                    <p><strong>Email:</strong> {{ $visitorEmail }}</p>
                @endif
                <p><strong>Session ID:</strong> {{ $session->session_id }}</p>
                <p><strong>Started:</strong> {{ $session->started_at->format('M j, Y H:i:s') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($session->status) }}</p>
            </div>
            
            <h3>Message:</h3>
            <div class="message-box">
                {{ $visitorMessage }}
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $sessionUrl }}" class="btn">View Chat Session</a>
            </p>
            
            <p><strong>Action Required:</strong></p>
            <ul>
                <li>Review the chat session</li>
                <li>Respond to the visitor within 2 hours</li>
                <li>Follow up via email if needed</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}</p>
            <p>Please respond promptly to maintain excellent customer service</p>
        </div>
    </div>
</body>
</html>

{{-- resources/views/admin/chat/index.blade.php --}}
<x-layouts.admin title="Live Chat Dashboard">
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
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-admin.stat-card 
            title="Active Chats" 
            :value="$statistics['active_sessions']"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>'
            color="blue"
        />
        
        <x-admin.stat-card 
            title="Waiting Queue" 
            :value="$statistics['waiting_sessions']"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            color="yellow"
        />
        
        <x-admin.stat-card 
            title="Online Operators" 
            :value="$statistics['online_operators']"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'
            color="green"
        />
        
        <x-admin.stat-card 
            title="Today's Chats" 
            :value="$statistics['today_sessions']"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
            color="purple"
        />
    </div>

    <!-- Active Chat Sessions -->
    @if($activeSessions->count() > 0)
        <x-admin.card title="Active Chat Sessions" class="mb-6">
            <div class="space-y-4">
                @foreach($activeSessions as $session)
                    <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-medium">
                                {{ substr($session->getVisitorName(), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $session->getVisitorName() }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($session->getVisitorEmail())
                                        {{ $session->getVisitorEmail() }} •
                                    @endif
                                    Started {{ $session->started_at->diffForHumans() }}
                                </p>
                                @if($session->latestMessage)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        "{{ \Illuminate\Support\Str::limit($session->latestMessage->message, 50) }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <x-admin.badge type="success">Active</x-admin.badge>
                            <x-admin.button size="sm" href="{{ route('admin.chat.show', $session) }}">
                                View Chat
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
            <div class="space-y-4">
                @foreach($waitingSessions as $session)
                    <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-medium">
                                {{ substr($session->getVisitorName(), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $session->getVisitorName() }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($session->getVisitorEmail())
                                        {{ $session->getVisitorEmail() }} •
                                    @endif
                                    Waiting {{ $session->started_at->diffForHumans() }}
                                </p>
                                @if($session->latestMessage)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        "{{ \Illuminate\Support\Str::limit($session->latestMessage->message, 50) }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <x-admin.badge type="warning">Waiting</x-admin.badge>
                            <x-admin.button size="sm" href="{{ route('admin.chat.show', $session) }}">
                                Respond
                            </x-admin.button>
                        </div>
                    </div>
                @endforeach
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
            </div>
        </x-admin.card>
    @endif

    @push('scripts')
    <script>
        // Auto-refresh dashboard every 30 seconds
        setInterval(function() {
            window.location.reload();
        }, 30000);
    </script>
    @endpush
</x-layouts.admin>

{{-- resources/views/admin/chat/show.blade.php --}}
<x-layouts.admin title="Chat Session">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Live Chat' => route('admin.chat.index'), 
        'Chat with ' . $chatSession->getVisitorName() => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Chat with {{ $chatSession->getVisitorName() }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Session: {{ $chatSession->session_id }} • 
                Started {{ $chatSession->started_at->diffForHumans() }}
                @if($chatSession->ended_at)
                    • Ended {{ $chatSession->ended_at->diffForHumans() }}
                @endif
            </p>
        </div>
        <div class="flex gap-3">
            <x-admin.badge :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')">
                {{ ucfirst($chatSession->status) }}
            </x-admin.badge>
            <x-admin.button color="light" href="{{ route('admin.chat.index') }}">
                Back to Dashboard
            </x-admin.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Chat Messages -->
        <div class="lg:col-span-3">
            <x-admin.card noPadding>
                <x-slot name="title">Chat Messages</x-slot>
                
                <!-- Messages Container -->
                <div class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-800">
                    @forelse($chatSession->messages as $message)
                        <div class="flex {{ $message->isFromVisitor() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->isFromVisitor() ? 'bg-blue-600 text-white' : ($message->isFromBot() ? 'bg-white border border-gray-200' : 'bg-green-100 text-green-800') }}">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-xs">
                                        {{ $message->isFromVisitor() ? '👤' : ($message->isFromBot() ? '🤖' : '👨‍💼') }}
                                    </span>
                                    <span class="text-xs font-medium opacity-75">{{ $message->getSenderName() }}</span>
                                    <span class="text-xs opacity-50">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                                <div class="text-sm whitespace-pre-wrap">{{ $message->message }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            No messages yet
                        </div>
                    @endforelse
                </div>
            </x-admin.card>
        </div>

        <!-- Session Info Sidebar -->
        <div class="space-y-6">
            <!-- Visitor Information -->
            <x-admin.card title="Visitor Information">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $chatSession->getVisitorName() }}</p>
                    </div>
                    
                    @if($chatSession->getVisitorEmail())
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $chatSession->getVisitorEmail() }}</p>
                        </div>
                    @endif
                    
                    @if($chatSession->visitor_info && isset($chatSession->visitor_info['phone']))
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $chatSession->visitor_info['phone'] }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Source</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ ucfirst($chatSession->source) }}</p>
                    </div>
                </div>
            </x-admin.card>

            <!-- Session Details -->
            <x-admin.card title="Session Details">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                        <x-admin.badge :type="$chatSession->status === 'active' ? 'success' : ($chatSession->status === 'waiting' ? 'warning' : 'danger')">
                            {{ ucfirst($chatSession->status) }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Priority</span>
                        <x-admin.badge :type="$chatSession->priority === 'urgent' ? 'danger' : ($chatSession->priority === 'high' ? 'warning' : 'info')">
                            {{ ucfirst($chatSession->priority) }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Started</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->started_at->format('M j, H:i') }}</span>
                    </div>
                    
                    @if($chatSession->ended_at)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ended</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->ended_at->format('M j, H:i') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Duration</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->getDuration() }} min</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Messages</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $chatSession->messages->count() }}</span>
                    </div>
                </div>
            </x-admin.card>

            <!-- Quick Actions -->
            @if($chatSession->status !== 'closed')
                <x-admin.card title="Quick Actions">
                    <div class="space-y-3">
                        @if($chatSession->getVisitorEmail())
                            <x-admin.button color="primary" class="w-full" 
                                href="mailto:{{ $chatSession->getVisitorEmail() }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Send Email
                            </x-admin.button>
                        @endif
                        
                        <form action="{{ route('admin.chat.close', $chatSession) }}" method="POST" class="w-full">
                            @csrf
                            <x-admin.button type="submit" color="warning" class="w-full"
                                onclick="return confirm('Are you sure you want to close this chat session?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Close Chat
                            </x-admin.button>
                        </form>
                    </div>
                </x-admin.card>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-scroll to bottom of messages
        const messagesContainer = document.querySelector('.overflow-y-auto');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Auto-refresh every 10 seconds for active chats
        @if($chatSession->status === 'active')
            setInterval(function() {
                window.location.reload();
            }, 10000);
        @endif
    </script>
    @endpush
</x-layouts.admin>

{{-- Add this to your main layout file to include the chat widget --}}
{{-- resources/views/layouts/app.blade.php or your main layout --}}

{{-- Add this before closing </body> tag --}}
@if(!request()->is('admin/*')) {{-- Don't show on admin pages --}}
    <x-chat-widget />
@endif

{{-- resources/views/admin/chat/settings.blade.php --}}
<x-layouts.admin title="Chat Settings">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Live Chat' => route('admin.chat.index'), 
        'Settings' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Settings</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure live chat functionality and responses</p>
        </div>
    </div>

    <form action="{{ route('admin.chat.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- General Settings -->
        <x-admin.form-section title="General Settings" description="Basic chat widget configuration">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.checkbox
                    name="chat_enabled"
                    label="Enable Live Chat"
                    :checked="old('chat_enabled', settings('chat_enabled', true))"
                    helper="Show the chat widget to website visitors"
                />
                
                <x-admin.select
                    name="chat_position"
                    label="Widget Position"
                    :options="['bottom-right' => 'Bottom Right', 'bottom-left' => 'Bottom Left']"
                    :selected="old('chat_position', settings('chat_position', 'bottom-right'))"
                />
                
                <x-admin.input
                    name="chat_greeting"
                    label="Default Greeting"
                    :value="old('chat_greeting', settings('chat_greeting', 'Hello! How can we help you today?'))"
                    helper="First message visitors see when starting a chat"
                />
                
                <x-admin.input
                    name="offline_message"
                    label="Offline Message"
                    :value="old('offline_message', settings('offline_message', 'We are currently offline. Please leave a message!'))"
                    helper="Message shown when no operators are available"
                />
            </div>
        </x-admin.form-section>

        <!-- Auto-Response Settings -->
        <x-admin.form-section title="Auto-Response Settings" description="Configure automated responses">
            <div class="space-y-4">
                <x-admin.checkbox
                    name="auto_response_enabled"
                    label="Enable Auto-Responses"
                    :checked="old('auto_response_enabled', settings('auto_response_enabled', true))"
                    helper="Automatically respond to common questions"
                />
                
                <x-admin.input
                    type="number"
                    name="response_delay"
                    label="Response Delay (seconds)"
                    :value="old('response_delay', settings('response_delay', 2))"
                    min="0"
                    max="10"
                    helper="Delay before sending auto-responses to seem more natural"
                />
            </div>
        </x-admin.form-section>

        <!-- Notification Settings -->
        <x-admin.form-section title="Notification Settings" description="Configure how you receive chat notifications">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.checkbox
                    name="email_notifications"
                    label="Email Notifications"
                    :checked="old('email_notifications', settings('email_notifications', true))"
                    helper="Send email alerts for new chat messages"
                />
                
                <x-admin.input
                    type="email"
                    name="notification_email"
                    label="Notification Email"
                    :value="old('notification_email', settings('notification_email', settings('admin_email')))"
                    helper="Email address to receive chat notifications"
                />
                
                <x-admin.checkbox
                    name="offline_notifications"
                    label="Offline Message Notifications"
                    :checked="old('offline_notifications', settings('offline_notifications', true))"
                    helper="Send notifications for messages received while offline"
                />
                
                <x-admin.input
                    type="number"
                    name="notification_sound"
                    label="Notification Sound"
                    :value="old('notification_sound', settings('notification_sound', 1))"
                    min="0"
                    max="1"
                    step="0.1"
                    helper="Sound volume for new message notifications (0 = off)"
                />
            </div>
        </x-admin.form-section>

        <!-- Business Hours -->
        <x-admin.form-section title="Business Hours" description="Set when live chat operators are typically available">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input
                    type="time"
                    name="business_hours_start"
                    label="Start Time"
                    :value="old('business_hours_start', settings('business_hours_start', '08:00'))"
                />
                
                <x-admin.input
                    type="time"
                    name="business_hours_end"
                    label="End Time"
                    :value="old('business_hours_end', settings('business_hours_end', '17:00'))"
                />
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Business Days
                    </label>
                    <div class="grid grid-cols-7 gap-2">
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <x-admin.checkbox
                                name="business_days[]"
                                :value="$day"
                                :label="substr($day, 0, 3)"
                                :checked="in_array($day, old('business_days', settings('business_days', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])))"
                            />
                        @endforeach
                    </div>
                </div>
            </div>
        </x-admin.form-section>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <x-admin.button type="submit" color="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Settings
            </x-admin.button>
        </div>
    </form>
</x-layouts.admin>