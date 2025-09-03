
@if(in_array($message->type, ['admin_to_client', 'support_response']))
<x-admin.card class="mt-6">
    <x-slot name="title">Reply to Message</x-slot>
    
    <form action="{{ route('client.messages.reply', $message) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-4">
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
                    placeholder="Jenis your reply here..."
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Attachments -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Attachments (Optional)
                </label>
                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="attachments" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Upload files</span>
                                <input 
                                    id="attachments" 
                                    name="attachments[]" 
                                    type="file" 
                                    class="sr-only" 
                                    multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                                    onchange="displaySelectedFiles(this)"
                                >
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            PNG, JPG, PDF, DOC up to 10MB each (max 5 files)
                        </p>
                    </div>
                </div>
                
                <!-- Selected Files Display -->
                <div id="selected-files" class="mt-3 space-y-2" style="display: none;">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Files:</h4>
                    <div id="file-list" class="space-y-1"></div>
                </div>
                
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    onclick="toggleReplyForm()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Batal
                </button>
                
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Reply
                </button>
            </div>
        </div>
    </form>
</x-admin.card>

<script>
function displaySelectedFiles(input) {
    const selectedFilesDiv = document.getElementById('selected-files');
    const fileListDiv = document.getElementById('file-list');
    
    if (input.files.length > 0) {
        selectedFilesDiv.style.display = 'block';
        fileListDiv.innerHTML = '';
        
        Array.from(input.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-md text-sm';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex items-center space-x-2';
            
            const fileIcon = document.createElement('svg');
            fileIcon.className = 'w-4 h-4 text-gray-500 dark:text-gray-400';
            fileIcon.innerHTML = '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>';
            fileIcon.setAttribute('fill', 'none');
            fileIcon.setAttribute('viewBox', '0 0 24 24');
            
            const fileName = document.createElement('span');
            fileName.className = 'text-gray-700 dark:text-gray-300';
            fileName.textContent = file.name;
            
            const fileSize = document.createElement('span');
            fileSize.className = 'text-gray-500 dark:text-gray-400';
            fileSize.textContent = formatFileSize(file.size);
            
            fileInfo.appendChild(fileIcon);
            fileInfo.appendChild(fileName);
            fileInfo.appendChild(fileSize);
            
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300';
            removeButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            removeButton.onclick = () => removeFile(index, input);
            
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeButton);
            fileListDiv.appendChild(fileItem);
        });
    } else {
        selectedFilesDiv.style.display = 'none';
    }
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

function toggleReplyForm() {
    const form = document.querySelector('form[action*="reply"]').closest('.card');
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}

// Auto-save draft functionality (optional)
let autosaveTimer;
const messageTextarea = document.getElementById('message');

if (messageTextarea) {
    messageTextarea.addEventListener('input', function() {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(() => {
            // Simpan draft to localStorage (if you want this feature)
            localStorage.setItem(`reply_draft_{{ $message->id }}`, this.value);
        }, 2000);
    });
    
    // Load draft on page load
    const savedDraft = localStorage.getItem('reply_draft_{{ $message->id }}');
    if (savedDraft && !messageTextarea.value) {
        messageTextarea.value = savedDraft;
    }
}

// Clear draft after successful submission
window.addEventListener('beforeunload', function() {
    // Only clear if form was submitted successfully
    if (document.querySelector('.alert-success')) {
        localStorage.removeItem(`reply_draft_{{ $message->id }}`);
    }
});
</script>

@else
<!-- Show why reply is not available -->
<x-admin.card class="mt-6">
    <div class="text-center py-6">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Reply Not Available</h3>
        <p class="text-gray-500 dark:text-gray-400">
            You can only reply to messages from our support team.
        </p>
        <div class="mt-4">
            <x-admin.button
                href="{{ route('client.messages.create') }}"
                color="primary"
                size="sm"
            >
                Send Pesan Baru
            </x-admin.button>
        </div>
    </div>
</x-admin.card>
@endif