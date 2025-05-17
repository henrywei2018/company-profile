<!-- resources/views/admin/projects/show.blade.php -->
<x-layouts.admin>
    <x-slot name="title">Project Details</x-slot>
    
    <x-slot name="breadcrumbs">
        <li class="inline-flex items-center">
            <svg class="w-5 h-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            <a href="{{ route('admin.projects.index') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-500">Projects</a>
        </li>
        <li class="inline-flex items-center">
            <svg class="w-5 h-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            <span class="text-gray-700 dark:text-gray-300">{{ $project->title }}</span>
        </li>
    </x-slot>
    
    <div class="max-w-5xl mx-auto">
        <!-- Header with Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->title }}</h1>
                <p class="text-gray-500 dark:text-gray-400">{{ $project->excerpt }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.projects.edit', $project) }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('projects.show', $project->slug) }}" target="_blank" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View on Website
                </a>
                <button type="button" 
                    onclick="if(confirm('Are you sure you want to delete this project? This action cannot be undone.')) { document.getElementById('delete-project-form').submit(); }" 
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
                
                <form id="delete-project-form" action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
        
        <!-- Project Tabs -->
        <x-tabs>
            <x-slot name="tabs">
                <x-tab id="details" label="Details" :active="true" />
                <x-tab id="images" label="Images" />
                <x-tab id="content" label="Content" />
                <x-tab id="client" label="Client" />
                <x-tab id="seo" label="SEO" />
            </x-slot>
            
            <x-slot name="content">
                <!-- Details Tab -->
                <x-tab-panel id="details" :active="true">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                        <!-- Featured Image -->
                        <div class="lg:col-span-2">
                            @if($project->featured_image)
                                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <img src="{{ $project->featuredImageUrl }}" alt="{{ $project->title }}" class="w-full h-auto object-cover">
                                </div>
                            @else
                                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 h-64 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Project Details -->
                        <div class="lg:col-span-3">
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Information</h3>
                                </div>
                                <div class="p-6">
                                    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                <x-status-badge status="{{ $project->status }}" />
                                            </dd>
                                        </div>
                                        
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                {{ $project->category ? $project->category->name : 'N/A' }}
                                            </dd>
                                        </div>
                                        
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                @if($project->client)
                                                    <a href="{{ route('admin.clients.show', $project->client) }}" class="text-blue-600 hover:underline dark:text-blue-400">
                                                        {{ $project->client->name }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </dd>
                                        </div>
                                        
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                {{ $project->start_date ? $project->start_date->format('F j, Y') : 'Not set' }}
                                            </dd>
                                        </div>
                                        
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                {{ $project->end_date ? $project->end_date->format('F j, Y') : 'Not set' }}
                                            </dd>
                                        </div>
                                        
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Featured</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                <span class="inline-flex items-center">
                                                    @if($project->is_featured)
                                                        <svg class="w-4 h-4 text-green-500 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Featured
                                                    @else
                                                        <svg class="w-4 h-4 text-red-500 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Not Featured
                                                    @endif
                                                </span>
                                            </dd>
                                        </div>
                                        
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                {{ $project->created_at->format('F j, Y \a\t g:i a') }}
                                            </dd>
                                        </div>
                                        
                                        <div class="py-3 grid grid-cols-3 gap-4">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                                {{ $project->updated_at->format('F j, Y \a\t g:i a') }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-tab-panel>
                
                <!-- Images Tab -->
                <x-tab-panel id="images">
                    <div class="space-y-6">
                        <!-- Featured Image -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Featured Image</h3>
                            @if($project->featured_image)
                                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <img src="{{ $project->featuredImageUrl }}" alt="{{ $project->title }}" class="w-full h-auto max-h-96 object-cover">
                                </div>
                            @else
                                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 h-64 flex items-center justify-center">
                                    <p class="text-gray-500 dark:text-gray-400">No featured image</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Image Gallery -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Image Gallery</h3>
                            @if($project->images && $project->images->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach($project->images as $image)
                                        <div class="relative group border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                                            <div class="aspect-w-1 aspect-h-1">
                                                <img src="{{ $image->url }}" alt="{{ $image->filename }}" class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-8 text-center">
                                    <p class="text-gray-500 dark:text-gray-400">No gallery images</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-tab-panel>
                
                <!-- Content Tab -->
                <x-tab-panel id="content">
                    <div class="space-y-8">
                        <!-- Description -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Description</h3>
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                                <div class="p-6 prose dark:prose-invert max-w-none">
                                    {!! $project->description !!}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Challenge -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Challenge</h3>
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                                <div class="p-6 prose dark:prose-invert max-w-none">
                                    {!! $project->challenge ?: '<p class="text-gray-500 dark:text-gray-400">No challenge description available</p>' !!}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Solution -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Solution</h3>
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                                <div class="p-6 prose dark:prose-invert max-w-none">
                                    {!! $project->solution ?: '<p class="text-gray-500 dark:text-gray-400">No solution description available</p>' !!}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Results -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Results</h3>
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                                <div class="p-6 prose dark:prose-invert max-w-none">
                                    {!! $project->results ?: '<p class="text-gray-500 dark:text-gray-400">No results description available</p>' !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </x-tab-panel>
                
                <!-- Client Tab -->
                <x-tab-panel id="client">
                    @if($project->client)
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Client Information</h3>
                            </div>
                            <div class="p-6">
                                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                            {{ $project->client->name }}
                                        </dd>
                                    </div>
                                    
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Company</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                            {{ $project->client->company ?: 'N/A' }}
                                        </dd>
                                    </div>
                                    
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                            <a href="mailto:{{ $project->client->email }}" class="text-blue-600 hover:underline dark:text-blue-400">
                                                {{ $project->client->email }}
                                            </a>
                                        </dd>
                                    </div>
                                    
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                            <a href="tel:{{ $project->client->phone }}" class="text-blue-600 hover:underline dark:text-blue-400">
                                                {{ $project->client->phone ?: 'N/A' }}
                                            </a>
                                        </dd>
                                    </div>
                                    
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                            {{ $project->client->address ?: 'N/A' }}
                                        </dd>
                                    </div>
                                    
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                            <x-status-badge status="{{ $project->client->status }}" />
                                        </dd>
                                    </div>
                                </dl>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('admin.clients.show', $project->client) }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Client Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Client Projects -->
                        @if($project->client->projects && $project->client->projects->count() > 1)
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Other Projects for this Client</h3>
                                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($project->client->projects->where('id', '!=', $project->id) as $otherProject)
                                            <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <a href="{{ route('admin.projects.show', $otherProject) }}" class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($otherProject->featured_image)
                                                            <img class="h-10 w-10 rounded-md object-cover" src="{{ $otherProject->featuredImageUrl }}" alt="{{ $otherProject->title }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                                <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $otherProject->title }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            <x-status-badge status="{{ $otherProject->status }}" /> · 
                                                            {{ $otherProject->start_date ? $otherProject->start_date->format('M Y') : 'No date' }}
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Client Associated</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                This project does not have a client associated with it.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.projects.edit', $project) }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Project to Add Client
                                </a>
                            </div>
                        </div>
                    @endif
                </x-tab-panel>
                
                <!-- SEO Tab -->
                <x-tab-panel id="seo">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">SEO Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">URL Slug</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                        <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $project->slug }}</code>
                                    </dd>
                                </div>
                                
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meta Title</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                        {{ $project->meta_title ?: $project->title }}
                                    </dd>
                                </div>
                                
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meta Description</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                        {{ $project->meta_description ?: ($project->excerpt ?: 'No meta description') }}
                                    </dd>
                                </div>
                                
                                <div class="py-3 grid grid-cols-3 gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meta Keywords</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                                        @if($project->meta_keywords)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(explode(',', $project->meta_keywords) as $keyword)
                                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs">
                                                        {{ trim($keyword) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">No keywords specified</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                            
                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Search Engine Preview</h4>
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="text-xl text-blue-600 dark:text-blue-400 font-medium truncate">
                                        {{ $project->meta_title ?: $project->title }}
                                    </div>
                                    <div class="text-sm text-green-700 dark:text-green-400 truncate mt-1">
                                        {{ route('projects.show', $project->slug) }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ Str::limit($project->meta_description ?: ($project->excerpt ?: 'No description available'), 160) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-tab-panel>
            </x-slot>
        </x-tabs>
        
        @if(isset($project->testimonial) && $project->testimonial)
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Client Testimonial</h2>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden p-6">
                    <div class="flex items-start">
                        @if($project->testimonial->photo)
                            <div class="flex-shrink-0 mr-4">
                                <img class="h-16 w-16 rounded-full object-cover" src="{{ $project->testimonial->photoUrl }}" alt="{{ $project->testimonial->name }}">
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center mb-2">
                                <div class="flex items-center mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $project->testimonial->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $project->testimonial->rating }}/5</span>
                            </div>
                            <blockquote class="italic text-gray-700 dark:text-gray-300 mb-3">
                                "{{ $project->testimonial->content }}"
                            </blockquote>
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ $project->testimonial->name }}
                                @if($project->testimonial->position || $project->testimonial->company)
                                    <span class="font-normal text-gray-500 dark:text-gray-400">
                                        @if($project->testimonial->position)
                                            - {{ $project->testimonial->position }}
                                        @endif
                                        @if($project->testimonial->company)
                                            {{ $project->testimonial->position ? ',' : '-' }} {{ $project->testimonial->company }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('admin.testimonials.edit', $project->testimonial) }}" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                                    Edit Testimonial
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Client Testimonial</h2>
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Testimonial Available</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        This project does not have any testimonials from the client.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('admin.testimonials.create', ['project_id' => $project->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Testimonial
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>