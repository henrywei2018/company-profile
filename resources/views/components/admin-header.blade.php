<!-- resources/views/components/admin-header.blade.php -->
@props(['title' => 'Dashboard'])

<header class="flex flex-wrap sm:justify-start sm:flex-nowrap w-full bg-white border-b border-gray-200 text-sm py-4 sm:py-0 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 dark:text-white">{{ $title }}</h1>
                @if(isset($breadcrumbs))
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse text-sm">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500">
                                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 0C4.478 0 0 4.478 0 10c0 4.411 2.865 8.159 6.84 9.476.5.092.683-.217.683-.481 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.341-3.369-1.341-.454-1.155-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.268 2.75 1.026A9.578 9.578 0 0 1 10 4.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.026 2.747-1.026.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.933.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C17.14 18.155 20 14.41 20 10c0-5.522-4.478-10-10-10z"/>
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            {{ $breadcrumbs }}
                        </ol>
                    </nav>
                @endif
            </div>
            
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Notifications -->
                <div class="hs-dropdown relative inline-flex">
                    <button id="hs-dropdown-notification" type="button" class="hs-dropdown-toggle inline-flex flex-shrink-0 justify-center items-center h-9 w-9 font-medium rounded-full text-gray-500 hover:text-gray-700 align-middle hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition-all text-sm dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-800">
                        <span class="sr-only">Notifications</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                        </svg>
                        <span class="absolute top-0 end-0 h-2 w-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[20rem] bg-white shadow-md rounded-lg p-2 dark:bg-gray-800 dark:border dark:border-gray-700" aria-labelledby="hs-dropdown-notification">
                        <div class="py-2 first:pt-0 last:pb-0">
                            <span class="block py-2 px-3 text-sm text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">Notifications</span>
                            
                            <div class="pt-2 max-h-72 overflow-y-auto">
                                @if(isset($unreadMessages) && count($unreadMessages) > 0)
                                    @foreach($unreadMessages as $message)
                                        <a class="flex py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" href="{{ route('admin.messages.show', $message) }}">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex justify-center items-center h-9 w-9 rounded-full bg-blue-50 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">
                                                    <svg class="flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                                </span>
                                            </div>
                                            <div class="ms-3">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ Str::limit($message->subject, 40) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-500">From: {{ $message->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="text-sm text-gray-500 p-4 dark:text-gray-400">No new notifications</div>
                                @endif
                            </div>
                            
                            <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700">
                                <a class="text-sm text-blue-600 hover:text-blue-800 font-medium dark:text-blue-500 dark:hover:text-blue-400" href="{{ route('admin.messages.index') }}">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                    <button id="hs-dropdown-with-header" type="button" class="hs-dropdown-toggle inline-flex flex-shrink-0 justify-center items-center gap-2 h-[2.375rem] w-[2.375rem] rounded-full font-medium bg-white text-gray-700 align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 focus:ring-offset-white transition-all text-xs dark:bg-gray-800 dark:hover:bg-slate-800 dark:text-gray-400 dark:hover:text-white dark:focus:ring-gray-700 dark:focus:ring-offset-gray-800">
                        <img class="inline-block h-[2.375rem] w-[2.375rem] rounded-full ring-2 ring-white dark:ring-gray-800" src="{{ auth()->user()->avatarUrl ?? asset('images/default-avatar.jpg') }}" alt="{{ auth()->user()->name }}">
                    </button>

                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 dark:bg-gray-800 dark:border dark:border-gray-700" aria-labelledby="hs-dropdown-with-header">
                        <div class="px-5 py-3 -m-2 bg-gray-100 rounded-t-lg dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Signed in as</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-300">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="mt-2 py-2 first:pt-0 last:pb-0">
                            <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300" href="{{ route('profile.edit') }}">
                                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                My Profile
                            </a>
                            <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300" href="{{ route('admin.settings.index') }}">
                                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.6 20-5.6-5.6 5.6-5.6"/><path d="m14.4 20 5.6-5.6-5.6-5.6"/></svg>
                                Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>