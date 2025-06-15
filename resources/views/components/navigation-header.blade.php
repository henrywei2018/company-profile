<nav x-data="{ open: false }" class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}">
                    <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo">
                </a>
            </div>
            <!-- Desktop Menu -->
            <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                <x-navigation-link :href="route('home')" :active="request()->routeIs('home')">
                    Home
                </x-navigation-link>
                <x-navigation-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                    Services
                </x-navigation-link>
                <x-navigation-link :href="route('portfolio.index')" :active="request()->routeIs('portfolio.*')">
                    Portfolio
                </x-navigation-link>
                <x-navigation-link :href="route('about')" :active="request()->routeIs('about')">
                    About
                </x-navigation-link>
                <x-navigation-link :href="route('contact.index')" :active="request()->routeIs('contact.*')">
                    Contact
                </x-navigation-link>
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
            </div>
            <!-- Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 focus:outline-none">
                    <svg x-show="!open" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path class="inline" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path class="inline" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div x-show="open" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-navigation-link :href="route('home')" :active="request()->routeIs('home')" class="block">
                Home
            </x-navigation-link>
            <x-navigation-link :href="route('services.index')" :active="request()->routeIs('services.*')" class="block">
                Services
            </x-navigation-link>
            <x-navigation-link :href="route('portfolio.index')" :active="request()->routeIs('portfolio.*')" class="block">
                Portfolio
            </x-navigation-link>
            <x-navigation-link :href="route('about')" :active="request()->routeIs('about')" class="block">
                About
            </x-navigation-link>
            <x-navigation-link :href="route('contact.index')" :active="request()->routeIs('contact.*')" class="block">
                Contact
            </x-navigation-link>
        </div>
    </div>
</nav>
