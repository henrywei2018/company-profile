{{-- resources/views/admin/partials/file-preview-modal.blade.php --}}
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