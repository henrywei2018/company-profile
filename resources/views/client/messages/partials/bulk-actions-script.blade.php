<!-- resources/views/client/messages/partials/bulk-actions-script.blade.php -->
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
    const selectAllCheckbox = document.getElementById('select-all');
    
    if (count > 0) {
        bulkActionsBar.style.display = 'block';
        countDisplay.textContent = count;
        
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
        showNotification('error', 'No messages selected');
        return;
    }
    
    // Show loading state
    const buttons = document.querySelectorAll('#bulk-actions button');
    buttons.forEach(btn => {
        btn.disabled = true;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
        
        // Store original content for restoration
        btn.dataset.originalContent = originalContent;
    });
    
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
            } else if (action === 'delete') {
                selectedMessages.forEach(messageId => {
                    const messageItem = document.querySelector(`[data-message-id="${messageId}"]`);
                    if (messageItem) {
                        messageItem.style.transition = 'opacity 0.3s, transform 0.3s';
                        messageItem.style.opacity = '0';
                        messageItem.style.transform = 'translateX(-100%)';
                        setTimeout(() => messageItem.remove(), 300);
                    }
                });
            }
            
            clearSelection();
            
            // Update statistics
            updateStatistics();
            
        } else {
            showNotification('error', data.message || 'Action failed');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'An error occurred while processing your request');
    } finally {
        // Restore button states
        buttons.forEach(btn => {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.originalContent || btn.innerHTML;
        });
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
            const badgeContainer = messageItem.querySelector('.flex.items-center.space-x-2.ml-2');
            if (badgeContainer) {
                badgeContainer.appendChild(indicator);
            }
        }
        
        // Update background color
        if (isRead) {
            messageItem.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            messageItem.classList.add('bg-white', 'dark:bg-gray-800');
        } else {
            messageItem.classList.remove('bg-white', 'dark:bg-gray-800');
            messageItem.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
        }
        
        // Add subtle animation
        messageItem.style.transition = 'background-color 0.3s ease';
    }
}

async function updateStatistics() {
    try {
        const response = await fetch('{{ route("api.client.messages.statistics") }}');
        const data = await response.json();
        
        if (data.success) {
            // Update statistics cards
            const unreadCountEl = document.getElementById('unread-count');
            if (unreadCountEl && data.data.unread !== undefined) {
                unreadCountEl.textContent = data.data.unread;
            }
            
            // Update other statistics if elements exist
            const stats = data.data;
            Object.keys(stats).forEach(key => {
                const element = document.getElementById(`${key}-count`);
                if (element) {
                    element.textContent = stats[key];
                }
            });
        }
    } catch (error) {
        console.error('Failed to update statistics:', error);
    }
}

function showNotification(type, message) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
        'bg-red-100 text-red-800 border border-red-200'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                ${type === 'success' ? 
                    '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                    '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                }
                <span>${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 10);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Only handle shortcuts when not typing in input fields
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
        return;
    }
    
    // Ctrl/Cmd + A to select all messages
    if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
        e.preventDefault();
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = true;
            selectAll();
        }
    }
    
    // Escape to clear selection
    if (e.key === 'Escape') {
        clearSelection();
    }
    
    // R key to mark selected as read
    if (e.key === 'r' || e.key === 'R') {
        if (selectedMessages.size > 0) {
            e.preventDefault();
            bulkAction('mark_read');
        }
    }
    
    // U key to mark selected as unread
    if (e.key === 'u' || e.key === 'U') {
        if (selectedMessages.size > 0) {
            e.preventDefault();
            bulkAction('mark_unread');
        }
    }
    
    // Delete key to delete selected messages
    if (e.key === 'Delete' || e.key === 'Backspace') {
        if (selectedMessages.size > 0) {
            e.preventDefault();
            if (confirm(`Are you sure you want to delete ${selectedMessages.size} message(s)?`)) {
                bulkAction('delete');
            }
        }
    }
});

// Initialize tooltips for keyboard shortcuts
document.addEventListener('DOMContentLoaded', function() {
    // Add keyboard shortcut hints to bulk action buttons
    const markReadBtn = document.querySelector('button[onclick="bulkAction(\'mark_read\')"]');
    if (markReadBtn) {
        markReadBtn.title = 'Mark as read (R)';
    }
    
    const markUnreadBtn = document.querySelector('button[onclick="bulkAction(\'mark_unread\')"]');
    if (markUnreadBtn) {
        markUnreadBtn.title = 'Mark as unread (U)';
    }
    
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.title = 'Select all (Ctrl+A)';
    }
});

// Auto-save selection state in session storage
function saveSelectionState() {
    sessionStorage.setItem('selectedMessages', JSON.stringify(Array.from(selectedMessages)));
}

function loadSelectionState() {
    const saved = sessionStorage.getItem('selectedMessages');
    if (saved) {
        try {
            const messageIds = JSON.parse(saved);
            messageIds.forEach(id => {
                const checkbox = document.querySelector(`.message-checkbox[value="${id}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    selectedMessages.add(id);
                }
            });
            updateBulkActions();
        } catch (e) {
            console.log('Error loading selection state:', e);
            sessionStorage.removeItem('selectedMessages');
        }
    }
}

// Save selection state when messages are selected
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('message-checkbox')) {
        saveSelectionState();
    }
});

// Load selection state on page load
window.addEventListener('load', loadSelectionState);

// Clear selection state when navigating away
window.addEventListener('beforeunload', function() {
    sessionStorage.removeItem('selectedMessages');
});

// Handle infinite scroll or pagination
function handleNewMessages() {
    // Re-attach event listeners to new message checkboxes
    const newCheckboxes = document.querySelectorAll('.message-checkbox:not([data-initialized])');
    newCheckboxes.forEach(checkbox => {
        checkbox.setAttribute('data-initialized', 'true');
        checkbox.addEventListener('change', updateBulkActions);
    });
}

// Observer for dynamically added content
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            handleNewMessages();
        }
    });
});

// Start observing
const messageList = document.querySelector('.message-list');
if (messageList) {
    observer.observe(messageList, { childList: true, subtree: true });
}

// Smooth animations for bulk actions
function animateMessageUpdate(messageId, action) {
    const messageItem = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageItem) {
        messageItem.style.transition = 'all 0.3s ease';
        
        if (action === 'delete') {
            messageItem.style.opacity = '0';
            messageItem.style.transform = 'translateX(-100%)';
            setTimeout(() => {
                if (messageItem.parentNode) {
                    messageItem.remove();
                }
            }, 300);
        } else {
            // Subtle flash animation for read/unread changes
            messageItem.style.transform = 'scale(0.98)';
            setTimeout(() => {
                messageItem.style.transform = 'scale(1)';
            }, 150);
        }
    }
}

// Enhanced error handling
window.addEventListener('error', function(e) {
    if (e.error && e.error.message && e.error.message.includes('bulk')) {
        showNotification('error', 'An error occurred during bulk operation. Please refresh the page and try again.');
    }
});

// Debounced function for better performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Debounced statistics update
const debouncedUpdateStatistics = debounce(updateStatistics, 1000);

// Use debounced version for frequent updates
function updateBulkActionsWithStats() {
    updateBulkActions();
    debouncedUpdateStatistics();
}
</script>