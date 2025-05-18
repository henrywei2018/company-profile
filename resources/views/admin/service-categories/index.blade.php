<!-- resources/views/admin/service-categories/index.blade.php -->
<x-layouts.admin title="Service Categories" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Service Categories' => route('admin.service-categories.index')
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button 
                href="#"
                color="primary"
                onclick="Preline.showModal('#create-category-modal')"
            >
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Category
            </x-admin.button>
        </div>
    </div>
    
    <!-- Categories List -->
    <x-admin.card>
        <x-slot name="title">Service Categories</x-slot>
        <x-slot name="subtitle">Manage categories for your services</x-slot>
        
        @if($categories->count() > 0)
            <x-admin.data-table responsive="true">
                <x-slot name="columns">
                    <x-admin.table-column sortable="true" field="name" direction="{{ request('sort') === 'name' ? request('direction', 'asc') : null }}">Name</x-admin.table-column>
                    <x-admin.table-column>Slug</x-admin.table-column>
                    <x-admin.table-column>Services Count</x-admin.table-column>
                    <x-admin.table-column>Status</x-admin.table-column>
                    <x-admin.table-column>Sort Order</x-admin.table-column>
                    <x-admin.table-column>Actions</x-admin.table-column>
                </x-slot>
                
                @foreach($categories as $category)
                    <x-admin.table-row>
                        <x-admin.table-cell highlight="true">
                            <div class="flex items-center">
                                @if($category->icon)
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="h-8 w-8">
                                    </div>
                                @else
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.585l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $category->name }}
                                    </div>
                                    @if($category->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-md truncate">
                                            {{ $category->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <span class="text-gray-600 dark:text-gray-400">{{ $category->slug }}</span>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <a href="{{ route('admin.services.index', ['category_id' => $category->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $category->services->count() }}
                            </a>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($category->is_active)
                                <x-admin.badge type="success" dot="true">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger" dot="true">Inactive</x-admin.badge>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            {{ $category->sort_order }}
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2">
                                <x-admin.icon-button 
                                    href="#"
                                    tooltip="Edit category"
                                    color="primary"
                                    size="sm"
                                    onclick="editCategory({{ $category->id }})"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-admin.icon-button>
                                
                                <form action="{{ route('admin.service-categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="Delete category"
                                        color="danger"
                                        size="sm"
                                        onclick="return confirm('Are you sure you want to delete this category? This will not delete associated services.')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                                
                                <form action="{{ route('admin.service-categories.toggle-status', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="{{ $category->is_active ? 'Deactivate' : 'Activate' }}"
                                        color="{{ $category->is_active ? 'warning' : 'success' }}"
                                        size="sm"
                                    >
                                        @if($category->is_active)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </x-admin.icon-button>
                                </form>
                            </div>
                        </x-admin.table-cell>
                    </x-admin.table-row>
                @endforeach
            </x-admin.data-table>
            
            <div class="px-6 py-4">
                {{ $categories->withQueryString()->links('vendor.pagination.tailwind') }}
            </div>
        @else
            <x-admin.empty-state 
                title="No categories found" 
                description="Start by creating your first service category."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.585l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>'
                actionText="Add New Category"
                actionUrl="#"
                :actionAttributes="['onclick' => 'Preline.showModal(\'#create-category-modal\')']"
            />
        @endif
    </x-admin.card>
    
    <!-- Create Category Modal -->
    <x-admin.modal id="create-category-modal" title="Add New Category">
        <form id="category-form" action="{{ route('admin.service-categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="method" value="POST">
            <input type="hidden" name="category_id" id="category_id" value="">
            
            <div class="space-y-4">
                <x-admin.input
                    name="name"
                    label="Category Name"
                    placeholder="Enter category name"
                    required
                    id="category_name"
                />
                
                <x-admin.input
                    name="slug"
                    label="Slug"
                    placeholder="category-slug"
                    helper="Leave empty to auto-generate from name."
                    id="category_slug"
                />
                
                <x-admin.textarea
                    name="description"
                    label="Description"
                    placeholder="Enter category description (optional)"
                    rows="3"
                    id="category_description"
                />
                
                <x-admin.input
                    name="sort_order"
                    label="Sort Order"
                    type="number"
                    placeholder="0"
                    helper="Lower numbers appear first"
                    id="category_sort_order"
                />
                
                <x-admin.file-input
                    name="icon"
                    label="Category Icon"
                    accept=".jpg,.jpeg,.png,.svg"
                    helper="Recommended size: 128x128px. Max 1MB."
                    id="category_icon"
                >
                    SVG, PNG, JPG or JPEG (max. 1MB)
                </x-admin.file-input>
                
                <x-admin.toggle
                    name="is_active"
                    label="Active Status"
                    checked="true"
                    helper="Set category as active/inactive"
                    id="category_is_active"
                />
            </div>
        </form>
        
        <x-slot name="footer">
            <x-admin.button
                color="light"
                type="button"
                onclick="Preline.hideModal('#create-category-modal')"
            >
                Cancel
            </x-admin.button>
            
            <x-admin.button
                color="primary"
                type="button"
                onclick="document.getElementById('category-form').submit()"
                id="submit_btn"
            >
                Create Category
            </x-admin.button>
        </x-slot>
    </x-admin.modal>
    
    @push('scripts')
    <script>
        function editCategory(id) {
            // Change form properties for editing
            document.getElementById('method').value = 'PUT';
            document.getElementById('category_id').value = id;
            document.getElementById('category-form').action = `{{ route('admin.service-categories.index') }}/${id}`;
            document.getElementById('submit_btn').innerText = 'Update Category';
            
            // Fetch category data
            fetch(`{{ route('admin.service-categories.index') }}/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('category_name').value = data.name;
                    document.getElementById('category_slug').value = data.slug;
                    document.getElementById('category_description').value = data.description;
                    document.getElementById('category_sort_order').value = data.sort_order;
                    
                    // Handle the toggle
                    if (document.getElementById('category_is_active')) {
                        if (typeof HSToggle !== 'undefined') {
                            // Use Preline's method if available
                            HSToggle.setValue('category_is_active', data.is_active);
                        } else {
                            // Fallback for custom toggle
                            const toggle = document.querySelector('[name="is_active"]');
                            if (toggle) toggle.checked = data.is_active;
                        }
                    }
                    
                    // Show the modal
                    Preline.showModal('#create-category-modal');
                })
                .catch(error => {
                    console.error('Error fetching category data:', error);
                    alert('Error fetching category data. Please try again.');
                });
        }
        
        // Reset form when modal is closed
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('create-category-modal');
            if (modal) {
                modal.addEventListener('hidden.hs.modal', function () {
                    document.getElementById('category-form').reset();
                    document.getElementById('method').value = 'POST';
                    document.getElementById('category_id').value = '';
                    document.getElementById('category-form').action = "{{ route('admin.service-categories.store') }}";
                    document.getElementById('submit_btn').innerText = 'Create Category';
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>