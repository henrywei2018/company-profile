{{-- resources/views/admin/quotations/show.blade.php --}}
<x-layouts.admin title="Quotation Details" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <x-admin.breadcrumb :items="[
                'Quotations' => route('admin.quotations.index'),
                'Quotation #' . $quotation->id => '#',
            ]" />
        </div>

        <div class="flex flex-wrap gap-3">
            {{-- Project Conversion Actions --}}
            @if ($quotation->canConvertToProject())
                <x-admin.button href="{{ route('admin.quotations.convert-to-project.form', $quotation) }}" color="success"
                    size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Convert to Project
                </x-admin.button>

                <button type="button" onclick="quickConvertToProject({{ $quotation->id }})"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Quick Convert
                </button>
            @elseif($quotation->project_created && isset($existingProject))
                <x-admin.button href="{{ route('admin.projects.show', $existingProject) }}" color="primary"
                    size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    View Project
                </x-admin.button>
            @endif

            <x-admin.button href="{{ route('admin.quotations.edit', $quotation) }}" color="primary" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Quotation
            </x-admin.button>
            <x-admin.button href="{{ route('admin.quotations.duplicate', $quotation) }}" color="light" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Duplicate
            </x-admin.button>
        </div>
    </div>
    
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-3 space-y-6">
            <!-- Header Information Card -->
            <x-admin.card>
                <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Quotation Request #{{ $quotation->id }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                                Received {{ $quotation->created_at->format('F j, Y \a\t g:i A') }}
                                @if($quotation->daysSinceCreation <= 7)
                                    <span class="text-blue-600 dark:text-blue-400">({{ $quotation->created_at->diffForHumans() }})</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="flex flex-col items-end space-y-2">
                            <x-admin.badge 
                                :type="match($quotation->status) {
                                    'pending' => 'warning',
                                    'reviewed' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'default'
                                }"
                                size="lg"
                            >
                                {{ $quotation->formattedStatus }}
                            </x-admin.badge>
                            
                            @if($quotation->priority !== 'normal')
                                <x-admin.badge 
                                    :type="match($quotation->priority) {
                                        'low' => 'gray',
                                        'high' => 'warning',
                                        'urgent' => 'danger',
                                        default => 'info'
                                    }"
                                    size="sm"
                                >
                                    {{ $quotation->formattedPriority }} Priority
                                </x-admin.badge>
                            @endif

                            @if($quotation->project_created)
                                <x-admin.badge type="success" size="sm">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Converted to Project
                                </x-admin.badge>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Project Type</h3>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $quotation->project_type ?: 'General Inquiry' }}
                            </p>
                        </div>
                        
                        @if($quotation->service)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Service</h3>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $quotation->service->title }}
                                </p>
                            </div>
                        @endif
                        
                        @if($quotation->budget_range)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Budget Range</h3>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $quotation->budget_range }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-admin.card>

            {{-- Project Conversion Status Card --}}
            @if($quotation->status === 'approved')
                <x-admin.card>
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Project Conversion Status</h2>
                    </div>
                    
                    <div class="p-6">
                        @if($quotation->project_created && isset($existingProject))
                            {{-- Already Converted --}}
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-green-800 dark:text-green-400">
                                            Successfully Converted to Project
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-green-700 dark:text-green-300">
                                                This quotation has been converted to project: 
                                                <a href="{{ route('admin.projects.show', $existingProject) }}" 
                                                   class="font-semibold underline hover:no-underline">
                                                    {{ $existingProject->title }}
                                                </a>
                                            </p>
                                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                Converted on {{ $quotation->project_created_at->format('M j, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                        <div class="mt-4">
                                            <x-admin.button 
                                                href="{{ route('admin.projects.show', $existingProject) }}" 
                                                color="success" 
                                                size="sm"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                View Project Details
                                            </x-admin.button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($quotation->canConvertToProject())
                            {{-- Ready for Conversion --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-400">
                                            Ready for Project Conversion
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                                This approved quotation can now be converted into a manageable project.
                                            </p>
                                            @php
                                                $conversionSummary = $quotation->getConversionSummary();
                                            @endphp
                                            @if(!empty($conversionSummary['warnings']))
                                                <div class="mt-2">
                                                    <p class="text-xs text-blue-600 dark:text-blue-400 mb-1">Considerations:</p>
                                                    <ul class="text-xs text-blue-600 dark:text-blue-400 space-y-1">
                                                        @foreach($conversionSummary['warnings'] as $warning)
                                                            <li>• {{ $warning }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-4 flex space-x-3">
                                            <x-admin.button 
                                                href="{{ route('admin.quotations.convert-to-project.form', $quotation) }}" 
                                                color="primary" 
                                                size="sm"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Convert with Options
                                            </x-admin.button>
                                            
                                            <button type="button" 
                                                    onclick="quickConvertToProject({{ $quotation->id }})"
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-600 dark:hover:bg-neutral-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                Quick Convert
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Not Ready for Conversion --}}
                            <div class="bg-gray-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-800 dark:text-neutral-300">
                                            Project Conversion Not Available
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600 dark:text-neutral-400">
                                                @if($quotation->status !== 'approved')
                                                    Quotation must be approved before it can be converted to a project.
                                                @elseif($quotation->project_created)
                                                    This quotation has already been converted to a project.
                                                @else
                                                    Project conversion is not available for this quotation.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endif

            <!-- Client Information -->
            <x-admin.card title="Client Information">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Full Name</h3>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $quotation->name }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email Address</h3>
                                <p class="mt-1">
                                    <a href="mailto:{{ $quotation->email }}" 
                                       class="text-base text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $quotation->email }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Phone Number</h3>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">
                                    @if($quotation->phone)
                                        <a href="tel:{{ $quotation->phone }}" 
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $quotation->phone }}
                                        </a>
                                    @else
                                        <span class="text-gray-500 dark:text-neutral-500">Not provided</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Company</h3>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ $quotation->company ?: 'Not provided' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($quotation->client)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-neutral-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Linked Client Account</h3>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">
                                        {{ $quotation->client->name }}
                                        <x-admin.badge type="success" size="sm" class="ml-2">Verified</x-admin.badge>
                                    </p>
                                </div>
                                <x-admin.button 
                                    href="{{ route('admin.users.show', $quotation->client) }}" 
                                    color="light" 
                                    size="sm"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    View Profile
                                </x-admin.button>
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Project Requirements -->
            <x-admin.card title="Project Requirements">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        @if($quotation->location)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Location</h3>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $quotation->location }}</p>
                            </div>
                        @endif
                        
                        @if($quotation->start_date)
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Desired Start Date</h3>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ $quotation->start_date->format('F j, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                    
                    @if($quotation->requirements)
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">Detailed Requirements</h3>
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-4 text-gray-900 dark:text-white">
                                    {!! nl2br(e($quotation->requirements)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Admin Response Section -->
            <x-admin.card title="Admin Response & Estimates">
                <form action="{{ route('admin.quotations.update', $quotation) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <x-admin.input 
                            label="Estimated Cost" 
                            name="estimated_cost" 
                            :value="$quotation->estimated_cost"
                            placeholder="e.g., $5,000 - $7,500"
                        />
                        
                        <x-admin.input 
                            label="Estimated Timeline" 
                            name="estimated_timeline" 
                            :value="$quotation->estimated_timeline"
                            placeholder="e.g., 6-8 weeks"
                        />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <x-admin.textarea 
                            label="Internal Notes" 
                            name="internal_notes" 
                            :value="$quotation->internal_notes"
                            rows="4"
                            helper="These notes are for internal use only"
                        />
                        
                        <x-admin.textarea 
                            label="Client Notes" 
                            name="admin_notes" 
                            :value="$quotation->admin_notes"
                            rows="4"
                            helper="These notes may be shared with the client"
                        />
                    </div>
                    
                    <div class="flex justify-end">
                        <x-admin.button type="submit" color="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Update Information
                        </x-admin.button>
                    </div>
                </form>
            </x-admin.card>

            <!-- Attachments -->
            @if($quotation->attachments && $quotation->attachments->count() > 0)
                <x-admin.card title="Attachments ({{ $quotation->attachments->count() }})">
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($quotation->attachments as $attachment)
                                @php
                                    $iconClass = $attachment->file_icon ?? 'text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700';
                                @endphp
                                
                                <div class="flex items-center p-4 bg-gray-50 dark:bg-neutral-800/50 rounded-lg border border-gray-200 dark:border-neutral-700 hover:shadow-md transition-all duration-200">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-10 h-10 rounded-lg {{ $iconClass }} flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $attachment->file_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-500">
                                            {{ $attachment->formatted_file_size }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 ml-2">
                                        <a href="{{ route('admin.quotations.attachments.download', ['quotation' => $quotation->id, 'attachment' => $attachment->id]) }}" 
                                           class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </x-admin.card>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="p-6 space-y-3">
                    @if($quotation->status === 'pending')
                        <form action="{{ route('admin.quotations.update-status', $quotation) }}" method="POST" class="space-y-3">
                            @csrf
                            <button type="submit" name="status" value="approved" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Approve Quotation
                            </button>
                            
                            <button type="submit" name="status" value="rejected"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Reject Quotation
                            </button>
                        </form>
                    @endif
                    
                    <a href="mailto:{{ $quotation->email }}?subject=RE: {{ urlencode($quotation->project_type) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Send Email
                    </a>
                </div>
            </x-admin.card>

            <!-- Status & Timeline -->
            <x-admin.card title="Status & Timeline">
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-600"></span>
                                    <div class="relative flex space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Received</p>
                                                <p class="text-xs text-gray-500 dark:text-neutral-500">{{ $quotation->created_at->format('M j, Y g:i A') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($quotation->reviewed_at)
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-600"></span>
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Reviewed</p>
                                                    <p class="text-xs text-gray-500 dark:text-neutral-500">{{ $quotation->reviewed_at->format('M j, Y g:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            
                            @if($quotation->approved_at)
                                <li>
                                    <div class="relative pb-8">
                                        @if($quotation->project_created)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-600"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Approved</p>
                                                    <p class="text-xs text-gray-500 dark:text-neutral-500">{{ $quotation->approved_at->format('M j, Y g:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            
                            @if($quotation->project_created)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Converted to Project</p>
                                                    <p class="text-xs text-gray-500 dark:text-neutral-500">{{ $quotation->project_created_at->format('M j, Y g:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </x-admin.card>

            <!-- Client Response -->
            @if($quotation->client_approved !== null)
                <x-admin.card title="Client Response">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <x-admin.badge :type="$quotation->client_approved ? 'success' : 'danger'" size="lg">
                                {{ $quotation->client_approved ? 'Approved' : 'Declined' }}
                            </x-admin.badge>
                            
                            @if($quotation->client_approved_at)
                                <span class="text-xs text-gray-500 dark:text-neutral-500">
                                    {{ $quotation->client_approved_at->format('M j, Y g:i A') }}
                                </span>
                            @endif
                        </div>
                        
                        @if(!$quotation->client_approved && $quotation->client_decline_reason)
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Decline Reason</h4>
                                <p class="text-sm text-red-700 dark:text-red-300">{{ $quotation->client_decline_reason }}</p>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endif

            <!-- Related Projects -->
            @if(isset($existingProject))
                <x-admin.card title="Related Project">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $existingProject->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-500">Created {{ $existingProject->created_at->format('M j, Y') }}</p>
                                <div class="mt-2">
                                    <x-admin.badge 
                                        :type="match($existingProject->status) {
                                            'planning' => 'info',
                                            'in_progress' => 'warning',
                                            'completed' => 'success',
                                            'on_hold' => 'orange',
                                            'cancelled' => 'danger',
                                            default => 'default'
                                        }"
                                        size="sm"
                                    >
                                        {{ ucfirst(str_replace('_', ' ', $existingProject->status)) }}
                                    </x-admin.badge>
                                </div>
                            </div>
                            <x-admin.button href="{{ route('admin.projects.show', $existingProject) }}" color="light" size="sm">
                                View Project
                            </x-admin.button>
                        </div>
                    </div>
                </x-admin.card>
            @endif

            <!-- Statistics -->
            <x-admin.card title="Statistics">
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700 dark:text-neutral-300">Days Since Received</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $quotation->daysSinceCreation }}</span>
                    </div>
                    
                    @if($quotation->responseTime)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-neutral-300">Response Time</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $quotation->responseTime }} days</span>
                        </div>
                    @endif
                    
                    @if($quotation->approvalTime)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-neutral-300">Approval Time</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $quotation->approvalTime }} days</span>
                        </div>
                    @endif
                    
                    @if($quotation->source)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-neutral-300">Source</span>
                            <x-admin.badge type="info" size="sm">{{ ucfirst(str_replace('_', ' ', $quotation->source)) }}</x-admin.badge>
                        </div>
                    @endif

                    @if($quotation->attachments->count() > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-neutral-300">Attachments</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $quotation->attachments->count() }} files</span>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        </div>
    </div>

    <!-- Related Quotations -->
    @if(isset($relatedQuotations) && $relatedQuotations->count() > 0)
        <div class="mt-8">
            <x-admin.card title="Other Quotations from This Client">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Project Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                            @foreach($relatedQuotations as $related)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $related->project_type ?: 'General Inquiry' }}
                                        @if($related->project_created)
                                            <x-admin.badge type="success" size="sm" class="ml-2">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                Project
                                            </x-admin.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-admin.badge 
                                            :type="match($related->status) {
                                                'pending' => 'warning',
                                                'reviewed' => 'info',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default => 'default'
                                            }"
                                            size="sm"
                                        >
                                            {{ ucfirst($related->status) }}
                                        </x-admin.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                        {{ $related->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-admin.button href="{{ route('admin.quotations.show', $related) }}" color="light" size="sm">
                                            View
                                        </x-admin.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>
    @endif

    <!-- Loading Modal for Quick Convert -->
    <div id="quickConvertModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-neutral-800">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <svg class="animate-spin h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Converting to Project</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-neutral-400">
                        Please wait while we create your project...
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

<script>
    async function quickConvertToProject(quotationId) {
        // Show loading modal
        document.getElementById('quickConvertModal').classList.remove('hidden');
        
        try {
            const response = await fetch(`/admin/quotations/${quotationId}/quick-convert`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success notification
                showNotification('success', data.message);
                
                // Redirect to project after short delay
                setTimeout(() => {
                    window.location.href = data.project_url;
                }, 1500);
            } else {
                throw new Error(data.message || 'Conversion failed');
            }
        } catch (error) {
            // Hide loading modal
            document.getElementById('quickConvertModal').classList.add('hidden');
            
            // Show error notification
            showNotification('error', error.message || 'Failed to convert quotation to project');
            
            console.error('Quick convert error:', error);
        }
    }

    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-neutral-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden`;
        
        const color = type === 'success' ? 'green' : 'red';
        const icon = type === 'success' ? 
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' :
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
        
        notification.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-${color}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            ${icon}
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="bg-white dark:bg-neutral-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
</script>

<style>
    /* Enhanced animations */
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Improved hover effects */
    .hover\:shadow-md:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Better transition effects */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
</style>