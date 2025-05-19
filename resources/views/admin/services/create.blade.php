<!-- resources/views/admin/services/create.blade.php -->
<x-layouts.admin title="Add New Service" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Services Management' => route('admin.services.index'),
            'Add New Service' => route('admin.services.create'),
        ]" />
    </div>

    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information -->
        <x-admin.form-section title="Basic Information" description="Enter the basic details of the service.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-admin.input name="title" label="Service Title" placeholder="Enter service title" required />
                </div>

                <div>
                    <x-admin.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')->toArray()"
                        placeholder="Select a category" />
                </div>

                <div>
                    <x-admin.input name="slug" label="Slug" placeholder="service-slug"
                        helper="Leave empty to auto-generate from title." />
                </div>

                <div class="md:col-span-2">
                    <x-admin.textarea name="short_description" label="Short Description"
                        placeholder="Enter a brief description (max 255 characters)" rows="3" />
                </div>

                <div class="md:col-span-2">
                    <x-admin.rich-editor name="description" label="Full Description"
                        placeholder="Enter detailed service description" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <x-admin.toggle name="is_active" label="Active Status" checked="true"
                    helper="Set service as active/inactive" />

                <x-admin.toggle name="featured" label="Featured Service"
                    helper="Display this service prominently on the website" />

                <div>
                    <x-admin.input name="sort_order" label="Sort Order" type="number" placeholder="0"
                        helper="Lower numbers appear first" />
                </div>
            </div>
        </x-admin.form-section>

        <!-- Media -->
        <x-admin.form-section title="Media" description="Upload service images and icons." class="mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-admin.image-uploader name="image" label="Service Image" accept=".jpg,.jpeg,.png,.webp"
                        helper="Recommended size: 1200x800px. Max 2MB." maxFileSize="2" />
                </div>

                <div>
                    <x-admin.image-uploader name="icon" label="Service Icon" accept=".jpg,.jpeg,.png,.svg"
                        helper="Square icon image. Recommended size: 128x128px. Max 1MB." maxFileSize="1"
                        aspectRatio="1:1" />
                </div>
            </div>
        </x-admin.form-section>

        <!-- SEO Information -->
        <x-admin.form-section title="SEO Information" description="Optimize service for search engines." class="mt-8">
            <div class="grid grid-cols-1 gap-6">
                <x-admin.input name="meta_title" label="Meta Title" placeholder="Enter meta title"
                    helper="Leave empty to use service title. Recommended length: 50-60 characters." />

                <x-admin.textarea name="meta_description" label="Meta Description" placeholder="Enter meta description"
                    rows="3"
                    helper="Brief description for search results. Recommended length: 150-160 characters." />

                <x-admin.input name="meta_keywords" label="Meta Keywords" placeholder="keyword1, keyword2, keyword3"
                    helper="Comma-separated keywords (optional)." />
            </div>
        </x-admin.form-section>

        <!-- Form Buttons -->
        <div class="flex justify-end mt-8 gap-3">
            <x-admin.button href="{{ route('admin.services.index') }}" color="light" type="button">
                Cancel
            </x-admin.button>

            <x-admin.button type="submit" color="primary">
                Create Service
            </x-admin.button>
        </div>
    </form>
</x-layouts.admin>
