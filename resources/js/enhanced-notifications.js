// Optimized Notification System - Focus on Unread Notifications
let notificationState = {
    bulkMode: false,
    selectedNotifications: new Set(),
    unreadNotifications: [],
    allNotifications: [],
    currentFilter: 'unread', // Default to unread
    currentCategory: '',
    unreadCount: 0
};

document.addEventListener('DOMContentLoaded', function() {
    initializeNotificationSystem();
});

function initializeNotificationSystem() {
    // Load initial badge count
    updateNotificationBadge();
    
    // Load notifications when dropdown is opened
    setupDropdownListeners();
    
    // Auto-refresh badge count every 30 seconds
    setInterval(updateNotificationBadge, 30000);
    
    // Set up keyboard shortcuts
    setupKeyboardShortcuts();
}

function setupDropdownListeners() {
    const dropdownToggle = document.getElementById('notification-dropdown-toggle');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function() {
            // Load notifications when dropdown is opened
            setTimeout(loadNotifications, 100);
        });
    }
}

// ===================================================================
// BADGE COUNT MANAGEMENT (Primary Function)
// ===================================================================

function updateNotificationBadge() {
    fetch('/client/notifications/unread-count')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notificationState.unreadCount = data.count;
            renderBadge(data.count);
            
            // Update summary if visible
            updateNotificationSummary({
                unread_count: data.count,
                total_count: data.total_badge_count || data.count
            });
        }
    })
    .catch(error => {
        console.error('Error updating notification badge:', error);
    });
}

function renderBadge(count) {
    const badge = document.getElementById('notification-badge');
    
    if (count > 0) {
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline-flex';
            badge.className = 'absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse';
        } else {
            // Create badge if it doesn't exist
            createNotificationBadge(count);
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
    }
}

function createNotificationBadge(count) {
    const toggleBtn = document.getElementById('notification-dropdown-toggle');
    if (toggleBtn && !document.getElementById('notification-badge')) {
        const newBadge = document.createElement('span');
        newBadge.id = 'notification-badge';
        newBadge.className = 'absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse';
        newBadge.textContent = count > 99 ? '99+' : count;
        toggleBtn.appendChild(newBadge);
    }
}

// ===================================================================
// NOTIFICATION LOADING (Focus on Unread)
// ===================================================================

function loadNotifications() {
    showLoading(true);
    
    // Always load unread first, then allow filtering
    const queryParams = new URLSearchParams({
        limit: '20',
        unread_only: notificationState.currentFilter === 'unread' ? 'true' : 'false'
    });
    
    if (notificationState.currentCategory) {
        queryParams.append('category', notificationState.currentCategory);
    }
    
    fetch(`/client/notifications/recent?${queryParams}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notificationState.allNotifications = data.notifications || [];
            
            // Separate unread for quick access
            notificationState.unreadNotifications = notificationState.allNotifications.filter(n => !n.is_read);
            
            renderNotifications(notificationState.allNotifications);
            updateNotificationSummary(data);
            
            // Update badge with server data
            if (data.unread_count !== undefined) {
                notificationState.unreadCount = data.unread_count;
                renderBadge(data.unread_count);
            }
        } else {
            showNotificationError('Failed to load notifications');
        }
    })
    .catch(error => {
        console.error('Error loading notifications:', error);
        showNotificationError('Network error occurred');
    })
    .finally(() => {
        showLoading(false);
    });
}

function renderNotifications(notifications) {
    const contentEl = document.getElementById('notification-content');
    if (!contentEl) return;
    
    if (!notifications || notifications.length === 0) {
        contentEl.innerHTML = getEmptyStateHtml();
        updateBulkActionsVisibility();
        return;
    }
    
    const html = notifications.map(notification => renderNotificationItem(notification)).join('');
    contentEl.innerHTML = html;
    
    updateBulkActionsVisibility();
}

function renderNotificationItem(notification) {
    const isRead = notification.is_read || false;
    const isSelected = notificationState.selectedNotifications.has(notification.id);
    const unreadClass = !isRead ? 'bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-500' : '';
    const selectedClass = isSelected ? 'selected ring-2 ring-blue-500' : '';
    const priorityClass = `priority-${notification.priority || 'normal'}`;
    
    const formattedTime = formatNotificationTime(notification);
    const title = escapeHtml(notification.title || 'Notification');
    const message = escapeHtml(notification.message || '');
    const actionUrl = notification.action_url || '#';
    const notificationId = notification.id || '';
    const color = notification.color || 'blue';
    const icon = notification.icon || 'bell';
    
    return `
        <div class="notification-item group px-4 py-3 border-b border-gray-100 dark:border-neutral-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer transition-all ${unreadClass} ${selectedClass} ${priorityClass}"
             data-notification-id="${notificationId}"
             data-notification-read="${isRead}"
             data-notification-category="${notification.category || ''}">
            
            <div class="flex items-start space-x-3">
                <!-- Bulk Selection Checkbox -->
                <div class="notification-checkbox flex-shrink-0 mt-1 ${notificationState.bulkMode ? 'opacity-100' : 'opacity-0'}">
                    <input type="checkbox" 
                           class="size-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 transition-opacity"
                           onchange="toggleNotificationSelection('${notificationId}')"
                           ${isSelected ? 'checked' : ''}>
                </div>
                
                <!-- Notification Icon -->
                <div class="flex-shrink-0">
                    <div class="size-8 bg-${color}-100 dark:bg-${color}-900/30 rounded-lg flex items-center justify-center">
                        ${getNotificationIconSvg(icon, color)}
                    </div>
                </div>
                
                <!-- Notification Content -->
                <div class="flex-1 min-w-0" onclick="handleNotificationClick('${notificationId}', '${actionUrl}', event)">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate pr-2">
                            ${title}
                            ${!isRead ? '<span class="inline-block w-2 h-2 bg-blue-600 rounded-full ml-2 flex-shrink-0"></span>' : ''}
                        </p>
                        <span class="text-xs text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                            ${formattedTime}
                        </span>
                    </div>
                    ${message ? `<p class="mt-1 text-xs text-gray-600 dark:text-neutral-300 line-clamp-2">${message}</p>` : ''}
                    
                    <!-- Priority & Category badges -->
                    <div class="flex items-center gap-1 mt-1">
                        ${notification.priority && notification.priority !== 'normal' ? 
                            `<span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full priority-badge priority-${notification.priority}">
                                ${notification.priority.charAt(0).toUpperCase() + notification.priority.slice(1)}
                            </span>` : ''}
                        ${notification.category ? 
                            `<span class="inline-block px-2 py-0.5 text-xs text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                                ${notification.category}
                            </span>` : ''}
                    </div>
                </div>
                
                <!-- Quick Action Buttons -->
                <div class="flex-shrink-0 flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    ${!isRead ? 
                        `<button type="button" 
                                 onclick="quickMarkAsRead('${notificationId}', event)"
                                 class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-all duration-200"
                                 title="Mark as read">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>` : ''}
                    
                    <button type="button" 
                             onclick="quickDelete('${notificationId}', event)"
                             class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-all duration-200"
                             title="Delete">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// ===================================================================
// QUICK ACTIONS (Enhanced with Badge Updates)
// ===================================================================

function quickMarkAsRead(notificationId, event) {
    event.stopPropagation();
    markNotificationAsRead(notificationId);
}

function markNotificationAsRead(notificationId) {
    if (!notificationId || notificationId === 'undefined') return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showToast('Security token not found', 'error');
        return;
    }
    
    fetch(`/client/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update notification in state
            const notification = notificationState.allNotifications.find(n => n.id === notificationId);
            if (notification) {
                notification.is_read = true;
            }
            
            // Remove from unread list
            notificationState.unreadNotifications = notificationState.unreadNotifications.filter(n => n.id !== notificationId);
            
            // Update UI
            updateNotificationItemUI(notificationId, true);
            
            // Update badge immediately
            notificationState.unreadCount = Math.max(0, notificationState.unreadCount - 1);
            renderBadge(notificationState.unreadCount);
            
            // Update summary
            updateNotificationSummary({
                unread_count: notificationState.unreadCount,
                total_count: notificationState.allNotifications.length
            });
            
            showToast('Marked as read', 'success');
            
            // If we're filtering unread only, remove the item
            if (notificationState.currentFilter === 'unread') {
                setTimeout(() => {
                    const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (element) {
                        element.style.transition = 'all 0.3s ease';
                        element.style.opacity = '0';
                        element.style.transform = 'translateX(-100%)';
                        setTimeout(() => {
                            element.remove();
                            // Update state
                            notificationState.allNotifications = notificationState.allNotifications.filter(n => n.id !== notificationId);
                            if (notificationState.allNotifications.length === 0) {
                                renderNotifications([]);
                            }
                        }, 300);
                    }
                }, 500);
            }
        } else {
            showToast('Failed to mark as read', 'error');
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        showToast('Error marking as read', 'error');
    });
}

function markAllNotificationsAsRead() {
    const unreadCount = notificationState.unreadNotifications.length;
    if (unreadCount === 0) {
        showToast('All notifications are already read', 'info');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showToast('Security token not found', 'error');
        return;
    }
    
    showToast(`Marking ${unreadCount} notifications as read...`, 'info');
    
    fetch('/client/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all notifications to read in state
            notificationState.allNotifications.forEach(n => n.is_read = true);
            notificationState.unreadNotifications = [];
            notificationState.unreadCount = 0;
            
            // Update badge
            renderBadge(0);
            
            // Show success message
            showToast(`${unreadCount} notifications marked as read`, 'success');
            
            // Reload notifications to reflect changes
            setTimeout(loadNotifications, 500);
        } else {
            showToast('Failed to mark all as read', 'error');
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
        showToast('Error marking all as read', 'error');
    });
}

function updateNotificationItemUI(notificationId, isRead) {
    const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notificationElement) {
        if (isRead) {
            notificationElement.classList.remove('bg-blue-50', 'dark:bg-blue-900/10', 'border-l-4', 'border-blue-500');
            notificationElement.setAttribute('data-notification-read', 'true');
            
            // Remove unread dot
            const unreadDot = notificationElement.querySelector('.w-2.h-2.bg-blue-600');
            if (unreadDot) {
                unreadDot.remove();
            }
            
            // Remove quick mark as read button
            const markReadBtn = notificationElement.querySelector('button[onclick*="quickMarkAsRead"]');
            if (markReadBtn) {
                markReadBtn.style.display = 'none';
            }
        }
    }
}

// ===================================================================
// FILTER MANAGEMENT
// ===================================================================

function filterNotifications() {
    const filterSelect = document.getElementById('notification-filter');
    const categorySelect = document.getElementById('notification-category');
    
    if (filterSelect) {
        notificationState.currentFilter = filterSelect.value;
    }
    if (categorySelect) {
        notificationState.currentCategory = categorySelect.value;
    }
    
    // Reload with new filters
    loadNotifications();
}

function getEmptyStateHtml() {
    const filter = notificationState.currentFilter;
    const category = notificationState.currentCategory;
    
    let message = 'No notifications found';
    let subMessage = 'You\'re all caught up!';
    let icon = 'M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3';
    
    if (filter === 'unread') {
        message = 'No unread notifications';
        subMessage = 'All your notifications have been read';
        icon = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
    } else if (filter === 'read') {
        message = 'No read notifications';
        subMessage = 'You haven\'t read any notifications yet';
    } else if (category) {
        message = `No ${category} notifications`;
        subMessage = 'Try selecting a different category';
    }
    
    return `
        <div class="px-4 py-12 text-center">
            <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}" />
                </svg>
            </div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">${message}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">${subMessage}</p>
            ${(filter !== 'unread' || category) ? 
                '<button onclick="clearFilters()" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full transition-colors">Show all notifications</button>' : ''}
        </div>
    `;
}

function clearFilters() {
    const filterSelect = document.getElementById('notification-filter');
    const categorySelect = document.getElementById('notification-category');
    
    if (filterSelect) filterSelect.value = 'unread';
    if (categorySelect) categorySelect.value = '';
    
    notificationState.currentFilter = 'unread';
    notificationState.currentCategory = '';
    loadNotifications();
}

// ===================================================================
// UTILITY FUNCTIONS
// ===================================================================

function updateNotificationSummary(data) {
    const summaryEl = document.getElementById('notification-summary');
    if (summaryEl && data) {
        const total = data.total_count || notificationState.allNotifications.length;
        const unread = data.unread_count || notificationState.unreadCount;
        summaryEl.textContent = `${unread} unread of ${total} total`;
    }
}

function formatNotificationTime(notification) {
    if (notification.formatted_time) {
        return notification.formatted_time;
    } else if (notification.created_at) {
        const createdAt = new Date(notification.created_at);
        const now = new Date();
        const diffInMinutes = Math.floor((now - createdAt) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'now';
        if (diffInMinutes < 60) return `${diffInMinutes}m`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h`;
        return `${Math.floor(diffInMinutes / 1440)}d`;
    }
    return 'now';
}

function showLoading(show) {
    const loadingEl = document.getElementById('notification-loading');
    const contentEl = document.getElementById('notification-content');
    
    if (show) {
        if (loadingEl) loadingEl.style.display = 'block';
        if (contentEl) contentEl.style.opacity = '0.5';
    } else {
        if (loadingEl) loadingEl.style.display = 'none';
        if (contentEl) contentEl.style.opacity = '1';
    }
}

function showNotificationError(message = 'Failed to load notifications') {
    const contentEl = document.getElementById('notification-content');
    if (!contentEl) return;
    
    contentEl.innerHTML = `
        <div class="px-4 py-8 text-center">
            <div class="mx-auto w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <p class="text-sm text-gray-900 dark:text-white font-medium mb-1">Oops! Something went wrong</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">${message}</p>
            <button onclick="loadNotifications()" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full transition-colors">
                Try again
            </button>
        </div>
    `;
}