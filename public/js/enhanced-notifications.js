/**
 * Universal Notification System for Laravel 12
 * Supports both Client and Admin dashboards with reusable configuration
 */

class UniversalNotificationSystem {
    constructor(config = {}) {
        this.config = {
            // Default configuration
            variant: 'client', // 'client' or 'admin'
            endpoints: {
                client: {
                    unreadCount: '/client/notifications/unread-count',
                    recent: '/client/notifications/recent',
                    markAsRead: '/client/notifications/{id}/read',
                    markAllAsRead: '/client/notifications/mark-all-read',
                    bulkMarkAsRead: '/client/notifications/bulk-mark-read',
                    bulkDelete: '/client/notifications/bulk-delete'
                },
                admin: {
                    unreadCount: '/admin/notifications/unread-count',
                    recent: '/admin/notifications/recent',
                    markAsRead: '/admin/notifications/{id}/read',
                    markAllAsRead: '/admin/notifications/mark-all-read',
                    bulkMarkAsRead: '/admin/notifications/bulk-mark-read',
                    bulkDelete: '/admin/notifications/bulk-delete'
                }
            },
            selectors: {
                dropdownToggle: 'notification-dropdown-toggle',
                dropdownMenu: '.notification-dropdown',
                badge: 'notification-badge',
                content: 'notification-content',
                filter: 'notification-filter',
                category: 'notification-category',
                summary: 'notification-summary',
                bulkActions: 'bulk-actions'
            },
            autoRefresh: 30000, // 30 seconds
            maxRetries: 3,
            ...config
        };

        this.state = {
            bulkMode: false,
            selectedNotifications: new Set(),
            unreadNotifications: [],
            allNotifications: [],
            currentFilter: 'unread',
            currentCategory: '',
            unreadCount: 0,
            isLoading: false,
            retryCount: 0
        };

        this.dropdownInstance = null;
        this.init();
    }

    /**
     * Initialize the notification system
     */
    init() {
        this.setupDropdownListeners();
        this.setupEventPrevention();
        this.updateNotificationBadge();
        this.startAutoRefresh();
        this.setupKeyboardShortcuts();
    }

    /**
     * Setup dropdown event listeners with proper event handling
     */
    setupDropdownListeners() {
        const dropdownToggle = document.getElementById(this.config.selectors.dropdownToggle);
        const dropdownMenu = document.querySelector(this.config.selectors.dropdownMenu);
        
        if (!dropdownToggle) return;

        // Handle dropdown toggle
        dropdownToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !dropdownMenu?.classList.contains('hidden');
            
            if (!isOpen) {
                // Load notifications when opening
                setTimeout(() => this.loadNotifications(), 100);
            }
        });

        // Store reference to HSDropdown instance if available
        if (window.HSDropdown && dropdownToggle.closest('.hs-dropdown')) {
            this.dropdownInstance = window.HSDropdown.getInstance(dropdownToggle.closest('.hs-dropdown'));
        }
    }

    /**
     * Prevent dropdown from closing when interacting with internal elements
     */
    setupEventPrevention() {
        const dropdownMenu = document.querySelector(this.config.selectors.dropdownMenu);
        if (!dropdownMenu) return;

        // Prevent dropdown close on internal clicks
        dropdownMenu.addEventListener('click', (e) => {
            // Allow dropdown to close only for specific elements
            const allowClose = e.target.closest('a[href]') || 
                              e.target.closest('[data-allow-close="true"]') ||
                              e.target.closest('.notification-item:not(.bulk-mode)');
            
            if (!allowClose) {
                e.stopPropagation();
                e.preventDefault();
            }
        });

        // Setup filter change handlers with event prevention
        this.setupFilterHandlers();
    }

    /**
     * Setup filter handlers with proper event prevention
     */
    setupFilterHandlers() {
        const filterSelect = document.getElementById(this.config.selectors.filter);
        const categorySelect = document.getElementById(this.config.selectors.category);

        if (filterSelect) {
            filterSelect.addEventListener('change', (e) => {
                e.stopPropagation();
                this.handleFilterChange('filter', e.target.value);
            });

            // Prevent dropdown close on filter click/focus
            filterSelect.addEventListener('click', (e) => e.stopPropagation());
            filterSelect.addEventListener('focus', (e) => e.stopPropagation());
        }

        if (categorySelect) {
            categorySelect.addEventListener('change', (e) => {
                e.stopPropagation();
                this.handleFilterChange('category', e.target.value);
            });

            // Prevent dropdown close on category click/focus
            categorySelect.addEventListener('click', (e) => e.stopPropagation());
            categorySelect.addEventListener('focus', (e) => e.stopPropagation());
        }
    }

    /**
     * Handle filter changes
     */
    handleFilterChange(type, value) {
        if (type === 'filter') {
            this.state.currentFilter = value;
        } else if (type === 'category') {
            this.state.currentCategory = value;
        }

        // Debounce the filter change
        clearTimeout(this.filterTimeout);
        this.filterTimeout = setTimeout(() => {
            this.loadNotifications();
        }, 300);
    }

    /**
     * Clear all filters
     */
    clearFilters(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        const filterSelect = document.getElementById(this.config.selectors.filter);
        const categorySelect = document.getElementById(this.config.selectors.category);
        
        if (filterSelect) filterSelect.value = 'unread';
        if (categorySelect) categorySelect.value = '';
        
        this.state.currentFilter = 'unread';
        this.state.currentCategory = '';
        this.loadNotifications();
    }

    /**
     * Get endpoint URL for current variant
     */
    getEndpoint(type, params = {}) {
        let url = this.config.endpoints[this.config.variant][type];
        
        // Replace URL parameters
        Object.entries(params).forEach(([key, value]) => {
            url = url.replace(`{${key}}`, value);
        });
        
        return url;
    }

    /**
     * Update notification badge
     */
    async updateNotificationBadge() {
        try {
            const response = await fetch(this.getEndpoint('unreadCount'));
            const data = await response.json();
            
            if (data.success) {
                this.state.unreadCount = data.count;
                this.renderBadge(data.count);
                this.updateNotificationSummary({
                    unread_count: data.count,
                    total_count: data.total_badge_count || data.count
                });
                this.state.retryCount = 0; // Reset retry count on success
            }
        } catch (error) {
            console.error('Error updating notification badge:', error);
            this.handleRetry('updateNotificationBadge');
        }
    }

    /**
     * Load notifications with current filters
     */
    async loadNotifications() {
        if (this.state.isLoading) return;
        
        this.showLoading(true);
        
        try {
            const queryParams = new URLSearchParams({
                limit: '20',
                unread_only: this.state.currentFilter === 'unread' ? 'true' : 'false'
            });
            
            if (this.state.currentCategory) {
                queryParams.append('category', this.state.currentCategory);
            }
            
            const response = await fetch(`${this.getEndpoint('recent')}?${queryParams}`);
            const data = await response.json();
            
            if (data.success) {
                this.state.allNotifications = data.notifications || [];
                this.state.unreadNotifications = this.state.allNotifications.filter(n => !n.is_read);
                
                this.renderNotifications(this.state.allNotifications);
                this.updateNotificationSummary(data);
                
                if (data.unread_count !== undefined) {
                    this.state.unreadCount = data.unread_count;
                    this.renderBadge(data.unread_count);
                }
                
                this.state.retryCount = 0; // Reset retry count on success
            } else {
                this.showNotificationError('Failed to load notifications');
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showNotificationError('Network error occurred');
            this.handleRetry('loadNotifications');
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Handle retry logic
     */
    handleRetry(methodName) {
        if (this.state.retryCount < this.config.maxRetries) {
            this.state.retryCount++;
            const delay = Math.pow(2, this.state.retryCount) * 1000; // Exponential backoff
            setTimeout(() => this[methodName](), delay);
        }
    }

    /**
     * Render notification badge
     */
    renderBadge(count) {
        let badge = document.getElementById(this.config.selectors.badge);
        
        if (count > 0) {
            if (badge) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-flex';
                badge.className = 'absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse';
            } else {
                this.createNotificationBadge(count);
            }
        } else {
            if (badge) {
                badge.style.display = 'none';
            }
        }
    }

    /**
     * Create notification badge if it doesn't exist
     */
    createNotificationBadge(count) {
        const toggleBtn = document.getElementById(this.config.selectors.dropdownToggle);
        if (toggleBtn && !document.getElementById(this.config.selectors.badge)) {
            const newBadge = document.createElement('span');
            newBadge.id = this.config.selectors.badge;
            newBadge.className = 'absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse';
            newBadge.textContent = count > 99 ? '99+' : count;
            toggleBtn.appendChild(newBadge);
        }
    }

    /**
     * Render notifications list
     */
    renderNotifications(notifications) {
        const contentEl = document.getElementById(this.config.selectors.content);
        if (!contentEl) return;
        
        if (!notifications || notifications.length === 0) {
            contentEl.innerHTML = this.getEmptyStateHtml();
            this.updateBulkActionsVisibility();
            return;
        }
        
        const html = notifications.map(notification => this.renderNotificationItem(notification)).join('');
        contentEl.innerHTML = html;
        
        this.updateBulkActionsVisibility();
    }

    /**
     * Render individual notification item
     */
    renderNotificationItem(notification) {
        const isRead = notification.is_read || false;
        const isSelected = this.state.selectedNotifications.has(notification.id);
        const unreadClass = !isRead ? 'bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-500' : '';
        const selectedClass = isSelected ? 'selected ring-2 ring-blue-500' : '';
        const priorityClass = `priority-${notification.priority || 'normal'}`;
        
        const formattedTime = this.formatNotificationTime(notification);
        const title = this.escapeHtml(notification.title || 'Notification');
        const message = this.escapeHtml(notification.message || '');
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
                    <div class="notification-checkbox flex-shrink-0 mt-1 ${this.state.bulkMode ? 'opacity-100' : 'opacity-0'}">
                        <input type="checkbox" 
                               class="size-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 transition-opacity"
                               onchange="notificationSystem.toggleNotificationSelection('${notificationId}', event)"
                               onclick="event.stopPropagation()"
                               ${isSelected ? 'checked' : ''}>
                    </div>
                    
                    <!-- Notification Icon -->
                    <div class="flex-shrink-0">
                        <div class="size-8 bg-${color}-100 dark:bg-${color}-900/30 rounded-lg flex items-center justify-center">
                            ${this.getNotificationIconSvg(icon, color)}
                        </div>
                    </div>
                    
                    <!-- Notification Content -->
                    <div class="flex-1 min-w-0" onclick="notificationSystem.handleNotificationClick('${notificationId}', '${actionUrl}', event)">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate pr-2">
                                ${title}
                                ${!isRead ? '<span class="inline-block w-2 h-2 bg-blue-600 rounded-full ml-2 flex-shrink-0"></span>' : ''}
                            </p>
                            <span class="text-xs text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                                ${formattedTime}
                            </span>
                        </div>
                        ${message ? `<p class="text-xs text-gray-600 dark:text-neutral-400 mt-1 line-clamp-2">${message}</p>` : ''}
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                        ${!isRead ? `
                            <button onclick="notificationSystem.quickMarkAsRead('${notificationId}', event)" 
                                    class="size-6 flex items-center justify-center text-gray-400 hover:text-blue-600 rounded transition-colors"
                                    title="Mark as read">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Handle notification click
     */
    handleNotificationClick(notificationId, actionUrl, event) {
        if (event) {
            event.stopPropagation();
        }

        // Mark as read if unread
        const notification = this.state.allNotifications.find(n => n.id === notificationId);
        if (notification && !notification.is_read) {
            this.quickMarkAsRead(notificationId);
        }

        // Navigate to action URL if provided and valid
        if (actionUrl && actionUrl !== '#') {
            // Close dropdown before navigation
            this.closeDropdown();
            
            setTimeout(() => {
                if (actionUrl.startsWith('http') || actionUrl.startsWith('/')) {
                    window.location.href = actionUrl;
                }
            }, 100);
        }
    }

    /**
     * Quick mark as read
     */
    async quickMarkAsRead(notificationId, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            this.showToast('Security token not found', 'error');
            return;
        }

        try {
            const response = await fetch(this.getEndpoint('markAsRead', { id: notificationId }), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (data.success) {
                // Update notification in state
                const notification = this.state.allNotifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.is_read = true;
                }
                
                // Remove from unread list
                this.state.unreadNotifications = this.state.unreadNotifications.filter(n => n.id !== notificationId);
                
                // Update UI
                this.updateNotificationItemUI(notificationId, true);
                
                // Update badge immediately
                this.state.unreadCount = Math.max(0, this.state.unreadCount - 1);
                this.renderBadge(this.state.unreadCount);
                
                this.showToast('Marked as read', 'success');
                
                // If filtering unread only, remove the item with animation
                if (this.state.currentFilter === 'unread') {
                    this.animateItemRemoval(notificationId);
                }
            } else {
                this.showToast('Failed to mark as read', 'error');
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
            this.showToast('Error marking as read', 'error');
        }
    }

    /**
     * Animate item removal
     */
    animateItemRemoval(notificationId) {
        const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (element) {
            element.style.transition = 'all 0.3s ease';
            element.style.opacity = '0';
            element.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                element.remove();
                // Check if we need to reload notifications
                const remainingNotifications = document.querySelectorAll('.notification-item');
                if (remainingNotifications.length === 0) {
                    this.loadNotifications();
                }
            }, 300);
        }
    }

    /**
     * Toggle bulk selection for notification
     */
    toggleNotificationSelection(notificationId, event) {
        if (event) {
            event.stopPropagation();
        }

        if (this.state.selectedNotifications.has(notificationId)) {
            this.state.selectedNotifications.delete(notificationId);
        } else {
            this.state.selectedNotifications.add(notificationId);
        }
        
        this.updateBulkActionsVisibility();
        this.updateNotificationSelectionUI(notificationId);
    }

    /**
     * Toggle bulk actions mode
     */
    toggleBulkActions(event) {
        if (event) {
            event.stopPropagation();
        }

        this.state.bulkMode = !this.state.bulkMode;
        this.state.selectedNotifications.clear();
        
        const dropdownMenu = document.querySelector(this.config.selectors.dropdownMenu);
        if (dropdownMenu) {
            dropdownMenu.classList.toggle('bulk-mode', this.state.bulkMode);
        }
        
        // Update all checkboxes visibility
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.style.opacity = this.state.bulkMode ? '1' : '0';
        });
        
        this.updateBulkActionsVisibility();
    }

    /**
     * Close dropdown programmatically
     */
    closeDropdown() {
        if (this.dropdownInstance) {
            this.dropdownInstance.close();
        } else {
            // Fallback for manual dropdown handling
            const dropdownMenu = document.querySelector(this.config.selectors.dropdownMenu);
            if (dropdownMenu) {
                dropdownMenu.classList.add('hidden');
            }
        }
    }

    /**
     * Start auto-refresh interval
     */
    startAutoRefresh() {
        setInterval(() => {
            this.updateNotificationBadge();
        }, this.config.autoRefresh);
    }

    /**
     * Setup keyboard shortcuts
     */
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            const dropdown = document.querySelector(`${this.config.selectors.dropdownMenu}:not(.hidden)`);
            if (!dropdown) return;
            
            // Ctrl+A - Select all
            if (e.ctrlKey && e.key === 'a' && this.state.bulkMode) {
                e.preventDefault();
                this.selectAllNotifications();
            }
            
            // Escape - Exit bulk mode or close dropdown
            if (e.key === 'Escape') {
                if (this.state.bulkMode) {
                    this.toggleBulkActions();
                } else {
                    this.closeDropdown();
                }
            }
        });
    }

    /**
     * Utility methods
     */
    showLoading(show) {
        this.state.isLoading = show;
        // Implement loading UI updates here
    }

    showToast(message, type) {
        // Implement toast notification here
        console.log(`${type.toUpperCase()}: ${message}`);
    }

    showNotificationError(message) {
        // Implement error display here
        console.error(message);
    }

    formatNotificationTime(notification) {
        // Implement time formatting here
        return new Date(notification.created_at).toLocaleDateString();
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    getNotificationIconSvg(icon, color) {
        // Return appropriate SVG based on icon type
        return `<svg class="size-4 text-${color}-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
        </svg>`;
    }

    getEmptyStateHtml() {
        return `
            <div class="px-4 py-12 text-center">
                <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No notifications found</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">You're all caught up!</p>
                ${this.state.currentFilter !== 'unread' || this.state.currentCategory ? 
                    '<button onclick="notificationSystem.clearFilters(event)" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full transition-colors">Show all notifications</button>' : ''}
            </div>
        `;
    }

    updateNotificationSummary(data) {
        // Implement summary update logic
    }

    updateBulkActionsVisibility() {
        // Implement bulk actions visibility logic
    }

    updateNotificationItemUI(notificationId, isRead) {
        // Implement individual notification UI update
    }

    updateNotificationSelectionUI(notificationId) {
        // Implement selection UI update
    }

    selectAllNotifications() {
        // Implement select all logic
    }
}

// Global initialization function
function initializeNotificationSystem(config = {}) {
    return new UniversalNotificationSystem(config);
}

// Auto-initialize for backward compatibility
let notificationSystem;
document.addEventListener('DOMContentLoaded', function() {
    // Detect variant based on current URL or data attribute
    const variant = document.body.dataset.variant || 
                   (window.location.pathname.includes('/admin/') ? 'admin' : 'client');
    
    notificationSystem = initializeNotificationSystem({ variant });
    window.notificationSystem = notificationSystem; // Make globally available
});