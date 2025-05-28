{{-- resources/views/components/client/client-sidebar.blade.php --}}
@props([
    'unreadMessagesCount' => 0, 
    'pendingApprovalsCount' => 0,
])

<div id="hs-application-sidebar"
    class="hs-overlay w-64 top-0 start-0 z-[60] flex flex-col fixed
    -translate-x-full transition-all duration-300 transform
    h-full bg-white border-e border-gray-200
    lg:block lg:translate-x-0 lg:right-auto lg:bottom-0
    dark:bg-gray-800 dark:border-gray-700"
    data-hs-overlay-keyboard="false" data-hs-overlay-backdrop="false">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="px-6 py-4 flex items-center">
            <a class="flex-none text-xl font-semibold dark:text-white" href="{{ route('client.dashboard') }}"
                aria-label="{{ config('app.name') }}">
                @if (isset($companyProfile) && $companyProfile->logo && $companyProfile->logoUrl)
                    <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" class="h-8 md:h-10">
                @elseif (asset('storage/logo.png'))
                    <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}" class="h-8 md:h-10">
                @else
                    <span class="text-blue-600 dark:text-blue-400">{{ config('app.name') }}</span>
                @endif
            </a>
        </div>
        <!-- End Logo -->

        <!-- Welcome Section -->
        <div class="px-6 py-3 bg-blue-50 dark:bg-blue-900/20 mx-4 rounded-lg mb-4">
            <div class="flex items-center space-x-3">
                @if(auth()->user()->avatar)
                    <img class="size-10 rounded-full object-cover"
                        src="{{ Storage::url(auth()->user()->avatar) }}"
                        alt="{{ auth()->user()->name }}">
                @else
                    <div class="size-10 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        @if(auth()->user()->company)
                            {{ auth()->user()->company }}
                        @else
                            Client Portal
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto p-4">
            <ul class="space-y-1.5 hs-accordion-group" data-hs-accordion-always-open>
                <!-- Dashboard -->
                <li>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.dashboard') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                        href="{{ route('client.dashboard') }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- Projects -->
                <li>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.projects.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                        href="{{ route('client.projects.index') }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        My Projects
                        @php
                            $activeProjectsCount = isset($clientStats['projects']['active']) ? $clientStats['projects']['active'] : 0;
                        @endphp
                        @if($activeProjectsCount > 0)
                            <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 ml-auto">
                                {{ $activeProjectsCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <!-- Quotations -->
                <li class="hs-accordion" id="quotations-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs('client.quotations.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        Quotations
                        @if($pendingApprovalsCount > 0)
                            <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 ml-auto mr-2">
                                {{ $pendingApprovalsCount }}
                            </span>
                        @endif
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="quotations-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('client.quotations.*') ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.quotations.index') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('client.quotations.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    All Quotations
                                    @php
                                        $totalQuotationsCount = isset($clientStats['quotations']['total']) ? $clientStats['quotations']['total'] : 0;
                                    @endphp
                                    @if($totalQuotationsCount > 0)
                                        <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 ml-auto">
                                            {{ $totalQuotationsCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.quotations.create') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('client.quotations.create') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Request Quote
                                </a>
                            </li>

                            @if($pendingApprovalsCount > 0)
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.quotations.index') && request('status') === 'approved' ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                        href="{{ route('client.quotations.index', ['status' => 'approved']) }}">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Pending Approval
                                        <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 ml-auto animate-pulse">
                                            {{ $pendingApprovalsCount }}
                                        </span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <!-- Messages -->
                <li class="hs-accordion" id="messages-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs('client.messages.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Messages
                        @if($unreadMessagesCount > 0)
                            <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 ml-auto mr-2">
                                {{ $unreadMessagesCount }}
                            </span>
                        @endif
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="messages-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('client.messages.*') ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.messages.index') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('client.messages.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    All Messages
                                    @if($unreadMessagesCount > 0)
                                        <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 ml-auto">
                                            {{ $unreadMessagesCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.messages.create') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('client.messages.create') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Send Message
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Testimonials -->
                <li>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('client.testimonials.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                        href="{{ route('client.testimonials.index') }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        My Reviews
                    </a>
                </li>

                <!-- Divider -->
                <li class="my-3">
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </li>

                <!-- Support & Help -->
                <li class="hs-accordion" id="support-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                        Support & Help
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="support-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 rounded-md"
                                    href="{{ route('contact.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Contact Us
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 rounded-md"
                                    href="{{ route('services.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Our Services
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 rounded-md"
                                    href="{{ route('portfolio.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Portfolio
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <!-- End Navigation -->

        <!-- Footer -->
        <div class="mt-auto p-4 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
            &copy; {{ date('Y') }} {{ config('app.name') }}
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline mt-1 block dark:text-blue-500">
                Visit Website
            </a>
        </div>
        <!-- End Footer -->
    </div>
</div>