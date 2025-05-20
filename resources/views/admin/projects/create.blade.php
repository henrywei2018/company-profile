<!-- resources/views/admin/projects/create.blade.php -->
@props([
    'project' => null, 
    'clients' => [], 
    'categories' => [], 
    'action' => route('admin.projects.store'), 
    'method' => 'POST',
    'formattedImages' => [],
    'columns' => 3,
    'lightbox' => true
])
<x-layouts.admin>
    <x-slot name="title">Create Project</x-slot>
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Projects Management' => route('admin.projects.index'),
            'Add New Service' => route('admin.projects.create'),
        ]" />
    </div>
    
    <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    
    <div class="space-y-6">
        <!-- Basic Information Section -->
        <x-admin.form-section title="Basic Information" description="Enter the basic details of the project.">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <x-admin.input 
                        name="title" 
                        label="Project Title" 
                        :value="$project->title ?? null" 
                        required 
                    />
                </div>
                
                <div>
                    <x-admin.input 
                        name="slug" 
                        label="Slug" 
                        :value="$project->slug ?? null" 
                        helper="Leave empty to generate automatically from title"
                    />
                </div>
                
                <div class="lg:col-span-2">
                    <x-admin.textarea 
                        name="excerpt" 
                        label="Short Description" 
                        :value="$project->excerpt ?? null" 
                        rows="2"
                        helper="A brief summary of the project (max 200 characters)"
                    />
                </div>
                
                <div class="lg:col-span-2">
                    <x-admin.rich-editor 
                        name="description" 
                        label="Full Description" 
                        :value="$project->description ?? null" 
                        required
                    />
                </div>
            </div>
        </x-admin.form-section>
        
        <!-- Client and Category Section -->
        <x-admin.form-section title="Client & Category" description="Associate the project with a client and category.">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <x-admin.select 
                        name="client_id" 
                        label="Client" 
                        :options="$clients" 
                        value="{{ $project->client_id ?? '' }}" 
                        placeholder="Select a client"
                    />
                </div>
                
                <div>
                    <x-admin.select 
                        name="category_id" 
                        label="Category" 
                        :options="$categories" 
                        value="{{ $project->category_id ?? '' }}" 
                        placeholder="Select a category"
                    />
                </div>
            </div>
        </x-admin.form-section>
        
        <!-- Timeline Section -->
        <x-admin.form-section title="Timeline" description="Set the project timeline.">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <x-admin.date-range-picker 
                        name="start_date" 
                        label="Start Date" 
                        :startDate="$project->start_date ?? null"
                        mode="single"
                    />
                </div>
                
                <div>
                    <x-admin.date-range-picker 
                        name="end_date" 
                        label="End Date" 
                        :startDate="$project->end_date ?? null"
                        mode="single"
                    />
                </div>
                
                <div>
                    <x-admin.select 
                        name="status" 
                        label="Status" 
                        :options="[
                            'pending' => 'Pending', 
                            'in_progress' => 'In Progress', 
                            'completed' => 'Completed', 
                            'on_hold' => 'On Hold',
                            'cancelled' => 'Cancelled'
                        ]" 
                        value="{{ $project->status ?? 'pending' }}" 
                        required
                    />
                </div>
                
                <div>
                    <x-admin.toggle 
                        name="is_featured" 
                        label="Feature this project" 
                        :checked="$project->is_featured ?? false" 
                        helper="Featured projects will be displayed prominently on the website"
                    />
                </div>
            </div>
        </x-admin.form-section>
        
        <!-- Images Section -->
        <x-admin.form-section title="Images" description="Upload images showcasing the project.">
            <div>
                <x-admin.image-uploader 
                    name="featured_image" 
                    label="Featured Image" 
                    accept=".jpg,.jpeg,.png,.webp" 
                    :preview="$project && $project->featured_image ? asset('storage/' . $project->featured_image) : null" 
                    helper="Recommended size: 1200x800 pixels"
                >
                    Upload a main image for the project
                </x-admin.image-uploader>
                
                <x-admin.image-gallery 
                    :images="$formattedImages"
                    :columns="$columns"
                    :lightbox="$lightbox"
                    aspectRatio="4:3"
                    showActions="true"
                >
                    @if(isset($project) && $project->images && $project->images->count() > 0)
                        <x-slot name="actions">
                            <!-- Custom action buttons for each image -->
                            <div class="flex gap-2">
                                <a href="#" class="p-2 bg-white/90 rounded-full text-gray-800 hover:bg-white transition-colors duration-200">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </x-slot>
                    @endif
                </x-admin.image-gallery>
            </div>
        </x-admin.form-section>
        
        <!-- Challenge & Solution Section -->
        <x-admin.form-section title="Challenge & Solution" description="Describe the project challenge and your solution.">
            <div class="space-y-6">
                <x-admin.rich-editor 
                    name="challenge" 
                    label="Challenge" 
                    :value="$project->challenge ?? null" 
                    placeholder="Describe the challenge or problem that needed to be solved..."
                />
                
                <x-admin.rich-editor 
                    name="solution" 
                    label="Solution" 
                    :value="$project->solution ?? null" 
                    placeholder="Explain how your company addressed the challenge..."
                />
                
                <x-admin.rich-editor 
                    name="results" 
                    label="Results" 
                    :value="$project->results ?? null" 
                    placeholder="Describe the outcomes and benefits of the project..."
                />
            </div>
        </x-admin.form-section>
        
        <!-- SEO Section -->
        <x-admin.form-section title="SEO Information" description="Optimize the project page for search engines.">
            <div class="space-y-6">
                <x-admin.input 
                    name="meta_title" 
                    label="Meta Title" 
                    :value="$project->meta_title ?? null" 
                    helper="Leave empty to use project title"
                />
                
                <x-admin.textarea 
                    name="meta_description" 
                    label="Meta Description" 
                    :value="$project->meta_description ?? null" 
                    rows="2"
                    helper="Brief description for search results (max 160 characters)"
                />
                
                <x-admin.input 
                    name="meta_keywords" 
                    label="Meta Keywords" 
                    :value="$project->meta_keywords ?? null" 
                    helper="Comma-separated keywords related to the project"
                />
            </div>
        </x-admin.form-section>
        
        <!-- Form Buttons -->
        <div class="flex justify-end gap-3">
            <x-admin.button
                href="{{ route('admin.projects.index') }}"
                color="light"
                type="button"
            >
                Cancel
            </x-admin.button>
            
            <x-admin.button
                type="submit"
                color="primary"
            >
                {{ $project ? 'Update Project' : 'Create Project' }}
            </x-admin.button>
        </div>
    </div>
</form>
</x-layouts.admin>