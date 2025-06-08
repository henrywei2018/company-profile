{{-- resources/views/admin/projects/files/show.blade.php --}}
<x-layouts.admin title="Project File Manager">
    <!-- Meta tags for CSRF and base URL -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="project-id" content="{{ $project->id }}">

    <!-- Sticky Header -->
    <div class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 -mx-6 px-6 py-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v0H8v0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                            File Manager
                        </h1>
                        <div class="flex items-center space-x-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ $project->title }}</span>
                            <span>•</span>
                            <span id="file-count">{{ $totalFiles }} files</span>
                            <span>•</span>
                            <span>{{ \App\Helpers\FileHelper::formatFileSize($totalSize) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 lg:mt-0 flex items-center space-x-3">
                <!-- View Toggle -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button onclick="setViewMode('grid')" 
                            id="grid-view-btn"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Grid
                    </button>
                    <button onclick="setViewMode('list')" 
                            id="list-view-btn"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        List
                    </button>
                </div>
                
                <!-- Back to Project -->
                <a href="{{ route('admin.projects.show', $project) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Project
                </a>
                
                <!-- Upload Button -->
                <a href="{{ route('admin.projects.files.create', $project) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Files
                </a>
            </div>
        </div>
    </div>

    <!-- File Manager Interface -->
    <div class="flex h-full min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex-shrink-0">
            <!-- Quick Stats -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Storage</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Used</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ \App\Helpers\FileHelper::formatFileSize($totalSize) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        @php
                            $maxStorage = 1024 * 1024 * 1024; // 1GB limit
                            $percentage = min(($totalSize / $maxStorage) * 100, 100);
                        @endphp
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $totalFiles }} files
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="file-search" 
                           placeholder="Search files..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:text-white">
                </div>
            </div>

            <!-- Filters -->
            <div class="p-4">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Categories</h4>
                <div class="space-y-2">
                    <button onclick="filterByCategory('')" 
                            class="filter-category-btn w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors active"
                            data-category="">
                        <div class="flex items-center justify-between">
                            <span>All Files</span>
                            <span class="text-xs text-gray-500">{{ $totalFiles }}</span>
                        </div>
                    </button>
                    
                    @foreach($filesByCategory as $category => $files)
                        <button onclick="filterByCategory('{{ $category }}')" 
                                class="filter-category-btn w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
                                data-category="{{ $category }}">
                            <div class="flex items-center justify-between">
                                <span>{{ ucfirst($category ?: 'General') }}</span>
                                <span class="text-xs text-gray-500">{{ $files->count() }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>

                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3 mt-6">File Types</h4>
                <div class="space-y-2">
                    @foreach($filesByType as $type => $files)
                        <button onclick="filterByType('{{ $type }}')" 
                                class="filter-type-btn w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
                                data-type="{{ $type }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($type === 'image')
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @elseif($type === 'document')
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @elseif($type === 'archive')
                                        <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H16"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @endif
                                    <span>{{ ucfirst($type) }}s</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $files->count() }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Toolbar -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Bulk Actions -->
                        <div class="flex items-center space-x-2" id="bulk-actions" style="display: none;">
                            <span class="text-sm text-gray-500 dark:text-gray-400" id="selected-count">0 selected</span>
                            <button onclick="downloadSelected()" 
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download
                            </button>
                            <button onclick="deleteSelected()" 
                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-3">
                        <select id="sort-select" class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="name-asc">Name A-Z</option>
                            <option value="name-desc">Name Z-A</option>
                            <option value="size-asc">Size (smallest first)</option>
                            <option value="size-desc">Size (largest first)</option>
                            <option value="date-desc" selected>Date (newest first)</option>
                            <option value="date-asc">Date (oldest first)</option>
                            <option value="type-asc">Type A-Z</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- File Content Area -->
            <div class="flex-1 overflow-auto bg-gray-50 dark:bg-gray-900 p-6">
                <!-- Grid View -->
                <div id="grid-view" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                    @foreach($allFiles as $file)
                        <x-admin.partials.file-grid-item :file="$file" :project="$project" />
                    @endforeach
                </div>

                <!-- List View -->
                <div id="list-view" class="hidden">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="w-8 px-6 py-3">
                                        <input type="checkbox" id="select-all-checkbox" 
                                               class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Modified</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($allFiles as $file)
                                    <x-admin.partials.file-list-item :file="$file" :project="$project" />
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="text-center py-12 hidden">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No files found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No files match your current filters.</p>
                    <div class="mt-6">
                        <button onclick="clearFilters()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="preview-title">File Preview</h3>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="preview-content" class="text-center">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600 dark:text-gray-400">Loading preview...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">Delete File</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this file?
                    </p>
                    <p class="font-medium text-red-600 dark:text-red-400 mt-2" id="delete-file-name"></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">This action cannot be undone.</p>
                </div>
                <div class="flex justify-center space-x-4 px-4 py-3">
                    <button onclick="closeDeleteModal()" 
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" 
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete File
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>

@push('scripts')
<script>
// Global variables
let selectedFiles = new Set();
let currentView = 'grid';
let deleteFileId = null;
let currentFilters = {
    search: '',
    category: '',
    type: ''
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('File Manager JavaScript loaded');
    
    // Initialize view mode
    const savedView = localStorage.getItem('fileManagerView') || 'grid';
    setViewMode(savedView);
    
    // Set default active filter
    const allFilesBtn = document.querySelector('[data-category=""]');
    if (allFilesBtn) {
        allFilesBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }
    
    // Bind events
    bindEvents();
});

function bindEvents() {
    // Search input
    const searchInput = document.getElementById('file-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentFilters.search = this.value.toLowerCase();
            applyFilters();
        });
    }
    
    // Sort select
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortFiles();
        });
    }
    
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleSelectAll();
        });
    }
    
    // Modal click outside to close
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        previewModal.addEventListener('click', function(e) {
            if (e.target === previewModal) {
                closePreview();
            }
        });
    }
    
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePreview();
            closeDeleteModal();
        }
        
        if (e.ctrlKey && e.key === 'a' && !e.target.matches('input')) {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
                toggleSelectAll();
            }
        }
        
        if (e.key === 'Delete' && selectedFiles.size > 0) {
            deleteSelected();
        }
    });
}

// View Mode Functions
function setViewMode(mode) {
    console.log('Setting view mode to:', mode);
    currentView = mode;
    
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');

    if (!gridView || !listView || !gridBtn || !listBtn) {
        console.error('View elements not found');
        return;
    }

    if (mode === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        gridBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
        listBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        listBtn.classList.add('text-gray-500', 'dark:text-gray-400');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        listBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
        gridBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm', 'dark:bg-gray-600');
        gridBtn.classList.add('text-gray-500', 'dark:text-gray-400');
    }

    localStorage.setItem('fileManagerView', mode);
    console.log('View mode set successfully');
}

// Filter Functions
function applyFilters() {
    const items = document.querySelectorAll('.file-item, .file-item-list');
    let visibleCount = 0;

    items.forEach(item => {
        const fileName = (item.dataset.fileName || '').toLowerCase();
        const fileCategory = item.dataset.fileCategory || '';
        const fileType = item.dataset.fileType || '';
        
        const matchesSearch = !currentFilters.search || fileName.includes(currentFilters.search);
        const matchesCategory = !currentFilters.category || fileCategory === currentFilters.category;
        const matchesType = !currentFilters.type || fileType === currentFilters.type;
        
        const isVisible = matchesSearch && matchesCategory && matchesType;
        
        if (isVisible) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    updateEmptyState(visibleCount);
    updateFileCount(visibleCount);
}

function filterByCategory(category) {
    console.log('Filtering by category:', category);
    
    currentFilters.category = category;
    
    // Update active filter button
    document.querySelectorAll('.filter-category-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    const activeBtn = document.querySelector(`[data-category="${category}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }

    // Clear type filter when changing category
    currentFilters.type = '';
    document.querySelectorAll('.filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });

    applyFilters();
}

function filterByType(type) {
    console.log('Filtering by type:', type);
    
    currentFilters.type = type;
    
    // Update active filter button
    document.querySelectorAll('.filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    const activeBtn = document.querySelector(`[data-type="${type}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }

    // Clear category filter when changing type
    currentFilters.category = '';
    document.querySelectorAll('.filter-category-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });

    applyFilters();
}

function clearFilters() {
    console.log('Clearing all filters');
    
    // Reset filters
    currentFilters = { search: '', category: '', type: '' };
    
    // Clear search
    const searchInput = document.getElementById('file-search');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Clear active filters
    document.querySelectorAll('.filter-category-btn, .filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    // Set "All Files" as active
    const allFilesBtn = document.querySelector('[data-category=""]');
    if (allFilesBtn) {
        allFilesBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }
    
    applyFilters();
}

function updateEmptyState(visibleCount) {
    const emptyState = document.getElementById('empty-state');
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');

    if (!emptyState) return;

    if (visibleCount === 0) {
        emptyState.classList.remove('hidden');
        if (gridView) gridView.classList.add('hidden');
        if (listView) listView.classList.add('hidden');
    } else {
        emptyState.classList.add('hidden');
        if (currentView === 'grid' && gridView) {
            gridView.classList.remove('hidden');
        } else if (currentView === 'list' && listView) {
            listView.classList.remove('hidden');
        }
    }
}

function updateFileCount(visibleCount) {
    const fileCountElement = document.getElementById('file-count');
    if (fileCountElement) {
        fileCountElement.textContent = `${visibleCount} files`;
    }
}

// Sort Functions
function sortFiles() {
    const sortSelect = document.getElementById('sort-select');
    if (!sortSelect) return;

    const sortBy = sortSelect.value;
    const [field, direction] = sortBy.split('-');
    
    const gridContainer = document.getElementById('grid-view');
    const listContainer = document.querySelector('#list-view tbody');
    
    if (gridContainer) {
        const gridItems = Array.from(gridContainer.children);
        sortItems(gridItems, gridContainer, field, direction);
    }
    
    if (listContainer) {
        const listItems = Array.from(listContainer.children);
        sortItems(listItems, listContainer, field, direction);
    }
}

function sortItems(items, container, field, direction) {
    items.sort((a, b) => {
        const aVal = getSortValue(a, field);
        const bVal = getSortValue(b, field);
        
        let comparison = 0;
        if (typeof aVal === 'string') {
            comparison = aVal.localeCompare(bVal);
        } else {
            comparison = aVal - bVal;
        }
        
        return direction === 'desc' ? -comparison : comparison;
    });

    items.forEach(item => container.appendChild(item));
}

function getSortValue(item, field) {
    switch (field) {
        case 'name':
            return item.dataset.fileName || '';
        case 'size':
            return parseInt(item.dataset.fileSize) || 0;
        case 'date':
            return parseInt(item.dataset.fileDate) || 0;
        case 'type':
            return item.dataset.fileType || '';
        default:
            return '';
    }
}

// Selection Functions
function selectFile(fileId, event) {
    if (event.ctrlKey || event.metaKey) {
        toggleSelection(fileId);
    } else if (event.shiftKey) {
        // TODO: Implement shift-select range selection
        toggleSelection(fileId);
    } else {
        selectedFiles.clear();
        document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = false);
        toggleSelection(fileId);
    }
}

function toggleSelection(fileId) {
    const checkbox = document.querySelector(`[data-file-id="${fileId}"] .file-checkbox`);
    
    if (selectedFiles.has(fileId)) {
        selectedFiles.delete(fileId);
        if (checkbox) checkbox.checked = false;
    } else {
        selectedFiles.add(fileId);
        if (checkbox) checkbox.checked = true;
    }

    updateBulkActions();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (!selectAllCheckbox) return;
    
    const isChecked = selectAllCheckbox.checked;
    
    document.querySelectorAll('.file-checkbox').forEach(checkbox => {
        const fileItem = checkbox.closest('[data-file-id]');
        if (fileItem && fileItem.style.display !== 'none') { // Only select visible items
            checkbox.checked = isChecked;
            const fileId = parseInt(fileItem.dataset.fileId);
            
            if (isChecked) {
                selectedFiles.add(fileId);
            } else {
                selectedFiles.delete(fileId);
            }
        }
    });

    updateBulkActions();
}

function updateBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (selectedFiles.size > 0) {
        if (bulkActions) bulkActions.style.display = 'flex';
        if (selectedCount) selectedCount.textContent = `${selectedFiles.size} selected`;
    } else {
        if (bulkActions) bulkActions.style.display = 'none';
    }
}

// File Actions
function downloadFile(fileId) {
    const projectId = document.querySelector('meta[name="project-id"]')?.content;
    if (!projectId) {
        console.error('Project ID not found');
        return;
    }
    
    window.location.href = `/admin/projects/${projectId}/files/${fileId}/download`;
}

function downloadSelected() {
    if (selectedFiles.size === 0) {
        showNotification('Please select files to download.', 'warning');
        return;
    }

    const projectId = document.querySelector('meta[name="project-id"]')?.content;
    if (!projectId) {
        console.error('Project ID not found');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/projects/${projectId}/files/bulk-download`;
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
    }

    selectedFiles.forEach(fileId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'file_ids[]';
        input.value = fileId;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function deleteFile(fileId, fileName) {
    deleteFileId = fileId;
    const deleteFileNameElement = document.getElementById('delete-file-name');
    if (deleteFileNameElement) {
        deleteFileNameElement.textContent = fileName;
    }
    
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) {
        deleteModal.classList.remove('hidden');
    }
}

function deleteSelected() {
    if (selectedFiles.size === 0) {
        showNotification('Please select files to delete.', 'warning');
        return;
    }

    if (confirm(`Are you sure you want to delete ${selectedFiles.size} selected file(s)? This action cannot be undone.`)) {
        const projectId = document.querySelector('meta[name="project-id"]')?.content;
        if (!projectId) {
            console.error('Project ID not found');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/projects/${projectId}/files/bulk-delete`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        selectedFiles.forEach(fileId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'file_ids[]';
            input.value = fileId;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDelete() {
    if (deleteFileId) {
        const projectId = document.querySelector('meta[name="project-id"]')?.content;
        if (!projectId) {
            console.error('Project ID not found');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/projects/${projectId}/files/${deleteFileId}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
    closeDeleteModal();
}

function closeDeleteModal() {
    const deleteModal = document.getElementById('delete-modal');
    if (deleteModal) {
        deleteModal.classList.add('hidden');
    }
    deleteFileId = null;
}

function previewFile(fileId) {
    const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
    const fileName = fileElement?.dataset.fileName || 'File';
    
    const previewTitle = document.getElementById('preview-title');
    if (previewTitle) {
        previewTitle.textContent = fileName;
    }
    
    const projectId = document.querySelector('meta[name="project-id"]')?.content;
    if (!projectId) {
        console.error('Project ID not found');
        return;
    }
    
    const previewUrl = `/admin/projects/${projectId}/files/${fileId}/preview`;
    
    // Show loading state
    const previewContent = document.getElementById('preview-content');
    if (previewContent) {
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Loading preview...</span>
            </div>
        `;
    }
    
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        previewModal.classList.remove('hidden');
    }
    
    fetch(previewUrl)
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Preview not available');
        })
        .then(html => {
            if (previewContent) {
                previewContent.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Preview error:', error);
            if (previewContent) {
                previewContent.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Preview not available for this file type.</p>
                    </div>
                `;
            }
        });
}

function closePreview() {
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        previewModal.classList.add('hidden');
    }
}

// Utility Functions
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
</script>
@endpush