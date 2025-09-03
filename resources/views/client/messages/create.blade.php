<!-- resources/views/client/messages/create.blade.php -->
<x-layouts.client title="Pesan Baru" :unreadPesans="0" :pendingQuotations="0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Pesans' => route('client.messages.index'),
            'Pesan Baru' => '#',
        ]" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <x-admin.card>
                <x-slot name="title">Kirim Pesan Baru</x-slot>
                <x-slot name="subtitle">Hubungi tim dukungan kami</x-slot>

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
                                placeholder="Ringkasan"
                                value="{{ old('subject', $prefillData['subject'] ?? '') }}"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type and Priority Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pesan Type -->
                            <input type="hidden" name="type" value="client_to_admin">
                            @if ($projects->count() > 0)
                            <div>
                                <label for="project_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Proyek Terkait (Optional)
                                </label>
                                <select id="project_id" name="project_id"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Tidak Ada</option>
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

                            <!-- Priority -->
                            <div>
                                <label for="priority"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Prioritas
                                </label>
                                <select id="priority" name="priority"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    
                                    <option value="normal"
                                        {{ old('priority', $prefillData['priority'] ?? 'normal') === 'normal' ? 'selected' : '' }}>
                                        Normal</option>
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
                        

                        <!-- Pesan Content -->
                        <div>
                            <label for="message"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Pesan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" name="message" rows="8" required maxlength="5000"
                                placeholder="Jelaskan pertanyaan Anda secara rinci..."
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('message') }}</textarea>
                            <div class="flex justify-between mt-1">
                                @error('message')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Berikan detail selengkap mungkin untuk membantu
kami membantu Anda dengan lebih baik.</p>
                                @enderror
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span id="char-count">0</span>/5000
                                </p>
                            </div>
                        </div>

                        <!-- File Attachments -->
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
                            dropDescription="Drop files here or click to browse"
                            uploadEndpoint="{{ route('client.messages.temp-upload') }}"
                            deleteEndpoint="{{ route('client.messages.temp-delete') }}"
                            :enableCategories="false"
                            :enableDescription="false"
                            :enablePublicToggle="false"
                            :autoUpload="true"
                            :uploadOnDrop="true"
                            :compact="false"
                            theme="default"
                            id="message-attachments"
                        />
                        
                        @error('attachments')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('attachments.*')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden field to store uploaded file paths -->
                    <input type="hidden" name="temp_files" id="temp_files" value="">

                    <!-- Kirim Button -->
                    <div class="flex justify-end">
                        <button type="submit" id="submit-btn"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kirim Pesan
                        </button>
                    </div>
                    </div>
                </form>
                @if($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4">
    <strong>Validation Kesalahans:</strong>
    <ul class="mt-2">
        @foreach($errors->all() as $error)
            <li>• {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
            </x-admin.card>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Tips Card -->
            <x-admin.card>
                <x-slot name="title">💡 Tips untuk pelayanan yang lebih baik</x-slot>

                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Jelaskan secara spesifik tentang masalah atau permintaan Anda</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Sertakan detail proyek yang relevan jika berlaku</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Lampirkan tangkapan layar atau dokumen yang membantu menjelaskan masalah Anda</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <p>Gunakan prioritas "Mendesak" hanya untuk masalah kritis</p>
                    </div>
                </div>
            </x-admin.card>

            <!-- Response Time Info -->
            <x-admin.card>
                <x-slot name="title">📞 Waktu Respon</x-slot>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Urgent:</span>
                        <span class="font-medium text-red-600 dark:text-red-400">Dalam 2 Jam</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">High:</span>
                        <span class="font-medium text-orange-600 dark:text-orange-400">Dalam 8 Jam</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Normal:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">Dalam 24 Jams</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Low:</span>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Dalam 48 Jam</span>
                    </div>
                </div>
            </x-admin.card>

            <!-- Recent Pesans -->
            @if (!empty($recentMessages) && count($recentMessages) > 0)
                <x-admin.card>
                    <x-slot name="title">📨 Pesan Baru</x-slot>

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
    document.getElementById('message-form').addEventListener('submit', function(e) {
    console.log('Form submitting...');
    console.log('Subject:', document.getElementById('subject').value);
    console.log('Panjang Pesan:', document.getElementById('message').value.length);
    console.log('Priority:', document.getElementById('priority').value);
    
    // Basic validation
    if (!document.getElementById('subject').value.trim()) {
        e.preventDefault();
        alert('Please enter a subject');
        return;
    }
    
    if (!document.getElementById('message').value.trim() || document.getElementById('message').value.trim().length < 10) {
        e.preventDefault();
        alert('Please enter a message (at least 10 characters)');
        return;
    }
    
    console.log('Form validation passed');
});
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
                alert('Maksimal 5 Files');
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

        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML =
                '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>Kirim Pesan';
        }, 10000);
    });

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
                console.log('Kesalahan loading draft:', e);
            }
        }
    }

    [subjectInput, messageTextarea].forEach(element => {
        element.addEventListener('input', function() {
            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(saveDraft, 2000);
        });
    });

    window.addEventListener('load', loadDraft);

    window.addEventListener('beforeunload', function() {
        if (document.querySelector('.alert-success')) {
            localStorage.removeItem('message_draft');
        }
    });
        document.addEventListener('DOMContentLoaded', function() {
            const tempFilesInput = document.getElementById('temp_files');
            const form = document.getElementById('message-form');
            let uploadedFiles = [];

            window.addEventListener('files-uploaded', function(event) {
                if (event.detail.component === 'message-attachments') {
                    if (event.detail.files) {
                        uploadedFiles.push(...event.detail.files);
                        updateTempFilesInput();
                    }
                }
            });

            window.addEventListener('file-deleted', function(event) {
                if (event.detail.component === 'message-attachments') {
                    uploadedFiles = uploadedFiles.filter(file => file.id !== event.detail.file.id);
                    updateTempFilesInput();
                }
            });

            function updateTempFilesInput() {
                const filePaths = uploadedFiles.map(file => file.path || file.file_path);
                tempFilesInput.value = JSON.stringify(filePaths);
            }

            // Form submission handling
            form.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submit-btn');
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
                        Kirim Pesan
                    `;
                }, 10000);
            });
        });
</script>