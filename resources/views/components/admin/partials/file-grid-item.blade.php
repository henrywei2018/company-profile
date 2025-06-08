{{-- resources/views/admin/partials/file-grid-item.blade.php --}}
@props(['file', 'project'])
<div class="file-item bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow cursor-pointer group relative"
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
        @if($file->isImage() && $file->existsOnDisk())
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
            @if($file->isPreviewable())
                <button onclick="previewFile({{ $file->id }}); event.stopPropagation();" 
                        class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
                        title="Preview">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            @endif
            <button onclick="downloadFile({{ $file->id }}); event.stopPropagation();" 
                    class="p-1 text-gray-400 hover:text-green-600 dark:hover:text-green-400"
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