{{-- resources/views/admin/quotations/convert-to-project.blade.php --}}
<x-layouts.admin title="Convert to Project" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <x-admin.breadcrumb :items="[
                'Quotations' => route('admin.quotations.index'),
                'Quotation #' . $quotation->id => route('admin.quotations.show', $quotation),
                'Convert to Project' => '#'
            ]" />
        </div>
        
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('admin.quotations.show', $quotation) }}" color="light" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Quotation
            </x-admin.button>
        </div>
    </div>

    <form action="{{ route('admin.quotations.convert-to-project', $quotation) }}" method="POST" class="space-y-8" x-data="projectConversionForm()">
        @csrf
        
        <!-- Header Card -->
        <x-admin.card>
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Convert Quotation to Project
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                            Transform this approved quotation into a manageable project
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <x-admin.badge type="success" size="lg">
                            Quotation #{{ $quotation->id }}
                        </x-admin.badge>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap gap-3 mb-6">
                    <button type="button" 
                            @click="useQuickConversion()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Quick Convert (Use Defaults)
                    </button>
                    
                    <button type="button" 
                            @click="fillSuggestedData()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Fill Suggested Data
                    </button>
                </div>
                
                <!-- Original Quotation Summary -->
                <div class="bg-gray-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-neutral-300 mb-3">Original Quotation Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-neutral-500">Client:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $quotation->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-neutral-500">Project Type:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $quotation->project_type }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-neutral-500">Budget Range:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $quotation->budget_range ?: 'Not specified' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.card>

        <!-- Project Information -->
        <x-admin.form-section 
            title="Project Information" 
            description="Configure the basic project details"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input 
                    label="Project Title" 
                    name="project_title" 
                    :value="old('project_title', $suggestedData['title'])"
                    required
                    placeholder="Enter project title"
                    x-model="projectTitle"
                />
                
                <x-admin.select 
                    label="Project Category" 
                    name="project_category_id" 
                    :value="old('project_category_id')"
                    :options="['' => 'Select a category'] + $categories->pluck('name', 'id')->toArray()"
                    x-model="projectCategory"
                />
                
                <x-admin.input 
                    label="Location" 
                    name="location" 
                    :value="old('location', $suggestedData['location'])"
                    placeholder="Project location"
                    x-model="projectLocation"
                />
                
                <x-admin.select 
                    label="Priority" 
                    name="priority" 
                    :value="old('priority', $suggestedData['priority'])"
                    :options="[
                        'low' => 'Low Priority',
                        'normal' => 'Normal Priority',
                        'high' => 'High Priority',
                        'urgent' => 'Urgent'
                    ]"
                    x-model="projectPriority"
                />
            </div>
            
            <div class="mt-6">
                <x-admin.textarea 
                    label="Project Description" 
                    name="project_description" 
                    :value="old('project_description', $suggestedData['description'])"
                    rows="5"
                    placeholder="Detailed project description..."
                    x-model="projectDescription"
                />
            </div>
        </x-admin.form-section>

        <!-- Timeline & Budget -->
        <x-admin.form-section 
            title="Timeline & Budget" 
            description="Set project timeline and budget information"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-admin.input 
                    label="Start Date" 
                    name="start_date" 
                    type="date"
                    :value="old('start_date', $suggestedData['start_date'])"
                    x-model="startDate"
                />
                
                <x-admin.input 
                    label="Estimated Completion Date" 
                    name="estimated_completion_date" 
                    type="date"
                    :value="old('estimated_completion_date')"
                    x-model="estimatedCompletionDate"
                />
                
                <x-admin.input 
                    label="Project Budget" 
                    name="budget" 
                    type="number"
                    step="0.01"
                    :value="old('budget', $suggestedData['budget'])"
                    placeholder="0.00"
                    x-model="projectBudget"
                />
            </div>
        </x-admin.form-section>

        <!-- Additional Options -->
        <x-admin.form-section 
            title="Additional Options" 
            description="Configure additional project settings"
        >
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="create_initial_milestone" 
                           id="create_initial_milestone"
                           checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="create_initial_milestone" class="ml-2 block text-sm text-gray-700 dark:text-neutral-300">
                        Create initial "Project Initiation" milestone
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="copy_attachments" 
                           id="copy_attachments"
                           @if($quotation->attachments->count() > 0) checked @endif
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="copy_attachments" class="ml-2 block text-sm text-gray-700 dark:text-neutral-300">
                        Copy quotation attachments to project ({{ $quotation->attachments->count() }} files)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="notify_client" 
                           id="notify_client"
                           @if($quotation->client) checked @endif
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="notify_client" class="ml-2 block text-sm text-gray-700 dark:text-neutral-300">
                        Send project notification to client
                        @if(!$quotation->client)
                            <span class="text-gray-500">(Client account required)</span>
                        @endif
                    </label>
                </div>
            </div>
        </x-admin.form-section>

        <!-- Conversion Preview -->
        <x-admin.card title="Conversion Preview">
            <div class="p-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-400 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        What will happen:
                    </h4>
                    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                        <li>• A new project will be created with the specified details</li>
                        <li>• The quotation will be marked as "converted to project"</li>
                        <li>• Project status will be set to "Planning"</li>
                        <li>• All quotation data will remain accessible</li>
                        @if($quotation->attachments->count() > 0)
                            <li>• {{ $quotation->attachments->count() }} attachment(s) will be copied to the project</li>
                        @endif
                        @if($quotation->client)
                            <li>• Client will be automatically linked to the project</li>
                        @endif
                    </ul>
                </div>
            </div>
        </x-admin.card>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-neutral-700">
            <x-admin.button type="button" color="light" onclick="history.back()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </x-admin.button>
            
            <div class="flex items-center space-x-3">
                <x-admin.button 
                    type="button" 
                    color="success"
                    @click="quickConvert()"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Quick Convert
                </x-admin.button>
                
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Project
                </x-admin.button>
            </div>
        </div>
    </form>

    <!-- Loading Modal -->
    <div x-show="isConverting" 
         x-transition
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         style="display: none;">
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
    function projectConversionForm() {
        return {
            isConverting: false,
            projectTitle: @json(old('project_title', $suggestedData['title'])),
            projectCategory: @json(old('project_category_id')),
            projectLocation: @json(old('location', $suggestedData['location'])),
            projectPriority: @json(old('priority', $suggestedData['priority'])),
            projectDescription: @json(old('project_description', $suggestedData['description'])),
            startDate: @json(old('start_date', $suggestedData['start_date'])),
            estimatedCompletionDate: @json(old('estimated_completion_date')),
            projectBudget: @json(old('budget', $suggestedData['budget'])),
            
            suggestedData: @json($suggestedData),
            
            fillSuggestedData() {
                this.projectTitle = this.suggestedData.title;
                this.projectLocation = this.suggestedData.location;
                this.projectPriority = this.suggestedData.priority;
                this.projectDescription = this.suggestedData.description;
                this.startDate = this.suggestedData.start_date;
                this.projectBudget = this.suggestedData.budget;
                
                // Auto-calculate estimated completion (3 months from start)
                if (this.startDate) {
                    const start = new Date(this.startDate);
                    start.setMonth(start.getMonth() + 3);
                    this.estimatedCompletionDate = start.toISOString().split('T')[0];
                }
            },
            
            useQuickConversion() {
                if (confirm('This will create a project using the quotation data with minimal customization. Continue?')) {
                    this.fillSuggestedData();
                    this.quickConvert();
                }
            },
            
            async quickConvert() {
                this.isConverting = true;
                
                try {
                    const response = await fetch(`{{ route('admin.quotations.quick-convert', $quotation) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Show success message and redirect
                        this.$dispatch('notify', {
                            type: 'success',
                            message: data.message
                        });
                        
                        setTimeout(() => {
                            window.location.href = data.project_url;
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Conversion failed');
                    }
                } catch (error) {
                    this.$dispatch('notify', {
                        type: 'error',
                        message: error.message || 'Failed to convert quotation'
                    });
                } finally {
                    this.isConverting = false;
                }
            }
        }
    }
</script>

<style>
    /* Enhanced form styling */
    .form-section {
        transition: all 0.2s ease-in-out;
    }
    
    .form-section:hover {
        transform: translateY(-1px);
    }
    
    /* Loading animation */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Better focus states */
    input:focus, 
    select:focus, 
    textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>