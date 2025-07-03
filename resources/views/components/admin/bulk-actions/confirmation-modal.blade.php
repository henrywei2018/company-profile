{{-- resources/views/components/admin/bulk-actions/confirmation-modal.blade.php --}}

@props([
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to perform this action?',
])

<div id="bulk-action-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <x-admin.icons.exclamation-triangle class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                    {{ $title }}
                </h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="modal-message">
                        {{ $message }}
                    </p>
                </div>
                
                <!-- Impact Preview -->
                <div id="impact-preview" class="hidden mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="text-left text-xs text-yellow-800 dark:text-yellow-200">
                        <div id="impact-details"></div>
                    </div>
                </div>
                
                <!-- Warning Messages -->
                <div id="warning-messages" class="hidden mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div class="text-left text-xs text-red-800 dark:text-red-200">
                        <div id="warning-details"></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-center space-x-3">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <button type="button" id="confirm-action-btn" onclick="confirmBulkAction()" 
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
