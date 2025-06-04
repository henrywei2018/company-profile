<x-layouts.admin title="Edit Company Profile">

    <div class="flex width-full items-center justify-between">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <x-admin.breadcrumb :items="[
                'Company Profile' => route('admin.company.index'),
                'Edit' => '#',
            ]" />
        </div>

        <div class="flex items-center gap-2">
            <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                Auto-saved
            </div>
            <x-admin.button type="submit" form="profile-form" color="primary" size="sm">
                Save Changes
            </x-admin.button>
        </div>
    </div>


    <!-- Main Form -->
    <form id="profile-form" action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid max-w gap-3 my-auto">
            <!-- Profile Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Card Header with Logo -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 px-6 py-8">
                    <div class="flex items-start gap-6">
                        <!-- Logo Upload -->
                        <div class="relative group">
                            @if ($companyProfile->logo_url)
                                <img src="{{ $companyProfile->logo_url }}" alt="Company Logo"
                                    class="w-20 h-20 object-contain rounded-xl bg-white border-2 border-white shadow-lg">
                            @else
                                <div
                                    class="w-20 h-20 bg-white rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Logo Upload Overlay -->
                            <label
                                class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <input type="file" name="logo" accept="image/*" class="sr-only">
                            </label>
                        </div>

                        <!-- Company Name & Tagline -->
                        <div class="flex-1 space-y-3">
                            <input type="text" name="company_name"
                                value="{{ old('company_name', $companyProfile->company_name ?? config('app.name')) }}"
                                placeholder="Company Name"
                                class="text-2xl font-bold bg-transparent border-none text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0 p-0 w-full"
                                required>

                            <input type="text" name="tagline" value="{{ old('tagline', $companyProfile->tagline) }}"
                                placeholder="Company tagline or slogan"
                                class="text-lg bg-transparent border-none text-gray-600 dark:text-gray-300 placeholder-gray-400 focus:ring-0 p-0 w-full">
                        </div>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-6 space-y-6">
                    <!-- About Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">About</label>
                        <textarea name="about" rows="3" maxlength="500" placeholder="Brief description of your company..."
                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">{{ old('about', $companyProfile->about) }}</textarea>
                        <div class="text-xs text-gray-500 text-right mt-1">
                            <span id="about-count">{{ strlen($companyProfile->about ?? '') }}</span>/500
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $companyProfile->established ?? '—' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Established</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $companyProfile->certificates_count }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Certificates</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ count($companyProfile->social_links) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Social Links</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $companyProfile->getCompletionPercentage() }}%</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Complete</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-8 px-6" role="tablist">
                        <button type="button"
                            class="tab-button active py-4 px-1 border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 text-sm font-medium"
                            data-tab="contact">
                            Contact
                        </button>
                        <button type="button"
                            class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium"
                            data-tab="social">
                            Social
                        </button>
                        <button type="button"
                            class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium"
                            data-tab="location">
                            Location
                        </button>
                        <button type="button"
                            class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium"
                            data-tab="culture">
                            Culture
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Contact Tab -->
                    <div id="contact-tab" class="tab-content active">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Email</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <input type="email" name="email"
                                            value="{{ old('email', $companyProfile->email) }}"
                                            class="pl-10 w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                                            required>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Phone</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                        </div>
                                        <input type="tel" name="phone"
                                            value="{{ old('phone', $companyProfile->phone) }}"
                                            class="pl-10 w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                                            required>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">WhatsApp</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-green-500" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.074-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.097" />
                                            </svg>
                                        </div>
                                        <input type="text" name="whatsapp"
                                            value="{{ old('whatsapp', $companyProfile->whatsapp) }}"
                                            placeholder="+62 812 3456 7890"
                                            class="pl-10 w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Address</label>
                                <textarea name="address" rows="5" placeholder="Complete business address..."
                                    class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                                    required>{{ old('address', $companyProfile->address) }}</textarea>

                                <div class="grid grid-cols-2 gap-3 mt-3">
                                    <input type="text" name="city"
                                        value="{{ old('city', $companyProfile->city) }}" placeholder="City"
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                    <input type="text" name="postal_code"
                                        value="{{ old('postal_code', $companyProfile->postal_code) }}"
                                        placeholder="Postal Code"
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Tab -->
                    <div id="social-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $socialPlatforms = [
                                    'facebook' => [
                                        'Facebook',
                                        '#1877F2',
                                        'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
                                    ],
                                    'instagram' => [
                                        'Instagram',
                                        '#E4405F',
                                        'M12.017 0C8.396 0 7.989.013 7.041.048 5.67.088 4.898.138 4.26.264a6.5 6.5 0 00-2.346 1.53A6.5 6.5 0 00.264 4.26C.138 4.898.088 5.67.048 7.042.013 7.989 0 8.396 0 12.017s.013 4.028.048 4.975c.04 1.371.09 2.143.216 2.781a6.5 6.5 0 001.53 2.347 6.5 6.5 0 002.347 1.53c.638.126 1.41.176 2.781.216.947.035 1.354.048 4.975.048s4.028-.013 4.975-.048c1.371-.04 2.143-.09 2.781-.216a6.5 6.5 0 002.347-1.53 6.5 6.5 0 001.53-2.347c.126-.638.176-1.41.216-2.781.035-.947.048-1.354.048-4.975s-.013-4.028-.048-4.975c-.04-1.371-.09-2.143-.216-2.781a6.5 6.5 0 00-1.53-2.347A6.5 6.5 0 0019.822.264c-.638-.126-1.41-.176-2.781-.216C16.094.013 15.686 0 12.017 0zm0 2.17c3.564 0 3.985.012 5.394.048 1.3.06 2.006.276 2.477.458.622.242 1.067.532 1.534.999.466.466.757.91.999 1.534.182.471.398 1.177.458 2.477.036 1.409.048 1.83.048 5.394s-.012 3.985-.048 5.394c-.06 1.3-.276 2.006-.458 2.477-.242.622-.532 1.067-.999 1.534-.466.466-.91.757-1.534.999-.471.182-1.177.398-2.477.458-1.409.036-1.83.048-5.394.048s-3.985-.012-5.394-.048c-1.3-.06-2.006-.276-2.477-.458-.622-.242-1.067-.532-1.534-.999-.466-.466-.757-.91-.999-1.534-.182-.471-.398-1.177-.458-2.477-.036-1.409-.048-1.83-.048-5.394s.012-3.985.048-5.394c.06-1.3.276-2.006.458-2.477.242-.622.532-1.067.999-1.534.466-.466.91-.757 1.534-.999.471-.182 1.177-.398 2.477-.458 1.409-.036 1.83-.048 5.394-.048z',
                                    ],
                                    'twitter' => [
                                        'Twitter/X',
                                        '#1DA1F2',
                                        'M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z',
                                    ],
                                    'linkedin' => [
                                        'LinkedIn',
                                        '#0077B5',
                                        'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
                                    ],
                                    'youtube' => [
                                        'YouTube',
                                        '#FF0000',
                                        'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
                                    ],
                                ];
                            @endphp

                            @foreach ($socialPlatforms as $platform => [$name, $color, $path])
                                <div
                                    class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5" style="color: {{ $color }}" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="{{ $path }}" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <input type="url" name="{{ $platform }}"
                                            value="{{ old($platform, $companyProfile->$platform) }}"
                                            placeholder="{{ $name }} URL"
                                            class="w-full border-none bg-transparent text-sm focus:ring-0 p-0 dark:text-gray-300">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Location Tab -->
                    <div id="location-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">GPS Coordinates</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Latitude</label>
                                        <input type="text" name="latitude"
                                            value="{{ old('latitude', $companyProfile->latitude) }}"
                                            placeholder="-6.2088"
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Longitude</label>
                                        <input type="text" name="longitude"
                                            value="{{ old('longitude', $companyProfile->longitude) }}"
                                            placeholder="106.8456"
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                    </div>
                                </div>

                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Get
                                                Coordinates</h4>
                                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                                Right-click your location on <a href="https://maps.google.com"
                                                    target="_blank" class="underline">Google Maps</a> and copy the
                                                coordinates.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Country</h3>
                                <select name="country"
                                    class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Select Country</option>
                                    @php
                                        $countries = [
                                            'Indonesia' => 'Indonesia',
                                            'Malaysia' => 'Malaysia',
                                            'Singapore' => 'Singapore',
                                            'Thailand' => 'Thailand',
                                            'Philippines' => 'Philippines',
                                            'Vietnam' => 'Vietnam',
                                            'Cambodia' => 'Cambodia',
                                            'Laos' => 'Laos',
                                            'Myanmar' => 'Myanmar',
                                            'Brunei' => 'Brunei',
                                        ];
                                    @endphp
                                    @foreach ($countries as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ old('country', $companyProfile->country) === $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Culture Tab -->
                    <div id="culture-tab" class="tab-content hidden">
                        <div class="space-y-6">
                            <!-- Vision & Mission -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Vision</label>
                                    <textarea name="vision" rows="4" placeholder="Your company's vision for the future..."
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">{{ old('vision', $companyProfile->vision) }}</textarea>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Mission</label>
                                    <textarea name="mission" rows="4" placeholder="Your company's mission and purpose..."
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">{{ old('mission', $companyProfile->mission) }}</textarea>
                                </div>
                            </div>

                            <!-- Company Values -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Company
                                        Values</label>
                                    <button type="button" onclick="addValue()"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Value
                                    </button>
                                </div>

                                <div id="values-container" class="space-y-3">
                                    @php
                                        $formValues = old('values', $companyProfile->values ?? []);
                                        if (is_string($formValues)) {
                                            $decoded = json_decode($formValues, true);
                                            $formValues = is_array($decoded) ? $decoded : [];
                                        }
                                        if (!is_array($formValues)) {
                                            $formValues = [];
                                        }
                                        $formValues = array_values(
                                            array_filter($formValues, function ($value) {
                                                return is_string($value) && !empty(trim($value));
                                            }),
                                        );
                                        if (empty($formValues)) {
                                            $formValues = [''];
                                        }
                                    @endphp

                                    @foreach ($formValues as $arrayIndex => $currentValue)
                                        @php
                                            $numericIndex = (int) $arrayIndex;
                                        @endphp

                                        <div class="value-item flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span
                                                    class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ $numericIndex + 1 }}</span>
                                            </div>
                                            <input type="text" name="values[{{ $numericIndex }}]"
                                                value="{{ $currentValue }}"
                                                placeholder="e.g., Quality, Integrity, Innovation"
                                                class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                            @if (count($formValues) > 1)
                                                <button type="button" onclick="removeValue(this)"
                                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.company.seo') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        SEO Settings
                    </a>

                    <a href="{{ route('admin.company.certificates') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Certificates
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Last updated:
                        {{ $companyProfile->updated_at ? $companyProfile->updated_at->diffForHumans() : 'Never' }}
                    </span>

                    <div class="flex items-center gap-2">
                        <x-admin.button href="{{ route('admin.company.index') }}" color="secondary" size="sm">
                            Cancel
                        </x-admin.button>
                        <x-admin.button type="submit" color="primary" size="sm">
                            Save Changes
                        </x-admin.button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // Tab functionality
            document.addEventListener('DOMContentLoaded', function() {
                const tabButtons = document.querySelectorAll('.tab-button');
                const tabContents = document.querySelectorAll('.tab-content');

                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const targetTab = button.dataset.tab;

                        // Update buttons
                        tabButtons.forEach(btn => {
                            btn.classList.remove('active', 'border-blue-500', 'text-blue-600',
                                'dark:text-blue-400');
                            btn.classList.add('border-transparent', 'text-gray-500',
                                'hover:text-gray-700', 'dark:text-gray-400',
                                'dark:hover:text-gray-300');
                        });

                        button.classList.remove('border-transparent', 'text-gray-500',
                            'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                        button.classList.add('active', 'border-blue-500', 'text-blue-600',
                            'dark:text-blue-400');

                        // Update content
                        tabContents.forEach(content => {
                            content.classList.add('hidden');
                            content.classList.remove('active');
                        });

                        const targetContent = document.getElementById(targetTab + '-tab');
                        if (targetContent) {
                            targetContent.classList.remove('hidden');
                            targetContent.classList.add('active');
                        }
                    });
                });

                // Character counter for about field
                const aboutField = document.querySelector('textarea[name="about"]');
                const counter = document.getElementById('about-count');

                if (aboutField && counter) {
                    aboutField.addEventListener('input', function() {
                        const length = this.value.length;
                        counter.textContent = length;

                        if (length > 450) {
                            counter.style.color = '#EF4444';
                        } else if (length > 400) {
                            counter.style.color = '#F59E0B';
                        } else {
                            counter.style.color = '';
                        }
                    });
                }

                // Auto-save indication (mock)
                let saveTimeout;
                const form = document.getElementById('profile-form');
                const inputs = form.querySelectorAll('input, textarea, select');

                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        clearTimeout(saveTimeout);
                        saveTimeout = setTimeout(() => {
                            // Mock auto-save
                            console.log('Auto-saving...');
                        }, 2000);
                    });
                });
            });

            // Values management
            function addValue() {
                const container = document.getElementById('values-container');
                const currentCount = container.querySelectorAll('.value-item').length;
                const newIndex = currentCount;

                const newValueHtml = `
                <div class="value-item flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400">${newIndex + 1}</span>
                    </div>
                    <input type="text" 
                           name="values[${newIndex}]" 
                           placeholder="e.g., Quality, Integrity, Innovation"
                           class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <button type="button" 
                            onclick="removeValue(this)" 
                            class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;

                container.insertAdjacentHTML('beforeend', newValueHtml);
                updateValueNumbers();
            }

            function removeValue(button) {
                const valueItems = document.querySelectorAll('.value-item');
                if (valueItems.length > 1) {
                    button.closest('.value-item').remove();
                    updateValueNumbers();
                }
            }

            function updateValueNumbers() {
                const valueItems = document.querySelectorAll('.value-item');
                valueItems.forEach((item, index) => {
                    const numberSpan = item.querySelector('.text-blue-600, .text-blue-400');
                    const input = item.querySelector('input');

                    if (numberSpan) {
                        numberSpan.textContent = index + 1;
                    }
                    if (input) {
                        input.name = `values[${index}]`;
                    }
                });
            }

            // Logo preview
            document.querySelector('input[name="logo"]')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.querySelector('img[alt="Company Logo"]');
                        if (img) {
                            img.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>
    @endpush
</x-layouts.admin>
