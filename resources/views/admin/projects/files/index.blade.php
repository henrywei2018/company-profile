{{-- resources/views/admin/projects/files/index.blade.php --}}
<x-layouts.admin title="Project Files - {{ $project->title }}">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Project Files</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage files and documents for "{{ $project->title }}"
            </p>
        </div>
        
        <div class="flex items-center space-x-3 mt-4 lg:mt-0">
            <!-- File Statistics -->
            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-4 py-2 rounded-lg">
                <span>{{ $files->count() }} files</span>
                <span>•</span>
                <span>{{ \App\Helpers\FileHelper::formatFileSize($totalSize) }}</span>
                <span>•</span>
                <span>{{ $totalDownloads }} downloads</span>
            </div>
            
            <!-- Actions -->
            <x-admin.button 
                href="{{ route('admin.projects.files.create', $project) }}" 
                color="primary"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Quick Actions Bar -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search and Filters -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="file-search" 
                           placeholder="Search files..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <select id="category-filter" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">All Categories</option>
                    @foreach($filesByCategory->keys() as $category)
                        <option value="{{ $category }}">{{ ucfirst($category ?: 'Uncategorized') }}</option>
                    @endforeach
                </select>
                
                <select id="type-filter" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <option value="">All Types</option>
                    <option value="image">Images</option>
                    <option value="document">Documents</option>
                    <option value="archive">Archives</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <!-- View Toggle -->
            <div class="flex items-center space-x-2">
                <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                    <button class="px-3 py-1 text-sm bg-blue-500 text-white view-toggle" data-view="grid">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                    <button class="px-3 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-l border-gray-300 dark:border-gray-600 view-toggle" data-view="list">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Export -->
                <x-admin.button 
                    href="{{ route('admin.projects.files.export', $project) }}" 
                    color="light" 
                    size="sm"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </x-admin.button>
            </div>
        </div>
    </div>

    <!-- Files Container -->
    <div id="files-container">
        @if($files->count() > 0)
            <!-- Grid View (Default) -->
            <div id="grid-view" class="files-view">
                @if($filesByCategory->count() > 0)
                    @foreach($filesByCategory as $category => $categoryFiles)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                {{ ucfirst($category ?: 'Uncategorized') }}
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ $categoryFiles->count() }})</span>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($categoryFiles as $file)
                                    <x-admin.card class="file-card hover:shadow-lg transition-shadow duration-200" 
                                                  data-category="{{ $file->category }}" 
                                                  data-type="{{ $file->file_icon }}">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                @if(str_starts_with($file->file_type, 'image/'))
                                                    <img src="{{ route('admin.projects.files.thumbnail', [$project, $file]) }}" 
                                                         alt="{{ $file->file_name }}"
                                                         class="w-12 h-12 object-cover rounded-lg">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                        <img src="{{ route('admin.projects.files.thumbnail', [$project, $file]) }}" 
                                                             alt="File icon" 
                                                             class="w-8 h-8">
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate" 
                                                    title="{{ $file->file_name }}">
                                                    {{ $file->file_name }}
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $file->formatted_file_size }} • {{ $file->created_at->format('M j, Y') }}
                                                </p>
                                                @if($file->description)
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                                        {{ $file->description }}
                                                    </p>
                                                @endif
                                                
                                                <!-- File Actions -->
                                                <div class="flex items-center space-x-2 mt-3">
                                                    <x-admin.button 
                                                        href="{{ route('admin.projects.files.download', [$project, $file]) }}" 
                                                        color="light" 
                                                        size="sm"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        Download
                                                    </x-admin.button>
                                                    
                                                    @if(in_array($file->file_type, ['application/pdf', 'text/plain', 'text/csv']) || str_starts_with($file->file_type, 'image/'))
                                                        <x-admin.button 
                                                            href="{{ route('admin.projects.files.preview', [$project, $file]) }}" 
                                                            color="info" 
                                                            size="sm"
                                                            target="_blank"
                                                        >
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                            Preview
                                                        </x-admin.button>
                                                    @endif
                                                    
                                                    <!-- More Actions Dropdown -->
                                                    <div class="relative" x-data="{ open: false }">
                                                        <x-admin.icon-button
                                                            type="button"
                                                            color="light"
                                                            size="sm"
                                                            @click="open = !open"
                                                        >
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                            </svg>
                                                        </x-admin.icon-button>
                                                        
                                                        <div x-show="open" @click.away="open = false" 
                                                             x-transition:enter="transition ease-out duration-100"
                                                             x-transition:enter-start="transform opacity-0 scale-95"
                                                             x-transition:enter-end="transform opacity-100 scale-100"
                                                             x-transition:leave="transition ease-in duration-75"
                                                             x-transition:leave-start="transform opacity-100 scale-100"
                                                             x-transition:leave-end="transform opacity-0 scale-95"
                                                             class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                                                            <div class="py-1">
                                                                <button onclick="editFile({{ $file->id }})" 
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                    Edit Details
                                                                </button>
                                                                <form method="POST" action="{{ route('admin.projects.files.destroy', [$project, $file]) }}" class="block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            onclick="return confirm('Are you sure you want to delete this file?')"
                                                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                                        Delete File
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- File Stats -->
                                                <div class="flex items-center space-x-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        {{ $file->download_count }} downloads
                                                    </span>
                                                    @if($file->is_public)
                                                        <span class="flex items-center text-green-600">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Public
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </x-admin.card>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- No categorized files, show all files -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($files as $file)
                            <x-admin.card class="file-card">
                                <!-- File card content similar to above -->
                            </x-admin.card>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- List View -->
            <div id="list-view" class="files-view hidden">
                <x-admin.card>
                    <x-admin.data-table>
                        <x-slot name="columns">
                            <x-admin.table-column>File</x-admin.table-column>
                            <x-admin.table-column>Category</x-admin.table-column>
                            <x-admin.table-column>Size</x-admin.table-column>
                            <x-admin.table-column>Downloads</x-admin.table-column>
                            <x-admin.table-column>Uploaded</x-admin.table-column>
                            <x-admin.table-column>Actions</x-admin.table-column>
                        </x-slot>
                        
                        @foreach($files as $file)
                            <x-admin.table-row class="file-row" 
                                               data-category="{{ $file->category }}" 
                                               data-type="{{ $file->file_icon }}">
                                <x-admin.table-cell highlight>
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            @if(str_starts_with($file->file_type, 'image/'))
                                                <img src="{{ route('admin.projects.files.thumbnail', [$project, $file]) }}" 
                                                     alt="{{ $file->file_name }}"
                                                     class="w-8 h-8 object-cover rounded">
                                            @else
                                                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                                    <img src="{{ route('admin.projects.files.thumbnail', [$project, $file]) }}" 
                                                         alt="File icon" 
                                                         class="w-5 h-5">
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $file->file_name }}
                                            </div>
                                            @if($file->description)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($file->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </x-admin.table-cell>
                                
                                <x-admin.table-cell>
                                    <x-admin.badge type="light">
                                        {{ ucfirst($file->category ?: 'Uncategorized') }}
                                    </x-admin.badge>
                                </x-admin.table-cell>
                                
                                <x-admin.table-cell>
                                    {{ $file->formatted_file_size }}
                                </x-admin.table-cell>
                                
                                <x-admin.table-cell>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $file->download_count }}
                                    </div>
                                </x-admin.table-cell>
                                
                                <x-admin.table-cell>
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $file->created_at->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $file->created_at->format('g:i A') }}
                                    </div>
                                </x-admin.table-cell>
                                
                                <x-admin.table-cell>
                                    <div class="flex items-center space-x-1">
                                        <x-admin.icon-button
                                            href="{{ route('admin.projects.files.download', [$project, $file]) }}"
                                            color="primary"
                                            size="sm"
                                            tooltip="Download"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </x-admin.icon-button>
                                        
                                        @if(in_array($file->file_type, ['application/pdf', 'text/plain', 'text/csv']) || str_starts_with($file->file_type, 'image/'))
                                            <x-admin.icon-button
                                                href="{{ route('admin.projects.files.preview', [$project, $file]) }}"
                                                color="info"
                                                size="sm"
                                                tooltip="Preview"
                                                target="_blank"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </x-admin.icon-button>
                                        @endif
                                        
                                        <x-admin.icon-button
                                            type="button"
                                            color="light"
                                            size="sm"
                                            tooltip="Edit"
                                            onclick="editFile({{ $file->id }})"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </x-admin.icon-button>
                                        
                                        <form method="POST" action="{{ route('admin.projects.files.destroy', [$project, $file]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.icon-button
                                                type="submit"
                                                color="danger"
                                                size="sm"
                                                tooltip="Delete"
                                                onclick="return confirm('Are you sure you want to delete this file?')"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </x-admin.icon-button>
                                        </form>
                                    </div>
                                </x-admin.table-cell>
                            </x-admin.table-row>
                        @endforeach
                    </x-admin.data-table>
                </x-admin.card>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $files->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <x-admin.empty-state
                title="No Files Uploaded"
                description="Upload project files, documents, and resources to get started."
                actionText="Upload First File"
                :actionUrl="route('admin.projects.files.create', $project)"
                icon='<svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>'
            />
        @endif
    </div>

    <!-- Edit File Modal -->
    <x-admin.modal id="edit-file-modal" title="Edit File Details" size="md">
        <form id="edit-file-form" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <x-admin.input
                    label="File Name"
                    name="file_name"
                    required
                />
                
                <x-admin.select
                    label="Category"
                    name="category"
                    :options="[
                        'documents' => 'Documents',
                        'images' => 'Images',
                        'plans' => 'Plans & Drawings',
                        'contracts' => 'Contracts',
                        'reports' => 'Reports',
                        'certificates' => 'Certificates',
                        'presentations' => 'Presentations',
                        'specifications' => 'Specifications',
                        'invoices' => 'Invoices',
                        'correspondence' => 'Correspondence',
                        'photos' => 'Project Photos',
                        'videos' => 'Videos',
                        'archives' => 'Archives',
                        'other' => 'Other',
                    ]"
                />
                
                <x-admin.textarea
                    label="Description"
                    name="description"
                    rows="3"
                    placeholder="Optional description for this file"
                />
                
                <x-admin.checkbox
                    label="Public File"
                    name="is_public"
                    helper="Allow clients to access this file directly"
                />
            </div>
        </form>
        
        <x-slot name="footer">
            <x-admin.button 
                color="light" 
                onclick="document.getElementById('edit-file-modal').classList.add('hidden')"
            >
                Cancel
            </x-admin.button>
            <x-admin.button 
                type="submit" 
                form="edit-file-form"
                color="primary"
            >
                Update File
            </x-admin.button>
        </x-slot>
    </x-admin.modal>

    <!-- Bulk Upload Modal -->
    <x-admin.modal id="bulk-upload-modal" title="Bulk Upload Files" size="lg">
        <form id="bulk-upload-form" action="{{ route('admin.projects.files.bulk-upload', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Files
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="bulk-files" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload files</span>
                                    <input id="bulk-files" name="files[]" type="file" class="sr-only" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, PDF, DOC, XLS up to 10MB each
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-admin.select
                        label="Default Category"
                        name="category"
                        :options="[
                            'documents' => 'Documents',
                            'images' => 'Images',
                            'plans' => 'Plans & Drawings',
                            'contracts' => 'Contracts',
                            'reports' => 'Reports',
                            'other' => 'Other',
                        ]"
                        value="documents"
                    />
                    
                    <div class="flex items-end">
                        <x-admin.checkbox
                            label="Make files public"
                            name="is_public"
                            helper="Allow clients to download these files"
                        />
                    </div>
                </div>
                
                <!-- File Preview List -->
                <div id="bulk-file-list" class="hidden">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Selected Files:</h4>
                    <div id="bulk-file-items" class="space-y-2 max-h-40 overflow-y-auto">
                        <!-- Files will be listed here via JavaScript -->
                    </div>
                </div>
            </div>
        </form>
        
        <x-slot name="footer">
            <x-admin.button 
                color="light" 
                onclick="document.getElementById('bulk-upload-modal').classList.add('hidden')"
            >
                Cancel
            </x-admin.button>
            <x-admin.button 
                type="submit" 
                form="bulk-upload-form"
                color="primary"
                id="bulk-upload-submit"
                disabled
            >
                Upload Files
            </x-admin.button>
        </x-slot>
    </x-admin.modal>

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <div class="flex flex-col space-y-3" x-data="{ open: false }">
            <!-- Sub Actions -->
            <div x-show="open" x-transition class="flex flex-col space-y-2">
                <x-admin.button 
                    onclick="document.getElementById('bulk-upload-modal').classList.remove('hidden')"
                    color="info"
                    size="sm"
                    class="shadow-lg"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4"/>
                    </svg>
                    Bulk Upload
                </x-admin.button>
                
                <x-admin.button 
                    href="{{ route('admin.projects.show', $project) }}" 
                    color="light"
                    size="sm"
                    class="shadow-lg"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Project
                </x-admin.button>
            </div>
            
            <!-- Main FAB -->
            <button @click="open = !open" 
                    class="w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 transform hover:scale-105">
                <svg class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-45': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </button>
        </div>
    </div>
</x-layouts.admin>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Toggle Functionality
    const viewToggles = document.querySelectorAll('.view-toggle');
    const views = document.querySelectorAll('.files-view');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetView = this.dataset.view;
            
            // Update button states
            viewToggles.forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });
            this.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            this.classList.add('bg-blue-500', 'text-white');
            
            // Show/hide views
            views.forEach(view => {
                if (view.id === targetView + '-view') {
                    view.classList.remove('hidden');
                } else {
                    view.classList.add('hidden');
                }
            });
        });
    });
    
    // Search Functionality
    const searchInput = document.getElementById('file-search');
    const categoryFilter = document.getElementById('category-filter');
    const typeFilter = document.getElementById('type-filter');
    
    function filterFiles() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedType = typeFilter.value;
        
        const fileCards = document.querySelectorAll('.file-card');
        const fileRows = document.querySelectorAll('.file-row');
        
        [...fileCards, ...fileRows].forEach(item => {
            const fileName = item.querySelector('.font-medium, .text-gray-900')?.textContent.toLowerCase() || '';
            const category = item.dataset.category || '';
            const type = item.dataset.type || '';
            
            const matchesSearch = searchTerm === '' || fileName.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || category === selectedCategory;
            const matchesType = selectedType === '' || type.includes(selectedType);
            
            if (matchesSearch && matchesCategory && matchesType) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterFiles);
    categoryFilter.addEventListener('change', filterFiles);
    typeFilter.addEventListener('change', filterFiles);
    
    // Bulk Upload File Selection
    const bulkFilesInput = document.getElementById('bulk-files');
    const bulkFileList = document.getElementById('bulk-file-list');
    const bulkFileItems = document.getElementById('bulk-file-items');
    const bulkUploadSubmit = document.getElementById('bulk-upload-submit');
    
    bulkFilesInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        
        if (files.length > 0) {
            bulkFileList.classList.remove('hidden');
            bulkUploadSubmit.disabled = false;
            
            bulkFileItems.innerHTML = files.map(file => `
                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">${file.name}</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">${formatFileSize(file.size)}</span>
                </div>
            `).join('');
        } else {
            bulkFileList.classList.add('hidden');
            bulkUploadSubmit.disabled = true;
        }
    });
    
    // Drag and Drop for Bulk Upload
    const dropZone = document.querySelector('.border-dashed');
    
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
        dropZone.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        bulkFilesInput.files = files;
        bulkFilesInput.dispatchEvent(new Event('change'));
    }
    
    // File size formatter
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});

// Edit File Function
function editFile(fileId) {
    // Fetch file details and populate the edit form
    fetch(`{{ route('admin.projects.files.index', $project) }}/${fileId}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('edit-file-form');
            form.action = `{{ route('admin.projects.files.index', $project) }}/${fileId}`;
            
            form.querySelector('[name="file_name"]').value = data.file_name;
            form.querySelector('[name="category"]').value = data.category || '';
            form.querySelector('[name="description"]').value = data.description || '';
            form.querySelector('[name="is_public"]').checked = data.is_public;
            
            document.getElementById('edit-file-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching file details:', error);
            alert('Error loading file details. Please try again.');
        });
}
</script>
@endpush