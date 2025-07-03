{{-- resources/views/components/admin/bulk-actions.blade.php --}}

@props([
    'route' => '#',
    'previewRoute' => '#',
    'statisticsRoute' => '#',
    'canForceDelete' => false,
    'showPriorityActions' => true,
    'showAssignActions' => false,
    'type' => 'admin', // admin or client
])

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
                    <x-admin.icons.check class="w-4 h-4 mr-1" />
                    Mark as Read
                </button>
                
                <button type="button" onclick="bulkMarkAsUnread()" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800">
                    <x-admin.icons.minus-circle class="w-4 h-4 mr-1" />
                    Mark as Unread
                </button>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <!-- Priority Dropdown -->
            @if($showPriorityActions)
            <x-admin.bulk-actions.priority-dropdown />
            @endif
            
            <!-- More Actions Dropdown -->
            <x-admin.bulk-actions.more-actions 
                :canForceDelete="$canForceDelete" 
                :showAssignActions="$showAssignActions" 
            />
            
            <!-- Clear Selection -->
            <button type="button" onclick="clearSelection()" 
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Clear Selection
            </button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<x-admin.bulk-actions.confirmation-modal />

<!-- JavaScript -->
<script>
// Global variables for bulk actions
let selectedMessages = new Set();
let currentBulkAction = null;
let currentBulkData = {};

// Configuration
const bulkActionConfig = {
    route: '{{ $route }}',
    previewRoute: '{{ $previewRoute }}',
    statisticsRoute: '{{ $statisticsRoute }}',
    canForceDelete: {{ $canForceDelete ? 'true' : 'false' }},
    type: '{{ $type }}'
};

// Initialize bulk actions
document.addEventListener('DOMContentLoaded', function() {
    initializeBulkActions();
});

function initializeBulkActions() {
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
            document.getElementById('priority-dropdown')?.classList.add('hidden');
        }
        if (!event.target.closest('#more-actions-btn')) {
            document.getElementById('more-actions-dropdown')?.classList.add('hidden');
        }
    });
}

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
    document.getElementById('priority-dropdown')?.classList.add('hidden');
    
    await bulkAction('change_priority', { priority: priority });
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
        
        const response = await fetch(bulkActionConfig.route, {
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

function showConfirmationModal(title, message, action, additionalData = {}) {
    const modal = document.getElementById('bulk-action-modal');
    const titleEl = document.getElementById('modal-title');
    const messageEl = document.getElementById('modal-message');
    const impactPreview = document.getElementById('impact-preview');
    const confirmBtn = document.getElementById('confirm-action-btn');
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    impactPreview?.classList.add('hidden');
    
    confirmBtn.textContent = 'Confirm';
    confirmBtn.className = 'px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800';
    confirmBtn.onclick = confirmBulkAction;
    
    currentBulkAction = action;
    currentBulkData = additionalData;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('bulk-action-modal')?.classList.add('hidden');
    currentBulkAction = null;
    currentBulkData = {};
}

async function confirmBulkAction() {
    if (!currentBulkAction) return;
    
    closeModal();
    await bulkAction(currentBulkAction, currentBulkData);
}

function showNotification(message, type = 'info') {
    // Implementation for notifications
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
    // Implementation depends on your specific statistics display
}
</script>