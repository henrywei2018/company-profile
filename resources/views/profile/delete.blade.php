{{-- resources/views/profile/delete.blade.php --}}

<x-dynamic-component :component="$layout" title="Delete Account">
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $isOwnProfile ? 'Delete Your Account' : 'Delete User Account' }}
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">
                    {{ $isOwnProfile ? 'This action cannot be undone and will permanently remove all your data' : 'This will permanently remove all user data and cannot be undone' }}
                </p>
                @if(!$isOwnProfile)
                    <div class="flex items-center justify-center space-x-2 mt-4">
                        <x-admin.badge type="danger" size="sm">Admin Action</x-admin.badge>
                        <span class="text-sm text-gray-500">Deleting account for: {{ $user->name }}</span>
                    </div>
                @endif
            </div>

            <!-- Warning Alert -->
            <x-admin.alert type="danger" class="mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-400">
                            {{ $isOwnProfile ? 'Your account will be permanently deleted' : 'This user account will be permanently deleted' }}
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>This action is irreversible. All associated data will be permanently removed from our systems and cannot be recovered.</p>
                        </div>
                    </div>
                </div>
            </x-admin.alert>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Data Summary -->
                <div class="space-y-6">
                    <x-admin.card title="Data That Will Be Deleted">
                        <div class="space-y-4">
                            @if(isset($dataSummary))
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $dataSummary['projects_count'] ?? 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Projects</div>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $dataSummary['quotations_count'] ?? 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Quotations</div>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $dataSummary['messages_count'] ?? 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Messages</div>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $dataSummary['posts_count'] ?? 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Posts</div>
                                </div>
                            </div>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Additional data to be removed:</h4>
                                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Profile information and personal data
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Uploaded files and documents
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Chat history and communication logs
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Account settings and preferences
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Activity logs and notifications
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Data Export Option -->
                    @if($isOwnProfile)
                    <x-admin.card title="Backup Your Data">
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Before deleting your account, you may want to download a copy of your data for your records.
                            </p>
                            
                            <x-admin.button 
                                href="{{ route('profile.export') }}" 
                                color="info" 
                                class="w-full"
                                target="_blank"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
                            >
                                Download My Data
                            </x-admin.button>
                            
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                This will download a JSON file containing all your account data and activity history.
                            </p>
                        </div>
                    </x-admin.card>
                    @endif
                </div>

                <!-- Deletion Confirmation -->
                <div class="space-y-6">
                    <x-admin.card title="Confirm Account Deletion">
                        <form action="{{ $isOwnProfile ? route('profile.destroy') : route('admin.users.destroy', $user) }}" 
                              method="POST" 
                              x-data="deletionForm({{ $isOwnProfile ? 'true' : 'false' }})"
                              x-on:submit="handleSubmit">
                            @csrf
                            @method('DELETE')
                            
                            <div class="space-y-6">
                                <!-- User Confirmation -->
                                @if($isOwnProfile)
                                <div>
                                    <x-admin.input
                                        label="Current Password"
                                        name="password"
                                        type="password"
                                        required
                                        placeholder="Enter your current password"
                                        helper="Verify your identity to proceed with account deletion"
                                        x-model="password"
                                    />
                                </div>
                                @else
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-amber-800 dark:text-amber-400">Admin Account Deletion</h4>
                                            <div class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                                                <p>You are about to permanently delete the account for <strong>{{ $user->name }}</strong> ({{ $user->email }}). This action will be logged for audit purposes.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Confirmation Text -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Type "{{ $isOwnProfile ? 'DELETE MY ACCOUNT' : 'DELETE USER ACCOUNT' }}" to confirm
                                    </label>
                                    <x-admin.input
                                        name="confirmation_text"
                                        :value="old('confirmation_text')"
                                        required
                                        placeholder="{{ $isOwnProfile ? 'DELETE MY ACCOUNT' : 'DELETE USER ACCOUNT' }}"
                                        helper="This confirms you understand the consequences of this action"
                                        x-model="confirmationText"
                                        x-on:input="checkConfirmation()"
                                        class="font-mono"
                                    />
                                </div>

                                <!-- Final Confirmation Checkbox -->
                                <div>
                                    <x-admin.checkbox
                                        label="I understand this action is permanent and irreversible"
                                        name="confirmation"
                                        required
                                        x-model="finalConfirmation"
                                        helper="Check this box to acknowledge you understand the permanent nature of this action"
                                    />
                                </div>

                                <!-- Optional Reason -->
                                <div>
                                    <x-admin.textarea
                                        label="Reason for deletion (optional)"
                                        name="reason"
                                        :value="old('reason')"
                                        rows="3"
                                        placeholder="{{ $isOwnProfile ? 'Let us know why you\'re leaving (optional feedback)' : 'Administrative reason for account deletion' }}"
                                        helper="This information helps us improve our service"
                                        x-model="reason"
                                    />
                                </div>

                                <!-- Deletion Status -->
                                <div x-show="isValidated" x-transition class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-medium text-red-800 dark:text-red-400">
                                            Ready to delete {{ $isOwnProfile ? 'your account' : 'user account' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
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
                                    color="danger"
                                    x-bind:disabled="!canDelete"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'
                                >
                                    <span x-show="!canDelete">Complete All Requirements</span>
                                    <span x-show="canDelete">{{ $isOwnProfile ? 'Delete My Account' : 'Delete User Account' }}</span>
                                </x-admin.button>
                            </div>
                        </form>
                    </x-admin.card>

                    <!-- Alternative Actions -->
                    @if($isOwnProfile)
                    <x-admin.card title="Alternative Options">
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Instead of deleting your account, you might consider:
                            </p>
                            
                            <div class="space-y-3">
                                <x-admin.button 
                                    href="{{ route('profile.preferences') }}" 
                                    color="light" 
                                    class="w-full justify-start"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>'
                                >
                                    <div class="text-left">
                                        <div class="font-medium">Disable notifications</div>
                                        <div class="text-xs text-gray-500">Turn off all email communications</div>
                                    </div>
                                </x-admin.button>
                                
                                <x-admin.button 
                                    href="mailto:support@example.com" 
                                    color="light" 
                                    class="w-full justify-start"
                                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'
                                >
                                    <div class="text-left">
                                        <div class="font-medium">Contact support</div>
                                        <div class="text-xs text-gray-500">Discuss account concerns with our team</div>
                                    </div>
                                </x-admin.button>
                            </div>
                        </div>
                    </x-admin.card>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function deletionForm(isOwnProfile) {
            return {
                password: '',
                confirmationText: '',
                finalConfirmation: false,
                reason: '',
                isValidated: false,
                isOwnProfile: isOwnProfile,
                
                get canDelete() {
                    const requiredText = this.isOwnProfile ? 'DELETE MY ACCOUNT' : 'DELETE USER ACCOUNT';
                    const textMatch = this.confirmationText.trim().toUpperCase() === requiredText;
                    const passwordValid = this.isOwnProfile ? this.password.length > 0 : true;
                    
                    return textMatch && this.finalConfirmation && passwordValid;
                },
                
                checkConfirmation() {
                    const requiredText = this.isOwnProfile ? 'DELETE MY ACCOUNT' : 'DELETE USER ACCOUNT';
                    this.isValidated = this.confirmationText.trim().toUpperCase() === requiredText && this.finalConfirmation;
                },
                
                handleSubmit(event) {
                    if (!this.canDelete) {
                        event.preventDefault();
                        return;
                    }
                    
                    // Final confirmation dialog
                    const confirmMessage = this.isOwnProfile 
                        ? 'Are you absolutely sure you want to delete your account? This action cannot be undone.'
                        : `Are you sure you want to delete the account for ${@json($user->name ?? '')}? This action cannot be undone.`;
                    
                    if (!confirm(confirmMessage)) {
                        event.preventDefault();
                        return;
                    }
                    
                    // Show loading state
                    const submitBtn = event.target.querySelector('button[type="submit"]');
                    const originalContent = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Deleting Account...
                    `;
                    
                    // Prevent multiple submissions
                    setTimeout(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalContent;
                        }
                    }, 10000);
                }
            }
        }
        
        // Enhanced security measures
        document.addEventListener('DOMContentLoaded', function() {
            // Disable right-click and keyboard shortcuts on sensitive form
            const form = document.querySelector('form');
            
            form.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
            
            form.addEventListener('keydown', function(e) {
                // Disable common shortcuts that might interfere
                if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'r' || e.key === 'f5')) {
                    e.preventDefault();
                }
            });
            
            // Track time spent on page (security feature)
            const startTime = Date.now();
            form.addEventListener('submit', function() {
                const timeSpent = (Date.now() - startTime) / 1000;
                if (timeSpent < 10) {
                    alert('Please take time to read the deletion information carefully.');
                    return false;
                }
            });
            
            // Auto-clear sensitive fields on page visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    const passwordField = form.querySelector('input[name="password"]');
                    if (passwordField) {
                        passwordField.value = '';
                    }
                }
            });
        });
    </script>
    @endpush
</x-dynamic-component>