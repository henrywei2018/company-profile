<!-- resources/views/profile/preferences.blade.php -->
<x-dynamic-component :component="$layout" title="Notification Preferences">
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notification Preferences</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Control how and when you receive notifications</p>
                </div>
                <x-admin.button 
                    href="{{ route('profile.show') }}" 
                    color="light"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                >
                    Back to Profile
                </x-admin.button>
            </div>

            <form action="{{ route('profile.preferences.update') }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <!-- Main Preferences -->
                    <div class="lg:col-span-2 space-y-3">
                        <!-- General Notification Settings -->
                        <x-admin.form-section 
                            title="General Settings"
                            description="Master controls for all notifications"
                        >
                            <div class="space-y-4">
                                <x-admin.toggle
                                    label="Enable email notifications"
                                    name="email_notifications"
                                    :checked="old('email_notifications', $user->email_notifications ?? true)"
                                    helper="Master switch for all email notifications"
                                    color="blue"
                                />

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <x-admin.select
                                        label="Notification Frequency"
                                        name="notification_frequency"
                                        :value="old('notification_frequency', $user->notification_frequency ?? 'immediate')"
                                        :options="[
                                            'immediate' => 'Immediate',
                                            'hourly' => 'Hourly digest',
                                            'daily' => 'Daily digest',
                                            'weekly' => 'Weekly digest'
                                        ]"
                                        helper="How often you want to receive notification emails"
                                    />
                                </div>
                            </div>
                        </x-admin.form-section>

                        <!-- Project Notifications -->
                        <x-admin.form-section 
                            title="Project Notifications"
                            description="Notifications about your projects and updates"
                        >
                            <div class="space-y-4">
                                <x-admin.toggle
                                    label="Project updates"
                                    name="project_update_notifications"
                                    :checked="old('project_update_notifications', $user->project_update_notifications ?? true)"
                                    helper="Status changes, milestones, and progress updates"
                                    color="green"
                                />

                                <x-admin.toggle
                                    label="Deadline alerts"
                                    name="deadline_alert_notifications"
                                    :checked="old('deadline_alert_notifications', $user->deadline_alert_notifications ?? true)"
                                    helper="Reminders about upcoming project deadlines"
                                    color="amber"
                                />

                                <x-admin.toggle
                                    label="Quotation updates"
                                    name="quotation_update_notifications"
                                    :checked="old('quotation_update_notifications', $user->quotation_update_notifications ?? true)"
                                    helper="New quotations, approvals, and status changes"
                                    color="blue"
                                />
                            </div>
                        </x-admin.form-section>

                        <!-- Communication Notifications -->
                        <x-admin.form-section 
                            title="Communication"
                            description="Messages, chat, and interaction notifications"
                        >
                            <div class="space-y-4">
                                <x-admin.toggle
                                    label="Message replies"
                                    name="message_reply_notifications"
                                    :checked="old('message_reply_notifications', $user->message_reply_notifications ?? true)"
                                    helper="Replies to your messages and new conversations"
                                    color="purple"
                                />

                                <x-admin.toggle
                                    label="Chat notifications"
                                    name="chat_notifications"
                                    :checked="old('chat_notifications', $user->chat_notifications ?? true)"
                                    helper="Real-time chat messages and mentions"
                                    color="indigo"
                                />

                                <x-admin.toggle
                                    label="Testimonial requests"
                                    name="testimonial_notifications"
                                    :checked="old('testimonial_notifications', $user->testimonial_notifications ?? true)"
                                    helper="Requests for testimonials and feedback"
                                    color="pink"
                                />
                            </div>
                        </x-admin.form-section>

                        {{-- <!-- System Notifications -->
                        <x-admin.form-section 
                            title="System & Security"
                            description="Important system updates and security alerts"
                        >
                            <div class="space-y-4">
                                <x-admin.toggle
                                    label="System notifications"
                                    name="system_notifications"
                                    :checked="old('system_notifications', $user->system_notifications ?? false)"
                                    helper="Maintenance, updates, and system announcements"
                                    color="gray"
                                />

                                <x-admin.toggle
                                    label="Security alerts"
                                    name="security_alert_notifications"
                                    :checked="old('security_alert_notifications', $user->security_alert_notifications ?? true)"
                                    helper="Login alerts and security-related notifications"
                                    color="red"
                                />

                                @if($user->hasAdminAccess())
                                <x-admin.toggle
                                    label="User registration alerts"
                                    name="user_registration_notifications"
                                    :checked="old('user_registration_notifications', $user->user_registration_notifications ?? false)"
                                    helper="Notifications when new users register"
                                    color="blue"
                                />

                                <x-admin.toggle
                                    label="Urgent notifications"
                                    name="urgent_notifications"
                                    :checked="old('urgent_notifications', $user->urgent_notifications ?? true)"
                                    helper="Critical system alerts and urgent matters"
                                    color="red"
                                />
                                @endif
                            </div>
                        </x-admin.form-section> --}}

                        <!-- Marketing Notifications -->
                        <x-admin.form-section 
                            title="Marketing & Promotions"
                            description="Newsletter, promotions, and marketing communications"
                        >
                            <div class="space-y-4">
                                <x-admin.toggle
                                    label="Marketing emails"
                                    name="marketing_notifications"
                                    :checked="old('marketing_notifications', $user->marketing_notifications ?? false)"
                                    helper="Newsletters, product updates, and promotional content"
                                    color="orange"
                                />

                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">About Marketing Emails</h4>
                                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                <p>We send marketing emails sparingly - typically once per month with product updates, tips, and relevant industry news. You can unsubscribe at any time.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-admin.form-section>

                        {{-- <!-- Quiet Hours -->
                        <x-admin.form-section 
                            title="Quiet Hours"
                            description="Set times when you don't want to receive notifications"
                        >
                            <div class="space-y-4">
                                <x-admin.toggle
                                    label="Enable quiet hours"
                                    name="quiet_hours[enabled]"
                                    :checked="old('quiet_hours.enabled', $user->quiet_hours['enabled'] ?? false)"
                                    helper="Disable notifications during specific hours"
                                    color="indigo"
                                    x-data="{ enabled: {{ old('quiet_hours.enabled', $user->quiet_hours['enabled'] ?? false) ? 'true' : 'false' }} }"
                                    x-model="enabled"
                                />

                                <div x-show="enabled" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <x-admin.input
                                        label="Start Time"
                                        name="quiet_hours[start]"
                                        type="time"
                                        :value="old('quiet_hours.start', $user->quiet_hours['start'] ?? '22:00')"
                                        helper="When quiet hours begin"
                                    />

                                    <x-admin.input
                                        label="End Time"
                                        name="quiet_hours[end]"
                                        type="time"
                                        :value="old('quiet_hours.end', $user->quiet_hours['end'] ?? '08:00')"
                                        helper="When quiet hours end"
                                    />
                                </div>

                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-amber-800 dark:text-amber-400">Note about Quiet Hours</h4>
                                            <div class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                                                <p>Urgent security alerts and critical system notifications will still be delivered during quiet hours for your safety.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-admin.form-section> --}}
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-3">
                        <!-- Current Settings Summary -->
                        <x-admin.card title="Current Settings">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Email Notifications</span>
                                    @if($user->email_notifications ?? true)
                                        <x-admin.badge type="success" size="sm">Enabled</x-admin.badge>
                                    @else
                                        <x-admin.badge type="danger" size="sm">Disabled</x-admin.badge>
                                    @endif
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Frequency</span>
                                    <x-admin.badge type="info" size="sm">
                                        {{ ucfirst($user->notification_frequency ?? 'immediate') }}
                                    </x-admin.badge>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Project Updates</span>
                                    @if($user->project_update_notifications ?? true)
                                        <x-admin.badge type="success" size="sm">On</x-admin.badge>
                                    @else
                                        <x-admin.badge type="gray" size="sm">Off</x-admin.badge>
                                    @endif
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Marketing Emails</span>
                                    @if($user->marketing_notifications ?? false)
                                        <x-admin.badge type="success" size="sm">Subscribed</x-admin.badge>
                                    @else
                                        <x-admin.badge type="gray" size="sm">Unsubscribed</x-admin.badge>
                                    @endif
                                </div>
                            </div>
                        </x-admin.card>

                        <!-- Form Actions -->
                        <x-admin.card>
                            <div class="flex flex-col space-y-3">
                                <x-admin.button 
                                    type="submit" 
                                    color="primary" 
                                    class="w-full"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'
                                >
                                    Save Preferences
                                </x-admin.button>
                                
                                <x-admin.button 
                                    type="button" 
                                    color="light" 
                                    class="w-full"
                                    onclick="resetToDefaults()"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'
                                >
                                    Reset to Defaults
                                </x-admin.button>
                            </div>
                        </x-admin.card>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function resetToDefaults() {
            if (confirm('Are you sure you want to reset all notification preferences to their default values?')) {
                // Reset form to default values
                const form = document.querySelector('form');
                const defaults = {
                    'email_notifications': true,
                    'project_update_notifications': true,
                    'quotation_update_notifications': true,
                    'message_reply_notifications': true,
                    'deadline_alert_notifications': true,
                    'chat_notifications': true,
                    'system_notifications': false,
                    'marketing_notifications': false,
                    'testimonial_notifications': true,
                    'urgent_notifications': true,
                    'user_registration_notifications': false,
                    'security_alert_notifications': true,
                    'notification_frequency': 'immediate'
                };

                Object.keys(defaults).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = defaults[key];
                        } else {
                            input.value = defaults[key];
                        }
                    }
                });

                // Reset quiet hours
                form.querySelector('[name="quiet_hours[enabled]"]').checked = false;
                form.querySelector('[name="quiet_hours[start]"]').value = '22:00';
                form.querySelector('[name="quiet_hours[end]"]').value = '08:00';

                showNotification('Preferences reset to default values. Don\'t forget to save!', 'info');
            }
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg p-4 ${getNotificationClasses(type)} transform transition-all duration-300 ease-in-out`;
            notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification?.remove(), 5000);
        }

        function getNotificationClasses(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
                error: 'bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
                info: 'bg-blue-50 border border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400'
            };
            return classes[type] || classes.info;
        }

        function getNotificationIcon(type) {
            const icons = {
                success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons.info;
        }

        // Auto-save draft functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            // Save preferences every 30 seconds
            setInterval(function() {
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    if (key !== '_token' && key !== '_method') {
                        data[key] = value;
                    }
                }
                localStorage.setItem('notification_preferences_draft', JSON.stringify(data));
            }, 30000);

            // Clear draft on successful submission
            form.addEventListener('submit', function() {
                localStorage.removeItem('notification_preferences_draft');
            });
        });
    </script>
    @endpush
</x-dynamic-component>