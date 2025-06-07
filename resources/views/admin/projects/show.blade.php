{{-- resources/views/admin/projects/show.blade.php - CLEAN VERSION --}}
<x-layouts.admin title="Project Management">
    <!-- Fixed Header with Project Title -->
    <div class="sticky top-0 z-40 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 -mx-6 px-6 py-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-3">
                    <!-- Project Status Indicator -->
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 rounded-full {{ 
                            $project->status === 'completed' ? 'bg-green-500' : 
                            ($project->status === 'in_progress' ? 'bg-blue-500 animate-pulse' : 
                            ($project->status === 'on_hold' ? 'bg-yellow-500' : 'bg-gray-500'))
                        }}"></div>
                    </div>
                    
                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                            {{ $project->title }}
                        </h1>
                        <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if($project->client)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $project->client->name }}
                                </span>
                            @elseif(!empty($project->client_name))
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $project->client_name }}
                                </span>
                            @endif
                            
                            @if($project->category)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ $project->category->name }}
                                </span>
                            @endif
                            
                            @if($project->end_date)
                                <span class="flex items-center {{ $project->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Due {{ $project->end_date->format('M j, Y') }}
                                    @if($project->isOverdue())
                                        <span class="ml-1 text-red-600">({{ abs($project->end_date->diffInDays(now())) }} days overdue)</span>
                                    @endif
                                </span>
                            @endif
                            <span class="text-gray-400">•</span>
                            <span>Created {{ $project->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Header Actions -->
            <div class="mt-4 lg:mt-0 flex items-center space-x-3">
                <!-- Progress Ring -->
                <div class="flex items-center space-x-3">
                    <div class="relative w-12 h-12">
                        <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"/>
                            <path class="text-blue-500" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" 
                                  stroke-dasharray="{{ ($project->progress_percentage ?? 0) }}, 100" 
                                  d="m18,2.0845 a 15.9155,15.9155 0 0,1 0,31.831 a 15.9155,15.9155 0 0,1 0,-31.831"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $project->progress_percentage ?? 0 }}%</span>
                        </div>
                    </div>
                    <div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $project->progress_percentage ?? 0 }}%</div>
                        <div class="text-gray-500 dark:text-gray-400">Complete</div>
                    </div>
                </div>
                
                <!-- Status Badge -->
                <x-admin.badge 
                    type="{{ $project->status_color }}"
                    size="lg"
                >
                    {{ $project->formatted_status }}
                </x-admin.badge>
                
                <!-- Quick Actions -->
                <div class="flex items-center space-x-2">
                    <x-admin.button 
                        href="{{ route('admin.projects.edit', $project) }}" 
                        color="light"
                        size="sm"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </x-admin.button>
                    
                    @if($project->slug)
                        <x-admin.button 
                            href="{{ route('portfolio.show', $project->slug) }}" 
                            color="info"
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
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => '#'
    ]" class="mb-6" />

    <!-- Critical Alerts -->
    @if($project->isOverdue())
        <x-admin.alert type="danger" class="mb-6">
            <x-slot name="title">Project Overdue</x-slot>
            <div class="flex items-center justify-between">
                <div>
                    This project is {{ abs($project->end_date->diffInDays(now())) }} days overdue. 
                    @if(isset($milestoneStats['overdue']) && $milestoneStats['overdue'] > 0)
                        {{ $milestoneStats['overdue'] }} milestones are also overdue.
                    @endif
                </div>
                <x-admin.button href="{{ route('admin.projects.edit', $project) }}" color="light" size="sm">
                    Update Timeline
                </x-admin.button>
            </div>
        </x-admin.alert>
    @elseif(isset($milestoneStats['overdue']) && $milestoneStats['overdue'] > 0)
        <x-admin.alert type="warning" class="mb-6">
            <x-slot name="title">{{ $milestoneStats['overdue'] }} Overdue Milestones</x-slot>
            Some milestones are past their due dates. Review and update milestone timelines.
        </x-admin.alert>
    @elseif(isset($milestoneStats['due_soon']) && $milestoneStats['due_soon'] > 0)
        <x-admin.alert type="info" class="mb-6">
            <x-slot name="title">{{ $milestoneStats['due_soon'] }} Milestones Due Soon</x-slot>
            You have milestones approaching their deadlines within the next 7 days.
        </x-admin.alert>
    @endif

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 mb-8">
        <!-- Left Column: Project Overview & Quick Stats -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <x-admin.card title="Project Statistics">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $milestoneStats['total'] ?? 0 }}</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400">Total Milestones</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $milestoneStats['completed'] ?? 0 }}</div>
                            <div class="text-xs text-green-600 dark:text-green-400">Completed</div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Completion Rate</span>
                            <span class="font-medium">{{ ($milestoneStats['total'] ?? 0) > 0 ? round((($milestoneStats['completed'] ?? 0) / ($milestoneStats['total'] ?? 1)) * 100) : 0 }}%</span>
                        </div>
                        <x-admin.progress 
                            :value="($milestoneStats['total'] ?? 0) > 0 ? round((($milestoneStats['completed'] ?? 0) / ($milestoneStats['total'] ?? 1)) * 100) : 0" 
                            height="sm"
                            color="green"
                        />
                    </div>
                    
                    @if(isset($milestoneStats['overdue']) && $milestoneStats['overdue'] > 0)
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="text-sm font-medium text-red-800 dark:text-red-400">{{ $milestoneStats['overdue'] }} Overdue</span>
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Project Details -->
            <x-admin.card title="Project Details">
                <dl class="space-y-3">
                    @if($project->client)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Client</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <a href="{{ route('admin.users.show', $project->client) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    {{ $project->client->name }}
                                </a>
                            </dd>
                        </div>
                    @elseif(!empty($project->client_name))
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Client</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $project->client_name }}</dd>
                        </div>
                    @endif
                    
                    @if($project->category)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Category</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $project->category->name }}</dd>
                        </div>
                    @endif
                    
                    @if($project->service)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Service</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $project->service->title }}</dd>
                        </div>
                    @endif
                    
                    @if($project->location)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Location</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $project->location }}</dd>
                        </div>
                    @endif
                    
                    @if($project->start_date)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Start Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $project->start_date->format('M j, Y') }}</dd>
                        </div>
                    @endif
                    
                    @if($project->end_date)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Deadline</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                {{ $project->end_date->format('M j, Y') }}
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                    ({{ $project->end_date->diffForHumans() }})
                                </span>
                            </dd>
                        </div>
                    @endif
                    
                    @if($project->budget)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Budget</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($project->budget, 0, ',', '.') }}</dd>
                        </div>
                    @endif
                    
                    @if($project->actual_cost)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Actual Cost</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($project->actual_cost, 0, ',', '.') }}</dd>
                        </div>
                    @endif
                    
                    @if($project->value)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Project Value</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $project->value }}</dd>
                        </div>
                    @endif
                    
                    @if($project->priority)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Priority</dt>
                            <dd class="text-sm">
                                <x-admin.badge type="{{ $project->priority_color }}" size="sm">
                                    {{ $project->formatted_priority }}
                                </x-admin.badge>
                            </dd>
                        </div>
                    @endif
                </dl>
            </x-admin.card>

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-2">
                    <x-admin.button 
                        href="{{ route('admin.projects.milestones.create', $project) }}" 
                        color="primary" 
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

        <!-- Right Column: Milestone Management Interface -->
        <div class="xl:col-span-3">
            <!-- Milestone Management Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Project Milestones</h2>
                    <x-admin.badge type="light">
                        {{ $milestoneStats['completed'] ?? 0 }}/{{ $milestoneStats['total'] ?? 0 }} completed
                    </x-admin.badge>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- View Toggle -->
                    <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                        <button class="px-3 py-1 text-sm bg-blue-500 text-white milestone-view-btn" data-view="timeline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/>
                            </svg>
                        </button>
                        <button class="px-3 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-l border-gray-300 dark:border-gray-600 milestone-view-btn" data-view="kanban">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                            </svg>
                        </button>
                        <button class="px-3 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-l border-gray-300 dark:border-gray-600 milestone-view-btn" data-view="list">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Quick Filters -->
                    <select class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" id="milestone-status-filter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="delayed">Delayed</option>
                    </select>
                    
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
            </div>

            <!-- Milestone Views Container -->
            <div id="milestone-views-container">
                <!-- Timeline View (Default) -->
                <div id="timeline-view" class="milestone-view">
                    @if($project->milestones->count() > 0)
                        <x-admin.card>
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($project->milestones->sortBy('due_date') as $milestone)
                                        <li class="milestone-item" data-status="{{ $milestone->status }}" data-priority="{{ $milestone->priority ?? 'normal' }}">
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex items-start space-x-3">
                                                    <!-- Milestone Status Icon -->
                                                    <div class="flex-shrink-0">
                                                        <span class="h-10 w-10 rounded-full {{ 
                                                            $milestone->status === 'completed' ? 'bg-green-500' : 
                                                            ($milestone->isOverdue() ? 'bg-red-500' : 
                                                            ($milestone->status === 'in_progress' ? 'bg-blue-500' : 'bg-gray-400'))
                                                        }} flex items-center justify-center ring-8 ring-white dark:ring-gray-900">
                                                            @if($milestone->status === 'completed')
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @else
                                                                <span class="text-white text-sm font-medium">{{ $loop->iteration }}</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Milestone Content -->
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-start justify-between">
                                                            <div class="flex-1">
                                                                <div class="flex items-center space-x-3 mb-2">
                                                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                                                        {{ $milestone->title }}
                                                                    </h4>
                                                                    <x-admin.badge type="{{ $milestone->status_color }}">
                                                                        {{ $milestone->formatted_status }}
                                                                    </x-admin.badge>
                                                                    @if(($milestone->priority ?? 'normal') !== 'normal')
                                                                        <x-admin.badge type="{{ $milestone->priority_color }}" size="sm">
                                                                            {{ ucfirst($milestone->priority ?? 'normal') }}
                                                                        </x-admin.badge>
                                                                    @endif
                                                                </div>
                                                                
                                                                @if($milestone->description)
                                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                                                        {{ $milestone->description }}
                                                                    </p>
                                                                @endif
                                                                
                                                                <!-- Progress Bar -->
                                                                @if(($milestone->progress_percent ?? 0) > 0)
                                                                    <div class="mb-3">
                                                                        <div class="flex items-center justify-between text-sm mb-1">
                                                                            <span class="text-gray-600 dark:text-gray-400">Progress</span>
                                                                            <span class="font-medium">{{ $milestone->progress_percent ?? 0 }}%</span>
                                                                        </div>
                                                                        <x-admin.progress 
                                                                            :value="$milestone->progress_percent ?? 0" 
                                                                            height="sm"
                                                                            color="{{ $milestone->status === 'completed' ? 'green' : 'blue' }}"
                                                                        />
                                                                    </div>
                                                                @endif
                                                                
                                                                <!-- Milestone Meta -->
                                                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                                    @if($milestone->due_date)
                                                                        <span class="flex items-center {{ $milestone->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                            </svg>
                                                                            Due {{ $milestone->due_date->format('M j, Y') }}
                                                                            @if($milestone->isOverdue())
                                                                                <span class="ml-1">({{ $milestone->days_overdue ?? 0 }} days overdue)</span>
                                                                            @elseif($milestone->isDueSoon())
                                                                                <span class="ml-1">(Due in {{ $milestone->days_until_due ?? 0 }} days)</span>
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                    
                                                                    @if($milestone->completion_date)
                                                                        <span class="flex items-center text-green-600">
                                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                            </svg>
                                                                            Completed {{ $milestone->completion_date->format('M j, Y') }}
                                                                        </span>
                                                                    @endif
                                                                    
                                                                    @if($milestone->estimated_hours ?? false)
                                                                        <span class="flex items-center">
                                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                            </svg>
                                                                            {{ $milestone->estimated_hours }}h estimated
                                                                            @if($milestone->actual_hours ?? false)
                                                                                / {{ $milestone->actual_hours }}h actual
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Quick Actions -->
                                                            <div class="flex items-center space-x-2 ml-4">
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
                                                                
                                                                <!-- Dropdown for more actions -->
                                                                <div class="relative" x-data="{ open: false }">
                                                                    <x-admin.icon-button
                                                                        type="button"
                                                                        color="light"
                                                                        size="sm"
                                                                        @click="open = !open"
                                                                    >
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                                        </svg>
                                                                    </x-admin.icon-button>
                                                                    
                                                                    <div x-show="open" @click.away="open = false" 
                                                                         x-transition:enter="transition ease-out duration-100"
                                                                         x-transition:enter-start="transform opacity-0 scale-95"
                                                                         x-transition:enter-end="transform opacity-100 scale-100"
                                                                         x-transition:leave="transition ease-in duration-75"
                                                                         x-transition:leave-start="transform opacity-100 scale-100"
                                                                         x-transition:leave-end="transform opacity-0 scale-95"
                                                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                                                                        <div class="py-1">
                                                                            @if($milestone->status === 'completed')
                                                                                <form method="POST" action="{{ route('admin.projects.milestones.reopen', [$project, $milestone]) }}" class="block">
                                                                                    @csrf
                                                                                    @method('PATCH')
                                                                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                                        Reopen Milestone
                                                                                    </button>
                                                                                </form>
                                                                            @endif
                                                                            <a href="{{ route('admin.projects.milestones.edit', [$project, $milestone]) }}" 
                                                                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                                Edit Details
                                                                            </a>
                                                                            <form method="POST" action="{{ route('admin.projects.milestones.destroy', [$project, $milestone]) }}" class="block">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" 
                                                                                        onclick="return confirm('Are you sure you want to delete this milestone?')"
                                                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                                                    Delete Milestone
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </x-admin.card>
                    @else
                        <x-admin.empty-state
                            title="No Milestones Yet"
                            description="Create milestones to track project progress and important deadlines."
                            actionText="Add First Milestone"
                            :actionUrl="route('admin.projects.milestones.create', $project)"
                            icon='<svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/></svg>'
                        />
                    @endif
                </div>

                <!-- Kanban View -->
                <div id="kanban-view" class="milestone-view hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4" x-data="kanbanBoard()">
                        @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'delayed' => 'Delayed'] as $status => $statusLabel)
                            <x-admin.card>
                                <x-slot name="header">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $statusLabel }}</h3>
                                        <x-admin.badge type="{{ 
                                            $status === 'completed' ? 'success' : 
                                            ($status === 'delayed' ? 'danger' : 
                                            ($status === 'in_progress' ? 'warning' : 'light'))
                                        }}" size="sm">
                                            {{ $project->milestones->where('status', $status)->count() }}
                                        </x-admin.badge>
                                    </div>
                                </x-slot>
                                
                                <div class="space-y-3 min-h-96" 
                                     data-status="{{ $status }}"
                                     @drop="drop($event, '{{ $status }}')"
                                     @dragover.prevent
                                     @dragenter.prevent>
                                    @foreach($project->milestones->where('status', $status) as $milestone)
                                        <div class="milestone-card p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-transparent hover:border-blue-300 cursor-move transition-colors" 
                                             draggable="true" 
                                             data-milestone-id="{{ $milestone->id }}"
                                             @dragstart="dragStart($event, {{ $milestone->id }})">
                                            <div class="flex items-start justify-between mb-2">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                                                    {{ $milestone->title }}
                                                </h4>
                                                <div class="flex items-center space-x-1 ml-2">
                                                    @if(($milestone->priority ?? 'normal') !== 'normal')
                                                        <div class="w-2 h-2 rounded-full {{ 
                                                            ($milestone->priority ?? 'normal') === 'critical' ? 'bg-red-500' : 
                                                            (($milestone->priority ?? 'normal') === 'high' ? 'bg-orange-500' : 'bg-yellow-500')
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
                                            
                                            @if($milestone->description)
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                                    {{ $milestone->description }}
                                                </p>
                                            @endif
                                            
                                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                @if($milestone->due_date)
                                                    <span class="{{ $milestone->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                                                        {{ $milestone->due_date->format('M j') }}
                                                    </span>
                                                @else
                                                    <span>No due date</span>
                                                @endif
                                                
                                                @if(($milestone->progress_percent ?? 0) > 0)
                                                    <span class="font-medium">{{ $milestone->progress_percent ?? 0 }}%</span>
                                                @endif
                                            </div>
                                            
                                            @if(($milestone->progress_percent ?? 0) > 0)
                                                <x-admin.progress 
                                                    :value="$milestone->progress_percent ?? 0" 
                                                    height="xs"
                                                    showLabel="false"
                                                    color="{{ $milestone->status === 'completed' ? 'green' : 'blue' }}"
                                                />
                                            @endif
                                        </div>
                                    @endforeach
                                    
                                    @if($project->milestones->where('status', $status)->isEmpty())
                                        <div class="flex items-center justify-center h-32 text-gray-400 dark:text-gray-500 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                                            <span class="text-sm">Drop milestones here</span>
                                        </div>
                                    @endif
                                </div>
                            </x-admin.card>
                        @endforeach
                    </div>
                </div>

                <!-- List View -->
                <div id="list-view" class="milestone-view hidden">
                    @if($project->milestones->count() > 0)
                        <x-admin.card>
                            <x-admin.data-table>
                                <x-slot name="columns">
                                    <x-admin.table-column width="w-8">#</x-admin.table-column>
                                    <x-admin.table-column>Milestone</x-admin.table-column>
                                    <x-admin.table-column>Status</x-admin.table-column>
                                    <x-admin.table-column>Progress</x-admin.table-column>
                                    <x-admin.table-column>Due Date</x-admin.table-column>
                                    <x-admin.table-column>Actions</x-admin.table-column>
                                </x-slot>
                                
                                @foreach($project->milestones->sortBy('sort_order') as $milestone)
                                    <x-admin.table-row class="milestone-item" data-status="{{ $milestone->status }}" data-priority="{{ $milestone->priority ?? 'normal' }}">
                                        <x-admin.table-cell>
                                            <div class="flex items-center justify-center w-6 h-6 rounded-full {{ 
                                                $milestone->status === 'completed' ? 'bg-green-100 text-green-600' : 
                                                ($milestone->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600')
                                            }}">
                                                @if($milestone->status === 'completed')
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    <span class="text-xs font-medium">{{ $loop->iteration }}</span>
                                                @endif
                                            </div>
                                        </x-admin.table-cell>
                                        
                                        <x-admin.table-cell highlight>
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $milestone->title }}</span>
                                                    @if(($milestone->priority ?? 'normal') !== 'normal')
                                                        <x-admin.badge type="{{ $milestone->priority_color }}" size="sm">
                                                            {{ ucfirst($milestone->priority ?? 'normal') }}
                                                        </x-admin.badge>
                                                    @endif
                                                </div>
                                                @if($milestone->description)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($milestone->description, 80) }}</p>
                                                @endif
                                            </div>
                                        </x-admin.table-cell>
                                        
                                        <x-admin.table-cell>
                                            <x-admin.badge type="{{ $milestone->status_color }}">
                                                {{ $milestone->formatted_status }}
                                            </x-admin.badge>
                                            @if($milestone->isOverdue() && $milestone->status !== 'completed')
                                                <div class="mt-1">
                                                    <x-admin.badge type="danger" size="sm">
                                                        {{ $milestone->days_overdue ?? 0 }}d overdue
                                                    </x-admin.badge>
                                                </div>
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
                                            @if($milestone->due_date)
                                                <div class="text-sm {{ $milestone->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-900 dark:text-white' }}">
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
                                            <div class="flex items-center space-x-1">
                                                @if($milestone->status !== 'completed')
                                                    <form method="POST" action="{{ route('admin.projects.milestones.complete', [$project, $milestone]) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-admin.icon-button
                                                            type="submit"
                                                            color="success"
                                                            size="sm"
                                                            tooltip="Complete"
                                                            onclick="return confirm('Mark as completed?')"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </x-admin.icon-button>
                                                    </form>
                                                @endif
                                                
                                                <x-admin.icon-button
                                                    href="{{ route('admin.projects.milestones.edit', [$project, $milestone]) }}"
                                                    color="primary"
                                                    size="sm"
                                                    tooltip="Edit"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </x-admin.icon-button>
                                            </div>
                                        </x-admin.table-cell>
                                    </x-admin.table-row>
                                @endforeach
                            </x-admin.data-table>
                        </x-admin.card>
                    @else
                        <x-admin.empty-state
                            title="No Milestones Yet"
                            description="Create milestones to track project progress and important deadlines."
                            actionText="Add First Milestone"
                            :actionUrl="route('admin.projects.milestones.create', $project)"
                            icon='<svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/></svg>'
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Content Tabs (Files, Images, etc.) -->
    <div class="mt-8">
        <x-admin.tabs 
            :tabs="[
                'files' => 'Files (' . $project->files->count() . ')',
                'images' => 'Images (' . $project->images->count() . ')',
                'timeline' => 'Activity Timeline',
                'settings' => 'Project Settings'
            ]"
            activeTab="files"
        >
            <!-- Files Tab -->
            <x-admin.tab-panel id="files">
                @if($project->files->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($project->files as $file)
                            <x-admin.card>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $file->file_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $file->formatted_file_size }} • {{ $file->created_at->format('M j, Y') }}
                                        </p>
                                        <div class="mt-2">
                                            <x-admin.button 
                                                href="{{ route('admin.projects.files.download', [$project, $file]) }}" 
                                                color="light" 
                                                size="sm"
                                            >
                                                Download
                                            </x-admin.button>
                                        </div>
                                    </div>
                                </div>
                            </x-admin.card>
                        @endforeach
                    </div>
                @else
                    <x-admin.empty-state
                        title="No Files Uploaded"
                        description="Upload project files, documents, and resources."
                        actionText="Upload Files"
                        :actionUrl="route('admin.projects.files.create', $project)"
                    />
                @endif
            </x-admin.tab-panel>
            
            <!-- Images Tab -->
            <x-admin.tab-panel id="images">
                @if($project->images->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($project->images as $image)
                            <div class="relative group">
                                <img src="{{ Storage::url($image->image_path) }}" 
                                     alt="{{ $image->alt_text }}" 
                                     class="w-full h-48 object-cover rounded-lg">
                                @if($image->is_featured)
                                    <div class="absolute top-2 right-2">
                                        <x-admin.badge type="warning" size="sm">
                                            Featured
                                        </x-admin.badge>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <x-admin.button color="light" size="sm">
                                        View Full Size
                                    </x-admin.button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-admin.empty-state
                        title="No Images"
                        description="Add project images to showcase your work."
                        actionText="Upload Images"
                        :actionUrl="route('admin.projects.edit', $project) . '#images-section'"
                    />
                @endif
            </x-admin.tab-panel>
            
            <!-- Timeline Tab -->
            <x-admin.tab-panel id="timeline">
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        <!-- Project Created -->
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-900">
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Project created</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->title }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ $project->created_at->format('M j, Y g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        <!-- Milestone Events -->
                        @foreach($project->milestones->sortBy('created_at') as $milestone)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $milestone->status === 'completed' ? 'bg-green-500' : 'bg-blue-500' }} flex items-center justify-center ring-8 ring-white dark:ring-gray-900">
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
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $milestone->status === 'completed' ? 'Milestone completed' : 'Milestone created' }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $milestone->title }}</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {{ ($milestone->status === 'completed' && $milestone->completion_date) ? $milestone->completion_date->format('M j, Y g:i A') : $milestone->created_at->format('M j, Y g:i A') }}
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Quick Settings -->
                    <x-admin.card title="Quick Settings">
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
                                
                                @if($project->priority ?? false)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                                        <select name="priority" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                            <option value="low" {{ ($project->priority ?? 'normal') === 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="normal" {{ ($project->priority ?? 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="high" {{ ($project->priority ?? 'normal') === 'high' ? 'selected' : '' }}>High</option>
                                            <option value="urgent" {{ ($project->priority ?? 'normal') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                    </div>
                                @endif
                                
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
                                
                                @if($project->is_active ?? false)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ ($project->is_active ?? true) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Project</label>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-6">
                                <x-admin.button type="submit" color="primary" size="sm">
                                    Update Settings
                                </x-admin.button>
                            </div>
                        </form>
                    </x-admin.card>
                    
                    <!-- Project Actions -->
                    <x-admin.card title="Project Actions">
                        <div class="space-y-4">
                            <x-admin.button 
                                href="{{ route('admin.projects.edit', $project) }}" 
                                color="primary" 
                                size="sm"
                                class="w-full"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Full Details
                            </x-admin.button>
                            
                            @if($project->quotation ?? false)
                                <x-admin.button 
                                    href="{{ route('admin.quotations.show', $project->quotation) }}" 
                                    color="light" 
                                    size="sm"
                                    class="w-full"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View Original Quotation
                                </x-admin.button>
                            @endif
                            
                            @if($project->slug ?? false)
                                <x-admin.button 
                                    href="{{ route('portfolio.show', $project->slug) }}" 
                                    color="info" 
                                    size="sm"
                                    class="w-full"
                                    target="_blank"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    View Public Page
                                </x-admin.button>
                            @endif
                            
                            <!-- Danger Zone -->
                            <div class="mt-8 p-4 bg-red-50 border border-red-200 rounded-md dark:bg-red-900/20 dark:border-red-800">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Danger Zone</h4>
                                <p class="text-sm text-red-700 dark:text-red-300 mb-4">
                                    Permanently delete this project and all associated data.
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
                        </div>
                    </x-admin.card>
                </div>
            </x-admin.tab-panel>
        </x-admin.tabs>
    </div>

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
                    <li>{{ $project->messages->count() ?? 0 }} related messages</li>
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

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <div class="flex flex-col space-y-3" x-data="{ open: false }">
            <!-- Sub Actions (when expanded) -->
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="flex flex-col space-y-2">
                <x-admin.button 
                    href="{{ route('admin.projects.milestones.create', $project) }}" 
                    color="success"
                    size="sm"
                    class="shadow-lg"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Milestone
                </x-admin.button>
                
                <x-admin.button 
                    href="{{ route('admin.projects.files.create', $project) }}" 
                    color="info"
                    size="sm"
                    class="shadow-lg"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Files
                </x-admin.button>
                
                <x-admin.button 
                    href="{{ route('admin.projects.edit', $project) }}" 
                    color="warning"
                    size="sm"
                    class="shadow-lg"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Project
                </x-admin.button>
            </div>
            
            <!-- Main FAB -->
            <button @click="open = !open" 
                    class="w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 transform hover:scale-105">
                <svg class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-45': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </button>
        </div>
    </div>
</x-layouts.admin>

@push('scripts')
<script>
// Milestone View Switching
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.milestone-view-btn');
    const views = document.querySelectorAll('.milestone-view');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetView = this.dataset.view;
            
            // Update button states
            viewButtons.forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });
            this.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            this.classList.add('bg-blue-500', 'text-white');
            
            // Show/hide views
            views.forEach(view => {
                if (view.id === targetView + '-view') {
                    view.classList.remove('hidden');
                } else {
                    view.classList.add('hidden');
                }
            });
        });
    });
    
    // Milestone Filtering
    const statusFilter = document.getElementById('milestone-status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value;
            const milestoneItems = document.querySelectorAll('.milestone-item');
            
            milestoneItems.forEach(item => {
                if (!selectedStatus || item.dataset.status === selectedStatus) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});

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

// Delete confirmation
function confirmDelete() {
    document.getElementById('delete-project-modal').classList.remove('hidden');
}

// Auto-save for quick settings
let autoSaveTimeout;
document.querySelectorAll('#settings select, #settings input').forEach(input => {
    input.addEventListener('change', function() {
        clearTimeout(autoSaveTimeout);
        showSaveIndicator('saving');
        
        autoSaveTimeout = setTimeout(() => {
            const form = this.closest('form');
            const formData = new FormData(form);
            
            fetch(form.action, {
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
                    // Update header status badge
                    updateProjectHeader(data.project);
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

function showSaveIndicator(status) {
    // Remove existing indicators
    const existing = document.querySelector('.save-indicator');
    if (existing) existing.remove();
    
    const indicator = document.createElement('div');
    indicator.className = 'save-indicator fixed top-4 right-4 px-3 py-2 rounded-md text-sm z-50';
    
    if (status === 'saving') {
        indicator.className += ' bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        indicator.innerHTML = '<div class="flex items-center"><svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Auto-saving...</div>';
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

function updateProjectHeader(project) {
    // Update progress ring
    const progressPath = document.querySelector('.text-blue-500');
    if (progressPath) {
        progressPath.setAttribute('stroke-dasharray', `${project.progress_percentage}, 100`);
    }
    
    // Update progress text
    const progressTexts = document.querySelectorAll('.progress-text');
    progressTexts.forEach(text => {
        text.textContent = `${project.progress_percentage}%`;
    });
    
    // Update status badge
    const statusBadge = document.querySelector('.status-badge');
    if (statusBadge) {
        statusBadge.textContent = project.formatted_status;
        statusBadge.className = `status-badge ${project.status_color}`;
    }
}
</script>
@endpush