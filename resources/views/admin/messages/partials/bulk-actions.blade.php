{{-- resources/views/admin/messages/partials/bulk-actions.blade.php --}}

<!-- Bulk Actions Toolbar -->
<div id="bulk-actions-toolbar" class="hidden bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">
                <span id="selected-count">0</span> message(s) selected
            </span>
            
            <!-- Quick Actions -->
            <div class="flex items-center space-x-2">
                <button type="button" onclick="bulkMarkAsRead()" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Mark as Read
                </button>
                
                <button type="button" onclick="bulkMarkAsUnread()" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Mark as Unread
                </button>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <!-- Priority Dropdown -->
            <div class="relative">
                <button type="button" id="priority-dropdown-btn" onclick="togglePriorityDropdown()"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Set Priority
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="priority-dropdown" class="hidden absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                    <div class="py-1">
                        <button type="button" onclick="bulkSetPriority('low')" 
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <span class="inline-block w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                            Low Priority
                        </button>
                        <button type="button" onclick="bulkSetPriority('normal')" 
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <span class="inline-block w-3 h-3 bg-blue-400 rounded-full mr-2"></span>
                            Normal Priority
                        </button>
                        <button type="button" onclick="bulkSetPriority('high')" 
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <span class="inline-block w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                            High Priority
                        </button>
                        <button type="button" onclick="bulkSetPriority('urgent')" 
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <span class="inline-block w-3 h-3 bg-red-400 rounded-full mr-2"></span>
                            Urgent Priority
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- More Actions Dropdown -->
            <div class="relative">
                <button type="button" id="more-actions-btn" onclick="toggleMoreActionsDropdown()"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    More Actions
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="more-actions-dropdown" class="hidden absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                    <div class="py-1">
                        <button type="button" onclick="bulkDelete()" 
                                class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Messages
                        </button>
                        <button type="button" onclick="bulkDeleteThreads()" 
                                class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path>
                            </svg>
                            Delete Conversations
                        </button>
                        <div class="border-t border-gray-200 dark:border-gray-600"></div>
                        <button type="button" onclick="previewBulkAction()" 
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview Impact
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Clear Selection -->
            <button type="button" onclick="clearSelection()" 
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Clear Selection
            </button>
        </div>
    </div>
</div>

<!-- Bulk Action Confirmation Modal -->
<div id="bulk-action-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                    Confirm Bulk Action
                </h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="modal-message">
                        Are you sure you want to perform this action?
                    </p>
                </div>
                
                <!-- Impact Preview -->
                <div id="impact-preview" class="hidden mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="text-left text-xs text-yellow-800 dark:text-yellow-200">
                        <div id="impact-details"></div>
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

<script>
// Global variables for bulk actions
let selectedMessages = new Set();
let currentBulkAction = null;
let currentBulkData = {};

// Initialize bulk actions
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to checkboxes
    const checkboxes = document.querySelectorAll('input[name="message_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', handleCheckboxChange);
    });
    
    // Add master checkbox listener
    const masterCheckbox = document.getElementById('select-all');
    if (masterCheckbox) {
        masterCheckbox.addEventListener('change', handleMasterCheckboxChange);
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#priority-dropdown-btn')) {
            document.getElementById('priority-dropdown').classList.add('hidden');
        }
        if (!event.target.closest('#more-actions-btn')) {
            document.getElementById('more-actions-dropdown').classList.add('hidden');
        }
    });
});

function handleCheckboxChange(event) {
    const messageId = parseInt(event.target.value);
    
    if (event.target.checked) {
        selectedMessages.add(messageId);
    } else {
        selectedMessages.delete(messageId);
    }
    
    updateBulkActionsToolbar();
    updateMasterCheckbox();
}

function handleMasterCheckboxChange(event) {
    const checkboxes = document.querySelectorAll('input[name="message_ids[]"]');
    
    checkboxes.forEach(checkbox => {
        const messageId = parseInt(checkbox.value);
        
        if (event.target.checked) {
            checkbox.checked = true;
            selectedMessages.add(messageId);
        } else {
            checkbox.checked = false;
            selectedMessages.delete(messageId);
        }
    });
    
    updateBulkActionsToolbar();
}

function updateBulkActionsToolbar() {
    const toolbar = document.getElementById('bulk-actions-toolbar');
    const countElement = document.getElementById('selected-count');
    
    if (selectedMessages.size > 0) {
        toolbar.classList.remove('hidden');
        countElement.textContent = selectedMessages.size;
    } else {
        toolbar.classList.add('hidden');
    }
}

function updateMasterCheckbox() {
    const masterCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[name="message_ids[]"]');
    
    if (!masterCheckbox) return;
    
    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
    
    if (checkedCount === 0) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    } else if (checkedCount === checkboxes.length) {
        masterCheckbox.checked = true;
        masterCheckbox.indeterminate = false;
    } else {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = true;
    }
}

function clearSelection() {
    selectedMessages.clear();
    
    // Uncheck all checkboxes
    const checkboxes = document.querySelectorAll('input[name="message_ids[]"], #select-all');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        checkbox.indeterminate = false;
    });
    
    updateBulkActionsToolbar();
}

function togglePriorityDropdown() {
    const dropdown = document.getElementById('priority-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Close other dropdown
    document.getElementById('more-actions-dropdown').classList.add('hidden');
}

function toggleMoreActionsDropdown() {
    const dropdown = document.getElementById('more-actions-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Close other dropdown
    document.getElementById('priority-dropdown').classList.add('hidden');
}

// Bulk action functions
async function bulkMarkAsRead() {
    await bulkAction('mark_read');
}

async function bulkMarkAsUnread() {
    await bulkAction('mark_unread');
}

async function bulkArchive() {
    await bulkAction('archive');
}

async function bulkDelete() {
    showConfirmationModal(
        'Delete Messages',
        `Are you sure you want to delete ${selectedMessages.size} message(s)? This action cannot be undone.`,
        'delete'
    );
}

async function bulkDeleteThreads() {
    showConfirmationModal(
        'Delete Conversations',
        `Are you sure you want to delete entire conversations for ${selectedMessages.size} message(s)? This will delete all messages in these conversation threads and cannot be undone.`,
        'delete_thread'
    );
}

async function bulkSetPriority(priority) {
    // Close dropdown
    document.getElementById('priority-dropdown').classList.add('hidden');
    
    await bulkAction('change_priority', { priority: priority });
}

async function previewBulkAction() {
    if (selectedMessages.size === 0) {
        showNotification('Please select messages first.', 'warning');
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.messages.preview-bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                message_ids: Array.from(selectedMessages)
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showImpactPreview(data.impact_info, data.message);
        } else {
            showNotification(data.message || 'Failed to preview action impact.', 'error');
        }
    } catch (error) {
        console.error('Preview error:', error);
        showNotification('An error occurred while previewing action impact.', 'error');
    }
}

function showImpactPreview(impactInfo, message) {
    const modal = document.getElementById('bulk-action-modal');
    const title = document.getElementById('modal-title');
    const messageEl = document.getElementById('modal-message');
    const impactPreview = document.getElementById('impact-preview');
    const impactDetails = document.getElementById('impact-details');
    const confirmBtn = document.getElementById('confirm-action-btn');
    
    title.textContent = 'Action Impact Preview';
    messageEl.textContent = message;
    
    // Show impact details
    impactDetails.innerHTML = `
        <div class="space-y-1">
            <div>Total messages: <strong>${impactInfo.total_messages}</strong></div>
            ${impactInfo.urgent_messages > 0 ? `<div class="text-red-600">Urgent messages: <strong>${impactInfo.urgent_messages}</strong></div>` : ''}
            ${impactInfo.project_linked > 0 ? `<div>Project-linked: <strong>${impactInfo.project_linked}</strong></div>` : ''}
            ${impactInfo.unique_clients > 0 ? `<div>Unique clients: <strong>${impactInfo.unique_clients}</strong></div>` : ''}
            ${impactInfo.with_attachments > 0 ? `<div>With attachments: <strong>${impactInfo.with_attachments}</strong></div>` : ''}
        </div>
    `;
    
    impactPreview.classList.remove('hidden');
    confirmBtn.textContent = 'Close';
    confirmBtn.className = 'px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700';
    confirmBtn.onclick = closeModal;
    
    modal.classList.remove('hidden');
}

function showConfirmationModal(title, message, action, additionalData = {}) {
    const modal = document.getElementById('bulk-action-modal');
    const titleEl = document.getElementById('modal-title');
    const messageEl = document.getElementById('modal-message');
    const impactPreview = document.getElementById('impact-preview');
    const confirmBtn = document.getElementById('confirm-action-btn');
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    impactPreview.classList.add('hidden');
    
    confirmBtn.textContent = 'Confirm';
    confirmBtn.className = 'px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800';
    confirmBtn.onclick = confirmBulkAction;
    
    currentBulkAction = action;
    currentBulkData = additionalData;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('bulk-action-modal').classList.add('hidden');
    currentBulkAction = null;
    currentBulkData = {};
}

async function confirmBulkAction() {
    if (!currentBulkAction) return;
    
    closeModal();
    await bulkAction(currentBulkAction, currentBulkData);
}

async function bulkAction(action, additionalData = {}) {
    if (selectedMessages.size === 0) {
        showNotification('Please select messages first.', 'warning');
        return;
    }
    
    try {
        const requestData = {
            action: action,
            message_ids: Array.from(selectedMessages),
            ...additionalData
        };
        
        const response = await fetch('{{ route("admin.messages.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify(requestData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Update statistics if provided
            if (data.statistics) {
                updateStatisticsDisplay(data.statistics);
            }
            
            // Clear selection and reload page
            clearSelection();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'An error occurred while performing the bulk action.', 'error');
        }
    } catch (error) {
        console.error('Bulk action error:', error);
        showNotification('An error occurred while performing the bulk action.', 'error');
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${getNotificationClasses(type)}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${getNotificationIcon(type)}</span>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function getNotificationClasses(type) {
    switch (type) {
        case 'success': return 'bg-green-600 text-white';
        case 'error': return 'bg-red-600 text-white';
        case 'warning': return 'bg-yellow-600 text-white';
        default: return 'bg-blue-600 text-white';
    }
}

function getNotificationIcon(type) {
    switch (type) {
        case 'success': return '✅';
        case 'error': return '❌';
        case 'warning': return '⚠️';
        default: return 'ℹ️';
    }
}

function updateStatisticsDisplay(stats) {
    // Update statistics cards if they exist
    // This function updates the statistics shown on the page
    // Implementation depends on your specific statistics display structure
}
</script>