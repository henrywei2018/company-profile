{{-- resources/views/client/messages/show.blade.php --}}
<x-layouts.client title="Message: {{ Str::limit($rootMessage->subject, 50) }}" :unreadMessages="0" :pendingQuotations="0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('client.messages.index'),
            'Message Details' => '#',
        ]" />

        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <!-- Quick Actions -->
            <button type="button"
                onclick="toggleMessageRead({{ $rootMessage->id }}, {{ $rootMessage->is_read ? 'false' : 'true' }})"
                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                @if ($rootMessage->is_read)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    Mark Unread
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mark Read
                @endif
            </button>

            @if ($canEscalate)
                <form action="{{ route('client.messages.mark-urgent', $rootMessage) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to mark this message as urgent?')"
                        class="inline-flex items-center px-3 py-2 border border-orange-300 dark:border-orange-600 text-sm font-medium rounded-md text-orange-700 dark:text-orange-300 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        Mark Urgent
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Message Thread Info -->
    <x-admin.card class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $rootMessage->subject }}
                </h1>
                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                    <span
                        class="px-2 py-1 bg-{{ $rootMessage->priority === 'urgent' ? 'red' : ($rootMessage->priority === 'high' ? 'orange' : 'gray') }}-100 text-{{ $rootMessage->priority === 'urgent' ? 'red' : ($rootMessage->priority === 'high' ? 'orange' : 'gray') }}-800 rounded-full text-xs">
                        {{ ucfirst($rootMessage->priority) }} Priority
                    </span>
                    <span>{{ $thread->count() }} message{{ $thread->count() !== 1 ? 's' : '' }}</span>
                    @if ($rootMessage->project)
                        <a href="{{ route('client.projects.show', $rootMessage->project) }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                            Project: {{ $rootMessage->project->title }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Started: {{ $rootMessage->created_at->format('M j, Y \a\t g:i A') }}
                </div>
                @if ($thread->count() > 1)
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Last activity: {{ $thread->last()->created_at->format('M j, Y \a\t g:i A') }}
                    </div>
                @endif
            </div>
        </div>
    </x-admin.card>

    <!-- Conversation Thread -->
    <x-admin.card class="mb-6">
        <x-slot name="title">Conversation</x-slot>

        <div class="space-y-6" id="conversation-thread">
            @foreach ($thread as $index => $threadMessage)
                <div class="flex space-x-4 {{ $threadMessage->type === 'admin_to_client' ? 'ml-8' : '' }}"
                    data-message-id="{{ $threadMessage->id }}">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 rounded-full flex items-center justify-center
                            {{ $threadMessage->type === 'admin_to_client'
                                ? 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400'
                                : 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400' }}">
                            @if ($threadMessage->type === 'admin_to_client')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="flex-1 min-w-0">
                        <div
                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 
                                    {{ $threadMessage->type === 'admin_to_client'
                                        ? 'bg-green-50 dark:bg-green-900/20'
                                        : 'bg-gray-50 dark:bg-gray-800' }}">

                            <!-- Message Header -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $threadMessage->type === 'admin_to_client' ? 'Support Team' : $threadMessage->name }}
                                    </span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full 
                                          {{ $threadMessage->type === 'admin_to_client'
                                              ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                              : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                        {{ $threadMessage->type === 'admin_to_client' ? 'Support' : 'You' }}
                                    </span>
                                    @if (!$threadMessage->is_read && $threadMessage->type === 'admin_to_client')
                                        <span
                                            class="text-xs px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                            New
                                        </span>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $threadMessage->created_at->format('M j, Y \a\t g:i A') }}
                                    @if ($index === 0)
                                        <span
                                            class="ml-2 text-xs px-2 py-1 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                                            Parent
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Message Body -->
                            <div class="text-gray-900 dark:text-white">
                                {!! nl2br(e($threadMessage->message)) !!}
                            </div>

                            <!-- Attachments -->
                            @if ($threadMessage->attachments->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Attachments ({{ $threadMessage->attachments->count() }})
                                    </h5>
                                    <div class="space-y-2">
                                        @foreach ($threadMessage->attachments as $attachment)
                                            <a href="{{ route('client.messages.attachment.download', [$threadMessage, $attachment]) }}"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                {{ $attachment->file_name }}
                                                <span class="ml-2 text-xs text-gray-500">
                                                    ({{ number_format($attachment->file_size / 1024, 1) }} KB)
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-admin.card>

    <!-- Reply Form -->
    {{-- resources/views/client/messages/show.blade.php - Reply section updated with Universal Uploader --}}
@if(in_array($message->type, ['admin_to_client', 'support_response']))
    <!-- Reply Form -->
    <x-admin.card class="mt-6">
        <x-slot name="title">Reply to Message</x-slot>
        
        <form action="{{ route('client.messages.reply', $message) }}" method="POST" enctype="multipart/form-data" id="reply-form">
            @csrf
            
            <div class="space-y-6">
                <!-- Reply Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Your Reply <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        required
                        placeholder="Type your reply here..."
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Attachments using Universal Uploader -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Attachments (Optional)
                    </label>
                    
                    <x-universal-file-uploader 
                        name="files"
                        :multiple="true"
                        :maxFiles="5"
                        maxFileSize="10MB"
                        :acceptedFileTypes="[
                            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/plain', 'text/csv',
                            'application/zip',
                            'application/x-rar-compressed'
                        ]"
                        dropDescription="Drop reply attachments here or click to browse"
                        uploadEndpoint="{{ route('client.messages.temp-upload') }}"
                        deleteEndpoint="{{ route('client.messages.temp-delete') }}"
                        :enableCategories="false"
                        :enableDescription="false"
                        :enablePublicToggle="false"
                        :autoUpload="true"
                        :uploadOnDrop="true"
                        :compact="true"
                        theme="minimal"
                        id="reply-attachments"
                    />
                    
                    @error('attachments')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden field to store uploaded file paths -->
                <input type="hidden" name="temp_files" id="reply_temp_files" value="">
                
                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('message').value = ''; document.getElementById('reply_temp_files').value = '';"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Clear
                    </button>
                    <button type="submit" id="reply-submit-btn"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Send Reply
                    </button>
                </div>
            </div>
        </form>
    </x-admin.card>

    @push('scripts')
    <script>
        // Handle reply form universal uploader events
        document.addEventListener('DOMContentLoaded', function() {
            const replyTempFilesInput = document.getElementById('reply_temp_files');
            const replyForm = document.getElementById('reply-form');
            let replyUploadedFiles = [];

            // Listen for file upload events for reply form
            window.addEventListener('files-uploaded', function(event) {
                if (event.detail.component === 'reply-attachments') {
                    // Store uploaded file information
                    if (event.detail.files) {
                        replyUploadedFiles.push(...event.detail.files);
                        updateReplyTempFilesInput();
                    }
                }
            });

            // Listen for file deletion events for reply form
            window.addEventListener('file-deleted', function(event) {
                if (event.detail.component === 'reply-attachments') {
                    // Remove deleted file from array
                    replyUploadedFiles = replyUploadedFiles.filter(file => file.id !== event.detail.file.id);
                    updateReplyTempFilesInput();
                }
            });

            function updateReplyTempFilesInput() {
                // Store file paths in hidden input for form submission
                const filePaths = replyUploadedFiles.map(file => file.path || file.file_path);
                replyTempFilesInput.value = JSON.stringify(filePaths);
            }

            // Reply form submission handling
            if (replyForm) {
                replyForm.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('reply-submit-btn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    `;

                    // Re-enable button after 10 seconds to prevent infinite disabled state
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Reply
                        `;
                    }, 10000);
                });
            }
        });
    </script>
    @endpush
@endif

    <!-- Related Messages (if any) -->
    @if ($relatedMessages->count() > 0)
        <x-admin.card class="mt-6">
            <x-slot name="title">Other Messages from This Project</x-slot>

            <div class="space-y-3">
                @foreach ($relatedMessages as $relatedMessage)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">
                                    <a href="{{ route('client.messages.show', $relatedMessage) }}"
                                        class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $relatedMessage->subject }}
                                    </a>
                                </h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ Str::limit($relatedMessage->message, 120) }}
                                </p>
                            </div>
                            <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                {{ $relatedMessage->created_at->format('M j, Y') }}
                                @if (!$relatedMessage->is_read)
                                    <span class="ml-2 inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif
</x-layouts.client>

<script>
    // Toggle message read status
    async function toggleMessageRead(messageId, markAsRead) {
        try {
            // Use the API route for consistent AJAX responses
            const response = await fetch(`{{ url('client/messages/api') }}/${messageId}/toggle-read`, {
                method: 'POST', // API route uses POST
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification('success', data.message);

                // Update the toggle button state
                updateToggleButton(messageId, data.is_read);

                // Update page title/indicator if needed
                updatePageReadStatus(data.is_read);

            } else {
                showNotification('error', data.message || 'Failed to update message status');
            }
        } catch (error) {
            console.error('Error toggling read status:', error);
            showNotification('error', 'An error occurred');
        }
    }
    // Auto-scroll to latest message on page load
    document.addEventListener('DOMContentLoaded', function() {
        const threadMessages = document.querySelectorAll('#conversation-thread > div');
        if (threadMessages.length > 1) {
            const lastMessage = threadMessages[threadMessages.length - 1];
            lastMessage.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });

    // Simple notification function
    function showNotification(type, message) {
        // Remove existing notifications
        document.querySelectorAll('.notification-toast').forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 transform transition-all duration-300 ${
            type === 'success' 
                ? 'bg-green-100 text-green-800 border border-green-200' 
                : 'bg-red-100 text-red-800 border border-red-200'
        }`;

        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    ${type === 'success' 
                        ? '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                        : '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'
                    }
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Form validation
    document.querySelector('form[action*="reply"]')?.addEventListener('submit', function(e) {
        const messageField = document.getElementById('message');
        const messageValue = messageField.value.trim();

        if (messageValue.length < 10) {
            e.preventDefault();
            showNotification('error', 'Please enter at least 10 characters for your reply.');
            messageField.focus();
            return false;
        }

        if (messageValue.length > 5000) {
            e.preventDefault();
            showNotification('error', 'Your reply is too long. Please keep it under 5000 characters.');
            messageField.focus();
            return false;
        }

        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalContent = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sending Reply...
        `;
    });

    function updateToggleButton(messageId, isRead) {
        const toggleButton = document.querySelector(`[onclick*="toggleMessageRead(${messageId}"]`);
        if (toggleButton) {
            if (isRead) {
                toggleButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Mark as Unread
            `;
                toggleButton.className = toggleButton.className.replace('bg-blue-600 hover:bg-blue-700',
                    'bg-gray-600 hover:bg-gray-700');
            } else {
                toggleButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Mark as Read
            `;
                toggleButton.className = toggleButton.className.replace('bg-gray-600 hover:bg-gray-700',
                    'bg-blue-600 hover:bg-blue-700');
            }
        }
    }

    function updatePageReadStatus(isRead) {
        // Update unread indicator in header if present
        const unreadIndicator = document.querySelector('.unread-indicator');
        if (unreadIndicator) {
            if (isRead) {
                unreadIndicator.style.display = 'none';
            } else {
                unreadIndicator.style.display = 'inline-block';
            }
        }

        // Update browser title if it shows unread status
        if (document.title.includes('[Unread]') && isRead) {
            document.title = document.title.replace('[Unread] ', '');
        } else if (!document.title.includes('[Unread]') && !isRead) {
            document.title = '[Unread] ' + document.title;
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const threadMessages = document.querySelectorAll('#conversation-thread > div');
        if (threadMessages.length > 1) {
            const lastMessage = threadMessages[threadMessages.length - 1];
            lastMessage.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });

    function showNotification(type, message) {
        // Remove existing notifications
        document.querySelectorAll('.notification-toast').forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' 
            ? 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-800 dark:text-green-100' 
            : 'bg-red-100 text-red-800 border border-red-200 dark:bg-red-800 dark:text-red-100'
    }`;

        notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                ${type === 'success' 
                    ? '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                    : '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                }
                <span>${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }
    // Handle reply form universal uploader events
        document.addEventListener('DOMContentLoaded', function() {
            const replyTempFilesInput = document.getElementById('reply_temp_files');
            const replyForm = document.getElementById('reply-form');
            let replyUploadedFiles = [];

            // Listen for file upload events for reply form
            window.addEventListener('files-uploaded', function(event) {
                if (event.detail.component === 'reply-attachments') {
                    // Store uploaded file information
                    if (event.detail.files) {
                        replyUploadedFiles.push(...event.detail.files);
                        updateReplyTempFilesInput();
                    }
                }
            });

            // Listen for file deletion events for reply form
            window.addEventListener('file-deleted', function(event) {
                if (event.detail.component === 'reply-attachments') {
                    // Remove deleted file from array
                    replyUploadedFiles = replyUploadedFiles.filter(file => file.id !== event.detail.file.id);
                    updateReplyTempFilesInput();
                }
            });

            function updateReplyTempFilesInput() {
                // Store file paths in hidden input for form submission
                const filePaths = replyUploadedFiles.map(file => file.path || file.file_path);
                replyTempFilesInput.value = JSON.stringify(filePaths);
            }

            // Reply form submission handling
            if (replyForm) {
                replyForm.addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('reply-submit-btn');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    `;

                    // Re-enable button after 10 seconds to prevent infinite disabled state
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = `
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Reply
                        `;
                    }, 10000);
                });
            }
        });
</script>
