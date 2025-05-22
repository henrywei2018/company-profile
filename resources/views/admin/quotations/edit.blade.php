<x-layouts.admin title="Edit Quotation">
    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb :items="[
            'Quotations' => route('admin.quotations.index'),
            'Quotation #' . $quotation->id => route('admin.quotations.show', $quotation),
            'Edit' => null
        ]" />

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Edit Quotation #{{ $quotation->id }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Update quotation details and manage client information
                </p>
            </div>
            
            <div class="mt-4 lg:mt-0">
                <x-admin.button href="{{ route('admin.quotations.show', $quotation) }}" color="light">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Details
                </x-admin.button>
            </div>
        </div>

        <form action="{{ route('admin.quotations.update', $quotation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Client Information -->
            <x-admin.form-section 
                title="Client Information" 
                description="Basic contact information for the client"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Full Name" 
                        name="name" 
                        :value="$quotation->name"
                        required
                    />
                    
                    <x-admin.input 
                        label="Email Address" 
                        name="email" 
                        type="email"
                        :value="$quotation->email"
                        required
                    />
                    
                    <x-admin.input 
                        label="Phone Number" 
                        name="phone" 
                        :value="$quotation->phone"
                        placeholder="+62 xxx-xxxx-xxxx"
                    />
                    
                    <x-admin.input 
                        label="Company" 
                        name="company" 
                        :value="$quotation->company"
                    />
                </div>
                
                <div>
                    <x-admin.select 
                        label="Link to Registered Client" 
                        name="client_id" 
                        :value="$quotation->client_id"
                        :options="['' => 'No linked client'] + $clients->pluck('name', 'id')->toArray()"
                        helper="Link this quotation to an existing registered client account"
                    />
                </div>
            </x-admin.form-section>

            <!-- Project Details -->
            <x-admin.form-section 
                title="Project Details" 
                description="Information about the requested project or service"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Project Type" 
                        name="project_type" 
                        :value="$quotation->project_type"
                        placeholder="e.g., Website Development, App Development"
                    />
                    
                    <x-admin.select 
                        label="Related Service" 
                        name="service_id" 
                        :value="$quotation->service_id"
                        :options="['' => 'Select a service'] + $services->pluck('title', 'id')->toArray()"
                    />
                    
                    <x-admin.input 
                        label="Project Location" 
                        name="location" 
                        :value="$quotation->location"
                        placeholder="e.g., Jakarta, Remote"
                    />
                    
                    <x-admin.input 
                        label="Budget Range" 
                        name="budget_range" 
                        :value="$quotation->budget_range"
                        placeholder="e.g., $5,000 - $10,000"
                    />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Desired Start Date" 
                        name="start_date" 
                        type="date"
                        :value="$quotation->start_date?->format('Y-m-d')"
                    />
                    
                    <x-admin.select 
                        label="Status" 
                        name="status" 
                        :value="$quotation->status"
                        :options="[
                            'pending' => 'Pending Review',
                            'reviewed' => 'Under Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected'
                        ]"
                        required
                    />
                </div>
                
                <x-admin.textarea 
                    label="Project Requirements" 
                    name="requirements" 
                    :value="$quotation->requirements"
                    rows="5"
                    placeholder="Detailed description of project requirements..."
                />
            </x-admin.form-section>

            <!-- Admin Response -->
            <x-admin.form-section 
                title="Admin Response & Estimates" 
                description="Internal notes and client-facing response information"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Estimated Cost" 
                        name="estimated_cost" 
                        :value="$quotation->estimated_cost"
                        placeholder="e.g., $7,500 - $9,000"
                    />
                    
                    <x-admin.input 
                        label="Estimated Timeline" 
                        name="estimated_timeline" 
                        :value="$quotation->estimated_timeline"
                        placeholder="e.g., 8-10 weeks"
                    />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.textarea 
                        label="Internal Notes" 
                        name="internal_notes" 
                        :value="$quotation->internal_notes"
                        rows="4"
                        helper="These notes are for internal use only"
                    />
                    
                    <x-admin.textarea 
                        label="Admin Notes" 
                        name="admin_notes" 
                        :value="$quotation->admin_notes"
                        rows="4"
                        helper="These notes may be shared with the client"
                    />
                </div>
            </x-admin.form-section>

            <!-- Additional Information -->
            <x-admin.form-section 
                title="Additional Information" 
                description="Extra details and client communication history"
            >
                <x-admin.textarea 
                    label="Additional Information" 
                    name="additional_info" 
                    :value="$quotation->additional_info"
                    rows="3"
                    placeholder="Any additional notes or information..."
                />
                
                @if($quotation->client_approved !== null)
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Client Response</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Client {{ $quotation->client_approved ? 'approved' : 'declined' }} this quotation
                                    @if($quotation->client_approved_at)
                                        on {{ $quotation->client_approved_at->format('F j, Y \a\t g:i A') }}
                                    @endif
                                </p>
                            </div>
                            <x-admin.badge :type="$quotation->client_approved ? 'success' : 'danger'">
                                {{ $quotation->client_approved ? 'Approved' : 'Declined' }}
                            </x-admin.badge>
                        </div>
                        
                        @if(!$quotation->client_approved && $quotation->client_decline_reason)
                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Decline Reason</label>
                                <p class="text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-900 p-2 rounded border">
                                    {{ $quotation->client_decline_reason }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </x-admin.form-section>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <x-admin.button type="button" color="light" onclick="history.back()">
                        Cancel
                    </x-admin.button>
                    
                    @if($quotation->status === 'approved' && !$quotation->client_approved)
                        <x-admin.button 
                            type="button" 
                            color="success"
                            onclick="window.location.href='{{ route('admin.projects.create', ['from_quotation' => $quotation->id]) }}'"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Project
                        </x-admin.button>
                    @endif
                </div>
                
                <div class="flex items-center space-x-3">
                    <x-admin.button type="submit" name="action" value="save_and_continue" color="light">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Save & Continue Editing
                    </x-admin.button>
                    
                    <x-admin.button type="submit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Quotation
                    </x-admin.button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>