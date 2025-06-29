

<x-layouts.admin title="Project Management">
    <!-- Fixed Header with Project Title -->
    <div class="sticky top-12 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 -mx-6 px-6 py-4 mb-6">
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
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                    $project->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                    ($project->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 
                    ($project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))
                }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
                
                <!-- Quick Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.projects.edit', $project) }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    
                    @if($project->slug)
                        <a href="{{ route('portfolio.show', $project->slug) }}" 
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            View Live
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Projects
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ $project->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Critical Alerts -->
    @if($project->isOverdue())
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-400">Project Overdue</h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <div class="flex items-center justify-between">
                            <div>
                                This project is {{ abs($project->end_date->diffInDays(now())) }} days overdue.
                            </div>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="bg-red-100 hover:bg-red-200 text-red-800 text-xs px-2 py-1 rounded dark:bg-red-900/30 dark:hover:bg-red-900/40 dark:text-red-400">
                                Update Timeline
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($milestoneStats['overdue']) && $milestoneStats['overdue'] > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6 dark:bg-yellow-900/20 dark:border-yellow-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">{{ $milestoneStats['overdue'] }} Overdue Milestones</h3>
                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">Some milestones are past their due dates. Review and update milestone timelines.</p>
                </div>
            </div>
        </div>
    @elseif(isset($milestoneStats['due_soon']) && $milestoneStats['due_soon'] > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6 dark:bg-blue-900/20 dark:border-blue-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-400">{{ $milestoneStats['due_soon'] }} Milestones Due Soon</h3>
                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">You have milestones approaching their deadlines within the next 7 days.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 mb-8">
        <!-- Left Column: Project Overview & Quick Stats -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Project Statistics</h3>
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
                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($milestoneStats['total'] ?? 0) > 0 ? round((($milestoneStats['completed'] ?? 0) / ($milestoneStats['total'] ?? 1)) * 100) : 0 }}%"></div>
                            </div>
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
                </div>
            </div>

            <!-- Project Details -->
            <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Project Details</h3>
                    <dl class="space-y-3">
                        @if($project->client)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Client</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
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
                        
                        @if($project->priority)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Priority</dt>
                                <dd class="text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $project->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                        ($project->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400' : 
                                        ($project->priority === 'normal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))
                                    }}">
                                        {{ ucfirst($project->priority) }}
                                    </span>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.projects.milestones.create', $project) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Milestone
                        </a>
                        
                        <a href="{{ route('admin.projects.files.create', $project) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Upload Files
                        </a>
                        
                        <a href="{{ route('admin.messages.create', ['project_id' => $project->id]) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Message
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Milestone Timeline -->
        <div class="xl:col-span-3">
            <!-- Milestone Management Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Project Milestones</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                        {{ $milestoneStats['completed'] ?? 0 }}/{{ $milestoneStats['total'] ?? 0 }} completed
                    </span>
                </div>
                
                <div class="mt-3 sm:mt-0 flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                    <!-- Quick Filters -->
                    <select class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                            id="milestone-status-filter" 
                            onchange="filterMilestones()">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="delayed">Delayed</option>
                    </select>
                    
                    <select class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" 
                            id="milestone-priority-filter" 
                            onchange="filterMilestones()">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    
                    <a href="{{ route('admin.projects.milestones.create', $project) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Milestone
                    </a>
                </div>
            </div>
        
            @php
                $projectMilestones = collect();
                try {
                    $projectMilestones = $project->milestones()->orderBy('due_date')->orderBy('sort_order')->get();
                } catch (\Exception $e) {
                    \Log::error('Error loading project milestones', ['project_id' => $project->id, 'error' => $e->getMessage()]);
                }
            @endphp
        
            <!-- Milestone Timeline -->
            <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                @if($projectMilestones->count() > 0)
                    <div class="px-6 py-6">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8" id="milestones-timeline">
                                @foreach($projectMilestones as $milestone)
                                    @php
                                        $milestoneTitle = \App\Helpers\BladeHelpers::safeAttribute($milestone, 'title', 'Untitled Milestone');
                                        $milestoneStatus = $milestone->status ?? 'pending';
                                        $milestonePriority = $milestone->priority ?? 'normal';
                                        $milestoneDescription = \App\Helpers\BladeHelpers::safeAttribute($milestone, 'description', '');
                                        $milestoneProgress = $milestone->progress_percent ?? 0;
                                        
                                        $statusColor = match($milestoneStatus) {
                                            'completed' => 'bg-green-500',
                                            'in_progress' => 'bg-blue-500',
                                            'delayed' => 'bg-red-500',
                                            default => 'bg-gray-400'
                                        };
                                        
                                        $priorityColor = match($milestonePriority) {
                                            'critical' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                            'normal' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                        };
                                        
                                        $isOverdue = $milestone->due_date && $milestone->due_date < now() && $milestoneStatus !== 'completed';
                                        $isDueSoon = $milestone->due_date && $milestone->due_date >= now() && $milestone->due_date <= now()->addDays(7) && $milestoneStatus !== 'completed';
                                    @endphp
                                    
                                    <li class="milestone-item" 
                                        data-status="{{ $milestoneStatus }}" 
                                        data-priority="{{ $milestonePriority }}"
                                        data-milestone-id="{{ $milestone->id }}">
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex items-start space-x-3">
                                                <!-- Milestone Status Icon -->
                                                <div class="flex-shrink-0">
                                                    <span class="h-10 w-10 rounded-full {{ $statusColor }} flex items-center justify-center ring-8 ring-white dark:ring-gray-900">
                                                        @if($milestoneStatus === 'completed')
                                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @elseif($isOverdue)
                                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
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
                                                                    {{ $milestoneTitle }}
                                                                </h4>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                                    $milestoneStatus === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                                                    ($milestoneStatus === 'delayed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                                                    ($milestoneStatus === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))
                                                                }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $milestoneStatus)) }}
                                                                </span>
                                                                @if($milestonePriority !== 'normal')
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $priorityColor }}">
                                                                        {{ ucfirst($milestonePriority) }}
                                                                    </span>
                                                                @endif
                                                                @if($isOverdue)
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                                        Overdue
                                                                    </span>
                                                                @elseif($isDueSoon)
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                                        Due Soon
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            
                                                            @if($milestoneDescription)
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                                                    {{ $milestoneDescription }}
                                                                </p>
                                                            @endif
                                                            
                                                            <!-- Progress Bar -->
                                                            @if($milestoneProgress > 0)
                                                                <div class="mb-3">
                                                                    <div class="flex items-center justify-between text-sm mb-1">
                                                                        <span class="text-gray-600 dark:text-gray-400">Progress</span>
                                                                        <span class="font-medium">{{ $milestoneProgress }}%</span>
                                                                    </div>
                                                                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $milestoneProgress }}%"></div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            
                                                            <!-- Milestone Meta -->
                                                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                                @if($milestone->due_date)
                                                                    <span class="flex items-center {{ $isOverdue ? 'text-red-600 font-medium' : '' }}">
                                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                        </svg>
                                                                        Due {{ $milestone->due_date->format('M j, Y') }}
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
                                                            @if($milestoneStatus !== 'completed')
                                                                <button type="button" 
                                                                        onclick="updateMilestoneStatus({{ $milestone->id }}, 'completed')"
                                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                                        title="Mark as completed">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <button type="button" 
                                                                        onclick="updateMilestoneStatus({{ $milestone->id }}, 'in_progress')"
                                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                                        title="Reopen milestone">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                            
                                                            <a href="{{ route('admin.projects.milestones.edit', [$project, $milestone]) }}"
                                                               class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                               title="Edit milestone">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                            </a>
                                                            
                                                            <!-- Dropdown for more actions -->
                                                            <div class="relative" x-data="{ open: false }">
                                                                <button type="button"
                                                                        @click="open = !open"
                                                                        class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                                    </svg>
                                                                </button>
                                                                
                                                                <div x-show="open" @click.away="open = false" 
                                                                     x-transition:enter="transition ease-out duration-100"
                                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                                     x-transition:leave="transition ease-in duration-75"
                                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                                                                    <div class="py-1">
                                                                        <button type="button" 
                                                                                onclick="updateMilestoneStatus({{ $milestone->id }}, 'pending')"
                                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                            Set as Pending
                                                                        </button>
                                                                        <button type="button" 
                                                                                onclick="updateMilestoneStatus({{ $milestone->id }}, 'in_progress')"
                                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                            Set as In Progress
                                                                        </button>
                                                                        <button type="button" 
                                                                                onclick="updateMilestoneStatus({{ $milestone->id }}, 'delayed')"
                                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                            Mark as Delayed
                                                                        </button>
                                                                        <div class="border-t border-gray-100 dark:border-gray-600"></div>
                                                                        <button type="button" 
                                                                                onclick="deleteMilestone({{ $milestone->id }})"
                                                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                                            Delete Milestone
                                                                        </button>
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
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Milestones Yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create milestones to track project progress and important deadlines.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.projects.milestones.create', $project) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add First Milestone
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Secondary Content Tabs (Files, Images, etc.) -->
    <div class="mt-8">
        <div x-data="{ activeTab: 'files' }">
            <!-- Tab Headers -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'files'" 
                            :class="{ 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500': activeTab === 'files', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'files' }"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Files
                        @php
                            $filesCount = 0;
                            try { $filesCount = $project->files()->count(); } catch (\Exception $e) {}
                        @endphp
                        ({{ $filesCount }})
                    </button>
                    <button @click="activeTab = 'images'" 
                            :class="{ 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500': activeTab === 'images', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'images' }"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Images
                        @php
                            $imagesCount = 0;
                            try { $imagesCount = $project->images()->count(); } catch (\Exception $e) {}
                        @endphp
                        ({{ $imagesCount }})
                    </button>
                    <button @click="activeTab = 'timeline'" 
                            :class="{ 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500': activeTab === 'timeline', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'timeline' }"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Activity Timeline
                    </button>
                    <button @click="activeTab = 'settings'" 
                            :class="{ 'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500': activeTab === 'settings', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'settings' }"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Project Settings
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-6">
                <!-- Files Tab -->
                <div x-show="activeTab === 'files'">
                    @php
                        $projectFiles = collect();
                        try {
                            $projectFiles = $project->files()->latest()->take(10)->get();
                        } catch (\Exception $e) {
                            \Log::error('Error loading project files', ['project_id' => $project->id, 'error' => $e->getMessage()]);
                        }
                    @endphp
                    
                    @if($projectFiles->count() > 0)
                        <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Files</h3>
                                    <a href="{{ route('admin.projects.files.index', $project) }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">View all files</a>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($projectFiles as $file)
                                    @php
                                        $fileName = 'Unknown File';
                                        $fileSize = 'Unknown size';
                                        $createdDate = 'Unknown date';
                                        
                                        try {
                                            $fileName = is_string($file->file_name) ? trim($file->file_name) : 'Unknown File';
                                            if (empty($fileName)) $fileName = 'Unknown File';
                                            
                                            $fileSize = $file->formatted_file_size ?? 'Unknown size';
                                            if (is_array($fileSize)) {
                                                \Log::warning('File size is array', ['file_id' => $file->id]);
                                                $fileSize = 'Unknown size';
                                            }
                                            
                                            if ($file->created_at) {
                                                $createdDate = $file->created_at->format('M j, Y');
                                            }
                                        } catch (\Exception $e) {
                                            \Log::error('Error processing file data', [
                                                'file_id' => $file->id ?? 'unknown',
                                                'error' => $e->getMessage()
                                            ]);
                                        }
                                    @endphp
                                    
                                    <div class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $fileName }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $fileSize }} • {{ $createdDate }}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <a href="{{ route('admin.projects.files.download', [$project, $file]) }}" 
                                                   class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6l-1-8H10l-1 8zM12 7V3m0 4l3-3m-6 0l3 3"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Files Uploaded</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload project files, documents, and resources.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.projects.files.create', $project) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Upload Files
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Images Tab -->
                <div x-show="activeTab === 'images'">
                    @php
                        $projectImages = collect();
                        try {
                            $projectImages = $project->images()->orderBy('sort_order')->get();
                        } catch (\Exception $e) {
                            \Log::error('Error loading project images', ['project_id' => $project->id, 'error' => $e->getMessage()]);
                        }
                    @endphp
                    
                    @if($projectImages->count() > 0)
                        <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Images</h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach($projectImages as $image)
                                        @php
                                            $imagePath = '';
                                            $altText = 'Project image';
                                            $isFeatured = false;
                                            
                                            try {
                                                $imagePath = $image->image_path ?? '';
                                                $altText = is_string($image->alt_text) ? trim($image->alt_text) : 'Project image';
                                                if (empty($altText)) $altText = 'Project image';
                                                if (is_array($altText)) {
                                                    \Log::warning('Alt text is array', ['image_id' => $image->id]);
                                                    $altText = 'Project image';
                                                }
                                                $isFeatured = (bool) ($image->is_featured ?? false);
                                            } catch (\Exception $e) {
                                                \Log::error('Error processing image data', [
                                                    'image_id' => $image->id ?? 'unknown',
                                                    'error' => $e->getMessage()
                                                ]);
                                            }
                                        @endphp
                                        
                                        <div class="relative group">
                                            @if($imagePath && Storage::disk('public')->exists($imagePath))
                                                <img src="{{ Storage::url($imagePath) }}" 
                                                    alt="{{ $altText }}" 
                                                    class="w-full h-48 object-cover rounded-lg">
                                            @else
                                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            @if($isFeatured)
                                                <div class="absolute top-2 right-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                        Featured
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Images</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add project images to showcase your work.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.projects.edit', $project) }}#images-section" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Upload Images
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Timeline Tab -->
                <div x-show="activeTab === 'timeline'">
                    <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Activity Timeline</h3>
                        </div>
                        <div class="p-6">
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
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ is_string($project->title) ? $project->title : 'Untitled Project' }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        {{ $project->created_at->format('M j, Y g:i A') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    <!-- Milestone Events -->
                                    @php
                                        $timelineMilestones = collect();
                                        try {
                                            $timelineMilestones = $project->milestones()->orderBy('created_at')->get();
                                        } catch (\Exception $e) {
                                            \Log::error('Error loading timeline milestones', ['project_id' => $project->id, 'error' => $e->getMessage()]);
                                        }
                                    @endphp
                                    
                                    @foreach($timelineMilestones as $milestone)
                                        @php
                                            $milestoneTitle = 'Untitled Milestone';
                                            $milestoneStatus = 'pending';
                                            $milestoneCreated = null;
                                            $milestoneCompleted = null;
                                            
                                            try {
                                                $milestoneTitle = is_string($milestone->title) ? trim($milestone->title) : 'Untitled Milestone';
                                                if (empty($milestoneTitle)) $milestoneTitle = 'Untitled Milestone';
                                                
                                                $milestoneStatus = is_string($milestone->status) ? $milestone->status : 'pending';
                                                $milestoneCreated = $milestone->created_at;
                                                $milestoneCompleted = $milestone->completion_date ?? $milestone->completed_date;
                                            } catch (\Exception $e) {
                                                \Log::error('Error processing timeline milestone data', [
                                                    'milestone_id' => $milestone->id ?? 'unknown',
                                                    'error' => $e->getMessage()
                                                ]);
                                            }
                                        @endphp
                                        
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full {{ $milestoneStatus === 'completed' ? 'bg-green-500' : 'bg-blue-500' }} flex items-center justify-center ring-8 ring-white dark:ring-gray-900">
                                                            @if($milestoneStatus === 'completed')
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
                                                                {{ $milestoneStatus === 'completed' ? 'Milestone completed' : 'Milestone created' }}
                                                            </p>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $milestoneTitle }}</p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            @if($milestoneStatus === 'completed' && $milestoneCompleted)
                                                                {{ $milestoneCompleted->format('M j, Y g:i A') }}
                                                            @elseif($milestoneCreated)
                                                                {{ $milestoneCreated->format('M j, Y g:i A') }}
                                                            @else
                                                                Unknown date
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Settings Tab -->
                <div x-show="activeTab === 'settings'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Quick Settings -->
                        <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Settings</h3>
                            </div>
                            <div class="p-6">
                                <form method="POST" action="{{ route('admin.projects.quick-update', $project) }}">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                            <select name="status" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <option value="planning" {{ $project->status === 'planning' ? 'selected' : '' }}>Planning</option>
                                                <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $project->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                        
                                        @if(isset($project->priority) && !is_null($project->priority))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                                                <select name="priority" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" name="featured" value="1" {{ $project->featured ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Project</label>
                                        </div>
                                        
                                        @if(isset($project->is_active))
                                            <div class="flex items-center">
                                                <input type="checkbox" name="is_active" value="1" {{ ($project->is_active ?? true) ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                                                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Project</label>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-6">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Update Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Project Actions -->
                        <div class="bg-white shadow rounded-lg dark:bg-neutral-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Actions</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <a href="{{ route('admin.projects.edit', $project) }}" 
                                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Full Details
                                    </a>
                                    
                                    @if(isset($project->quotation) && $project->quotation)
                                        <a href="{{ route('admin.quotations.show', $project->quotation) }}" 
                                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            View Original Quotation
                                        </a>
                                    @endif
                                    
                                    @if(isset($project->slug) && !empty($project->slug))
                                        <a href="{{ route('portfolio.show', $project->slug) }}" 
                                           target="_blank"
                                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            View Public Page
                                        </a>
                                    @endif
                                    
                                    <!-- Danger Zone -->
                                    <div class="mt-8 p-4 bg-red-50 border border-red-200 rounded-md dark:bg-red-900/20 dark:border-red-800">
                                        <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Danger Zone</h4>
                                        <p class="text-sm text-red-700 dark:text-red-300 mb-4">
                                            Permanently delete this project and all associated data.
                                        </p>
                                        <button type="button" 
                                                onclick="confirmDelete()"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Delete Project
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-project-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" onclick="closeDeleteModal(event)">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800" onclick="event.stopPropagation()">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="mt-2 px-7 py-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center">Delete Project</h3>
                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        <div class="mb-4">
                            <p class="font-medium text-red-600 dark:text-red-400 mb-2">
                                Are you sure you want to delete "{{ $project->title }}"?
                            </p>
                            <p class="mb-4">This action cannot be undone and will permanently delete:</p>
                        </div>
                        
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                            <ul class="list-disc list-inside space-y-1">
                                @php
                                    $imagesCount = 0;
                                    $filesCount = 0;
                                    $milestonesCount = 0;
                                    $messagesCount = 0;
                                    
                                    try {
                                        $imagesCount = $project->images->count();
                                        $filesCount = $project->files->count();
                                        $milestonesCount = $project->milestones->count();
                                        $messagesCount = $project->messages->count() ?? 0;
                                    } catch (\Exception $e) {
                                        \Log::error('Error counting project relations for delete modal', ['error' => $e->getMessage()]);
                                    }
                                @endphp
                                <li>{{ $imagesCount }} project images</li>
                                <li>{{ $filesCount }} project files</li>
                                <li>{{ $milestonesCount }} project milestones</li>
                                <li>{{ $messagesCount }} related messages</li>
                                <li>All project history and analytics data</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <div class="flex space-x-3">
                        <button onclick="closeDeleteModal()" 
                                class="flex-1 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Permanently
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>

<script>
// Milestone status update function
function updateMilestoneStatus(milestoneId, status) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Security token not found. Please refresh the page.');
        return;
    }

    // Show loading state
    const milestoneItem = document.querySelector(`[data-milestone-id="${milestoneId}"]`);
    if (milestoneItem) {
        milestoneItem.style.opacity = '0.6';
    }

    fetch(`{{ route('admin.projects.show', $project) }}/milestones/${milestoneId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            
            // Reload the page to update the UI
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Failed to update milestone status');
        }
    })
    .catch(error => {
        console.error('Error updating milestone status:', error);
        showNotification(error.message || 'Failed to update milestone status', 'error');
        
        // Restore original state
        if (milestoneItem) {
            milestoneItem.style.opacity = '1';
        }
    });
}

// Delete milestone function
function deleteMilestone(milestoneId) {
    if (!confirm('Are you sure you want to delete this milestone? This action cannot be undone.')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Security token not found. Please refresh the page.');
        return;
    }

    // Show loading state
    const milestoneItem = document.querySelector(`[data-milestone-id="${milestoneId}"]`);
    if (milestoneItem) {
        milestoneItem.style.opacity = '0.6';
    }

    fetch(`{{ route('admin.projects.show', $project) }}/milestones/${milestoneId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Remove the milestone from the UI
            if (milestoneItem) {
                milestoneItem.remove();
            }
            
            // Reload after a short delay to update statistics
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to delete milestone');
        }
    })
    .catch(error => {
        console.error('Error deleting milestone:', error);
        showNotification(error.message || 'Failed to delete milestone', 'error');
        
        // Restore original state
        if (milestoneItem) {
            milestoneItem.style.opacity = '1';
        }
    });
}

// Filter milestones function
function filterMilestones() {
    const statusFilter = document.getElementById('milestone-status-filter').value;
    const priorityFilter = document.getElementById('milestone-priority-filter').value;
    const milestoneItems = document.querySelectorAll('.milestone-item');

    milestoneItems.forEach(item => {
        const itemStatus = item.getAttribute('data-status');
        const itemPriority = item.getAttribute('data-priority');
        
        let showItem = true;
        
        // Filter by status
        if (statusFilter && itemStatus !== statusFilter) {
            showItem = false;
        }
        
        // Filter by priority
        if (priorityFilter && itemPriority !== priorityFilter) {
            showItem = false;
        }
        
        // Show/hide item
        if (showItem) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    // Show message if no items are visible
    const visibleItems = document.querySelectorAll('.milestone-item[style="display: block"], .milestone-item:not([style*="display: none"])');
    const timelineContainer = document.getElementById('milestones-timeline');
    
    if (visibleItems.length === 0 && timelineContainer) {
        let noResultsMessage = document.getElementById('no-results-message');
        if (!noResultsMessage) {
            noResultsMessage = document.createElement('div');
            noResultsMessage.id = 'no-results-message';
            noResultsMessage.className = 'text-center py-8 text-gray-500 dark:text-gray-400';
            noResultsMessage.innerHTML = '<p>No milestones match the selected filters.</p>';
            timelineContainer.parentNode.appendChild(noResultsMessage);
        }
        noResultsMessage.style.display = 'block';
    } else {
        const noResultsMessage = document.getElementById('no-results-message');
        if (noResultsMessage) {
            noResultsMessage.style.display = 'none';
        }
    }
}

// Delete project confirmation
function confirmDelete() {
    document.getElementById('delete-project-modal').classList.remove('hidden');
}

function closeDeleteModal(event) {
    if (!event || event.target === event.currentTarget) {
        document.getElementById('delete-project-modal').classList.add('hidden');
    }
}

// Show notification function
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out`;
    
    let bgColor, textColor, iconSvg;
    
    switch (type) {
        case 'success':
            bgColor = 'bg-green-50 dark:bg-green-900/20';
            textColor = 'text-green-800 dark:text-green-400';
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
            break;
        case 'error':
            bgColor = 'bg-red-50 dark:bg-red-900/20';
            textColor = 'text-red-800 dark:text-red-400';
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
            break;
        default:
            bgColor = 'bg-blue-50 dark:bg-blue-900/20';
            textColor = 'text-blue-800 dark:text-blue-400';
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
    }
    
    notification.innerHTML = `
        <div class="${bgColor} p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${textColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${iconSvg}
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium ${textColor}">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button onclick="this.closest('.notification-toast').remove()" 
                                class="inline-flex rounded-md p-1.5 ${textColor} hover:bg-black hover:bg-opacity-10 focus:outline-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token to meta tags if not present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const csrfMeta = document.createElement('meta');
        csrfMeta.name = 'csrf-token';
        csrfMeta.content = '{{ csrf_token() }}';
        document.head.appendChild(csrfMeta);
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>

<style>
.notification-toast {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>