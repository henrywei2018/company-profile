@props(['unreadMessagesCount' => 0, 'pendingQuotationsCount' => 0])

<div id="hs-application-sidebar"
    class="hs-overlay hs-overlay-open:translate-x-0
    -translate-x-full transition-all duration-300 transform
    w-64 h-full
    hidden
    fixed inset-y-0 start-0 z-60
    bg-white border-e border-gray-200
    lg:block lg:translate-x-0 lg:end-auto lg:bottom-0
    dark:bg-neutral-800 dark:border-neutral-700"
    role="dialog" tabindex="-1" aria-label="Sidebar">
    <div class="relative flex flex-col h-full max-h-full">
        <div class="px-6 pt-4 flex items-center">
            <!-- Logo -->
            <a class="flex-none rounded-xl text-xl inline-block font-semibold focus:outline-hidden focus:opacity-80"
                href="{{ route('admin.dashboard') }}" aria-label="{{ config('app.name') }}">
                @if (isset($companyProfile) && $companyProfile->logo)
                    <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" class="h-12">
                @else
                    <svg class="w-28 h-auto" width="116" height="32" viewBox="0 0 116 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M33.5696 30.8182V11.3182H37.4474V13.7003H37.6229C37.7952 13.3187 38.0445 12.9309 38.3707 12.5369C38.7031 12.1368 39.134 11.8045 39.6634 11.5398C40.1989 11.2689 40.8636 11.1335 41.6577 11.1335C42.6918 11.1335 43.6458 11.4044 44.5199 11.946C45.3939 12.4815 46.0926 13.291 46.6158 14.3743C47.139 15.4515 47.4006 16.8026 47.4006 18.4276C47.4006 20.0095 47.1451 21.3452 46.6342 22.4347C46.1295 23.518 45.4401 24.3397 44.5661 24.8999C43.6982 25.4538 42.7256 25.7308 41.6484 25.7308C40.8852 25.7308 40.2358 25.6046 39.7003 25.3523C39.1709 25.0999 38.737 24.7829 38.3984 24.4013C38.0599 24.0135 37.8014 23.6226 37.6229 23.2287H37.5028V30.8182H33.5696ZM37.4197 18.4091C37.4197 19.2524 37.5367 19.9879 37.7706 20.6158C38.0045 21.2436 38.343 21.733 38.7862 22.0838C39.2294 22.4285 39.768 22.6009 40.402 22.6009C41.0421 22.6009 41.5838 22.4254 42.027 22.0746C42.4702 21.7176 42.8056 21.2251 43.0334 20.5973C43.2673 19.9633 43.3842 19.2339 43.3842 18.4091C43.3842 17.5904 43.2704 16.8703 43.0426 16.2486C42.8149 15.6269 42.4794 15.1406 42.0362 14.7898C41.593 14.4389 41.0483 14.2635 40.402 14.2635C39.7618 14.2635 39.2202 14.4328 38.777 14.7713C38.34 15.1098 38.0045 15.59 37.7706 16.2116C37.5367 16.8333 37.4197 17.5658 37.4197 18.4091Z"
                            class="fill-blue-600 dark:fill-white" fill="currentColor" />
                        <path
                            d="M1 29.5V16.5C1 9.87258 6.37258 4.5 13 4.5C19.6274 4.5 25 9.87258 25 16.5C25 23.1274 19.6274 28.5 13 28.5H12"
                            class="stroke-blue-600 dark:stroke-white" stroke="currentColor" stroke-width="2" />
                        <path
                            d="M5 29.5V16.66C5 12.1534 8.58172 8.5 13 8.5C17.4183 8.5 21 12.1534 21 16.66C21 21.1666 17.4183 24.82 13 24.82H12"
                            class="stroke-blue-600 dark:stroke-white" stroke="currentColor" stroke-width="2" />
                        <circle cx="13" cy="16.5214" r="5" class="fill-blue-600 dark:fill-white"
                            fill="currentColor" />
                    </svg>
                @endif
            </a>
            <!-- End Logo -->
        </div>

        <!-- Navigation -->
        <div
            class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            <nav class="hs-accordion-group p-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="flex flex-col space-y-1">
                    <!-- Dashboard -->
                    <li>
                        <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                            href="{{ route('admin.dashboard') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <!-- Company Profile -->
                    <li>
                        <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.company.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                            href="{{ route('admin.company.edit') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" />
                            </svg>
                            Company Profile
                        </a>
                    </li>

                    <!-- Blog Accordion -->
                    <li class="hs-accordion" id="blog-accordion">
                        <button type="button"
                            class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.blog.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            Blog

                            <svg class="hs-accordion-active:block ms-auto hidden size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m18 15-6-6-6 6" />
                            </svg>

                            <svg class="hs-accordion-active:hidden ms-auto block size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div id="blog-accordion-content"
                            class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('admin.blog.*') ? 'block' : 'hidden' }}">
                            <ul class="ps-8 pt-1 space-y-1">
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.blog.index') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.blog.index') }}">
                                        Posts List
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.blog.create') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.blog.create') }}">
                                        Add New Post
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.blog.categories.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.blog.categories.index') }}">
                                        Categories
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Services Accordion -->
                    <li class="hs-accordion" id="services-accordion">
                        <button type="button"
                            class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.services.') || request()->routeIs('admin.service-categories.') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Services

                            <svg class="hs-accordion-active:block ms-auto hidden size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m18 15-6-6-6 6" />
                            </svg>

                            <svg class="hs-accordion-active:hidden ms-auto block size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div id="services-accordion-content"
                            class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('admin.services.') || request()->routeIs('admin.service-categories.') ? 'block' : 'hidden' }}">
                            <ul class="pt-2 ps-6">
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.services.index') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.services.index') }}">
                                        Services List
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.services.create') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.services.create') }}">
                                        Add New Service
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.service-categories.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.service-categories.index') }}">
                                        Categories
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Projects Accordion -->
                    <li class="hs-accordion" id="projects-accordion">
                        <button type="button"
                            class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2" />
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                            </svg>
                            Projects

                            <svg class="hs-accordion-active:block ms-auto hidden size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m18 15-6-6-6 6" />
                            </svg>

                            <svg class="hs-accordion-active:hidden ms-auto block size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div id="projects-accordion-content"
                            class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('admin.projects.*') ? 'block' : 'hidden' }}">
                            <ul class="pt-2 ps-6">
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.index') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.projects.index') }}">
                                        Projects List
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.create') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.projects.create') }}">
                                        Add New Project
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Team -->
                    <li>
                        <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.team.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                            href="{{ route('admin.team.index') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Team
                        </a>
                    </li>

                    <!-- Messages -->
                    <li>
                        <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.messages.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                            href="{{ route('admin.messages.index') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Messages
                            @if ($unreadMessagesCount > 0)
                                <span
                                    class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-500 text-white ml-auto">
                                    {{ $unreadMessagesCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <!-- Quotations -->
                    <li>
                        <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.quotations.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                            href="{{ route('admin.quotations.index') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
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

                    <!-- Testimonials -->
                    <li>
                        <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.testimonials.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                            href="{{ route('admin.testimonials.index') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Testimonials
                        </a>
                    </li>

                    <!-- Divider -->
                    <li class="my-3 border-t border-slate-700"></li>

                    <!-- Settings -->
                    <li class="hs-accordion" id="settings-accordion">
                        <button type="button"
                            class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.*') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2" />
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                            </svg>
                            Settings

                            <svg class="hs-accordion-active:block ms-auto hidden size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m18 15-6-6-6 6" />
                            </svg>

                            <svg class="hs-accordion-active:hidden ms-auto block size-4"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div id="settings-accordion-content"
                            class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 {{ request()->routeIs('admin.projects.*') ? 'block' : 'hidden' }}">
                            <ul class="pt-2 ps-6">
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.index') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.users.index') }}">
                                        Users
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.index') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.settings.index') }}">
                                        General Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.index') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.settings.email') }}">
                                        Email Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="flex items-center gap-x-3.5 py-2 px-2.5 {{ request()->routeIs('admin.projects.index') ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300' }} text-sm rounded-lg focus:outline-hidden focus:bg-gray-100 dark:focus:bg-neutral-700"
                                        href="{{ route('admin.settings.seo') }}">
                                        SEO Settings
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                </ul>
            </nav>
        </div>
