{{-- resources/views/admin/projects/files/index.blade.php --}}
<x-layouts.admin title="Project Files">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Project Files</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage files for {{ $project->title }}
            </p>
        </div>
        
        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <x-admin.button 
                href="{{ route('admin.projects.show', $project) }}" 
                color="light"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Project
            </x-admin.button>
            
            <button @click="showUploadModal = true" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Files
            </button>
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Files' => '#'
    ]" class="mb-6" />

    <!-- File Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-admin.stat-card
            title="Total Files"
            :value="$files->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
            iconColor="text-blue-500"
            iconBg="bg-blue-100 dark:bg-blue-800/30"
        />
        
        <x-admin.stat-card
            title="Total Size"
            :value="formatFileSize($totalSize)"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>'
            iconColor="text-green-500"
            iconBg="bg-green-100 dark:bg-green-800/30"
        />
        
        <x-admin.stat-card
            title="Downloads"
            :value="$totalDownloads"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
            iconColor="text-purple-500"
            iconBg="bg-purple-100 dark:bg-purple-800/30"
        />
        
        <x-admin.stat-card
            title="Categories"
            :value="$filesByCategory->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'
            iconColor="text-amber-500"
            iconBg="bg-amber-100 dark:bg-amber-800/30"
        />
    </div>

    <!-- Upload Modal -->
    <div x-data="{ showUploadModal: false }" x-init="$watch('showUploadModal', value => { if (!value) $nextTick(() => { if (window.location.search.includes('uploaded=1')) window.location.reload(); }) })">
        <x-admin.modal id="upload-files-modal" x-show="showUploadModal" @click.away="showUploadModal = false" title="Upload Files" size="xl">
            <div class="p-6">
                <x-admin.filepond-uploader 
                    :project="$project"
                    name="files"
                    :multiple="true"
                    :maxFiles="20"
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
                        'application/x-7z-compressed',
                        'application/json',
                        'application/xml'
                    ]"
                    :allowImagePreview="true"
                    :allowImageCrop="false"
                    :allowImageResize="true"
                    :imageResizeTargetWidth="1200"
                    :imageResizeTargetHeight="800"
                    dropDescription="Drop files here or click to browse"
                    category="general"
                    :isPublic="false"
                />
            </div>
            
            <x-slot name="footer">
                <x-admin.button color="light" @click="showUploadModal = false">
                    Close
                </x-admin.button>
            </x-slot>
        </x-admin.modal>
    </div>

    <!-- Files by Category -->
    @if($filesByCategory->count() > 0)
        <div class="space-y-6">
            @foreach($filesByCategory as $category => $categoryFiles)
                <x-admin.card>
                    <x-slot name="header">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst($category ?: 'Uncategorized') }}
                                </h3>
                                <x-admin.badge type="light" size="sm" class="ml-3">
                                    {{ $categoryFiles->count() }} files
                                </x-admin.badge>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button class="text-gray-400 hover:text-gray-600" onclick="toggleCategory('{{ $category }}')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </x-slot>

                    <div id="category-{{ $category }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($categoryFiles->take(9) as $file)
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex-shrink-0">
                                    @if(str_starts_with($file->file_type, 'image/'))
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @elseif($file->file_type === 'application/pdf')
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="ml-4 flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $file->file_name }}
                                    </h4>
                                    @if($file->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate">
                                            {{ $file->description }}
                                        </p>
                                    @endif
                                    <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $file->formatted_file_size }}</span>
                                        <span class="mx-1">•</span>
                                        <span>{{ $file->created_at->format('M j, Y') }}</span>
                                        @if($file->download_count > 0)
                                            <span class="mx-1">•</span>
                                            <span>{{ $file->download_count }} downloads</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="ml-4 flex-shrink-0">
                                    <div class="flex items-center space-x-2">
                                        @if(str_starts_with($file->file_type, 'image/') || $file->file_type === 'application/pdf')
                                            <a href="{{ route('admin.projects.files.preview', [$project, $file]) }}" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-800 text-xs">
                                                Preview
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('admin.projects.files.download', [$project, $file]) }}" 
                                           class="text-green-600 hover:text-green-800 text-xs">
                                            Download
                                        </a>
                                        
                                        <button onclick="deleteFile({{ $file->id }}, '{{ $file->file_name }}')"
                                                class="text-red-600 hover:text-red-800 text-xs">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($categoryFiles->count() > 9)
                            <div class="col-span-full text-center py-4">
                                <button onclick="loadMoreFiles('{{ $category }}')" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Show {{ $categoryFiles->count() - 9 }} more files →
                                </button>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endforeach
        </div>
    @else
        <x-admin.empty-state
            title="No Files Uploaded"
            description="Upload your first project files to get started."
            actionText="Upload Files"
            actionUrl="javascript:void(0)"
            @click="showUploadModal = true"
            icon='<svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>'
        />
    @endif

    <!-- Delete Confirmation Modal -->
    <x-admin.modal id="delete-file-modal" title="Delete File" size="md">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <p class="mb-4">Are you sure you want to delete this file?</p>
            <p class="font-medium text-red-600 dark:text-red-400" id="file-name-display"></p>
            <p class="mt-2 text-xs">This action cannot be undone.</p>
        </div>
        
        <x-slot name="footer">
            <x-admin.button color="light" onclick="document.getElementById('delete-file-modal').classList.add('hidden')">
                Cancel
            </x-admin.button>
            <form id="delete-file-form" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" color="danger">
                    Delete File
                </x-admin.button>
            </form>
        </x-slot>
    </x-admin.modal>
</x-layouts.admin>

@push('scripts')
<script>
// Helper function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Toggle category visibility
function toggleCategory(category) {
    const element = document.getElementById('category-' + category);
    const button = event.target.closest('button');
    const icon = button.querySelector('svg');
    
    if (element.style.display === 'none') {
        element.style.display = 'grid';
        icon.style.transform = 'rotate(0deg)';
    } else {
        element.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
    }
}

// Load more files for a category (AJAX)
function loadMoreFiles(category) {
    // Implementation for loading more files via AJAX
    fetch(`{{ route('admin.projects.files.index', $project) }}?category=${category}&load_more=true`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('category-' + category);
            container.innerHTML = data.html;
        }
    })
    .catch(error => {
        console.error('Error loading more files:', error);
    });
}

// Delete file confirmation
function deleteFile(fileId, fileName) {
    document.getElementById('file-name-display').textContent = fileName;
    document.getElementById('delete-file-form').action = 
        `{{ route('admin.projects.files.index', $project) }}/${fileId}`;
    document.getElementById('delete-file-modal').classList.remove('hidden');
}

// Real-time file upload status (if needed)
function checkUploadStatus() {
    // This could be used to check ongoing uploads
    // Implementation depends on your needs
}

// Initialize tooltips or other interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
    
    // Example: Auto-refresh after successful upload
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('uploaded') === '1') {
        // Remove the parameter and show success message
        history.replaceState({}, '', window.location.pathname);
        showNotification('success', 'Files uploaded successfully!');
    }
});

// Simple notification system
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// File search functionality
function searchFiles(query) {
    if (query.length < 2) {
        location.reload();
        return;
    }
    
    fetch(`{{ route('admin.projects.files.search', $project) }}?query=${encodeURIComponent(query)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the file display with search results
            updateFileDisplay(data.files);
        }
    })
    .catch(error => {
        console.error('Search error:', error);
    });
}

// Update file display (for search results)
function updateFileDisplay(files) {
    // Implementation to update the file list display
    // This would replace the current file grid with search results
}

// Bulk file operations
function bulkDeleteFiles() {
    const checkedFiles = document.querySelectorAll('input[name="selected_files[]"]:checked');
    if (checkedFiles.length === 0) {
        alert('Please select files to delete');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${checkedFiles.length} files?`)) {
        return;
    }
    
    const fileIds = Array.from(checkedFiles).map(cb => cb.value);
    
    fetch(`{{ route('admin.projects.files.bulk-delete', $project) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ file_ids: fileIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Delete failed');
        }
    })
    .catch(error => {
        console.error('Bulk delete error:', error);
        showNotification('error', 'Delete failed');
    });
}

// File organization
function organizeFiles() {
    // Implementation for file organization features
    // This could open a modal for bulk category assignment
}

// Export files list
function exportFilesList() {
    window.location.href = `{{ route('admin.projects.files.export', $project) }}`;
}
</script>
@endpush

@php
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
@endphp