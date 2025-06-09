{{-- resources/views/admin/users/change-password.blade.php --}}
<x-layouts.admin title="Change Password - {{ $user->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Users Management' => route('admin.users.index'),
        $user->name => route('admin.users.profile.show', $user),
        'Change Password' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Change Password</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">
                Update the password for {{ $user->name }}
                @if($user->id === auth()->id())
                    <x-admin.badge type="info" size="sm" class="ml-2">Your Account</x-admin.badge>
                @endif
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <x-admin.button 
                href="{{ route('admin.users.profile.show', $user) }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
            >
                View Profile
            </x-admin.button>
            <x-admin.button 
                href="{{ route('admin.users.index') }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
            >
                Back to Users
            </x-admin.button>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <!-- User Info Card -->
        <x-admin.card class="mb-6">
            <div class="flex items-center space-x-4">
                <x-admin.avatar 
                    :src="$user->avatar_url" 
                    :alt="$user->name"
                    size="md"
                />
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $user->email }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        @foreach($user->roles as $role)
                            <x-admin.badge :type="$role->badge_color ?? 'primary'" size="sm">
                                {{ $role->formatted_name }}
                            </x-admin.badge>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- Change Password Form -->
        <x-admin.form-section 
            title="New Password"
            description="Set a new secure password for this user account"
        >
            <form action="{{ route('admin.users.password.update', $user) }}" method="POST" x-data="passwordForm()">
                @csrf
                
                @if($user->id === auth()->id())
                <x-admin.alert type="warning" class="mb-6">
                    <strong>Security Notice:</strong> You are changing your own password. You will remain logged in after the change.
                </x-admin.alert>
                @endif

                <div class="space-y-6">
                    <x-admin.input
                        label="New Password"
                        name="password"
                        type="password"
                        required
                        placeholder="Enter new password"
                        helper="Password must be at least 8 characters long with letters and numbers"
                        x-model="password"
                        x-on:input="checkPasswordStrength()"
                    />

                    <!-- Password Strength Indicator -->
                    <div x-show="password.length > 0" class="space-y-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Password Strength</label>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-neutral-700">
                            <div class="h-2 rounded-full transition-all duration-300" 
                                 x-bind:class="strengthColor" 
                                 x-bind:style="`width: ${strengthPercentage}%`">
                            </div>
                        </div>
                        <p class="text-xs" x-bind:class="strengthTextColor" x-text="strengthText"></p>
                    </div>

                    <x-admin.input
                        label="Confirm New Password"
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="Confirm new password"
                        helper="Must match the password above"
                        x-model="confirmPassword"
                        x-on:input="checkPasswordMatch()"
                    />

                    <!-- Password Match Indicator -->
                    <div x-show="confirmPassword.length > 0" class="flex items-center space-x-2">
                        <div x-show="passwordsMatch" class="flex items-center text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm">Passwords match</span>
                        </div>
                        <div x-show="!passwordsMatch" class="flex items-center text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm">Passwords do not match</span>
                        </div>
                    </div>

                    <!-- Security Recommendations -->
                    <x-admin.help-text type="info" title="Password Security Tips">
                        <ul class="text-sm space-y-1 mt-2">
                            <li>• Use at least 8 characters</li>
                            <li>• Include uppercase and lowercase letters</li>
                            <li>• Include numbers and special characters</li>
                            <li>• Avoid common words or personal information</li>
                            <li>• Don't reuse passwords from other accounts</li>
                        </ul>
                    </x-admin.help-text>
                    
                    <!-- Password History Info -->
                    @if($user->last_login_at)
                    <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4 text-sm">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium text-gray-700 dark:text-neutral-300">Account Information</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600 dark:text-neutral-400">
                            <div>
                                <span class="font-medium">Last Login:</span> {{ $user->last_login_at->format('M d, Y g:i A') }}
                            </div>
                            <div>
                                <span class="font-medium">Login Count:</span> {{ $user->login_count ?? 0 }} times
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end space-x-3">
                        <x-admin.button 
                            type="button" 
                            color="light" 
                            onclick="history.back()"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                        >
                            Cancel
                        </x-admin.button>
                        
                        <x-admin.button 
                            type="submit" 
                            color="primary"
                            x-bind:disabled="!canSubmit"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1221 9z"/>'
                        >
                            <span x-show="!canSubmit">Set Requirements First</span>
                            <span x-show="canSubmit">Change Password</span>
                        </x-admin.button>
                    </div>
                </x-slot>
            </form>
        </x-admin.form-section>
    </div>

    @push('scripts')
    <script>
        function passwordForm() {
            return {
                password: '',
                confirmPassword: '',
                strengthPercentage: 0,
                strengthText: '',
                strengthColor: 'bg-gray-300',
                strengthTextColor: 'text-gray-500',
                passwordsMatch: false,
                
                get canSubmit() {
                    return this.password.length >= 8 && 
                           this.confirmPassword.length > 0 && 
                           this.passwordsMatch && 
                           this.strengthPercentage >= 50;
                },
                
                checkPasswordStrength() {
                    let score = 0;
                    let feedback = [];
                    
                    // Length check
                    if (this.password.length >= 8) score += 20;
                    else feedback.push('at least 8 characters');
                    
                    // Uppercase check
                    if (/[A-Z]/.test(this.password)) score += 20;
                    else feedback.push('uppercase letter');
                    
                    // Lowercase check
                    if (/[a-z]/.test(this.password)) score += 20;
                    else feedback.push('lowercase letter');
                    
                    // Number check
                    if (/[0-9]/.test(this.password)) score += 20;
                    else feedback.push('number');
                    
                    // Special character check
                    if (/[^A-Za-z0-9]/.test(this.password)) score += 20;
                    else feedback.push('special character');
                    
                    this.strengthPercentage = score;
                    
                    if (score < 40) {
                        this.strengthText = 'Weak - Add: ' + feedback.slice(0, 2).join(', ');
                        this.strengthColor = 'bg-red-500';
                        this.strengthTextColor = 'text-red-600 dark:text-red-400';
                    } else if (score < 80) {
                        this.strengthText = 'Medium - Add: ' + feedback.slice(0, 1).join(', ');
                        this.strengthColor = 'bg-amber-500';
                        this.strengthTextColor = 'text-amber-600 dark:text-amber-400';
                    } else {
                        this.strengthText = 'Strong';
                        this.strengthColor = 'bg-green-500';
                        this.strengthTextColor = 'text-green-600 dark:text-green-400';
                    }
                    
                    this.checkPasswordMatch();
                },
                
                checkPasswordMatch() {
                    this.passwordsMatch = this.password === this.confirmPassword && this.confirmPassword.length > 0;
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>