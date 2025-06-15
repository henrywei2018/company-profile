<!-- resources/views/client/messages/create.blade.php -->
<x-layouts.client title="New Message" :unreadMessages="0" :pendingQuotations="0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('client.messages.index'),
            'New Message' => '#',
        ]" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <x-admin.card>
                <x-slot name="title">Send New Message</x-slot>
                <x-slot name="subtitle">Get in touch with our support team</x-slot>

                <form action="{{ route('client.messages.store') }}" method="POST" enctype="multipart/form-data"
                    id="message-form">
                    @csrf

                    <div class="space-y-6">
                        <!-- Subject -->
                        <div>
                            <label for="subject"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" required maxlength="255"
                                placeholder="Brief description of your inquiry"
                                value="{{ old('subject', $prefillData['subject'] ?? '') }}"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type and Priority Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Message Type -->
                            <div>
                                <label for="type"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Type
                                </label>
                                <select id="type" name="type"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @foreach ($messageTypes as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('type', $prefillData['type'] ?? 'general') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Priority
                                </label>
                                <select id="priority" name="priority"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="low"
                                        {{ old('priority', $prefillData['priority'] ?? 'normal') === 'low' ? 'selected' : '' }}>
                                        Low</option>
                                    <option value="normal"
                                        {{ old('priority', $prefillData['priority'] ?? 'normal') === 'normal' ? 'selected' : '' }}>
                                        Normal</option>
                                    <option value="high"
                                        {{ old('priority', $prefillData['priority'] ?? 'normal') === 'high' ? 'selected' : '' }}>
                                        High</option>
                                    <option value="urgent"
                                        {{ old('priority', $prefillData['priority'] ?? 'normal') === 'urgent' ? 'selected' : '' }}>
                                        Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Project Selection -->
                        @if ($projects->count() > 0)
                            <div>
                                <label for="project_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Related Project (Optional)
                                </label>
                                <select id="project_id" name="project_id"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">No specific project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ old('project_id', $prefillData['project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                            {{ $project->title }} ({{ ucfirst($project->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Message Content -->
                        <div>
                            <label for="message"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" name="message" rows="8" required maxlength="5000"
                                placeholder="Please describe your inquiry in detail..."
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('message') }}</textarea>
                            <div class="flex justify-between mt-1">
                                @error('message')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Be as detailed as possible to help
                                        us assist you better</p>
                                @enderror
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span id="char-count">0</span>/5000
                                </p>
                            </div>
                        </div>

                        <!-- File Attachments -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Attachments (Optional)
                            </label>
                            <div
                                class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label for="attachments"
                                            class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload files</span>
                                            <input id="attachments" name="attachments[]" type="file" class="sr-only"
                                                multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                                onchange="displaySelectedFiles(this)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        PNG, JPG, PDF, DOC, XLS, ZIP up to 10MB each (max 5 files)
                                    </p>
                                </div>
                            </div>

                            <!-- Selected Files Display -->
                            <div id="selected-files" class="mt-3 space-y-2" style="display: none;">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Files:</h4>
                                <div id="file-list" class="space-y-1"></div>
                            </div>

                            @error('attachments')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-admin.button href="{{ route('client.messages.index') }}" color="light" type="button">
                                Cancel
                            </x-admin.button>

                            <x-admin.button type="submit" color="primary" id="submit-btn">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Send Message
                            </x-admin.button>
                        </div>
                    </div>
                </form>
            </x-admin.card>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Tips Card -->
            <x-admin.card>
                <x-slot name="title">💡 Tips for Better Support</x-slot>

                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Be specific about your issue or request</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Include relevant project details if applicable</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Attach screenshots or documents that help explain your issue</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Use "Urgent" priority only for critical issues</p>
                    </div>
                </div>
            </x-admin.card>

            <!-- Response Time Info -->
            <x-admin.card>
                <x-slot name="title">📞 Response Times</x-slot>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Urgent:</span>
                        <span class="font-medium text-red-600 dark:text-red-400">Within 2 hours</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">High:</span>
                        <span class="font-medium text-orange-600 dark:text-orange-400">Within 8 hours</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Normal:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">Within 24 hours</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Low:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Within 48 hours</span>
                    </div>
                </div>
            </x-admin.card>

            <!-- Recent Messages -->
            @if (!empty($recentMessages) && count($recentMessages) > 0)
                <x-admin.card>
                    <x-slot name="title">📨 Recent Messages</x-slot>

                    <div class="space-y-3">
                        @foreach (array_slice($recentMessages, 0, 3) as $recentMessage)
                            <div class="text-sm">
                                <a href="{{ route('client.messages.show', $recentMessage['id']) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                    {{ Str::limit($recentMessage['title'], 40) }}
                                </a>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                    {{ $recentMessage['created_at'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </x-admin.card>
            @endif
        </div>
    </div>
</x-layouts.client>

<script>
    // Character counter for message
    document.getElementById('message').addEventListener('input', function() {
        const charCount = this.value.length;
        document.getElementById('char-count').textContent = charCount;

        if (charCount > 4500) {
            document.getElementById('char-count').classList.add('text-red-500');
        } else {
            document.getElementById('char-count').classList.remove('text-red-500');
        }
    });

    // File upload handling
    function displaySelectedFiles(input) {
        const selectedFilesDiv = document.getElementById('selected-files');
        const fileListDiv = document.getElementById('file-list');

        if (input.files.length > 0) {
            if (input.files.length > 5) {
                alert('You can only upload up to 5 files at once.');
                input.value = '';
                return;
            }

            selectedFilesDiv.style.display = 'block';
            fileListDiv.innerHTML = '';

            Array.from(input.files).forEach((file, index) => {
                // Check file size (10MB limit)
                if (file.size > 10 * 1024 * 1024) {
                    alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
                    input.value = '';
                    selectedFilesDiv.style.display = 'none';
                    return;
                }

                const fileItem = document.createElement('div');
                fileItem.className =
                    'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-sm';

                const fileInfo = document.createElement('div');
                fileInfo.className = 'flex items-center space-x-3';

                const fileIcon = getFileIcon(file.type);
                const fileName = document.createElement('span');
                fileName.className = 'text-gray-700 dark:text-gray-300 font-medium';
                fileName.textContent = file.name;

                const fileSize = document.createElement('span');
                fileSize.className = 'text-gray-500 dark:text-gray-400 text-xs';
                fileSize.textContent = formatFileSize(file.size);

                fileInfo.appendChild(fileIcon);
                fileInfo.appendChild(fileName);
                fileInfo.appendChild(fileSize);

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className =
                    'text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1';
                removeButton.innerHTML =
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                removeButton.onclick = () => removeFile(index, input);

                fileItem.appendChild(fileInfo);
                fileItem.appendChild(removeButton);
                fileListDiv.appendChild(fileItem);
            });
        } else {
            selectedFilesDiv.style.display = 'none';
        }
    }

    function getFileIcon(fileType) {
        const icon = document.createElement('div');
        icon.className = 'w-8 h-8 rounded-md flex items-center justify-center text-xs font-medium text-white';

        if (fileType.includes('pdf')) {
            icon.className += ' bg-red-500';
            icon.textContent = 'PDF';
        } else if (fileType.includes('image')) {
            icon.className += ' bg-green-500';
            icon.textContent = 'IMG';
        } else if (fileType.includes('document') || fileType.includes('word')) {
            icon.className += ' bg-blue-500';
            icon.textContent = 'DOC';
        } else if (fileType.includes('sheet') || fileType.includes('excel')) {
            icon.className += ' bg-green-600';
            icon.textContent = 'XLS';
        } else if (fileType.includes('zip') || fileType.includes('rar')) {
            icon.className += ' bg-purple-500';
            icon.textContent = 'ZIP';
        } else {
            icon.className += ' bg-gray-500';
            icon.textContent = 'FILE';
        }

        return icon;
    }

    function removeFile(indexToRemove, input) {
        const dt = new DataTransfer();
        const files = Array.from(input.files);

        files.forEach((file, index) => {
            if (index !== indexToRemove) {
                dt.items.add(file);
            }
        });

        input.files = dt.files;
        displaySelectedFiles(input);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form validation
    document.getElementById('message-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';

        // Re-enable button after 10 seconds to prevent infinite disabled state
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML =
                '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>Send Message';
        }, 10000);
    });

    // Auto-save draft (optional feature)
    let autosaveTimer;
    const messageTextarea = document.getElementById('message');
    const subjectInput = document.getElementById('subject');

    function saveDraft() {
        const draftData = {
            subject: subjectInput.value,
            message: messageTextarea.value,
            type: document.getElementById('type').value,
            priority: document.getElementById('priority').value,
            project_id: document.getElementById('project_id')?.value || null
        };

        localStorage.setItem('message_draft', JSON.stringify(draftData));
    }

    function loadDraft() {
        const saved = localStorage.getItem('message_draft');
        if (saved) {
            try {
                const draftData = JSON.parse(saved);
                if (!subjectInput.value && draftData.subject) subjectInput.value = draftData.subject;
                if (!messageTextarea.value && draftData.message) messageTextarea.value = draftData.message;
                if (draftData.type) document.getElementById('type').value = draftData.type;
                if (draftData.priority) document.getElementById('priority').value = draftData.priority;
                if (draftData.project_id && document.getElementById('project_id')) {
                    document.getElementById('project_id').value = draftData.project_id;
                }
            } catch (e) {
                console.log('Error loading draft:', e);
            }
        }
    }

    // Auto-save functionality
    [subjectInput, messageTextarea].forEach(element => {
        element.addEventListener('input', function() {
            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(saveDraft, 2000);
        });
    });

    // Load draft on page load
    window.addEventListener('load', loadDraft);

    // Clear draft on successful submission
    window.addEventListener('beforeunload', function() {
        if (document.querySelector('.alert-success')) {
            localStorage.removeItem('message_draft');
        }
    });
</script>
