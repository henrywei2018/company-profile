<!-- Replace the previous temp-files-display component with this dynamic version -->
<!-- resources/views/components/temp-files-display.blade.php -->

@props([
    'sessionKey' => 'temp_testimonial_images_' . session()->getId(),
    'title' => 'Uploaded',
    'emptyMessage' => 'No files uploaded yet',
    'showPreview' => true,
    'allowDelete' => true,
    'deleteEndpoint' => null,
    'gridCols' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
    'componentId' => 'temp-files-display-' . uniqid()
])

<div class="temp-files-display" id="{{ $componentId }}" x-data="tempFilesDisplay({
    sessionKey: '{{ $sessionKey }}',
    title: '{{ $title }}',
    emptyMessage: '{{ $emptyMessage }}',
    showPreview: {{ $showPreview ? 'true' : 'false' }},
    allowDelete: {{ $allowDelete ? 'true' : 'false' }},
    deleteEndpoint: '{{ $deleteEndpoint }}',
    gridCols: '{{ $gridCols }}',
    componentId: '{{ $componentId }}'
})" x-init="init()">
    
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="title"></h4>
    </div>

    <!-- Empty State -->
    <div x-show="files.length === 0" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-text="emptyMessage"></p>
    </div>

    <!-- Files Grid -->
    <div x-show="files.length > 0" :class="'grid ' + gridCols + ' gap-4'">
        <template x-for="(file, index) in files" :key="file.id || 'temp_' + index">
            <div class="temp-file-item bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200"
                 :data-file-id="file.id || 'temp_' + index">
                
                <!-- Image Preview -->
                <template x-if="showPreview && file.url && isImageFile(file.file_name)">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative group">
                        <img :src="file.url" 
                             :alt="file.file_name || 'Uploaded file'"
                             class="w-full h-full object-cover">
                        
                        <!-- Overlay on hover -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2">
                                <!-- Preview Button -->
                                <button type="button" 
                                        @click="openImagePreview(file.url, file.file_name)"
                                        class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                
                                <!-- Delete Button -->
                                <button x-show="allowDelete && deleteEndpoint" 
                                        type="button" 
                                        @click="deleteFile(file.id || 'temp_' + index)"
                                        class="p-2 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- File Icon for non-images -->
                <template x-if="!showPreview || !file.url || !isImageFile(file.file_name)">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </template>
                
                <!-- File Info -->
                <div class="p-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate" 
                               :title="file.file_name || 'Unknown file'"
                               x-text="file.file_name || 'Unknown file'"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                               x-text="file.size || file.file_size || 'Unknown size'"></p>
                        </div>
                        
                        <button x-show="allowDelete && deleteEndpoint" 
                                type="button" 
                                @click="deleteFile(file.id || 'temp_' + index)"
                                class="ml-2 p-1 text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Upload Status -->
                    <div class="mt-2 flex items-center">
                        <div class="flex items-center text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-xs">Uploaded</span>
                        </div>
                        <template x-if="file.created_at">
                            <span class="text-xs text-gray-400 ml-auto" x-text="file.created_at"></span>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Actions -->
    <div x-show="files.length > 0 && allowDelete && deleteEndpoint" class="mt-4 flex justify-end">
        <button type="button" 
                @click="clearAllFiles()"
                class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 transition-colors">
            Clear all files
        </button>
    </div>

    <!-- Image Preview Modal -->
    <div x-show="previewModal.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         @click="closeImagePreview()"
         @keydown.escape.window="closeImagePreview()">
        
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 @click.stop>
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="previewModal.title"></h3>
                        <button type="button" @click="closeImagePreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="text-center">
                        <img :src="previewModal.url" :alt="previewModal.title" class="max-w-full h-auto rounded-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function tempFilesDisplay(config) {
    return {
        // Configuration
        sessionKey: config.sessionKey,
        title: config.title,
        emptyMessage: config.emptyMessage,
        showPreview: config.showPreview,
        allowDelete: config.allowDelete,
        deleteEndpoint: config.deleteEndpoint,
        gridCols: config.gridCols,
        componentId: config.componentId,

        // State
        files: [],
        previewModal: {
            show: false,
            url: '',
            title: ''
        },

        // Initialize
        init() {
            this.loadFiles();
            this.setupEventListeners();
        },

        // Load files from server
        async loadFiles() {
            try {
                // You can create an endpoint to fetch temp files, or load from initial data
                const initialFiles = @json(session()->get($sessionKey ?? 'temp_testimonial_images_' . session()->getId(), []));
                
                // Convert single file to array format for consistency
                if (initialFiles && !Array.isArray(initialFiles)) {
                    this.files = [initialFiles];
                } else {
                    this.files = initialFiles || [];
                }
            } catch (error) {
                console.error('Failed to load temp files:', error);
                this.files = [];
            }
        },

        // Setup event listeners for universal uploader
        setupEventListeners() {
            // Listen for upload success
            document.addEventListener('files-uploaded', (event) => {
                if (event.detail.files && Array.isArray(event.detail.files)) {
                    this.addFiles(event.detail.files);
                }
            });

            // Listen for file deletion from uploader
            document.addEventListener('file-deleted', (event) => {
                if (event.detail.fileId) {
                    this.removeFileFromDisplay(event.detail.fileId);
                }
            });
        },

        // Add new files to display
        addFiles(newFiles) {
            newFiles.forEach(file => {
                // Check if file already exists
                const existingIndex = this.files.findIndex(f => f.id === file.id);
                if (existingIndex >= 0) {
                    // Update existing file
                    this.files[existingIndex] = file;
                } else {
                    // Add new file
                    this.files.push(file);
                }
            });
        },

        // Remove file from display
        removeFileFromDisplay(fileId) {
            this.files = this.files.filter(file => file.id !== fileId);
        },

        // Check if file is an image
        isImageFile(filename) {
            if (!filename) return false;
            const ext = filename.split('.').pop()?.toLowerCase();
            return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
        },

        // Open image preview
        openImagePreview(url, title) {
            this.previewModal = {
                show: true,
                url: url,
                title: title || 'Image Preview'
            };
        },

        // Close image preview
        closeImagePreview() {
            this.previewModal.show = false;
        },

        // Delete individual file
        async deleteFile(fileId) {
            if (!confirm('Are you sure you want to delete this file?')) {
                return;
            }

            try {
                const response = await fetch(this.deleteEndpoint, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ file_id: fileId })
                });

                const result = await response.json();

                if (result.success) {
                    // Remove from display
                    this.removeFileFromDisplay(fileId);
                    
                    // Show success message
                    this.showNotification(result.message || 'File deleted successfully!', 'success');
                } else {
                    this.showNotification(result.message || 'Failed to delete file', 'error');
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showNotification('An error occurred while deleting the file', 'error');
            }
        },

        // Clear all files
        async clearAllFiles() {
            if (!confirm('Are you sure you want to delete all files?')) {
                return;
            }

            for (const file of this.files) {
                await this.deleteFile(file.id);
            }
        },

        // Show notification
        showNotification(message, type = 'info') {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
            } else if (typeof window.showNotification === 'function') {
                window.showNotification(message, type);
            } else {
                // Simple fallback
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transition-all duration-300 ${this.getToastColor(type)}`;
                toast.textContent = message;
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }, 3000);
            }
        },

        // Get toast color
        getToastColor(type) {
            switch(type) {
                case 'success': return 'bg-green-500';
                case 'error': return 'bg-red-500';
                case 'warning': return 'bg-yellow-500';
                default: return 'bg-blue-500';
            }
        }
    }
}
</script>
@endpush