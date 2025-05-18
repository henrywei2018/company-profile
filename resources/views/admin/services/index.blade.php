<!-- resources/views/admin/services/index.blade.php -->
<x-layouts.admin title="Services Management" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Services Management' => route('admin.services.index')
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button href="{{ route('admin.services.create') }}" icon='<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'>
                Add New Service
            </x-admin.button>
        </div>
    </div>
    
    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.services.index') }}" method="GET" :resetRoute="route('admin.services.index')">
        <x-admin.input
            name="search"
            label="Search"
            placeholder="Search by title or description"
            value="{{ request('search') }}"
        />
        
        <x-admin.select
            name="category_id"
            label="Category"
            :options="$categories->pluck('name', 'id')->toArray()"
            placeholder="All Categories"
            value="{{ request('category_id') }}"
        />
        
        <x-admin.select
            name="status"
            label="Status"
            :options="['1' => 'Active', '0' => 'Inactive']"
            placeholder="All Statuses"
            value="{{ request('status') }}"
        />
        
        <x-admin.select
            name="featured"
            label="Featured"
            :options="['1' => 'Featured', '0' => 'Not Featured']"
            placeholder="All"
            value="{{ request('featured') }}"
        />
    </x-admin.filter>
    
    <!-- Services List -->
    <x-admin.card>
        <x-slot name="headerActions">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $services->total() }} services found</span>
        </x-slot>
        
        @if($services->count() > 0)
            <x-admin.data-table>
                <x-slot name="columns">
                    <x-admin.table-column sortable="true" field="title" direction="{{ request('sort') === 'title' ? request('direction', 'asc') : null }}">Title</x-admin.table-column>
                    <x-admin.table-column>Category</x-admin.table-column>
                    <x-admin.table-column>Featured</x-admin.table-column>
                    <x-admin.table-column>Status</x-admin.table-column>
                    <x-admin.table-column sortable="true" field="created_at" direction="{{ request('sort') === 'created_at' ? request('direction', 'asc') : null }}">Created</x-admin.table-column>
                    <x-admin.table-column>Actions</x-admin.table-column>
                </x-slot>
                
                @foreach($services as $service)
                    <x-admin.table-row>
                        <x-admin.table-cell highlight="true">
                            <div class="flex items-center">
                                @if($service->image)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}">
                                    </div>
                                @elseif($service->icon)
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-gray-100 dark:bg-neutral-800 rounded">
                                        <img class="h-6 w-6" src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->title }}">
                                    </div>
                                @else
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $service->title }}
                                    </a>
                                    @if($service->short_description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-md truncate">
                                            {{ $service->short_description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($service->category)
                                {{ $service->category->name }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">None</span>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($service->featured)
                                <x-admin.badge type="primary" dot="true">Featured</x-admin.badge>
                            @else
                                <x-admin.badge type="default">No</x-admin.badge>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($service->is_active)
                                <x-admin.badge type="success" dot="true">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger" dot="true">Inactive</x-admin.badge>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            {{ $service->created_at->format('M d, Y') }}
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2">
                                <x-admin.icon-button 
                                    href="{{ route('admin.services.edit', $service) }}"
                                    tooltip="Edit service"
                                    color="primary"
                                    size="sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-admin.icon-button>
                                
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="Delete service"
                                        color="danger"
                                        size="sm"
                                        onclick="return confirm('Are you sure you want to delete this service?')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                                
                                <form action="{{ route('admin.services.toggle-status', $service) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="{{ $service->is_active ? 'Deactivate' : 'Activate' }}"
                                        color="{{ $service->is_active ? 'warning' : 'success' }}"
                                        size="sm"
                                    >
                                        @if($service->is_active)
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
                {{ $services->withQueryString()->links('vendor.pagination.tailwind') }}
            </div>
        @else
            <x-admin.empty-state 
                title="No services found" 
                description="There are no services matching your criteria."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z" /></svg>'
                actionText="Add New Service"
                :actionUrl="route('admin.services.create')"
            />
        @endif
    </x-admin.card>
</x-layouts.admin>