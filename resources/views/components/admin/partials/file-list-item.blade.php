{{-- resources/views/components/admin/partials/file-list-item.blade.php --}}
@props(['file', 'project'])

@php
    use App\Helpers\BladeHelpers;
    $fileName = BladeHelpers::safeAttribute($file, 'file_name', 'Unknown file');
    $fileCategory = BladeHelpers::safeAttribute($file, 'category', '');
    $fileType = method_exists($file, 'getFileCategoryAttribute') ? $file->getFileCategoryAttribute() : 'other';
    $fileTypeName = method_exists($file, 'getFileTypeNameAttribute') ? $file->getFileTypeNameAttribute() : 'Unknown';
    $formattedSize = method_exists($file, 'getFormattedFileSizeAttribute') ? $file->getFormattedFileSizeAttribute() : 'Unknown size';
@endphp

<tr class="file-item-list hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
    data-file-id="{{ $file->id }}"
    data-file-name="{{ strtolower($fileName) }}"
    data-file-category="{{ $fileCategory }}"
    data-file-type="{{ $fileType }}"
    data-file-size="{{ $file->file_size ?? 0 }}"
    data-file-date="{{ $file->created_at->timestamp }}"
    onclick="selectFile({{ $file->id }}, event)">
    
    <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox" 
               class="file-checkbox rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
               onchange="toggleSelection({{ $file->id }})"
               onclick="event.stopPropagation()">
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10">
                @if($file->isImage() && $file->existsOnDisk())
                    <img class="h-10 w-10 rounded-lg object-cover" 
                         src="{{ Storage::url($file->file_path) }}" 
                         alt="{{ $fileName }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center" style="display: none;">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @else
                    <div class="h-10 w-10 rounded-lg flex items-center justify-center {{ 
                        $fileType === 'document' ? 'bg-blue-100 dark:bg-blue-900/30' : 
                        ($fileType === 'image' ? 'bg-green-100 dark:bg-green-900/30' : 
                        ($fileType === 'archive' ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-gray-100 dark:bg-gray-700'))
                    }}">
                        @if($fileType === 'document')
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @elseif($fileType === 'image')
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @elseif($fileType === 'archive')
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
                <div class="flex items-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $fileName }}</div>
                    @if(method_exists($file, 'isRecentlyUploaded') && $file->isRecentlyUploaded())
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            New
                        </span>
                    @endif
                </div>
                @if($file->description)
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($file->description, 50) }}</div>
                @endif
                @if($file->download_count > 0)
                    <div class="text-xs text-blue-600 dark:text-blue-400">{{ $file->download_count }} downloads</div>
                @endif
            </div>
        </div>
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
        {{ $formattedSize }}
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
        {{ $fileTypeName }}
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
            {{ ucfirst($fileCategory ?: 'General') }}
        </span>
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
        <div>{{ $file->created_at->format('M j, Y') }}</div>
        <div class="text-xs">{{ $file->created_at->format('g:i A') }}</div>
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex items-center justify-end space-x-2">
            @if(method_exists($file, 'isPreviewable') && $file->isPreviewable())
                <button onclick="previewFile({{ $file->id }}); event.stopPropagation();"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 rounded"
                        title="Preview">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            @endif
            <button onclick="downloadFile({{ $file->id }}); event.stopPropagation();"
                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 p-1 rounded"
                    title="Download">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
            <button onclick="deleteFile({{ $file->id }}, '{{ addslashes($fileName) }}'); event.stopPropagation();"
                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded"
                    title="Delete">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </td>
</tr>