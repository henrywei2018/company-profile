{{-- resources/views/components/admin/filepond-uploader.blade.php --}}
@props([
    'project',
    'name' => 'filepond',
    'multiple' => true,
    'maxFiles' => 10,
    'maxFileSize' => '10MB',
    'acceptedFileTypes' => [],
    'allowImagePreview' => true,
    'allowImageCrop' => false,
    'allowImageResize' => true,
    'imageResizeTargetWidth' => 1200,
    'imageResizeTargetHeight' => 800,
    'dropDescription' => 'Drop files here or click to browse',
    'category' => 'general',
    'isPublic' => false,
    'description' => null
])

@php
    $uploadId = 'filepond-' . uniqid();
    $formId = 'filepond-form-' . uniqid();
@endphp

<div x-data="filePondUploader()" class="filepond-upload-container">
    <!-- FilePond Upload Area -->
    <div class="mb-4">
        <input type="file" 
               id="{{ $uploadId }}"
               name="{{ $name }}"
               @if($multiple) multiple @endif
               class="filepond">
    </div>

    <!-- Upload Form -->
    <form id="{{ $formId }}" 
          action="{{ route('admin.projects.files.process-filepond', $project) }}" 
          method="POST" 
          class="space-y-4">
        @csrf
        
        <!-- Hidden field for FilePond files -->
        <input type="hidden" name="filepond_files" x-model="filePondFiles">
        
        <!-- File metadata -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Category
                </label>
                <select name="category" 
                        id="category" 
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="general" {{ $category === 'general' ? 'selected' : '' }}>General</option>
                    <option value="documents" {{ $category === 'documents' ? 'selected' : '' }}>Documents</option>
                    <option value="images" {{ $category === 'images' ? 'selected' : '' }}>Images</option>
                    <option value="plans" {{ $category === 'plans' ? 'selected' : '' }}>Plans & Drawings</option>
                    <option value="contracts" {{ $category === 'contracts' ? 'selected' : '' }}>Contracts</option>
                    <option value="reports" {{ $category === 'reports' ? 'selected' : '' }}>Reports</option>
                    <option value="certificates" {{ $category === 'certificates' ? 'selected' : '' }}>Certificates</option>
                    <option value="presentations" {{ $category === 'presentations' ? 'selected' : '' }}>Presentations</option>
                    <option value="other" {{ $category === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <input type="text" 
                       name="description" 
                       id="description" 
                       value="{{ $description }}"
                       placeholder="Optional description for uploaded files"
                       class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" 
                   name="is_public" 
                   id="is_public" 
                   value="1" 
                   {{ $isPublic ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
            <label for="is_public" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                Make files publicly accessible
            </label>
        </div>
        
        <!-- Upload Progress -->
        <div x-show="uploading" class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-blue-700 dark:text-blue-300">Processing files...</span>
            </div>
        </div>
        
        <!-- Upload Button -->
        <div class="flex items-center space-x-3">
            <button type="submit" 
                    :disabled="filePondFiles.length === 0 || uploading"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Files
            </button>
            
            <span x-show="filePondFiles.length > 0" class="text-sm text-gray-600 dark:text-gray-400">
                <span x-text="filePondFiles.length"></span> file(s) ready
            </span>
        </div>
    </form>
    
    <!-- Upload Results -->
    <div x-show="uploadComplete && uploadResults.length > 0" class="mt-4">
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-400">
                        Files uploaded successfully!
                    </h3>
                    <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                        <ul class="list-disc list-inside space-y-1">
                            <template x-for="file in uploadResults" :key="file.id">
                                <li x-text="file.name"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.min.js"></script>

<script>
function filePondUploader() {
    return {
        filePondFiles: [],
        uploading: false,
        uploadComplete: false,
        uploadResults: [],
        pond: null,
        
        init() {
            this.initFilePond();
        },
        
        initFilePond() {
            // Register FilePond plugins
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                @if($allowImagePreview)
                FilePondPluginImagePreview,
                @endif
                @if($allowImageCrop)
                FilePondPluginImageCrop,
                @endif
                @if($allowImageResize)
                FilePondPluginImageResize,
                FilePondPluginImageTransform
                @endif
            );

            // Create FilePond instance
            const inputElement = document.querySelector('#{{ $uploadId }}');
            this.pond = FilePond.create(inputElement, {
                allowMultiple: {{ $multiple ? 'true' : 'false' }},
                maxFiles: {{ $maxFiles }},
                maxFileSize: '{{ $maxFileSize }}',
                @if(count($acceptedFileTypes) > 0)
                acceptedFileTypes: @json($acceptedFileTypes),
                @endif
                
                // Server configuration
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
                            console.error('Upload error:', response);
                            return response;
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
                
                // Labels
                labelIdle: '{{ $dropDescription }} <span class="filepond--label-action">Browse</span>',
                labelFileProcessing: 'Uploading',
                labelFileProcessingComplete: 'Upload complete',
                labelFileProcessingAborted: 'Upload cancelled',
                labelFileProcessingError: 'Error during upload',
                labelTapToRetry: 'Tap to retry',
                labelTapToCancel: 'Tap to cancel',
                
                @if($allowImageResize)
                // Image resize options
                imageResizeTargetWidth: {{ $imageResizeTargetWidth }},
                imageResizeTargetHeight: {{ $imageResizeTargetHeight }},
                imageResizeMode: 'contain',
                imageResizeUpscale: false,
                @endif
                
                // Callbacks
                onprocessfile: (error, file) => {
                    if (!error) {
                        this.filePondFiles.push(file.serverId);
                    }
                },
                onremovefile: (error, file) => {
                    if (!error && file.serverId) {
                        const index = this.filePondFiles.indexOf(file.serverId);
                        if (index > -1) {
                            this.filePondFiles.splice(index, 1);
                        }
                    }
                },
                onprocessfiles: () => {
                    console.log('All files processed');
                }
            });
        },
        
        async submitForm() {
            if (this.filePondFiles.length === 0) {
                alert('Please select files to upload');
                return;
            }
            
            this.uploading = true;
            this.uploadComplete = false;
            this.uploadResults = [];
            
            try {
                const form = document.querySelector('#{{ $formId }}');
                const formData = new FormData(form);
                
                // Add FilePond files as JSON
                formData.set('filepond_files', JSON.stringify(this.filePondFiles));
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.uploadResults = result.files || [];
                    this.uploadComplete = true;
                    
                    // Clear FilePond
                    this.pond.removeFiles();
                    this.filePondFiles = [];
                    
                    // Show success message
                    this.showNotification('success', result.message);
                    
                    // Optionally redirect or refresh
                    setTimeout(() => {
                        if (window.location.pathname.includes('/files/create')) {
                            window.location.href = '{{ route("admin.projects.files.index", $project) }}';
                        } else {
                            window.location.reload();
                        }
                    }, 2000);
                    
                } else {
                    this.showNotification('error', result.message || 'Upload failed');
                }
                
            } catch (error) {
                console.error('Upload error:', error);
                this.showNotification('error', 'Upload failed: ' + error.message);
            } finally {
                this.uploading = false;
            }
        },
        
        showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    }
}

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.querySelector('#{{ $formId }}').addEventListener('submit', function(e) {
        e.preventDefault();
        // Get the Alpine.js component instance
        const component = Alpine.$data(this.closest('[x-data]'));
        if (component && component.submitForm) {
            component.submitForm();
        }
    });
});
</script>
@endpush