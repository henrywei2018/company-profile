{{-- resources/views/admin/projects/files/create.blade.php --}}
<x-layouts.admin title="Upload Project Files">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Upload Project Files</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Add files and documents to "{{ $project->title }}"
            </p>
        </div>
        
        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <x-admin.button 
                href="{{ route('admin.projects.files.index', $project) }}" 
                color="light"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Files
            </x-admin.button>
            
            <x-admin.button 
                href="{{ route('admin.projects.show', $project) }}" 
                color="info"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Project
            </x-admin.button>
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Files' => route('admin.projects.files.index', $project),
        'Upload Files' => '#'
    ]" class="mb-6" />

    <!-- Upload Guidelines -->
    <x-admin.help-text type="info" class="mb-6" dismissible>
        <x-slot name="title">File Upload Guidelines</x-slot>
        <div class="space-y-2">
            <p>• Maximum file size: <strong>10MB per file</strong></p>
            <p>• Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR</p>
            <p>• You can upload multiple files at once</p>
            <p>• Use meaningful file names and add descriptions for better organization</p>
        </div>
    </x-admin.help-text>

    <!-- Upload Form -->
    <x-admin.card>
        <x-slot name="header">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span class="font-medium">File Upload</span>
            </div>
        </x-slot>
        
        <form action="{{ route('admin.projects.files.store', $project) }}" method="POST" enctype="multipart/form-data" id="upload-form">
            @csrf
            
            <div class="space-y-6">
                <!-- File Selection Area -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Files <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Drag & Drop Zone -->
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200" id="drop-zone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="files" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload files</span>
                                    <input id="files" name="files[]" type="file" class="sr-only" multiple required 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.svg,.zip,.rar,.7z,.txt,.csv">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, PDF, DOC, XLS, ZIP up to 10MB each
                            </p>
                        </div>
                    </div>
                    
                    @error('files')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('files.*')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Selected Files Preview -->
                <div id="selected-files" class="hidden">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Selected Files:</h3>
                    <div id="file-list" class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md p-3">
                        <!-- Files will be listed here via JavaScript -->
                    </div>
                </div>
                
                <!-- File Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-admin.select
                            label="Category"
                            name="category"
                            :options="$categories"
                            helper="Organize files by category for easier management"
                        />
                    </div>
                    
                    <div class="flex items-end">
                        <x-admin.checkbox
                            label="Make files public"
                            name="is_public"
                            helper="Allow clients to download these files directly"
                        />
                    </div>
                </div>
                
                <!-- Description -->
                <x-admin.textarea
                    label="Description (Optional)"
                    name="description"
                    rows="3"
                    placeholder="Add a description for these files..."
                    helper="This description will be applied to all uploaded files"
                />
                
                <!-- Upload Progress (Hidden Initially) -->
                <div id="upload-progress" class="hidden">
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-2">
                        <span id="progress-text">Uploading files...</span>
                        <span id="progress-percentage">0%</span>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                <x-admin.button 
                    href="{{ route('admin.projects.files.index', $project) }}" 
                    color="light"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancel
                </x-admin.button>
                
                <div class="flex space-x-3">
                    <x-admin.button 
                        type="button" 
                        color="light"
                        onclick="clearFiles()"
                        id="clear-button"
                        style="display: none;"
                    >
                        Clear Files
                    </x-admin.button>
                    
                    <x-admin.button 
                        type="submit" 
                        color="primary"
                        id="upload-button"
                        disabled
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span id="upload-text">Upload Files</span>
                    </x-admin.button>
                </div>
            </div>
        </form>
    </x-admin.card>

    <!-- Upload Tips -->
    <x-admin.card class="mt-6">
        <x-slot name="header">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span class="font-medium">Upload Tips & Best Practices</span>
            </div>
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">File Organization</h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Use descriptive file names (e.g., "Project_Blueprint_v2.pdf")
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Choose appropriate categories for better organization
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Add descriptions to provide context
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Group related files together
                    </li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Security & Access</h4>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Private files are only accessible to admin users
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Public files can be downloaded by project clients
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        All uploads are scanned for security
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Download tracking is automatically enabled
                    </li>
                </ul>
            </div>
        </div>
    </x-admin.card>

    <!-- Recent Project Files (if any) -->
    @if($project->files->count() > 0)
        <x-admin.card class="mt-6">
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Recent Files in This Project</span>
                    </div>
                    <x-admin.button 
                        href="{{ route('admin.projects.files.index', $project) }}" 
                        color="light" 
                        size="sm"
                    >
                        View All Files
                    </x-admin.button>
                </div>
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($project->files->take(6) as $file)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0">
                            @if(str_starts_with($file->file_type, 'image/'))
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-800/30 rounded flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800/30 rounded flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $file->file_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $file->formatted_file_size }} • {{ $file->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif
</x-layouts.admin>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('files');
    const dropZone = document.getElementById('drop-zone');
    const selectedFiles = document.getElementById('selected-files');
    const fileList = document.getElementById('file-list');
    const uploadButton = document.getElementById('upload-button');
    const clearButton = document.getElementById('clear-button');
    const uploadForm = document.getElementById('upload-form');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const progressPercentage = document.getElementById('progress-percentage');
    
    // Store selected files for manipulation
    let selectedFilesArray = [];
    
    // File input change handler
    fileInput.addEventListener('change', handleFiles);
    
    // Drag and drop handlers
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight(e) {
        dropZone.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
    }
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = Array.from(dt.files);
        
        // Update selectedFilesArray
        selectedFilesArray = files;
        updateFileInput();
        handleFiles();
    }
    
    function updateFileInput() {
        // Create new DataTransfer object to update file input
        const dt = new DataTransfer();
        selectedFilesArray.forEach(file => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
    }
    
    function handleFiles() {
        const files = Array.from(fileInput.files);
        selectedFilesArray = files;
        
        if (files.length > 0) {
            displaySelectedFiles(files);
            uploadButton.disabled = false;
            clearButton.style.display = 'inline-flex';
        } else {
            selectedFiles.classList.add('hidden');
            uploadButton.disabled = true;
            clearButton.style.display = 'none';
        }
    }
    
    function displaySelectedFiles(files) {
        selectedFiles.classList.remove('hidden');
        
        fileList.innerHTML = files.map((file, index) => {
            const fileSize = formatFileSize(file.size);
            const fileIcon = getFileIcon(file.type);
            const validation = validateFile(file);
            const isValid = validation.valid;
            
            return `
                <div class="flex items-center justify-between p-3 ${isValid ? 'bg-white dark:bg-gray-700' : 'bg-red-50 dark:bg-red-900/20'} border ${isValid ? 'border-gray-200 dark:border-gray-600' : 'border-red-200 dark:border-red-800'} rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 ${isValid ? 'bg-gray-100 dark:bg-gray-600' : 'bg-red-100 dark:bg-red-800'} rounded flex items-center justify-center">
                                ${fileIcon}
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium ${isValid ? 'text-gray-900 dark:text-white' : 'text-red-800 dark:text-red-400'}">${file.name}</p>
                            <p class="text-xs ${isValid ? 'text-gray-500 dark:text-gray-400' : 'text-red-600 dark:text-red-500'}">${fileSize} • ${file.type || 'Unknown type'}</p>
                            ${!isValid ? `<p class="text-xs text-red-600 dark:text-red-400 mt-1">${validation.errors.join(', ')}</p>` : ''}
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }
    
    function validateFile(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',
            'text/plain',
            'text/csv',
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed'
        ];
        
        const errors = [];
        
        if (file.size > maxSize) {
            errors.push('File exceeds 10MB limit');
        }
        
        if (!allowedTypes.includes(file.type) && !file.type.startsWith('image/')) {
            errors.push('File type not supported');
        }
        
        if (file.name.length > 255) {
            errors.push('Filename too long');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    }
    
    function getFileIcon(fileType) {
        if (fileType.startsWith('image/')) {
            return '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
        } else if (fileType === 'application/pdf') {
            return '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
        } else {
            return '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Enhanced form submission with proper error handling
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const files = Array.from(fileInput.files);
        
        if (files.length === 0) {
            showError('Please select at least one file to upload.');
            return;
        }
        
        // Validate all files before upload
        const invalidFiles = files.filter(file => !validateFile(file).valid);
        if (invalidFiles.length > 0) {
            showError('Please remove invalid files before uploading.');
            return;
        }
        
        // Show progress and disable form
        showUploadProgress();
        
        // Create FormData
        const formData = new FormData(this);
        
        // Use fetch API instead of XMLHttpRequest for better error handling
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json().catch(() => {
                // If response is not JSON (redirect), handle as success
                return { success: true };
            });
        })
        .then(data => {
            if (data.success !== false) {
                showUploadSuccess();
                setTimeout(() => {
                    window.location.href = '{{ route('admin.projects.files.index', $project) }}';
                }, 1500);
            } else {
                throw new Error(data.message || 'Upload failed');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showUploadError(error.message || 'Upload failed. Please try again.');
        });
    });
    
    function showUploadProgress() {
        uploadProgress.classList.remove('hidden');
        uploadButton.disabled = true;
        document.getElementById('upload-text').textContent = 'Uploading...';
        
        // Simulate progress for better UX
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) {
                progress = 90;
                clearInterval(progressInterval);
            }
            updateProgress(progress);
        }, 200);
        
        // Store interval reference for cleanup
        uploadForm.progressInterval = progressInterval;
    }
    
    function showUploadSuccess() {
        clearInterval(uploadForm.progressInterval);
        updateProgress(100);
        progressText.textContent = 'Upload completed successfully!';
        progressBar.classList.add('bg-green-600');
        progressBar.classList.remove('bg-blue-600');
    }
    
    function showUploadError(message) {
        clearInterval(uploadForm.progressInterval);
        progressText.textContent = message;
        progressBar.classList.add('bg-red-600');
        progressBar.classList.remove('bg-blue-600');
        uploadButton.disabled = false;
        document.getElementById('upload-text').textContent = 'Upload Files';
        
        // Hide progress after 5 seconds
        setTimeout(() => {
            uploadProgress.classList.add('hidden');
            resetProgress();
        }, 5000);
    }
    
    function updateProgress(percentage) {
        progressBar.style.width = Math.min(percentage, 100) + '%';
        progressPercentage.textContent = Math.round(Math.min(percentage, 100)) + '%';
    }
    
    function resetProgress() {
        progressBar.style.width = '0%';
        progressPercentage.textContent = '0%';
        progressBar.classList.remove('bg-green-600', 'bg-red-600');
        progressBar.classList.add('bg-blue-600');
        progressText.textContent = 'Uploading files...';
    }
    
    function showError(message) {
        // Create or update error message
        let errorDiv = document.getElementById('upload-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'upload-error';
            errorDiv.className = 'mt-4 p-4 bg-red-50 border border-red-200 rounded-md';
            uploadForm.insertBefore(errorDiv, uploadForm.firstChild);
        }
        
        errorDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Upload Error</h3>
                    <div class="mt-2 text-sm text-red-700">${message}</div>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            if (errorDiv && errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 10000);
    }
    
    // Clear files function
    window.clearFiles = function() {
        selectedFilesArray = [];
        fileInput.value = '';
        selectedFiles.classList.add('hidden');
        uploadButton.disabled = true;
        clearButton.style.display = 'none';
        uploadProgress.classList.add('hidden');
        resetProgress();
        
        // Remove any error messages
        const errorDiv = document.getElementById('upload-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    };
    
    // Remove individual file function
    window.removeFile = function(index) {
        selectedFilesArray.splice(index, 1);
        updateFileInput();
        
        if (selectedFilesArray.length === 0) {
            clearFiles();
        } else {
            displaySelectedFiles(selectedFilesArray);
        }
    };
});
</script>
@endpush