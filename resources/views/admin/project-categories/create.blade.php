<!-- resources/views/admin/project-categories/create.blade.php -->
<x-layouts.admin title="Add New Project Category" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Project Categories' => route('admin.project-categories.index'),
            'Add New Category' => route('admin.project-categories.create'),
        ]" />
    </div>

    <form action="{{ route('admin.project-categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information -->
        <x-admin.form-section title="Category Information" description="Enter the basic details of the project category.">
            <div class="grid grid-cols-1 gap-6">
                <x-admin.input 
                    name="name" 
                    label="Category Name" 
                    placeholder="Enter category name" 
                    required 
                />
                
                <x-admin.textarea 
                    name="description" 
                    label="Description" 
                    placeholder="Enter category description" 
                    rows="3" 
                />
                
                <x-admin.image-uploader 
                    name="icon" 
                    label="Category Icon" 
                    accept=".jpg,.jpeg,.png,.svg" 
                    helper="Square icon image. Recommended size: 128x128px. Max 1MB." 
                    maxFileSize="1" 
                    aspectRatio="1:1" 
                />
                
                <x-admin.toggle 
                    name="is_active" 
                    label="Active Status" 
                    checked="true" 
                    helper="Set category as active/inactive" 
                />
            </div>
        </x-admin.form-section>

        <!-- Form Buttons -->
        <div class="flex justify-end mt-8 gap-3">
            <x-admin.button 
                href="{{ route('admin.project-categories.index') }}" 
                color="light" 
                type="button"
            >
                Cancel
            </x-admin.button>

            <x-admin.button 
                type="submit" 
                color="primary"
            >
                Create Category
            </x-admin.button>
        </div>
    </form>
</x-layouts.admin>