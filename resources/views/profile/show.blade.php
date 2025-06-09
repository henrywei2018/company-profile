<!-- resources/views/profile/show.blade.php -->
<x-layouts.app title="My Profile">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div class="flex items-center space-x-6">
                            <div class="relative">
                                <x-admin.avatar 
                                    :src="$user->avatar_url" 
                                    :alt="$user->name"
                                    size="xl"
                                />
                                @if($activitySummary['profile_completion']['essential_percentage'] < 100)
                                <div class="absolute -top-2 -right-2 bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded-full">
                                    {{ $activitySummary['profile_completion']['essential_percentage'] }}%
                                </div>
                                @endif
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                                <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                                @if($user->company)
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ $user->company }}</p>
                                @endif
                                @if($user->position)
                                <p class="text-xs text-gray-400 dark:text-gray-600">{{ $user->position }}</p>
                                @endif
                                <div class="flex items-center space-x-2 mt-2">
                                    @if($user->email_verified_at)
                                        <x-admin.badge type="success" size="sm">Verified</x-admin.badge>
                                    @else
                                        <x-admin.badge type="warning" size="sm">Unverified</x-admin.badge>
                                    @endif
                                    @if($user->is_active)
                                        <x-admin.badge type="success" size="sm">Active</x-admin.badge>
                                    @else
                                        <x-admin.badge type="danger" size="sm">Inactive</x-admin.badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <x-admin.button 
                                href="{{ route('admin.users.profile.edit') }}" 
                                color="primary"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
                            >
                                Edit Profile
                            </x-admin.button>
                            <x-admin.button 
                                href="{{ route('profile.preferences') }}" 
                                color="light"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                            >
                                Preferences
                            </x-admin.button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Completion -->
                <div class="lg:col-span-2">
                    @if($activitySummary['profile_completion']['essential_percentage'] < 100)
                    <x-admin.card title="Complete Your Profile" class="mb-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile Completion</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $activitySummary['profile_completion']['completed_essential'] }}/{{ $activitySummary['profile_completion']['total_essential'] }} essential fields
                                </span>
                            </div>
                            <x-admin.progress 
                                :value="$activitySummary['profile_completion']['essential_percentage']" 
                                color="blue"
                                showLabel="true"
                                labelPosition="outside-right"
                            />
                            @if(count($suggestions) > 0)
                            <div class="space-y-2 mt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Suggestions:</h4>
                                @foreach(array_slice($suggestions, 0, 3) as $suggestion)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $suggestion['title'] }}</h5>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $suggestion['description'] }}</p>
                                    </div>
                                    <x-admin.button 
                                        href="{{ $suggestion['action_url'] }}" 
                                        color="primary" 
                                        size="sm"
                                    >
                                        Complete
                                    </x-admin.button>
                                </div>
                                @endforeach
                                @if(count($suggestions) > 3)
                                <a href="{{ route('profile.completion') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    View all suggestions ({{ count($suggestions) }})
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </x-admin.card>
                    @endif

                    <!-- Profile Information -->
                    <x-admin.card title="Profile Information">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->name }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->email }}</p>
                            </div>
                            
                            @if($user->phone)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->phone }}</p>
                            </div>
                            @endif
                            
                            @if($user->company)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->company }}</p>
                            </div>
                            @endif

                            @if($user->position)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->position }}</p>
                            </div>
                            @endif

                            @if($user->website)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    <a href="{{ $user->website }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        {{ $user->website }}
                                    </a>
                                </p>
                            </div>
                            @endif
                            
                            @if($user->address)
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->address }}</p>
                                @if($user->city || $user->state || $user->country)
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ collect([$user->city, $user->state, $user->postal_code, $user->country])->filter()->join(', ') }}
                                </p>
                                @endif
                            </div>
                            @endif

                            @if($user->bio)
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Biography</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->bio }}</p>
                            </div>
                            @endif
                        </div>
                    </x-admin.card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Account Statistics -->
                    <x-admin.card title="Account Statistics">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Last Login</span>
                                <span class="text-sm text-gray-900 dark:text-white">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Login Count</span>
                                <x-admin.badge type="info">{{ $user->login_count ?? 0 }}</x-admin.badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Active Roles</span>
                                <x-admin.badge type="primary">{{ $activitySummary['account_stats']['roles_count'] }}</x-admin.badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Permissions</span>
                                <x-admin.badge type="success">{{ $activitySummary['account_stats']['permissions_count'] }}</x-admin.badge>
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Content Statistics -->
                    <x-admin.card title="Content Statistics">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Projects</span>
                                <x-admin.badge type="blue">{{ $activitySummary['content_stats']['projects_count'] }}</x-admin.badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Quotations</span>
                                <x-admin.badge type="amber">{{ $activitySummary['content_stats']['quotations_count'] }}</x-admin.badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Messages</span>
                                <x-admin.badge type="green">{{ $activitySummary['content_stats']['messages_count'] }}</x-admin.badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Posts</span>
                                <x-admin.badge type="purple">{{ $activitySummary['content_stats']['posts_count'] }}</x-admin.badge>
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Quick Actions -->
                    <x-admin.card title="Quick Actions">
                        <div class="space-y-3">
                            <x-admin.button 
                                href="{{ route('admin.users.profile.edit') }}" 
                                color="primary" 
                                class="w-full"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
                            >
                                Edit Profile
                            </x-admin.button>
                            
                            <x-admin.button 
                                href="{{ route('profile.change-password') }}" 
                                color="warning" 
                                class="w-full"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'
                            >
                                Change Password
                            </x-admin.button>
                            
                            <x-admin.button 
                                href="{{ route('profile.preferences') }}" 
                                color="info" 
                                class="w-full"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>'
                            >
                                Notification Settings
                            </x-admin.button>

                            <x-admin.button 
                                href="{{ route('profile.export') }}" 
                                color="light" 
                                class="w-full"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
                            >
                                Export Data
                            </x-admin.button>
                        </div>
                    </x-admin.card>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>