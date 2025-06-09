{{-- resources/views/profile/edit.blade.php --}}
@php
    $isOwnProfile = !isset($user) || $user->id === auth()->id();
    $pageTitle = $isOwnProfile ? 'Edit Profile' : 'Edit User Profile: ' . $user->name;
    $layout = $isOwnProfile ? 'layouts.client' : 'layouts.admin';
@endphp

<x-dynamic-component :component="$layout" :title="$pageTitle">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $isOwnProfile ? 'Edit Your Profile' : 'Edit User Profile' }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $isOwnProfile ? 'Update your personal information and preferences' : 'Manage user account information and settings' }}
                    </p>
                    @if(!$isOwnProfile)
                        <div class="flex items-center space-x-2 mt-2">
                            <x-admin.badge type="info" size="sm">Admin Edit</x-admin.badge>
                            <span class="text-xs text-gray-500">Editing: {{ $user->name }}</span>
                        </div>
                    @endif
                </div>
                <x-admin.button 
                    href="{{ $isOwnProfile ? route('profile.show') : route('admin.users.profile.show', $user) }}" 
                    color="light"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                >
                    {{ $isOwnProfile ? 'Back to Profile' : 'Back to User Profile' }}
                </x-admin.button>
            </div>

            <!-- Profile Completion Alert -->
            @if(isset($completion) && $completion['essential_percentage'] < 100)
            <x-admin.alert type="info" class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium">Complete Your Profile</h4>
                        <p class="text-sm mt-1">
                            You're {{ $completion['essential_percentage'] }}% complete. 
                            Fill in the remaining {{ count($completion['missing_essential'] ?? []) }} essential field(s) to unlock all features.
                        </p>
                    </div>
                    <div class="ml-4">
                        <x-admin.progress 
                            :value="$completion['essential_percentage']" 
                            color="blue"
                            height="sm"
                            class="w-24"
                        />
                    </div>
                </div>
            </x-admin.alert>
            @endif

            <!-- Admin-specific alerts -->
            @if(!$isOwnProfile)
                @if($user->id === 1)
                <x-admin.alert type="warning" class="mb-6">
                    <strong>System User:</strong> Be careful when editing system user accounts.
                </x-admin.alert>
                @endif

                @if($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin'))
                <x-admin.alert type="danger" class="mb-6">
                    <strong>Restricted:</strong> You cannot edit super-admin users.
                </x-admin.alert>
                @endif
            @endif

            <form action="{{ $isOwnProfile ? route('profile.update') : route('admin.users.profile.update', $user) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  x-data="profileForm({{ json_encode($completion ?? []) }})">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Profile Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <x-admin.form-section 
                            title="Basic Information"
                            description="Essential profile details and contact information"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-admin.input
                                        label="Full Name"
                                        name="name"
                                        :value="old('name', $user->name ?? '')"
                                        required
                                        placeholder="Enter full name"
                                        helper="This name will be displayed across the platform"
                                        x-ref="nameField"
                                        x-on:input="updateCompletion('name', $event.target.value)"
                                        :class="isset($completion) && in_array('name', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                    />
                                </div>

                                <x-admin.input
                                    label="Email Address"
                                    name="email"
                                    type="email"
                                    :value="old('email', $user->email ?? '')"
                                    required
                                    placeholder="user@example.com"
                                    helper="Used for login and important notifications"
                                    x-on:input="updateCompletion('email', $event.target.value)"
                                />

                                <x-admin.input
                                    label="Phone Number"
                                    name="phone"
                                    type="tel"
                                    :value="old('phone', $user->phone ?? '')"
                                    placeholder="+1 (555) 123-4567"
                                    helper="For urgent communications and support"
                                    x-on:input="updateCompletion('phone', $event.target.value)"
                                    :class="isset($completion) && in_array('phone', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                />
                            </div>
                        </x-admin.form-section>

                        <!-- Professional Information -->
                        <x-admin.form-section 
                            title="Professional Information"
                            description="Work-related details and company information"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-admin.input
                                    label="Company"
                                    name="company"
                                    :value="old('company', $user->company ?? '')"
                                    placeholder="Your company name"
                                    helper="Organization you represent"
                                    x-on:input="updateCompletion('company', $event.target.value)"
                                    :class="isset($completion) && in_array('company', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                />

                                <x-admin.input
                                    label="Position/Title"
                                    name="position"
                                    :value="old('position', $user->position ?? '')"
                                    placeholder="e.g., Project Manager, CEO"
                                    helper="Your role or job title"
                                    x-on:input="updateCompletion('position', $event.target.value)"
                                />

                                <div class="md:col-span-2">
                                    <x-admin.input
                                        label="Website"
                                        name="website"
                                        type="url"
                                        :value="old('website', $user->website ?? '')"
                                        placeholder="https://yourwebsite.com"
                                        helper="Your personal or company website"
                                        x-on:input="updateCompletion('website', $event.target.value)"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <x-admin.textarea
                                        label="Biography"
                                        name="bio"
                                        :value="old('bio', $user->bio ?? '')"
                                        rows="4"
                                        placeholder="Tell us about yourself, your expertise, and what you do..."
                                        helper="Brief description about yourself (max 1000 characters)"
                                        x-on:input="updateCompletion('bio', $event.target.value); updateCharCount($event.target.value)"
                                    />
                                    <div class="text-xs text-gray-500 mt-1" x-text="`${charCount}/1000 characters`"></div>
                                </div>
                            </div>
                        </x-admin.form-section>

                        <!-- Address Information -->
                        <x-admin.form-section 
                            title="Address Information"
                            description="Location and contact details"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-admin.textarea
                                        label="Street Address"
                                        name="address"
                                        :value="old('address', $user->address ?? '')"
                                        rows="3"
                                        placeholder="Street address, building number, suite, etc."
                                        helper="Your physical address"
                                        x-on:input="updateCompletion('address', $event.target.value)"
                                        :class="isset($completion) && in_array('address', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                    />
                                </div>

                                <x-admin.input
                                    label="City"
                                    name="city"
                                    :value="old('city', $user->city ?? '')"
                                    placeholder="Your city"
                                    x-on:input="updateCompletion('city', $event.target.value)"
                                    :class="isset($completion) && in_array('city', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                />

                                <x-admin.input
                                    label="State/Province"
                                    name="state"
                                    :value="old('state', $user->state ?? '')"
                                    placeholder="State or province"
                                    x-on:input="updateCompletion('state', $event.target.value)"
                                    :class="isset($completion) && in_array('state', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                />

                                <x-admin.input
                                    label="Postal Code"
                                    name="postal_code"
                                    :value="old('postal_code', $user->postal_code ?? '')"
                                    placeholder="ZIP or postal code"
                                    x-on:input="updateCompletion('postal_code', $event.target.value)"
                                />

                                <x-admin.input
                                    label="Country"
                                    name="country"
                                    :value="old('country', $user->country ?? '')"
                                    placeholder="Your country"
                                    x-on:input="updateCompletion('country', $event.target.value)"
                                    :class="isset($completion) && in_array('country', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                />
                            </div>
                        </x-admin.form-section>

                        <!-- Privacy & Preferences -->
                        <x-admin.form-section 
                            title="Privacy & Preferences"
                            description="Control how your information is shared and displayed"
                        >
                            <div class="space-y-4">
                                <x-admin.checkbox
                                    label="Allow testimonials to be displayed publicly"
                                    name="allow_testimonials"
                                    :checked="old('allow_testimonials', $user->allow_testimonials ?? true)"
                                    helper="Let us showcase your testimonials and reviews on our website"
                                />

                                <x-admin.checkbox
                                    label="Make profile public"
                                    name="allow_public_profile"
                                    :checked="old('allow_public_profile', $user->allow_public_profile ?? false)"
                                    helper="Allow your profile to be visible to other users and in search results"
                                />

                                @if($isOwnProfile)
                                <x-admin.checkbox
                                    label="Receive marketing emails"
                                    name="marketing_notifications"
                                    :checked="old('marketing_notifications', $user->marketing_notifications ?? false)"
                                    helper="Get updates about new features, tips, and relevant industry news"
                                />
                                @endif
                            </div>
                        </x-admin.form-section>

                        <!-- Admin-only sections -->
                        @if(!$isOwnProfile && auth()->user()->hasAdminAccess())
                        <x-admin.form-section 
                            title="Admin Settings"
                            description="Administrative controls and user status"
                        >
                            <div class="space-y-4">
                                <x-admin.checkbox
                                    label="Account Active"
                                    name="is_active"
                                    :checked="old('is_active', $user->is_active ?? true)"
                                    helper="Active users can log in and access the system"
                                />

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Account Information</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 text-sm">
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Created:</span>
                                            <span class="text-gray-900 dark:text-white ml-2">{{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Last Login:</span>
                                            <span class="text-gray-900 dark:text-white ml-2">
                                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y g:i A') : 'Never' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Email Status:</span>
                                            @if($user->email_verified_at)
                                                <x-admin.badge type="success" size="sm" class="ml-2">Verified</x-admin.badge>
                                            @else
                                                <x-admin.badge type="warning" size="sm" class="ml-2">Unverified</x-admin.badge>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-gray-600 dark:text-gray-400">Login Count:</span>
                                            <span class="text-gray-900 dark:text-white ml-2">{{ $user->login_count ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-admin.form-section>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Profile Picture -->
                        <x-admin.card title="Profile Picture">
                            <div class="text-center">
                                <div class="relative inline-block">
                                    <x-admin.avatar 
                                        :src="($user->avatar ?? '') ? asset('storage/' . $user->avatar) : ''" 
                                        :alt="$user->name ?? 'User'"
                                        size="xl"
                                        class="mx-auto mb-4"
                                        x-ref="avatarPreview"
                                    />
                                    @if(isset($completion) && in_array('avatar', $completion['missing_essential'] ?? []))
                                    <div class="absolute -top-2 -right-2 bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded-full">
                                        Missing
                                    </div>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Current profile picture
                                </p>
                                
                                <div class="space-y-4">
                                    <x-admin.input
                                        type="file"
                                        name="avatar"
                                        label="Upload New Picture"
                                        accept="image/jpeg,image/png,image/jpg,image/gif"
                                        helper="JPEG, PNG, JPG or GIF. Max size: 2MB"
                                        x-on:change="previewAvatar($event)"
                                        :class="isset($completion) && in_array('avatar', $completion['missing_essential'] ?? []) ? 'border-amber-300 focus:border-amber-500' : ''"
                                    />
                                    
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <p>• Recommended size: 400x400px</p>
                                        <p>• Square images work best</p>
                                        <p>• Professional photos recommended</p>
                                    </div>
                                </div>
                            </div>
                        </x-admin.card>

                        <!-- Real-time Profile Completion -->
                        @if(isset($completion))
                        <x-admin.card title="Profile Completion" x-show="showCompletion">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Essential Fields</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="`${completedEssential}/${totalEssential}`">
                                        {{ $completion['completed_essential'] ?? 0 }}/{{ $completion['total_essential'] ?? 9 }}
                                    </span>
                                </div>
                                <x-admin.progress 
                                    x-bind:value="essentialPercentage" 
                                    color="blue"
                                    showLabel="true"
                                    labelPosition="outside-right"
                                />
                                
                                <div x-show="missingFields.length > 0" class="space-y-2">
                                    <h4 class="text-sm font-medium text-amber-700 dark:text-amber-400">Missing Essential Fields:</h4>
                                    <ul class="text-xs text-amber-600 dark:text-amber-500 space-y-1">
                                        <template x-for="field in missingFields" :key="field">
                                            <li class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                <span x-text="formatFieldName(field)"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Progress</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400" x-text="`${completedAll}/${totalAll}`">
                                            {{ $completion['completed_all'] ?? 0 }}/{{ $completion['total_all'] ?? 13 }}
                                        </span>
                                    </div>
                                    <x-admin.progress 
                                        x-bind:value="overallPercentage" 
                                        color="green"
                                        showLabel="true"
                                        labelPosition="outside-right"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </x-admin.card>
                        @endif

                        <!-- Quick Actions -->
                        <x-admin.card title="Quick Actions">
                            <div class="space-y-3">
                                <x-admin.button 
                                    href="{{ $isOwnProfile ? route('profile.change-password') : route('admin.users.profile.password', $user) }}" 
                                    color="warning" 
                                    class="w-full"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'
                                >
                                    Change Password
                                </x-admin.button>
                                
                                <x-admin.button 
                                    href="{{ $isOwnProfile ? route('profile.preferences') : route('admin.users.profile.preferences', $user) }}" 
                                    color="info" 
                                    class="w-full"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                                >
                                    {{ $isOwnProfile ? 'Notification Settings' : 'User Preferences' }}
                                </x-admin.button>

                                @if(!$isOwnProfile && auth()->user()->hasAdminAccess())
                                <x-admin.button 
                                    href="{{ route('admin.users.roles', $user) }}" 
                                    color="purple" 
                                    class="w-full"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
                                >
                                    Manage Roles
                                </x-admin.button>
                                @endif
                            </div>
                        </x-admin.card>

                        <!-- Form Actions -->
                        <x-admin.card>
                            <div class="flex flex-col space-y-3">
                                <x-admin.button 
                                    type="submit" 
                                    color="primary" 
                                    class="w-full"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'
                                    x-bind:disabled="!isFormValid"
                                >
                                    <span x-show="!isFormValid">Complete Required Fields</span>
                                    <span x-show="isFormValid">{{ $isOwnProfile ? 'Update Profile' : 'Update User Profile' }}</span>
                                </x-admin.button>
                                
                                <x-admin.button 
                                    type="button" 
                                    color="light" 
                                    class="w-full"
                                    onclick="history.back()"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                                >
                                    Cancel
                                </x-admin.button>
                            </div>
                        </x-admin.card>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function profileForm(initialCompletion) {
            return {
                // Profile completion tracking
                completion: initialCompletion || {},
                charCount: 0,
                
                // Form validation
                isFormValid: true,
                
                // Essential fields tracking
                essentialFields: ['name', 'email', 'phone', 'company', 'address', 'city', 'state', 'country', 'avatar'],
                allFields: ['name', 'email', 'phone', 'company', 'address', 'city', 'state', 'country', 'avatar', 'postal_code', 'bio', 'website', 'position'],
                
                init() {
                    this.updateCharCount(this.$refs.bioField?.value || '');
                    this.calculateCompletion();
                },
                
                get completedEssential() {
                    return this.essentialFields.filter(field => this.completion.fields_status?.[field]).length;
                },
                
                get totalEssential() {
                    return this.essentialFields.length;
                },
                
                get completedAll() {
                    return this.allFields.filter(field => this.completion.fields_status?.[field]).length;
                },
                
                get totalAll() {
                    return this.allFields.length;
                },
                
                get essentialPercentage() {
                    return Math.round((this.completedEssential / this.totalEssential) * 100);
                },
                
                get overallPercentage() {
                    return Math.round((this.completedAll / this.totalAll) * 100);
                },
                
                get missingFields() {
                    return this.essentialFields.filter(field => !this.completion.fields_status?.[field]);
                },
                
                get showCompletion() {
                    return this.essentialPercentage < 100;
                },
                
                updateCompletion(fieldName, value) {
                    if (!this.completion.fields_status) {
                        this.completion.fields_status = {};
                    }
                    
                    this.completion.fields_status[fieldName] = value && value.trim().length > 0;
                    this.calculateCompletion();
                },
                
                calculateCompletion() {
                    // Update form validation
                    const requiredFields = ['name', 'email'];
                    this.isFormValid = requiredFields.every(field => 
                        this.completion.fields_status?.[field] || false
                    );
                },
                
                updateCharCount(value) {
                    this.charCount = (value || '').length;
                },
                
                formatFieldName(field) {
                    return field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                },
                
                previewAvatar(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.$refs.avatarPreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                        
                        // Update completion status
                        this.updateCompletion('avatar', true);
                    }
                }
            }
        }
        
        // Auto-save draft functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const isOwnProfile = {{ $isOwnProfile ? 'true' : 'false' }};
            
            if (isOwnProfile) {
                // Auto-save to localStorage every 30 seconds for own profile
                setInterval(function() {
                    const formData = new FormData(form);
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        if (key !== '_token' && key !== '_method' && key !== 'avatar') {
                            data[key] = value;
                        }
                    }
                    localStorage.setItem('profile_draft', JSON.stringify(data));
                }, 30000);

                // Load draft on page load
                const savedDraft = localStorage.getItem('profile_draft');
                if (savedDraft) {
                    try {
                        const data = JSON.parse(savedDraft);
                        Object.keys(data).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input && !input.value) {
                                if (input.type === 'checkbox') {
                                    input.checked = data[key] === 'on';
                                } else {
                                    input.value = data[key];
                                }
                            }
                        });
                    } catch (e) {
                        console.warn('Could not load profile draft');
                    }
                }

                // Clear draft on successful submission
                form.addEventListener('submit', function() {
                    localStorage.removeItem('profile_draft');
                });
            }
            
            // Form validation and UX enhancements
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
            });
            
            function validateField(field) {
                const value = field.value.trim();
                const isRequired = field.hasAttribute('required');
                const fieldContainer = field.closest('.space-y-1, .space-y-2');
                
                // Remove existing validation messages
                const existingError = fieldContainer?.querySelector('.validation-error');
                if (existingError) {
                    existingError.remove();
                }
                
                if (isRequired && !value) {
                    showFieldError(field, 'This field is required');
                } else if (field.type === 'email' && value && !isValidEmail(value)) {
                    showFieldError(field, 'Please enter a valid email address');
                } else if (field.type === 'url' && value && !isValidUrl(value)) {
                    showFieldError(field, 'Please enter a valid URL');
                }
            }
            
            function showFieldError(field, message) {
                const fieldContainer = field.closest('.space-y-1, .space-y-2');
                if (fieldContainer) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error text-xs text-red-600 dark:text-red-400 mt-1';
                    errorDiv.textContent = message;
                    fieldContainer.appendChild(errorDiv);
                    
                    field.classList.add('border-red-300', 'focus:border-red-500');
                    setTimeout(() => {
                        field.classList.remove('border-red-300', 'focus:border-red-500');
                    }, 3000);
                }
            }
            
            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
            
            function isValidUrl(url) {
                try {
                    new URL(url);
                    return true;
                } catch {
                    return false;
                }
            }
        });
    </script>
    @endpush
</x-dynamic-component>