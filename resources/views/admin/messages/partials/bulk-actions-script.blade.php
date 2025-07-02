{{-- resources/views/admin/messages/partials/bulk-actions-script.blade.php --}}
<script>
let selectedMessages = new Set();

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.message-checkbox:checked');
    selectedMessages.clear();
    
    checkboxes.forEach(checkbox => {
        selectedMessages.add(parseInt(checkbox.value));
    });
    
    const count = selectedMessages.size;
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const bulkActionsPanel = document.getElementById('bulk-actions');
    const countDisplay = document.getElementById('selected-count');
    const selectAllCheckbox = document.getElementById('select-all');
    
    if (count > 0) {
        bulkActionsBar.style.display = 'block';
        bulkActionsPanel.classList.remove('hidden');
        countDisplay.textContent = `${count} message${count > 1 ? 's' : ''} selected`;
        
        // Update select all checkbox state
        const totalCheckboxes = document.querySelectorAll('.message-checkbox').length;
        if (count === totalCheckboxes) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = count > 0;
        }
    } else {
        bulkActionsBar.style.display = 'none';
        bulkActionsPanel.classList.add('hidden');
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
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
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.message-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActions();
}

async function bulkAction(action) {
    if (selectedMessages.size === 0) {
        alert('Please select messages first.');
        return;
    }

    // Confirmation for destructive actions
    if (action === 'delete') {
        if (!confirm(`Are you sure you want to delete ${selectedMessages.size} message(s)? This action cannot be undone.`)) {
            return;
        }
    }

    try {
        const response = await fetch('{{ route("admin.messages.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                message_ids: Array.from(selectedMessages)
            })
        });

        const data = await response.json();

        if (data.success) {
            // Show success message
            showNotification(data.message || `Successfully performed ${action} on ${selectedMessages.size} message(s).`, 'success');
            
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

// Admin-specific bulk actions
async function bulkMarkAsRead() {
    await bulkAction('mark_read');
}

async function bulkMarkAsUnread() {
    await bulkAction('mark_unread');
}

async function bulkDelete() {
    await bulkAction('delete');
}

async function bulkArchive() {
    await bulkAction('archive');
}

async function bulkAssignPriority(priority) {
    if (selectedMessages.size === 0) {
        alert('Please select messages first.');
        return;
    }

    try {
        const response = await fetch('{{ route("admin.messages.bulk-priority") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                priority: priority,
                message_ids: Array.from(selectedMessages)
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(`Successfully updated priority to ${priority} for ${selectedMessages.size} message(s).`, 'success');
            clearSelection();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'An error occurred while updating priority.', 'error');
        }
    } catch (error) {
        console.error('Priority update error:', error);
        showNotification('An error occurred while updating priority.', 'error');
    }
}

async function bulkForward() {
    if (selectedMessages.size === 0) {
        alert('Please select messages first.');
        return;
    }

    const email = prompt('Enter email address to forward messages to:');
    if (!email) return;

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    try {
        const response = await fetch('{{ route("admin.messages.bulk-forward") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                message_ids: Array.from(selectedMessages)
            })
        });

        const data = await response.json();

        if (data.success) {
            showNotification(`Successfully forwarded ${selectedMessages.size} message(s) to ${email}.`, 'success');
            clearSelection();
        } else {
            showNotification(data.message || 'An error occurred while forwarding messages.', 'error');
        }
    } catch (error) {
        console.error('Forward error:', error);
        showNotification('An error occurred while forwarding messages.', 'error');
    }
}

// Notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 transform ${
        type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
        type === 'error' ? 'bg-red-100 border-red-400 text-red-700' :
        'bg-blue-100 border-blue-400 text-blue-700'
    } border`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' :
                    type === 'error' ?
                    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>' :
                    '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" class="inline-flex rounded-md p-1.5 hover:bg-gray-100 focus:outline-none">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
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

// Advanced filtering functions
function showAdvancedFilters() {
    const modal = document.getElementById('advanced-filters-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function hideAdvancedFilters() {
    const modal = document.getElementById('advanced-filters-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Quick actions for individual messages
function quickToggleRead(messageId, isRead) {
    fetch(`{{ route('admin.messages.index') }}/${messageId}/toggle-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI without full page reload
            const row = document.querySelector(`tr[data-message-id="${messageId}"]`);
            if (row) {
                if (isRead) {
                    row.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                } else {
                    row.classList.add('bg-blue-50', 'dark:bg-blue-900/10');
                }
            }
            showNotification(`Message marked as ${isRead ? 'unread' : 'read'}.`, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating the message.', 'error');
    });
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + A to select all
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !e.target.matches('input, textarea')) {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
                selectAll();
            }
        }
        
        // Delete key to delete selected
        if (e.key === 'Delete' && selectedMessages.size > 0 && !e.target.matches('input, textarea')) {
            e.preventDefault();
            bulkDelete();
        }
        
        // Escape key to clear selection
        if (e.key === 'Escape') {
            clearSelection();
        }
    });
});
</script>