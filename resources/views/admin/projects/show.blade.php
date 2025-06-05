{{-- resources/views/admin/projects/show.blade.php --}}
<x-layouts.admin title="Project Details">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                    {{ $project->title }}
                </h1>
                @if($project->featured)
                    <x-admin.badge type="warning" class="ml-3">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Featured
                    </x-admin.badge>
                @endif
            </div>
            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                @if($project->client)
                    <span>Client: {{ $project->client->name }}</span>
                @endif
                @if($project->category)
                    <span>{{ $project->category->name }}</span>
                @endif
                @if($project->location)
                    <span>{{ $project->location }}</span>
                @endif
                <span>Created {{ $project->created_at->format('M j, Y') }}</span>
            </div>
        </div>
        
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <!-- Status Badge -->
            <x-admin.badge 
                type="{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'warning' : 'info') }}"
                size="lg"
            >
                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
            </x-admin.badge>
            
            <!-- Priority Badge -->
            <x-admin.badge 
                type="{{ $project->priority === 'urgent' ? 'danger' : ($project->priority === 'high' ? 'warning' : 'light') }}"
            >
                {{ ucfirst($project->priority) }} Priority
            </x-admin.badge>
            
            <!-- Action Buttons -->
            <x-admin.button 
                href="{{ route('admin.projects.edit', $project) }}" 
                color="primary"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </x-admin.button>
            
            @if($project->slug)
                <x-admin.button 
                    href="{{ route('projects.show', $project->slug) }}" 
                    color="light"
                    size="sm"
                    target="_blank"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    View Live
                </x-admin.button>
            @endif
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => '#'
    ]" class="mb-6" />

    <!-- Project Status Alerts -->
    @if($project->isOverdue())
        <x-admin.alert type="danger" class="mb-6">
            <x-slot name="title">Project Overdue</x-slot>
            This project is {{ now()->diffInDays($project->end_date) }} days overdue. 
            <a href="{{ route('admin.projects.edit', $project) }}" class="font-medium underline">Update timeline</a>
        </x-admin.alert>
    @elseif($project->end_date && $project->end_date->diffInDays(now()) <= 7 && $project->status === 'in_progress')
        <x-admin.alert type="warning" class="mb-6">
            <x-slot name="title">Deadline Approaching</x-slot>
            This project is due in {{ now()->diffInDays($project->end_date) }} days.
        </x-admin.alert>
    @endif

    <!-- Project Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <x-admin.stat-card
            title="Progress"
            :value="($project->progress_percentage ?? 0) . '%'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
            iconColor="text-blue-500"
            iconBg="bg-blue-100 dark:bg-blue-800/30"
        >
            <x-slot name="footer">
                <x-admin.progress 
                    :value="$project->progress_percentage ?? 0" 
                    height="xs"
                    color="blue"
                />
            </x-slot>
        </x-admin.stat-card>
        
        <x-admin.stat-card
            title="Images"
            :value="$project->images->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
            iconColor="text-green-500"
            iconBg="bg-green-100 dark:bg-green-800/30"
            :href="'#images-section'"
        />
        
        <x-admin.stat-card
            title="Files"
            :value="$project->files->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
            iconColor="text-purple-500"
            iconBg="bg-purple-100 dark:bg-purple-800/30"
            :href="'#files-section'"
        />
        
        <x-admin.stat-card
            title="Milestones"
            :value="$project->milestones->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/>'
            iconColor="text-amber-500"
            iconBg="bg-amber-100 dark:bg-amber-800/30"
            :href="'#milestones-section'"
        />
        
        <x-admin.stat-card
            title="Messages"
            :value="$project->messages->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'
            iconColor="text-indigo-500"
            iconBg="bg-indigo-100 dark:bg-indigo-800/30"
            :href="route('admin.messages.index', ['project' => $project->id])"
        />
    </div>

    <!-- Main Content Tabs -->
    <x-admin.tabs activeTab="overview">
        <x-slot name="tabs">
            <x-admin.tab id="overview" label="Overview" :active="true" />
            <x-admin.tab id="milestones" label="Milestones ({{ $project->milestones->count() }})" />
            <x-admin.tab id="files" label="Files ({{ $project->files->count() }})" />
            <x-admin.tab id="images" label="Images ({{ $project->images->count() }})" />
            <x-admin.tab id="timeline" label="Timeline" />
            <x-admin.tab id="settings" label="Settings" />
        </x-slot>
        
        <x-slot name="content">
            <!-- Overview Tab -->
            <x-admin.tab-panel id="overview" :active="true">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Project Description -->
                        @if($project->description)
                            <x-admin.card title="Project Description">
                                <div class="prose dark:prose-invert max-w-none">
                                    {!! $project->description !!}
                                </div>
                            </x-admin.card>
                        @endif
                        
                        <!-- Challenge, Solution, Results -->
                        @if($project->challenge || $project->solution || $project->results)
                            <div class="grid grid-cols-1 gap-6">
                                @if($project->challenge)
                                    <x-admin.card title="Challenge">
                                        <div class="prose dark:prose-invert max-w-none">
                                            {!! $project->challenge !!}
                                        </div>
                                    </x-admin.card>
                                @endif
                                
                                @if($project->solution)
                                    <x-admin.card title="Solution">
                                        <div class="prose dark:prose-invert max-w-none">
                                            {!! $project->solution !!}
                                        </div>
                                    </x-admin.card>
                                @endif
                                
                                @if($project->results)
                                    <x-admin.card title="Results">
                                        <div class="prose dark:prose-invert max-w-none">
                                            {!! $project->results !!}
                                        </div>
                                    </x-admin.card>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Services & Technologies -->
                        @if($project->services_used || $project->technologies_used)
                            <x-admin.card title="Services & Technologies">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($project->services_used)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Services Used</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($project->services_used as $service)
                                                    <x-admin.badge type="info">{{ $service }}</x-admin.badge>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($project->technologies_used)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Technologies</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($project->technologies_used as $tech)
                                                    <x-admin.badge type="light">{{ $tech }}</x-admin.badge>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </x-admin.card>
                        @endif
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Project Details -->
                        <x-admin.card title="Project Details">
                            <dl class="space-y-4">
                                @if($project->client)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            <a href="{{ route('admin.users.show', $project->client) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                {{ $project->client->name }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                                
                                @if($project->category)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $project->category->name }}</dd>
                                    </div>
                                @endif
                                
                                @if($project->budget)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($project->budget, 2) }}</dd>
                                    </div>
                                @endif
                                
                                @if($project->start_date)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $project->start_date->format('M j, Y') }}</dd>
                                    </div>
                                @endif
                                
                                @if($project->end_date)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deadline</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ $project->end_date->format('M j, Y') }}
                                            @if($project->isOverdue())
                                                <span class="text-red-600 font-medium">(Overdue)</span>
                                            @elseif($project->end_date->diffInDays(now()) <= 7)
                                                <span class="text-amber-600 font-medium">(Due Soon)</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                                
                                @if($project->actual_completion_date)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $project->actual_completion_date->format('M j, Y') }}</dd>
                                    </div>
                                @endif
                                
                                @if($project->year)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Year</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $project->year }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </x-admin.card>
                        
                        <!-- Quotation Info -->
                        @if($project->quotation)
                            <x-admin.card title="Related Quotation">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Quotation ID</span>
                                        <span class="text-sm font-medium">#{{ $project->quotation->id }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                        <x-admin.badge type="success">{{ ucfirst($project->quotation->status) }}</x-admin.badge>
                                    </div>
                                    <div class="pt-3">
                                        <x-admin.button 
                                            href="{{ route('admin.quotations.show', $project->quotation) }}" 
                                            color="light" 
                                            size="sm"
                                            class="w-full"
                                        >
                                            View Quotation
                                        </x-admin.button>
                                    </div>
                                </div>
                            </x-admin.card>
                        @endif
                        
                        <!-- Quick Actions -->
                        <x-admin.card title="Quick Actions">
                            <div class="space-y-2">
                                <x-admin.button 
                                    href="{{ route('admin.projects.milestones.create', $project) }}" 
                                    color="light" 
                                    size="sm"
                                    class="w-full"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add Milestone
                                </x-admin.button>
                                
                                <x-admin.button 
                                    href="{{ route('admin.projects.files.create', $project) }}" 
                                    color="light" 
                                    size="sm"
                                    class="w-full"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Upload Files
                                </x-admin.button>
                                
                                <x-admin.button 
                                    href="{{ route('admin.messages.create', ['project_id' => $project->id]) }}" 
                                    color="light" 
                                    size="sm"
                                    class="w-full"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Send Message
                                </x-admin.button>
                            </div>
                        </x-admin.card>
                    </div>
                </div>
            </x-admin.tab-panel>
            
            <!-- Milestones Tab -->
            <x-admin.tab-panel id="milestones">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Milestones</h3>
                    <x-admin.button 
                        href="{{ route('admin.projects.milestones.create', $project) }}" 
                        color="primary"
                        size="sm"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Milestone
                    </x-admin.button>
                </div>
                
                @if($project->milestones->count() > 0)
                    <div class="space-y-4">
                        @foreach($project->milestones->sortBy('due_date') as $milestone)
                            <x-admin.card>
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                {{ $milestone->title }}
                                            </h4>
                                            <x-admin.badge 
                                                type="{{ $milestone->status === 'completed' ? 'success' : ($milestone->isOverdue() ? 'danger' : 'warning') }}"
                                                class="ml-3"
                                            >
                                                {{ ucfirst($milestone->status) }}
                                            </x-admin.badge>
                                        </div>
                                        
                                        @if($milestone->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $milestone->description }}</p>
                                        @endif
                                        
                                        <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                                            @if($milestone->due_date)
                                                <span>Due: {{ $milestone->due_date->format('M j, Y') }}</span>
                                            @endif
                                            @if($milestone->completion_date)
                                                <span>Completed: {{ $milestone->completion_date->format('M j, Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 ml-4">
                                        <x-admin.icon-button
                                            href="{{ route('admin.projects.milestones.edit', [$project, $milestone]) }}"
                                            color="primary"
                                            size="sm"
                                            tooltip="Edit milestone"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </x-admin.icon-button>
                                        
                                        @if($milestone->status !== 'completed')
                                            <form method="POST" action="{{ route('admin.projects.milestones.complete', [$project, $milestone]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <x-admin.icon-button
                                                    type="submit"
                                                    color="success"
                                                    size="sm"
                                                    tooltip="Mark as completed"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </x-admin.icon-button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </x-admin.card>
                        @endforeach
                    </div>
                @else
                    <x-admin.empty-state
                        title="No Milestones Yet"
                        description="Add milestones to track project progress and important deadlines."
                        actionText="Add First Milestone"
                        :actionUrl="route('admin.projects.milestones.create', $project)"
                        icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/></svg>'
                    />
                @endif
            </x-admin.tab-panel>
            
            <!-- Files Tab -->
            <x-admin.tab-panel id="files">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Files</h3>
                    <x-admin.button 
                        href="{{ route('admin.projects.files.create', $project) }}" 
                        color="primary"
                        size="sm"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Files
                    </x-admin.button>
                </div>
                
                @if($project->files->count() > 0)
                    <x-admin.data-table>
                        <x-slot name="columns">
                            <x-admin.table-column>File</x-admin.table-column>
                            <x-admin.table-column>Type</x-admin.table-column>
                            <x-admin.table-column>Size</x-admin.table-column>
                            <x-admin.table-column>Downloads</x-admin.table-column>
                            <x-admin.table-column>Uploaded</x-admin.table-column>
                            <x-admin.table-column>Actions</x-admin.table-column>
                        </x-slot>
                        
                        @foreach($project->files as $file)
                            <x-admin.table-row>
                                <x-admin.table-cell highlight>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 flex-shrink-0 mr-3">
                                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $file->file_name }}</p>
                                            @if($file->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($file->description, 50) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </x-admin.table-cell>
                                
                                <x-admin.table-cell>
                                    <x-admin.badge type="light">{{ $file->file_icon }}</x-admin.badge>
                                </x-admin.table-cell>
                                
                                <x-admin.table-cell>{{ $file->formatted_file_size }}</x-admin.table-cell>
                                
                                <x-admin.table-cell>{{ $file->download_count }}</x-admin.table-cell>
                                
                                <x-admin.table-cell>{{ $file->created_at->format('M j, Y') }}</x-admin.table-cell>
                                
                                <x-admin.table-cell>
                                    <div class="flex items-center space-x-2">
                                        <x-admin.icon-button
                                            href="{{ route('admin.projects.files.download', [$project, $file]) }}"
                                            color="primary"
                                            size="sm"
                                            tooltip="Download file"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </x-admin.icon-button>
                                        
                                        <form method="POST" action="{{ route('admin.projects.files.destroy', [$project, $file]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.icon-button
                                                type="submit"
                                                color="danger"
                                                size="sm"
                                                tooltip="Delete file"
                                                onclick="return confirm('Are you sure you want to delete this file?')"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </x-admin.icon-button>
                                        </form>
                                    </div>
                                </x-admin.table-cell>
                            </x-admin.table-row>
                        @endforeach
                    </x-admin.data-table>
                @else
                    <x-admin.empty-state
                        title="No Files Uploaded"
                        description="Upload project files, documents, and resources for easy access."
                        actionText="Upload First File"
                        :actionUrl="route('admin.projects.files.create', $project)"
                        icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                    />
                @endif
            </x-admin.tab-panel>
            
            <!-- Images Tab -->
            <x-admin.tab-panel id="images">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Images</h3>
                    <x-admin.button 
                        href="{{ route('admin.projects.edit', $project) }}#images-section" 
                        color="primary"
                        size="sm"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Manage Images
                    </x-admin.button>
                </div>
                
                @if($project->images->count() > 0)
                    <x-admin.image-gallery
                        :images="$project->images->map(fn($img) => [
                            'path' => $img->image_path,
                            'alt' => $img->alt_text,
                            'caption' => $img->alt_text,
                            'id' => $img->id,
                            'is_featured' => $img->is_featured
                        ])"
                        :columns="3"
                        :lightbox="true"
                        aspectRatio="4:3"
                    />
                @else
                    <x-admin.empty-state
                        title="No Images Uploaded"
                        description="Add images to showcase this project in your portfolio."
                        actionText="Add Images"
                        :actionUrl="route('admin.projects.edit', $project) . '#images-section'"
                        icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                    />
                @endif
            </x-admin.tab-panel>
            
            <!-- Timeline Tab -->
            <x-admin.tab-panel id="timeline">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Project Timeline</h3>
                
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        <!-- Project Created -->
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Project created</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ $project->created_at->format('M j, Y g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        <!-- Milestones -->
                        @foreach($project->milestones->sortBy('due_date') as $milestone)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $milestone->status === 'completed' ? 'bg-green-500' : ($milestone->isOverdue() ? 'bg-red-500' : 'bg-blue-500') }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                @if($milestone->status === 'completed')
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $milestone->title }}</p>
                                                @if($milestone->description)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $milestone->description }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                @if($milestone->completion_date)
                                                    {{ $milestone->completion_date->format('M j, Y') }}
                                                @elseif($milestone->due_date)
                                                    Due {{ $milestone->due_date->format('M j, Y') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </x-admin.tab-panel>
            
            <!-- Settings Tab -->
            <x-admin.tab-panel id="settings">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Project Settings</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Status & Visibility -->
                    <x-admin.card title="Status & Visibility">
                        <form method="POST" action="{{ route('admin.projects.quick-update', $project) }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="status" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                        <option value="planning" {{ $project->status === 'planning' ? 'selected' : '' }}>Planning</option>
                                        <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $project->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                                    <select name="priority" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                        <option value="low" {{ $project->priority === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="normal" {{ $project->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="high" {{ $project->priority === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ $project->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Progress (%)</label>
                                    <input type="number" name="progress_percentage" min="0" max="100" 
                                           value="{{ $project->progress_percentage ?? 0 }}" 
                                           class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="featured" value="1" {{ $project->featured ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Project</label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ $project->is_active ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Project</label>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <x-admin.button type="submit" color="primary" size="sm">
                                    Update Settings
                                </x-admin.button>
                            </div>
                        </form>
                    </x-admin.card>
                    
                    <!-- Danger Zone -->
                    <x-admin.card title="Danger Zone">
                        <div class="space-y-4">
                            <div class="p-4 bg-red-50 border border-red-200 rounded-md dark:bg-red-900/20 dark:border-red-800">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Delete Project</h4>
                                <p class="text-sm text-red-700 dark:text-red-300 mb-4">
                                    Once you delete a project, there is no going back. Please be certain.
                                </p>
                                <x-admin.button 
                                    type="button" 
                                    color="danger" 
                                    size="sm"
                                    onclick="confirmDelete()"
                                >
                                    Delete Project
                                </x-admin.button>
                            </div>
                            
                            @if($project->quotation)
                                <div class="p-4 bg-amber-50 border border-amber-200 rounded-md dark:bg-amber-900/20 dark:border-amber-800">
                                    <h4 class="text-sm font-medium text-amber-800 dark:text-amber-400 mb-2">Convert Back to Quotation</h4>
                                    <p class="text-sm text-amber-700 dark:text-amber-300 mb-4">
                                        Convert this project back to quotation status if needed.
                                    </p>
                                    <x-admin.button 
                                        href="{{ route('admin.projects.convert-to-quotation', $project) }}" 
                                        color="warning" 
                                        size="sm"
                                        onclick="return confirm('Are you sure you want to convert this project back to a quotation?')"
                                    >
                                        Convert to Quotation
                                    </x-admin.button>
                                </div>
                            @endif
                        </div>
                    </x-admin.card>
                </div>
            </x-admin.tab-panel>
        </x-slot>
    </x-admin.tabs>

    <!-- Delete Confirmation Modal -->
    <x-admin.modal id="delete-project-modal" title="Delete Project" size="lg">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <div class="mb-4">
                <p class="font-medium text-red-600 dark:text-red-400 mb-2">
                    Are you sure you want to delete "{{ $project->title }}"?
                </p>
                <p class="mb-4">This action cannot be undone and will permanently delete:</p>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                <ul class="list-disc list-inside space-y-1">
                    <li>{{ $project->images->count() }} project images</li>
                    <li>{{ $project->files->count() }} project files</li>
                    <li>{{ $project->milestones->count() }} project milestones</li>
                    <li>{{ $project->messages->count() }} related messages</li>
                    <li>All project history and analytics data</li>
                </ul>
            </div>
        </div>
        
        <x-slot name="footer">
            <x-admin.button 
                color="light" 
                onclick="document.getElementById('delete-project-modal').classList.add('hidden')"
            >
                Cancel
            </x-admin.button>
            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" color="danger">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Project Permanently
                </x-admin.button>
            </form>
        </x-slot>
    </x-admin.modal>
    
    <!-- Quick Actions FAB -->
    <div class="fixed bottom-4 right-4 z-50">
        <x-admin.floating-action-button :actions="[
            [
                'title' => 'Edit Project', 
                'href' => route('admin.projects.edit', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\"/>',
                'color_classes' => 'bg-blue-600 hover:bg-blue-700'
            ],
            [
                'title' => 'Add Milestone', 
                'href' => route('admin.projects.milestones.create', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"/>',
                'color_classes' => 'bg-green-600 hover:bg-green-700'
            ],
            [
                'title' => 'Upload Files', 
                'href' => route('admin.projects.files.create', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12\"/>',
                'color_classes' => 'bg-purple-600 hover:bg-purple-700'
            ],
            [
                'title' => 'Send Message', 
                'href' => route('admin.messages.create', ['project_id' => $project->id]),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z\"/>',
                'color_classes' => 'bg-indigo-600 hover:bg-indigo-700'
            ],
            [
                'title' => 'All Projects', 
                'href' => route('admin.projects.index'),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10\"/>',
                'color_classes' => 'bg-gray-600 hover:bg-gray-700'
            ]
        ]" />
    </div>
</x-layouts.admin>

@push('scripts')
<script>
function confirmDelete() {
    document.getElementById('delete-project-modal').classList.remove('hidden');
}

// Auto-update project progress when milestones are completed
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for stat card links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Auto-save settings form
    const settingsForm = document.querySelector('form[action*="quick-update"]');
    if (settingsForm) {
        const inputs = settingsForm.querySelectorAll('select, input[type="number"]');
        let saveTimeout;
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                clearTimeout(saveTimeout);
                showSaveIndicator('saving');
                
                saveTimeout = setTimeout(() => {
                    const formData = new FormData(settingsForm);
                    fetch(settingsForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSaveIndicator('saved');
                            // Update any UI elements that need refreshing
                            updateProjectStatus(data.project);
                        } else {
                            showSaveIndicator('error');
                        }
                    })
                    .catch(error => {
                        console.error('Save error:', error);
                        showSaveIndicator('error');
                    });
                }, 1000);
            });
        });
    }
});

function showSaveIndicator(status) {
    // Remove existing indicators
    const existing = document.querySelector('.save-indicator');
    if (existing) existing.remove();
    
    const indicator = document.createElement('div');
    indicator.className = 'save-indicator fixed top-4 right-4 px-3 py-2 rounded-md text-sm z-50';
    
    if (status === 'saving') {
        indicator.className += ' bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        indicator.innerHTML = '<div class="flex items-center"><svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...</div>';
    } else if (status === 'saved') {
        indicator.className += ' bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        indicator.innerHTML = '<div class="flex items-center"><svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Saved</div>';
    } else if (status === 'error') {
        indicator.className += ' bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
        indicator.innerHTML = '<div class="flex items-center"><svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>Error saving</div>';
    }
    
    document.body.appendChild(indicator);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (indicator.parentNode) {
            indicator.remove();
        }
    }, 3000);
}

function updateProjectStatus(project) {
    // Update status badges and other UI elements
    const statusBadges = document.querySelectorAll('.project-status-badge');
    statusBadges.forEach(badge => {
        badge.textContent = project.formatted_status;
        badge.className = `project-status-badge ${project.status_color}`;
    });
    
    // Update progress bars
    const progressBars = document.querySelectorAll('.project-progress');
    progressBars.forEach(bar => {
        bar.style.width = project.progress_percentage + '%';
        bar.setAttribute('aria-valuenow', project.progress_percentage);
    });
}
</script>
@endpush