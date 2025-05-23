<!-- resources/views/admin/quotations/edit.blade.php -->
<x-layouts.admin title="Edit Quotation" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <x-admin.breadcrumb :items="[
                'Quotations' => route('admin.quotations.index'),
                'Quotation #' . $quotation->id => route('admin.quotations.show', $quotation),
                'Edit' => '#'
            ]" />
        </div>
        
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('admin.quotations.show', $quotation) }}" color="light" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Details
            </x-admin.button>
        </div>
    </div>

    <form action="{{ route('admin.quotations.update', $quotation) }}" method="POST" class="space-y-8" x-data="quotationForm()">
        @csrf
        @method('PUT')
        
        <!-- Header Card -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Edit Quotation #{{ $quotation->id }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                            Update quotation details and manage status
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
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
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    
                    <x-admin.select 
                        label="Priority" 
                        name="priority" 
                        :value="$quotation->priority"
                        :options="[
                            'low' => 'Low',
                            'normal' => 'Normal',
                            'high' => 'High',
                            'urgent' => 'Urgent'
                        ]"
                    />
                    
                    <x-admin.select 
                        label="Source" 
                        name="source" 
                        :value="$quotation->source"
                        :options="[
                            '' => 'Select Source',
                            'website' => 'Website Form',
                            'phone' => 'Phone Call',
                            'email' => 'Email',
                            'referral' => 'Referral',
                            'social_media' => 'Social Media'
                        ]"
                    />
                </div>
            </div>
        </x-admin.card>

        <!-- Client Information -->
        <x-admin.form-section 
            title="Client Information" 
            description="Basic contact information and client linking"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input 
                    label="Full Name" 
                    name="name" 
                    :value="$quotation->name"
                    required
                    placeholder="Enter client's full name"
                />
                
                <x-admin.input 
                    label="Email Address" 
                    name="email" 
                    type="email"
                    :value="$quotation->email"
                    required
                    placeholder="client@example.com"
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
                    placeholder="Company name (optional)"
                />
            </div>
            
            <div class="mt-6">
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
                    placeholder="e.g., Website Development, Construction"
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <x-admin.input 
                    label="Desired Start Date" 
                    name="start_date" 
                    type="date"
                    :value="$quotation->start_date?->format('Y-m-d')"
                />
                
                <div class="md:pt-6">
                    <!-- Spacer for alignment -->
                </div>
            </div>
            
            <div class="mt-6">
                <x-admin.textarea 
                    label="Project Requirements" 
                    name="requirements" 
                    :value="$quotation->requirements"
                    rows="5"
                    placeholder="Detailed description of project requirements..."
                />
            </div>
        </x-admin.form-section>

        <!-- Admin Response & Estimates -->
        <x-admin.form-section 
            title="Admin Response & Estimates" 
            description="Cost estimates and timeline information"
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
        </x-admin.form-section>

        <!-- Notes & Communication -->
        <x-admin.form-section 
            title="Notes & Communication" 
            description="Internal notes and client-facing information"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.textarea 
                    label="Internal Notes" 
                    name="internal_notes" 
                    :value="$quotation->internal_notes"
                    rows="5"
                    helper="These notes are for internal use only and won't be visible to the client"
                    placeholder="Internal team notes, reminders, etc."
                />
                
                <x-admin.textarea 
                    label="Client Notes" 
                    name="admin_notes" 
                    :value="$quotation->admin_notes"
                    rows="5"
                    helper="These notes may be shared with the client in communications"
                    placeholder="Notes that can be included in client communications"
                />
            </div>
            
            <div class="mt-6">
                <x-admin.textarea 
                    label="Additional Information" 
                    name="additional_info" 
                    :value="$quotation->additional_info"
                    rows="3"
                    placeholder="Any additional notes or information..."
                />
            </div>
        </x-admin.form-section>

        <!-- Client Response Information (Read-only if exists) -->
        @if($quotation->client_approved !== null)
            <x-admin.form-section 
                title="Client Response" 
                description="Client's response to this quotation"
            >
                <div class="bg-gray-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Client Decision</h4>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                @if($quotation->client_approved_at)
                                    Responded on {{ $quotation->client_approved_at->format('F j, Y \a\t g:i A') }}
                                @endif
                            </p>
                        </div>
                        <x-admin.badge :type="$quotation->client_approved ? 'success' : 'danger'" size="lg">
                            {{ $quotation->client_approved ? 'Approved' : 'Declined' }}
                        </x-admin.badge>
                    </div>
                    
                    @if(!$quotation->client_approved && $quotation->client_decline_reason)
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Decline Reason</h5>
                            <p class="text-sm text-red-700 dark:text-red-300">{{ $quotation->client_decline_reason }}</p>
                        </div>
                    @endif
                </div>
            </x-admin.form-section>
        @endif

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-neutral-700">
            <div class="flex items-center space-x-3">
                <x-admin.button type="button" color="light" onclick="history.back()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </x-admin.button>
                
                @if($quotation->status === 'approved' && !$quotation->hasProject())
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
                
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Quotation
                </x-admin.button>
            </div>
        </div>
    </form>

    <!-- Unsaved Changes Warning -->
    <div x-show="hasUnsavedChanges" 
         x-transition
         class="fixed bottom-4 right-4 bg-amber-100 border border-amber-400 text-amber-700 px-4 py-3 rounded-lg shadow-lg dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400"
         style="display: none;">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-sm font-medium">You have unsaved changes</span>
        </div>
    </div>
</x-layouts.admin>

<script>
    function quotationForm() {
        return {
            hasUnsavedChanges: false,
            originalData: {},
            
            init() {
                // Capture original form data
                this.captureOriginalData();
                
                // Watch for changes
                this.$el.addEventListener('input', () => {
                    this.checkForChanges();
                });
                
                this.$el.addEventListener('change', () => {
                    this.checkForChanges();
                });
                
                // Warn before leaving if there are unsaved changes
                window.addEventListener('beforeunload', (e) => {
                    if (this.hasUnsavedChanges) {
                        e.preventDefault();
                        e.returnValue = '';
                    }
                });
            },
            
            captureOriginalData() {
                const formData = new FormData(this.$el);
                this.originalData = {};
                for (let [key, value] of formData.entries()) {
                    this.originalData[key] = value;
                }
            },
            
            checkForChanges() {
                const formData = new FormData(this.$el);
                let hasChanges = false;
                
                for (let [key, value] of formData.entries()) {
                    if (this.originalData[key] !== value) {
                        hasChanges = true;
                        break;
                    }
                }
                
                this.hasUnsavedChanges = hasChanges;
            }
        }
    }
</script>

<style>
    /* Ensure form sections have consistent spacing */
    .form-section + .form-section {
        margin-top: 2rem;
    }
    
    /* Enhanced focus states for better accessibility */
    input:focus, 
    select:focus, 
    textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Smooth transitions for status badges */
    .badge {
        transition: all 0.2s ease-in-out;
    }
    
    /* Better visual hierarchy for form sections */
    .form-section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .dark .form-section-title {
        color: #f9fafb;
    }
</style>