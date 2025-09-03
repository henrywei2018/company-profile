<!-- resources/views/client/messages/partials/bulk-actions.blade.php -->
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3" id="bulk-actions" style="display: none;">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700 dark:text-gray-300">
                <span id="selected-count">0</span> messages selected
            </span>
            
            <div class="flex items-center space-x-2">
                <button
                    type="button"
                    onclick="bulkAction('mark_read')"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mark Read
                </button>
                
                <button
                    type="button"
                    onclick="bulkAction('mark_unread')"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Mark Unread
                </button>
            </div>
        </div>
        
        <button
            type="button"
            onclick="clearSelection()"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
        >
            Clear Selection
        </button>
    </div>
</div>

<!-- Enhanced Message List Item Template -->
<!-- Add this checkbox structure to your message list items in index.blade.php -->
<!--
<div class="message-item flex items-start space-x-4 p-4 border-b border-gray-200 dark:border-gray-700" data-message-id="{{ $message->id }}">
    <div class="flex-shrink-0">
        <input 
            type="checkbox" 
            class="message-checkbox mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            value="{{ $message->id }}"
            onchange="updateBulkActions()"
        >
    </div>
    
    <!-- Rest of your message content -->
    <div class="flex-1 min-w-0">
        <!-- Your existing message content -->
    </div>
</div>
-->

<script>
let selectedMessages = new Set();

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.message-checkbox:checked');
    selectedMessages.clear();
    
    checkboxes.forEach(checkbox => {
        selectedMessages.add(parseInt(checkbox.value));
    });
    
    const count = selectedMessages.size;
    const bulkActionsBar = document.getElementById('bulk-actions');
    const countDisplay = document.getElementById('selected-count');
    
    if (count > 0) {
        bulkActionsBar.style.display = 'block';
        countDisplay.textContent = count;
    } else {
        bulkActionsBar.style.display = 'none';
    }
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.message-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedMessages.clear();
    updateBulkActions();
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.message-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
    
    updateBulkActions();
}

async function bulkAction(action) {
    if (selectedMessages.size === 0) {
        showNotification('error', 'No messages selected');
        return;
    }
    
    try {
        const response = await fetch('{{ route("client.messages.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                message_ids: Array.from(selectedMessages)
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showNotification('success', data.message || 'Action completed successfully');
            
            // Update UI based on action
            if (action === 'mark_read') {
                selectedMessages.forEach(messageId => {
                    updateMessageReadStatus(messageId, true);
                });
            } else if (action === 'mark_unread') {
                selectedMessages.forEach(messageId => {
                    updateMessageReadStatus(messageId, false);
                });
            }
            
            clearSelection();
            
            // Optionally refresh the page
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('error', data.message || 'Action failed');
        }
    } catch (error) {
        console.error('Kesalahan:', error);
        showNotification('error', 'An error occurred');
    }
}

function updateMessageReadStatus(messageId, isRead) {
    const messageItem = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageItem) {
        // Update visual indicators
        const unreadIndicator = messageItem.querySelector('.unread-indicator');
        if (isRead && unreadIndicator) {
            unreadIndicator.remove();
        } else if (!isRead && !unreadIndicator) {
            // Add unread indicator
            const indicator = document.createElement('span');
            indicator.className = 'unread-indicator w-2 h-2 bg-blue-500 rounded-full';
            // Add to appropriate location in your message item
        }
        
        // Update background color
        if (isRead) {
            messageItem.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            messageItem.classList.add('bg-white', 'dark:bg-gray-800');
        } else {
            messageItem.classList.remove('bg-white', 'dark:bg-gray-800');
            messageItem.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
        }
    }
}

function showNotification(type, message) {
    // Simple notification - integrate with your existing notification system
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
        'bg-red-100 text-red-800 border border-red-200'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + A to select all messages
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && e.target.closest('.message-list')) {
        e.preventDefault();
        selectAll();
    }
    
    // Escape to clear selection
    if (e.key === 'Escape') {
        clearSelection();
    }
});
</script>