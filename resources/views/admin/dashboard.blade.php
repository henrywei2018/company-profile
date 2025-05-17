<!-- resources/views/admin/dashboard.blade.php -->
<x-layouts.admin>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Statistics Section -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Projects Stat -->
            <x-stat-card 
                title="Total Projects" 
                value="{{ $totalProjects }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' />"
                iconColor="text-purple-500" 
                iconBg="bg-purple-100" 
                change="{{ $projectsChange }}"
                href="{{ route('admin.projects.index') }}"
            />
            
            <!-- Clients Stat -->
            <x-stat-card 
                title="Active Clients" 
                value="{{ $activeClients }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' />"
                iconColor="text-blue-500" 
                iconBg="bg-blue-100" 
                change="{{ $clientsChange }}"
                href="{{ route('admin.clients.index') }}"
            />
            
            <!-- Messages Stat -->
            <x-stat-card 
                title="Unread Messages" 
                value="{{ $unreadMessages }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' />"
                iconColor="text-amber-500" 
                iconBg="bg-amber-100" 
                href="{{ route('admin.messages.index') }}"
            />
            
            <!-- Quotations Stat -->
            <x-stat-card 
                title="Pending Quotes" 
                value="{{ $pendingQuotations }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' />"
                iconColor="text-green-500" 
                iconBg="bg-green-100" 
                href="{{ route('admin.quotations.index') }}"
            />
        </div>
    </div>
    
    <!-- Quick Actions Section -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Add Project -->
            <x-quick-action 
                title="New Project" 
                description="Create a new project" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4' />"
                href="{{ route('admin.projects.create') }}"
                color="purple"
            />
            
            <!-- Add Service -->
            <x-quick-action 
                title="New Service" 
                description="Add a new service" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4' />"
                href="{{ route('admin.services.create') }}"
                color="blue"
            />
            
            <!-- Add Blog Post -->
            <x-quick-action 
                title="New Blog Post" 
                description="Write a new article" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4' />"
                href="{{ route('admin.blog.create') }}"
                color="green"
            />
            
            <!-- Check Messages -->
            <x-quick-action 
                title="View Messages" 
                description="Check recent inquiries" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' />"
                href="{{ route('admin.messages.index') }}"
                color="amber"
            />
        </div>
    </div>
    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Messages -->
        <x-recent-activity title="Recent Messages" viewAllRoute="{{ route('admin.messages.index') }}">
            @forelse($recentMessages as $message)
                <x-activity-item 
                    route="{{ route('admin.messages.show', $message) }}"
                    icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' />"
                    iconColor="text-blue-500"
                    iconBg="bg-blue-100"
                    time="{{ $message->created_at->diffForHumans() }}"
                >
                    <span class="{{ $message->is_read ? 'font-normal' : 'font-bold' }}">
                        {{ $message->subject }}
                    </span> from {{ $message->name }}
                </x-activity-item>
            @empty
                <li class="py-3 text-gray-500 dark:text-gray-400 text-center">
                    No new messages
                </li>
            @endforelse
        </x-recent-activity>
        
        <!-- Recent Quotations -->
        <x-recent-activity title="Recent Quotation Requests" viewAllRoute="{{ route('admin.quotations.index') }}">
            @forelse($recentQuotations as $quotation)
                <x-activity-item 
                    route="{{ route('admin.quotations.show', $quotation) }}"
                    icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' />"
                    iconColor="text-amber-500"
                    iconBg="bg-amber-100"
                    time="{{ $quotation->created_at->diffForHumans() }}"
                >
                    Quotation for {{ $quotation->service }} 
                    <x-status-badge status="{{ $quotation->status }}" />
                </x-activity-item>
            @empty
                <li class="py-3 text-gray-500 dark:text-gray-400 text-center">
                    No new quotation requests
                </li>
            @endforelse
        </x-recent-activity>
    </div>
    
    <!-- Recent Projects -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Recent Projects</h2>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all projects</a>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <x-table>
                <x-slot name="header">
                    <tr>
                        <x-table.heading>Project</x-table.heading>
                        <x-table.heading>Client</x-table.heading>
                        <x-table.heading>Status</x-table.heading>
                        <x-table.heading>Timeline</x-table.heading>
                        <x-table.heading>Actions</x-table.heading>
                    </tr>
                </x-slot>
                
                @forelse($recentProjects as $project)
                    <x-table.row>
                        <x-table.cell>
                            <div class="flex items-center">
                                @if($project->featured_image)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-md object-cover" src="{{ $project->featuredImageUrl }}" alt="{{ $project->title }}">
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $project->title }}</div>
                                    <div class="text-gray-500 dark:text-gray-400 text-sm">{{ Str::limit($project->excerpt, 50) }}</div>
                                </div>
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            {{ $project->client->name ?? 'N/A' }}
                        </x-table.cell>
                        <x-table.cell>
                            <x-status-badge status="{{ $project->status }}" />
                        </x-table.cell>
                        <x-table.cell>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $project->start_date ? $project->start_date->format('M d, Y') : 'TBD' }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                to {{ $project->end_date ? $project->end_date->format('M d, Y') : 'TBD' }}
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            <x-dropdown-menu>
                                <x-dropdown-item href="{{ route('admin.projects.show', $project) }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' />">
                                    View
                                </x-dropdown-item>
                                <x-dropdown-item href="{{ route('admin.projects.edit', $project) }}" icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' />">
                                    Edit
                                </x-dropdown-item>
                                <x-dropdown-button 
                                    method="DELETE" 
                                    action="{{ route('admin.projects.destroy', $project) }}" 
                                    confirm="true" 
                                    confirmMessage="Are you sure you want to delete this project?"
                                    icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' />"
                                >
                                    Delete
                                </x-dropdown-button>
                            </x-dropdown-menu>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="5" class="text-center py-4">
                            <div class="text-gray-500 dark:text-gray-400">No projects found</div>
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table>
        </div>
    </div>
</x-layouts.admin>