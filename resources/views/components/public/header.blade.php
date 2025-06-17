{{-- Include Preline JS for dropdown functionality --}}
@once
    @push('scripts')
    <script>
        // Header Admin Navigation JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Get user meta data from page
            const isAdmin = document.querySelector('meta[name="is-admin"]')?.getAttribute('content') === 'true';
            const isClient = document.querySelector('meta[name="is-client"]')?.getAttribute('content') === 'true';
            const userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
            const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');

            // Initialize admin navigation features
            if (isAdmin || isClient) {
                initAdminNavigation();
            }

            // Initialize user dropdown functionality
            initUserDropdown();

            // Initialize dashboard quick access
            initDashboardQuickAccess();

            // Initialize notification system
            if (userId) {
                initNotificationSystem(userId);
            }

            // Initialize mobile menu toggle
            initMobileMenu();

            // Initialize theme toggle
            initThemeToggle();

            // Initialize dropdown menus
            initDropdownMenus();
        });

        function initAdminNavigation() {
            // Add admin-specific navigation features
            const adminLinks = document.querySelectorAll('a[href*="admin.dashboard"], a[href*="client.dashboard"]');
            
            adminLinks.forEach(link => {
                // Add loading state on click
                link.addEventListener('click', function(e) {
                    const button = e.currentTarget;
                    const originalHtml = button.innerHTML;
                    
                    // Add loading state
                    button.classList.add('loading');
                    button.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Loading...
                    `;
                    
                    // Remove loading state after navigation (fallback)
                    setTimeout(() => {
                        button.classList.remove('loading');
                        button.innerHTML = originalHtml;
                    }, 3000);
                });

                // Add keyboard shortcut for quick access
                if (link.href.includes('admin.dashboard')) {
                    document.addEventListener('keydown', function(e) {
                        if (e.ctrlKey && e.shiftKey && e.key === 'A') {
                            e.preventDefault();
                            link.click();
                        }
                    });
                }
            });
        }

        function initUserDropdown() {
            const userDropdowns = document.querySelectorAll('.group');
            
            userDropdowns.forEach(dropdown => {
                const dropdownMenu = dropdown.querySelector('.absolute');
                if (!dropdownMenu) return;

                let dropdownTimeout;

                // Show dropdown on hover
                dropdown.addEventListener('mouseenter', function() {
                    clearTimeout(dropdownTimeout);
                    dropdownMenu.classList.remove('opacity-0', 'invisible');
                    dropdownMenu.classList.add('opacity-100', 'visible');
                });

                // Hide dropdown on mouse leave with delay
                dropdown.addEventListener('mouseleave', function() {
                    dropdownTimeout = setTimeout(() => {
                        dropdownMenu.classList.remove('opacity-100', 'visible');
                        dropdownMenu.classList.add('opacity-0', 'invisible');
                    }, 300);
                });

                // Keep dropdown open when hovering over menu
                dropdownMenu.addEventListener('mouseenter', function() {
                    clearTimeout(dropdownTimeout);
                });

                dropdownMenu.addEventListener('mouseleave', function() {
                    dropdownTimeout = setTimeout(() => {
                        dropdownMenu.classList.remove('opacity-100', 'visible');
                        dropdownMenu.classList.add('opacity-0', 'invisible');
                    }, 300);
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                userDropdowns.forEach(dropdown => {
                    const dropdownMenu = dropdown.querySelector('.absolute');
                    if (dropdownMenu && !dropdown.contains(e.target)) {
                        dropdownMenu.classList.remove('opacity-100', 'visible');
                        dropdownMenu.classList.add('opacity-0', 'invisible');
                    }
                });
            });
        }

        function initDashboardQuickAccess() {
            // Add quick access tooltips
            const dashboardLinks = document.querySelectorAll('a[href*="dashboard"]');
            
            dashboardLinks.forEach(link => {
                const href = link.getAttribute('href');
                let tooltipText = 'Dashboard';
                
                if (href.includes('admin')) {
                    tooltipText = 'Admin Dashboard - Manage system settings and users';
                } else if (href.includes('client')) {
                    tooltipText = 'Client Dashboard - View projects and communications';
                }
                
                // Add tooltip
                link.setAttribute('title', tooltipText);
                link.setAttribute('aria-label', tooltipText);
            });

            // Add keyboard shortcuts info for admins
            const isAdmin = document.querySelector('meta[name="is-admin"]')?.getAttribute('content') === 'true';
            
            if (isAdmin) {
                const hasSeenShortcutHint = localStorage.getItem('admin_shortcut_hint_seen');
                
                if (!hasSeenShortcutHint) {
                    setTimeout(() => {
                        showTooltip('Press Ctrl+Shift+A for quick admin access', 'info', 5000);
                        localStorage.setItem('admin_shortcut_hint_seen', 'true');
                    }, 2000);
                }
            }
        }

        function initNotificationSystem(userId) {
            // Check for pending notifications
            checkNotifications(userId);
            
            // Set up periodic notification checking
            setInterval(() => {
                checkNotifications(userId);
            }, 30000); // Check every 30 seconds
        }

        function checkNotifications(userId) {
            // This would typically be an API call
            const notifications = JSON.parse(localStorage.getItem(`notifications_${userId}`) || '[]');
            const unreadCount = notifications.filter(n => !n.read).length;
            
            if (unreadCount > 0) {
                updateNotificationBadge(unreadCount);
            }
        }

        function updateNotificationBadge(count) {
            const userButton = document.querySelector('.group button');
            
            if (!userButton) return;
            
            // Remove existing badge
            const existingBadge = userButton.querySelector('.notification-badge');
            if (existingBadge) {
                existingBadge.remove();
            }
            
            // Add new badge if count > 0
            if (count > 0) {
                const badge = document.createElement('span');
                badge.className = 'absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center border-2 border-white';
                badge.textContent = count > 9 ? '9+' : count.toString();
                userButton.style.position = 'relative';
                userButton.appendChild(badge);
            }
        }

        function initMobileMenu() {
            const mobileMenuButton = document.querySelector('[data-hs-collapse]');
            const mobileMenu = document.querySelector('#navbar-collapse');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    const isHidden = mobileMenu.classList.contains('hidden');
                    
                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        mobileMenuButton.querySelector('svg:first-of-type').classList.add('hidden');
                        mobileMenuButton.querySelector('svg:last-of-type').classList.remove('hidden');
                    } else {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.querySelector('svg:first-of-type').classList.remove('hidden');
                        mobileMenuButton.querySelector('svg:last-of-type').classList.add('hidden');
                    }
                });
            }
        }

        function initThemeToggle() {
            const darkModeButtons = document.querySelectorAll('[data-hs-theme-click-value]');
            
            darkModeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const theme = button.getAttribute('data-hs-theme-click-value');
                    const html = document.querySelector('html');
                    
                    if (theme === 'dark') {
                        html.classList.add('dark');
                        localStorage.setItem('hs_theme', 'dark');
                    } else {
                        html.classList.remove('dark');
                        localStorage.setItem('hs_theme', 'light');
                    }
                    
                    // Update button visibility
                    updateThemeButtons();
                });
            });

            // Initialize theme buttons visibility
            updateThemeButtons();
        }

        function updateThemeButtons() {
            const isDark = document.querySelector('html').classList.contains('dark');
            
            document.querySelectorAll('.hs-dark-mode-active\\:hidden').forEach(el => {
                if (isDark) {
                    el.classList.add('hidden');
                } else {
                    el.classList.remove('hidden');
                }
            });
            
            document.querySelectorAll('.hs-dark-mode-active\\:block').forEach(el => {
                if (isDark) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }

        function initDropdownMenus() {
            // Mobile dropdown functionality
            const dropdownButtons = document.querySelectorAll('[data-hs-dropdown-toggle]');
            
            dropdownButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const dropdown = button.nextElementSibling;
                    const isHidden = dropdown.classList.contains('hidden');
                    
                    if (isHidden) {
                        dropdown.classList.remove('hidden', 'opacity-0');
                        dropdown.classList.add('opacity-100');
                        button.classList.add('hs-dropdown-open');
                    } else {
                        dropdown.classList.add('hidden', 'opacity-0');
                        dropdown.classList.remove('opacity-100');
                        button.classList.remove('hs-dropdown-open');
                    }
                });
            });
        }

        function showTooltip(message, type = 'info', duration = 3000) {
            const tooltip = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 
                           type === 'success' ? 'bg-green-500' : 'bg-blue-500';
            
            tooltip.className = `fixed top-20 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 max-w-sm transform transition-all duration-300 translate-x-full`;
            tooltip.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'info' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' : ''}
                    </svg>
                    <span class="text-sm">${message}</span>
                </div>
            `;
            
            document.body.appendChild(tooltip);
            
            // Animate in
            setTimeout(() => {
                tooltip.classList.remove('translate-x-full');
                tooltip.classList.add('translate-x-0');
            }, 100);
            
            // Animate out and remove
            setTimeout(() => {
                tooltip.classList.remove('translate-x-0');
                tooltip.classList.add('translate-x-full');
                
                setTimeout(() => {
                    if (tooltip.parentNode) {
                        tooltip.parentNode.removeChild(tooltip);
                    }
                }, 300);
            }, duration);
        }

        // Session management for authenticated users
        function initSessionManagement() {
            const isAdmin = document.querySelector('meta[name="is-admin"]')?.getAttribute('content') === 'true';
            const sessionTimeout = isAdmin ? 60 * 60 * 1000 : 30 * 60 * 1000; // 60 min for admin, 30 min for others
            
            let sessionTimer;
            
            function resetSessionTimer() {
                clearTimeout(sessionTimer);
                sessionTimer = setTimeout(() => {
                    showTooltip('Your session will expire in 5 minutes. Please save your work.', 'warning', 10000);
                    
                    setTimeout(() => {
                        if (confirm('Your session has expired. Click OK to login again or Cancel to extend your session.')) {
                            window.location.href = '/login';
                        } else {
                            resetSessionTimer();
                        }
                    }, 5 * 60 * 1000);
                }, sessionTimeout - 5 * 60 * 1000);
            }
            
            // Reset timer on user activity
            ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
                document.addEventListener(event, resetSessionTimer, true);
            });
            
            resetSessionTimer();
        }

        // Initialize session management for authenticated users
        const userId = document.querySelector('meta[name="auth-user-id"]')?.getAttribute('content');
        if (userId) {
            initSessionManagement();
        }

        // Export functions for external use
        window.headerAdminNav = {
            showTooltip,
            updateNotificationBadge,
            checkNotifications
        };
    </script>
    @endpush
@endonce{{-- resources/views/components/public/header.blade.php - FIXED --}}
@props([
    'variant' => 'default', // default, transparent, dark, gradient
    'sticky' => true,
    'announcementBanner' => null,
    'companyProfile' => null
])

@php
    $headerClasses = match($variant) {
        'transparent' => 'absolute top-0 inset-x-0 z-50 bg-transparent backdrop-blur-sm',
        'gradient' => 'bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500',
        'dark' => 'bg-gradient-to-r from-orange-900 via-amber-900 to-orange-800',
        default => 'bg-white/95 backdrop-blur-sm border-b border-orange-100/50 shadow-sm'
    };
    
    if ($sticky) {
        $headerClasses .= ' sticky top-0';
    }
    
    // Get company profile if not provided - with null safety
    if (!$companyProfile) {
        try {
            $companyProfile = \App\Models\CompanyProfile::getInstance();
        } catch (\Exception $e) {
            $companyProfile = null;
        }
    }
@endphp

{{-- Announcement Banner --}}
@if($announcementBanner)
<div class="py-2 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-500 text-center relative overflow-hidden">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="max-w-7xl px-4 sm:px-6 lg:px-8 mx-auto relative z-10">
        <p class="text-sm text-white font-medium">
            {{ $announcementBanner }}
        </p>
    </div>
</div>
@endif

{{-- Main Header --}}
<header class="flex flex-wrap lg:justify-start lg:flex-nowrap z-50 w-full py-4 lg:py-6 {{ $headerClasses }}">
    <nav class="relative max-w-7xl w-full flex flex-wrap lg:grid lg:grid-cols-12 basis-full items-center px-4 md:px-6 lg:px-8 mx-auto">
        
        {{-- Logo Section --}}
        <div class="lg:col-span-3 flex items-center">
            {{-- Logo --}}
            <a class="flex-none rounded-xl text-xl inline-block font-bold focus:outline-none focus:opacity-80 group" 
               href="{{ route('home') }}" 
               aria-label="{{ $companyProfile?->company_name ?? config('app.name') }}">
                
                @if($companyProfile?->logo_url)
                    <img src="{{ $companyProfile->logo_url }}" 
                         alt="{{ $companyProfile->company_name ?? config('app.name') }}" 
                         class="h-8 w-auto group-hover:scale-105 transition-transform duration-200"
                         loading="lazy">
                @else
                    {{-- Fallback SVG Logo --}}
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12L7 9l1.5-1.5L10 9l8-8 1.5 1.5L10 12z"/>
                            </svg>
                        </div>
                        <span class="font-bold text-xl text-gray-900 dark:text-white">
                            {{ $companyProfile?->company_name ?? config('app.name') }}
                        </span>
                    </div>
                @endif
            </a>

            {{-- Mobile Menu Toggle --}}
            <button type="button" 
                    class="lg:hidden ml-auto p-2 inline-flex items-center justify-center rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-500 transition-colors duration-200"
                    data-hs-collapse="#navbar-collapse"
                    aria-controls="navbar-collapse"
                    aria-label="Toggle navigation">
                <span class="sr-only">Open main menu</span>
                {{-- Hamburger Icon --}}
                <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                {{-- Close Icon (hidden by default) --}}
                <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Desktop Navigation --}}
        <div class="lg:col-span-6 hidden lg:flex lg:justify-center">
            <div class="flex space-x-8">
                <a href="{{ route('home') }}" 
                   class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
                
                <div class="relative group">
                    <button class="nav-link flex items-center gap-1">
                        Services
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    {{-- Services Dropdown --}}
                    <div class="absolute left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Service 1</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Service 2</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Service 3</a>
                        </div>
                    </div>
                </div>

                <a href="#" class="nav-link">About</a>
                <a href="#" class="nav-link">Portfolio</a>
                <a href="#" class="nav-link">Contact</a>
            </div>
        </div>

        {{-- CTA Section --}}
        <div class="lg:col-span-3 hidden lg:flex lg:justify-end lg:items-center gap-x-2">
            {{-- Theme Toggle --}}
            <button type="button" 
                    class="hs-dark-mode-active:hidden block p-2 text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 rounded-lg"
                    data-hs-theme-click-value="dark">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </button>
            <button type="button" 
                    class="hs-dark-mode-active:block hidden p-2 text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 rounded-lg"
                    data-hs-theme-click-value="light">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </button>

            {{-- CTA Buttons --}}
            @auth
                @php
                    $user = auth()->user();
                    $isAdmin = $user->hasRole(['admin', 'super-admin']) ?? false;
                    $isClient = $user->hasRole('client') ?? false;
                @endphp

                {{-- User Dropdown --}}
                <div class="relative group">
                    <button class="flex items-center gap-2 p-2 text-gray-600 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-500 rounded-lg">
                        {{-- User Avatar --}}
                        @if($user->avatar ?? false)
                            <img src="{{ $user->avatar }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        
                        {{-- User Name & Role --}}
                        <div class="text-left hidden md:block">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $user->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @if($isAdmin)
                                    Administrator
                                @elseif($isClient)
                                    Client
                                @else
                                    User
                                @endif
                            </div>
                        </div>
                        
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    {{-- User Dropdown Menu --}}
                    <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-200 dark:border-gray-700">
                        <div class="py-2">
                            {{-- Profile Info --}}
                            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar_url ?? false)
                                        <img src="{{ $user->avatar_url }}" 
                                             alt="{{ $user->name }}" 
                                             class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Navigation Links --}}
                            <div class="py-1">
                                @if($isAdmin)
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Admin Dashboard
                                    </a>
                                @endif
                                
                                @if($isClient)
                                    <a href="{{ route('client.dashboard') }}" 
                                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Client Dashboard
                                    </a>
                                @endif
                                
                                {{-- Default Dashboard --}}
                                <a href="{{ route('dashboard') }}" 
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"/>
                                    </svg>
                                    My Dashboard
                                </a>
                                
                                <a href="{{ route('profile.edit') }}" 
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Profile Settings
                                </a>
                            </div>
                            
                            {{-- Logout --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" 
                   class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    Login
                </a>
                <a href="{{ route('register') }}" 
                   class="btn btn-primary">
                    Get Started
                </a>
            @endauth
        </div>

        {{-- Mobile Navigation --}}
        <div id="navbar-collapse" 
             class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow lg:hidden">
            <div class="flex flex-col gap-5 mt-5 lg:hidden">
                <a href="{{ route('home') }}" 
                   class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Home
                </a>
                
                {{-- Mobile Services Dropdown --}}
                <div class="hs-dropdown">
                    <button class="mobile-nav-link w-full flex items-center justify-between"
                            id="services-dropdown"
                            data-hs-dropdown-toggle>
                        Services
                        <svg class="w-4 h-4 transition-transform hs-dropdown-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="hs-dropdown-menu transition-opacity duration-300 hs-dropdown-open:opacity-100 opacity-0 hidden pl-4 mt-2 space-y-2"
                         aria-labelledby="services-dropdown">
                        <a href="#" class="block py-2 text-sm text-gray-600 hover:text-gray-800">Service 1</a>
                        <a href="#" class="block py-2 text-sm text-gray-600 hover:text-gray-800">Service 2</a>
                        <a href="#" class="block py-2 text-sm text-gray-600 hover:text-gray-800">Service 3</a>
                    </div>
                </div>

                <a href="#" class="mobile-nav-link">About</a>
                <a href="#" class="mobile-nav-link">Portfolio</a>
                <a href="#" class="mobile-nav-link">Contact</a>

                {{-- Mobile Auth Buttons --}}
                <div class="flex flex-col gap-3 pt-4 border-t border-gray-200">
                    @auth
                        @php
                            $user = auth()->user();
                            $isAdmin = $user->hasRole(['admin', 'super-admin']) ?? false;
                            $isClient = $user->hasRole('client') ?? false;
                        @endphp

                        {{-- User Profile Section --}}
                        <div class="flex items-center gap-3 px-1 py-2">
                            @if($user->avatar_url ?? false)
                                <img src="{{ $user->avatar_url }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($isAdmin)
                                        Administrator
                                    @elseif($isClient)
                                        Client
                                    @else
                                        User
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Dashboard Links --}}
                        @if($isAdmin)
                            <a href="{{ route('admin.dashboard') }}" 
                               class="btn btn-outline-primary w-full flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Admin Dashboard
                            </a>
                        @endif
                        
                        @if($isClient)
                            <a href="{{ route('client.dashboard') }}" 
                               class="btn btn-outline-primary w-full flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Client Dashboard
                            </a>
                        @endif
                        
                        <a href="{{ route('dashboard') }}" 
                           class="btn btn-primary w-full flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"/>
                            </svg>
                            My Dashboard
                        </a>

                        {{-- Secondary Actions --}}
                        <div class="flex gap-2">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex-1 btn btn-outline-primary text-sm py-2 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full btn text-sm py-2 bg-red-600 hover:bg-red-700 text-white border-red-600 flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" 
                           class="btn btn-outline-primary w-full">
                            Login
                        </a>
                        <a href="{{ route('register') }}" 
                           class="btn btn-primary w-full">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

{{-- Include Preline JS for dropdown functionality --}}
@once
    @push('scripts')
    <script>
        // Initialize mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle functionality
            const mobileMenuButton = document.querySelector('[data-hs-collapse]');
            const mobileMenu = document.querySelector('#navbar-collapse');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    const isHidden = mobileMenu.classList.contains('hidden');
                    
                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        // Toggle icons
                        mobileMenuButton.querySelector('svg:first-of-type').classList.add('hidden');
                        mobileMenuButton.querySelector('svg:last-of-type').classList.remove('hidden');
                    } else {
                        mobileMenu.classList.add('hidden');
                        // Toggle icons
                        mobileMenuButton.querySelector('svg:first-of-type').classList.remove('hidden');
                        mobileMenuButton.querySelector('svg:last-of-type').classList.add('hidden');
                    }
                });
            }

            // Mobile dropdown functionality
            const dropdownButtons = document.querySelectorAll('[data-hs-dropdown-toggle]');
            dropdownButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const dropdown = button.nextElementSibling;
                    const isHidden = dropdown.classList.contains('hidden');
                    
                    if (isHidden) {
                        dropdown.classList.remove('hidden', 'opacity-0');
                        dropdown.classList.add('opacity-100');
                        button.classList.add('hs-dropdown-open');
                    } else {
                        dropdown.classList.add('hidden', 'opacity-0');
                        dropdown.classList.remove('opacity-100');
                        button.classList.remove('hs-dropdown-open');
                    }
                });
            });

            // Theme toggle functionality
            const darkModeButtons = document.querySelectorAll('[data-hs-theme-click-value]');
            darkModeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const theme = button.getAttribute('data-hs-theme-click-value');
                    const html = document.querySelector('html');
                    
                    if (theme === 'dark') {
                        html.classList.add('dark');
                        localStorage.setItem('hs_theme', 'dark');
                    } else {
                        html.classList.remove('dark');
                        localStorage.setItem('hs_theme', 'light');
                    }
                    
                    // Update button visibility
                    document.querySelectorAll('.hs-dark-mode-active\\:hidden').forEach(el => {
                        if (theme === 'dark') {
                            el.classList.add('hidden');
                        } else {
                            el.classList.remove('hidden');
                        }
                    });
                    
                    document.querySelectorAll('.hs-dark-mode-active\\:block').forEach(el => {
                        if (theme === 'dark') {
                            el.classList.remove('hidden');
                        } else {
                            el.classList.add('hidden');
                        }
                    });
                });
            });
        });
    </script>
    @endpush
@endonce