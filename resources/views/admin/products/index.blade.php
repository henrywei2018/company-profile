{{-- resources/views/admin/products/index.blade.php --}}
<x-layouts.admin title="Products Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Products' => '']" />

    <!-- Header Section -->
    <x-admin.header-section 
        title="Products Management" 
        description="Manage your product catalog and inventory"
        :createRoute="route('admin.products.create')"
        createText="Create New Product">
        
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
                    onclick="exportProducts()"
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
        :action="route('admin.products.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search by name, SKU, brand, or description..."
        :hasActiveFilters="request()->hasAny(['search', 'category', 'service', 'status', 'brand', 'stock_status'])"
        :clearFiltersRoute="route('admin.products.index')"
        :filters="[
            [
                'name' => 'category',
                'label' => 'Category',
                'allLabel' => 'All Categories',
                'options' => $categories->pluck('name', 'id')->toArray()
            ],
            [
                'name' => 'service',
                'label' => 'Service',
                'allLabel' => 'All Services',
                'options' => $services->pluck('name', 'id')->toArray()
            ],
            [
                'name' => 'status', 
                'label' => 'Status',
                'allLabel' => 'All Status',
                'options' => [
                    'published' => 'Published',
                    'draft' => 'Draft', 
                    'archived' => 'Archived'
                ]
            ],
            [
                'name' => 'brand', 
                'label' => 'Brand',
                'allLabel' => 'All Brands',
                'options' => $brands->mapWithKeys(fn($brand) => [$brand => $brand])->toArray()
            ],
            [
                'name' => 'stock_status', 
                'label' => 'Stock Status',
                'allLabel' => 'All Stock Status',
                'options' => [
                    'in_stock' => 'In Stock',
                    'out_of_stock' => 'Out of Stock',
                    'on_backorder' => 'On Backorder'
                ]
            ]
        ]" />

    <!-- Bulk Actions -->
    <x-admin.bulk-actions 
        formId="bulk-form"
        :actionRoute="route('admin.products.bulk-action')"
        selectedCountText="products selected"
        :actions="[
            [
                'value' => 'publish',
                'label' => 'Publish',
                'bgColor' => 'bg-green-100',
                'textColor' => 'text-green-700',
                'hoverColor' => 'bg-green-200'
            ],
            [
                'value' => 'draft',
                'label' => 'Set as Draft',
                'bgColor' => 'bg-yellow-100',
                'textColor' => 'text-yellow-700',
                'hoverColor' => 'bg-yellow-200'
            ],
            [
                'value' => 'activate',
                'label' => 'Activate',
                'bgColor' => 'bg-blue-100',
                'textColor' => 'text-blue-700',
                'hoverColor' => 'bg-blue-200'
            ],
            [
                'value' => 'deactivate',
                'label' => 'Deactivate',
                'bgColor' => 'bg-gray-100',
                'textColor' => 'text-gray-700',
                'hoverColor' => 'bg-gray-200'
            ],
            [
                'value' => 'feature',
                'label' => 'Set Featured',
                'bgColor' => 'bg-purple-100',
                'textColor' => 'text-purple-700',
                'hoverColor' => 'bg-purple-200'
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
        :items="$products"
        emptyTitle="No products found"
        emptyDescription="Get started by creating your first product."
        emptyActionText="Create your first product"
        :emptyActionRoute="route('admin.products.create')"
        :hasActiveFilters="request()->hasAny(['search', 'category', 'service', 'status', 'brand', 'stock_status'])"
        :clearFiltersRoute="route('admin.products.index')"
        :headers="[
            ['label' => 'Product'],
            ['label' => 'Category & Service'], 
            ['label' => 'Price & Stock'],
            ['label' => 'Status'],
            ['label' => 'Order']
        ]">

        <x-slot name="emptyIcon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 21c0 .6-.4 1-1 1H5c-.6 0-1-.4-1-1v-3c0-.6.4-1 1-1h3c.6 0 1 .4 1 1v3zM20 21c0 .6-.4 1-1 1h-3c-.6 0-1-.4-1-1v-3c0-.6.4-1 1-1h3c.6 0 1 .4 1 1v3zM9 10c0 .6-.4 1-1 1H5c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h3c.6 0 1 .4 1 1v3zM20 10c0 .6-.4 1-1 1h-3c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h3c.6 0 1 .4 1 1v3z"/>
        </x-slot>

        @foreach($products as $product)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-6 py-4">
                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" 
                           class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </td>
                
                <!-- Product Info -->
                <td class="px-6 py-4">
                    <div class="flex items-start space-x-3">
                        <x-admin.media-preview 
                            :src="$product->featured_image_url"
                            :alt="$product->name"
                            width="w-20"
                            height="h-20" 
                            class="rounded-lg" />
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                    {{ $product->name }}
                                </a>
                            </div>
                            
                            <!-- Description -->
                            @if($product->short_description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ Str::limit($product->short_description, 100) }}
                                </p>
                            @endif
                            
                            <!-- Meta -->
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>ID: {{ $product->id }}</span>
                                <span>{{ $product->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- Category & Service -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-2">
                        @if($product->category)
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ $product->category->name }}
                                </span>
                            </div>
                        @endif
                        
                        @if($product->service)
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"/>
                                    </svg>
                                    {{ $product->service->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </td>
                
                <!-- Price & Stock -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-2">
                        <!-- Price -->
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {!! $product->formatted_price !!}
                        </div>
                        
                        <!-- Stock Status -->
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                @if($product->stock_status === 'in_stock') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @elseif($product->stock_status === 'out_of_stock') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 @endif">
                                @if($product->stock_status === 'in_stock')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($product->stock_status === 'out_of_stock')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                                {{ $product->stock_status_label }}
                            </span>
                        </div>
                        
                        <!-- Stock Quantity -->
                        @if($product->manage_stock)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Qty: {{ $product->stock_quantity }}
                            </div>
                        @endif
                    </div>
                </td>
                
                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-2">
                        <!-- Publication Status -->
                        <x-admin.status-badge :status="$product->status" />
                        
                        <!-- Active Status -->
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                @if($product->is_active) bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100 @endif">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </td>
                
                <!-- Order -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $product->sort_order }}
                    </span>
                </td>
                
                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <!-- Quick Actions -->
                        <form method="POST" action="{{ route('admin.products.toggle-featured', $product) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="{{ $product->is_featured ? 'text-yellow-600 hover:text-yellow-900' : 'text-gray-400 hover:text-yellow-600' }}"
                                    title="{{ $product->is_featured ? 'Remove Featured' : 'Set Featured' }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="{{ $product->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                                    title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                @if($product->is_active)
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
                                    <a href="{{ route('admin.products.show', $product) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.duplicate', $product) }}" class="inline w-full">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            Duplicate
                                        </button>
                                    </form>
                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')" class="inline w-full">
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
        title="Product Statistics"
        :statsEndpoint="route('admin.products.statistics')" />

    @push('scripts')
    <script>
        // Override the default input name for product bulk actions
        function getInputName(action) {
            return 'product_ids[]';
        }

        // Export functionality
        function exportProducts() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.products.export") }}?' + params.toString();
            window.open(exportUrl, '_blank');
        }
    </script>
    @endpush
</x-layouts.admin>