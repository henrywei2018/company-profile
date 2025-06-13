{{-- resources/views/components/client/client-header.blade.php - FIXED --}}
@props([
    'unreadMessagesCount' => 0,
    'pendingQuotationsCount' => 0,
    'recentNotifications' => collect(),
    'unreadNotificationsCount' => 0,
    'pendingApprovalsCount' => 0,
    'overdueProjectsCount' => 0,
])


<header
    class="sticky top-0 inset-x-0 z-50 w-full bg-white border-b border-gray-200 text-sm dark:bg-gray-800 dark:border-gray-700 lg:ps-64">
    <nav class="w-full mx-auto px-4 py-2.5 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8"
        aria-label="Global">
        <!-- Left: Logo and Mobile Menu Toggle -->
        <div class="flex items-center lg:hidden">
            <!-- Mobile Menu Toggle -->
            <button type="button"
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full mr-2"
                data-hs-overlay="#hs-application-sidebar" aria-controls="hs-application-sidebar"
                aria-label="Toggle navigation">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Mobile Logo -->
            <a href="{{ route('client.dashboard') }}" aria-label="{{ config('app.name') }}"
                class="text-xl font-bold text-blue-600 dark:text-white">
                {{ config('app.name') }}
            </a>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center justify-end w-full gap-x-2">
            <!-- Theme Toggle -->
            <button id="theme-toggle"
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full">
                <!-- Dark mode: Sun icon -->
                <svg class="hidden dark:block size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5" />
                    <line x1="12" y1="1" x2="12" y2="3" />
                    <line x1="12" y1="21" x2="12" y2="23" />
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                    <line x1="1" y1="12" x2="3" y2="12" />
                    <line x1="21" y1="12" x2="23" y2="12" />
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                </svg>
                <!-- Light mode: Moon icon -->
                <svg class="block dark:hidden size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                </svg>
                <span class="sr-only">Toggle dark mode</span>
            </button>

            <!-- FIXED: Notification Dropdown -->
            <div class="hs-dropdown relative inline-flex">
    <button type="button" 
        id="notification-dropdown-toggle"
        class="hs-dropdown-toggle relative size-[38px] inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
        aria-haspopup="menu" 
        aria-expanded="false" 
        aria-label="Notifications">
        
        <!-- Bell Icon -->
        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
            <path d="m13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        
        <!-- Badge (will be created by JavaScript if needed) -->
        <span class="sr-only">Notifications</span>
    </button>

    <!-- Enhanced Dropdown Panel -->
    <div class="hs-dropdown-menu notification-dropdown hidden z-50 mt-2 w-96 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
        aria-labelledby="notification-dropdown-toggle">
        
        <!-- Header with Quick Actions -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-800 dark:text-white">Notifications</h3>
                <div class="flex items-center gap-2">
                    <!-- Bulk Actions Toggle -->
                    <button type="button" 
                        id="bulk-actions-toggle"
                        onclick="toggleBulkActions()"
                        class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hidden transition-colors">
                        <svg class="size-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Select
                    </button>
                    
                    <!-- Quick Mark All Read -->
                    <button type="button" 
                        id="mark-all-read-btn"
                        onclick="markAllNotificationsAsRead()"
                        class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                        Mark all read
                    </button>
                    
                    <!-- View All Link -->
                    <a href="{{ route('client.notifications.index') }}" 
                       class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                        View all
                    </a>
                </div>
            </div>
            
            <!-- Bulk Actions Bar (Hidden by default) -->
            <div id="bulk-actions-bar" class="hidden mt-3 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button type="button" 
                            onclick="selectAllNotifications()"
                            class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            Select all
                        </button>
                        <button type="button" 
                            onclick="deselectAllNotifications()"
                            class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            Deselect all
                        </button>
                        <span id="selection-count" class="text-xs text-gray-500 dark:text-gray-400">
                            0 selected
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                            onclick="bulkMarkAsRead()"
                            class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors">
                            Mark read
                        </button>
                        <button type="button" 
                            onclick="bulkDelete()"
                            class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                        <button type="button" 
                            onclick="toggleBulkActions()"
                            class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="notification-filters px-4 py-2 border-b border-gray-100 dark:border-neutral-600">
            <div class="flex items-center gap-2">
                <select id="notification-filter" onchange="filterNotifications()" 
                    class="text-xs border border-gray-200 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors">
                    <option value="unread">Unread only</option>
                    <option value="all">All notifications</option>
                    <option value="read">Read only</option>
                </select>
                
                <select id="notification-category" onchange="filterNotifications()" 
                    class="text-xs border border-gray-200 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors">
                    <option value="">All categories</option>
                    <option value="project">Projects</option>
                    <option value="quotation">Quotations</option>
                    <option value="message">Messages</option>
                    <option value="chat">Chat</option>
                    <option value="user">Account</option>
                    <option value="system">System</option>
                </select>
                
                <button type="button" onclick="refreshNotifications()" 
                    class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 ml-auto p-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors">
                    <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Notification List Container -->
        <div class="max-h-80 overflow-y-auto" id="notification-list">
            <!-- Loading State -->
            <div id="notification-loading" class="px-4 py-8 text-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2"></div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Loading notifications...</p>
            </div>
            
            <!-- Notification Content (populated by JavaScript) -->
            <div id="notification-content">
                <!-- Content will be loaded here -->
            </div>
        </div>

        <!-- Footer with Summary and Actions -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700/50">
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <span id="notification-summary">Loading...</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                        onclick="openNotificationSettings()"
                        class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                        <svg class="size-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </button>
                    <a href="{{ route('client.notifications.index') }}" 
                       class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                        View all →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- User Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 inline-flex justify-center items-center rounded-full text-sm font-semibold text-gray-800 dark:text-white"
                    data-hs-dropdown-toggle>
                    @if (auth()->user()->avatar)
                        <img class="size-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}"
                            alt="{{ auth()->user()->name }}">
                    @else
                        <div class="size-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-white">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </span>
                        </div>
                    @endif
                </button>

                <div
                    class="hs-dropdown-menu hidden z-50 min-w-60 mt-2 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="px-5 py-3 bg-gray-50 dark:bg-neutral-700 rounded-t-lg">
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Signed in as</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                            {{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ auth()->user()->email }}
                        </p>
                    </div>
                    <div class="p-1.5 space-y-0.5">
                        <a href="{{ route('client.dashboard') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Dashboard
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

@push('scripts')
<script>
// Include all the functions from enhanced_notification_js artifact
{!! file_get_contents(resource_path('js/enhanced-notifications.js')) !!}

// Additional helper functions specific to this implementation
function getNotificationIconSvg(icon, color) {
    const icons = {
        'bell': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5M9 7l6 6-6 6" />',
        'folder': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />',
        'document-text': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        'mail': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
        'chat-bubble-left': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />',
        'user': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'
    };
    
    const iconPath = icons[icon] || icons['bell'];
    
    return `
        <svg class="size-4 text-${color}-600 dark:text-${color}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${iconPath}
        </svg>
    `;
}

function handleNotificationClick(notificationId, url, event) {
    // Don't navigate if we're in bulk mode or clicking on checkbox/buttons
    if (notificationState.bulkMode || 
        event.target.type === 'checkbox' || 
        event.target.closest('button') ||
        event.target.closest('.notification-checkbox')) {
        return;
    }
    
    // Mark as read if it's not already read
    if (notificationId) {
        const notification = notificationState.allNotifications.find(n => n.id === notificationId);
        if (notification && !notification.is_read) {
            markNotificationAsRead(notificationId);
        }
    }
    
    // Navigate to URL if provided and valid
    if (url && url !== '#' && url !== 'undefined' && url !== '') {
        setTimeout(() => {
            window.location.href = url;
        }, 100);
    }
}

function quickDelete(notificationId, event) {
    event.stopPropagation();
    
    if (!confirm('Are you sure you want to delete this notification?')) {
        return;
    }
    
    deleteNotification(notificationId);
}

function deleteNotification(notificationId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showToast('Security token not found', 'error');
        return;
    }
    
    fetch(`/client/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove from UI with animation
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationElement) {
                notificationElement.style.transition = 'all 0.3s ease';
                notificationElement.style.opacity = '0';
                notificationElement.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    notificationElement.remove();
                    
                    // Update state arrays
                    notificationState.allNotifications = notificationState.allNotifications.filter(n => n.id !== notificationId);
                    notificationState.unreadNotifications = notificationState.unreadNotifications.filter(n => n.id !== notificationId);
                    
                    // Update badge
                    notificationState.unreadCount = notificationState.unreadNotifications.length;
                    renderBadge(notificationState.unreadCount);
                    
                    // Check if list is empty
                    if (notificationState.allNotifications.length === 0) {
                        renderNotifications([]);
                    }
                }, 300);
            }
            
            showToast('Notification deleted', 'success');
        } else {
            showToast('Failed to delete notification', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting notification:', error);
        showToast('Error deleting notification', 'error');
    });
}

function openNotificationSettings() {
    window.location.href = '{{ route("client.notifications.preferences") }}';
}

function refreshNotifications() {
    const refreshBtn = document.querySelector('button[onclick="refreshNotifications()"]');
    if (refreshBtn) {
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<svg class="size-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        refreshBtn.disabled = true;
        
        // Update badge count first
        updateNotificationBadge();
        
        // Then reload notifications
        loadNotifications().finally(() => {
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        });
    } else {
        updateNotificationBadge();
        loadNotifications();
    }
}

// Bulk action functions (implementation from enhanced_notification_js)
function toggleBulkActions() {
    notificationState.bulkMode = !notificationState.bulkMode;
    
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const bulkToggleBtn = document.getElementById('bulk-actions-toggle');
    const contentEl = document.getElementById('notification-content');
    
    if (notificationState.bulkMode) {
        bulkActionsBar.classList.remove('hidden');
        bulkActionsBar.classList.add('fade-in');
        bulkToggleBtn.textContent = 'Cancel';
        contentEl.classList.add('bulk-mode');
    } else {
        bulkActionsBar.classList.add('hidden');
        bulkToggleBtn.textContent = 'Select';
        contentEl.classList.remove('bulk-mode');
        // Clear selections
        notificationState.selectedNotifications.clear();
        updateSelectionUI();
    }
    
    updateBulkActionsVisibility();
}

function updateBulkActionsVisibility() {
    const toggleBtn = document.getElementById('bulk-actions-toggle');
    if (toggleBtn) {
        if (notificationState.allNotifications && notificationState.allNotifications.length > 0) {
            toggleBtn.classList.remove('hidden');
        } else {
            toggleBtn.classList.add('hidden');
        }
    }
}

function selectAllNotifications() {
    notificationState.allNotifications.forEach(notification => {
        notificationState.selectedNotifications.add(notification.id);
    });
    updateSelectionUI();
}

function deselectAllNotifications() {
    notificationState.selectedNotifications.clear();
    updateSelectionUI();
}

function toggleNotificationSelection(notificationId) {
    if (notificationState.selectedNotifications.has(notificationId)) {
        notificationState.selectedNotifications.delete(notificationId);
    } else {
        notificationState.selectedNotifications.add(notificationId);
    }
    updateSelectionCount();
}

function updateSelectionUI() {
    // Update checkboxes
    document.querySelectorAll('.notification-item input[type="checkbox"]').forEach(checkbox => {
        const notificationId = checkbox.closest('.notification-item').dataset.notificationId;
        checkbox.checked = notificationState.selectedNotifications.has(notificationId);
        
        // Update visual selection
        const item = checkbox.closest('.notification-item');
        if (checkbox.checked) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    });
    
    updateSelectionCount();
}

function updateSelectionCount() {
    const countEl = document.getElementById('selection-count');
    const count = notificationState.selectedNotifications.size;
    if (countEl) {
        countEl.textContent = `${count} selected`;
    }
}

function bulkMarkAsRead() {
    const selectedIds = Array.from(notificationState.selectedNotifications);
    if (selectedIds.length === 0) {
        showToast('No notifications selected', 'warning');
        return;
    }
    
    const unreadIds = selectedIds.filter(id => {
        const notification = notificationState.allNotifications.find(n => n.id === id);
        return notification && !notification.is_read;
    });
    
    if (unreadIds.length === 0) {
        showToast('All selected notifications are already read', 'info');
        return;
    }
    
    performBulkAction('/client/notifications/bulk-mark-as-read', { notification_ids: unreadIds }, 'Marking as read...')
        .then(() => {
            showToast(`${unreadIds.length} notifications marked as read`, 'success');
            loadNotifications();
            notificationState.selectedNotifications.clear();
        });
}

function bulkDelete() {
    const selectedIds = Array.from(notificationState.selectedNotifications);
    if (selectedIds.length === 0) {
        showToast('No notifications selected', 'warning');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${selectedIds.length} notifications? This action cannot be undone.`)) {
        return;
    }
    
    performBulkAction('/client/notifications/bulk-delete', { notification_ids: selectedIds }, 'Deleting...')
        .then(() => {
            showToast(`${selectedIds.length} notifications deleted`, 'success');
            loadNotifications();
            notificationState.selectedNotifications.clear();
        });
}

function performBulkAction(url, data, loadingMessage) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showToast('Security token not found', 'error');
        return Promise.reject('CSRF token missing');
    }
    
    showToast(loadingMessage, 'info');
    
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Operation failed');
        }
        return data;
    })
    .catch(error => {
        console.error('Bulk action failed:', error);
        showToast('Operation failed: ' + error.message, 'error');
        throw error;
    });
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Only work when notification dropdown is open
        const dropdown = document.querySelector('.hs-dropdown-menu:not(.hidden)');
        if (!dropdown) return;
        
        // Ctrl+A - Select all
        if (e.ctrlKey && e.key === 'a' && notificationState.bulkMode) {
            e.preventDefault();
            selectAllNotifications();
        }
        
        // Escape - Exit bulk mode or close dropdown
        if (e.key === 'Escape') {
            if (notificationState.bulkMode) {
                toggleBulkActions();
            }
        }
        
        // Delete - Delete selected notifications
        if (e.key === 'Delete' && notificationState.bulkMode && notificationState.selectedNotifications.size > 0) {
            bulkDelete();
        }
        
        // Enter - Mark selected as read
        if (e.key === 'Enter' && notificationState.bulkMode && notificationState.selectedNotifications.size > 0) {
            e.preventDefault();
            bulkMarkAsRead();
        }
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Toast notification system
function showToast(message, type = 'info', duration = 3000) {
    // Remove existing toast
    const existingToast = document.getElementById('notification-toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-black',
        info: 'bg-blue-500 text-white'
    };
    
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z" />',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
    };
    
    const toast = document.createElement('div');
    toast.id = 'notification-toast';
    toast.className = `fixed top-4 right-4 z-50 flex items-center p-4 rounded-lg shadow-lg ${colors[type]} transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `
        <svg class="size-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${icons[type]}
        </svg>
        <span class="text-sm font-medium">${escapeHtml(message)}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-current hover:text-opacity-75">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 10);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, duration);
}

</script>
    @endpush
    <style>
        .notification-dropdown {
            width: 384px;
            /* w-96 */
            max-width: 90vw;
        }

        @media (max-width: 640px) {
            .notification-dropdown {
                width: 320px;
                /* w-80 */
            }
        }

        .notification-item {
            transition: all 0.2s ease;
            position: relative;
            border-left: 3px solid transparent;
        }

        .notification-item:hover {
            transform: translateX(2px);
        }

        .notification-item:hover .flex-shrink-0:last-child {
            opacity: 1 !important;
        }

        /* Priority indicators */
        .notification-item.priority-urgent {
            border-left-color: #ef4444;
        }

        .notification-item.priority-high {
            border-left-color: #f97316;
        }

        .notification-item.priority-normal {
            border-left-color: transparent;
        }

        .notification-item.priority-low {
            border-left-color: #10b981;
        }

        /* Selection states */
        .notification-item.selected {
            background-color: rgba(59, 130, 246, 0.1) !important;
            border-left-color: #3b82f6 !important;
        }

        /* Bulk mode styles */
        .notification-checkbox {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .bulk-mode .notification-checkbox {
            opacity: 1;
        }

        .priority-badge {
            font-size: 0.65rem;
            line-height: 1;
            padding: 0.25rem 0.5rem;
        }

        .priority-badge.priority-urgent {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .priority-badge.priority-high {
            background-color: #fed7aa;
            color: #ea580c;
            border: 1px solid #fdba74;
        }

        .priority-badge.priority-low {
            background-color: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        /* Dark mode priority badges */
        .dark .priority-badge.priority-urgent {
            background-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .dark .priority-badge.priority-high {
            background-color: rgba(249, 115, 22, 0.2);
            color: #fdba74;
            border-color: rgba(249, 115, 22, 0.3);
        }

        .dark .priority-badge.priority-low {
            background-color: rgba(16, 185, 129, 0.2);
            color: #a7f3d0;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .fade-in {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-out {
            animation: slideOut 0.3s ease forwards;
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(-100%);
            }
        }

        /* Loading shimmer effect */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        .dark .loading-shimmer {
            background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
            background-size: 200% 100%;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        #notification-list::-webkit-scrollbar {
            width: 4px;
        }

        #notification-list::-webkit-scrollbar-track {
            background: transparent;
        }

        #notification-list::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 2px;
        }

        #notification-list::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .dark #notification-list::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        .dark #notification-list::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Firefox scrollbar */
        #notification-list {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }

        .dark #notification-list {
            scrollbar-color: #4b5563 transparent;
        }

        #bulk-actions-bar {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            backdrop-filter: blur(10px);
        }

        #bulk-actions-bar.hidden {
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
        }

        .notification-filters select {
            transition: all 0.2s ease;
        }

        .notification-filters select:focus {
            outline: none;
            ring: 2px;
            ring-color: #3b82f6;
            ring-opacity: 0.5;
        }

        .notification-item .flex-shrink-0:last-child {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .notification-item:hover .flex-shrink-0:last-child {
            opacity: 1;
        }

        .notification-item .flex-shrink-0:last-child button {
            transition: all 0.2s ease;
            border-radius: 0.25rem;
            padding: 0.25rem;
        }

        .notification-item .flex-shrink-0:last-child button:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: scale(1.1);
        }

        .dark .notification-item .flex-shrink-0:last-child button:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        #notification-toast {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
        }

        #notification-toast svg {
            flex-shrink: 0;
        }

        @media (max-width: 640px) {
            .notification-dropdown {
                left: 1rem !important;
                right: 1rem !important;
                width: auto !important;
                margin-top: 0.5rem;
            }

            .notification-item {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .notification-item .text-sm {
                font-size: 0.8rem;
            }

            .notification-item .text-xs {
                font-size: 0.7rem;
            }

            #bulk-actions-bar {
                padding: 0.5rem;
            }

            #bulk-actions-bar .flex {
                flex-direction: column;
                gap: 0.5rem;
            }

            #bulk-actions-bar .flex:first-child {
                justify-content: center;
            }
        }

        @media (prefers-contrast: high) {
            .notification-item {
                border: 1px solid currentColor;
            }

            .notification-item.selected {
                border: 2px solid #3b82f6;
            }

            .priority-badge {
                border-width: 2px;
                font-weight: bold;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .notification-item,
            #bulk-actions-bar,
            .notification-checkbox,
            #notification-toast {
                transition: none;
            }

            .fade-in,
            .slide-out,
            .loading-shimmer {
                animation: none;
            }

            .notification-item:hover {
                transform: none;
            }
        }

        .notification-item:focus-within {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        .notification-checkbox input[type="checkbox"]:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @media print {

            .notification-dropdown,
            #notification-toast,
            #bulk-actions-bar {
                display: none !important;
            }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .text-ellipsis {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Notification badge positioning */
        .notification-badge-container {
            position: relative;
        }

        .notification-badge-container .notification-badge {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            min-width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 9999px;
            border: 2px solid white;
        }

        .dark .notification-badge-container .notification-badge {
            border-color: #1f2937;
        }
    </style>
