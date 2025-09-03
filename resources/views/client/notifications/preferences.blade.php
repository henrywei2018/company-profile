{{-- resources/views/client/notifications/preferences.blade.php --}}
<x-layouts.client title="Preferensi Notifikasi">
    
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notification Preferences</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Customize how and when you receive notifications about your projects and activities.
                </p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('client.notifications.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali to Notifications
                </a>
                
                <button onclick="sendTestNotification()" 
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3"></path>
                    </svg>
                    Send Test
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Notifications</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['total_notifications'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Minggu Ini</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['notifications_this_week'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Most Common</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $stats['most_common_type'] ?? 'None')) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Preferences Form -->
    <form method="POST" action="{{ route('client.notifications.preferences.update') }}">
        @csrf
        @method('PUT')
        
        <div class="space-y-8">
            <!-- Global Settings -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Global Settings</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">General notification preferences that apply across all types.</p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Master Switch -->
                    <div class="flex items-center justify-between">
                        <div class="flex-grow">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Email Notifications</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Enable or disable all email notifications</p>
                        </div>
                        <div class="ml-4">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ ($preferences['email_notifications'] ?? true) ? 'bg-blue-600' : 'bg-gray-200' }}" 
                                    onclick="toggleSwitch(this, 'email_notifications')" 
                                    role="switch" 
                                    aria-checked="{{ ($preferences['email_notifications'] ?? true) ? 'true' : 'false' }}">
                                <span class="sr-only">Enable email notifications</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($preferences['email_notifications'] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <input type="hidden" name="email_notifications" value="{{ ($preferences['email_notifications'] ?? true) ? '1' : '0' }}" id="email_notifications">
                        </div>
                    </div>

                    <!-- Notification Frequency -->
                    <div>
                        <label for="notification_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notification Frequency</label>
                        <select id="notification_frequency" name="notification_frequency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="immediate" {{ ($preferences['notification_frequency'] ?? 'immediate') === 'immediate' ? 'selected' : '' }}>Immediate</option>
                            <option value="hourly" {{ ($preferences['notification_frequency'] ?? 'immediate') === 'hourly' ? 'selected' : '' }}>Hourly Digest</option>
                            <option value="daily" {{ ($preferences['notification_frequency'] ?? 'immediate') === 'daily' ? 'selected' : '' }}>Daily Summary</option>
                            <option value="weekly" {{ ($preferences['notification_frequency'] ?? 'immediate') === 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                        </select>
                    </div>

                    <!-- Quiet Hours -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quiet Hours</label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Set hours when you don't want to receive notifications</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="quiet_hours_start" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                                <input type="time" id="quiet_hours_start" name="quiet_hours_start" 
                                       value="{{ $preferences['quiet_hours']['start'] ?? '' }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="quiet_hours_end" class="block text-xs font-medium text-gray-700 dark:text-gray-300">End Time</label>
                                <input type="time" id="quiet_hours_end" name="quiet_hours_end" 
                                       value="{{ $preferences['quiet_hours']['end'] ?? '' }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Types -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Notification Types</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Choose which types of notifications you want to receive.</p>
                </div>
                
                <div class="px-6 py-4 space-y-6">
                    <!-- Project Notifications -->
                    <div class="flex items-center justify-between">
                        <div class="flex-grow">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Project Updates</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Get notified about project status changes, completions, and deadlines</p>
                        </div>
                        <div class="ml-4">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ ($preferences['project_updates'] ?? true) ? 'bg-blue-600' : 'bg-gray-200' }}" 
                                    onclick="toggleSwitch(this, 'project_updates')" 
                                    role="switch" 
                                    aria-checked="{{ ($preferences['project_updates'] ?? true) ? 'true' : 'false' }}">
                                <span class="sr-only">Enable project updates</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($preferences['project_updates'] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <input type="hidden" name="project_updates" value="{{ ($preferences['project_updates'] ?? true) ? '1' : '0' }}" id="project_updates">
                        </div>
                    </div>

                    <!-- Quotation Notifications -->
                    <div class="flex items-center justify-between">
                        <div class="flex-grow">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Quotation Updates</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Get notified about quotation approvals, rejections, and responses needed</p>
                        </div>
                        <div class="ml-4">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ ($preferences['quotation_updates'] ?? true) ? 'bg-blue-600' : 'bg-gray-200' }}" 
                                    onclick="toggleSwitch(this, 'quotation_updates')" 
                                    role="switch" 
                                    aria-checked="{{ ($preferences['quotation_updates'] ?? true) ? 'true' : 'false' }}">
                                <span class="sr-only">Enable quotation updates</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($preferences['quotation_updates'] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <input type="hidden" name="quotation_updates" value="{{ ($preferences['quotation_updates'] ?? true) ? '1' : '0' }}" id="quotation_updates">
                        </div>
                    </div>

                    <!-- Message Notifications -->
                    <div class="flex items-center justify-between">
                        <div class="flex-grow">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Message Replies</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Get notified when you receive replies to your messages</p>
                        </div>
                        <div class="ml-4">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ ($preferences['message_replies'] ?? true) ? 'bg-blue-600' : 'bg-gray-200' }}" 
                                    onclick="toggleSwitch(this, 'message_replies')" 
                                    role="switch" 
                                    aria-checked="{{ ($preferences['message_replies'] ?? true) ? 'true' : 'false' }}">
                                <span class="sr-only">Enable message replies</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($preferences['message_replies'] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <input type="hidden" name="message_replies" value="{{ ($preferences['message_replies'] ?? true) ? '1' : '0' }}" id="message_replies">
                        </div>
                    </div>

                    <!-- Deadline Alerts -->
                    <div class="flex items-center justify-between">
                        <div class="flex-grow">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Deadline Alerts</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Get notified about approaching project deadlines</p>
                        </div>
                        <div class="ml-4">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ ($preferences['deadline_alerts'] ?? true) ? 'bg-blue-600' : 'bg-gray-200' }}" 
                                    onclick="toggleSwitch(this, 'deadline_alerts')" 
                                    role="switch" 
                                    aria-checked="{{ ($preferences['deadline_alerts'] ?? true) ? 'true' : 'false' }}">
                                <span class="sr-only">Enable deadline alerts</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($preferences['deadline_alerts'] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <input type="hidden" name="deadline_alerts" value="{{ ($preferences['deadline_alerts'] ?? true) ? '1' : '0' }}" id="deadline_alerts">
                        </div>
                    </div>

                    <!-- System Notifications -->
                    <div class="flex items-center justify-between">
                        <div class="flex-grow">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">System Notifications</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Get notified about system updates, maintenance, and important announcements</p>
                        </div>
                        <div class="ml-4">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ ($preferences['system_notifications'] ?? false) ? 'bg-blue-600' : 'bg-gray-200' }}" 
                                    onclick="toggleSwitch(this, 'system_notifications')" 
                                    role="switch" 
                                    aria-checked="{{ ($preferences['system_notifications'] ?? false) ? 'true' : 'false' }}">
                                <span class="sr-only">Enable system notifications</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($preferences['system_notifications'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <input type="hidden" name="system_notifications" value="{{ ($preferences['system_notifications'] ?? false) ? '1' : '0' }}" id="system_notifications">
                        </div>
                    </div>

                    <!-- Marketing Emails -->
                    <div class="flex items-center justify-between">
                        <div class="flex-grow">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Marketing Emails</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Receive newsletters, promotions, and company updates</p>
                        </div>
                        <div class="ml-4">
                            <button type="button" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ ($preferences['marketing_emails'] ?? false) ? 'bg-blue-600' : 'bg-gray-200' }}" 
                                    onclick="toggleSwitch(this, 'marketing_emails')" 
                                    role="switch" 
                                    aria-checked="{{ ($preferences['marketing_emails'] ?? false) ? 'true' : 'false' }}">
                                <span class="sr-only">Enable marketing emails</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($preferences['marketing_emails'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <input type="hidden" name="marketing_emails" value="{{ ($preferences['marketing_emails'] ?? false) ? '1' : '0' }}" id="marketing_emails">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Simpan Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Preferences
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
    function toggleSwitch(button, fieldName) {
        const isEnabled = button.getAttribute('aria-checked') === 'true';
        const newState = !isEnabled;
        
        // Update button appearance
        button.setAttribute('aria-checked', newState);
        const span = button.querySelector('span:last-child');
        const input = document.getElementById(fieldName);
        
        if (newState) {
            button.classList.remove('bg-gray-200');
            button.classList.add('bg-blue-600');
            span.classList.remove('translate-x-0');
            span.classList.add('translate-x-5');
            input.value = '1';
        } else {
            button.classList.remove('bg-blue-600');
            button.classList.add('bg-gray-200');
            span.classList.remove('translate-x-5');
            span.classList.add('translate-x-0');
            input.value = '0';
        }
    }

    function sendTestNotification() {
        fetch('{{ route("client.dashboard.test-notification") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Test notification sent! You should receive it shortly if your preferences allow it.');
            } else {
                alert('Gagal mencoba notifikasi: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Kesalahan:', error);
            alert('An error occurred while sending the test notification.');
        });
    }
    </script>
    @endpush

</x-layouts.client>