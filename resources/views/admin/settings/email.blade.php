{{-- resources/views/admin/settings/email.blade.php --}}
<x-layouts.admin title="Email Settings">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Settings' => route('admin.settings.index'), 'Email Settings' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Email Settings</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Configure email settings for messages, quotations, and notifications</p>
        </div>
        <div class="flex items-center gap-3">
            <x-admin.button type="button" color="info" onclick="testEmailConnection()" id="test-connection-btn"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'>
                Test Connection
            </x-admin.button>
        </div>
    </div>

    <form action="{{ route('admin.settings.email.update') }}" method="POST" id="email-settings-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- SMTP Configuration -->
            <x-admin.form-section title="SMTP Configuration" description="Configure your email server settings">
                <div class="space-y-4">
                    <x-admin.input 
                        label="SMTP Host" 
                        name="mail_host" 
                        :value="old('mail_host', config('mail.mailers.smtp.host'))" 
                        placeholder="smtp.gmail.com"
                        required 
                        helper="Your SMTP server hostname"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <x-admin.input 
                            label="SMTP Port" 
                            name="mail_port" 
                            type="number"
                            :value="old('mail_port', config('mail.mailers.smtp.port'))" 
                            placeholder="587"
                            required 
                        />

                        <x-admin.select 
                            label="Encryption" 
                            name="mail_encryption" 
                            :value="old('mail_encryption', config('mail.mailers.smtp.encryption'))"
                            :options="[
                                'tls' => 'TLS (Recommended)',
                                'ssl' => 'SSL',
                                'null' => 'None'
                            ]"
                            required
                        />
                    </div>

                    <x-admin.input 
                        label="SMTP Username" 
                        name="mail_username" 
                        :value="old('mail_username', config('mail.mailers.smtp.username'))" 
                        placeholder="your-email@gmail.com"
                        required 
                        helper="Usually your email address"
                    />

                    <x-admin.input 
                        label="SMTP Password" 
                        name="mail_password" 
                        type="password"
                        :value="old('mail_password', config('mail.mailers.smtp.password'))" 
                        placeholder="Your app password or SMTP password"
                        required 
                        helper="For Gmail, use App Password, not your regular password"
                    />
                </div>
            </x-admin.form-section>

            <!-- Email Addresses -->
            <x-admin.form-section title="Email Addresses" description="Configure sender and recipient email addresses">
                <div class="space-y-4">
                    <x-admin.input 
                        label="From Email Address" 
                        name="mail_from_address" 
                        type="email"
                        :value="old('mail_from_address', config('mail.from.address'))" 
                        placeholder="noreply@company.com"
                        required 
                        helper="Email address that will appear as sender"
                    />

                    <x-admin.input 
                        label="From Name" 
                        name="mail_from_name" 
                        :value="old('mail_from_name', config('mail.from.name'))" 
                        placeholder="CV Usaha Prima Lestari"
                        required 
                        helper="Name that will appear as sender"
                    />

                    <x-admin.input 
                        label="Admin Email" 
                        name="admin_email" 
                        type="email"
                        :value="old('admin_email', settings('admin_email', 'admin@company.com'))" 
                        placeholder="admin@company.com"
                        required 
                        helper="Email to receive new message and quotation notifications"
                    />

                    <x-admin.input 
                        label="Support Email" 
                        name="support_email" 
                        type="email"
                        :value="old('support_email', settings('support_email', 'support@company.com'))" 
                        placeholder="support@company.com"
                        helper="Email for customer support inquiries"
                    />
                </div>
            </x-admin.form-section>
        </div>

        <!-- Message Settings -->
        <x-admin.form-section title="Message Email Settings" description="Configure how message emails are handled" class="mt-6">
            <div class="space-y-4">
                <x-admin.toggle 
                    label="Enable Message Email Notifications" 
                    name="message_email_enabled" 
                    :checked="old('message_email_enabled', settings('message_email_enabled', true))"
                    helper="Send email notifications when new messages are received"
                />

                <x-admin.toggle 
                    label="Auto-Reply to Messages" 
                    name="message_auto_reply_enabled" 
                    :checked="old('message_auto_reply_enabled', settings('message_auto_reply_enabled', true))"
                    helper="Automatically send confirmation emails to message senders"
                />

                <x-admin.rich-editor 
                    label="Auto-Reply Message Template" 
                    name="message_auto_reply_template" 
                    :value="old('message_auto_reply_template', settings('message_auto_reply_template', $defaultMessageReply))"
                    helper="Use {name}, {email}, {subject} as placeholders"
                    minHeight="150px"
                />

                <x-admin.input 
                    label="Message Reply-To Email" 
                    name="message_reply_to" 
                    type="email"
                    :value="old('message_reply_to', settings('message_reply_to', settings('admin_email')))" 
                    placeholder="admin@company.com"
                    helper="Email address for replies to message notifications"
                />
            </div>
        </x-admin.form-section>

        <!-- Quotation Settings -->
        <x-admin.form-section title="Quotation Email Settings" description="Configure how quotation emails are handled" class="mt-6">
            <div class="space-y-4">
                <x-admin.toggle 
                    label="Enable Quotation Email Notifications" 
                    name="quotation_email_enabled" 
                    :checked="old('quotation_email_enabled', settings('quotation_email_enabled', true))"
                    helper="Send email notifications when new quotation requests are received"
                />

                <x-admin.toggle 
                    label="Send Quotation Confirmation to Client" 
                    name="quotation_client_confirmation_enabled" 
                    :checked="old('quotation_client_confirmation_enabled', settings('quotation_client_confirmation_enabled', true))"
                    helper="Automatically send confirmation emails to quotation requesters"
                />

                <x-admin.rich-editor 
                    label="Quotation Confirmation Template" 
                    name="quotation_confirmation_template" 
                    :value="old('quotation_confirmation_template', settings('quotation_confirmation_template', $defaultQuotationConfirmation))"
                    helper="Use {name}, {email}, {service}, {company} as placeholders"
                    minHeight="200px"
                />

                <div class="grid grid-cols-2 gap-4">
                    <x-admin.input 
                        label="Quotation Reply-To Email" 
                        name="quotation_reply_to" 
                        type="email"
                        :value="old('quotation_reply_to', settings('quotation_reply_to', settings('admin_email')))" 
                        placeholder="quotations@company.com"
                        helper="Email address for quotation-related replies"
                    />

                    <x-admin.input 
                        label="CC Email for Quotations" 
                        name="quotation_cc_email" 
                        type="email"
                        :value="old('quotation_cc_email', settings('quotation_cc_email'))" 
                        placeholder="sales@company.com"
                        helper="Optional: CC email for all quotation notifications"
                    />
                </div>

                <x-admin.toggle 
                    label="Send Status Update Emails" 
                    name="quotation_status_updates_enabled" 
                    :checked="old('quotation_status_updates_enabled', settings('quotation_status_updates_enabled', true))"
                    helper="Send emails when quotation status changes (approved, rejected, etc.)"
                />
            </div>
        </x-admin.form-section>

        <!-- Email Queue Settings -->
        <x-admin.form-section title="Email Queue & Delivery Settings" description="Configure email delivery and queue settings" class="mt-6">
            <div class="space-y-4">
                <x-admin.select 
                    label="Email Queue Driver" 
                    name="queue_driver" 
                    :value="old('queue_driver', config('queue.default'))"
                    :options="[
                        'sync' => 'Sync (Send immediately)',
                        'database' => 'Database Queue',
                        'redis' => 'Redis Queue',
                    ]"
                    helper="How emails should be processed"
                />

                <x-admin.toggle 
                    label="Email Logging" 
                    name="mail_logging_enabled" 
                    :checked="old('mail_logging_enabled', settings('mail_logging_enabled', true))"
                    helper="Log all outgoing emails for debugging"
                />

                <div class="grid grid-cols-2 gap-4">
                    <x-admin.input 
                        label="Daily Email Limit" 
                        name="daily_email_limit" 
                        type="number"
                        :value="old('daily_email_limit', settings('daily_email_limit', 500))" 
                        placeholder="500"
                        helper="Maximum emails to send per day (0 = unlimited)"
                    />

                    <x-admin.input 
                        label="Retry Failed Emails (times)" 
                        name="email_retry_attempts" 
                        type="number"
                        :value="old('email_retry_attempts', settings('email_retry_attempts', 3))" 
                        placeholder="3"
                        helper="How many times to retry failed emails"
                    />
                </div>
            </div>
        </x-admin.form-section>

        <!-- Test Email Section -->
        <x-admin.form-section title="Test Email Configuration" description="Send a test email to verify your settings" class="mt-6">
            <div class="space-y-4">
                <x-admin.input 
                    label="Test Email Address" 
                    name="test_email" 
                    type="email"
                    :value="old('test_email', auth()->user()->email)" 
                    placeholder="test@example.com"
                    helper="Email address to send test email to"
                />

                <x-admin.alert type="info" class="mb-4">
                    <strong>Note:</strong> Test emails will be sent using the current settings. Make sure to save your changes first if you want to test new configuration.
                </x-admin.alert>

                <div class="flex gap-3">
                    <x-admin.button type="button" color="info" onclick="sendTestEmail()"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />'>
                        Send Test Email
                    </x-admin.button>

                    <x-admin.button type="button" color="warning" onclick="testQuotationEmail()"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'>
                        Test Quotation Email
                    </x-admin.button>

                    <x-admin.button type="button" color="success" onclick="testMessageEmail()"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />'>
                        Test Message Email
                    </x-admin.button>
                </div>
            </div>
        </x-admin.form-section>

        <!-- Submit Buttons -->
        <x-admin.form-section class="mt-6">
            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <x-admin.button type="button" color="light" href="{{ route('admin.settings.index') }}">
                        Cancel
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'>
                        Save Email Settings
                    </x-admin.button>
                </div>
            </x-slot>
        </x-admin.form-section>
    </form>

    @push('scripts')
    <script>
        // Test email connection
        async function testEmailConnection() {
            const btn = document.getElementById('test-connection-btn');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Testing...';
            
            try {
                const response = await fetch('{{ route("admin.settings.email.test-connection") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Connection successful!', 'success');
                } else {
                    showNotification('Connection failed: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('Connection test failed: ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        // Send test email
        async function sendTestEmail() {
            const testEmail = document.querySelector('input[name="test_email"]').value;
            if (!testEmail) {
                showNotification('Please enter a test email address', 'warning');
                return;
            }

            try {
                const response = await fetch('{{ route("admin.settings.email.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: testEmail, type: 'general' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Test email sent successfully!', 'success');
                } else {
                    showNotification('Failed to send test email: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('Error sending test email: ' + error.message, 'error');
            }
        }

        // Test quotation email
        async function testQuotationEmail() {
            const testEmail = document.querySelector('input[name="test_email"]').value;
            if (!testEmail) {
                showNotification('Please enter a test email address', 'warning');
                return;
            }

            try {
                const response = await fetch('{{ route("admin.settings.email.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: testEmail, type: 'quotation' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Test quotation email sent successfully!', 'success');
                } else {
                    showNotification('Failed to send test quotation email: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('Error sending test quotation email: ' + error.message, 'error');
            }
        }

        // Test message email
        async function testMessageEmail() {
            const testEmail = document.querySelector('input[name="test_email"]').value;
            if (!testEmail) {
                showNotification('Please enter a test email address', 'warning');
                return;
            }

            try {
                const response = await fetch('{{ route("admin.settings.email.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: testEmail, type: 'message' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Test message email sent successfully!', 'success');
                } else {
                    showNotification('Failed to send test message email: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('Error sending test message email: ' + error.message, 'error');
            }
        }

        // Show notification
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-100 text-green-800 border-green-200' :
                type === 'error' ? 'bg-red-100 text-red-800 border-red-200' :
                'bg-yellow-100 text-yellow-800 border-yellow-200'
            } border`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-sm font-bold">×</button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Form submission with validation
        document.getElementById('email-settings-form').addEventListener('submit', function(e) {
            const requiredFields = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_from_address', 'mail_from_name'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.querySelector(`input[name="${field}"]`);
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required SMTP configuration fields', 'error');
            }
        });
    </script>
    @endpush
</x-layouts.admin>