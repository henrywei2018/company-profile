{{-- resources/views/admin/projects/create-from-quotation.blade.php --}}
<x-layouts.admin title="Create Project from Quotation">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Projects' => route('admin.projects.index'),
            'Create from Quotation' => '#'
        ]" />
    </div>

    <!-- Quotation Info Card -->
    <x-admin.card class="mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-2">
                Creating Project from Quotation #{{ $quotation->quotation_number ?? $quotation->id }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-blue-700 dark:text-blue-400">Client:</span>
                    <span class="font-medium text-blue-900 dark:text-blue-300">{{ $quotation->name }}</span>
                </div>
                <div>
                    <span class="text-blue-700 dark:text-blue-400">Project Type:</span>
                    <span class="font-medium text-blue-900 dark:text-blue-300">{{ $quotation->project_type }}</span>
                </div>
                <div>
                    <span class="text-blue-700 dark:text-blue-400">Status:</span>
                    <x-admin.badge type="success">{{ ucfirst($quotation->status) }}</x-admin.badge>
                </div>
            </div>
        </div>
    </x-admin.card>

    <!-- Project Form -->
    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Hidden quotation reference -->
        <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
        
        <!-- Basic Information -->
        <x-admin.form-section title="Basic Information" description="Project details pre-filled from quotation">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input 
                    label="Project Title" 
                    name="title" 
                    :value="old('title', $quotation->project_type)"
                    required
                />
                
                <x-admin.input 
                    label="Slug" 
                    name="slug" 
                    :value="old('slug', Str::slug($quotation->project_type))"
                    helper="URL-friendly version of the title"
                />
                
                <x-admin.select 
                    label="Client" 
                    name="client_id" 
                    :value="old('client_id', $quotation->client_id)"
                    :options="['' => 'Select Client'] + $clients->pluck('name', 'id')->toArray()"
                />
                
                <x-admin.input 
                    label="Client Name (Display)" 
                    name="client_name" 
                    :value="old('client_name', $quotation->client?->name ?? $quotation->name)"
                />
                
                <x-admin.input 
                    label="Location" 
                    name="location" 
                    :value="old('location', $quotation->location)"
                />
                
                <x-admin.select 
                    label="Category" 
                    name="category_id" 
                    :value="old('category_id')"
                    :options="['' => 'Select Category'] + $categories->pluck('name', 'id')->toArray()"
                />
            </div>
            
            <x-admin.textarea 
                label="Description" 
                name="description" 
                :value="old('description', $quotation->requirements)"
                rows="4"
                required
            />
        </x-admin.form-section>

        <!-- Project Details -->
        <x-admin.form-section title="Project Details" description="Timeline and budget information">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input 
                    label="Start Date" 
                    name="start_date" 
                    type="date"
                    :value="old('start_date', $quotation->start_date?->format('Y-m-d'))"
                />
                
                <x-admin.input 
                    label="End Date" 
                    name="end_date" 
                    type="date"
                    :value="old('end_date')"
                    helper="Estimate based on timeline: {{ $quotation->estimated_timeline }}"
                />
                
                <x-admin.input 
                    label="Project Value" 
                    name="value" 
                    :value="old('value', $quotation->estimated_cost ?? $quotation->budget_range)"
                    helper="From quotation estimate"
                />
                
                <x-admin.select 
                    label="Status" 
                    name="status" 
                    :value="old('status', 'planning')"
                    :options="[
                        'planning' => 'Planning',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'on_hold' => 'On Hold'
                    ]"
                />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input 
                    label="Year" 
                    name="year" 
                    type="number"
                    :value="old('year', date('Y'))"
                    min="2000"
                    max="{{ date('Y') + 5 }}"
                />
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Services Used
                    </label>
                    <div class="space-y-2">
                        @if($quotation->service)
                            <label class="flex items-center">
                                <input type="checkbox" name="services_used[]" value="{{ $quotation->service->title }}" 
                                       checked class="rounded text-blue-600">
                                <span class="ml-2 text-sm">{{ $quotation->service->title }}</span>
                            </label>
                        @endif
                        @foreach($services as $service)
                            @if(!$quotation->service || $service->id !== $quotation->service_id)
                                <label class="flex items-center">
                                    <input type="checkbox" name="services_used[]" value="{{ $service->title }}" 
                                           class="rounded text-blue-600">
                                    <span class="ml-2 text-sm">{{ $service->title }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </x-admin.form-section>

        <!-- Additional Information -->
        <x-admin.form-section title="Additional Information" description="Optional project details">
            <x-admin.textarea 
                label="Challenge" 
                name="challenge" 
                :value="old('challenge')"
                rows="3"
                placeholder="What challenges did this project address?"
            />
            
            <x-admin.textarea 
                label="Solution" 
                name="solution" 
                :value="old('solution')"
                rows="3"
                placeholder="How did we solve these challenges?"
            />
            
            <x-admin.textarea 
                label="Result" 
                name="result" 
                :value="old('result')"
                rows="3"
                placeholder="What were the outcomes?"
            />
            
            <x-admin.checkbox 
                label="Featured Project" 
                name="featured" 
                :checked="old('featured', false)"
                helper="Display this project prominently on the website"
            />
        </x-admin.form-section>

        <!-- Copy Quotation Attachments -->
        @if($quotation->attachments->count() > 0)
            <x-admin.form-section title="Quotation Attachments" description="Select attachments to copy to project">
                <div class="space-y-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        The following files were attached to the quotation. Select which ones to include in the project:
                    </p>
                    @foreach($quotation->attachments as $attachment)
                        <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                            <input type="checkbox" name="copy_attachments[]" value="{{ $attachment->id }}" 
                                   class="rounded text-blue-600">
                            <div class="ml-3">
                                <span class="text-sm font-medium">{{ $attachment->file_name }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $attachment->file_size_formatted }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </x-admin.form-section>
        @endif

        <!-- Form Actions -->
        <div class="flex items-center justify-between mt-6">
            <x-admin.button href="{{ route('admin.quotations.show', $quotation) }}" color="light">
                Back to Quotation
            </x-admin.button>
            
            <div class="flex space-x-3">
                <x-admin.button type="submit" name="action" value="save_and_continue" color="light">
                    Save & Continue Editing
                </x-admin.button>
                
                <x-admin.button type="submit">
                    Create Project
                </x-admin.button>
            </div>
        </div>
    </form>
</x-layouts.admin>