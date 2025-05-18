<!-- resources/views/admin/services/edit.blade.php -->
<x-layouts.admin title="Edit Service" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Services Management' => route('admin.services.index'),
            'Edit Service' => route('admin.services.edit', $service)
        ]" />
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <x-admin.button
                href="{{ route('services.show', $service->slug) }}"
                color="light"
                type="button"
                target="_blank"
            >
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View on Website
            </x-admin.button>
            
            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?')">
                @csrf
                @method('DELETE')
                <x-admin.button
                    type="submit"
                    color="danger"
                >
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Service
                </x-admin.button>
            </form>
        </div>
    </div>
    
    <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <x-admin.form-section title="Basic Information" description="Update the basic details of the service.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-admin.input
                        name="title"
                        label="Service Title"
                        placeholder="Enter service title"
                        :value="$service->title"
                        required
                    />
                </div>
                
                <div>
                    <x-admin.select
                        name="category_id"
                        label="Category"
                        :options="$categories->pluck('name', 'id')->toArray()"
                        placeholder="Select a category"
                        :value="$service->category_id"
                    />
                </div>
                
                <div>
                    <x-admin.input
                        name="slug"
                        label="Slug"
                        placeholder="service-slug"
                        :value="$service->slug"
                        helper="Leave empty to auto-generate from title."
                    />
                </div>
                
                <div class="md:col-span-2">
                    <x-admin.textarea
                        name="short_description"
                        label="Short Description"
                        placeholder="Enter a brief description (max 255 characters)"
                        rows="3"
                        :value="$service->short_description"
                    />
                </div>
                
                <div class="md:col-span-2">
                    <x-admin.rich-editor
                        name="description"
                        label="Full Description"
                        placeholder="Enter detailed service description"
                        :value="$service->description"
                    />
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <x-admin.toggle
                        name="is_active"
                        label="Active Status"
                        :checked="$service->is_active"
                        helper="Set service as active/inactive"
                    />
                </div>
                
                <div>
                    <x-admin.toggle
                        name="featured"
                        label="Featured Service"
                        :checked="$service->featured"
                        helper="Display this service prominently on the website"
                    />
                </div>
                
                <div>
                    <x-admin.input
                        name="sort_order"
                        label="Sort Order"
                        type="number"
                        placeholder="0"
                        :value="$service->sort_order"
                        helper="Lower numbers appear first"
                    />
                </div>
            </div>
        </x-admin.form-section>
        
        <!-- Media -->
        <x-admin.form-section title="Media" description="Update service images and icons." class="mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-admin.image-uploader
                        name="image"
                        label="Service Image"
                        accept=".jpg,.jpeg,.png,.webp"
                        helper="Recommended size: 1200x800px. Max 2MB."
                        maxFileSize="2"
                        :preview="$service->image ? asset('storage/' . $service->image) : null"
                    />
                </div>
                
                <div>
                    <x-admin.image-uploader
                        name="icon"
                        label="Service Icon"
                        accept=".jpg,.jpeg,.png,.svg"
                        helper="Square icon image. Recommended size: 128x128px. Max 1MB."
                        maxFileSize="1"
                        aspectRatio="1:1"
                        :preview="$service->icon ? asset('storage/' . $service->icon) : null"
                    />
                </div>
            </div>
        </x-admin.form-section>
        
        <!-- SEO Information -->
        <x-admin.form-section title="SEO Information" description="Optimize service for search engines." class="mt-8">
            <div class="grid grid-cols-1 gap-6">
                <x-admin.input
                    name="meta_title"
                    label="Meta Title"
                    placeholder="Enter meta title"
                    :value="$service->seo->title ?? $service->title"
                    helper="Leave empty to use service title. Recommended length: 50-60 characters."
                />
                
                <x-admin.textarea
                    name="meta_description"
                    label="Meta Description"
                    placeholder="Enter meta description"
                    rows="3"
                    :value="$service->seo->description ?? $service->short_description"
                    helper="Brief description for search results. Recommended length: 150-160 characters."
                />
                
                <x-admin.input
                    name="meta_keywords"
                    label="Meta Keywords"
                    placeholder="keyword1, keyword2, keyword3"
                    :value="$service->seo->keywords ?? ''"
                    helper="Comma-separated keywords (optional)."
                />
            </div>
        </x-admin.form-section>
        
        <!-- Form Buttons -->
        <div class="flex justify-end mt-8 gap-3">
            <x-admin.button
                href="{{ route('admin.services.index') }}"
                color="light"
                type="button"
            >
                Cancel
            </x-admin.button>
            
            <x-admin.button
                type="submit"
                color="primary"
            >
                Update Service
            </x-admin.button>
        </div>
    </form>
</x-layouts.admin>