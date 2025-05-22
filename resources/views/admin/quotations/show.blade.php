<x-layouts.admin title="Quotation Details">
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb :items="[
            'Quotations' => route('admin.quotations.index'),
            'Quotation #' . $quotation->id => null
        ]" />

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Quotation Request #{{ $quotation->id }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Received {{ $quotation->created_at->format('F j, Y \a\t g:i A') }}
                    @if($quotation->created_at->diffInDays() <= 7)
                        <span class="text-amber-600">({{ $quotation->created_at->diffForHumans() }})</span>
                    @endif
                </p>
            </div>
            
            <div class="mt-4 lg:mt-0 flex items-center space-x-3">
                @if($quotation->status === 'approved' && !$quotation->client_approved)
                    <x-admin.button href="{{ route('admin.projects.create', ['from_quotation' => $quotation->id]) }}" color="success">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Project
                    </x-admin.button>
                @endif
                
                <x-admin.button href="{{ route('admin.quotations.edit', $quotation) }}" color="light">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Quotation
                </x-admin.button>
                
                <x-admin.dropdown>
                    <x-slot name="trigger">
                        <x-admin.button color="light">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                            More Actions
                        </x-admin.button>
                    </x-slot>
                    
                    <x-admin.dropdown-item href="{{ route('admin.quotations.duplicate', $quotation) }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Duplicate
                    </x-admin.dropdown-item>
                    
                    <x-admin.dropdown-item 
                        type="form"
                        action="{{ route('admin.quotations.destroy', $quotation) }}"
                        method="DELETE"
                        confirm="true"
                        confirmMessage="Are you sure you want to delete this quotation?"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </x-admin.dropdown-item>
                </x-admin.dropdown>
            </div>
        </div>

        <!-- Status and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Status Card -->
            <x-admin.card title="Status">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Status</span>
                        <x-admin.badge 
                            :type="match($quotation->status) {
                                'pending' => 'warning',
                                'reviewed' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'default'
                            }"
                        >
                            {{ ucfirst($quotation->status) }}
                        </x-admin.badge>
                    </div>
                    
                    @if($quotation->client_approved !== null)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Client Response</span>
                            <x-admin.badge :type="$quotation->client_approved ? 'success' : 'danger'">
                                {{ $quotation->client_approved ? 'Approved' : 'Declined' }}
                            </x-admin.badge>
                        </div>
                    @endif
                    
                    <!-- Quick Status Update -->
                    @if($quotation->status === 'pending')
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <form action="{{ route('admin.quotations.update-status', $quotation) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="flex space-x-2">
                                    <button type="submit" name="status" value="approved" class="flex-1 bg-green-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button type="submit" name="status" value="rejected" class="flex-1 bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-red-700">
                                        Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </x-admin.card>
            
            <!-- Timeline -->
            <div class="lg:col-span-2">
                <x-admin.card title="Timeline">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-900 dark:text-white">Quotation received</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->created_at->format('M j, Y g:i A') }}</p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                <p>From {{ $quotation->name }} ({{ $quotation->email }})</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($quotation->reviewed_at)
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-900 dark:text-white">Quotation reviewed</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->reviewed_at->format('M j, Y g:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            
                            @if($quotation->approved_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div>
                                                    <p class="text-sm text-gray-900 dark:text-white">Quotation approved</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->approved_at->format('M j, Y g:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Client Information -->
            <x-admin.card title="Client Information">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="mailto:{{ $quotation->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    {{ $quotation->email }}
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($quotation->phone)
                                    <a href="tel:{{ $quotation->phone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        {{ $quotation->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $quotation->company ?: 'Not provided' }}
                            </p>
                        </div>
                    </div>
                    
                    @if($quotation->client_id)
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registered Client</label>
                            <p class="mt-1">
                                <a href="{{ route('admin.users.show', $quotation->client) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    View Client Profile
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </x-admin.card>
            
            <!-- Project Details -->
            <x-admin.card title="Project Details">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Type</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->project_type ?: 'General Inquiry' }}</p>
                    </div>
                    
                    @if($quotation->service)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->service->title }}</p>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->location ?: 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget Range</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->budget_range ?: 'Not specified' }}</p>
                        </div>
                    </div>
                    
                    @if($quotation->start_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Desired Start Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->start_date->format('F j, Y') }}</p>
                        </div>
                    @endif
                    
                    @if($quotation->requirements)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Requirements</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-white prose prose-sm max-w-none">
                                {!! nl2br(e($quotation->requirements)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        </div>

        <!-- Admin Response Section -->
        <x-admin.card title="Admin Response & Notes">
            <form action="{{ route('admin.quotations.update', $quotation) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-admin.textarea 
                            label="Internal Notes" 
                            name="internal_notes" 
                            :value="$quotation->internal_notes"
                            rows="4"
                            helper="These notes are for internal use only and will not be visible to the client."
                        />
                    </div>
                    
                    <div>
                        <x-admin.textarea 
                            label="Admin Notes" 
                            name="admin_notes" 
                            :value="$quotation->admin_notes"
                            rows="4"
                            helper="These notes may be included in client communications."
                        />
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-admin.input 
                            label="Estimated Cost" 
                            name="estimated_cost" 
                            :value="$quotation->estimated_cost"
                            placeholder="e.g., $2,500 - $3,500"
                        />
                    </div>
                    
                    <div>
                        <x-admin.input 
                            label="Estimated Timeline" 
                            name="estimated_timeline" 
                            :value="$quotation->estimated_timeline"
                            placeholder="e.g., 4-6 weeks"
                        />
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <x-admin.button type="submit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Update Information
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>

        <!-- Send Email Response -->
        <x-admin.card title="Send Email Response">
            <form action="{{ route('admin.quotations.send-response', $quotation) }}" method="POST" class="space-y-6">
                @csrf
                
                <x-admin.input 
                    label="Email Subject" 
                    name="email_subject" 
                    :value="old('email_subject', 'Response to Your Quotation Request - ' . $quotation->project_type)"
                    required
                />
                
                <x-admin.textarea 
                    label="Email Message" 
                    name="email_message" 
                    rows="8"
                    :value="old('email_message', 'Dear ' . $quotation->name . ',\n\nThank you for your quotation request regarding ' . $quotation->project_type . '. We have reviewed your requirements and are pleased to provide you with the following information...\n\nPlease let us know if you have any questions or if you would like to proceed.\n\nBest regards,\nCV Usaha Prima Lestari Team')"
                    required
                />
                
                <x-admin.checkbox 
                    label="Include quotation details in email" 
                    name="include_quotation" 
                    :checked="true"
                    helper="This will include the project details, estimated cost, and timeline in the email."
                />
                
                <div class="flex justify-end">
                    <x-admin.button type="submit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Send Email Response
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>

        <!-- Related Information -->
        @if($relatedQuotations && $relatedQuotations->count() > 0)
            <x-admin.card title="Other Quotations from This Client">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Project Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($relatedQuotations as $related)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $related->project_type ?: 'General Inquiry' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-admin.badge 
                                            :type="match($related->status) {
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default => 'default'
                                            }"
                                        >
                                            {{ ucfirst($related->status) }}
                                        </x-admin.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $related->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.quotations.show', $related) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        @endif
    </div>
</x-layouts.admin>