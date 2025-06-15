<x-layouts.client>
    <x-slot name="title">Quotation Details</x-slot>
    <x-slot name="description">View detailed information about your quotation request.</x-slot>

    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $quotation->project_type }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Quotation #{{ $quotation->quotation_number }}</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Status Badge -->
            <x-admin.status-badge :status="$quotation->status" />
            
            <!-- Priority Badge -->
            <x-admin.badge :priority="$quotation->priority" />
            
            <!-- Actions Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <x-admin.button @click="open = !open" color="light">
                    Actions
                    <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </x-admin.button>

                <div x-show="open" @click.away="open = false" 
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                    <div class="py-1">
                        @if(in_array($quotation->status, ['pending', 'reviewed']))
                            <a href="{{ route('client.quotations.edit', $quotation) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700">
                                Edit Quotation
                            </a>
                        @endif

                        <form method="POST" action="{{ route('client.quotations.duplicate', $quotation) }}" class="inline">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700 text-left">
                                Duplicate
                            </button>
                        </form>

                        <a href="{{ route('client.quotations.print', $quotation) }}" 
                           target="_blank"
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700">
                            Print
                        </a>

                        @if($quotation->status === 'pending')
                            <form method="POST" action="{{ route('client.quotations.cancel', $quotation) }}" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to cancel this quotation?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="block w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-neutral-700 text-left">
                                    Cancel Quotation
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Project Information -->
            <x-admin.card>
                
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Information</h3>
                

                <div class="px-6 py-4 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Type</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->project_type }}</p>
                        </div>

                        @if($quotation->service)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $quotation->service->name }}
                                </span>
                            </div>
                        @endif

                        @if($quotation->location)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->location }}</p>
                            </div>
                        @endif

                        @if($quotation->budget_range)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget Range</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->budget_range }}</p>
                            </div>
                        @endif

                        @if($quotation->start_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expected Start Date</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->start_date->format('F j, Y') }}</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Requirements</label>
                        <div class="prose max-w-none text-sm text-gray-900 dark:text-white">
                            {!! nl2br(e($quotation->requirements)) !!}
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Contact Information -->
            <x-admin.card>
                
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contact Information</h3>
                

                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <a href="mailto:{{ $quotation->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $quotation->email }}
                                </a>
                            </p>
                        </div>

                        @if($quotation->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <a href="tel:{{ $quotation->phone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $quotation->phone }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        @if($quotation->company)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->company }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-admin.card>

            <!-- Attachments -->
            @if($quotation->attachments->count() > 0)
                <x-admin.card>
                    
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Attachments 
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $quotation->attachments->count() }})</span>
                        </h3>
                    

                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($quotation->attachments as $attachment)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <div class="flex items-start space-x-3">
                                        <!-- File Icon -->
                                        <div class="flex-shrink-0">
                                            @if($attachment->isImage())
                                                <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                                </svg>
                                            @elseif($attachment->isDocument())
                                                <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="h-8 w-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>

                                        <!-- File Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $attachment->file_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attachment->formatted_file_size }}
                                            </p>
                                            <div class="mt-2">
                                                <a href="{{ route('client.quotations.download-attachment', [$quotation, $attachment]) }}" 
                                                   class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-admin.card>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Status Information -->
            <x-admin.card>
                
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status Information</h3>
                

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Status</label>
                        <x-admin.status-badge :status="$quotation->status" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                        <x-admin.badge :priority="$quotation->priority" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->created_at->format('F j, Y \a\t g:i A') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->created_at->diffForHumans() }}</p>
                    </div>

                    @if($quotation->updated_at->ne($quotation->created_at))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Updated</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->updated_at->format('F j, Y \a\t g:i A') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->updated_at->diffForHumans() }}</p>
                        </div>
                    @endif

                    @if($quotation->reviewed_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reviewed</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->reviewed_at->format('F j, Y \a\t g:i A') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->reviewed_at->diffForHumans() }}</p>
                        </div>
                    @endif

                    @if($quotation->approved_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Approved</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->approved_at->format('F j, Y \a\t g:i A') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->approved_at->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Client Response Section (if quotation is approved) -->
            @if($quotation->status === 'approved' && is_null($quotation->client_approved))
                <x-admin.card>
                    
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your Response Required</h3>
                    

                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            This quotation has been approved. Please review and let us know if you'd like to proceed.
                        </p>

                        <div class="space-y-3">
                            <form method="POST" action="{{ route('client.quotations.approve', $quotation) }}" class="inline">
                                @csrf
                                <x-admin.button type="submit" color="primary" class="w-full">
                                    Accept & Proceed
                                </x-admin.button>
                            </form>

                            <x-admin.button href="{{ route('client.quotations.show-decline-form', $quotation) }}" color="light" class="w-full">
                                Decline
                            </x-admin.button>
                        </div>
                    </div>
                </x-admin.card>
            @endif

            <!-- Client Response Status -->
            @if(!is_null($quotation->client_approved))
                <x-admin.card>
                    
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your Response</h3>
                    

                    <div class="px-6 py-4">
                        @if($quotation->client_approved)
                            <div class="flex items-center text-green-600 dark:text-green-400">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium">Approved by you</span>
                            </div>
                            @if($quotation->client_approved_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $quotation->client_approved_at->format('F j, Y \a\t g:i A') }}
                                </p>
                            @endif
                        @else
                            <div class="flex items-center text-red-600 dark:text-red-400">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium">Declined by you</span>
                            </div>
                            @if($quotation->client_decline_reason)
                                <div class="mt-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Reason:</label>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $quotation->client_decline_reason }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </x-admin.card>
            @endif

            <!-- Quick Actions -->
            <x-admin.card>
               
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
                

                <div class="px-6 py-4 space-y-3">
                    <x-admin.button href="{{ route('client.quotations.index') }}" color="light" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Quotations
                    </x-admin.button>

                    @if(in_array($quotation->status, ['pending', 'reviewed']))
                        <x-admin.button href="{{ route('client.quotations.edit', $quotation) }}" color="gray" class="w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Quotation
                        </x-admin.button>
                    @endif

                    <form method="POST" action="{{ route('client.quotations.duplicate', $quotation) }}">
                        @csrf
                        <x-admin.button type="submit" color="light" class="w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Duplicate
                        </x-admin.button>
                    </form>

                    <x-admin.button href="{{ route('client.quotations.print', $quotation) }}" target="_blank" color="light" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print
                    </x-admin.button>
                </div>
            </x-admin.card>

        </div>
    </div>
</x-layouts.client>