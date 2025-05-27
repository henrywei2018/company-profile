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
        <div class="flex gap-3">
            <x-admin.button color="info" href="{{ route('admin.chat.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                View Dashboard
            </x-admin.button>
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
                    :options="[
                        'bottom-right' => 'Bottom Right', 
                        'bottom-left' => 'Bottom Left',
                        'top-right' => 'Top Right',
                        'top-left' => 'Top Left'
                    ]"
                    :selected="old('chat_position', settings('chat_position', 'bottom-right'))"
                />
                
                <x-admin.select
                    name="chat_theme"
                    label="Widget Theme"
                    :options="[
                        'primary' => 'Primary (Blue)', 
                        'dark' => 'Dark',
                        'light' => 'Light'
                    ]"
                    :selected="old('chat_theme', settings('chat_theme', 'primary'))"
                />
                
                <x-admin.select
                    name="chat_size"
                    label="Widget Size"
                    :options="[
                        'compact' => 'Compact', 
                        'normal' => 'Normal',
                        'large' => 'Large'
                    ]"
                    :selected="old('chat_size', settings('chat_size', 'normal'))"
                />
                
                <div class="md:col-span-2">
                    <x-admin.input
                        name="chat_greeting"
                        label="Default Greeting Message"
                        :value="old('chat_greeting', settings('chat_greeting', 'Hello! How can we help you today?'))"
                        helper="First message visitors see when starting a chat"
                    />
                </div>
                
                <div class="md:col-span-2">
                    <x-admin.textarea
                        name="offline_message"
                        label="Offline Message"
                        :value="old('offline_message', settings('offline_message', 'We are currently offline. Please leave a message and we will get back to you within 24 hours!'))"
                        rows="3"
                        helper="Message shown when no operators are available"
                    />
                </div>
            </div>
        </x-admin.form-section>

        <!-- Operator Settings -->
        <x-admin.form-section title="Operator Settings" description="Configure operator availability and assignment">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input
                    type="number"
                    name="max_concurrent_chats"
                    label="Max Concurrent Chats per Operator"
                    :value="old('max_concurrent_chats', settings('max_concurrent_chats', 3))"
                    min="1"
                    max="10"
                    helper="Maximum number of chats one operator can handle simultaneously"
                />
                
                <x-admin.select
                    name="assignment_method"
                    label="Chat Assignment Method"
                    :options="[
                        'round_robin' => 'Round Robin',
                        'least_busy' => 'Least Busy',
                        'manual' => 'Manual Assignment'
                    ]"
                    :selected="old('assignment_method', settings('assignment_method', 'least_busy'))"
                />
                
                <x-admin.checkbox
                    name="auto_assign_waiting"
                    label="Auto-assign Waiting Chats"
                    :checked="old('auto_assign_waiting', settings('auto_assign_waiting', true))"
                    helper="Automatically assign waiting chats when operators become available"
                />
                
                <x-admin.input
                    type="number"
                    name="operator_timeout_minutes"
                    label="Operator Timeout (minutes)"
                    :value="old('operator_timeout_minutes', settings('operator_timeout_minutes', 30))"
                    min="5"
                    max="120"
                    helper="Mark operators as offline after this period of inactivity"
                />
            </div>
        </x-admin.form-section>

        <!-- Auto-Response Settings -->
        <x-admin.form-section title="Auto-Response Settings" description="Configure automated responses and bot behavior">
            <div class="space-y-4">
                <x-admin.checkbox
                    name="auto_response_enabled"
                    label="Enable Auto-Responses"
                    :checked="old('auto_response_enabled', settings('auto_response_enabled', true))"
                    helper="Automatically respond to common questions when operators are offline"
                />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input
                        type="number"
                        name="response_delay_seconds"
                        label="Response Delay (seconds)"
                        :value="old('response_delay_seconds', settings('response_delay_seconds', 2))"
                        min="0"
                        max="10"
                        helper="Delay before sending auto-responses to seem more natural"
                    />
                    
                    <x-admin.input
                        type="number"
                        name="typing_indicator_duration"
                        label="Typing Indicator Duration (seconds)"
                        :value="old('typing_indicator_duration', settings('typing_indicator_duration', 3))"
                        min="1"
                        max="10"
                        helper="How long to show typing indicator before bot responds"
                    />
                </div>
                
                <x-admin.checkbox
                    name="collect_visitor_info"
                    label="Request Visitor Information"
                    :checked="old('collect_visitor_info', settings('collect_visitor_info', true))"
                    helper="Ask for visitor name and email when operators are offline"
                />
            </div>
        </x-admin.form-section>

        <!-- Business Hours -->
        <x-admin.form-section title="Business Hours" description="Set when live chat operators are typically available">
            <div class="space-y-4">
                <x-admin.checkbox
                    name="business_hours_enabled"
                    label="Enable Business Hours"
                    :checked="old('business_hours_enabled', settings('business_hours_enabled', true))"
                    helper="Show different messages based on business hours"
                />
                
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
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Business Days
                    </label>
                    <div class="grid grid-cols-7 gap-2">
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <div class="text-center">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    {{ substr($day, 0, 3) }}
                                </label>
                                <input type="checkbox" 
                                       name="business_days[]" 
                                       value="{{ $day }}"
                                       {{ in_array($day, old('business_days', settings('business_days', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']))) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <x-admin.input
                    name="timezone"
                    label="Timezone"
                    :value="old('timezone', settings('timezone', 'Asia/Jakarta'))"
                    helper="Timezone for business hours calculation"
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
                
                <x-admin.checkbox
                    name="desktop_notifications"
                    label="Desktop Notifications"
                    :checked="old('desktop_notifications', settings('desktop_notifications', true))"
                    helper="Show browser notifications for new chats"
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
                    name="notification_sound_volume"
                    label="Notification Sound Volume"
                    :value="old('notification_sound_volume', settings('notification_sound_volume', 50))"
                    min="0"
                    max="100"
                    helper="Sound volume for new message notifications (0 = muted)"
                />
                
                <x-admin.input
                    type="number"
                    name="urgent_chat_threshold_minutes"
                    label="Urgent Chat Threshold (minutes)"
                    :value="old('urgent_chat_threshold_minutes', settings('urgent_chat_threshold_minutes', 5))"
                    min="1"
                    max="60"
                    helper="Mark chats as urgent after waiting this long"
                />
            </div>
        </x-admin.form-section>

        <!-- Data & Privacy Settings -->
        <x-admin.form-section title="Data & Privacy Settings" description="Configure data retention and privacy options">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input
                    type="number"
                    name="chat_retention_days"
                    label="Chat Retention (days)"
                    :value="old('chat_retention_days', settings('chat_retention_days', 90))"
                    min="1"
                    max="365"
                    helper="How long to keep closed chat sessions"
                />
                
                <x-admin.input
                    type="number"
                    name="max_messages_per_session"
                    label="Max Messages per Session"
                    :value="old('max_messages_per_session', settings('max_messages_per_session', 1000))"
                    min="100"
                    max="5000"
                    helper="Maximum messages to keep per chat session"
                />
                
                <x-admin.checkbox
                    name="anonymize_visitor_data"
                    label="Anonymize Visitor Data"
                    :checked="old('anonymize_visitor_data', settings('anonymize_visitor_data', false))"
                    helper="Remove visitor personal information after chat ends"
                />
                
                <x-admin.checkbox
                    name="log_chat_analytics"
                    label="Enable Chat Analytics"
                    :checked="old('log_chat_analytics', settings('log_chat_analytics', true))"
                    helper="Collect analytics data for reporting and insights"
                />
            </div>
        </x-admin.form-section>

        <!-- Advanced Settings -->
        <x-admin.form-section title="Advanced Settings" description="Advanced configuration options">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input
                    type="number"
                    name="session_timeout_minutes"
                    label="Session Timeout (minutes)"
                    :value="old('session_timeout_minutes', settings('session_timeout_minutes', 30))"
                    min="5"
                    max="120"
                    helper="Close inactive sessions after this period"
                />
                
                <x-admin.input
                    type="number"
                    name="message_rate_limit"
                    label="Message Rate Limit (per minute)"
                    :value="old('message_rate_limit', settings('message_rate_limit', 10))"
                    min="1"
                    max="60"
                    helper="Maximum messages per visitor per minute (spam protection)"
                />
                
                <x-admin.checkbox
                    name="require_visitor_email"
                    label="Require Visitor Email"
                    :checked="old('require_visitor_email', settings('require_visitor_email', false))"
                    helper="Require visitors to provide email before chatting"
                />
                
                <x-admin.checkbox
                    name="enable_file_uploads"
                    label="Enable File Uploads"
                    :checked="old('enable_file_uploads', settings('enable_file_uploads', false))"
                    helper="Allow visitors to upload files in chat"
                />
                
                <x-admin.input
                    name="allowed_file_types"
                    label="Allowed File Types"
                    :value="old('allowed_file_types', settings('allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx'))"
                    helper="Comma-separated list of allowed file extensions"
                />
                
                <x-admin.input
                    type="number"
                    name="max_file_size_mb"
                    label="Max File Size (MB)"
                    :value="old('max_file_size_mb', settings('max_file_size_mb', 5))"
                    min="1"
                    max="25"
                    helper="Maximum file size for uploads"
                />
            </div>
        </x-admin.form-section>

        <!-- Quick Responses Management -->
        <x-admin.form-section title="Quick Responses" description="Manage predefined responses for operators">
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Current Quick Responses</h4>
                    <x-admin.button type="button" size="sm" color="light" onclick="addQuickResponse()">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Response
                    </x-admin.button>
                </div>
                
                <div id="quick-responses-container" class="space-y-3">
                    @php
                        $quickResponses = old('quick_responses', settings('quick_responses', [
                            ['name' => 'Greeting', 'message' => 'Hello! How can I help you today?'],
                            ['name' => 'Acknowledge', 'message' => 'I understand your concern. Let me check that for you.'],
                            ['name' => 'Follow-up', 'message' => 'Is there anything else I can help you with today?']
                        ]));
                    @endphp
                    
                    @foreach($quickResponses as $index => $response)
                        <div class="quick-response-item flex space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                                <input type="text" 
                                       name="quick_responses[{{ $index }}][name]" 
                                       placeholder="Response name"
                                       value="{{ $response['name'] ?? '' }}"
                                       class="text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                <div class="md:col-span-2">
                                    <textarea name="quick_responses[{{ $index }}][message]" 
                                              rows="2" 
                                              placeholder="Response message"
                                              class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white resize-none">{{ $response['message'] ?? '' }}</textarea>
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="removeQuickResponse(this)" 
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-admin.form-section>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
            <x-admin.button type="button" color="light" href="{{ route('admin.chat.index') }}">
                Cancel
            </x-admin.button>
            <x-admin.button type="submit" color="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Settings
            </x-admin.button>
        </div>
    </form>

    @push('scripts')
    <script>
        let quickResponseIndex = {{ count($quickResponses ?? []) }};
        
        function addQuickResponse() {
            const container = document.getElementById('quick-responses-container');
            const newResponse = document.createElement('div');
            newResponse.className = 'quick-response-item flex space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg';
            newResponse.innerHTML = `
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input type="text" 
                           name="quick_responses[${quickResponseIndex}][name]" 
                           placeholder="Response name"
                           class="text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <div class="md:col-span-2">
                        <textarea name="quick_responses[${quickResponseIndex}][message]" 
                                  rows="2" 
                                  placeholder="Response message"
                                  class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white resize-none"></textarea>
                    </div>
                </div>
                <button type="button" 
                        onclick="removeQuickResponse(this)" 
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            `;
            container.appendChild(newResponse);
            quickResponseIndex++;
        }
        
        function removeQuickResponse(button) {
            button.closest('.quick-response-item').remove();
        }
        
        // Test notification function
        function testNotification() {
            if (Notification.permission === 'granted') {
                new Notification('Test Chat Notification', {
                    body: 'This is how chat notifications will appear',
                    icon: '/favicon.ico'
                });
            } else if (Notification.permission !== 'denied') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        new Notification('Test Chat Notification', {
                            body: 'This is how chat notifications will appear',
                            icon: '/favicon.ico'
                        });
                    }
                });
            } else {
                alert('Notifications are blocked. Please enable them in your browser settings.');
            }
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const businessHoursEnabled = document.querySelector('input[name="business_hours_enabled"]').checked;
            const startTime = document.querySelector('input[name="business_hours_start"]').value;
            const endTime = document.querySelector('input[name="business_hours_end"]').value;
            
            if (businessHoursEnabled && startTime && endTime && startTime >= endTime) {
                e.preventDefault();
                alert('Business hours start time must be before end time.');
                return false;
            }
        });
    </script>
    @endpush
</x-layouts.admin>