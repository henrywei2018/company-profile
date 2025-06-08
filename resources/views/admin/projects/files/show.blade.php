{{-- resources/views/admin/projects/files/show.blade.php --}}
<x-layouts.admin title="Project Files Manager">
    <!-- Page Header -->
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
                            <span>{{ $totalFiles }} files</span>
                            <span>•</span>
                            <span>{{ formatFileSize($totalSize) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 lg:mt-0 flex items-center space-x-3">
                <!-- View Toggle -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1" role="group">
                    <button onclick="setViewMode('grid')" 
                            id="grid-view-btn"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span class="ml-1">Grid</span>
                    </button>
                    <button onclick="setViewMode('list')" 
                            id="list-view-btn"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span class="ml-1">List</span>
                    </button>
                </div>
                
                <!-- Upload Button -->
                <a href="{{ route('admin.projects.files.create', $project) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Files
                </a>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Files' => route('admin.projects.files.index', $project),
        'File Manager' => '#'
    ]" class="mb-6" />

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
                        <span class="font-medium text-gray-900 dark:text-white">{{ formatFileSize($totalSize) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        @php
                            $maxStorage = 1024 * 1024 * 1024; // 1GB for example
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
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           onkeyup="filterFiles()">
                </div>
            </div>

            <!-- Filters -->
            <div class="p-4">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Categories</h4>
                <div class="space-y-2">
                    <button onclick="filterByCategory('')" 
                            class="filter-category-btn w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
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
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download
                            </button>
                            <button onclick="deleteSelected()" 
                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-700 dark:border-red-600 dark:text-red-400 dark:hover:bg-red-900/20">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-3">
                        <select id="sort-select" onchange="sortFiles()" class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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
                        <div class="file-item bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow cursor-pointer group"
                             data-file-id="{{ $file->id }}"
                             data-file-name="{{ strtolower($file->file_name) }}"
                             data-file-category="{{ $file->category ?? '' }}"
                             data-file-type="{{ $file->file_category }}"
                             data-file-size="{{ $file->file_size }}"
                             data-file-date="{{ $file->created_at->timestamp }}"
                             onclick="selectFile({{ $file->id }}, event)">
                            
                            <!-- Checkbox -->
                            <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <input type="checkbox" 
                                       class="file-checkbox rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500"
                                       onchange="toggleSelection({{ $file->id }})"
                                       onclick="event.stopPropagation()">
                            </div>

                            <!-- File Icon/Preview -->
                            <div class="flex flex-col items-center mb-3">
                                @if(str_starts_with($file->file_type, 'image/') && Storage::disk('public')->exists($file->file_path))
                                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        <img src="{{ Storage::url($file->file_path) }}" 
                                             alt="{{ $file->file_name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-16 h-16 rounded-lg flex items-center justify-center {{ 
                                        $file->file_category === 'document' ? 'bg-blue-100 dark:bg-blue-900/30' : 
                                        ($file->file_category === 'image' ? 'bg-green-100 dark:bg-green-900/30' : 
                                        ($file->file_category === 'archive' ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-gray-100 dark:bg-gray-700'))
                                    }}">
                                        @if($file->file_category === 'document')
                                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @elseif($file->file_category === 'image')
                                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @elseif($file->file_category === 'archive')
                                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H16"/>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- File Info -->
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $file->file_name }}">
                                    {{ $file->file_name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $file->formatted_file_size }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $file->created_at->format('M j, Y') }}
                                </p>
                            </div>

                            <!-- Quick Actions -->
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex items-center space-x-1">
                                    <button onclick="downloadFile({{ $file->id }}); event.stopPropagation();" 
                                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                            title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deleteFile({{ $file->id }}, '{{ $file->file_name }}'); event.stopPropagation();" 
                                            class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- List View -->
                <div id="list-view" class="hidden">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="w-8 px-6 py-3">
                                        <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll()" 
                                               class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
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
                                    <tr class="file-item-list hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                        data-file-id="{{ $file->id }}"
                                        data-file-name="{{ strtolower($file->file_name) }}"
                                        data-file-category="{{ $file->category ?? '' }}"
                                        data-file-type="{{ $file->file_category }}"
                                        data-file-size="{{ $file->file_size }}"
                                        data-file-date="{{ $file->created_at->timestamp }}"
                                        onclick="selectFile({{ $file->id }}, event)">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" 
                                                   class="file-checkbox rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500"
                                                   onchange="toggleSelection({{ $file->id }})"
                                                   onclick="event.stopPropagation()">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if(str_starts_with($file->file_type, 'image/') && Storage::disk('public')->exists($file->file_path))
                                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($file->file_path) }}" alt="">
                                                    @else
                                                        <div class="h-10 w-10 rounded-lg flex items-center justify-center {{ 
                                                            $file->file_category === 'document' ? 'bg-blue-100 dark:bg-blue-900/30' : 
                                                            ($file->file_category === 'image' ? 'bg-green-100 dark:bg-green-900/30' : 
                                                            ($file->file_category === 'archive' ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-gray-100 dark:bg-gray-700'))
                                                        }}">
                                                            @if($file->file_category === 'document')
                                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            @elseif($file->file_category === 'image')
                                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                            @elseif($file->file_category === 'archive')
                                                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H16"/>
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $file->file_name }}</div>
                                                    @if($file->description)
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($file->description, 50) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $file->formatted_file_size }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $file->file_type_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst($file->category ?: 'General') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div>{{ $file->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs">{{ $file->created_at->format('g:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                @if(str_starts_with($file->file_type, 'image/') || $file->file_type === 'application/pdf')
                                                    <button onclick="previewFile({{ $file->id }}); event.stopPropagation();"
                                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                            title="Preview">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                                <button onclick="downloadFile({{ $file->id }}); event.stopPropagation();"
                                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                        title="Download">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </button>
                                                <button onclick="deleteFile({{ $file->id }}, '{{ $file->file_name }}'); event.stopPropagation();"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                        title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
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
                        <button onclick="clearFilters()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
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
            <div id="preview-content" class="text-center"></div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
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
let selectedFiles = new Set();
let currentView = 'grid';
let deleteFileId = null;

// View Mode Functions
function setViewMode(mode) {
    currentView = mode;
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');

    if (mode === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
        gridBtn.classList.remove('text-gray-500');
        listBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
        listBtn.classList.add('text-gray-500');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
        listBtn.classList.remove('text-gray-500');
        gridBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
        gridBtn.classList.add('text-gray-500');
    }

    // Save preference
    localStorage.setItem('fileManagerView', mode);
}

// Filter Functions
function filterFiles() {
    const searchTerm = document.getElementById('file-search').value.toLowerCase();
    const items = document.querySelectorAll('.file-item, .file-item-list');
    let visibleCount = 0;

    items.forEach(item => {
        const fileName = item.dataset.fileName;
        const isVisible = fileName.includes(searchTerm);
        
        if (isVisible) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    updateEmptyState(visibleCount);
}

function filterByCategory(category) {
    const items = document.querySelectorAll('.file-item, .file-item-list');
    let visibleCount = 0;

    // Update active filter button
    document.querySelectorAll('.filter-category-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    const activeBtn = document.querySelector(`[data-category="${category}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }

    items.forEach(item => {
        const itemCategory = item.dataset.fileCategory;
        const isVisible = category === '' || itemCategory === category;
        
        if (isVisible) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    updateEmptyState(visibleCount);
}

function filterByType(type) {
    const items = document.querySelectorAll('.file-item, .file-item-list');
    let visibleCount = 0;

    // Update active filter button
    document.querySelectorAll('.filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    const activeBtn = document.querySelector(`[data-type="${type}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }

    items.forEach(item => {
        const itemType = item.dataset.fileType;
        const isVisible = itemType === type;
        
        if (isVisible) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    updateEmptyState(visibleCount);
}

function clearFilters() {
    // Clear search
    document.getElementById('file-search').value = '';
    
    // Clear active filters
    document.querySelectorAll('.filter-category-btn, .filter-type-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    });
    
    // Show all files
    const items = document.querySelectorAll('.file-item, .file-item-list');
    items.forEach(item => {
        item.style.display = '';
    });

    updateEmptyState(items.length);
}

function updateEmptyState(visibleCount) {
    const emptyState = document.getElementById('empty-state');
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');

    if (visibleCount === 0) {
        emptyState.classList.remove('hidden');
        gridView.classList.add('hidden');
        listView.classList.add('hidden');
    } else {
        emptyState.classList.add('hidden');
        if (currentView === 'grid') {
            gridView.classList.remove('hidden');
        } else {
            listView.classList.remove('hidden');
        }
    }
}

// Sort Functions
function sortFiles() {
    const sortBy = document.getElementById('sort-select').value;
    const [field, direction] = sortBy.split('-');
    
    const gridContainer = document.getElementById('grid-view');
    const listContainer = document.querySelector('#list-view tbody');
    
    const gridItems = Array.from(gridContainer.children);
    const listItems = Array.from(listContainer.children);

    function getSortValue(item, field) {
        switch (field) {
            case 'name':
                return item.dataset.fileName;
            case 'size':
                return parseInt(item.dataset.fileSize);
            case 'date':
                return parseInt(item.dataset.fileDate);
            case 'type':
                return item.dataset.fileType;
            default:
                return '';
        }
    }

    function sortItems(items, container) {
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

        // Re-append sorted items
        items.forEach(item => container.appendChild(item));
    }

    sortItems(gridItems, gridContainer);
    sortItems(listItems, listContainer);
}

// Selection Functions
function selectFile(fileId, event) {
    if (event.ctrlKey || event.metaKey) {
        toggleSelection(fileId);
    } else if (event.shiftKey) {
        // TODO: Implement range selection
        toggleSelection(fileId);
    } else {
        // Single selection
        selectedFiles.clear();
        document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = false);
        toggleSelection(fileId);
    }
}

function toggleSelection(fileId) {
    const checkbox = document.querySelector(`[data-file-id="${fileId}"] .file-checkbox`);
    
    if (selectedFiles.has(fileId)) {
        selectedFiles.delete(fileId);
        checkbox.checked = false;
    } else {
        selectedFiles.add(fileId);
        checkbox.checked = true;
    }

    updateBulkActions();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const isChecked = selectAllCheckbox.checked;
    
    document.querySelectorAll('.file-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
        const fileId = parseInt(checkbox.closest('[data-file-id]').dataset.fileId);
        
        if (isChecked) {
            selectedFiles.add(fileId);
        } else {
            selectedFiles.delete(fileId);
        }
    });

    updateBulkActions();
}

function updateBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (selectedFiles.size > 0) {
        bulkActions.style.display = 'flex';
        selectedCount.textContent = `${selectedFiles.size} selected`;
    } else {
        bulkActions.style.display = 'none';
    }
}

// File Actions
function downloadFile(fileId) {
    window.location.href = `{{ route('admin.projects.files.index', $project) }}/${fileId}/download`;
}

function downloadSelected() {
    if (selectedFiles.size === 0) {
        alert('Please select files to download.');
        return;
    }

    // Create a form to submit the selected files for bulk download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ route('admin.projects.files.index', $project) }}/bulk-download`;
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    // Add selected file IDs
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
    document.getElementById('delete-file-name').textContent = fileName;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function deleteSelected() {
    if (selectedFiles.size === 0) {
        alert('Please select files to delete.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${selectedFiles.size} selected file(s)? This action cannot be undone.`)) {
        // Submit bulk delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.projects.files.index', $project) }}/bulk-delete`;
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Add selected file IDs
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
        // Submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.projects.files.index', $project) }}/${deleteFileId}`;
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

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
    document.getElementById('delete-modal').classList.add('hidden');
    deleteFileId = null;
}

function previewFile(fileId) {
    // Find file data
    const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
    const fileName = fileElement.dataset.fileName;
    
    // Show preview modal
    document.getElementById('preview-title').textContent = fileName;
    
    // Get file info and show preview
    fetch(`{{ route('admin.projects.files.index', $project) }}/${fileId}/preview`)
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Preview not available');
        })
        .then(html => {
            document.getElementById('preview-content').innerHTML = html;
            document.getElementById('preview-modal').classList.remove('hidden');
        })
        .catch(error => {
            document.getElementById('preview-content').innerHTML = 
                '<p class="text-gray-500 dark:text-gray-400">Preview not available for this file type.</p>';
            document.getElementById('preview-modal').classList.remove('hidden');
        });
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Load saved view preference
    const savedView = localStorage.getItem('fileManagerView') || 'grid';
    setViewMode(savedView);

    // Set default active filter (All Files)
    const allFilesBtn = document.querySelector('[data-category=""]');
    if (allFilesBtn) {
        allFilesBtn.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
    }

    // Initialize keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape key closes modals
        if (e.key === 'Escape') {
            closePreview();
            closeDeleteModal();
        }
        
        // Ctrl+A selects all files
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            document.getElementById('select-all-checkbox').checked = true;
            toggleSelectAll();
        }
        
        // Delete key deletes selected files
        if (e.key === 'Delete' && selectedFiles.size > 0) {
            deleteSelected();
        }
    });

    // Close modals when clicking outside
    document.getElementById('preview-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePreview();
        }
    });

    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Auto-refresh file list periodically (every 30 seconds)
    setInterval(function() {
        // Only refresh if no files are selected to avoid disrupting user workflow
        if (selectedFiles.size === 0) {
            // You can implement a silent refresh here if needed
            console.log('Auto-refresh check - no files selected');
        }
    }, 30000);
});

// Notification system
function showNotification(message, type = 'info') {
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

// File drag and drop for upload
function initializeDragDrop() {
    const dropZone = document.querySelector('.flex-1.overflow-auto');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'border-2', 'border-dashed', 'border-blue-300');
    }

    function unhighlight(e) {
        dropZone.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'border-2', 'border-dashed', 'border-blue-300');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            // Redirect to upload page with indication that files were dropped
            showNotification('Redirecting to upload page...', 'info');
            setTimeout(() => {
                window.location.href = '{{ route('admin.projects.files.create', $project) }}';
            }, 1000);
        }
    }
}

// Context menu for files
function initializeContextMenu() {
    document.addEventListener('contextmenu', function(e) {
        const fileItem = e.target.closest('.file-item, .file-item-list');
        if (fileItem) {
            e.preventDefault();
            showContextMenu(e, fileItem);
        }
    });
}

function showContextMenu(e, fileItem) {
    const fileId = parseInt(fileItem.dataset.fileId);
    const fileName = fileItem.dataset.fileName;
    
    // Remove existing context menu
    const existingMenu = document.querySelector('.context-menu');
    if (existingMenu) {
        existingMenu.remove();
    }

    // Create context menu
    const menu = document.createElement('div');
    menu.className = 'context-menu fixed bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50 py-1';
    menu.style.left = e.pageX + 'px';
    menu.style.top = e.pageY + 'px';

    const menuItems = [
        { label: 'Download', action: () => downloadFile(fileId), icon: 'download' },
        { label: 'Preview', action: () => previewFile(fileId), icon: 'eye' },
        { label: 'Select', action: () => toggleSelection(fileId), icon: 'check' },
        { label: 'Delete', action: () => deleteFile(fileId, fileName), icon: 'trash', danger: true }
    ];

    menuItems.forEach(item => {
        const menuItem = document.createElement('button');
        menuItem.className = `w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center ${
            item.danger ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300'
        }`;
        
        menuItem.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${getIconPath(item.icon)}
            </svg>
            ${item.label}
        `;
        
        menuItem.onclick = () => {
            item.action();
            menu.remove();
        };
        
        menu.appendChild(menuItem);
    });

    document.body.appendChild(menu);

    // Remove menu when clicking elsewhere
    setTimeout(() => {
        document.addEventListener('click', function removeMenu() {
            menu.remove();
            document.removeEventListener('click', removeMenu);
        });
    }, 100);
}

function getIconPath(iconName) {
    const icons = {
        download: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        eye: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        check: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        trash: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'
    };
    return icons[iconName] || '';
}

// Initialize all features when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDragDrop();
    initializeContextMenu();
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

<style>
.file-item {
    position: relative;
}

.file-item:hover .absolute {
    opacity: 1;
}

.notification-toast {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.context-menu {
    min-width: 160px;
}

.file-item.selected,
.file-item-list.selected {
    background-color: #dbeafe;
}

.dark .file-item.selected,
.dark .file-item-list.selected {
    background-color: rgba(59, 130, 246, 0.1);
}

/* Custom scrollbar */
.overflow-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.dark .overflow-auto::-webkit-scrollbar-track {
    background: #1e293b;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.dark .overflow-auto::-webkit-scrollbar-thumb {
    background: #475569;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>