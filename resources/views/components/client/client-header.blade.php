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
            <a href="{{ route('client.cart.index') }}"
                class="relative inline-flex items-center p-2 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6">
                    </path>
                </svg>

                @php
                    $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                @endphp

                @if ($cartCount > 0)
                    <span data-cart-count
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                @else
                    <span data-cart-count
                        class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        0
                    </span>
                @endif
            </a>

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
            <x-notification.enhanced-dropdown :notifications="collect()" {{-- (not used, JS handles loading) --}} :unread-count="$unreadNotificationsCount ?? 0" variant="client"
                :max-display="10" :show-filters="true" :show-bulk-actions="true" />

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
                            My Profile
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
    <script src="{{ asset('js/enhanced-notifications.js') }}"></script>
    <script>
        // === CONFIG ===
        const CONTENT_ID = 'client-notification-content';
        const BADGE_ID = 'client-notification-badge';
        const LOADING_ID = 'notification-loading';
        const FILTER_ID = 'client-notification-filter';
        const CATEGORY_ID = 'client-notification-category';

        // === GLOBAL DATA ===
        let allNotifications = [];
        let unreadCount = 0;

        // === ICONS ===
        function getIconSvg(icon, color) {
            const colorClass = color ? `text-${color}-500 dark:text-${color}-400` : 'text-blue-500';
            const icons = {
                bell: `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="m13.73 21a2 2 0 0 1-3.46 0"/></svg>`,
                folder: `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>`,
                chat: `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>`,
                user: `<svg class="w-5 h-5 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`,
            };
            return icons[icon] || icons['bell'];
        }

        // === UI UPDATE FUNCTIONS ===
        function updateBadge(count) {
            const badge = document.getElementById(BADGE_ID);
            if (!badge) return;
            if (count > 0) {
                badge.innerText = count > 99 ? "99+" : count;
                badge.style.display = "inline-flex";
            } else {
                badge.style.display = "none";
            }
        }

        function renderNotifications() {
            const container = document.getElementById(CONTENT_ID);
            if (!container) return;
            // Remove loading spinner if present
            const loading = document.getElementById(LOADING_ID);
            if (loading) loading.remove();

            // Filters
            const filter = document.getElementById(FILTER_ID)?.value || 'unread';
            const category = document.getElementById(CATEGORY_ID)?.value || '';
            let filtered = allNotifications;
            if (filter === 'unread') filtered = filtered.filter(n => !n.is_read);
            else if (filter === 'read') filtered = filtered.filter(n => n.is_read);
            if (category) filtered = filtered.filter(n => n.category === category);

            if (filtered.length) {
                container.innerHTML = filtered.map(n => {
                    const isUnread = !n.is_read;
                    return `
            <div class="flex gap-3 p-3 rounded-md mb-2 border transition
                ${isUnread ? 'bg-blue-50 dark:bg-blue-900/50 border-blue-300 dark:border-blue-800 shadow-sm' : 'bg-white dark:bg-neutral-900 border-gray-200 dark:border-gray-700'}
                hover:bg-blue-100/40 dark:hover:bg-blue-900/70">
                <div class="flex-shrink-0 flex items-center">
                    ${getIconSvg(n.icon, n.color)}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center gap-2 mb-0.5">
                        <span class="font-medium text-gray-900 dark:text-white text-sm">${n.title || '(No Title)'}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap" title="${n.formatted_date || n.created_at}">${n.formatted_time || ''}</span>
                    </div>
                    <div class="text-xs text-gray-700 dark:text-gray-200">${n.message || ''}</div>
                    ${n.action_url ? `<a href="${n.action_url}" class="inline-block mt-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline transition">View</a>` : ''}
                </div>
            </div>
            `;
                }).join('');
            } else {
                container.innerHTML =
                    `<div class="p-6 text-center text-gray-400 dark:text-gray-500 text-sm">No notifications</div>`;
            }
        }

        function applyFilters() {
            renderNotifications();
        }

        // === FETCH AND RENDER ===
        function loadNotifications() {
            const container = document.getElementById(CONTENT_ID);
            if (container) {
                container.innerHTML = `
            <div id="${LOADING_ID}" class="px-4 py-8 text-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2"></div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Loading notifications...</p>
            </div>
        `;
            }
            fetch('/client/notifications/recent')
                .then(r => r.json())
                .then(data => {
                    allNotifications = data.notifications || [];
                    unreadCount = data.unread_count || 0;
                    updateBadge(unreadCount);
                    renderNotifications();
                })
                .catch(() => {
                    if (container) container.innerHTML =
                        "<div style='padding:16px;text-align:center;color:red;'>Error loading notifications</div>";
                });
        }

        // === "MARK ALL AS READ" HANDLER ===
        window.markAllNotificationsAsRead = function() {
            const btn = document.getElementById('mark-all-read-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Marking...';
            }

            fetch('/client/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                })
                .then(async r => {
                    const text = await r.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        data = null;
                    }
                    if (r.ok && data && data.success) {
                        allNotifications = allNotifications.map(n => Object.assign({}, n, {
                            is_read: true
                        }));
                        unreadCount = 0;
                        updateBadge(0);
                        renderNotifications();
                    } else {
                        alert("Backend error: " + (data && data.message ? data.message : text));
                    }
                })
                .catch((err) => {
                    alert("Fetch failed: " + (err && err.message ? err.message : err));
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerText = 'Mark all read';
                    }
                });
        };

        // === FILTERS, REFRESH, INITIALIZATION ===
        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            loadNotifications();

            // Attach filter event handlers
            const filterEl = document.getElementById(FILTER_ID);
            const catEl = document.getElementById(CATEGORY_ID);
            if (filterEl) filterEl.addEventListener('change', applyFilters);
            if (catEl) catEl.addEventListener('change', applyFilters);

            // Add refreshNotifications to window for refresh button
            window.refreshNotifications = loadNotifications;
        });
    </script>
@endpush
<style>
    .notification-dropdown {
        width: 384px;
        /* w-96 */
        max-width: 90vw;
        padding-left: 6px;
        padding-right: 12px;
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
