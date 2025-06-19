{{-- resources/views/components/admin/mobile-breadcrumb.blade.php --}}
{{-- Mobile Sticky Breadcrumb with Navigation Toggle --}}

@php
    // Get breadcrumbs from NavigationService
    $navigationService = app(\App\Services\NavigationService::class);
    $breadcrumbs = $navigationService->getBreadcrumbs();
    
    // Get current page title from breadcrumbs or route
    $currentPageTitle = collect($breadcrumbs)->last()['title'] ?? 'Dashboard';
    
    // Build breadcrumb trail (all items except the last one)
    $breadcrumbTrail = collect($breadcrumbs)->slice(0, -1);
@endphp

<!-- Mobile Breadcrumb Bar - Only visible on mobile/tablet -->
<div class="mobile-breadcrumb sticky top-0 inset-x-0 z-[10] bg-white border-y border-gray-200 px-4 sm:px-6 lg:px-8 lg:hidden dark:bg-neutral-800 dark:border-neutral-700">
    <div class="flex items-center py-2">
        <!-- Navigation Toggle -->
        <button type="button" 
            class="size-8 flex justify-center items-center gap-x-2 border border-gray-200 text-gray-800 hover:text-gray-500 rounded-lg focus:outline-hidden focus:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:border-neutral-700 dark:text-neutral-200 dark:hover:text-neutral-500 dark:focus:text-neutral-500" 
            aria-haspopup="dialog" 
            aria-expanded="false" 
            aria-controls="hs-application-sidebar" 
            aria-label="Toggle navigation" 
            data-hs-overlay="#hs-application-sidebar">
            <span class="sr-only">Toggle Navigation</span>
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                <path d="M15 3v18"></path>
                <path d="m8 9 3 3-3 3"></path>
            </svg>
        </button>
        <!-- End Navigation Toggle -->

        <!-- Breadcrumb -->
        <nav class="ms-3 flex items-center whitespace-nowrap overflow-hidden" aria-label="Breadcrumb">
            <ol class="flex items-center">
                @if($breadcrumbTrail->count() > 0)
                    @foreach($breadcrumbTrail as $index => $breadcrumb)
                        <li class="flex items-center">
                            @if(isset($breadcrumb['route']))
                                <a href="{{ route($breadcrumb['route']) }}" 
                                   class="flex items-center text-sm text-gray-800 hover:text-blue-600 dark:text-neutral-400 dark:hover:text-blue-400 transition-colors">
                                    {{ $breadcrumb['title'] }}
                                </a>
                            @else
                                <span class="text-sm text-gray-800 dark:text-neutral-400">
                                    {{ $breadcrumb['title'] }}
                                </span>
                            @endif
                            
                            <!-- Breadcrumb Separator -->
                            <svg class="shrink-0 mx-2 overflow-visible size-2.5 text-gray-400 dark:text-neutral-500" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                            </svg>
                        </li>
                    @endforeach
                @endif
                
                <!-- Current Page -->
                <li class="text-sm font-semibold text-gray-800 truncate dark:text-neutral-400" aria-current="page">
                    {{ $currentPageTitle }}
                </li>
            </ol>
        </nav>
        <!-- End Breadcrumb -->
    </div>
</div>

<!-- Enhanced Styles for Mobile Breadcrumb -->
<style>
    /* Ensure proper mobile breadcrumb behavior */
    @media (max-width: 1023px) {
        /* Limit breadcrumb text to prevent overflow */
        .mobile-breadcrumb nav {
            max-width: calc(100vw - 120px); /* Account for toggle button and actions */
        }
        
        /* Truncate long breadcrumb items */
        .mobile-breadcrumb ol li {
            max-width: 150px;
        }
        
        .mobile-breadcrumb ol li span,
        .mobile-breadcrumb ol li a {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }
        
        /* Improve touch targets */
        .mobile-breadcrumb button {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Ensure dropdown is properly positioned */
        .hs-dropdown-menu {
            right: 0;
            left: auto;
        }
    }
    
    /* Smooth transitions */
    .mobile-breadcrumb button {
        transition: all 0.2s ease-in-out;
    }
    
    /* Focus states for accessibility */
    .mobile-breadcrumb button:focus,
    .mobile-breadcrumb a:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
    
    /* Dropdown animation */
    .hs-dropdown-menu {
        transform-origin: top right;
        transition: all 0.15s ease-out;
    }
    
    .hs-dropdown-open .hs-dropdown-menu {
        transform: scale(1);
    }
    
    .hs-dropdown-menu:not(.hs-dropdown-open) {
        transform: scale(0.95);
    }
</style>

{{-- JavaScript for enhanced mobile functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle mobile dropdown
    const dropdownToggle = document.getElementById('hs-dropdown-mobile-user');
    const dropdownMenu = dropdownToggle?.nextElementSibling;
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = dropdownMenu.classList.contains('hs-dropdown-open');
            
            // Close all other dropdowns first
            document.querySelectorAll('.hs-dropdown-menu.hs-dropdown-open').forEach(menu => {
                menu.classList.remove('hs-dropdown-open', 'opacity-100');
                menu.classList.add('opacity-0', 'hidden');
            });
            
            if (!isOpen) {
                // Open this dropdown
                dropdownMenu.classList.remove('hidden', 'opacity-0');
                dropdownMenu.classList.add('hs-dropdown-open', 'opacity-100');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('hs-dropdown-open', 'opacity-100');
                dropdownMenu.classList.add('opacity-0', 'hidden');
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('hs-dropdown-open', 'opacity-100');
                dropdownMenu.classList.add('opacity-0', 'hidden');
            }
        });
    }
    
    // Auto-hide mobile breadcrumb on scroll (optional)
    let lastScrollY = window.scrollY;
    const breadcrumbBar = document.querySelector('.mobile-breadcrumb');
    
    if (breadcrumbBar) {
        window.addEventListener('scroll', function() {
            const currentScrollY = window.scrollY;
            
            // Only apply on mobile
            if (window.innerWidth < 1024) {
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    // Scrolling down - hide breadcrumb
                    breadcrumbBar.style.transform = 'translateY(-100%)';
                } else {
                    // Scrolling up - show breadcrumb
                    breadcrumbBar.style.transform = 'translateY(0)';
                }
            }
            
            lastScrollY = currentScrollY;
        });
    }
    
    // Handle breadcrumb link clicks
    const breadcrumbLinks = document.querySelectorAll('.mobile-breadcrumb a');
    breadcrumbLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Add loading state
            this.style.opacity = '0.6';
            this.style.pointerEvents = 'none';
        });
    });
});
</script>