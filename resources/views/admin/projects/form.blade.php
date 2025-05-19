<!-- resources/views/admin/projects/form.blade.php -->
@props(['project' => null, 'clients' => [], 'categories' => [], 'action', 'method' => 'POST'])

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
                    <x-form.input 
                        name="title" 
                        label="Project Title" 
                        :value="$project->title ?? null" 
                        required 
                    />
                </div>
                
                <div>
                    <x-form.input 
                        name="slug" 
                        label="Slug" 
                        :value="$project->slug ?? null" 
                        helper="Leave empty to generate automatically from title"
                    />
                </div>
                
                <div class="lg:col-span-2">
                    <x-form.textarea 
                        name="excerpt" 
                        label="Short Description" 
                        :value="$project->excerpt ?? null" 
                        rows="2"
                        helper="A brief summary of the project (max 200 characters)"
                    />
                </div>
                
                <div class="lg:col-span-2">
                    <x-form.rich-editor 
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
                    <x-form.select 
                        name="client_id" 
                        label="Client" 
                        :options="$clients" 
                        :selected="$project->client_id ?? null" 
                        placeholder="Select a client"
                    />
                </div>
                
                <div>
                    <x-form.select 
                        name="category_id" 
                        label="Category" 
                        :options="$categories" 
                        :selected="$project->category_id ?? null" 
                        placeholder="Select a category"
                    />
                </div>
            </div>
        </x-admin.form-section>
        
        <!-- Timeline Section -->
        <x-admin.form-section title="Timeline" description="Set the project timeline.">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <x-form.date-picker 
                        name="start_date" 
                        label="Start Date" 
                        :value="$project->start_date ?? null"
                    />
                </div>
                
                <div>
                    <x-form.date-picker 
                        name="end_date" 
                        label="End Date" 
                        :value="$project->end_date ?? null"
                    />
                </div>
                
                <div>
                    <x-form.select 
                        name="status" 
                        label="Status" 
                        :options="[
                            'pending' => 'Pending', 
                            'in_progress' => 'In Progress', 
                            'completed' => 'Completed', 
                            'on_hold' => 'On Hold',
                            'cancelled' => 'Cancelled'
                        ]" 
                        :selected="$project->status ?? 'pending'" 
                        required
                    />
                </div>
                
                <div>
                    <x-form.checkbox 
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
                <x-form.file-input 
                    name="featured_image" 
                    label="Featured Image" 
                    accept="image/*" 
                    :preview="$project && $project->featured_image ? $project->featuredImageUrl : null" 
                    helper="Recommended size: 1200x800 pixels"
                >
                    Upload a main image for the project
                </x-form.file-input>
                
                <x-admin.image-gallery 
    :images="$formattedImages"
    :columns="$columns"
    :lightbox="$lightbox"
    aspectRatio="4:3"
    showActions="true"
>
    @if($project->images->count() > 0)
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
                <x-form.rich-editor 
                    name="challenge" 
                    label="Challenge" 
                    :value="$project->challenge ?? null" 
                    placeholder="Describe the challenge or problem that needed to be solved..."
                />
                
                <x-form.rich-editor 
                    name="solution" 
                    label="Solution" 
                    :value="$project->solution ?? null" 
                    placeholder="Explain how your company addressed the challenge..."
                />
                
                <x-form.rich-editor 
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
                <x-form.input 
                    name="meta_title" 
                    label="Meta Title" 
                    :value="$project->meta_title ?? null" 
                    helper="Leave empty to use project title"
                />
                
                <x-form.textarea 
                    name="meta_description" 
                    label="Meta Description" 
                    :value="$project->meta_description ?? null" 
                    rows="2"
                    helper="Brief description for search results (max 160 characters)"
                />
                
                <x-form.input 
                    name="meta_keywords" 
                    label="Meta Keywords" 
                    :value="$project->meta_keywords ?? null" 
                    helper="Comma-separated keywords related to the project"
                />
            </div>
        </x-admin.form-section>
        
        <!-- Form Buttons -->
        <div class="flex justify-end">
            <x-form.button cancelRoute="{{ route('admin.projects.index') }}" submitText="{{ $project ? 'Update Project' : 'Create Project' }}" />
        </div>
    </div>
</form>