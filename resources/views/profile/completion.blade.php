<!-- resources/views/profile/completion.blade.php -->
<x-layouts.app title="Complete Your Profile">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                    <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Complete Your Profile</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">
                    Help us serve you better by completing your profile information
                </p>
            </div>

            <!-- Progress Overview -->
            <x-admin.card class="mb-8">
                <div class="text-center">
                    <div class="mb-6">
                        <div class="relative inline-flex items-center justify-center">
                            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-300 dark:text-gray-700" stroke="currentColor" stroke-width="2" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                <path class="text-blue-600 dark:text-blue-400" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-dasharray="{{ $completion['essential_percentage'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                                        {{ $completion['essential_percentage'] }}%
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Complete</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        @if($completion['is_essential_complete'])
                            🎉 Your profile is complete!
                        @else
                            {{ $completion['completed_essential'] }} of {{ $completion['total_essential'] }} essential fields completed
                        @endif
                    </h2>
                    
                    @if(!$completion['is_essential_complete'])
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Complete the remaining {{ count($completion['missing_essential']) }} field(s) to unlock all platform features
                    </p>
                    @else
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Great job! Your essential profile is complete. Consider adding optional information to enhance your experience.
                    </p>
                    @endif

                    @if(!$completion['is_essential_complete'])
                    <x-admin.button 
                        href="{{ route('profile.edit') }}" 
                        color="primary" 
                        size="lg"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
                    >
                        Complete Profile Now
                    </x-admin.button>
                    @else
                    <x-admin.button 
                        href="{{ route('profile.show') }}" 
                        color="primary" 
                        size="lg"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                    >
                        View Your Profile
                    </x-admin.button>
                    @endif
                </div>
            </x-admin.card>

            <!-- Completion Steps -->
            @if(count($suggestions) > 0)
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recommended Actions</h3>
                <div class="space-y-4">
                    @foreach($suggestions as $index => $suggestion)
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $suggestion['priority'] === 'high' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : ($suggestion['priority'] === 'medium' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400') }}">
                                    {{ $index + 1 }}
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $suggestion['title'] }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $suggestion['description'] }}
                                        </p>
                                        <x-admin.badge 
                                            :type="$suggestion['priority'] === 'high' ? 'danger' : ($suggestion['priority'] === 'medium' ? 'warning' : 'info')" 
                                            size="sm" 
                                            class="mt-2"
                                        >
                                            {{ ucfirst($suggestion['priority']) }} Priority
                                        </x-admin.badge>
                                    </div>
                                    <div class="ml-4">
                                        <x-admin.button 
                                            href="{{ $suggestion['action_url'] }}" 
                                            color="primary"
                                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>'
                                        >
                                            {{ $suggestion['type'] === 'avatar' ? 'Upload Photo' : 'Complete' }}
                                        </x-admin.button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Field Status Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Essential Fields -->
                <x-admin.card title="Essential Fields" subtitle="Required for full platform access">
                    <div class="space-y-3">
                        @foreach(['name', 'email', 'phone', 'company', 'address', 'city', 'state', 'country', 'avatar'] as $field)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ ucwords(str_replace('_', ' ', $field)) }}
                            </span>
                            @if($completion['fields_status'][$field] ?? false)
                                <div class="flex items-center text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs">Complete</span>
                                </div>
                            @else
                                <div class="flex items-center text-amber-600 dark:text-amber-400">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs">Pending</span>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </x-admin.card>

                <!-- Enhanced Fields -->
                <x-admin.card title="Enhanced Fields" subtitle="Optional but recommended">
                    <div class="space-y-3">
                        @foreach(['postal_code', 'bio', 'website', 'position'] as $field)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ ucwords(str_replace('_', ' ', $field)) }}
                            </span>
                            @if($completion['fields_status'][$field] ?? false)
                                <div class="flex items-center text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs">Complete</span>
                                </div>
                            @else
                                <div class="flex items-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs">Optional</span>
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </x-admin.card>
            </div>

            <!-- Benefits of Completion -->
            <x-admin.card title="Benefits of a Complete Profile">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Better Service</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Complete information helps us provide more personalized and effective service tailored to your needs.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Enhanced Communication</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Complete contact information ensures you receive important updates and can communicate effectively with our team.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/30 mb-4">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Security & Trust</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Verified profiles help build trust and ensure secure transactions and communications within our platform.
                        </p>
                    </div>
                </div>
            </x-admin.card>

            <!-- Quick Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                <x-admin.button 
                    href="{{ route('profile.edit') }}" 
                    color="primary" 
                    size="lg"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
                >
                    Edit Profile
                </x-admin.button>
                
                <x-admin.button 
                    href="{{ route('profile.preferences') }}" 
                    color="info"
                    size="lg"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>'
                >
                    Set Preferences
                </x-admin.button>
                
                @if($completion['is_essential_complete'])
                <x-admin.button 
                    href="{{ route('dashboard') }}" 
                    color="light"
                    size="lg"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>'
                >
                    Go to Dashboard
                </x-admin.button>
                @else
                <x-admin.button 
                    href="{{ route('profile.show') }}" 
                    color="light"
                    size="lg"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                >
                    Skip for Now
                </x-admin.button>
                @endif
            </div>

            <!-- Help Section -->
            @if(!$completion['is_essential_complete'])
            <div class="mt-8 bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Need Help?</h4>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <p class="mb-2">If you're having trouble completing your profile or have questions about what information is required, we're here to help:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Contact our support team at <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">support@example.com</a></li>
                                <li>Check our <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">frequently asked questions</a></li>
                                <li>Schedule a call with our customer success team</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-refresh completion status
        document.addEventListener('DOMContentLoaded', function() {
            // Check if user has completed profile in another tab
            setInterval(function() {
                fetch('{{ route("profile.completion-status") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.completion.is_essential_complete) {
                            // Reload page to show completion state
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.log('Could not check completion status');
                    });
            }, 30000); // Check every 30 seconds

            // Track completion interactions
            document.querySelectorAll('a[href*="profile"]').forEach(link => {
                link.addEventListener('click', function() {
                    // Track which completion step user is taking
                    const action = this.textContent.trim();
                    console.log('Profile completion action:', action);
                    
                    // You could send analytics here
                    // analytics.track('profile_completion_action', { action: action });
                });
            });
        });
    </script>
    @endpush
</x-layouts.app>