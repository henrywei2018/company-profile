<x-layouts.admin title="Post Categories">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Posts' => route('admin.posts.index'), 'Categories' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Post Categories</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Organize your blog posts with categories</p>
        </div>
        <div class="flex gap-3">
            <!-- Export Button -->
            <x-admin.button color="light" onclick="exportCategories()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </x-admin.button>

            <!-- Statistics Button -->
            <x-admin.button color="light" onclick="showStatistics()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistics
            </x-admin.button>

            <!-- Create Button -->
            <x-admin.button color="primary" href="{{ route('admin.post-categories.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Category
            </x-admin.button>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.post-categories.index') }}" resetRoute="{{ route('admin.post-categories.index') }}" :collapsed="false">
        <x-admin.input name="search" label="Search Categories" placeholder="Search by name or description..." :value="request('search')" />
    </x-admin.filter>

    <!-- Bulk Actions Form -->
    <form id="bulk-form" method="POST" action="{{ route('admin.post-categories.bulk-action') }}" class="hidden">
        @csrf
        <input type="hidden" name="categories" id="bulk-categories">
    </form>

    <!-- Categories Table -->
    <x-admin.card noPadding>
        <x-slot name="title">
            <div class="flex items-center justify-between w-full">
                <span>Categories ({{ $categories->total() }})</span>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} results
                </div>
            </div>
        </x-slot>

        @if($categories->count() > 0)
            <!-- Bulk Actions Bar -->
            <div id="bulk-actions" class="hidden mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mx-6">
                <div class="flex items-center justify-between">
                    <span id="selected-count" class="text-sm font-medium text-blue-900 dark:text-blue-100">
                        0 categories selected
                    </span>
                    <div class="flex gap-2">
                        <button type="button" onclick="bulkAction('delete')" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 dark:bg-red-800 dark:text-red-200">
                            Delete Selected
                        </button>
                        <button type="button" onclick="clearSelection()" class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200">
                            Clear Selection
                        </button>
                    </div>
                </div>
            </div>

            <x-admin.data-table checkbox>
                <x-slot name="columns">
                    <x-admin.table-column sortable field="name" :direction="request('sort') === 'name' ? (request('direction') ?? 'asc') : null">
                        Name
                    </x-admin.table-column>
                    <x-admin.table-column>Slug</x-admin.table-column>
                    <x-admin.table-column>Description</x-admin.table-column>
                    <x-admin.table-column sortable field="posts_count" :direction="request('sort') === 'posts_count' ? (request('direction') ?? 'asc') : null">
                        Posts Count
                    </x-admin.table-column>
                    <x-admin.table-column sortable field="created_at" :direction="request('sort') === 'created_at' ? (request('direction') ?? 'asc') : null">
                        Created
                    </x-admin.table-column>
                    <x-admin.table-column class="text-center">Actions</x-admin.table-column>
                </x-slot>

                @foreach($categories as $category)
                    <x-admin.table-row>
                        <x-admin.table-cell>
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="category-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </x-admin.table-cell>

                        <x-admin.table-cell highlight>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($category->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('admin.post-categories.edit', $category) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $category->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $category->id }}</p>
                                </div>
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">{{ $category->slug }}</code>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            @if($category->description)
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ Str::limit($category->description, 80) }}
                                </p>
                            @else
                                <span class="text-sm text-gray-400 italic">No description</span>
                            @endif
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="flex items-center gap-2">
                                @if($category->posts_count > 0)
                                    <x-admin.badge type="info">
                                        {{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}
                                    </x-admin.badge>
                                    @if($category->published_posts_count > 0)
                                        <x-admin.badge type="success" size="sm">
                                            {{ $category->published_posts_count }} published
                                        </x-admin.badge>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">No posts</span>
                                @endif
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="text-sm">
                                <div class="text-gray-900 dark:text-white">{{ $category->created_at->format('M j, Y') }}</div>
                                <div class="text-gray-500 dark:text-gray-400">{{ $category->created_at->format('g:i A') }}</div>
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Edit Button -->
                                <div class="relative group">
                                    <a href="{{ route('admin.post-categories.edit', $category) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-blue-400 dark:hover:bg-blue-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                        Edit Category
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700"></div>
                                    </div>
                                </div>

                                <!-- View Posts Button (only show if category has posts) -->
                                @if($category->posts_count > 0)
                                <div class="relative group">
                                    <a href="{{ route('admin.posts.index', ['category' => $category->id]) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-green-400 dark:hover:bg-green-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                        View Posts ({{ $category->posts_count }})
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700"></div>
                                    </div>
                                </div>
                                @endif

                                <!-- Delete Button -->
                                <div class="relative group">
                                    <form action="{{ route('admin.post-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('{{ $category->posts_count > 0 ? 'This category has ' . $category->posts_count . ' posts. Are you sure you want to delete it?' : 'Are you sure you want to delete this category?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-red-400 dark:hover:bg-red-900/30"
                                                {{ $category->posts_count > 0 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                        {{ $category->posts_count > 0 ? 'Cannot delete (has posts)' : 'Delete Category' }}
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700"></div>
                                    </div>
                                </div>
                            </div>
                        </x-admin.table-cell>
                    </x-admin.table-row>
                @endforeach
            </x-admin.data-table>

            @if($categories->hasPages())
                <x-slot name="footer">
                    <x-admin.pagination :paginator="$categories" :appends="request()->query()" />
                </x-slot>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <x-admin.empty-state 
                    title="No categories found"
                    description="{{ request()->has('search') ? 'Try adjusting your search criteria.' : 'You haven\'t created any post categories yet or no categories match your search criteria.' }}"
                    :actionText="request()->has('search') ? null : 'Create Your First Category'" 
                    :actionUrl="request()->has('search') ? null : route('admin.post-categories.create')"
                    icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>' />
            </div>
        @endif
    </x-admin.card>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <x-admin.stat-card 
            title="Total Categories" 
            :value="$categories->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'
            iconColor="text-blue-500" 
            iconBg="bg-blue-100 dark:bg-blue-800/30" />

        <x-admin.stat-card 
            title="Categories with Posts" 
            :value="$categories->where('posts_count', '>', 0)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
            iconColor="text-green-500" 
            iconBg="bg-green-100 dark:bg-green-800/30" />

        <x-admin.stat-card 
            title="Empty Categories" 
            :value="$categories->where('posts_count', 0)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>'
            iconColor="text-amber-500" 
            iconBg="bg-amber-100 dark:bg-amber-800/30" />
    </div>

    <!-- Statistics Modal -->
    <div id="statistics-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Statistics</h3>
                <button onclick="closeStatistics()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="statistics-content">
                <!-- Statistics content will be loaded here -->
                <div class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bulk selection functionality
            const selectAllCheckbox = document.getElementById('hs-at-with-checkboxes-main');
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            // Select all functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    categoryCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkActions();
                });
            }
            
            // Individual checkbox change
            categoryCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = checkedBoxes.length === categoryCheckboxes.length;
                        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < categoryCheckboxes.length;
                    }
                    updateBulkActions();
                });
            });
            
            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
                const count = checkedBoxes.length;
                
                if (count > 0) {
                    bulkActions.classList.remove('hidden');
                    selectedCount.textContent = `${count} categor${count === 1 ? 'y' : 'ies'} selected`;
                } else {
                    bulkActions.classList.add('hidden');
                }
            }
        });
        
        // Bulk actions
        function bulkAction(action) {
            const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one category.');
                return;
            }
            
            const categoryIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (action === 'delete') {
                if (confirm(`Are you sure you want to delete ${categoryIds.length} categor${categoryIds.length === 1 ? 'y' : 'ies'}?`)) {
                    document.getElementById('bulk-categories').value = JSON.stringify(categoryIds);
                    document.getElementById('bulk-form').submit();
                }
            }
        }
        
        function clearSelection() {
            document.querySelectorAll('.category-checkbox:checked').forEach(cb => cb.checked = false);
            const selectAllCheckbox = document.getElementById('hs-at-with-checkboxes-main');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            document.getElementById('bulk-actions').classList.add('hidden');
        }
        
        // Export functionality
        function exportCategories() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.post-categories.export") }}?' + params.toString();
            window.open(exportUrl, '_blank');
        }
        
        // Statistics functionality
        function showStatistics() {
            document.getElementById('statistics-modal').classList.remove('hidden');
            loadStatistics();
        }
        
        function closeStatistics() {
            document.getElementById('statistics-modal').classList.add('hidden');
        }
        
        function loadStatistics() {
            fetch('{{ route("admin.post-categories.statistics") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderStatistics(data.data);
                    } else {
                        document.getElementById('statistics-content').innerHTML = 
                            '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                    document.getElementById('statistics-content').innerHTML = 
                        '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
                });
        }
        
        function renderStatistics(stats) {
            const content = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${stats.total_categories}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Categories</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">${stats.categories_with_posts}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">With Posts</div>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">${stats.empty_categories}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Empty</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${stats.average_posts_per_category}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Avg Posts</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Most Popular Categories</h4>
                        <div class="space-y-2">
                            ${stats.most_popular_categories.map(category => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="font-medium text-sm">${category.name}</div>
                                    <span class="text-xs text-gray-500">${category.posts_count} posts</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Recently Created</h4>
                        <div class="space-y-2">
                            ${stats.recent_categories.map(category => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="font-medium text-sm">${category.name}</div>
                                    <span class="text-xs text-gray-500">${category.created_at}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('statistics-content').innerHTML = content;
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