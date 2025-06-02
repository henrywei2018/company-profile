{{-- resources/views/components/admin/admin-header.blade.php --}}
<header
    class="sticky top-0 inset-x-0 z-50 w-full bg-white border-b border-gray-200 text-sm dark:bg-gray-800 dark:border-gray-700 lg:ps-64">
    <nav class="w-full mx-auto px-4 py-2.5 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8"
        aria-label="Global">
        <!-- Left: Logo -->
        <div class="flex items-center lg:hidden">
            <a href="{{ route('admin.dashboard') }}" aria-label="{{ config('app.name') }}"
                class="text-xl font-bold text-blue-600 dark:text-white">
                @if (isset($companyProfile) && $companyProfile->logo)
                    <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" class="h-8 md:h-10">
                @else
                    {{ config('app.name') }}
                @endif
            </a>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center justify-end w-full gap-x-2">
            <!-- Theme Toggle -->
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

            <!-- Improved Notification Dropdown -->
            <x-notification.dropdown 
                :notifications="$recentNotifications ?? collect()" 
                :unread-count="$unreadNotificationsCount ?? 0" 
                variant="admin" 
                :max-display="8"
                :show-all="true" 
            />

            <!-- User Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 inline-flex justify-center items-center rounded-full text-sm font-semibold text-gray-800 dark:text-white"
                    data-hs-dropdown-toggle>
                    @if(auth()->user()->avatar)
                        <img class="size-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        <img class="size-8 rounded-full"
                            src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80"
                            alt="User Avatar">
                    @endif
                </button>

                <div class="hs-dropdown-menu hidden z-50 min-w-60 mt-2 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
                    aria-labelledby="hs-dropdown-toggle">
                    <div class="px-5 py-3 bg-gray-50 dark:bg-neutral-700 rounded-t-lg">
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Signed in as</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="p-1.5 space-y-0.5">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9,22 9,12 15,12 15,22" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.profile.edit') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            My Profile
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Settings
                        </a>
                        
                        <div class="border-t border-gray-200 dark:border-neutral-700 my-1"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                    <polyline points="10 17 15 12 10 7" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End User Dropdown -->
        </div>
    </nav>
</header>

@push('scripts')
<script>
    function testAdminNotification() {
        fetch('{{ route("admin.notifications.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
                
                // Refresh notifications after a short delay
                setTimeout(() => {
                    window.notificationManager.updateBadgeCount();
                    window.notificationManager.refresh();
                }, 2000);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            console.error('Failed to send test notification:', error);
            showToast('error', 'Failed to send test notification');
        });
    }

    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
@endpush