{{-- resources/views/components/admin/filepond-uploader.blade.php --}}
@props([
    'project',
    'name' => 'files',
    'multiple' => true,
    'maxFiles' => 10,
    'maxFileSize' => '10MB',
    'acceptedFileTypes' => [],
    'dropDescription' => 'Drop files here or click to browse',
    'category' => 'general',
    'isPublic' => false,
    'description' => ''
])

@php
    $inputId = 'filepond-' . Str::random(8);
    $acceptedTypes = !empty($acceptedFileTypes) ? implode(',', $acceptedFileTypes) : 'image/*,application/pdf,.doc,.docx,.txt,.csv,.zip,.rar,.7z';
@endphp

<div x-data="filepondUploader('{{ $inputId }}', {{ json_encode([
    'project_id' => $project->id,
    'upload_url' => route('admin.projects.files.upload', $project),
    'delete_url' => route('admin.projects.files.delete', $project),
    'process_url' => route('admin.projects.files.process', $project),
    'csrf_token' => csrf_token(),
    'max_files' => $maxFiles,
    'max_file_size' => $maxFileSize,
    'accepted_file_types' => $acceptedTypes,
    'allow_multiple' => $multiple,
    'drop_description' => $dropDescription,
    'category' => $category,
    'is_public' => $isPublic,
    'description' => $description
]) }})" class="filepond-container">
    
    <!-- File Categories and Options -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label for="file-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Category
            </label>
            <select id="file-category" x-model="fileCategory" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="general">General</option>
                <option value="documents">Documents</option>
                <option value="images">Images</option>
                <option value="plans">Plans & Drawings</option>
                <option value="contracts">Contracts</option>
                <option value="reports">Reports</option>
                <option value="certificates">Certificates</option>
                <option value="presentations">Presentations</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <div>
            <label for="file-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description (Optional)
            </label>
            <input type="text" 
                   id="file-description" 
                   x-model="fileDescription"
                   placeholder="Brief description of files"
                   class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>
        
        <div class="flex items-end">
            <label class="flex items-center">
                <input type="checkbox" x-model="isPublic" class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make files public</span>
            </label>
        </div>
    </div>
    
    <!-- FilePond Input -->
    <input type="file" 
           id="{{ $inputId }}"
           x-ref="fileInput"
           name="{{ $name }}"
           {{ $multiple ? 'multiple' : '' }}
           accept="{{ $acceptedTypes }}"
           class="filepond">
    
    <!-- Upload Progress -->
    <div x-show="isUploading" class="mt-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-400">
                        Processing <span x-text="uploadedFiles.length"></span> file(s)...
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upload Button -->
    <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <span x-show="uploadedFiles.length > 0">
                <span x-text="uploadedFiles.length"></span> file(s) ready to upload
            </span>
        </div>
        
        <button type="button" 
                @click="submitFiles()"
                :disabled="uploadedFiles.length === 0 || isUploading"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg x-show="!isUploading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <svg x-show="isUploading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="isUploading ? 'Uploading...' : 'Upload Files'"></span>
        </button>
    </div>
</div>

@once
@push('styles')
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

<style>
.filepond--root {
    margin-bottom: 0;
}

.filepond--panel-root {
    border-radius: 0.5rem;
    border: 2px dashed #d1d5db;
    background-color: #f9fafb;
}

.dark .filepond--panel-root {
    border-color: #4b5563;
    background-color: #1f2937;
}

.filepond--drop-label {
    color: #6b7280;
}

.dark .filepond--drop-label {
    color: #9ca3af;
}

.filepond--label-action {
    color: #3b82f6;
    text-decoration: underline;
}

.dark .filepond--label-action {
    color: #60a5fa;
}

.filepond--item {
    border-radius: 0.375rem;
}

.filepond--file-action-button {
    border-radius: 50%;
}

.filepond--progress-indicator {
    color: #3b82f6;
}

.filepond--file-status {
    color: #6b7280;
}

.dark .filepond--file-status {
    color: #9ca3af;
}
</style>
@endpush

@push('scripts')
<!-- FilePond scripts -->
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>

<script>
// Register FilePond plugins
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageResize,
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize
);

function filepondUploader(inputId, config) {
    return {
        pond: null,
        uploadedFiles: [],
        isUploading: false,
        fileCategory: config.category,
        fileDescription: config.description,
        isPublic: config.is_public,
        
        init() {
            this.initFilePond();
        },
        
        initFilePond() {
            // Wait for FilePond to be available
            if (typeof FilePond === 'undefined') {
                setTimeout(() => this.initFilePond(), 100);
                return;
            }
            
            // Configure FilePond using the Laravel FilePond package endpoints
            FilePond.setOptions({
                server: {
                    process: {
                        url: config.upload_url,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': config.csrf_token,
                            'Accept': 'application/json'
                        },
                        onload: (response) => {
                            // Return the server ID for the uploaded file
                            return response;
                        },
                        onerror: (response) => {
                            console.error('Upload error:', response);
                            return response;
                        }
                    },
                    revert: {
                        url: config.delete_url,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': config.csrf_token
                        },
                        onload: (response) => {
                            return response;
                        }
                    }
                },
                allowMultiple: config.allow_multiple,
                maxFiles: config.max_files,
                maxFileSize: config.max_file_size,
                acceptedFileTypes: config.accepted_file_types.split(','),
                dropDescription: config.drop_description,
                labelIdle: `${config.drop_description} <span class="filepond--label-action">Browse</span>`,
                allowImagePreview: true,
                allowImageCrop: false,
                allowImageResize: true,
                imageResizeTargetWidth: 1200,
                imageResizeTargetHeight: 800,
                imageResizeMode: 'contain',
                stylePanelLayout: 'compact circle',
                styleLoadIndicatorPosition: 'center bottom',
                styleProgressIndicatorPosition: 'right bottom',
                styleButtonRemoveItemPosition: 'left bottom',
                styleButtonProcessItemPosition: 'right bottom',
                labelFileProcessing: 'Uploading',
                labelFileProcessingComplete: 'Upload complete',
                labelFileProcessingAborted: 'Upload cancelled',
                labelFileProcessingError: 'Error during upload',
                labelTapToCancel: 'tap to cancel',
                labelTapToRetry: 'tap to retry',
                labelTapToUndo: 'tap to undo'
            });
            
            // Create FilePond instance
            this.pond = FilePond.create(this.$refs.fileInput);
            
            // Listen for file events
            this.pond.on('addfile', (error, file) => {
                if (!error) {
                    console.log('File added:', file.filename);
                    this.updateUploadedFiles();
                }
            });
            
            this.pond.on('removefile', (error, file) => {
                console.log('File removed:', file.filename);
                this.updateUploadedFiles();
            });
            
            this.pond.on('processfile', (error, file) => {
                if (!error) {
                    console.log('File processed:', file.filename, 'Server ID:', file.serverId);
                    this.updateUploadedFiles();
                } else {
                    console.error('Process error:', error);
                }
            });
            
            this.pond.on('processfiles', () => {
                console.log('All files processed');
            });
        },
        
        updateUploadedFiles() {
            // Get files that have been successfully processed (uploaded to temp storage)
            this.uploadedFiles = this.pond.getFiles().filter(file => 
                file.status === FilePond.FileStatus.PROCESSING_COMPLETE && file.serverId
            );
            console.log('Updated uploaded files:', this.uploadedFiles.length);
        },
        
        async submitFiles() {
            if (this.uploadedFiles.length === 0) {
                this.showNotification('No files to upload', 'warning');
                return;
            }
            
            this.isUploading = true;
            
            try {
                // Get server IDs from processed files
                const serverIds = this.uploadedFiles.map(file => file.serverId);
                console.log('Submitting server IDs:', serverIds);
                
                // Prepare form data
                const formData = new FormData();
                serverIds.forEach(serverId => {
                    formData.append('filepond_files[]', serverId);
                });
                formData.append('category', this.fileCategory);
                formData.append('description', this.fileDescription);
                formData.append('is_public', this.isPublic ? '1' : '0');
                formData.append('_token', config.csrf_token);
                
                // Submit to server
                const response = await fetch(config.process_url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                console.log('Submit response:', result);
                
                if (result.success) {
                    this.showNotification(result.message, 'success');
                    
                    // Clear FilePond
                    this.pond.removeFiles();
                    this.uploadedFiles = [];
                    
                    // Redirect or refresh after success
                    setTimeout(() => {
                        window.location.href = window.location.href.replace('/create', '');
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Upload failed');
                }
                
            } catch (error) {
                console.error('Upload error:', error);
                this.showNotification(error.message || 'Upload failed', 'error');
            } finally {
                this.isUploading = false;
            }
        },
        
        showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification-toast');
            existingNotifications.forEach(notification => notification.remove());

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification-toast fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out`;
            
            let bgColor, textColor, iconSvg;
            
            switch (type) {
                case 'success':
                    bgColor = 'bg-green-50 dark:bg-green-900/20';
                    textColor = 'text-green-800 dark:text-green-400';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                    break;
                case 'error':
                    bgColor = 'bg-red-50 dark:bg-red-900/20';
                    textColor = 'text-red-800 dark:text-red-400';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                    break;
                case 'warning':
                    bgColor = 'bg-yellow-50 dark:bg-yellow-900/20';
                    textColor = 'text-yellow-800 dark:text-yellow-400';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>';
                    break;
                default:
                    bgColor = 'bg-blue-50 dark:bg-blue-900/20';
                    textColor = 'text-blue-800 dark:text-blue-400';
                    iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
            }
            
            notification.innerHTML = `
                <div class="${bgColor} p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 ${textColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${iconSvg}
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium ${textColor}">${message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button onclick="this.closest('.notification-toast').remove()" 
                                        class="inline-flex rounded-md p-1.5 ${textColor} hover:bg-black hover:bg-opacity-10 focus:outline-none">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Add to page
            document.body.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    }
}
</script>
@endonce