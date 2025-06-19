{{-- resources/views/components/admin/admin-sidebar.blade.php --}}
{{-- Mobile-Responsive Sidebar with NavigationService - Preline Style --}}

@php
    // Get navigation data from NavigationService
    $navigationService = app(\App\Services\NavigationService::class);
    $navigation = $navigationService->getFilteredAdminNavigation();
    
    // Debug: Log navigation data
    \Log::info('Admin Sidebar Navigation Data:', ['count' => count($navigation), 'items' => collect($navigation)->pluck('title')]);
    
    $iconMap = [
        'dashboard' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
        'building' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path></svg>',
        'blog' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
        'banners' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="m7 11 2-7 2 7 2-7 2 7"></path></svg>',
        'services' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"></path></svg>',
        'projects' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>',
        'team' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'testimonials' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path></svg>',
        'messages' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
        'chat' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z"></path><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"></path></svg>',
        'quotations' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
        'users' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'settings' => '<svg class="shrink-0 mt-0.5 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="15" r="3"></circle><circle cx="9" cy="7" r="4"></circle><path d="M10 15H6a4 4 0 0 0-4 4v2"></path><path d="m21.7 16.4-.9-.3"></path><path d="m15.2 13.9-.9-.3"></path><path d="m16.6 18.7.3-.9"></path><path d="m19.1 12.2.3-.9"></path><path d="m19.6 18.7-.4-1"></path><path d="m16.8 12.3-.4-1"></path><path d="m14.3 16.6 1-.4"></path><path d="m20.7 13.8 1-.4"></path></svg>',
    ];
@endphp

<!-- Sidebar -->
<div id="hs-application-sidebar" class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-transform duration-300 w-64 h-full fixed inset-y-0 start-0 z-[70] bg-white border-e border-gray-200 lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 dark:bg-neutral-800 dark:border-neutral-700" role="dialog" aria-label="Sidebar">
    <div class="relative flex flex-col h-full max-h-full">
        
        <!-- Header -->
        <div class="px-6 pt-4 flex items-center">
            <!-- Logo -->
            <a class="flex-none rounded-xl text-xl inline-block font-semibold focus:outline-hidden focus:opacity-80" href="{{ route('admin.dashboard') }}" aria-label="{{ config('app.name') }}">
                
                <img src="{{ asset('storage/company/logos/1749016203_logo.png') }}" alt="{{ config('app.name') }}" class="w-28 h-auto">
            </a>
            <!-- End Logo -->
        </div>
        <!-- End Header -->

        <!-- Content -->
        <div class="h-full overflow-y-auto overflow-x-hidden [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            <nav class="hs-accordion-group p-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="flex flex-col space-y-1">
                    
                    @if (count($navigation) > 0)
                        @foreach ($navigation as $item)
                            @if (isset($item['children']) && count($item['children']) > 0)
                                {{-- Accordion Item with Children --}}
                                <li class="hs-accordion" id="{{ Str::slug($item['title']) }}-accordion">
                                    <button type="button" 
                                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:text-neutral-200 {{ $item['active'] ? 'bg-gray-100 dark:bg-neutral-700 text-gray-800 dark:text-white' : '' }}" 
                                        aria-expanded="{{ $item['active'] ? 'true' : 'false' }}" 
                                        aria-controls="{{ Str::slug($item['title']) }}-accordion-child">
                                        
                                        {!! $iconMap[$item['icon']] ?? '' !!}
                                        
                                        {{ $item['title'] }}
                                        
                                        @if (isset($item['badge']) && $item['badge'] > 0)
                                            <span class="ms-auto inline-flex items-center py-0.5 px-2 rounded-full text-xs bg-blue-50 text-blue-600 dark:bg-blue-800/30 dark:text-blue-500">
                                                {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                            </span>
                                        @endif

                                        <svg class="hs-accordion-active:block ms-auto hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m18 15-6-6-6 6"></path>
                                        </svg>

                                        <svg class="hs-accordion-active:hidden ms-auto block size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m6 9 6 6 6-6"></path>
                                        </svg>
                                    </button>

                                    <div id="{{ Str::slug($item['title']) }}-accordion-child" 
                                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ $item['active'] ? '' : 'hidden' }}" 
                                        role="region" 
                                        aria-labelledby="{{ Str::slug($item['title']) }}-accordion">
                                        <ul class="ps-8 pt-1 space-y-1">
                                            @foreach ($item['children'] as $child)
                                                <li>
                                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:text-neutral-200 {{ $child['active'] ? 'bg-gray-100 dark:bg-neutral-700 text-gray-800 dark:text-white' : '' }}" 
                                                       href="{{ isset($child['route']) ? route($child['route']) : '#' }}">
                                                        
                                                        @if (isset($child['icon']))
                                                            {!! $iconMap[$child['icon']] ?? '' !!}
                                                        @endif
                                                        
                                                        {{ $child['title'] }}
                                                        
                                                        @if (isset($child['badge']) && $child['badge'] > 0)
                                                            <span class="ms-auto inline-flex items-center py-0.5 px-2 rounded-full text-xs bg-blue-50 text-blue-600 dark:bg-blue-800/30 dark:text-blue-500">
                                                                {{ $child['badge'] > 99 ? '99+' : $child['badge'] }}
                                                            </span>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @else
                                {{-- Simple Link Item --}}
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:text-neutral-200 {{ $item['active'] ? 'bg-gray-100 dark:bg-neutral-700 text-gray-800 dark:text-white' : '' }}" 
                                       href="{{ isset($item['route']) ? route($item['route']) : '#' }}">
                                        
                                        {!! $iconMap[$item['icon']] ?? '' !!}
                                        
                                        {{ $item['title'] }}
                                        
                                        @if (isset($item['badge']) && $item['badge'] > 0)
                                            <span class="ms-auto inline-flex items-center py-0.5 px-2 rounded-full text-xs bg-blue-50 text-blue-600 dark:bg-blue-800/30 dark:text-blue-500">
                                                {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        {{-- Debug: Show navigation loading issue --}}
                        <li class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-center space-x-2">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-900 dark:border-white"></div>
                                    <span>No navigation items found</span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Navigation count: {{ count($navigation) }}
                                </div>
                                @if(config('app.debug'))
                                    <div class="text-xs text-gray-400 mt-2">
                                        Debug: Check NavigationService
                                    </div>
                                @endif
                            </div>
                        </li>
                        
                        {{-- Fallback manual navigation items for testing --}}
                        <li>
                            <a class="flex items-center gap-x-3.5 py-2 px-2.5 bg-gray-100 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-700 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700 dark:text-white" href="{{ route('admin.dashboard') }}">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                Dashboard (Fallback)
                            </a>
                        </li>
                    @endif
                    
                </ul>
            </nav>
        </div>
        <!-- End Content -->
        
    </div>
</div>

<!-- Backdrop for mobile overlay -->
<div id="sidebar-backdrop" class="hs-overlay-backdrop transition duration fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 z-[60] hidden" data-hs-overlay-backdrop="#hs-application-sidebar"></div>

<!-- Auto-initialize Preline accordion functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Preline components
    if (typeof window.HSOverlay !== 'undefined') {
        window.HSOverlay.autoInit();
    }
    if (typeof window.HSAccordion !== 'undefined') {
        window.HSAccordion.autoInit();
    }
    
    // Debug: Check if navigation items exist
    const navigationItems = document.querySelectorAll('#hs-application-sidebar ul li');
    console.log('Navigation items found:', navigationItems.length);
    
    // Manual overlay functionality as fallback
    const overlayToggle = document.querySelector('[data-hs-overlay="#hs-application-sidebar"]');
    const sidebar = document.getElementById('hs-application-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    
    if (overlayToggle && sidebar) {
        overlayToggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Toggle clicked');
            
            if (sidebar.classList.contains('hs-overlay-open')) {
                // Close sidebar
                sidebar.classList.remove('hs-overlay-open');
                sidebar.classList.add('');
                if (backdrop) {
                    backdrop.classList.add('hidden');
                }
                console.log('Closing sidebar');
            } else {
                // Open sidebar
                sidebar.classList.add('hs-overlay-open');
                sidebar.classList.remove('-translate-x-full');
                if (backdrop) {
                    backdrop.classList.remove('hidden');
                }
                console.log('Opening sidebar');
            }
        });
    }
    
    // Close sidebar when clicking backdrop
    if (backdrop) {
        backdrop.addEventListener('click', function() {
            if (sidebar.classList.contains('hs-overlay-open')) {
                sidebar.classList.remove('hs-overlay-open');
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                console.log('Backdrop clicked - closing sidebar');
            }
        });
    }
    
    // Manual accordion functionality as fallback
    const accordionToggles = document.querySelectorAll('.hs-accordion-toggle');
    console.log('Accordion toggles found:', accordionToggles.length);
    
    accordionToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const accordion = this.closest('.hs-accordion');
            const content = accordion.querySelector('.hs-accordion-content');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            console.log('Accordion clicked:', this.textContent.trim(), 'isExpanded:', isExpanded);
            
            // Toggle current accordion
            if (isExpanded) {
                content.classList.add('hidden');
                this.setAttribute('aria-expanded', 'false');
                accordion.classList.remove('hs-accordion-active');
            } else {
                content.classList.remove('hidden');
                this.setAttribute('aria-expanded', 'true');
                accordion.classList.add('hs-accordion-active');
            }
        });
    });
    
    // Auto-expand active accordions on page load
    const activeItems = document.querySelectorAll('.hs-accordion-toggle[aria-expanded="true"]');
    activeItems.forEach(toggle => {
        const accordion = toggle.closest('.hs-accordion');
        const content = accordion.querySelector('.hs-accordion-content');
        
        if (content) {
            content.classList.remove('hidden');
            accordion.classList.add('hs-accordion-active');
        }
    });
    
    // Close sidebar when clicking outside on mobile (alternative to backdrop)
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 1024) { // Only on mobile
            const sidebar = document.getElementById('hs-application-sidebar');
            const toggleButton = document.querySelector('[data-hs-overlay="#hs-application-sidebar"]');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (sidebar && sidebar.classList.contains('hs-overlay-open')) {
                if (!sidebar.contains(e.target) && !toggleButton.contains(e.target)) {
                    sidebar.classList.remove('hs-overlay-open');
                    sidebar.classList.add('-translate-x-full');
                    if (backdrop) {
                        backdrop.classList.add('hidden');
                    }
                }
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('hs-application-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        
        if (window.innerWidth >= 1024) {
            // Desktop - ensure sidebar is visible and backdrop is hidden
            sidebar.classList.remove('hs-overlay-open', '-translate-x-full');
            if (backdrop) {
                backdrop.classList.add('hidden');
            }
        } else {
            // Mobile - ensure sidebar is hidden unless toggled
            if (!sidebar.classList.contains('hs-overlay-open')) {
                sidebar.classList.add('-translate-x-full');
                if (backdrop) {
                    backdrop.classList.add('hidden');
                }
            }
        }
    });
    
    // Close sidebar when clicking navigation links on mobile
    const navLinks = document.querySelectorAll('#hs-application-sidebar a[href]');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('hs-application-sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                
                // Close sidebar after short delay to allow navigation
                setTimeout(() => {
                    sidebar.classList.remove('hs-overlay-open');
                    sidebar.classList.add('-translate-x-full');
                    if (backdrop) {
                        backdrop.classList.add('hidden');
                    }
                }, 100);
            }
        });
    });
});
</script>

<!-- Additional CSS for better mobile experience -->
<style>
    /* Ensure sidebar has proper z-index layers */
    #hs-application-sidebar {
        z-index: 70 !important;
    }
    
    #sidebar-backdrop {
        z-index: 60 !important;
    }
    
    /* Safe isolation - only prevent header from being affected */
    header:not(#hs-application-sidebar header),
    .admin-header,
    .mobile-breadcrumb,
    [data-component="header"] {
        transform: none !important;
    }
    
    /* Improve scrolling on mobile */
    @media (max-width: 1023px) {
        #hs-application-sidebar {
            -webkit-overflow-scrolling: touch;
        }
        
        /* Ensure content area has proper height */
        #hs-application-sidebar .h-full {
            height: 100vh;
            max-height: 100vh;
        }
    }
    
    /* Custom scrollbar styling */
    #hs-application-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #hs-application-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #hs-application-sidebar::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }
    
    #hs-application-sidebar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.7);
    }
    
    /* Backdrop transitions */
    #sidebar-backdrop {
        transition: opacity 0.3s ease-in-out;
    }
    
    #sidebar-backdrop.hidden {
        opacity: 0;
        pointer-events: none;
    }
    
    #sidebar-backdrop:not(.hidden) {
        opacity: 1;
        pointer-events: auto;
    }
</style>