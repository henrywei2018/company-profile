{{-- resources/views/admin/projects/milestones/index.blade.php --}}
<x-layouts.admin title="Project Milestones">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Project Milestones</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage milestones for {{ $project->title }}
            </p>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <!-- Project Status -->
            <x-admin.badge 
                type="{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'warning' : 'info') }}"
                size="lg"
            >
                Project: {{ ucfirst(str_replace('_', ' ', $project->status)) }}
            </x-admin.badge>
            
            <!-- Add Milestone Button -->
            <x-admin.button 
                href="{{ route('admin.projects.milestones.create', $project) }}" 
                icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'
            >
                Add Milestone
            </x-admin.button>
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Milestones' => '#'
    ]" class="mb-6" />

    <!-- Project Overview Card -->
    <x-admin.card class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if($project->featured_image_url)
                        <img src="{{ $project->featured_image_url }}" alt="{{ $project->title }}" 
                             class="h-16 w-16 object-cover rounded-lg">
                    @else
                        <div class="h-16 w-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">{{ $project->title }}</h3>
                    <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($project->client)
                            <span>Client: {{ $project->client->name }}</span>
                        @endif
                        @if($project->end_date)
                            <span>Due: {{ $project->end_date->format('M j, Y') }}</span>
                        @endif
                        <span>{{ $milestones->where('status', 'completed')->count() }}/{{ $milestones->count() }} milestones completed</span>
                    </div>
                </div>
            </div>
            
            <div class="flex-shrink-0">
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->progress_percentage ?? 0 }}%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Overall Progress</div>
                </div>
                <div class="mt-2 w-32">
                    <x-admin.progress 
                        :value="$project->progress_percentage ?? 0" 
                        height="sm"
                        showLabel="false"
                        color="blue"
                    />
                </div>
            </div>
        </div>
    </x-admin.card>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <x-admin.stat-card
            title="Total Milestones"
            :value="$milestones->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/>'
            iconColor="text-blue-500"
            iconBg="bg-blue-100 dark:bg-blue-800/30"
        />
        
        <x-admin.stat-card
            title="Completed"
            :value="$milestones->where('status', 'completed')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-green-500"
            iconBg="bg-green-100 dark:bg-green-800/30"
        />
        
        <x-admin.stat-card
            title="In Progress"
            :value="$milestones->where('status', 'in_progress')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-amber-500"
            iconBg="bg-amber-100 dark:bg-amber-800/30"
        />
        
        <x-admin.stat-card
            title="Overdue"
            :value="$milestones->filter(fn($m) => $m->isOverdue())->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
            iconColor="text-red-500"
            iconBg="bg-red-100 dark:bg-red-800/30"
        />
        
        <x-admin.stat-card
            title="Due Soon"
            :value="$milestones->filter(fn($m) => $m->isDueSoon())->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5V3h0z"/>'
            iconColor="text-orange-500"
            iconBg="bg-orange-100 dark:bg-orange-800/30"
        />
    </div>

    <!-- Alerts for Critical Milestones -->
    @if($milestones->filter(fn($m) => $m->isOverdue())->count() > 0)
        <x-admin.alert type="danger" class="mb-6">
            <x-slot name="title">{{ $milestones->filter(fn($m) => $m->isOverdue())->count() }} Overdue Milestones</x-slot>
            You have milestones that are past their due date. Consider updating timelines or marking them as completed if they're done.
            <div class="mt-2">
                @foreach($milestones->filter(fn($m) => $m->isOverdue())->take(3) as $overdue)
                    <div class="text-sm">
                        • <strong>{{ $overdue->title }}</strong> - {{ $overdue->days_overdue }} days overdue
                    </div>
                @endforeach
            </div>
        </x-admin.alert>
    @elseif($milestones->filter(fn($m) => $m->isDueSoon())->count() > 0)
        <x-admin.alert type="warning" class="mb-6">
            <x-slot name="title">{{ $milestones->filter(fn($m) => $m->isDueSoon())->count() }} Milestones Due Soon</x-slot>
            Some milestones are approaching their due dates within the next 7 days.
        </x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.projects.milestones.index', $project) }}" method="GET" 
                    :resetRoute="route('admin.projects.milestones.index', $project)">
        <x-admin.select
            name="status"
            label="Status"
            :options="[
                '' => 'All Statuses',
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'delayed' => 'Delayed'
            ]"
            :value="request('status')"
        />
        
        <x-admin.select
            name="priority"
            label="Priority"
            :options="[
                '' => 'All Priorities',
                'low' => 'Low',
                'normal' => 'Normal',
                'high' => 'High',
                'critical' => 'Critical'
            ]"
            :value="request('priority')"
        />
        
        <x-admin.input
            name="search"
            label="Search"
            placeholder="Search milestones..."
            :value="request('search')"
        />
        
        <x-admin.select
            name="view"
            label="View"
            :options="[
                'list' => 'List View',
                'timeline' => 'Timeline View',
                'kanban' => 'Kanban Board'
            ]"
            :value="request('view', 'list')"
        />
    </x-admin.filter>

    <!-- View Toggle Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.projects.milestones.index', array_merge([$project], request()->except('view'))) }}" 
                   class="@if(!request('view') || request('view') === 'list') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    List View
                </a>
                
                <a href="{{ route('admin.projects.milestones.index', array_merge([$project], request()->except('view'), ['view' => 'timeline'])) }}" 
                   class="@if(request('view') === 'timeline') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/>
                    </svg>
                    Timeline
                </a>
                
                <a href="{{ route('admin.projects.milestones.index', array_merge([$project], request()->except('view'), ['view' => 'kanban'])) }}" 
                   class="@if(request('view') === 'kanban') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    Kanban
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content based on view type -->
    @if(request('view') === 'timeline')
        <!-- Timeline View -->
        <x-admin.card>
            <x-slot name="headerActions">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $filteredMilestones->count() }} milestones</span>
            </x-slot>
            
            @if($filteredMilestones->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($filteredMilestones->sortBy('due_date') as $milestone)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div>
                                            <span class="h-10 w-10 rounded-full {{ 
                                                $milestone->status === 'completed' ? 'bg-green-500' : 
                                                ($milestone->isOverdue() ? 'bg-red-500' : 'bg-blue-500') 
                                            }} flex items-center justify-center ring-8 ring-white dark:ring-gray-900">
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
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <h4 class="text-base font-medium text-gray-900 dark:text-white">{{ $milestone->title }}</h4>
                                                        <x-admin.badge type="{{ $milestone->status_color }}">
                                                            {{ $milestone->formatted_status }}
                                                        </x-admin.badge>
                                                        @if($milestone->priority !== 'normal')
                                                            <x-admin.badge type="{{ $milestone->priority_color }}" size="sm">
                                                                {{ ucfirst($milestone->priority) }}
                                                            </x-admin.badge>
                                                        @endif
                                                    </div>
                                                    @if($milestone->description)
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($milestone->description, 100) }}</p>
                                                    @endif
                                                    <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                        @if($milestone->due_date)
                                                            <span>Due: {{ $milestone->due_date->format('M j, Y') }}</span>
                                                        @endif
                                                        @if($milestone->completion_date)
                                                            <span>Completed: {{ $milestone->completion_date->format('M j, Y') }}</span>
                                                        @endif
                                                        @if($milestone->progress_percent)
                                                            <span>Progress: {{ $milestone->progress_percent }}%</span>
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
                                                                onclick="return confirm('Mark this milestone as completed?')"
                                                            >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            </x-admin.icon-button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($milestone->progress_percent > 0)
                                                <div class="mt-3">
                                                    <x-admin.progress 
                                                        :value="$milestone->progress_percent" 
                                                        height="xs"
                                                        color="{{ $milestone->status === 'completed' ? 'green' : 'blue' }}"
                                                    />
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <x-admin.empty-state
                    title="No Milestones Found"
                    description="No milestones match your current filters."
                    actionText="Add First Milestone"
                    :actionUrl="route('admin.projects.milestones.create', $project)"
                />
            @endif
        </x-admin.card>
        
    @elseif(request('view') === 'kanban')
        <!-- Kanban Board View -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="kanbanBoard()">
            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'delayed' => 'Delayed'] as $status => $statusLabel)
                <x-admin.card title="{{ $statusLabel }}">
                    <x-slot name="headerActions">
                        <x-admin.badge type="{{ 
                            $status === 'completed' ? 'success' : 
                            ($status === 'delayed' ? 'danger' : 
                            ($status === 'in_progress' ? 'warning' : 'light'))
                        }}" size="sm">
                            {{ $filteredMilestones->where('status', $status)->count() }}
                        </x-admin.badge>
                    </x-slot>
                    
                    <div class="space-y-3 min-h-96" data-status="{{ $status }}">
                        @foreach($filteredMilestones->where('status', $status) as $milestone)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border cursor-move" 
                                 draggable="true" 
                                 data-milestone-id="{{ $milestone->id }}"
                                 x-on:dragstart="dragStart($event, {{ $milestone->id }})">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $milestone->title }}</h4>
                                        @if($milestone->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($milestone->description, 60) }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center space-x-1 ml-2">
                                        @if($milestone->priority !== 'normal')
                                            <div class="w-2 h-2 rounded-full {{ 
                                                $milestone->priority === 'critical' ? 'bg-red-500' : 
                                                ($milestone->priority === 'high' ? 'bg-orange-500' : 'bg-yellow-500')
                                            }}"></div>
                                        @endif
                                        
                                        <x-admin.icon-button
                                            href="{{ route('admin.projects.milestones.edit', [$project, $milestone]) }}"
                                            color="light"
                                            size="sm"
                                            tooltip="Edit"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </x-admin.icon-button>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    @if($milestone->due_date)
                                        <span class="{{ $milestone->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                                            {{ $milestone->due_date->format('M j') }}
                                        </span>
                                    @else
                                        <span>No due date</span>
                                    @endif
                                    
                                    @if($milestone->progress_percent > 0)
                                        <span>{{ $milestone->progress_percent }}%</span>
                                    @endif
                                </div>
                                
                                @if($milestone->progress_percent > 0)
                                    <div class="mt-2">
                                        <x-admin.progress 
                                            :value="$milestone->progress_percent" 
                                            height="xs"
                                            showLabel="false"
                                            color="blue"
                                        />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        @if($filteredMilestones->where('status', $status)->isEmpty())
                            <div class="flex items-center justify-center h-32 text-gray-400 dark:text-gray-500 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg"
                                 x-on:drop="drop($event, '{{ $status }}')"
                                 x-on:dragover.prevent
                                 x-on:dragenter.prevent>
                                <span class="text-sm">Drop milestones here</span>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endforeach
        </div>
        
    @else
        <!-- Default List View -->
        <x-admin.card>
            <x-slot name="headerActions">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $filteredMilestones->count() }} milestones</span>
                    
                    @if($filteredMilestones->count() > 1)
                        <x-admin.button color="light" size="sm" onclick="toggleBulkActions()">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/>
                            </svg>
                            Bulk Actions
                        </x-admin.button>
                    @endif
                </div>
            </x-slot>
            
            <!-- Bulk Actions Bar (Hidden by default) -->
            <div id="bulk-actions-bar" class="hidden bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800 p-4 -mx-6 -mt-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600">
                        <label for="select-all" class="text-sm font-medium text-blue-900 dark:text-blue-400">Select All</label>
                        <span id="selected-count" class="text-sm text-blue-700 dark:text-blue-300">0 selected</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <select id="bulk-action-select" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="">Choose action...</option>
                            <option value="complete">Mark as Completed</option>
                            <option value="in_progress">Mark as In Progress</option>
                            <option value="pending">Mark as Pending</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        
                        <x-admin.button color="primary" size="sm" onclick="executeBulkAction()">
                            Apply
                        </x-admin.button>
                        
                        <x-admin.button color="light" size="sm" onclick="toggleBulkActions()">
                            Cancel
                        </x-admin.button>
                    </div>
                </div>
            </div>
            
            @if($filteredMilestones->count() > 0)
                <x-admin.data-table>
                    <x-slot name="columns">
                        <x-admin.table-column width="w-12">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 bulk-select-toggle hidden">
                        </x-admin.table-column>
                        <x-admin.table-column sortable="true" field="title">Milestone</x-admin.table-column>
                        <x-admin.table-column>Status</x-admin.table-column>
                        <x-admin.table-column>Priority</x-admin.table-column>
                        <x-admin.table-column sortable="true" field="due_date">Due Date</x-admin.table-column>
                        <x-admin.table-column>Progress</x-admin.table-column>
                        <x-admin.table-column>Actions</x-admin.table-column>
                    </x-slot>
                    
                    @foreach($filteredMilestones->sortBy('sort_order') as $milestone)
                        <x-admin.table-row>
                            <x-admin.table-cell>
                                <input type="checkbox" 
                                       class="rounded border-gray-300 text-blue-600 milestone-checkbox bulk-select-toggle hidden" 
                                       value="{{ $milestone->id }}"
                                       data-milestone-id="{{ $milestone->id }}">
                            </x-admin.table-cell>
                            
                            <x-admin.table-cell highlight>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full {{ 
                                            $milestone->status === 'completed' ? 'bg-green-100 text-green-600' : 
                                            ($milestone->isOverdue() ? 'bg-red-100 text-red-600' : 
                                            ($milestone->status === 'in_progress' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'))
                                        }} flex items-center justify-center">
                                            @if($milestone->status === 'completed')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <span class="text-xs font-medium">{{ $loop->iteration }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $milestone->title }}
                                        </p>
                                        @if($milestone->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ Str::limit($milestone->description, 80) }}
                                            </p>
                                        @endif
                                        
                                        @if($milestone->dependencies && count($milestone->dependencies) > 0)
                                            <div class="flex items-center mt-1">
                                                <svg class="w-3 h-3 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                                </svg>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    Depends on {{ count($milestone->dependencies) }} milestone(s)
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </x-admin.table-cell>
                            
                            <x-admin.table-cell>
                                <x-admin.badge type="{{ $milestone->status_color }}">
                                    {{ $milestone->formatted_status }}
                                </x-admin.badge>
                                
                                @if($milestone->isOverdue() && $milestone->status !== 'completed')
                                    <div class="mt-1">
                                        <x-admin.badge type="danger" size="sm">
                                            {{ $milestone->days_overdue }} days overdue
                                        </x-admin.badge>
                                    </div>
                                @elseif($milestone->isDueSoon() && $milestone->status !== 'completed')
                                    <div class="mt-1">
                                        <x-admin.badge type="warning" size="sm">
                                            Due in {{ $milestone->days_until_due }} days
                                        </x-admin.badge>
                                    </div>
                                @endif
                            </x-admin.table-cell>
                            
                            <x-admin.table-cell>
                                <x-admin.badge type="{{ $milestone->priority_color }}" size="sm">
                                    {{ $milestone->formatted_priority }}
                                </x-admin.badge>
                            </x-admin.table-cell>
                            
                            <x-admin.table-cell>
                                @if($milestone->due_date)
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $milestone->due_date->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $milestone->due_date->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">No due date</span>
                                @endif
                            </x-admin.table-cell>
                            
                            <x-admin.table-cell>
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1">
                                        <x-admin.progress 
                                            :value="$milestone->progress_percent ?? 0" 
                                            height="xs"
                                            showLabel="false"
                                            color="{{ $milestone->status === 'completed' ? 'green' : 'blue' }}"
                                        />
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right">
                                        {{ $milestone->progress_percent ?? 0 }}%
                                    </span>
                                </div>
                            </x-admin.table-cell>
                            
                            <x-admin.table-cell>
                                <div class="flex items-center space-x-2">
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
                                                onclick="return confirm('Mark this milestone as completed?')"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </x-admin.icon-button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.projects.milestones.reopen', [$project, $milestone]) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <x-admin.icon-button
                                                type="submit"
                                                color="warning"
                                                size="sm"
                                                tooltip="Reopen milestone"
                                                onclick="return confirm('Reopen this milestone?')"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </x-admin.icon-button>
                                        </form>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.projects.milestones.destroy', [$project, $milestone]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-admin.icon-button
                                            type="submit"
                                            color="danger"
                                            size="sm"
                                            tooltip="Delete milestone"
                                            onclick="return confirm('Are you sure you want to delete this milestone?')"
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
                    title="No Milestones Found"
                    description="No milestones match your current filters or this project doesn't have any milestones yet."
                    actionText="Add First Milestone"
                    :actionUrl="route('admin.projects.milestones.create', $project)"
                    icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/></svg>'
                />
            @endif
        </x-admin.card>
    @endif

    <!-- Quick Actions FAB -->
    <div class="fixed bottom-4 right-4 z-50">
        <x-admin.floating-action-button :actions="[
            [
                'title' => 'Add Milestone', 
                'href' => route('admin.projects.milestones.create', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"/>',
                'color_classes' => 'bg-green-600 hover:bg-green-700'
            ],
            [
                'title' => 'Project Details', 
                'href' => route('admin.projects.show', $project),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"/>',
                'color_classes' => 'bg-blue-600 hover:bg-blue-700'
            ],
            [
                'title' => 'Timeline View', 
                'href' => route('admin.projects.milestones.index', array_merge([$project], request()->except('view'), ['view' => 'timeline'])),
                'icon' => '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z\"/>',
                'color_classes' => 'bg-purple-600 hover:bg-purple-700'
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
// Kanban Board Functionality
function kanbanBoard() {
    return {
        draggedItem: null,
        
        dragStart(event, milestoneId) {
            this.draggedItem = milestoneId;
            event.dataTransfer.effectAllowed = 'move';
        },
        
        drop(event, newStatus) {
            event.preventDefault();
            
            if (!this.draggedItem) return;
            
            // Update milestone status via AJAX
            fetch(`{{ route('admin.projects.milestones.index', $project) }}/${this.draggedItem}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to show updated status
                } else {
                    alert('Failed to update milestone status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the milestone');
            });
            
            this.draggedItem = null;
        }
    }
}

// Bulk Actions Functionality
function toggleBulkActions() {
    const bulkBar = document.getElementById('bulk-actions-bar');
    const checkboxes = document.querySelectorAll('.bulk-select-toggle');
    
    if (bulkBar.classList.contains('hidden')) {
        bulkBar.classList.remove('hidden');
        checkboxes.forEach(cb => cb.classList.remove('hidden'));
    } else {
        bulkBar.classList.add('hidden');
        checkboxes.forEach(cb => {
            cb.classList.add('hidden');
            cb.checked = false;
        });
    }
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCheckboxes = document.querySelectorAll('.milestone-checkbox:checked');
    const countElement = document.getElementById('selected-count');
    const selectAllCheckbox = document.getElementById('select-all');
    
    if (countElement) {
        countElement.textContent = `${selectedCheckboxes.length} selected`;
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.milestone-checkbox');
    if (selectAllCheckbox && allCheckboxes.length > 0) {
        selectAllCheckbox.checked = selectedCheckboxes.length === allCheckboxes.length;
        selectAllCheckbox.indeterminate = selectedCheckboxes.length > 0 && selectedCheckboxes.length < allCheckboxes.length;
    }
}

function executeBulkAction() {
    const action = document.getElementById('bulk-action-select').value;
    const selectedIds = Array.from(document.querySelectorAll('.milestone-checkbox:checked'))
        .map(cb => cb.value);
    
    if (!action || selectedIds.length === 0) {
        alert('Please select an action and at least one milestone.');
        return;
    }
    
    if (action === 'delete' && !confirm(`Are you sure you want to delete ${selectedIds.length} milestone(s)?`)) {
        return;
    }
    
    // Execute bulk action via AJAX
    fetch(`{{ route('admin.projects.milestones.bulk-update', $project) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            action: action,
            milestone_ids: selectedIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to execute bulk action: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while executing the bulk action');
    });
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const milestoneCheckboxes = document.querySelectorAll('.milestone-checkbox');
            milestoneCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Individual checkbox functionality
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('milestone-checkbox')) {
            updateSelectedCount();
        }
    });
    
    // Kanban drag and drop
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
    });
    
    document.addEventListener('dragenter', function(e) {
        e.preventDefault();
    });
    
    // Auto-refresh for real-time updates (optional)
    if (document.querySelector('[data-auto-refresh="true"]')) {
        setInterval(function() {
            // Check for updates without full page refresh
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                // Update specific sections if needed
                console.log('Auto-refresh check completed');
            })
            .catch(error => console.error('Auto-refresh error:', error));
        }, 30000); // Check every 30 seconds
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + N = New milestone
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            window.location.href = "{{ route('admin.projects.milestones.create', $project) }}";
        }
        
        // Ctrl/Cmd + A = Select all (when bulk actions are active)
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !document.getElementById('bulk-actions-bar').classList.contains('hidden')) {
            e.preventDefault();
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            }
        }
    });
});
</script>
@endpush