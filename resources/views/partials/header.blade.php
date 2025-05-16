<div class="bg-amber-400 dark:bg-amber-500">
    <div class="max-w-[85rem] px-4 py-2 sm:px-6 lg:px-8 mx-auto">
        <div class="grid justify-center md:grid-cols-2 md:justify-between gap-2">
            <!-- Contact Info -->
            <div class="flex items-center gap-x-3 gap-y-2 flex-wrap text-white">
                @if(isset($companyProfile->phone) && $companyProfile->phone)
                    <a class="inline-flex gap-x-2 text-xs hover:text-amber-200 dark:hover:text-amber-200" href="tel:{{ $companyProfile->phone }}">
                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        {{ $companyProfile->phone }}
                    </a>
                @endif
                
                @if(isset($companyProfile->email) && $companyProfile->email)
                    <a class="inline-flex gap-x-2 text-xs hover:text-amber-200 dark:hover:text-amber-200" href="mailto:{{ $companyProfile->email }}">
                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z"/>
                            <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z"/>
                        </svg>
                        {{ $companyProfile->email }}
                    </a>
                @endif
                
                @if(isset($companyProfile->address) && $companyProfile->address)
                    <div class="inline-flex gap-x-2 text-xs">
                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        {{ $companyProfile->address }}
                    </div>
                @endif
            </div>
            <!-- Social Media Links -->
            <div class="flex items-center justify-end gap-x-3">
                @if(isset($companyProfile->facebook) && $companyProfile->facebook)
                    <a class="size-6 inline-flex justify-center items-center gap-x-2 text-white hover:text-amber-200 dark:hover:text-amber-200" href="{{ $companyProfile->facebook }}" target="_blank" rel="noopener noreferrer">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                        </svg>
                    </a>
                @endif
                
                @if(isset($companyProfile->twitter) && $companyProfile->twitter)
                    <a class="size-6 inline-flex justify-center items-center gap-x-2 text-white hover:text-amber-200 dark:hover:text-amber-200" href="{{ $companyProfile->twitter }}" target="_blank" rel="noopener noreferrer">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                        </svg>
                    </a>
                @endif
                
                @if(isset($companyProfile->linkedin) && $companyProfile->linkedin)
                    <a class="size-6 inline-flex justify-center items-center gap-x-2 text-white hover:text-amber-200 dark:hover:text-amber-200" href="{{ $companyProfile->linkedin }}" target="_blank" rel="noopener noreferrer">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
                        </svg>
                    </a>
                @endif
                
                @if(isset($companyProfile->instagram) && $companyProfile->instagram)
                    <a class="size-6 inline-flex justify-center items-center gap-x-2 text-white hover:text-amber-200 dark:hover:text-amber-200" href="{{ $companyProfile->instagram }}" target="_blank" rel="noopener noreferrer">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="flex flex-wrap lg:justify-start lg:flex-nowrap z-50 w-full py-7">
    <nav class="relative max-w-7xl w-full mx-auto px-4 md:flex md:items-center md:justify-between md:px-6 lg:px-8 py-2" aria-label="Global">
        <div class="flex items-center justify-between">
            <a class="flex-none" href="{{ route('home') }}" aria-label="{{ config('app.name') }}">
                @if(isset($companyProfile) && $companyProfile->logo)
                    <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" class="h-10 md:h-12">
                @else
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-amber-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 21H21M3 18H21M5 18V10C5 9.73478 5.10536 9.48043 5.29289 9.29289C5.48043 9.10536 5.73478 9 6 9H18C18.2652 9 18.5196 9.10536 18.7071 9.29289C18.8946 9.48043 19 9.73478 19 10V18M7 9V6C7 5.73478 7.10536 5.48043 7.29289 5.29289C7.48043 5.10536 7.73478 5 8 5H16C16.2652 5 16.5196 5.10536 16.7071 5.29289C16.8946 5.48043 17 5.73478 17 6V9M9 13H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-800 dark:text-white">{{ config('app.name', 'CV Usaha Prima Lestari') }}</span>
                    </div>
                @endif
            </a>
            <div class="md:hidden">
                <button type="button" class="hs-collapse-toggle size-8 flex justify-center items-center text-sm font-semibold rounded-full border border-gray-200 text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" data-hs-collapse="#navbar-collapse-with-animation" aria-controls="navbar-collapse-with-animation" aria-label="Toggle navigation">
                    <svg class="hs-collapse-open:hidden flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
                    <svg class="hs-collapse-open:block hidden flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        </div>
        <div id="navbar-collapse-with-animation" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block">
            <div class="flex flex-col gap-y-4 gap-x-0 mt-5 md:flex-row md:items-center md:justify-end md:gap-y-0 md:gap-x-7 md:mt-0 md:ps-7">
                <a href="{{ route('home') }}" class="font-medium text-gray-700 hover:text-amber-600 md:py-6 dark:text-gray-200 dark:hover:text-amber-500 {{ request()->routeIs('home') ? 'text-amber-600 dark:text-amber-500' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="font-medium text-gray-700 hover:text-amber-600 md:py-6 dark:text-gray-200 dark:hover:text-amber-500 {{ request()->routeIs('about*') ? 'text-amber-600 dark:text-amber-500' : '' }}">About</a>
                <a href="{{ route('services.index') }}" class="font-medium text-gray-700 hover:text-amber-600 md:py-6 dark:text-gray-200 dark:hover:text-amber-500 {{ request()->routeIs('services*') ? 'text-amber-600 dark:text-amber-500' : '' }}">Services</a>
                <a href="{{ route('portfolio.index') }}" class="font-medium text-gray-700 hover:text-amber-600 md:py-6 dark:text-gray-200 dark:hover:text-amber-500 {{ request()->routeIs('portfolio*') ? 'text-amber-600 dark:text-amber-500' : '' }}">Portfolio</a>
                <a href="{{ route('blog.index') }}" class="font-medium text-gray-700 hover:text-amber-600 md:py-6 dark:text-gray-200 dark:hover:text-amber-500 {{ request()->routeIs('blog*') ? 'text-amber-600 dark:text-amber-500' : '' }}">Blog</a>
                <a href="{{ route('contact.index') }}" class="font-medium text-gray-700 hover:text-amber-600 md:py-6 dark:text-gray-200 dark:hover:text-amber-500 {{ request()->routeIs('contact*') ? 'text-amber-600 dark:text-amber-500' : '' }}">Contact</a>
                
                @guest
                    <div class="pt-3 md:pt-0">
                        <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-x-2 py-2 px-3 text-sm font-semibold rounded-lg border border-transparent bg-amber-600 text-white hover:bg-amber-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Login
                        </a>
                    </div>
                @else
                    <div class="hs-dropdown [--strategy:static] sm:[--strategy:fixed] [--adaptive:none] sm:[--trigger:hover] sm:py-4">
                        <button type="button" class="flex items-center w-full text-gray-700 hover:text-gray-500 font-medium dark:text-gray-200 dark:hover:text-gray-400">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="flex-shrink-0 ms-2 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] sm:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 sm:w-48 hidden z-10 bg-white sm:shadow-md rounded-lg p-2 dark:bg-gray-800 sm:dark:border dark:border-gray-700 dark:divide-gray-700 before:absolute top-full sm:border before:-top-5 before:start-0 before:w-full before:h-5">
                            @if(Auth::user()->hasRole('admin'))
                                <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-amber-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300" href="{{ route('admin.dashboard') }}">
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/>
                                        <path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/>
                                        <path d="M12 3v6"/>
                                    </svg>
                                    Admin Dashboard
                                </a>
                            @endif
                            
                            @if(Auth::user()->hasRole('client'))
                                <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-amber-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300" href="{{ route('client.dashboard') }}">
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 17 L22 17"/>
                                        <path d="M2 12 L22 12"/>
                                        <path d="M2 7 L22 7"/>
                                    </svg>
                                    Client Dashboard
                                </a>
                            @endif
                            
                            <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-amber-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300" href="{{ route('profile.edit') }}">
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                My Profile
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-amber-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
                
                <div class="pt-3 md:pt-0">
                    <a href="{{ route('quotation.create') }}" class="inline-flex justify-center items-center gap-x-2 py-2 px-3 text-sm font-semibold rounded-lg border border-amber-600 text-amber-600 hover:border-amber-500 hover:text-amber-500 disabled:opacity-50 disabled:pointer-events-none dark:border-amber-500 dark:text-amber-500 dark:hover:text-amber-400 dark:hover:border-amber-400 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-amber-600">
                        Get a Quote
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>