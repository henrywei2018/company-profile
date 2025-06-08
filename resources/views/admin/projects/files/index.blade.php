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
            
            <x-admin.button 
                href="{{ route('admin.projects.files.create', $project) }}" 
                color="primary"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Files
            </x-admin.button>
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
        <x-admin.card class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $files->total() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Files</div>
                </div>
            </div>
        </x-admin.card>
        
        <x-admin.card class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-800/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatFileSize($totalSize) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Size</div>
                </div>
            </div>
        </x-admin.card>
        
        <x-admin.card class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-800/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalDownloads }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Downloads</div>
                </div>
            </div>
        </x-admin.card>
        
        <x-admin.card class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-amber-100 dark:bg-amber-800/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $filesByCategory->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Categories</div>
                </div>
            </div>
        </x-admin.card>
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
                                    <svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            :actionUrl="route('admin.projects.files.create', $project)"
            icon='<svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>'
        />
    @endif

    <!-- Delete Confirmation Modal -->
    <div id="delete-file-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Delete File</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this file?
                    </p>
                    <p class="font-medium text-red-600 dark:text-red-400 mt-2" id="file-name-display"></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">This action cannot be undone.</p>
                </div>
                <div class="flex justify-center space-x-4 px-4 py-3">
                    <button onclick="closeDeleteModal()" 
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <form id="delete-file-form" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

@push('scripts')
<script>
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

// Delete file confirmation
function deleteFile(fileId, fileName) {
    document.getElementById('file-name-display').textContent = fileName;
    document.getElementById('delete-file-form').action = 
        `{{ route('admin.projects.files.index', $project) }}/${fileId}`;
    document.getElementById('delete-file-modal').classList.remove('hidden');
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById('delete-file-modal').classList.add('hidden');
}

// Load more files for a category (AJAX)
function loadMoreFiles(category) {
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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh after successful upload
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('uploaded') === '1') {
        history.replaceState({}, '', window.location.pathname);
        showNotification('success', 'Files uploaded successfully!');
    }
    
    // Close modal when clicking outside
    document.getElementById('delete-file-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
});
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