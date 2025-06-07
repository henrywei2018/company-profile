{{-- resources/views/components/admin/filepond-uploader.blade.php --}}
@props([
    'project',
    'name' => 'files',
    'multiple' => true,
    'maxFiles' => 10,
    'maxFileSize' => '10MB',
    'acceptedFileTypes' => ['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.*'],
    'allowImagePreview' => true,
    'allowImageCrop' => false,
    'allowImageResize' => true,
    'imageResizeTargetWidth' => 1200,
    'imageResizeTargetHeight' => 800,
    'dropDescription' => 'Drop files here or click to browse',
    'category' => 'general',
    'isPublic' => false
])

<div x-data="filepondUploader()" x-init="initFilePond()" class="w-full">
    <!-- FilePond Input -->
    <input 
        type="file" 
        class="filepond"
        name="{{ $name }}"
        {{ $multiple ? 'multiple' : '' }}
        data-max-file-size="{{ $maxFileSize }}"
        data-max-files="{{ $maxFiles }}"
    />
    
    <!-- Additional Options -->
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Category
            </label>
            <select name="category" x-model="uploadOptions.category" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description
            </label>
            <input 
                type="text" 
                name="description" 
                x-model="uploadOptions.description"
                placeholder="Optional file description"
                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700"
            />
        </div>
    </div>
    
    <div class="mt-4">
        <label class="flex items-center">
            <input 
                type="checkbox" 
                name="is_public" 
                x-model="uploadOptions.isPublic"
                value="1"
                class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500"
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                Make files publicly accessible
            </span>
        </label>
    </div>
    
    <!-- Upload Progress -->
    <div x-show="uploading" class="mt-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
            <div class="flex items-center">
                <svg class="animate-spin h-5 w-5 text-blue-500 mr-3" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-blue-700 dark:text-blue-300">Processing files...</span>
            </div>
        </div>
    </div>
    
    <!-- Upload Results -->
    <div x-show="uploadResults.length > 0" class="mt-4">
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
            <h4 class="font-medium text-green-800 dark:text-green-300 mb-2">Upload Complete</h4>
            <ul class="text-sm text-green-700 dark:text-green-400">
                <template x-for="result in uploadResults" :key="result.id">
                    <li class="flex items-center justify-between py-1">
                        <span x-text="result.name"></span>
                        <span x-text="result.size" class="text-xs"></span>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</div>

@push('styles')
<!-- FilePond CSS -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endpush

@push('scripts')
<!-- FilePond JS -->
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>

<script>
function filepondUploader() {
    return {
        pond: null,
        uploading: false,
        uploadResults: [],
        uploadOptions: {
            category: @js($category),
            description: '',
            isPublic: @js($isPublic)
        },
        
        initFilePond() {
            // Register plugins
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
                FilePondPluginImageResize
                @endif
            );
            
            // Get the file input element
            const inputElement = this.$el.querySelector('.filepond');
            
            // Create FilePond instance
            this.pond = FilePond.create(inputElement, {
                allowMultiple: {{ $multiple ? 'true' : 'false' }},
                maxFiles: {{ $maxFiles }},
                maxFileSize: '{{ $maxFileSize }}',
                acceptedFileTypes: @json($acceptedFileTypes),
                dropDescription: '{{ $dropDescription }}',
                
                // Server configuration
                server: {
                    process: {
                        url: '{{ route("admin.projects.files.filepond.process", $project) }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        onload: (response) => {
                            // Return the server file ID
                            return response;
                        },
                        onerror: (response) => {
                            console.error('Upload error:', response);
                        }
                    },
                    revert: {
                        url: '{{ route("admin.projects.files.filepond.revert", $project) }}',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    },
                    load: (source, load, error, progress, abort, headers) => {
                        // Load existing files (for editing)
                        fetch(source, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.blob())
                        .then(load)
                        .catch(error);
                        
                        return {
                            abort: () => {
                                abort();
                            }
                        };
                    }
                },
                
                @if($allowImageResize)
                imageResizeTargetWidth: {{ $imageResizeTargetWidth }},
                imageResizeTargetHeight: {{ $imageResizeTargetHeight }},
                @endif
                
                // Event handlers
                onprocessfiles: () => {
                    this.submitFiles();
                },
                
                onprocessfilestart: () => {
                    this.uploading = true;
                    this.uploadResults = [];
                },
                
                onprocessfile: (error, file) => {
                    if (error) {
                        console.error('File processing error:', error);
                        return;
                    }
                }
            });
        },
        
        submitFiles() {
            if (!this.pond.getFiles().length) {
                return;
            }
            
            this.uploading = true;
            
            // Get server IDs from processed files
            const serverIds = this.pond.getFiles()
                .filter(file => file.serverId)
                .map(file => file.serverId);
            
            if (serverIds.length === 0) {
                this.uploading = false;
                return;
            }
            
            // Submit to permanent storage
            const formData = new FormData();
            serverIds.forEach(id => {
                formData.append('filepond_files[]', id);
            });
            formData.append('category', this.uploadOptions.category);
            formData.append('description', this.uploadOptions.description);
            formData.append('is_public', this.uploadOptions.isPublic ? '1' : '0');
            
            fetch('{{ route("admin.projects.files.filepond.submit", $project) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                this.uploading = false;
                
                if (data.success) {
                    this.uploadResults = data.files || [];
                    this.pond.removeFiles();
                    
                    // Show success message
                    this.showNotification('success', data.message);
                    
                    // Optionally refresh the page or update file list
                    setTimeout(() => {
                        if (window.location.pathname.includes('/files')) {
                            window.location.reload();
                        }
                    }, 2000);
                } else {
                    this.showNotification('error', data.message || 'Upload failed');
                }
            })
            .catch(error => {
                this.uploading = false;
                console.error('Upload error:', error);
                this.showNotification('error', 'Upload failed. Please try again.');
            });
        },
        
        showNotification(type, message) {
            // Simple notification system
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    }
}
</script>
@endpush