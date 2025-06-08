{{-- resources/views/components/admin/simple-filepond.blade.php --}}
@props([
    'project' => null,
    'name' => 'files',
    'multiple' => true,
    'maxFiles' => 10,
    'maxFileSize' => '10MB',
    'category' => 'general',
    'isPublic' => false,
    'description' => null
])

@php
    $uploaderId = 'filepond-' . uniqid();
    $projectId = $project ? $project->id : 0;
@endphp

<div x-data="simpleFilePond()" x-init="init()" class="w-full">
    <!-- FilePond Container -->
    <div class="mb-4">
        <input type="file" 
               id="{{ $uploaderId }}"
               name="{{ $name }}"
               {{ $multiple ? 'multiple' : '' }}
               class="filepond-input"
        />
    </div>

    <!-- Additional Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="file-category-{{ $uploaderId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Category
            </label>
            <select id="file-category-{{ $uploaderId }}" 
                    name="category" 
                    x-model="selectedCategory"
                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="general">General</option>
                <option value="documents">Documents</option>
                <option value="images">Images</option>
                <option value="plans">Plans & Drawings</option>
                <option value="contracts">Contracts</option>
                <option value="reports">Reports</option>
                <option value="certificates">Certificates</option>
                <option value="presentations">Presentations</option>
                <option value="specifications">Specifications</option>
                <option value="invoices">Invoices</option>
                <option value="correspondence">Correspondence</option>
                <option value="photos">Project Photos</option>
                <option value="videos">Videos</option>
                <option value="archives">Archives</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div>
            <label class="flex items-center pt-7">
                <input type="checkbox" 
                       name="is_public" 
                       x-model="isPublic"
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make files public</span>
            </label>
        </div>
    </div>

    <div class="mb-4">
        <label for="file-description-{{ $uploaderId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Description (optional)
        </label>
        <textarea id="file-description-{{ $uploaderId }}" 
                  name="description" 
                  x-model="description"
                  rows="2" 
                  placeholder="Add a description for these files..."
                  class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
    </div>

    <!-- Upload Button -->
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <span x-text="uploadedFiles.length"></span> files selected
        </div>
        
        <button type="button" 
                @click="processFiles()"
                :disabled="uploadedFiles.length === 0 || isProcessing"
                :class="{ 
                    'opacity-50 cursor-not-allowed': uploadedFiles.length === 0 || isProcessing,
                    'bg-blue-600 hover:bg-blue-700': uploadedFiles.length > 0 && !isProcessing
                }"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            <svg x-show="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="isProcessing ? 'Processing...' : 'Upload Files'"></span>
        </button>
    </div>

    <!-- Upload Progress -->
    <div x-show="isProcessing" class="mt-4">
        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                 :style="`width: ${uploadProgress}%`"></div>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 text-center">
            <span x-text="uploadProgress"></span>% complete
        </div>
    </div>

    <!-- Success Message -->
    <div x-show="uploadSuccess" x-transition class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-400" x-text="successMessage"></p>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div x-show="uploadError" x-transition class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800 dark:text-red-400" x-text="errorMessage"></p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>

<script>
function simpleFilePond() {
    return {
        pond: null,
        uploadedFiles: [],
        selectedCategory: '{{ $category }}',
        isPublic: {{ $isPublic ? 'true' : 'false' }},
        description: '{{ $description }}',
        isProcessing: false,
        uploadProgress: 0,
        uploadSuccess: false,
        uploadError: false,
        successMessage: '',
        errorMessage: '',

        init() {
            // Register FilePond plugins
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageResize,
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize
            );

            const inputElement = document.getElementById('{{ $uploaderId }}');

            // Create FilePond instance with simplified configuration
            this.pond = FilePond.create(inputElement, {
                allowMultiple: {{ $multiple ? 'true' : 'false' }},
                maxFiles: {{ $maxFiles }},
                maxFileSize: '{{ $maxFileSize }}',
                
                // Simplified file type validation - let the server handle it
                acceptedFileTypes: [
                    'image/*',
                    'application/*',
                    'text/*'
                ],
                
                allowImagePreview: true,
                allowImageCrop: false,
                allowImageResize: true,
                imageResizeTargetWidth: 1200,
                imageResizeTargetHeight: 800,
                labelIdle: 'Drop files here or <span class="filepond--label-action">Browse</span>',
                
                // Server configuration for temporary file handling
                server: {
                    process: {
                        url: '{{ route("admin.projects.files.filepond-process", $project) }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        onload: (response) => {
                            return response;
                        },
                        onerror: (response) => {
                            console.error('FilePond upload error:', response);
                            this.showError('Upload failed: ' + response);
                        }
                    },
                    revert: {
                        url: '{{ route("admin.projects.files.filepond-revert", $project) }}',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }
                },

                // Event handlers
                onaddfile: (error, file) => {
                    if (!error) {
                        this.uploadedFiles.push(file);
                        this.resetMessages();
                    } else {
                        console.error('File add error:', error);
                        this.showError('Failed to add file: ' + error.body);
                    }
                },

                onremovefile: (error, file) => {
                    if (!error) {
                        this.uploadedFiles = this.uploadedFiles.filter(f => f.id !== file.id);
                    }
                },

                onerror: (error) => {
                    console.error('FilePond error:', error);
                    this.showError('Upload error: ' + (error.body || error.message || 'Unknown error'));
                }
            });
        },

        async processFiles() {
            if (this.uploadedFiles.length === 0) {
                this.showError('No files selected');
                return;
            }

            this.isProcessing = true;
            this.uploadProgress = 0;
            this.resetMessages();

            try {
                // Get server IDs from FilePond files
                const serverIds = this.pond.getFiles()
                    .map(file => file.serverId)
                    .filter(id => id);

                if (serverIds.length === 0) {
                    throw new Error('No files were successfully uploaded to process');
                }

                // Simulate progress
                const progressInterval = setInterval(() => {
                    if (this.uploadProgress < 90) {
                        this.uploadProgress += 10;
                    }
                }, 100);

                // Process files
                const response = await fetch('{{ route("admin.projects.files.process-filepond", $project) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        filepond_files: serverIds,
                        category: this.selectedCategory,
                        description: this.description,
                        is_public: this.isPublic
                    })
                });

                clearInterval(progressInterval);
                this.uploadProgress = 100;

                const data = await response.json();

                if (data.success) {
                    this.showSuccess(data.message);
                    
                    // Clear FilePond
                    this.pond.removeFiles();
                    this.uploadedFiles = [];
                    
                    // Redirect after delay
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Upload failed');
                }

            } catch (error) {
                console.error('Processing error:', error);
                this.showError(error.message || 'Failed to process files');
            } finally {
                this.isProcessing = false;
            }
        },

        showSuccess(message) {
            this.successMessage = message;
            this.uploadSuccess = true;
            this.uploadError = false;
        },

        showError(message) {
            this.errorMessage = message;
            this.uploadError = true;
            this.uploadSuccess = false;
        },

        resetMessages() {
            this.uploadSuccess = false;
            this.uploadError = false;
            this.successMessage = '';
            this.errorMessage = '';
        }
    }
}
</script>
@endpush