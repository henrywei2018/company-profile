{{-- resources/views/components/admin/project-form.blade.php --}}
@props([
    'project' => null,
    'clients' => [],
    'categories' => [],
    'services' => [],
    'action' => '',
    'method' => 'POST',
    'quotation' => null
])

<div x-data="projectFormData()" x-init="initializeForm()">
    <form :action="formAction" method="POST" enctype="multipart/form-data" @submit="handleSubmit">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif
        
        {{-- Hidden Fields --}}
        @if($quotation)
            <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
        @endif
        
        <div class="space-y-8">
            {{-- Basic Information Section --}}
            <x-admin.form-section 
                title="Project Information" 
                description="Enter the basic project details and timeline"
            >
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="lg:col-span-2">
                        <x-admin.input
                            name="title"
                            label="Project Title"
                            placeholder="Enter project title"
                            :value="old('title', $project->title ?? ($quotation->project_type ?? ''))"
                            required
                            x-model="formData.title"
                            @input="generateSlug"
                        />
                    </div>
                    
                    <div>
                        <x-admin.input
                            name="slug"
                            label="URL Slug"
                            placeholder="auto-generated-from-title"
                            :value="old('slug', $project->slug ?? '')"
                            x-model="formData.slug"
                            helper="Leave empty to auto-generate from title"
                        />
                    </div>
                    
                    <div>
                        <x-admin.input
                            name="location"
                            label="Project Location"
                            placeholder="e.g., Jakarta, Indonesia"
                            :value="old('location', $project->location ?? ($quotation->location ?? ''))"
                            x-model="formData.location"
                        />
                    </div>
                    
                    <div>
                        <x-admin.select
                            name="client_id"
                            label="Client"
                            :options="$clients->pluck('name', 'id')->prepend('Select Client', '')"
                            :value="old('client_id', $project->client_id ?? ($quotation->client_id ?? ''))"
                            x-model="formData.client_id"
                        />
                    </div>
                    
                    <div>
                        <x-admin.select
                            name="project_category_id"
                            label="Category"
                            :options="$categories->pluck('name', 'id')->prepend('Select Category', '')"
                            :value="old('project_category_id', $project->project_category_id ?? '')"
                            x-model="formData.category_id"
                        />
                    </div>
                    
                    <div class="lg:col-span-2">
                        <x-admin.rich-editor
                            name="description"
                            label="Project Description"
                            :value="old('description', $project->description ?? ($quotation->requirements ?? ''))"
                            placeholder="Detailed description of the project..."
                            required
                        />
                    </div>
                </div>
            </x-admin.form-section>

            {{-- Timeline & Status Section --}}
            <x-admin.form-section 
                title="Timeline & Status" 
                description="Set project timeline, status, and priority"
            >
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <x-admin.date-range-picker
                            name="project_dates"
                            label="Project Timeline"
                            :startDate="old('start_date', $project->start_date ?? ($quotation->start_date ?? null))"
                            :endDate="old('end_date', $project->end_date ?? null)"
                            mode="range"
                            helper="Select start and end dates for the project"
                        />
                    </div>
                    
                    <div class="space-y-4">
                        <x-admin.select
                            name="status"
                            label="Project Status"
                            :options="[
                                'planning' => 'Planning',
                                'in_progress' => 'In Progress', 
                                'on_hold' => 'On Hold',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled'
                            ]"
                            :value="old('status', $project->status ?? 'planning')"
                            required
                            x-model="formData.status"
                        />
                        
                        <x-admin.select
                            name="priority"
                            label="Priority Level"
                            :options="[
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High', 
                                'urgent' => 'Urgent'
                            ]"
                            :value="old('priority', $project->priority ?? 'normal')"
                            x-model="formData.priority"
                        />
                    </div>
                    
                    <div>
                        <x-admin.input
                            name="budget"
                            label="Project Budget"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            :value="old('budget', $project->budget ?? ($quotation->estimated_cost ?? ''))"
                            x-model="formData.budget"
                            helper="Project budget in your default currency"
                        />
                    </div>
                    
                    <div>
                        <x-admin.input
                            name="progress_percentage"
                            label="Progress (%)"
                            type="number"
                            min="0"
                            max="100"
                            :value="old('progress_percentage', $project->progress_percentage ?? 0)"
                            x-model="formData.progress"
                            helper="Current project completion percentage"
                        />
                        
                        {{-- Progress Indicator --}}
                        <div class="mt-2">
                            <x-admin.progress 
                                :value="0" 
                                x-bind:value="formData.progress"
                                height="sm"
                                showLabel="true"
                                color="blue"
                            />
                        </div>
                    </div>
                </div>
                
                {{-- Status-dependent fields --}}
                <div x-show="formData.status === 'completed'" x-transition class="mt-6">
                    <x-admin.input
                        name="actual_completion_date"
                        label="Actual Completion Date"
                        type="date"
                        :value="old('actual_completion_date', $project->actual_completion_date?->format('Y-m-d') ?? '')"
                    />
                </div>
            </x-admin.form-section>

            {{-- Image Gallery Section --}}
            <x-admin.form-section 
                title="Project Images" 
                description="Upload and manage project images"
            >
                @if($project && $project->images->count() > 0)
                    {{-- Existing Images Management --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Current Images</h4>
                        <x-admin.image-gallery
                            name="existing_images"
                            :images="$project->images->map(fn($img) => [
                                'id' => $img->id,
                                'url' => $img->image_url,
                                'alt' => $img->alt_text,
                                'featured' => $img->is_featured
                            ])"
                            :columns="4"
                            :lightbox="true"
                            :showRemoveButton="true"
                            :showFeaturedToggle="true"
                            :showAltTextField="true"
                        />
                    </div>
                @endif
                
                {{-- New Image Upload --}}
                <x-admin.image-uploader
                    name="new_images"
                    label="Upload New Images"
                    :multiple="true"
                    :maxFiles="10"
                    accept=".jpg,.jpeg,.png,.webp"
                    helper="Upload multiple project images. First image will be set as featured if no featured image exists."
                />
            </x-admin.form-section>

            {{-- Project Details Section --}}
            <x-admin.form-section 
                title="Project Details" 
                description="Challenge, solution, and results information"
            >
                <div class="space-y-6">
                    <x-admin.rich-editor
                        name="challenge"
                        label="Challenge"
                        :value="old('challenge', $project->challenge ?? '')"
                        placeholder="What challenges did this project address?"
                        minHeight="150px"
                    />
                    
                    <x-admin.rich-editor
                        name="solution"
                        label="Solution"
                        :value="old('solution', $project->solution ?? '')"
                        placeholder="How did you solve these challenges?"
                        minHeight="150px"
                    />
                    
                    <x-admin.rich-editor
                        name="results"
                        label="Results"
                        :value="old('results', $project->results ?? '')"
                        placeholder="What were the outcomes and benefits?"
                        minHeight="150px"
                    />
                </div>
            </x-admin.form-section>

            {{-- Services & Technologies --}}
            <x-admin.form-section 
                title="Services & Technologies" 
                description="Select services used and technologies involved"
            >
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Services Used
                        </label>
                        <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                            @foreach($services as $service)
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="services_used[]" 
                                        value="{{ $service->title }}"
                                        @if($project && $project->services_used && in_array($service->title, $project->services_used))
                                            checked
                                        @elseif($quotation && $quotation->service_id == $service->id)
                                            checked
                                        @endif
                                        class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $service->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div>
                        <x-admin.textarea
                            name="technologies_used"
                            label="Technologies Used"
                            :value="old('technologies_used', $project ? implode(', ', $project->technologies_used ?? []) : '')"
                            placeholder="e.g., Laravel, Vue.js, MySQL, Redis"
                            rows="4"
                            helper="Comma-separated list of technologies"
                        />
                    </div>
                </div>
            </x-admin.form-section>

            {{-- Settings Section --}}
            <x-admin.form-section 
                title="Project Settings" 
                description="Visibility and feature settings"
            >
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <x-admin.toggle
                            name="featured"
                            label="Featured Project"
                            :checked="old('featured', $project->featured ?? false)"
                            helper="Featured projects are highlighted on the website"
                        />
                        
                        <x-admin.toggle
                            name="is_active"
                            label="Active Project"
                            :checked="old('is_active', $project->is_active ?? true)"
                            helper="Inactive projects are hidden from public view"
                        />
                    </div>
                    
                    <div>
                        <x-admin.input
                            name="year"
                            label="Project Year"
                            type="number"
                            min="2000"
                            :max="date('Y') + 5"
                            :value="old('year', $project->year ?? date('Y'))"
                            helper="Year when the project was completed"
                        />
                    </div>
                </div>
            </x-admin.form-section>

            {{-- SEO Section --}}
            <x-admin.form-section 
                title="SEO Settings" 
                description="Search engine optimization settings"
            >
                <div class="space-y-4">
                    <x-admin.input
                        name="meta_title"
                        label="Meta Title"
                        :value="old('meta_title', $project->meta_title ?? '')"
                        helper="Leave empty to use project title"
                        maxlength="60"
                    />
                    
                    <x-admin.textarea
                        name="meta_description"
                        label="Meta Description"
                        :value="old('meta_description', $project->meta_description ?? '')"
                        rows="3"
                        maxlength="160"
                        helper="Brief description for search results (max 160 characters)"
                    />
                    
                    <x-admin.input
                        name="meta_keywords"
                        label="Meta Keywords"
                        :value="old('meta_keywords', $project->meta_keywords ?? '')"
                        helper="Comma-separated keywords for SEO"
                    />
                </div>
            </x-admin.form-section>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
            <x-admin.button 
                href="{{ route('admin.projects.index') }}" 
                color="light"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancel
            </x-admin.button>
            
            <div class="flex space-x-3">
                @if($project)
                    <x-admin.button 
                        type="submit" 
                        name="action" 
                        value="save_and_continue" 
                        color="light"
                    >
                        Save & Continue Editing
                    </x-admin.button>
                @endif
                
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $project ? 'Update Project' : 'Create Project' }}
                </x-admin.button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function projectFormData() {
    return {
        formData: {
            title: @js(old('title', $project->title ?? ($quotation->project_type ?? ''))),
            slug: @js(old('slug', $project->slug ?? '')),
            status: @js(old('status', $project->status ?? 'planning')),
            priority: @js(old('priority', $project->priority ?? 'normal')),
            progress: @js(old('progress_percentage', $project->progress_percentage ?? 0)),
            budget: @js(old('budget', $project->budget ?? ($quotation->estimated_cost ?? ''))),
            location: @js(old('location', $project->location ?? ($quotation->location ?? ''))),
            client_id: @js(old('client_id', $project->client_id ?? ($quotation->client_id ?? ''))),
            category_id: @js(old('project_category_id', $project->project_category_id ?? ''))
        },
        formAction: @js($action),
        
        initializeForm() {
            // Initialize any form-specific logic
            this.updateProgressIndicator();
        },
        
        generateSlug() {
            if (!this.formData.slug || this.formData.slug === '') {
                this.formData.slug = this.formData.title
                    .toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
            }
        },
        
        updateProgressIndicator() {
            // Update progress bar color based on percentage
            const progress = parseInt(this.formData.progress);
            if (progress >= 100) {
                this.formData.status = 'completed';
            }
        },
        
        handleSubmit(event) {
            // Add any form validation or processing here
            console.log('Form submitted with data:', this.formData);
        }
    }
}
</script>
@endpush