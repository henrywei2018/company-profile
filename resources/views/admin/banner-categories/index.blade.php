{{-- resources/views/admin/banner-categories/index.blade.php --}}
<x-layouts.admin title="Banner Categories Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banner Categories' => '']" />

    <!-- Header Section -->
    <x-admin.header-section 
        title="Banner Categories Management" 
        description="Organize your banners with categories"
        :createRoute="route('admin.banner-categories.create')"
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
        </x-slot>
    </x-admin.header-section>

    <!-- Filter Section -->
    <x-admin.filter-section 
        :action="route('admin.banner-categories.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search by name, slug, or description..."
        :hasActiveFilters="request()->hasAny(['search'])"
        :clearFiltersRoute="route('admin.banner-categories.index')" />

    <!-- Bulk Actions -->
    <x-admin.bulk-actions 
        formId="bulk-form"
        :actionRoute="route('admin.banner-categories.bulk-action')"
        selectedCountText="categories selected"
        :actions="[
            [
                'value' => 'activate',
                'label' => 'Activate',
                'bgColor' => 'bg-green-100',
                'textColor' => 'text-green-700',
                'hoverColor' => 'bg-green-200'
            ],
            [
                'value' => 'deactivate', 
                'label' => 'Deactivate',
                'bgColor' => 'bg-yellow-100',
                'textColor' => 'text-yellow-700',
                'hoverColor' => 'bg-yellow-200'
            ],
            [
                'value' => 'delete',
                'label' => 'Delete', 
                'bgColor' => 'bg-red-100',
                'textColor' => 'text-red-700',
                'hoverColor' => 'bg-red-200'
            ]
        ]" />

    <!-- Data Table -->
    <x-admin.new.data-table 
        :items="$categories"
        emptyTitle="No banner categories found"
        emptyDescription="Get started by creating your first banner category."
        emptyActionText="Create your first category"
        :emptyActionRoute="route('admin.banner-categories.create')"
        :hasActiveFilters="request()->hasAny(['search'])"
        :clearFiltersRoute="route('admin.banner-categories.index')"
        :headers="[
            ['label' => 'Category'],
            ['label' => 'Usage'], 
            ['label' => 'Status'],
            ['label' => 'Order'],
            ['label' => 'Created']
        ]">

        <x-slot name="emptyIcon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </x-slot>

        @foreach($categories as $category)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-6 py-4">
                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" 
                           class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </td>
                
                <!-- Category Info -->
                <td class="px-6 py-4">
                    <div class="flex items-start space-x-3">
                        <!-- Category Icon -->
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-semibold flex-shrink-0">
                            {{ strtoupper(substr($category->name, 0, 2)) }}
                        </div>
                        
                        <!-- Category Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.banner-categories.edit', $category) }}" 
                                   class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                    {{ $category->name }}
                                </a>
                            </div>
                            
                            <!-- Slug -->
                            <div class="mt-1">
                                <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs text-gray-600 dark:text-gray-300">
                                    {{ $category->slug }}
                                </code>
                            </div>
                            
                            <!-- Description -->
                            @if($category->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ Str::limit($category->description, 100) }}
                                </p>
                            @else
                                <span class="text-sm text-gray-400 italic mt-1">No description</span>
                            @endif
                            
                            <!-- Meta -->
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>ID: {{ $category->id }}</span>
                                <span>{{ $category->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- Usage -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        @if($category->banners_count > 0)
                            <a href="{{ route('admin.banners.index', ['category' => $category->id]) }}" 
                               class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 hover:bg-blue-200 dark:hover:bg-blue-700">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $category->banners_count }} {{ Str::plural('banner', $category->banners_count) }}
                            </a>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                No banners
                            </span>
                        @endif
                    </div>
                    
                    <!-- Usage Details -->
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Component: &lt;x-banner-slider categorySlug="{{ $category->slug }}" /&gt;
                    </div>
                </td>
                
                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-admin.status-badge :status="$category->is_active ? 'active' : 'inactive'" />
                </td>
                
                <!-- Order -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $category->display_order }}
                    </span>
                </td>
                
                <!-- Created -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm">
                        <div class="text-gray-900 dark:text-white">{{ $category->created_at->format('M j, Y') }}</div>
                        <div class="text-gray-500 dark:text-gray-400">{{ $category->created_at->diffForHumans() }}</div>
                    </div>
                </td>
                
                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <!-- Quick Actions -->
                        <form method="POST" action="{{ route('admin.banner-categories.toggle-status', $category) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="{{ $category->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                                    title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                @if($category->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                        </form>

                        <!-- Dropdown Menu -->
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                <div class="py-1">
                                    <a href="{{ route('admin.banner-categories.edit', $category) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    
                                    @if($category->banners_count > 0)
                                        <a href="{{ route('admin.banners.index', ['category' => $category->id]) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            View Banners ({{ $category->banners_count }})
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('admin.banners.create', ['category' => $category->id]) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Banner
                                    </a>
                                    
                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                    
                                    <!-- Only show delete if no banners -->
                                    @if($category->banners_count === 0)
                                        <form method="POST" action="{{ route('admin.banner-categories.destroy', $category) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this category?')" class="inline w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            Cannot delete: has {{ $category->banners_count }} banner(s)
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.new.data-table>

    <!-- Statistics Modal -->
    <x-admin.statistics-modal 
        modalId="statistics-modal"
        title="Banner Category Statistics"
        :statsEndpoint="route('admin.banner-categories.statistics')" />

    @push('scripts')
    <script>
        // Override the default input name for category bulk actions
        function getInputName(action) {
            return 'category_ids[]';
        }

        // Export functionality
        function exportCategories() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.banner-categories.export") }}?' + params.toString();
            window.open(exportUrl, '_blank');
        }

        // Drag and drop reordering (optional enhancement)
        document.addEventListener('DOMContentLoaded', function() {
            // You can add drag-and-drop functionality here if needed
            // For example, using SortableJS library
        });
    </script>
    @endpush
</x-layouts.admin>