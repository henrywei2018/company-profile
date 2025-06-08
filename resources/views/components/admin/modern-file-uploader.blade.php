{{-- resources/views/components/admin/modern-file-uploader.blade.php --}}
@props([
    'project' => null,
    'name' => 'files',
    'multiple' => true,
    'maxFiles' => 10,
    'maxFileSize' => '10MB',
    'acceptedFileTypes' => [],
    'dropDescription' => 'Drop files here or click to browse',
    'category' => 'general',
    'isPublic' => false,
    'showPreview' => true,
    'allowImageResize' => true,
    'uploadUrl' => null,
    'deleteUrl' => null
])

@php
    $uploadUrl = $uploadUrl ?? ($project ? route('admin.projects.files.store', $project) : '#');
    $acceptedTypes = is_array($acceptedFileTypes) ? implode(',', $acceptedFileTypes) : $acceptedFileTypes;
    $maxFileSizeBytes = str_contains($maxFileSize, 'MB') ? ((int)$maxFileSize * 1024 * 1024) : (int)$maxFileSize;
    $uploaderId = 'uploader-' . uniqid();
@endphp

<div x-data="modernFileUploader({
    maxFiles: {{ $maxFiles }},
    maxFileSize: {{ $maxFileSizeBytes }},
    acceptedTypes: @js($acceptedFileTypes),
    uploadUrl: '{{ $uploadUrl }}',
    category: '{{ $category }}',
    isPublic: {{ $isPublic ? 'true' : 'false' }},
    csrfToken: '{{ csrf_token() }}'
})" 
class="modern-file-uploader" 
id="{{ $uploaderId }}">

    <!-- Upload Area -->
    <div class="upload-container" 
         :class="{ 
             'dragover': isDragOver, 
             'uploading': isUploading,
             'error': hasError 
         }"
         @dragover.prevent="isDragOver = true"
         @dragleave.prevent="isDragOver = false" 
         @drop.prevent="handleDrop($event)">
        
        <!-- Main Upload Zone -->
        <div class="upload-zone" @click="$refs.fileInput.click()">
            <div class="upload-icon" x-show="!isUploading">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            
            <!-- Upload Progress -->
            <div class="upload-progress" x-show="isUploading">
                <div class="progress-ring">
                    <svg class="w-16 h-16">
                        <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="none" 
                                :stroke-dasharray="176" 
                                :stroke-dashoffset="176 - (176 * uploadProgress / 100)"
                                class="progress-circle"/>
                    </svg>
                    <div class="progress-text" x-text="Math.round(uploadProgress) + '%'"></div>
                </div>
            </div>
            
            <div class="upload-text" x-show="!isUploading">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $dropDescription }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Maximum {{ $maxFiles }} files, {{ $maxFileSize }} per file
                </p>
                <div class="supported-formats">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        Supported: PDF, DOC, XLS, PPT, Images, Archives
                    </span>
                </div>
            </div>
            
            <button type="button" class="upload-button" x-show="!isUploading">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Choose Files
            </button>
        </div>
        
        <!-- Hidden File Input -->
        <input type="file" 
               x-ref="fileInput"
               @change="handleFileSelect($event)"
               :multiple="multiple"
               :accept="acceptedTypes"
               class="hidden">
    </div>
    
    <!-- File Preview Area -->
    <div class="file-previews" x-show="files.length > 0">
        <div class="previews-header">
            <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                Selected Files (<span x-text="files.length"></span>)
            </h4>
            <button type="button" 
                    @click="clearAllFiles()"
                    class="text-sm text-red-600 hover:text-red-800">
                Clear All
            </button>
        </div>
        
        <div class="file-grid">
            <template x-for="(file, index) in files" :key="file.id">
                <div class="file-item" 
                     :class="{ 
                         'uploading': file.status === 'uploading',
                         'success': file.status === 'uploaded',
                         'error': file.status === 'error' 
                     }">
                    
                    <!-- File Preview -->
                    <div class="file-preview">
                        <!-- Image Preview -->
                        <template x-if="file.type.startsWith('image/')">
                            <img :src="file.preview" :alt="file.name" class="preview-image">
                        </template>
                        
                        <!-- File Icon -->
                        <template x-if="!file.type.startsWith('image/')">
                            <div class="file-icon" :class="getFileTypeClass(file.type)">
                                <span x-text="getFileExtension(file.name)"></span>
                            </div>
                        </template>
                        
                        <!-- Upload Progress Overlay -->
                        <div class="upload-overlay" x-show="file.status === 'uploading'">
                            <div class="progress-bar">
                                <div class="progress-fill" :style="'width: ' + file.progress + '%'"></div>
                            </div>
                            <span class="progress-percentage" x-text="Math.round(file.progress) + '%'"></span>
                        </div>
                        
                        <!-- Status Icons -->
                        <div class="status-icon">
                            <template x-if="file.status === 'uploaded'">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="file.status === 'error'">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                        </div>
                    </div>
                    
                    <!-- File Info -->
                    <div class="file-info">
                        <div class="file-name" :title="file.name" x-text="file.name"></div>
                        <div class="file-size" x-text="formatFileSize(file.size)"></div>
                        <div class="file-status" x-show="file.status === 'error'">
                            <span class="text-red-600 text-xs" x-text="file.error"></span>
                        </div>
                    </div>
                    
                    <!-- File Actions -->
                    <div class="file-actions">
                        <button type="button" 
                                @click="removeFile(index)"
                                :disabled="file.status === 'uploading'"
                                class="action-button delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
    
    <!-- Upload Controls -->
    <div class="upload-controls" x-show="files.length > 0">
        <div class="controls-left">
            <label class="checkbox-label">
                <input type="checkbox" x-model="isPublic" class="form-checkbox">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make files public</span>
            </label>
        </div>
        
        <div class="controls-right">
            <button type="button" 
                    @click="startUpload()"
                    :disabled="isUploading || files.length === 0"
                    class="upload-submit-button">
                <svg class="w-5 h-5 mr-2" x-show="!isUploading" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <svg class="animate-spin w-5 h-5 mr-2" x-show="isUploading" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isUploading ? 'Uploading...' : 'Upload Files'"></span>
            </button>
        </div>
    </div>
    
    <!-- Global Upload Progress -->
    <div class="global-progress" x-show="isUploading && uploadProgress > 0">
        <div class="progress-bar-container">
            <div class="progress-bar">
                <div class="progress-fill" :style="'width: ' + uploadProgress + '%'"></div>
            </div>
            <span class="progress-text" x-text="'Uploading ' + uploadedCount + ' of ' + totalCount + ' files'"></span>
        </div>
    </div>
</div>

<style>
.modern-file-uploader {
    @apply w-full;
}

.upload-container {
    @apply relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg transition-all duration-300;
}

.upload-container.dragover {
    @apply border-blue-500 bg-blue-50 dark:bg-blue-900/20;
}

.upload-container.uploading {
    @apply border-blue-500;
}

.upload-container.error {
    @apply border-red-500 bg-red-50 dark:bg-red-900/20;
}

.upload-zone {
    @apply flex flex-col items-center justify-center p-12 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors;
}

.upload-icon {
    @apply mb-4;
}

.upload-progress {
    @apply relative mb-4;
}

.progress-ring {
    @apply relative flex items-center justify-center;
}

.progress-circle {
    @apply text-blue-500 transform -rotate-90 transition-all duration-300;
}

.progress-text {
    @apply absolute text-lg font-bold text-blue-600 dark:text-blue-400;
}

.upload-text {
    @apply text-center mb-6;
}

.supported-formats {
    @apply mt-2;
}

.upload-button {
    @apply inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors;
}

.file-previews {
    @apply mt-6 space-y-4;
}

.previews-header {
    @apply flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-700;
}

.file-grid {
    @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-4;
}

.file-item {
    @apply bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 transition-all duration-300;
}

.file-item.uploading {
    @apply border-blue-500 bg-blue-50 dark:bg-blue-900/20;
}

.file-item.success {
    @apply border-green-500 bg-green-50 dark:bg-green-900/20;
}

.file-item.error {
    @apply border-red-500 bg-red-50 dark:bg-red-900/20;
}

.file-preview {
    @apply relative aspect-square mb-3 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-700;
}

.preview-image {
    @apply w-full h-full object-cover;
}

.file-icon {
    @apply w-full h-full flex items-center justify-center text-white font-bold text-sm rounded;
}

.file-icon.pdf {
    @apply bg-red-500;
}

.file-icon.doc {
    @apply bg-blue-500;
}

.file-icon.xls {
    @apply bg-green-500;
}

.file-icon.ppt {
    @apply bg-orange-500;
}

.file-icon.txt {
    @apply bg-gray-500;
}

.file-icon.zip {
    @apply bg-purple-500;
}

.file-icon.default {
    @apply bg-gray-400;
}

.upload-overlay {
    @apply absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center;
}

.progress-bar {
    @apply w-3/4 h-2 bg-gray-300 rounded-full overflow-hidden mb-2;
}

.progress-fill {
    @apply h-full bg-blue-500 transition-all duration-300;
}

.progress-percentage {
    @apply text-white text-sm font-medium;
}

.status-icon {
    @apply absolute top-2 right-2;
}

.file-info {
    @apply space-y-1;
}

.file-name {
    @apply text-sm font-medium text-gray-900 dark:text-white truncate;
}

.file-size {
    @apply text-xs text-gray-500 dark:text-gray-400;
}

.file-actions {
    @apply flex justify-end mt-3;
}

.action-button {
    @apply p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors;
}

.action-button.delete {
    @apply text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/20;
}

.action-button:disabled {
    @apply opacity-50 cursor-not-allowed;
}

.upload-controls {
    @apply flex items-center justify-between mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg;
}

.checkbox-label {
    @apply flex items-center;
}

.form-checkbox {
    @apply rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500;
}

.upload-submit-button {
    @apply inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors;
}

.global-progress {
    @apply mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg;
}

.progress-bar-container {
    @apply flex items-center space-x-4;
}

.progress-text {
    @apply text-sm font-medium text-blue-800 dark:text-blue-300 whitespace-nowrap;
}
</style>

<script>
function modernFileUploader(config) {
    return {
        // Configuration
        maxFiles: config.maxFiles || 10,
        maxFileSize: config.maxFileSize || (10 * 1024 * 1024),
        acceptedTypes: config.acceptedTypes || [],
        uploadUrl: config.uploadUrl,
        category: config.category || 'general',
        isPublic: config.isPublic || false,
        csrfToken: config.csrfToken,
        
        // State
        files: [],
        isDragOver: false,
        isUploading: false,
        hasError: false,
        uploadProgress: 0,
        uploadedCount: 0,
        totalCount: 0,
        
        // Initialize
        init() {
            console.log('Modern File Uploader initialized');
        },
        
        // Handle drag and drop
        handleDrop(event) {
            this.isDragOver = false;
            const droppedFiles = Array.from(event.dataTransfer.files);
            this.processFiles(droppedFiles);
        },
        
        // Handle file selection
        handleFileSelect(event) {
            const selectedFiles = Array.from(event.target.files);
            this.processFiles(selectedFiles);
            // Clear the input to allow selecting the same files again
            event.target.value = '';
        },
        
        // Process selected files
        processFiles(fileList) {
            if (this.files.length + fileList.length > this.maxFiles) {
                this.showNotification(`Maximum ${this.maxFiles} files allowed`, 'error');
                return;
            }
            
            fileList.forEach(file => {
                if (this.validateFile(file)) {
                    const fileData = {
                        id: Date.now() + Math.random(),
                        file: file,
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        status: 'pending',
                        progress: 0,
                        error: null,
                        preview: null
                    };
                    
                    // Generate preview for images
                    if (file.type.startsWith('image/')) {
                        this.generatePreview(fileData);
                    }
                    
                    this.files.push(fileData);
                }
            });
        },
        
        // Validate individual file
        validateFile(file) {
            // Check file size
            if (file.size > this.maxFileSize) {
                this.showNotification(`File "${file.name}" exceeds maximum size of ${this.formatFileSize(this.maxFileSize)}`, 'error');
                return false;
            }
            
            // Check file type if specified
            if (this.acceptedTypes.length > 0 && !this.acceptedTypes.some(type => {
                    if (type.endsWith('/*')) {
                        return file.type.startsWith(type.slice(0, -1));
                    }
                    return file.type === type;
                })) {
                this.showNotification(`File type "${file.type}" not allowed`, 'error');
                return false;
            }
            
            return true;
        },
        
        // Generate preview for images
        generatePreview(fileData) {
            const reader = new FileReader();
            reader.onload = (e) => {
                fileData.preview = e.target.result;
                this.$nextTick(() => {
                    // Force reactivity update
                    this.files = [...this.files];
                });
            };
            reader.readAsDataURL(fileData.file);
        },
        
        // Remove file from list
        removeFile(index) {
            this.files.splice(index, 1);
        },
        
        // Clear all files
        clearAllFiles() {
            this.files = [];
            this.resetUploadState();
        },
        
        // Start upload process
        async startUpload() {
            if (this.files.length === 0) return;
            
            this.isUploading = true;
            this.uploadedCount = 0;
            this.totalCount = this.files.length;
            this.uploadProgress = 0;
            
            for (let i = 0; i < this.files.length; i++) {
                const fileData = this.files[i];
                
                if (fileData.status === 'uploaded') {
                    this.uploadedCount++;
                    continue;
                }
                
                await this.uploadSingleFile(fileData, i);
            }
            
            this.isUploading = false;
            this.showNotification(`Successfully uploaded ${this.uploadedCount} files`, 'success');
            
            // Optional: Auto-clear successfully uploaded files after a delay
            setTimeout(() => {
                this.files = this.files.filter(file => file.status !== 'uploaded');
                if (this.files.length === 0) {
                    this.resetUploadState();
                }
            }, 3000);
        },
        
        // Upload single file
        async uploadSingleFile(fileData, index) {
            try {
                fileData.status = 'uploading';
                fileData.progress = 0;
                
                const formData = new FormData();
                formData.append('files[]', fileData.file);
                formData.append('category', this.category);
                formData.append('is_public', this.isPublic ? '1' : '0');
                formData.append('_token', this.csrfToken);
                
                const response = await fetch(this.uploadUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    fileData.status = 'uploaded';
                    fileData.progress = 100;
                    this.uploadedCount++;
                } else {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Upload failed');
                }
                
            } catch (error) {
                fileData.status = 'error';
                fileData.error = error.message;
                this.showNotification(`Failed to upload ${fileData.name}: ${error.message}`, 'error');
            }
            
            // Update global progress
            this.uploadProgress = Math.round((this.uploadedCount / this.totalCount) * 100);
        },
        
        // Reset upload state
        resetUploadState() {
            this.isUploading = false;
            this.uploadProgress = 0;
            this.uploadedCount = 0;
            this.totalCount = 0;
        },
        
        // Get file type class for styling
        getFileTypeClass(mimeType) {
            if (mimeType.includes('pdf')) return 'pdf';
            if (mimeType.includes('word') || mimeType.includes('document')) return 'doc';
            if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'xls';
            if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'ppt';
            if (mimeType.includes('text')) return 'txt';
            if (mimeType.includes('zip') || mimeType.includes('archive')) return 'zip';
            return 'default';
        },
        
        // Get file extension
        getFileExtension(filename) {
            const parts = filename.split('.');
            return parts.length > 1 ? parts.pop().toUpperCase() : 'FILE';
        },
        
        // Format file size
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        // Show notification
        showNotification(message, type = 'info') {
            // Create and show notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto transform transition-all duration-300 ease-in-out ${
                type === 'success' ? 'bg-green-50 border border-green-200' :
                type === 'error' ? 'bg-red-50 border border-red-200' :
                'bg-blue-50 border border-blue-200'
            }`;
            
            notification.innerHTML = `
                <div class="p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            ${type === 'success' ? 
                                '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' :
                                type === 'error' ?
                                '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' :
                                '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                            }
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium ${
                                type === 'success' ? 'text-green-800' :
                                type === 'error' ? 'text-red-800' :
                                'text-blue-800'
                            }">${message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="this.closest('.fixed').remove()" class="inline-flex text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
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
    };
}
</script>