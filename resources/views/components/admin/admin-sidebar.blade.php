{{-- resources/views/components/admin/admin-sidebar.blade.php --}}
{{-- Mobile-Responsive Sidebar with NavigationService --}}

@php
    // Navigation data is injected by NavigationServiceProvider
    $navigation = $adminNavigation ?? [];
    
    $iconMap = [
        'dashboard' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
        'building' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path></svg>',
        'blog' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'banners' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="m7 11 2-7 2 7 2-7 2 7"></path></svg>',
        'services' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"></path></svg>',
        'projects' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'team' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'testimonials' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path></svg>',
        'messages' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
        'chat' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z"></path><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"></path></svg>',
        'quotations' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
        'users' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'settings' => '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
    ];
@endphp

<!-- Sidebar -->
<div id="hs-application-sidebar"
    class="hs-overlay hs-overlay-open:translate-x-0
    w-64 h-full
    fixed inset-y-0 start-0 z-[60]
    bg-white border-e border-gray-200
    -translate-x-full transition-all duration-300 transform
    lg:block lg:translate-x-0 lg:end-auto lg:bottom-0
    dark:bg-gray-800 dark:border-gray-700"
    role="dialog" tabindex="-1" aria-label="Sidebar">
    
    <!-- Sidebar Content -->
    <div class="relative flex flex-col h-full max-h-full">
        <!-- Sidebar Header -->
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a class="flex-none text-xl font-semibold dark:text-white focus:outline-none focus:opacity-80" 
                   href="{{ route('admin.dashboard') }}" aria-label="{{ config('app.name') }}">
                    @if (isset($companyProfile) && $companyProfile->logo && $companyProfile->logoUrl)
                        <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" 
                             class="h-8 md:h-10 w-auto object-contain">
                    @elseif (file_exists(public_path('storage/logo.png')))
                        <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}" 
                             class="h-8 md:h-10 w-auto object-contain">
                    @else
                        <span class="text-blue-600 dark:text-blue-400 font-bold">
                            {{ Str::limit(config('app.name'), 15) }}
                        </span>
                    @endif
                </a>
                
                <!-- Mobile Close Button -->
                <button type="button" 
                        class="lg:hidden size-8 inline-flex justify-center items-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:bg-gray-800"
                        data-hs-overlay="#hs-application-sidebar" aria-controls="hs-application-sidebar" aria-label="Close sidebar">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <!-- End Sidebar Header -->

        <!-- Navigation -->
        <div class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500">
            <nav class="hs-accordion-group p-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="space-y-1.5">
                    @if (count($navigation) > 0)
                        @foreach ($navigation as $item)
                            @if (isset($item['children']) && count($item['children']) > 0)
                                {{-- Accordion Item with Children --}}
                                <li class="hs-accordion" id="{{ Str::slug($item['title']) }}-accordion">
                                    <button type="button"
                                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700 {{ $item['active'] ? 'bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}"
                                        aria-expanded="{{ $item['active'] ? 'true' : 'false' }}"
                                        aria-controls="{{ Str::slug($item['title']) }}-accordion-child">
                                        
                                        {!! $iconMap[$item['icon']] ?? '' !!}
                                        
                                        <span class="flex-1">{{ $item['title'] }}</span>
                                        
                                        @if (isset($item['badge']) && $item['badge'] > 0)
                                            <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-500 text-white">
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
                                        role="region" aria-labelledby="{{ Str::slug($item['title']) }}-accordion">
                                        <ul class="ps-7 pt-1 space-y-1">
                                            @foreach ($item['children'] as $child)
                                                <li>
                                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700 {{ $child['active'] ? 'bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}"
                                                        href="{{ isset($child['route']) ? route($child['route']) : '#' }}">
                                                        
                                                        @if (isset($child['icon']))
                                                            {!! $iconMap[$child['icon']] ?? '' !!}
                                                        @endif
                                                        
                                                        <span class="flex-1">{{ $child['title'] }}</span>
                                                        
                                                        @if (isset($child['badge']) && $child['badge'] > 0)
                                                            <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-500 text-white">
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
                                    <a class="w-full flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700 {{ $item['active'] ? 'bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' }}"
                                        href="{{ isset($item['route']) ? route($item['route']) : '#' }}">
                                        
                                        {!! $iconMap[$item['icon']] ?? '' !!}
                                        
                                        <span class="flex-1">{{ $item['title'] }}</span>
                                        
                                        @if (isset($item['badge']) && $item['badge'] > 0)
                                            <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-500 text-white">
                                                {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        {{-- Fallback loading state --}}
                        <li class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-900 dark:border-white"></div>
                                <span>Loading navigation...</span>
                            </div>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        <!-- End Navigation -->

        <!-- Sidebar Footer -->
        <div class="mt-auto">
            <!-- Quick Stats (Mobile Only) -->
            <div class="lg:hidden px-3 py-2 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                    <a href="{{ route('home') }}" class="text-blue-600 hover:underline dark:text-blue-400">
                        View Website
                    </a>
                </div>
            </div>
            
            <!-- Desktop Footer -->
            <div class="hidden lg:block p-4 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col space-y-1">
                    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                    <a href="{{ route('home') }}" class="text-blue-600 hover:underline dark:text-blue-400">
                        View Website
                    </a>
                </div>
            </div>
        </div>
        <!-- End Sidebar Footer -->
    </div>
</div>

<!-- Backdrop for mobile -->
<div class="hs-overlay-backdrop transition duration fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 lg:hidden" 
     data-hs-overlay-backdrop-template></div>

<!-- Mobile-specific styles -->
<style>
    /* Enhanced mobile scroll behavior */
    @media (max-width: 1023px) {
        #hs-application-sidebar {
            /* Improved touch scrolling on mobile */
            -webkit-overflow-scrolling: touch;
        }
        
        /* Mobile navigation focus improvements */
        #hs-application-sidebar a:focus,
        #hs-application-sidebar button:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Better touch targets on mobile */
        #hs-application-sidebar .hs-accordion-toggle,
        #hs-application-sidebar a {
            min-height: 44px; /* iOS recommended touch target size */
        }
    }
    
    /* Ensure smooth animations */
    #hs-application-sidebar {
        transition: transform 0.3s ease-in-out;
    }
    
    /* Custom scrollbar for better mobile experience */
    #hs-application-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #hs-application-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #hs-application-sidebar::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.5);
        border-radius: 3px;
    }
    
    #hs-application-sidebar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(155, 155, 155, 0.7);
    }
</style>