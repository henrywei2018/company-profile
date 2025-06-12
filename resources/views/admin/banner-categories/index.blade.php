<x-layouts.admin title="Banner Categories">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banner Categories' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Banner Categories</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Organize your banners with categories</p>
        </div>
        <div class="flex gap-3">
            <x-admin.button color="primary" href="{{ route('admin.banner-categories.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Category
            </x-admin.button>
        </div>
    </div>

    <!-- Search -->
    <x-admin.filter action="{{ route('admin.banner-categories.index') }}" resetRoute="{{ route('admin.banner-categories.index') }}" :collapsed="false">
        <x-admin.input
            name="search"
            label="Search Categories"
            placeholder="Search by name or description..."
            :value="request('search')"
        />
    </x-admin.filter>

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

        <x-admin.data-table>
            <x-slot name="columns">
                <x-admin.table-column sortable field="name" :direction="request('sort') === 'name' ? request('direction') : null">
                    Name
                </x-admin.table-column>
                <x-admin.table-column>Slug</x-admin.table-column>
                <x-admin.table-column>Description</x-admin.table-column>
                <x-admin.table-column sortable field="banners_count" :direction="request('sort') === 'banners_count' ? request('direction') : null">
                    Banners Count
                </x-admin.table-column>
                <x-admin.table-column>Status</x-admin.table-column>
                <x-admin.table-column sortable field="display_order" :direction="request('sort') === 'display_order' ? request('direction') : null">
                    Order
                </x-admin.table-column>
                <x-admin.table-column sortable field="created_at" :direction="request('sort') === 'created_at' ? request('direction') : null">
                    Created
                </x-admin.table-column>
                <x-admin.table-column class="text-center">Actions</x-admin.table-column>
            </x-slot>

            @forelse($categories as $category)
                <x-admin.table-row>
                    <x-admin.table-cell highlight>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($category->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $category->id }}</p>
                            </div>
                        </div>
                    </x-admin.table-cell>
                    
                    <x-admin.table-cell>
                        <code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">{{ $category->slug }}</code>
                    </x-admin.table-cell>
                    
                    <x-admin.table-cell>
                        @if($category->description)
                            <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $category->description }}</p>
                        @else
                            <span class="text-sm text-gray-400 italic">No description</span>
                        @endif
                    </x-admin.table-cell>
                    
                    <x-admin.table-cell>
                        <div class="flex items-center gap-2">
                            @if($category->banners_count > 0)
                                <x-admin.badge type="info">{{ $category->banners_count }} {{ Str::plural('banner', $category->banners_count) }}</x-admin.badge>
                            @else
                                <span class="text-sm text-gray-400">No banners</span>
                            @endif
                        </div>
                    </x-admin.table-cell>
                    
                    <x-admin.table-cell>
                        @if($category->is_active)
                            <x-admin.badge type="success">Active</x-admin.badge>
                        @else
                            <x-admin.badge type="danger">Inactive</x-admin.badge>
                        @endif
                    </x-admin.table-cell>
                    
                    <x-admin.table-cell>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            {{ $category->display_order }}
                        </span>
                    </x-admin.table-cell>
                    
                    <x-admin.table-cell>
                        <div class="text-sm">
                            <div class="text-gray-900 dark:text-white">{{ $category->created_at->format('M j, Y') }}</div>
                            <div class="text-gray-500 dark:text-gray-400">{{ $category->created_at->format('g:i A') }}</div>
                        </div>
                    </x-admin.table-cell>
                    
                    <x-admin.table-cell>
                        <div class="flex items-center justify-center gap-1">
                            <x-admin.dropdown placement="bottom-left">
                                <x-slot name="trigger">
                                    <x-admin.icon-button size="sm" color="light">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </x-admin.icon-button>
                                </x-slot>

                                <x-admin.dropdown-item 
                                    href="{{ route('admin.banner-categories.edit', $category) }}"
                                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'
                                >
                                    Edit
                                </x-admin.dropdown-item>

                                @if($category->banners_count > 0)
                                    <x-admin.dropdown-item 
                                        href="{{ route('admin.banners.index', ['category' => $category->id]) }}"
                                        icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                                    >
                                        View Banners ({{ $category->banners_count }})
                                    </x-admin.dropdown-item>
                                @endif

                                <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>

                                <x-admin.dropdown-item 
                                    type="form"
                                    :action="route('admin.banner-categories.destroy', $category)"
                                    method="DELETE"
                                    :confirm="true"
                                    :confirmMessage="$category->banners_count > 0 ? 'This category has ' . $category->banners_count . ' banners. Are you sure you want to delete it?' : 'Are you sure you want to delete this category?'"
                                    icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>'
                                >
                                    Delete
                                </x-admin.dropdown-item>
                            </x-admin.dropdown>
                        </div>
                    </x-admin.table-cell> 
                </x-admin.table-row>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12">
                        <x-admin.empty-state
                            title="No banner categories found"
                            description="You haven't created any banner categories yet or no categories match your search criteria."
                            :actionText="request()->has('search') ? null : 'Create Your First Category'"
                            :actionUrl="request()->has('search') ? null : route('admin.banner-categories.create')"
                            icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>'
                        />
                    </td>
                </tr>
            @endforelse
        </x-admin.data-table>

        @if($categories->hasPages())
            <x-slot name="footer">
                <x-admin.pagination :paginator="$categories" :appends="request()->query()" />
            </x-slot>
        @endif
    </x-admin.card>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <x-admin.stat-card
            title="Total Categories"
            :value="$categories->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'
            iconColor="text-blue-500"
            iconBg="bg-blue-100 dark:bg-blue-800/30"
        />
        
        <x-admin.stat-card
            title="Active Categories"
            :value="$categories->where('is_active', true)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-green-500"
            iconBg="bg-green-100 dark:bg-green-800/30"
        />
        
        <x-admin.stat-card
            title="Categories with Banners"
            :value="$categories->where('banners_count', '>', 0)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
            iconColor="text-purple-500"
            iconBg="bg-purple-100 dark:bg-purple-800/30"
        />
        
        <x-admin.stat-card
            title="Empty Categories"
            :value="$categories->where('banners_count', 0)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>'
            iconColor="text-amber-500"
            iconBg="bg-amber-100 dark:bg-amber-800/30"
        />
    </div>
</x-layouts.admin>