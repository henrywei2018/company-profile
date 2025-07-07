{{-- resources/views/admin/product-categories/index.blade.php --}}
<x-layouts.admin title="Product Categories Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Product Categories' => '']" />

    <!-- Header Section -->
    <x-admin.header-section 
        title="Product Categories Management" 
        description="Organize your products with categories and subcategories"
        :createRoute="route('admin.product-categories.create')"
        createText="Create New Category">
        
        <x-slot name="additionalActions">
            <!-- Statistics Button -->
            <button type="button" 
                    onclick="showStatistics()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistics
            </button>

            <!-- Export Button -->
            <button type="button" 
                    onclick="exportCategories()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </button>

            <!-- Tree View Toggle -->
            <button type="button" 
                    onclick="toggleTreeView()"
                    id="tree-view-toggle"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Tree View
            </button>
        </x-slot>
    </x-admin.header-section>

    <!-- Filter Section -->
    <x-admin.filter-section 
        :action="route('admin.product-categories.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search by category name or description..."
        :hasActiveFilters="request()->hasAny(['search', 'service_category', 'parent', 'status'])"
        :clearFiltersRoute="route('admin.product-categories.index')"
        :filters="[
            [
                'name' => 'service_category',
                'label' => 'Service Category',
                'allLabel' => 'All Service Categories',
                'options' => $serviceCategories->pluck('name', 'id')->toArray()
            ],
            [
                'name' => 'parent',
                'label' => 'Parent Category',
                'allLabel' => 'All Categories',
                'options' => array_merge(
                    ['root' => 'Root Categories Only'],
                    $parentCategories->pluck('name', 'id')->toArray()
                )
            ],
            [
                'name' => 'status', 
                'label' => 'Status',
                'allLabel' => 'All Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive'
                ]
            ]
        ]" />

    <!-- Categories Table -->
    <x-admin.data-table 
        :items="$categories"
        :showBulkActions="true"
        :bulkActions="[
            ['value' => 'activate', 'label' => 'Activate Selected'],
            ['value' => 'deactivate', 'label' => 'Deactivate Selected'],
            ['value' => 'delete', 'label' => 'Delete Selected', 'class' => 'text-red-600']
        ]"
        :bulkActionUrl="route('admin.product-categories.bulk-action')"
        emptyTitle="No categories found"
        emptyDescription="Get started by creating your first product category"
        emptyActionText="Create your first category"
        :emptyActionRoute="route('admin.product-categories.create')"
        :hasActiveFilters="request()->hasAny(['search', 'service_category', 'parent', 'status'])"
        :clearFiltersRoute="route('admin.product-categories.index')"
        :headers="[
            ['label' => 'Category'],
            ['label' => 'Hierarchy & Service'], 
            ['label' => 'Products'],
            ['label' => 'Status'],
            ['label' => 'Order']
        ]">

        <x-slot name="emptyIcon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </x-slot>

        @foreach($categories as $category)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800" data-category-id="{{ $category->id }}">
                <td class="px-6 py-4">
                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" 
                           class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </td>
                
                <!-- Category Info -->
                <td class="px-6 py-4">
                    <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            @if($category->icon)
                                <img src="{{ Storage::url($category->icon) }}" 
                                     alt="{{ $category->name }}" 
                                     class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Category Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <!-- Depth Indicator -->
                                @if($category->parent)
                                    <span class="text-gray-400 text-sm">└─</span>
                                @endif
                                
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    <a href="{{ route('admin.product-categories.show', $category) }}" 
                                       class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $category->name }}
                                    </a>
                                </h3>
                            </div>

                            <!-- Slug -->
                            @if($category->slug)
                                <div class="mt-1">
                                    <code class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-400">
                                        /{{ $category->slug }}
                                    </code>
                                </div>
                            @endif
                            
                            <!-- Description -->
                            @if($category->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ Str::limit($category->description, 100) }}
                                </p>
                            @endif
                            
                            <!-- Meta -->
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>ID: {{ $category->id }}</span>
                                <span>{{ $category->created_at->format('M d, Y') }}</span>
                                @if($category->children_count > 0)
                                    <span class="text-blue-600 dark:text-blue-400">
                                        {{ $category->children_count }} subcategories
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- Hierarchy & Service -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-2">
                        <!-- Parent Category -->
                        @if($category->parent)
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Parent:</span>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $category->parent->name }}
                                </div>
                            </div>
                        @else
                            <div class="text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                    Root Category
                                </span>
                            </div>
                        @endif

                        <!-- Service Category -->
                        @if($category->serviceCategory)
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Service:</span>
                                <div class="text-sm font-medium text-purple-700 dark:text-purple-300">
                                    {{ $category->serviceCategory->name }}
                                </div>
                            </div>
                        @endif
                    </div>
                </td>
                
                <!-- Products Count -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $category->products_count }} Total
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $category->active_products_count }} Active
                        </div>
                        @if($category->products_count > 0)
                            <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                               class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                View Products →
                            </a>
                        @endif
                    </div>
                </td>
                
                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-2">
                        <!-- Active Status -->
                        <div class="flex items-center">
                            <button type="button" 
                                    onclick="toggleStatus({{ $category->id }}, 'active')"
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $category->is_active ? 'bg-blue-600' : 'bg-gray-300' }}">
                                <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform {{ $category->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                            </button>
                            <span class="ml-2 text-xs {{ $category->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </td>

                <!-- Order & Actions -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center justify-between">
                        <!-- Sort Order -->
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            #{{ $category->sort_order }}
                        </div>

                        <!-- Actions Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" 
                                    class="flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <a href="{{ route('admin.product-categories.show', $category) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View Details
                                    </a>
                                    <a href="{{ route('admin.product-categories.edit', $category) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Category
                                    </a>
                                    <button onclick="duplicateCategory({{ $category->id }})" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Duplicate
                                    </button>
                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                    <button onclick="deleteCategory({{ $category->id }})" 
                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.data-table>

    <!-- Statistics Modal -->
    <div id="statistics-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Category Statistics</h3>
                    <button onclick="closeStatistics()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="statistics-content" class="space-y-4">
                    <!-- Statistics content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Global variables
            let currentTreeView = false;

            // Toggle status
            function toggleStatus(categoryId, type) {
                fetch(`/admin/product-categories/${categoryId}/toggle-active`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Simple reload for now
                    } else {
                        alert('Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update status');
                });
            }

            // Duplicate category
            function duplicateCategory(categoryId) {
                if (!confirm('Are you sure you want to duplicate this category?')) {
                    return;
                }

                fetch(`/admin/product-categories/${categoryId}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            location.reload();
                        }
                    } else {
                        alert(data.message || 'Failed to duplicate category');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to duplicate category');
                });
            }

            // Delete category
            function deleteCategory(categoryId) {
                if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    return;
                }

                fetch(`/admin/product-categories/${categoryId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete category');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete category');
                });
            }

            // Show statistics
            function showStatistics() {
                fetch('{{ route("admin.product-categories.statistics") }}')
                    .then(response => response.json())
                    .then(data => {
                        const content = document.getElementById('statistics-content');
                        content.innerHTML = `
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${data.total_categories}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Categories</div>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">${data.active_categories}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Active Categories</div>
                                </div>
                                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${data.root_categories}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Root Categories</div>
                                </div>
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">${data.categories_with_products}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">With Products</div>
                                </div>
                            </div>
                        `;
                        document.getElementById('statistics-modal').classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load statistics');
                    });
            }

            // Close statistics modal
            function closeStatistics() {
                document.getElementById('statistics-modal').classList.add('hidden');
            }

            // Export categories
            function exportCategories() {
                const format = prompt('Export format (csv or json):', 'csv');
                if (format && ['csv', 'json'].includes(format.toLowerCase())) {
                    window.location.href = `{{ route('admin.product-categories.export') }}?format=${format}`;
                }
            }

            // Toggle tree view
            function toggleTreeView() {
                currentTreeView = !currentTreeView;
                const button = document.getElementById('tree-view-toggle');
                
                if (currentTreeView) {
                    button.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        List View
                    `;
                    // Implement tree view logic here
                    alert('Tree view functionality would be implemented here');
                } else {
                    button.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Tree View
                    `;
                }
            }

            // Close modal when clicking outside
            document.getElementById('statistics-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeStatistics();
                }
            });
        </script>
    @endpush
</x-layouts.admin>