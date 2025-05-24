<!-- resources/views/admin/projects/index.blade.php -->
<x-admin-layout :title="'Projects'">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Projects Management' => route('admin.projects.index')
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button href="{{ route('admin.projects.create') }}" icon='<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'>
                Add New Project
            </x-admin.button>
        </div>
    </div>
    
    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.projects.index') }}" method="GET" :resetRoute="route('admin.projects.index')">
        <x-admin.input
            name="search"
            label="Search"
            placeholder="Search by title or description"
            value="{{ request('search') }}"
        />
        
        <x-admin.select
            name="category"
            label="Category"
            :options="$categories"
            placeholder="All Categories"
            value="{{ request('category') }}"
        /> 
        
        <x-admin.select
            name="status"
            label="Status"
            :options="[
                'completed' => 'Completed',
                'in_progress' => 'In Progress',
                'upcoming' => 'Upcoming'
            ]"
            placeholder="All Statuses"
            value="{{ request('status') }}"
        />
        
        <x-admin.select
            name="year"
            label="Year"
            :options="$years"
            placeholder="All Years"
            value="{{ request('year') }}"
        />
    </x-admin.filter>
    
    <!-- Projects List -->
    <x-admin.card>
        <x-slot name="headerActions">
            <span class="text-sm text-gray-500 dark:text-gray-400 px-4 py-4">{{ $projects->total() }} projects found</span>
        </x-slot>
        
        @if($projects->count() > 0)
            <x-admin.data-table>
                <x-slot name="columns">
                    <x-admin.table-column sortable="true" field="title" direction="{{ request('sort') === 'title' ? request('direction', 'asc') : null }}">Project</x-admin.table-column>
                    <x-admin.table-column>Category</x-admin.table-column>
                    <x-admin.table-column>Status</x-admin.table-column>
                    <x-admin.table-column sortable="true" field="year" direction="{{ request('sort') === 'year' ? request('direction', 'asc') : null }}">Year</x-admin.table-column>
                    <x-admin.table-column>Actions</x-admin.table-column>
                </x-slot>
                
                @foreach($projects as $project)
                    <x-admin.table-row>
                        <x-admin.table-cell class="max-w-xs truncate" highlight="true">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($project->getFeaturedImageUrlAttribute())
    <img src="{{ $project->getFeaturedImageUrlAttribute() }}">
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-gray-100 dark:bg-neutral-800 rounded">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $project->title }}
                                    </a>
                                    @if($project->location)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $project->location }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            @if($project->category)
                                {{ $project->category }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">None</span>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            <x-admin.badge 
                                type="{{ 
                                    $project->status === 'completed' ? 'success' : 
                                    ($project->status === 'in_progress' ? 'primary' : 'warning') 
                                }}" 
                                dot="true"
                            >
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </x-admin.badge>
                            
                            @if($project->featured)
                                <div class="mt-1">
                                    <x-admin.badge type="info">Featured</x-admin.badge>
                                </div>
                            @endif
                        </x-admin.table-cell>
                        
                        <x-admin.table-cell>
                            {{ $project->year ?? '-' }}
                        </x-admin.table-cell>
                        
                        
                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2">
                                <x-admin.icon-button 
                                    href="{{ route('admin.projects.edit', $project) }}"
                                    tooltip="Edit project"
                                    color="primary"
                                    size="sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-admin.icon-button>
                                
                                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="Delete project"
                                        color="danger"
                                        size="sm"
                                        onclick="return confirm('Are you sure you want to delete this project?')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                                
                                <form action="{{ route('admin.projects.toggle-featured', $project) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="{{ $project->featured ? 'Remove from featured' : 'Mark as featured' }}"
                                        color="{{ $project->featured ? 'warning' : 'info' }}"
                                        size="sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                                
                                <x-admin.icon-button 
                                    href="{{ route('portfolio.show', $project->slug) }}"
                                    tooltip="View on website"
                                    color="light"
                                    size="sm"
                                    target="_blank"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </x-admin.icon-button>
                            </div>
                        </x-admin.table-cell>
                    </x-admin.table-row>
                @endforeach
            </x-admin.data-table>
            
            <div class="px-6 py-4">
                {{ $projects->withQueryString()->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="No projects found" 
                description="There are no projects matching your criteria."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>'
                actionText="Add New Project"
                :actionUrl="route('admin.projects.create')"
            />
        @endif
    </x-admin.card>
</x-admin-layout>