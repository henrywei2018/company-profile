<x-layouts.admin title="Company Profile">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Company Profile</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Manage your company's identity, brand, and digital presence
            </p>
        </div>
    </div>

    <!-- Profile Completion Alert -->
    @if($statistics['profile_complete'] < 80)
        <x-admin.alert type="warning" class="mb-6">
            <x-slot name="title">Profile Incomplete</x-slot>
            Your company profile is {{ $companyProfile->getCompletionPercentage() }}% complete. 
            <a href="{{ route('admin.company.edit') }}" class="font-medium underline">Complete your profile</a> 
            to improve your online presence.
        </x-admin.alert>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card 
            title="Profile Complete" 
            :value="$companyProfile->getCompletionPercentage() . '%'"
            :trend="$companyProfile->getCompletionPercentage() >= 80 ? 'up' : 'down'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
            iconColor="text-green-500" 
            iconBg="bg-green-100 dark:bg-green-800/30" 
        />

        <x-admin.stat-card 
            title="Social Links" 
            :value="$statistics['social_links_count']"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />'
            iconColor="text-blue-500" 
            iconBg="bg-blue-100 dark:bg-blue-800/30" 
        />

        <x-admin.stat-card 
            title="Certificates" 
            :value="$companyProfile->certificates_count"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
            iconColor="text-purple-500" 
            iconBg="bg-purple-100 dark:bg-purple-800/30" 
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Company Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <x-admin.card>
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Company Information</h3>
                        <x-admin.badge 
                            :color="$statistics['profile_complete'] ? 'green' : 'yellow'"
                            :text="$statistics['profile_complete'] ? 'Complete' : 'Incomplete'"
                        />
                    </div>
                </x-slot>

                <div class="space-y-6">
                    <!-- Company Name and Logo -->
                    <div class="flex items-start gap-6">
                        @if($companyProfile->logo_url)
                            <div class="flex-shrink-0">
                                <img 
                                    src="{{ $companyProfile->logo_url }}" 
                                    alt="{{ $companyProfile->company_name }} Logo"
                                    class="w-20 h-20 object-contain rounded-lg border border-gray-200 dark:border-gray-700 bg-white"
                                />
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $companyProfile->company_name ?: config('app.name') }}
                            </h4>
                            @if($companyProfile->tagline)
                                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $companyProfile->tagline }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- About Section -->
                    @if($companyProfile->about)
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-2">About</h5>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $companyProfile->about }}</p>
                        </div>
                    @endif

                    <!-- Vision & Mission -->
                    @if($companyProfile->vision || $companyProfile->mission)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($companyProfile->vision)
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">Vision</h5>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $companyProfile->vision }}</p>
                                </div>
                            @endif
                            
                            @if($companyProfile->mission)
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">Mission</h5>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $companyProfile->mission }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Contact Information Card -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contact Information</h3>
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Primary Contact -->
                    <div class="space-y-4">
                        <h5 class="font-medium text-gray-900 dark:text-white">Primary Contact</h5>
                        
                        @if($companyProfile->email)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $companyProfile->email }}</p>
                                </div>
                            </div>
                        @endif

                        @if($companyProfile->phone)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-800/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Phone</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $companyProfile->phone }}</p>
                                </div>
                            </div>
                        @endif

                        @if($companyProfile->whatsapp)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-800/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.074-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.097"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">WhatsApp</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $companyProfile->whatsapp }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Address -->
                    <div class="space-y-4">
                        <h5 class="font-medium text-gray-900 dark:text-white">Address</h5>
                        
                        @if($companyProfile->address)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-red-100 dark:bg-red-800/30 rounded-lg flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Address</p>
                                    <p class="font-medium text-gray-900 dark:text-white leading-relaxed">
                                        {{ $companyProfile->full_address }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </x-admin.card>

            <!-- Social Media Card -->
            @if($companyProfile->social_links)
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Social Media Presence</h3>
                    </x-slot>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($companyProfile->social_links as $platform => $url)
                            <a href="{{ $url }}" target="_blank" 
                               class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <div class="w-8 h-8 bg-{{ $platform === 'facebook' ? 'blue' : ($platform === 'twitter' ? 'sky' : ($platform === 'instagram' ? 'pink' : ($platform === 'linkedin' ? 'blue' : 'red'))) }}-100 dark:bg-{{ $platform === 'facebook' ? 'blue' : ($platform === 'twitter' ? 'sky' : ($platform === 'instagram' ? 'pink' : ($platform === 'linkedin' ? 'blue' : 'red'))) }}-800/30 rounded-lg flex items-center justify-center">
                                    @if($platform === 'facebook')
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    @elseif($platform === 'twitter')
                                        <svg class="w-4 h-4 text-sky-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    @elseif($platform === 'instagram')
                                        <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C8.396 0 7.989.013 7.041.048 5.67.088 4.898.138 4.26.264a6.5 6.5 0 00-2.346 1.53A6.5 6.5 0 00.264 4.26C.138 4.898.088 5.67.048 7.042.013 7.989 0 8.396 0 12.017s.013 4.028.048 4.975c.04 1.371.09 2.143.216 2.781a6.5 6.5 0 001.53 2.347 6.5 6.5 0 002.347 1.53c.638.126 1.41.176 2.781.216.947.035 1.354.048 4.975.048s4.028-.013 4.975-.048c1.371-.04 2.143-.09 2.781-.216a6.5 6.5 0 002.347-1.53 6.5 6.5 0 001.53-2.347c.126-.638.176-1.41.216-2.781.035-.947.048-1.354.048-4.975s-.013-4.028-.048-4.975c-.04-1.371-.09-2.143-.216-2.781a6.5 6.5 0 00-1.53-2.347A6.5 6.5 0 0019.822.264c-.638-.126-1.41-.176-2.781-.216C16.094.013 15.686 0 12.017 0zm0 2.17c3.564 0 3.985.012 5.394.048 1.3.06 2.006.276 2.477.458.622.242 1.067.532 1.534.999.466.466.757.91.999 1.534.182.471.398 1.177.458 2.477.036 1.409.048 1.83.048 5.394s-.012 3.985-.048 5.394c-.06 1.3-.276 2.006-.458 2.477-.242.622-.532 1.067-.999 1.534-.466.466-.91.757-1.534.999-.471.182-1.177.398-2.477.458-1.409.036-1.83.048-5.394.048s-3.985-.012-5.394-.048c-1.3-.06-2.006-.276-2.477-.458-.622-.242-1.067-.532-1.534-.999-.466-.466-.757-.91-.999-1.534-.182-.471-.398-1.177-.458-2.477-.036-1.409-.048-1.83-.048-5.394s.012-3.985.048-5.394c.06-1.3.276-2.006.458-2.477.242-.622.532-1.067.999-1.534.466-.466.91-.757 1.534-.999.471-.182 1.177-.398 2.477-.458 1.409-.036 1.83-.048 5.394-.048z"/><path d="M12.017 15.33a3.312 3.312 0 110-6.625 3.312 3.312 0 010 6.625zM12.017 7.729a4.288 4.288 0 100 8.575 4.288 4.288 0 000-8.575zM18.584 6.199a1.002 1.002 0 11-2.003 0 1.002 1.002 0 012.003 0z"/></svg>
                                    @elseif($platform === 'linkedin')
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $platform }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Connected</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                </x-slot>

                <div class="space-y-3 items-center">
                    <x-admin.button 
                        href="{{ route('admin.company.edit') }}" 
                        color="primary" 
                        size="sm" 
                        class="w-full justify-center"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'>
                        Edit Profile
                    </x-admin.button>
                    
                    <x-admin.button 
                        href="{{ route('admin.company.seo') }}" 
                        color="secondary" 
                        size="sm" 
                        class="w-full justify-center"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />'>
                        SEO Settings
                    </x-admin.button>
                    
                    <x-admin.button 
                        href="{{ route('admin.company.certificates') }}" 
                        color="secondary" 
                        size="sm" 
                        class="w-full justify-center"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'>
                        Manage Certificates
                    </x-admin.button>

                    <hr class="border-gray-200 dark:border-gray-700">
                    
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                            
                            <div class="py-1">
                                <a href="{{ route('admin.company.export.pdf') }}" 
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download PDF
                                </a>
                                
                                <a href="{{ route('admin.company.export.pdf.stream') }}" 
                                   target="_blank"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View PDF
                                </a>
                                
                                <a href="{{ route('admin.company.export.certificates.pdf') }}" 
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Certificates PDF
                                </a>
                                
                                <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                
                                <a href="{{ route('admin.company.export') }}" 
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Export JSON
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Profile Health -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Health</h3>
                </x-slot>

                <div class="space-y-4">
                    <!-- Completion Progress -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Completion</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $companyProfile->getCompletionPercentage() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $companyProfile->getCompletionPercentage() }}%"></div>
                        </div>
                    </div>

                    <!-- Health Checklist -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            @if($statistics['has_logo'])
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-600 dark:text-gray-400">Company Logo</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($statistics['has_description'])
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-600 dark:text-gray-400">Company Description</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($statistics['contact_info_complete'])
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-600 dark:text-gray-400">Contact Information</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($statistics['social_links_count'] >= 3)
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-600 dark:text-gray-400">Social Media ({{ $statistics['social_links_count'] }}/5)</span>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Recent Updates -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Updates</h3>
                </x-slot>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">Profile Updated</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $companyProfile->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-layouts.admin>