<!-- resources/views/profile/edit.blade.php -->
<x-layouts.app title="Edit Profile">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Update your personal information and preferences</p>
                </div>
                <x-admin.button 
                    href="{{ route('profile.show') }}" 
                    color="light"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                >
                    Back to Profile
                </x-admin.button>
            </div>

            <!-- Profile Completion Alert -->
            @if($completion['essential_percentage'] < 100)
            <x-admin.alert type="info" class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium">Complete Your Profile</h4>
                        <p class="text-sm mt-1">
                            You're {{ $completion['essential_percentage'] }}% complete. 
                            Fill in the remaining {{ count($completion['missing_essential']) }} essential field(s) to unlock all features.
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

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Profile Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <x-admin.form-section 
                            title="Basic Information"
                            description="Your essential profile details"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-admin.input
                                        label="Full Name"
                                        name="name"
                                        :value="old('name', $user->name)"
                                        required
                                        placeholder="Enter your full name"
                                        helper="This name will be displayed across the platform"
                                        :class="in_array('name', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                    />
                                </div>

                                <x-admin.input
                                    label="Email Address"
                                    name="email"
                                    type="email"
                                    :value="old('email', $user->email)"
                                    required
                                    placeholder="your@email.com"
                                    helper="Used for login and important notifications"
                                />

                                <x-admin.input
                                    label="Phone Number"
                                    name="phone"
                                    type="tel"
                                    :value="old('phone', $user->phone)"
                                    placeholder="+1 (555) 123-4567"
                                    helper="For urgent communications"
                                    :class="in_array('phone', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                />
                            </div>
                        </x-admin.form-section>

                        <!-- Professional Information -->
                        <x-admin.form-section 
                            title="Professional Information"
                            description="Your work and company details"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-admin.input
                                    label="Company"
                                    name="company"
                                    :value="old('company', $user->company)"
                                    placeholder="Your company name"
                                    helper="Organization you represent"
                                    :class="in_array('company', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                />

                                <x-admin.input
                                    label="Position/Title"
                                    name="position"
                                    :value="old('position', $user->position)"
                                    placeholder="e.g., Project Manager"
                                    helper="Your role or job title"
                                />

                                <div class="md:col-span-2">
                                    <x-admin.input
                                        label="Website"
                                        name="website"
                                        type="url"
                                        :value="old('website', $user->website)"
                                        placeholder="https://yourwebsite.com"
                                        helper="Your personal or company website"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <x-admin.textarea
                                        label="Biography"
                                        name="bio"
                                        :value="old('bio', $user->bio)"
                                        rows="4"
                                        placeholder="Tell us about yourself, your expertise, and what you do..."
                                        helper="Brief description about yourself (max 1000 characters)"
                                    />
                                </div>
                            </div>
                        </x-admin.form-section>

                        <!-- Address Information -->
                        <x-admin.form-section 
                            title="Address Information"
                            description="Your location and contact details"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-admin.textarea
                                        label="Address"
                                        name="address"
                                        :value="old('address', $user->address)"
                                        rows="3"
                                        placeholder="Street address, building number, etc."
                                        helper="Your physical address"
                                        :class="in_array('address', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                    />
                                </div>

                                <x-admin.input
                                    label="City"
                                    name="city"
                                    :value="old('city', $user->city)"
                                    placeholder="Your city"
                                    :class="in_array('city', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                />

                                <x-admin.input
                                    label="State/Province"
                                    name="state"
                                    :value="old('state', $user->state)"
                                    placeholder="State or province"
                                    :class="in_array('state', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                />

                                <x-admin.input
                                    label="Postal Code"
                                    name="postal_code"
                                    :value="old('postal_code', $user->postal_code)"
                                    placeholder="ZIP or postal code"
                                />

                                <x-admin.input
                                    label="Country"
                                    name="country"
                                    :value="old('country', $user->country)"
                                    placeholder="Your country"
                                    :class="in_array('country', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                />
                            </div>
                        </x-admin.form-section>

                        <!-- Privacy Settings -->
                        <x-admin.form-section 
                            title="Privacy Settings"
                            description="Control how your information is shared"
                        >
                            <div class="space-y-4">
                                <x-admin.checkbox
                                    label="Allow testimonials to be displayed publicly"
                                    name="allow_testimonials"
                                    :checked="old('allow_testimonials', $user->allow_testimonials ?? true)"
                                    helper="Let us showcase your testimonials on our website"
                                />

                                <x-admin.checkbox
                                    label="Make profile public"
                                    name="allow_public_profile"
                                    :checked="old('allow_public_profile', $user->allow_public_profile ?? false)"
                                    helper="Allow your profile to be visible to other users"
                                />
                            </div>
                        </x-admin.form-section>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Profile Picture -->
                        <x-admin.card title="Profile Picture">
                            <div class="text-center">
                                <x-admin.avatar 
                                    :src="$user->avatar_url" 
                                    :alt="$user->name"
                                    size="xl"
                                    class="mx-auto mb-4"
                                />
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
                                        :class="in_array('avatar', $completion['missing_essential']) ? 'border-amber-300' : ''"
                                    />
                                    
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <p>• Recommended size: 400x400px</p>
                                        <p>• Square images work best</p>
                                        <p>• Professional photos recommended</p>
                                    </div>
                                </div>
                            </div>
                        </x-admin.card>

                        <!-- Profile Completion -->
                        <x-admin.card title="Profile Completion">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Essential Fields</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $completion['completed_essential'] }}/{{ $completion['total_essential'] }}
                                    </span>
                                </div>
                                <x-admin.progress 
                                    :value="$completion['essential_percentage']" 
                                    color="blue"
                                    showLabel="true"
                                    labelPosition="outside-right"
                                />
                                
                                @if(count($completion['missing_essential']) > 0)
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-amber-700 dark:text-amber-400">Missing Essential Fields:</h4>
                                    <ul class="text-xs text-amber-600 dark:text-amber-500 space-y-1">
                                        @foreach($completion['missing_essential'] as $field)
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $field)) }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Completion</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $completion['completed_all'] }}/{{ $completion['total_all'] }}
                                    </span>
                                </div>
                                <x-admin.progress 
                                    :value="$completion['overall_percentage']" 
                                    color="green"
                                    showLabel="true"
                                    labelPosition="outside-right"
                                />
                            </div>
                        </x-admin.card>

                        <!-- Account Status -->
                        <x-admin.card title="Account Status">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Email Status</span>
                                    @if($user->email_verified_at)
                                        <x-admin.badge type="success" size="sm">Verified</x-admin.badge>
                                    @else
                                        <x-admin.badge type="warning" size="sm">Unverified</x-admin.badge>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Account Status</span>
                                    @if($user->is_active)
                                        <x-admin.badge type="success" size="sm">Active</x-admin.badge>
                                    @else
                                        <x-admin.badge type="danger" size="sm">Inactive</x-admin.badge>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('M Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $user->updated_at->diffForHumans() }}</span>
                                </div>
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
                                >
                                    Update Profile
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
        // Auto-save draft functionality (optional)
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, textarea, select');
            
            // Auto-save to localStorage every 30 seconds
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

            // Preview uploaded avatar
            const avatarInput = form.querySelector('input[name="avatar"]');
            if (avatarInput) {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const avatarImg = document.querySelector('.avatar-preview img, .mx-auto img');
                            if (avatarImg) {
                                avatarImg.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>