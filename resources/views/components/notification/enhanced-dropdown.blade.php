{{-- resources/views/components/notification/enhanced-dropdown.blade.php --}}
@props([
    'notifications' => collect(),
    'unreadCount' => 0,
    'variant' => 'client', // 'client' or 'admin'
    'maxDisplay' => 10,
    'showFilters' => true,
    'showBulkActions' => true,
    'id' => null,
    'viewAllRoute' => null, // Optional override for view all link
    'settingsRoute' => route('profile.preferences') // Optional override for settings link
])

@php
    $dropdownId = $id ?? ($variant . '-notification-dropdown');
    $toggleId = $dropdownId . '-toggle';
    $badgeId = $variant . '-notification-badge';
    $contentId = $variant . '-notification-content';
    $listId = $variant . '-notification-list';
    $filterId = $variant . '-notification-filter';
    $categoryId = $variant . '-notification-category';

    $defaultViewAll = $variant === 'admin'
        ? route('admin.notifications.index')
        : route('client.notifications.index');
    $defaultSettings = $variant === 'admin'
        ? route('admin.notifications.preferences')
        : route('client.notifications.preferences');
@endphp

<div class="hs-dropdown relative inline-flex"
    id="{{ $dropdownId }}"
    data-hs-dropdown
    data-hs-dropdown-placement="bottom-end"
    data-variant="{{ $variant }}">

    <!-- Toggle Button -->
    <button type="button"
        id="{{ $toggleId }}"
        class="hs-dropdown-toggle relative size-[38px] inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
        aria-haspopup="menu"
        aria-expanded="false"
        aria-label="Notifications">
        <!-- Bell Icon -->
        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
            <path d="m13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <!-- Badge (dynamically updated by JS) -->
        <span id="{{ $badgeId }}"
        class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse"
        style="{{ $unreadCount > 0 ? '' : 'display:none;' }}">
        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
    </span>
        <span class="sr-only">Notifications</span>
    </button>

    <!-- Dropdown Panel -->
    <div class="hs-dropdown-menu notification-dropdown hidden z-50 mt-2 w-96 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
        aria-labelledby="{{ $toggleId }}">

        <!-- Header with Actions -->
        <div class="px-1 py-2 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-800 dark:text-white">Notifications</h3>
                <div class="flex items-center gap-2">
                    @if($showBulkActions)
                        <!-- Bulk Actions Toggle (hidden by default, shown by JS) -->
                        <button type="button"
                            id="bulk-actions-toggle"
                            onclick="toggleBulkActions()"
                            class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hidden transition-colors">
                            <svg class="size-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Select
                        </button>
                    @endif
                    <!-- Quick Mark All Read -->
                    <button type="button"
                        id="mark-all-read-btn"
                        onclick="markAllNotificationsAsRead()"
                        class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                        Mark all read
                    </button>
                    <!-- View All Link -->
                    <a href="{{ $viewAllRoute ?? $defaultViewAll }}"
                        class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                        View all
                    </a>
                </div>
            </div>

            <!-- Bulk Actions Bar (hidden by default, shown by JS) -->
            @if($showBulkActions)
            <div id="bulk-actions-bar" class="hidden mt-3 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="selectAllNotifications()"
                            class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            Select all
                        </button>
                        <button type="button" onclick="deselectAllNotifications()"
                            class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            Deselect all
                        </button>
                        <span id="selection-count" class="text-xs text-gray-500 dark:text-gray-400">
                            0 selected
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="bulkMarkAsRead()"
                            class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700 transition-colors">
                            Mark read
                        </button>
                        <button type="button" onclick="bulkDelete()"
                            class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                        <button type="button" onclick="toggleBulkActions()"
                            class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Filter Controls -->
        @if($showFilters)
        <div class="notification-filters py-2 border-b border-gray-100 dark:border-neutral-600">
            <div class="flex items-center gap-2">
                <select id="{{ $filterId }}" onchange="filterNotifications()"
                    class="text-xs border border-gray-200 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors">
                    <option value="unread">Unread only</option>
                    <option value="all">All notifications</option>
                    <option value="read">Read only</option>
                </select>
                <select id="{{ $categoryId }}" onchange="filterNotifications()"
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
        @endif

        <!-- Notification List Container -->
        <div class="max-h-80 overflow-y-auto" id="{{ $listId }}">
            <!-- Loading State (JS will overwrite this) -->
            <div id="notification-loading" class="px-4 py-8 text-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2"></div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Loading notifications...</p>
            </div>
            <div id="{{ $contentId }}">
                <!-- Notification content will be injected by JS -->
            </div>
        </div>

        <!-- Footer with Summary and Actions -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700/50">
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <span id="notification-summary"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openNotificationSettings()"
                        class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                        <svg class="size-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </button>
                    <a href="{{ $viewAllRoute ?? $defaultViewAll }}"
                        class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                        View all →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
