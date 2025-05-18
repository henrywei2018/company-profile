<!-- resources/views/admin/dashboard.blade.php -->
<x-layouts.admin :title="'Dashboard'" :enableCharts="true">
    <!-- Statistics Section -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Projects Stat -->
            <x-admin.stat-card 
                title="Total Projects" 
                value="{{ $totalProjects }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z' />"
                iconColor="text-purple-500" 
                iconBg="bg-purple-100" 
                :change="$projectsChange"
                href="{{ route('admin.projects.index') }}"
            />
            
            <!-- Clients Stat -->
            <x-admin.stat-card 
                title="Active Clients" 
                value="{{ $activeClients }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' />"
                iconColor="text-blue-500" 
                iconBg="bg-blue-100" 
                :change="$clientsChange"
                href="{{ route('admin.users.index', ['role' => 'client']) }}"
            />
            
            <!-- Messages Stat -->
            <x-admin.stat-card 
                title="Unread Messages" 
                value="{{ $unreadMessages }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' />"
                iconColor="text-red-500" 
                iconBg="bg-red-100"
                href="{{ route('admin.messages.index', ['read' => 'unread']) }}"
            />
            
            <!-- Quotations Stat -->
            <x-admin.stat-card 
                title="Pending Quotations" 
                value="{{ $pendingQuotations }}" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' />"
                iconColor="text-amber-500" 
                iconBg="bg-amber-100"
                href="{{ route('admin.quotations.index', ['status' => 'pending']) }}"
            />
        </div>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="mb-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                    {{ $message->subject }} from {{ $message->name }}
                </x-activity-item>
            @empty
                <li class="py-4 text-center text-gray-500 dark:text-gray-400">
                    No recent messages
                </li>
            @endforelse
        </x-recent-activity>
        
        <!-- Recent Quotations -->
        <x-recent-activity title="Recent Quotations" viewAllRoute="{{ route('admin.quotations.index') }}">
            @forelse($recentQuotations as $quotation)
                <x-activity-item 
                    route="{{ route('admin.quotations.show', $quotation) }}" 
                    icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' />" 
                    iconColor="text-amber-500" 
                    iconBg="bg-amber-100" 
                    time="{{ $quotation->created_at->diffForHumans() }}"
                >
                    {{ $quotation->service->title ?? 'General Inquiry' }} by {{ $quotation->name }}
                </x-activity-item>
            @empty
                <li class="py-4 text-center text-gray-500 dark:text-gray-400">
                    No recent quotations
                </li>
            @endforelse
        </x-recent-activity>
    </div>
    
    <!-- Recent Projects -->
    <div class="mb-6">
        <div class="bg-white dark:bg-neutral-800 shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Projects</h3>
                <a href="{{ route('admin.projects.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
            </div>
            <div class="p-4">
                @if($recentProjects->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Project</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                                @foreach($recentProjects as $project)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.projects.show', $project) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $project->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $project->client->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge status="{{ $project->status }}"></x-status-badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $project->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-4 text-center text-gray-500 dark:text-gray-400">
                        No recent projects
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-quick-action 
                title="Add New Project" 
                description="Create a new client project" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 6v6m0 0v6m0-6h6m-6 0H6' />"
                href="{{ route('admin.projects.create') }}"
                color="blue"
            />
            
            <x-quick-action 
                title="Add New Service" 
                description="Create a new service offering" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' />"
                href="{{ route('admin.services.create') }}"
                color="green"
            />
            
            <x-quick-action 
                title="New Blog Post" 
                description="Write a new article" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' />"
                href="{{ route('admin.blog.create') }}"
                color="purple"
            />
            
            <x-quick-action 
                title="Update Company" 
                description="Manage company information" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' />"
                href="{{ route('admin.company.edit') }}"
                color="amber"
            />
        </div>
    </div>
</x-layouts.admin>