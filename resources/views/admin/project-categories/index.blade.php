<!-- resources/views/admin/project-categories/index.blade.php -->
<x-layouts.admin title="Project Categories" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Project Categories' => route('admin.project-categories.index')
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button href="{{ route('admin.project-categories.create') }}" icon='<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'>
                Add New Category
            </x-admin.button>
        </div>
    </div>
    
    <!-- Categories List -->
    <x-admin.card>
        <x-slot name="headerActions">
            <span class="text-sm text-gray-500 dark:text-gray-400 px-4 py-4">{{ $categories->total() }} categories found</span>
        </x-slot>
        
        @if($categories->count() > 0)
            <x-admin.data-table>
                <x-slot name="columns">
                    <x-admin.table-column sortable="true" field="name" direction="{{ request('sort') === 'name' ? request('direction', 'asc') : null }}">Name</x-admin.table-column>
                    <x-admin.table-column>Description</x-admin.table-column>
                    <x-admin.table-column>Projects</x-admin.table-column>
                    <x-admin.table-column>Status</x-admin.table-column>
                    <x-admin.table-column sortable="true" field="created_at" direction="{{ request('sort') === 'created_at' ? request('direction', 'asc') : null }}">Created</x-admin.table-column>
                    <x-admin.table-column>Actions</x-admin.table-column>
                </x-slot>
                
                @foreach($categories as $category)
                    <x-admin.table-row>
                        <x-admin.table-cell class="max-w-xs truncate" highlight="true">
                            <div class="flex items-center">
                                @if($category->icon)
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-gray-100 dark:bg-neutral-800 rounded">
                                        <img class="h-6 w-6" src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}">
                                    </div>
                                @else
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <a href="{{ route('admin.project-categories.edit', $category) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $category->name }}
                                    </a>
                                </div>
                            </div>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $category->description ?? 'No description' }}
                            </div>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <x-admin.badge>
                                {{ $category->projects_count }}
                            </x-admin.badge>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($category->is_active)
                                <x-admin.badge type="success" dot="true">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger" dot="true">Inactive</x-admin.badge>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            {{ $category->created_at->format('M d, Y') }}
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2">
                                <x-admin.icon-button 
                                    href="{{ route('admin.project-categories.edit', $category) }}"
                                    tooltip="Edit category"
                                    color="primary"
                                    size="sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-admin.icon-button>
                                
                                <form action="{{ route('admin.project-categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="Delete category"
                                        color="danger"
                                        size="sm"
                                        onclick="return confirm('Are you sure you want to delete this category?')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                                
                                <form action="{{ route('admin.project-categories.toggle-active', $category) }}" method="POST" class="inline">
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
                {{ $categories->withQueryString()->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="No categories found" 
                description="There are no project categories yet."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>'
                actionText="Add New Category"
                :actionUrl="route('admin.project-categories.create')"
            />
        @endif
    </x-admin.card>
</x-layouts.admin>