<!-- resources/views/admin/service-categories/edit.blade.php -->
<x-layouts.admin title="Edit Service Category" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Service Categories' => route('admin.service-categories.index'),
            'Edit Category' => route('admin.service-categories.edit', $serviceCategory),
        ]" />
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <form action="{{ route('admin.service-categories.destroy', $serviceCategory) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?')">
                @csrf
                @method('DELETE')
                <x-admin.button
                    type="submit"
                    color="danger"
                >
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Category
                </x-admin.button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.service-categories.update', $serviceCategory) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <x-admin.form-section title="Category Information" description="Update the details of the service category.">
            <div class="grid grid-cols-1 gap-6">
                <x-admin.input 
                    name="name" 
                    label="Category Name" 
                    placeholder="Enter category name" 
                    :value="$serviceCategory->name"
                    required 
                />
                
                <x-admin.textarea 
                    name="description" 
                    label="Description" 
                    placeholder="Enter category description" 
                    rows="3" 
                    :value="$serviceCategory->description"
                />
                
                <x-admin.image-uploader 
                    name="icon" 
                    label="Category Icon" 
                    accept=".jpg,.jpeg,.png,.svg" 
                    helper="Square icon image. Recommended size: 128x128px. Max 1MB." 
                    maxFileSize="1" 
                    aspectRatio="1:1" 
                    :preview="$serviceCategory->icon ? asset('storage/' . $serviceCategory->icon) : null"
                />
                
                <x-admin.toggle 
                    name="is_active" 
                    label="Active Status" 
                    :checked="$serviceCategory->is_active" 
                    helper="Set category as active/inactive" 
                />
            </div>
        </x-admin.form-section>

        <!-- Form Buttons -->
        <div class="flex justify-end mt-8 gap-3">
            <x-admin.button 
                href="{{ route('admin.service-categories.index') }}" 
                color="light" 
                type="button"
            >
                Cancel
            </x-admin.button>

            <x-admin.button 
                type="submit" 
                color="primary"
            >
                Update Category
            </x-admin.button>
        </div>
    </form>
</x-layouts.admin>