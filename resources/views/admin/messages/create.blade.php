{{-- resources/views/admin/messages/create.blade.php --}}
<x-layouts.admin title="Send Message" :unreadMessages="0" :pendingApprovals="0">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.messages.index') }}"
                                class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                                Messages
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-500 dark:text-gray-400">Send Message</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Send New Message</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Send a message to clients or custom email addresses
                </p>
            </div>

            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.messages.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Messages
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            <!-- Message Form -->
            <div class="xl:col-span-3 space-y-6">
                <!-- Message Form Card -->
                <x-admin.card>
                    <x-slot name="title">New Message</x-slot>
                    
                    <form action="{{ route('admin.messages.store') }}" method="POST" enctype="multipart/form-data" id="message-form">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Recipient Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Send To:
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <label class="relative flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="radio" name="send_type" value="client" 
                                               onchange="toggleRecipientType('client')" checked
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Client</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Send to existing client</div>
                                        </div>
                                    </label>
                                    
                                    <label class="relative flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="radio" name="send_type" value="custom" 
                                               onchange="toggleRecipientType('custom')"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Custom Email</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Send to any email</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Client Selection -->
                            <div id="client-selection" class="space-y-4">
                                <div>
                                    <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Select Client <span class="text-red-500">*</span>
                                    </label>
                                    <select name="client_id" id="client_id" onchange="updateClientEmail()"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Choose a client...</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" 
                                                    data-email="{{ $client->email }}" 
                                                    data-name="{{ $client->name }}"
                                                    {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} @if($client->company)({{ $client->company }})@endif - {{ $client->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Custom Email Fields -->
                            <div id="custom-email-fields" class="space-y-4 hidden">
                                <div>
                                    <label for="recipient_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="recipient_email" id="recipient_email" 
                                           value="{{ old('recipient_email') }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                           placeholder="client@example.com">
                                    @error('recipient_email')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="recipient_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Recipient Name
                                    </label>
                                    <input type="text" name="recipient_name" id="recipient_name" 
                                           value="{{ old('recipient_name') }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                           placeholder="Client Name">
                                    @error('recipient_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Message Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Priority
                                </label>
                                <select name="priority" id="priority" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject" id="subject" 
                                       value="{{ old('subject') }}"
                                       required
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       placeholder="Enter message subject">
                                @error('subject')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message Content -->
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea name="message" id="message" rows="10" 
                                          required
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                          placeholder="Enter your message here...">{{ old('message') }}</textarea>
                                <div class="mt-1 flex justify-between">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Minimum 10 characters</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400" id="char-count">0/10000</p>
                                </div>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- File Attachments - Universal File Uploader -->
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
                                        'image/jpeg',
                                        'image/png',
                                        'image/gif',
                                        'image/webp',
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'text/plain',
                                        'text/csv',
                                        'application/zip',
                                        'application/x-rar-compressed',
                                    ]"
                                    dropDescription="Drop files here or click to browse"
                                    uploadEndpoint="{{ route('admin.messages.temp-upload') }}"
                                    deleteEndpoint="{{ route('admin.messages.temp-delete') }}" 
                                    :enableCategories="false"
                                    :enableDescription="false" 
                                    :enablePublicToggle="false" 
                                    :autoUpload="true" 
                                    :uploadOnDrop="true"
                                    :compact="false" 
                                    theme="default" 
                                    id="admin-create-attachments" 
                                />

                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                
                                <!-- Hidden input for temp files -->
                                <input type="hidden" name="temp_files" id="temp_files" value="">
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" onclick="previewMessage()" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                    Preview
                                </button>
                                <button type="submit" id="send-button"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md disabled:opacity-50">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Message Templates -->
                <x-admin.card>
                    <x-slot name="title">Quick Templates</x-slot>
                    
                    <div class="space-y-2">
                        <button type="button" onclick="applyTemplate('welcome')" 
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            Welcome Message
                        </button>
                        <button type="button" onclick="applyTemplate('followup')" 
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            Follow-up
                        </button>
                        <button type="button" onclick="applyTemplate('reminder')" 
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            Project Reminder
                        </button>
                        <button type="button" onclick="applyTemplate('completion')" 
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            Project Completion
                        </button>
                    </div>
                </x-admin.card>

                <!-- Message Info -->
                <x-admin.card>
                    <x-slot name="title">Message Information</x-slot>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Type:</span>
                            <span class="font-medium text-gray-900 dark:text-white">Admin to Client</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Priority:</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="priority-display">Normal</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Recipient:</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="recipient-display">Not selected</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Attachments:</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="attachments-count">0</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Characters:</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="char-display">0</span>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Message Preview</h3>
                
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>To:</strong> <span id="preview-recipient"></span><br>
                            <strong>Subject:</strong> <span id="preview-subject"></span><br>
                            <strong>Priority:</strong> <span id="preview-priority"></span>
                        </p>
                    </div>
                    
                    <div class="prose dark:prose-invert max-w-none">
                        <div id="preview-message" class="whitespace-pre-wrap text-gray-900 dark:text-white"></div>
                    </div>
                    
                    <div id="preview-attachments" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 hidden">
                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Attachments:</h5>
                        <div id="preview-attachments-list"></div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="hidePreviewModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Close Preview
                    </button>
                    <button onclick="sendFromPreview()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                        Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Global variables following show pattern
        let adminCreateUploadedFiles = [];

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Admin messages create view loaded with universal uploader support');

            // Initialize all functionality
            initializeCreateFeatures();
            setupUniversalUploaderEvents();
            setupFormHandlers();
        });

        // Initialize create page features
        function initializeCreateFeatures() {
            // Character counter for message textarea
            const messageTextarea = document.getElementById('message');
            if (messageTextarea) {
                updateCharCount();
                messageTextarea.addEventListener('input', updateCharCount);
            }

            // Priority display update
            const prioritySelect = document.getElementById('priority');
            prioritySelect.addEventListener('change', function() {
                const priority = this.value;
                document.getElementById('priority-display').textContent = priority.charAt(0).toUpperCase() + priority.slice(1);
            });

            // Recipient display updates
            const recipientEmail = document.getElementById('recipient_email');
            const recipientName = document.getElementById('recipient_name');
            
            if (recipientEmail) {
                recipientEmail.addEventListener('input', updateRecipientDisplay);
            }
            if (recipientName) {
                recipientName.addEventListener('input', updateRecipientDisplay);
            }

            // Auto-focus message area
            if (messageTextarea && !messageTextarea.value.trim()) {
                setTimeout(() => messageTextarea.focus(), 500);
            }

            // Initialize displays
            updateCharCount();
            updateRecipientDisplay();
        }

        // Set up Universal Uploader event listeners - Following show pattern
        function setupUniversalUploaderEvents() {
            console.log('🔧 Setting up universal uploader events for create page...');

            // PRIMARY EVENT LISTENER - files-uploaded (bulk files)
            window.addEventListener('files-uploaded', function(e) {
                console.log('🎯 Create files-uploaded event captured:', e.detail);

                // Check if this event is for our create uploader
                if (isEventForCreateUploader(e.detail)) {
                    console.log('✅ Event is for create uploader, processing files...');

                    if (e.detail.files && Array.isArray(e.detail.files)) {
                        // Extract file paths from the uploaded files
                        const newFilePaths = e.detail.files.map(file => {
                            console.log('📎 Processing create file:', file);
                            return extractFilePath(file);
                        }).filter(path => path); // Filter out undefined/null paths

                        if (newFilePaths.length > 0) {
                            // Add to our tracking array
                            adminCreateUploadedFiles = [...adminCreateUploadedFiles, ...newFilePaths];

                            // Update the hidden input
                            updateTempFilesInput();

                            // Update attachments count display
                            updateAttachmentsCount();

                            console.log('📁 Create files array updated:', adminCreateUploadedFiles);
                        }
                    }
                } else {
                    console.log('❌ Event not for create uploader, ignoring');
                }
            });

            // SECONDARY EVENT LISTENER - file-uploaded (individual files)
            window.addEventListener('file-uploaded', function(e) {
                console.log('📎 Create file-uploaded event captured:', e.detail);

                if (isEventForCreateUploader(e.detail)) {
                    const filePath = extractFilePath(e.detail);
                    if (filePath) {
                        adminCreateUploadedFiles.push(filePath);
                        updateTempFilesInput();
                        updateAttachmentsCount();
                        console.log('📁 Single file added to create array:', filePath);
                    }
                }
            });

            // FILE DELETION EVENT LISTENER
            window.addEventListener('file-deleted', function(e) {
                console.log('🗑️ Create file-deleted event captured:', e.detail);

                if (isEventForCreateUploader(e.detail)) {
                    const filePathToRemove = extractFilePath(e.detail);
                    if (filePathToRemove) {
                        // Remove from tracking array
                        adminCreateUploadedFiles = adminCreateUploadedFiles.filter(path => path !== filePathToRemove);
                        updateTempFilesInput();
                        updateAttachmentsCount();
                        console.log('🗑️ File removed from create array:', filePathToRemove);
                        console.log('📁 Remaining create files:', adminCreateUploadedFiles);
                    }
                }
            });
        }

        // Check if event is for our create uploader
        function isEventForCreateUploader(eventDetail) {
            const uploaderIds = ['admin-create-attachments', 'create-attachments'];

            // Check by uploader ID
            if (eventDetail.uploaderId && uploaderIds.includes(eventDetail.uploaderId)) {
                return true;
            }

            // Check by element ID if present
            if (eventDetail.elementId && uploaderIds.includes(eventDetail.elementId)) {
                return true;
            }

            // Check by target element
            if (eventDetail.target && eventDetail.target.id && uploaderIds.includes(eventDetail.target.id)) {
                return true;
            }

            // Fallback: if we're on the admin messages create page
            const isCreatePage = window.location.pathname.includes('/admin/messages/create');
            return isCreatePage;
        }

        // Extract file path from various event formats
        function extractFilePath(eventData) {
            // Handle different data structures
            if (typeof eventData === 'string') {
                return eventData;
            }

            if (eventData && typeof eventData === 'object') {
                // Try different possible properties
                return eventData.path ||
                    eventData.file_path ||
                    eventData.filePath ||
                    eventData.url ||
                    eventData.file?.path ||
                    eventData.file?.file_path ||
                    null;
            }

            return null;
        }

        // Update the hidden temp_files input
        function updateTempFilesInput() {
            const tempFilesInput = document.getElementById('temp_files');
            if (tempFilesInput) {
                const filesJson = JSON.stringify(adminCreateUploadedFiles);
                tempFilesInput.value = filesJson;
                console.log('📝 Updated temp_files input:', filesJson);
            } else {
                console.error('❌ temp_files input not found!');
            }
        }

        // Update attachments count display
        function updateAttachmentsCount() {
            const attachmentsCount = document.getElementById('attachments-count');
            if (attachmentsCount) {
                attachmentsCount.textContent = adminCreateUploadedFiles.length;
            }
        }

        // Setup form handlers
        function setupFormHandlers() {
            const form = document.getElementById('message-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('📤 Create form submitting...');
                    console.log('📁 Files being submitted:', adminCreateUploadedFiles);
                    console.log('📝 temp_files input value:', document.getElementById('temp_files')?.value);

                    // Form validation
                    const sendType = document.querySelector('input[name="send_type"]:checked').value;
                    const subject = document.getElementById('subject').value.trim();
                    const message = document.getElementById('message').value.trim();
                    
                    let hasRecipient = false;
                    
                    if (sendType === 'client') {
                        hasRecipient = document.getElementById('client_id').value !== '';
                        if (!hasRecipient) {
                            alert('Please select a client.');
                            e.preventDefault();
                            return;
                        }
                    } else {
                        hasRecipient = document.getElementById('recipient_email').value.trim() !== '';
                        if (!hasRecipient) {
                            alert('Please enter a recipient email address.');
                            e.preventDefault();
                            return;
                        }
                    }
                    
                    if (!subject) {
                        alert('Please enter a subject.');
                        e.preventDefault();
                        return;
                    }
                    
                    if (!message || message.length < 10) {
                        alert('Please enter a message (minimum 10 characters).');
                        e.preventDefault();
                        return;
                    }

                    // Disable submit button to prevent double submission
                    const submitBtn = document.getElementById('send-button');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = `
                            <svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Sending...
                        `;

                        // Re-enable after timeout to prevent permanent disable
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 15000);
                    }
                });
            } else {
                console.log('❌ Create form not found');
            }
        }

        // Toggle recipient type
        function toggleRecipientType(type) {
            const clientSelection = document.getElementById('client-selection');
            const customEmailFields = document.getElementById('custom-email-fields');
            
            if (type === 'client') {
                clientSelection.classList.remove('hidden');
                customEmailFields.classList.add('hidden');
                
                // Clear custom email fields
                document.getElementById('recipient_email').value = '';
                document.getElementById('recipient_name').value = '';
            } else {
                clientSelection.classList.add('hidden');
                customEmailFields.classList.remove('hidden');
                
                // Clear client selection
                document.getElementById('client_id').value = '';
            }
            
            updateRecipientDisplay();
        }

        // Update client email when selection changes
        function updateClientEmail() {
            const select = document.getElementById('client_id');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const email = selectedOption.dataset.email;
                const name = selectedOption.dataset.name;
                
                // Update recipient display
                document.getElementById('recipient-display').textContent = name || email;
            } else {
                document.getElementById('recipient-display').textContent = 'Not selected';
            }
        }

        // Update recipient display
        function updateRecipientDisplay() {
            const sendType = document.querySelector('input[name="send_type"]:checked').value;
            let recipient = 'Not selected';
            
            if (sendType === 'client') {
                const select = document.getElementById('client_id');
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption.value) {
                    recipient = selectedOption.dataset.name || selectedOption.dataset.email;
                }
            } else {
                const email = document.getElementById('recipient_email').value;
                const name = document.getElementById('recipient_name').value;
                recipient = name || email || 'Not selected';
            }
            
            document.getElementById('recipient-display').textContent = recipient;
        }

        // Update character count
        function updateCharCount() {
            const messageTextarea = document.getElementById('message');
            const charCount = messageTextarea.value.length;
            const charDisplay = document.getElementById('char-display');
            const charCountDisplay = document.getElementById('char-count');
            
            if (charDisplay) {
                charDisplay.textContent = charCount;
            }
            
            if (charCountDisplay) {
                charCountDisplay.textContent = `${charCount}/10000`;
                
                // Change color based on character count
                if (charCount > 9000) {
                    charCountDisplay.className = 'text-sm text-red-500';
                } else if (charCount > 8000) {
                    charCountDisplay.className = 'text-sm text-yellow-500';
                } else {
                    charCountDisplay.className = 'text-sm text-gray-500 dark:text-gray-400';
                }
            }
        }

        // Apply message templates
        function applyTemplate(templateName) {
            const templates = {
                welcome: {
                    subject: 'Welcome to Our Service!',
                    message: 'Dear Client,\n\nWelcome to our service! We are excited to work with you.\n\nIf you have any questions, please don\'t hesitate to reach out.\n\nBest regards,\nAdmin Team'
                },
                followup: {
                    subject: 'Following Up on Your Project',
                    message: 'Dear Client,\n\nI wanted to follow up on your project and see how everything is progressing.\n\nPlease let me know if you need any assistance or have any questions.\n\nBest regards,\nAdmin Team'
                },
                reminder: {
                    subject: 'Project Reminder',
                    message: 'Dear Client,\n\nThis is a friendly reminder about your upcoming project deadline.\n\nPlease review the project details and let us know if you need any support.\n\nBest regards,\nAdmin Team'
                },
                completion: {
                    subject: 'Project Completion Notice',
                    message: 'Dear Client,\n\nWe are pleased to inform you that your project has been completed successfully.\n\nPlease review the deliverables and let us know if you have any feedback.\n\nThank you for choosing our services!\n\nBest regards,\nAdmin Team'
                }
            };
            
            const template = templates[templateName];
            if (template) {
                document.getElementById('subject').value = template.subject;
                document.getElementById('message').value = template.message;
                updateCharCount();
            }
        }

        // Preview message
        function previewMessage() {
            const sendType = document.querySelector('input[name="send_type"]:checked').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            const priority = document.getElementById('priority').value;
            
            let recipient = '';
            if (sendType === 'client') {
                const select = document.getElementById('client_id');
                const selectedOption = select.options[select.selectedIndex];
                recipient = selectedOption.value ? (selectedOption.dataset.name + ' (' + selectedOption.dataset.email + ')') : 'No client selected';
            } else {
                const email = document.getElementById('recipient_email').value;
                const name = document.getElementById('recipient_name').value;
                recipient = name ? `${name} (${email})` : email || 'No email entered';
            }
            
            if (!subject || !message) {
                alert('Please fill in subject and message before previewing.');
                return;
            }
            
            // Update preview content
            document.getElementById('preview-recipient').textContent = recipient;
            document.getElementById('preview-subject').textContent = subject;
            document.getElementById('preview-priority').textContent = priority.charAt(0).toUpperCase() + priority.slice(1);
            document.getElementById('preview-message').textContent = message;
            
            // Handle attachments preview
            if (adminCreateUploadedFiles.length > 0) {
                document.getElementById('preview-attachments').classList.remove('hidden');
                const attachmentsList = document.getElementById('preview-attachments-list');
                attachmentsList.innerHTML = '';
                
                adminCreateUploadedFiles.forEach(filePath => {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'text-sm text-gray-600 dark:text-gray-400';
                    fileDiv.textContent = filePath.split('/').pop(); // Show just filename
                    attachmentsList.appendChild(fileDiv);
                });
            } else {
                document.getElementById('preview-attachments').classList.add('hidden');
            }
            
            document.getElementById('preview-modal').classList.remove('hidden');
        }

        // Hide preview modal
        function hidePreviewModal() {
            document.getElementById('preview-modal').classList.add('hidden');
        }

        // Send from preview
        function sendFromPreview() {
            hidePreviewModal();
            document.getElementById('message-form').submit();
        }

        // Clear create form
        function clearCreateForm() {
            if (confirm('Are you sure you want to clear this form?')) {
                // Clear form fields
                document.getElementById('subject').value = '';
                document.getElementById('message').value = '';
                document.getElementById('priority').value = 'normal';
                document.getElementById('client_id').value = '';
                document.getElementById('recipient_email').value = '';
                document.getElementById('recipient_name').value = '';
                
                // Clear temp files
                const tempFilesInput = document.getElementById('temp_files');
                if (tempFilesInput) tempFilesInput.value = '';
                
                // Clear files array
                adminCreateUploadedFiles = [];
                
                // Clear universal uploader
                clearCreateUploaderInstances();
                
                // Update displays
                updateCharCount();
                updateRecipientDisplay();
                updateAttachmentsCount();
                
                showNotification('Form cleared', 'success');
            }
        }

        // Clear universal uploader instances
        function clearCreateUploaderInstances() {
            const uploaderIds = ['admin-create-attachments', 'create-attachments'];

            // Try to clear instances from global registry
            if (window.universalUploaderInstances) {
                uploaderIds.forEach(uploaderId => {
                    if (window.universalUploaderInstances[uploaderId]) {
                        try {
                            window.universalUploaderInstances[uploaderId].clearAll();
                            console.log(`🧹 Cleared create uploader instance: ${uploaderId}`);
                        } catch (e) {
                            console.warn(`⚠️ Failed to clear ${uploaderId}:`, e);
                        }
                    }
                });
            }

            // Also try Vue instances
            const uploaderElements = uploaderIds.map(id => document.getElementById(id)).filter(el => el);

            uploaderElements.forEach(element => {
                if (element && element.__vue__ && element.__vue__.clearAll) {
                    try {
                        element.__vue__.clearAll();
                        console.log(`🧹 Cleared Vue instance on ${element.id}`);
                    } catch (e) {
                        console.warn(`⚠️ Failed to clear Vue instance on ${element.id}:`, e);
                    }
                }
            });
        }

        // Show notification function (following show pattern)
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
                'bg-blue-100 text-blue-800 border border-blue-200'
            }`;

            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-1">${message}</div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-current hover:opacity-70">
                        ×
                    </button>
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

        // Debug function to check current state
        window.debugCreateUploader = function() {
            console.log('🐛 Create Uploader Debug Info:');
            console.log('📁 Current files array:', adminCreateUploadedFiles);
            console.log('📝 temp_files input value:', document.getElementById('temp_files')?.value);
            console.log('🔧 Universal uploader instances:', window.universalUploaderInstances);
            console.log('📂 DOM elements:', {
                createAttachments: document.getElementById('admin-create-attachments'),
                tempFilesInput: document.getElementById('temp_files'),
                createForm: document.getElementById('message-form')
            });
        };

        // Export functions to global scope
        window.clearCreateForm = clearCreateForm;
        window.showNotification = showNotification;
    </script>
</x-layouts.admin>