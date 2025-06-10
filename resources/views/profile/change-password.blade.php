{{-- resources/views/profile/change-password.blade.php --}}
<x-dynamic-component :component="$layout" title="Change Password">
    <div class="py-2">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Change Your Password</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Update your account password for security
                    </p>
                </div>
                <x-admin.button 
                    href="{{ route('profile.show') }}" 
                    color="light"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                >
                    Back to Profile
                </x-admin.button>
            </div>

            <!-- Security Notices -->
            <x-admin.alert type="info" class="mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-400">Security Notice</h4>
                        <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                            <p>You will remain logged in after changing your password. Other sessions will be logged out for security.</p>
                        </div>
                    </div>
                </div>
            </x-admin.alert>

            <!-- Change Password Form -->
            <x-admin.form-section 
                title="New Password"
                description="Enter your current password and choose a new secure password"
            >
                <form action="{{ route('profile.password.update') }}" 
                      method="POST" 
                      x-data="passwordForm()">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-6">
                        <x-admin.input
                            label="Current Password"
                            name="current_password"
                            type="password"
                            required
                            placeholder="Enter your current password"
                            helper="Your existing password for verification"
                            x-ref="currentPassword"
                        />

                        <x-admin.input
                            label="New Password"
                            name="password"
                            type="password"
                            required
                            placeholder="Enter new password"
                            helper="Password must be at least 8 characters long with letters and numbers"
                            x-model="password"
                            x-on:input="checkPasswordStrength()"
                            x-ref="newPassword"
                        />

                        <!-- Password Strength Indicator -->
                        <div x-show="password.length > 0" x-transition class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Password Strength</label>
                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
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
                        <div x-show="confirmPassword.length > 0" x-transition class="flex items-center space-x-2">
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
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-400">Password Security Tips</h4>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        <ul class="space-y-1">
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Use at least 8 characters
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Include uppercase and lowercase letters
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Include numbers and special characters
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Avoid common words or personal information
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Don't reuse passwords from other accounts
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account History Info -->
                        @if($user->last_login_at ?? false)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-sm">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Account Information</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600 dark:text-gray-400">
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

                    <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
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
                            <span x-show="!canSubmit">Complete Password Requirements</span>
                            <span x-show="canSubmit">Change Password</span>
                        </x-admin.button>
                    </div>
                </form>
            </x-admin.form-section>
        </div>
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
                    const currentPassword = this.$refs.currentPassword?.value || '';
                    return this.password.length >= 8 && 
                           this.confirmPassword.length > 0 && 
                           this.passwordsMatch && 
                           this.strengthPercentage >= 50 &&
                           currentPassword.length > 0;
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
        
        // Enhanced form security
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const passwordInputs = form.querySelectorAll('input[type="password"]');
            
            // Disable autocomplete on password fields
            passwordInputs.forEach(input => {
                input.setAttribute('autocomplete', 'new-password');
            });
            
            // Clear password fields on page visibility change (security feature)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    passwordInputs.forEach(input => {
                        if (input.name !== 'current_password') {
                            input.value = '';
                        }
                    });
                }
            });
            
            // Prevent form submission with weak passwords
            form.addEventListener('submit', function(e) {
                const passwordInput = form.querySelector('input[name="password"]');
                const confirmInput = form.querySelector('input[name="password_confirmation"]');
                
                if (passwordInput.value.length < 8) {
                    e.preventDefault();
                    showMessage('Password must be at least 8 characters long', 'error');
                    return;
                }
                
                if (passwordInput.value !== confirmInput.value) {
                    e.preventDefault();
                    showMessage('Passwords do not match', 'error');
                    return;
                }
                
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Changing Password...
                `;
                
                // Reset button after 10 seconds (fallback)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }, 10000);
            });
        });
        
        function showMessage(message, type = 'info') {
            const alertClass = type === 'error' ? 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400' : 
                              type === 'success' ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400' :
                              'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400';
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg p-4 border ${alertClass} transform transition-all duration-300 ease-in-out`;
            messageDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            setTimeout(() => messageDiv?.remove(), 5000);
        }
    </script>
    @endpush
</x-dynamic-component>