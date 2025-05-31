@props(['unreadMessagesCount' => 0, 'pendingQuotationsCount' => 0])

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
            <a class="flex-none text-xl font-semibold dark:text-white" href="{{ route('admin.dashboard') }}"
                aria-label="{{ config('app.name') }}">
                @if (isset($companyProfile) && $companyProfile->logo && $companyProfile->logoUrl)
                    <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" class="h-8 md:h-10">
                @elseif (asset('storage/logo.png'))
                    <img src="{{ asset('storage/logo.png') }}" alt="{{ config('app.name') }}" class="h-8 md:h-10">
                @else
                    <svg class="w-20 h-auto" width="116" height="32" viewBox="0 0 116 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <!-- SVG path content -->
                    </svg>
                @endif
            </a>
        </div>
        <!-- End Logo -->

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto p-4">
            <ul class="space-y-1.5 hs-accordion-group" data-hs-accordion-always-open>
                <!-- Dashboard -->
                <li>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                        href="{{ route('admin.dashboard') }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <!-- Company Profile -->
                <li class="hs-accordion" id="company-profile-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs(['admin.company-profile.*', 'admin.cetifications.*']) ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z">
                            </path>
                        </svg>
                        Company Profile
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

                    <div id="company-profile-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs(['admin.company-profile.*', 'admin.cetifications.*']) ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.company-profile.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.company-profile.index') }}">
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.certifications.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.certifications.index') }}">
                                    Certificates
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Posts Management -->
                <li class="hs-accordion" id="posts-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs(['admin.posts.*', 'admin.post-categories.*']) ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Blog
                        @if (isset($draftPostsCount) && $draftPostsCount > 0)
                            <span
                                class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-amber-500 text-white ml-auto mr-2">
                                {{ $draftPostsCount }}
                            </span>
                        @endif
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="posts-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs(['admin.posts.*', 'admin.post-categories.*']) ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.posts.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.posts.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    All Posts
                                    @if (isset($totalPostsCount) && $totalPostsCount > 0)
                                        <span
                                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 ml-auto">
                                            {{ $totalPostsCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.posts.create') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.posts.create') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    New Post
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs(['admin.posts.index']) && request('status') === 'draft' ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.posts.index', ['status' => 'draft']) }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Drafts
                                    @if (isset($draftPostsCount) && $draftPostsCount > 0)
                                        <span
                                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 ml-auto">
                                            {{ $draftPostsCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs(['admin.posts.index']) && request('status') === 'published' ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.posts.index', ['status' => 'published']) }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Published
                                    @if (isset($publishedPostsCount) && $publishedPostsCount > 0)
                                        <span
                                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 ml-auto">
                                            {{ $publishedPostsCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            <!-- Divider -->
                            <li class="my-2">
                                <div class="border-t border-gray-200 dark:border-gray-700"></div>
                            </li>

                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.post-categories.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.post-categories.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    Categories
                                    @if (isset($categoriesCount) && $categoriesCount > 0)
                                        <span
                                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 ml-auto">
                                            {{ $categoriesCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.post-categories.create') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.post-categories.create') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    New Category
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <!-- Services -->
                <li class="hs-accordion" id="services-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs(['admin.services.*', 'admin.service-categories.*']) ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z">
                            </path>
                        </svg>
                        Services
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="services-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs(['admin.services.*', 'admin.service-categories.*']) ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.services.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.services.index') }}">
                                    Services List
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.service-categories.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.service-categories.index') }}">
                                    Service Categories
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.services.create') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.services.create') }}">
                                    Add New Service
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Projects -->
                <li class="hs-accordion" id="projects-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs(['admin.projects.*', 'admin.project-categories.*']) ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        Projects
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="projects-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs(['admin.projects.*', 'admin.project-categories.*']) ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.projects.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.projects.index') }}">
                                    Projects List
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.project-categories.*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.project-categories.index') }}">
                                    Project Categories
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.projects.create') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.projects.create') }}">
                                    Add New Project
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Team -->
                <li class="hs-accordion" id="team-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs(['admin.team.*', 'admin.team-departments.*']) ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        Team
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="team-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs(['admin.team.*', 'admin.team-departments.*']) ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.team.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.team.index') }}">
                                    Team Members
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.team-departments.*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.team-member-departments.index') }}">
                                    Departments
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.team.create') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.team.create') }}">
                                    Add New Member
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Messages -->
                <li class="hs-accordion" id="messages-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs('admin.messages.*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                            </path>
                        </svg>
                        Messages
                        @if ($unreadMessagesCount > 0)
                            <span
                                class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-500 text-white ml-auto">
                                {{ $unreadMessagesCount }}
                            </span>
                        @endif
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="messages-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('admin.messages.*') ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.messages.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.messages.index') }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    All Messages
                                    @if ($unreadMessagesCount > 0)
                                        <span
                                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 ml-auto">
                                            {{ $unreadMessagesCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.messages.create') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.messages.create') }}">
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
                <!-- Updated Live Chat section for sidebar -->
                <li class="hs-accordion" id="chat-accordion">
    <button type="button"
        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs('admin.chat.*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        Live Chat
        @php
            // Get active chat sessions count
            try {
                $activeChatSessions = \App\Models\ChatSession::where('status', 'active')->count();
                $waitingChatSessions = \App\Models\ChatSession::where('status', 'waiting')->count();
                $totalActiveSessions = $activeChatSessions + $waitingChatSessions;

                // Get current user's operator status
                $currentOperator = \App\Models\ChatOperator::where('user_id', auth()->id())->first();
                $isOperatorOnline = $currentOperator ? $currentOperator->is_online : false;
            } catch (\Exception $e) {
                $activeChatSessions = 0;
                $waitingChatSessions = 0;
                $totalActiveSessions = 0;
                $isOperatorOnline = false;
            }
        @endphp
        @if ($totalActiveSessions > 0)
            <span
                class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium {{ $waitingChatSessions > 0 ? 'bg-red-500 text-white animate-pulse' : 'bg-green-500 text-white' }} ml-auto mr-2">
                {{ $totalActiveSessions }}
            </span>
        @endif
        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4"
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="m18 15-6-6-6 6" />
        </svg>
        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>

    <div id="chat-accordion-child"
        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('admin.chat.*') ? 'block' : 'hidden' }}">
        <ul class="pt-2 ps-2">
            <!-- Main Chat Dashboard -->
            <li>
                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.chat.index') && !request()->has('filter') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                    href="{{ route('admin.chat.index') }}">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Chat Dashboard
                    @if ($totalActiveSessions > 0)
                        <span
                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium {{ $waitingChatSessions > 0 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' }} ml-auto">
                            {{ $totalActiveSessions }}
                        </span>
                    @endif
                </a>
            </li>

            <!-- Active Chats (only show if there are active sessions) -->
            @if ($activeChatSessions > 0)
                <li>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.chat.index') && request('filter') === 'active' ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                        href="{{ route('admin.chat.index', ['filter' => 'active']) }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Active Chats
                        <span
                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 ml-auto">
                            {{ $activeChatSessions }}
                        </span>
                    </a>
                </li>
            @endif

            <!-- Waiting Queue (only show if there are waiting sessions) -->
            @if ($waitingChatSessions > 0)
                <li>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.chat.index') && request('filter') === 'waiting' ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                        href="{{ route('admin.chat.index', ['filter' => 'waiting']) }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Waiting Queue
                        <span
                            class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 ml-auto animate-pulse">
                            {{ $waitingChatSessions }}
                        </span>
                    </a>
                </li>
            @endif

            <!-- Chat Settings -->
            <li>
                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.chat.settings') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                    href="{{ route('admin.chat.settings') }}">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Chat Settings                                    
                </a>
            </li>

            <!-- Chat Templates -->
            <li>
                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.chat.templates') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                    href="{{ route('admin.chat.templates') }}">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Quick Templates                                    
                </a>
            </li>

            <!-- Divider -->
            <li class="my-2">
                <div class="border-t border-gray-200 dark:border-gray-700"></div>
            </li>

            <!-- Reports & Analytics -->
            {{-- <li>
                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.chat.reports*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                    href="{{ route('admin.chat.reports') }}">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reports & Analytics                                    
                </a>
            </li> --}}

            <!-- Online Status Toggle -->
            <li>
                <button type="button"
                    class="w-full flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 transition-colors"
                    onclick="toggleOperatorStatus()" id="operator-toggle-btn">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span id="operator-status-text">
                        {{ $isOperatorOnline ? 'Go Offline' : 'Go Online' }}
                    </span>
                    <div class="ml-auto flex items-center">
                        <div id="operator-status-indicator"
                            class="w-2 h-2 rounded-full {{ $isOperatorOnline ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}">
                        </div>
                    </div>
                </button>
            </li>
        </ul>
    </div>
</li>

                <!-- Quotations -->
                <li>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.quotations.*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                        href="{{ route('admin.quotations.index') }}">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Quotations
                        @if ($pendingQuotationsCount > 0)
                            <span
                                class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-amber-500 text-white ml-auto">
                                {{ $pendingQuotationsCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <!-- User Management -->
                <li class="hs-accordion" id="users-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        User Management
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="users-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            @can('view users')
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.users.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                        href="{{ route('admin.users.index') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        All Users
                                    </a>
                                </li>
                            @endcan

                            @can('view roles')
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.roles.*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                        href="{{ route('admin.roles.index') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Roles & Permissions
                                    </a>
                                </li>
                            @endcan

                            @can('create users')
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.users.create') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                        href="{{ route('admin.users.create') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Add New User
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>

                <!-- Settings -->
                <li class="hs-accordion" id="settings-accordion">
                    <button type="button"
                        class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-3 text-sm {{ request()->routeIs('admin.settings.*') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }} rounded-md">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                            </path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        Settings
                        <svg class="hs-accordion-active:block ms-auto hidden w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m18 15-6-6-6 6" />
                        </svg>
                        <svg class="hs-accordion-active:hidden ms-auto block w-4 h-4"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div id="settings-accordion-child"
                        class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('admin.settings.*') ? 'block' : 'hidden' }}">
                        <ul class="pt-2 ps-2">
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.settings.index') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.settings.index') }}">
                                    General Settings
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.settings.email') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.settings.email') }}">
                                    Email Settings
                                </a>
                            </li>
                            <li>
                                <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm rounded-md {{ request()->routeIs('admin.settings.seo') ? 'bg-gray-100 dark:bg-gray-900 text-blue-600' : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900' }}"
                                    href="{{ route('admin.settings.seo') }}">
                                    SEO Settings
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <!-- End Navigation -->

        <!-- Footer -->
        <div
            class="mt-auto p-4 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
            &copy; {{ date('Y') }} {{ config('app.name') }}
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline mt-1 block dark:text-blue-500">View
                Website</a>
        </div>
        <!-- End Footer -->
    </div>
</div>
@push('scripts')
    <script>
        // Initialize operator status from server-side data
        let isOperatorOnline = {{ $isOperatorOnline ? 'true' : 'false' }};

        async function toggleOperatorStatus() {
            const btn = document.getElementById('operator-toggle-btn');
            const statusText = document.getElementById('operator-status-text');
            const statusIndicator = document.getElementById('operator-status-indicator');

            // Disable button during request
            btn.disabled = true;
            btn.style.opacity = '0.6';

            try {
                const endpoint = isOperatorOnline ?
                    '{{ route('admin.chat.operator.offline') }}' :
                    '{{ route('admin.chat.operator.online') }}';

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Update local state
                    isOperatorOnline = !isOperatorOnline;

                    // Update UI immediately
                    updateOperatorStatusUI();

                    // Show success message
                    showNotification(
                        isOperatorOnline ? 'You are now online for chat support' : 'You are now offline',
                        'success'
                    );

                    // Optional: Update the dashboard statistics if on chat page
                    if (window.location.pathname.includes('admin/chat')) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Failed to toggle operator status:', error);
                showNotification('Failed to update operator status', 'error');
            } finally {
                // Re-enable button
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        }

        function updateOperatorStatusUI() {
            const statusText = document.getElementById('operator-status-text');
            const statusIndicator = document.getElementById('operator-status-indicator');

            if (isOperatorOnline) {
                statusText.textContent = 'Go Offline';
                statusIndicator.className = 'w-2 h-2 rounded-full bg-green-500 animate-pulse';
            } else {
                statusText.textContent = 'Go Online';
                statusIndicator.className = 'w-2 h-2 rounded-full bg-gray-400';
            }
        }

        function showNotification(message, type) {
            // Remove any existing notifications
            const existingNotifications = document.querySelectorAll('.chat-notification');
            existingNotifications.forEach(n => n.remove());

            // Create new notification
            const notification = document.createElement('div');
            notification.className = `chat-notification fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Check operator status periodically (every 30 seconds)
        setInterval(async function() {
            try {
                const response = await fetch('/admin/chat/operator/status', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.is_online !== isOperatorOnline) {
                        isOperatorOnline = data.is_online;
                        updateOperatorStatusUI();
                    }
                }
            } catch (error) {
                console.log('Status check failed:', error);
            }
        }, 30000);

        // Initialize UI on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateOperatorStatusUI();
        });
    </script>
    <script>
    // Global operator status management for sidebar
    let operatorOnlineStatus = {{ $isOperatorOnline ? 'true' : 'false' }};

    async function toggleOperatorStatus() {
        try {
            const action = operatorOnlineStatus ? 'offline' : 'online';
            const response = await fetch(`{{ route('admin.chat.operator.offline') }}`.replace('offline', action), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (data.success) {
                operatorOnlineStatus = data.status === 'online';
                updateSidebarOperatorUI();
                
                // Show notification
                showSidebarNotification(
                    operatorOnlineStatus ? 'You are now online for chat support' : 'You are now offline',
                    'success'
                );
                
                // Refresh page statistics if on chat dashboard
                if (window.location.pathname.includes('/admin/chat')) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                showSidebarNotification('Failed to update operator status', 'error');
            }
        } catch (error) {
            console.error('Error toggling operator status:', error);
            showSidebarNotification('Error updating operator status', 'error');
        }
    }

    function updateSidebarOperatorUI() {
        const statusText = document.getElementById('operator-status-text');
        const statusIndicator = document.getElementById('operator-status-indicator');
        
        if (statusText) {
            statusText.textContent = operatorOnlineStatus ? 'Go Offline' : 'Go Online';
        }
        
        if (statusIndicator) {
            if (operatorOnlineStatus) {
                statusIndicator.className = 'w-2 h-2 rounded-full bg-green-500 animate-pulse';
            } else {
                statusIndicator.className = 'w-2 h-2 rounded-full bg-gray-400';
            }
        }
    }

    function showSidebarNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 transition-all duration-300 transform translate-x-full`;
        
        const bgColor = {
            'success': 'border-green-200 dark:border-green-700',
            'error': 'border-red-200 dark:border-red-700',
            'info': 'border-blue-200 dark:border-blue-700',
            'warning': 'border-yellow-200 dark:border-yellow-700'
        }[type] || 'border-gray-200 dark:border-gray-700';
        
        notification.className += ` ${bgColor}`;
        
        const iconColor = {
            'success': 'text-green-400',
            'error': 'text-red-400',
            'info': 'text-blue-400',
            'warning': 'text-yellow-400'
        }[type] || 'text-blue-400';
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' ? 
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>' :
                            '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
                        }
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-gray-900 dark:text-white">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button type="button" class="inline-flex text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 4000);
    }

    // Auto-refresh sidebar statistics every 30 seconds
    setInterval(async function() {
        try {
            const response = await fetch('{{ route('admin.chat.statistics') }}');
            const data = await response.json();
            
            // Update badges if they exist
            updateSidebarBadges(data);
        } catch (error) {
            console.error('Error refreshing sidebar statistics:', error);
        }
    }, 30000);

    function updateSidebarBadges(data) {
        const totalActive = (data.active_sessions || 0) + (data.waiting_sessions || 0);
        
        // Update main chat badge
        const mainBadge = document.querySelector('#chat-accordion button .bg-red-500, #chat-accordion button .bg-green-500');
        if (mainBadge && totalActive > 0) {
            mainBadge.textContent = totalActive;
            mainBadge.className = `inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium ${
                data.waiting_sessions > 0 ? 'bg-red-500 text-white animate-pulse' : 'bg-green-500 text-white'
            } ml-auto mr-2`;
        } else if (mainBadge && totalActive === 0) {
            mainBadge.remove();
        }
        
        // Update dashboard badge
        const dashboardBadge = document.querySelector('a[href="{{ route('admin.chat.index') }}"] .bg-red-100, a[href="{{ route('admin.chat.index') }}"] .bg-green-100');
        if (dashboardBadge && totalActive > 0) {
            dashboardBadge.textContent = totalActive;
        }
    }
</script>
@endpush
