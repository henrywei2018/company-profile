{{-- resources/views/admin/settings/email.blade.php - Simplified Version --}}
<x-layouts.admin title="Email Settings">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Email Settings</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Configure email settings and notifications</p>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('admin.settings.index') }}" 
                   class="border-transparent border-b-2 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    General
                </a>
                <a href="{{ route('admin.settings.seo') }}" 
                   class="border-transparent border-b-2 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    SEO
                </a>
                <a href="{{ route('admin.settings.email') }}" 
                   class="border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                    Email
                </a>
            </nav>
        </div>

        <form action="{{ route('admin.settings.email.update') }}" method="POST" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Email Settings -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Basic Email Configuration</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure basic email settings for your application.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-200">From Email Address</label>
                        <input type="email" 
                               name="mail_from_address" 
                               id="mail_from_address" 
                               value="{{ old('mail_from_address', settings('mail_from_address', config('mail.from.address'))) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email address that will appear as sender</p>
                        @error('mail_from_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">From Name</label>
                        <input type="text" 
                               name="mail_from_name" 
                               id="mail_from_name" 
                               value="{{ old('mail_from_name', settings('mail_from_name', config('mail.from.name'))) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Name that will appear as sender</p>
                        @error('mail_from_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Admin Email</label>
                        <input type="email" 
                               name="admin_email" 
                               id="admin_email" 
                               value="{{ old('admin_email', settings('admin_email', config('mail.from.address'))) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email to receive admin notifications</p>
                        @error('admin_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="support_email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Support Email</label>
                        <input type="email" 
                               name="support_email" 
                               id="support_email" 
                               value="{{ old('support_email', settings('support_email')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email for customer support inquiries</p>
                        @error('support_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Message Email Settings -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Message Email Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure how message emails are handled.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="message_email_enabled" 
                                   name="message_email_enabled" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('message_email_enabled', settings('message_email_enabled', true)) ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="message_email_enabled" class="font-medium text-gray-700 dark:text-gray-200">Enable Message Email Notifications</label>
                            <p class="text-gray-500 dark:text-gray-400">Send email notifications when new messages are received</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="message_auto_reply_enabled" 
                                   name="message_auto_reply_enabled" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('message_auto_reply_enabled', settings('message_auto_reply_enabled', true)) ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="message_auto_reply_enabled" class="font-medium text-gray-700 dark:text-gray-200">Auto-Reply to Messages</label>
                            <p class="text-gray-500 dark:text-gray-400">Automatically send confirmation emails to message senders</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="message_reply_to" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Message Reply-To Email</label>
                    <input type="email" 
                           name="message_reply_to" 
                           id="message_reply_to" 
                           value="{{ old('message_reply_to', settings('message_reply_to', settings('admin_email'))) }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email address for replies to message notifications</p>
                    @error('message_reply_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Quotation Email Settings -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quotation Email Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure how quotation emails are handled.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="quotation_email_enabled" 
                                   name="quotation_email_enabled" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('quotation_email_enabled', settings('quotation_email_enabled', true)) ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="quotation_email_enabled" class="font-medium text-gray-700 dark:text-gray-200">Enable Quotation Email Notifications</label>
                            <p class="text-gray-500 dark:text-gray-400">Send email notifications when new quotation requests are received</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="quotation_client_confirmation_enabled" 
                                   name="quotation_client_confirmation_enabled" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('quotation_client_confirmation_enabled', settings('quotation_client_confirmation_enabled', true)) ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="quotation_client_confirmation_enabled" class="font-medium text-gray-700 dark:text-gray-200">Send Quotation Confirmation to Client</label>
                            <p class="text-gray-500 dark:text-gray-400">Automatically send confirmation emails to quotation requesters</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="quotation_reply_to" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Quotation Reply-To Email</label>
                        <input type="email" 
                               name="quotation_reply_to" 
                               id="quotation_reply_to" 
                               value="{{ old('quotation_reply_to', settings('quotation_reply_to', settings('admin_email'))) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email address for quotation-related replies</p>
                        @error('quotation_reply_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quotation_cc_email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">CC Email for Quotations</label>
                        <input type="email" 
                               name="quotation_cc_email" 
                               id="quotation_cc_email" 
                               value="{{ old('quotation_cc_email', settings('quotation_cc_email')) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional: CC email for all quotation notifications</p>
                        @error('quotation_cc_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Test Email Section -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Test Email Configuration</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Send a test email to verify your settings.</p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Test Email</h4>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <div class="flex items-center gap-3">
                                    <input type="email" 
                                           name="test_email" 
                                           id="test_email" 
                                           value="{{ auth()->user()->email }}" 
                                           placeholder="test@example.com"
                                           class="block w-full border-blue-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <button type="button" 
                                            onclick="sendTestEmail()" 
                                            class="inline-flex items-center px-3 py-2 border border-blue-600 rounded-md text-sm font-medium text-blue-600 bg-white hover:bg-blue-50">
                                        Send Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Email Settings
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function sendTestEmail() {
            const testEmail = document.getElementById('test_email').value;
            if (!testEmail) {
                alert('Please enter a test email address');
                return;
            }

            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Sending...';
            button.disabled = true;

            fetch('{{ route("admin.settings.email.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    email: testEmail, 
                    type: 'general' 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Test email sent successfully!');
                } else {
                    alert('Failed to send test email: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending test email');
            })
            .finally(() => {
                button.textContent = originalText;
                button.disabled = false;
            });
        }
    </script>
    @endpush
</x-layouts.admin>