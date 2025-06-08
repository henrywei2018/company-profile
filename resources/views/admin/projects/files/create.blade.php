{{-- resources/views/admin/projects/files/create.blade.php - Enhanced Version --}}
<x-layouts.admin title="Upload Project Files">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Upload Files</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Upload files for {{ $project->title }}
            </p>
        </div>
        
        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <x-admin.button 
                href="{{ route('admin.projects.files.index', $project) }}" 
                color="light"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Files
            </x-admin.button>
            
            <x-admin.button 
                href="{{ route('admin.projects.show', $project) }}" 
                color="info"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
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
        'Upload' => '#'
    ]" class="mb-6" />

    <!-- Project Info -->
    <x-admin.card class="mb-6">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 p-6 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-6 flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $project->title }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3 text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Status:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->formatted_status }}</span>
                        </div>
                        @if($project->client)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Client:</span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->client->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Files:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->files->count() }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Created:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin.card>

    <!-- Upload Method Tabs -->
    <x-admin.card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Modern File Uploader
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Advanced drag & drop with previews, progress tracking, and instant feedback
                    </p>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Recommended</span>
                </div>
            </div>
        </x-slot>

        <x-admin.modern-file-uploader 
            :project="$project"
            name="files"
            :multiple="true"
            :maxFiles="10"
            maxFileSize="10MB"
            :acceptedFileTypes="[
                'image/*',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv',
                'application/zip',
                'application/x-rar-compressed',
                'application/x-7z-compressed'
            ]"
            dropDescription="Drop files here or click to browse"
            category="general"
            :isPublic="false"
        />
    </x-admin.card>

    <!-- Upload Guidelines -->
    <x-admin.card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Upload Guidelines
            </h3>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Supported Formats</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        PDF, Word, Excel, PowerPoint, Images (JPG, PNG, WebP), Text files, Archives (ZIP, RAR, 7Z)
                    </p>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Size Limits</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Maximum 10MB per file, up to 10 files per upload session
                    </p>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Organization</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Use categories to organize files. Add descriptions for better searchability
                    </p>
                </div>
            </div>
        </div>
    </x-admin.card>

    <!-- Current Project Files Summary -->
    @if($project->files->count() > 0)
        <x-admin.card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Recent Project Files
                    </h3>
                    <a href="{{ route('admin.projects.files.index', $project) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                        View All Files ({{ $project->files->count() }}) →
                    </a>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($project->files->take(6) as $file)
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ str_starts_with($file->file_type, 'image/') ? 'bg-green-100 dark:bg-green-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                                @if(str_starts_with($file->file_type, 'image/'))
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $file->file_name }}
                            </p>
                            <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $file->formatted_file_size }}</span>
                                <span class="mx-1">•</span>
                                <span>{{ $file->created_at->format('M j') }}</span>
                                @if($file->download_count > 0)
                                    <span class="mx-1">•</span>
                                    <span>{{ $file->download_count }} downloads</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            <a href="{{ route('admin.projects.files.download', [$project, $file]) }}" 
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-medium">
                                Download
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($project->files->count() > 6)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.projects.files.index', $project) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                        View {{ $project->files->count() - 6 }} more files →
                    </a>
                </div>
            @endif
        </x-admin.card>
    @endif
</x-layouts.admin>

@push('scripts')
<script>

// Global upload success handler
document.addEventListener('DOMContentLoaded', function() {
    // Check for upload success
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('uploaded') === '1') {
        showNotification('Files uploaded successfully!', 'success');
        // Clean URL
        history.replaceState({}, '', window.location.pathname);
    }
});

// Global notification function
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    const icons = {
        success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        error: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
        warning: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        info: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg border p-4 ${colors[type]} transform transition-all duration-300 ease-in-out`;
    notification.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    ${icons[type]}
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Enhanced form validation for traditional upload
document.querySelector('#traditional-files')?.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const maxSize = 10 * 1024 * 1024; // 10MB
    const maxFiles = 10;
    
    if (files.length > maxFiles) {
        showNotification(`Maximum ${maxFiles} files allowed`, 'warning');
        e.target.value = '';
        return;
    }
    
    const oversizedFiles = files.filter(file => file.size > maxSize);
    if (oversizedFiles.length > 0) {
        showNotification(`${oversizedFiles.length} file(s) exceed 10MB limit`, 'error');
        e.target.value = '';
        return;
    }

    const unsupportedFiles = files.filter(file => ![
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/jpg',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'text/csv',
        'application/zip',
        'application/x-rar-compressed',
        'application/x-7z-compressed'
    ].includes(file.type));

    if (unsupportedFiles.length > 0) {
        showNotification(`${unsupportedFiles.length} unsupported file type(s) detected`, 'error');
        e.target.value = '';
    }
    const dropArea = document.querySelector('#traditional-files')?.closest('div');

if (dropArea) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, e => e.preventDefault());
        dropArea.addEventListener(eventName, e => e.stopPropagation());
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.add('ring', 'ring-blue-300');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.remove('ring', 'ring-blue-300');
        });
    });

    dropArea.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        const fileInput = document.querySelector('#traditional-files');
        if (fileInput) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
}
});
</script>
@endpush