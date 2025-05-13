<!-- resources/views/admin/dashboard.blade.php -->
<x-admin-layout :title="'Dashboard'">
    <!-- Overview Statistics -->
    <div class="mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Overview</h2>
        
        <x-dashboard-stats :stats="[
            [
                'title' => 'Total Projects',
                'value' => $totalProjects ?? 0,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />',
                'bg_color' => 'bg-blue-100',
                'icon_color' => 'text-blue-600',
                'change' => $projectsChange ?? 0
            ],
            [
                'title' => 'New Messages',
                'value' => $unreadMessages ?? 0,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                'bg_color' => 'bg-red-100',
                'icon_color' => 'text-red-600'
            ],
            [
                'title' => 'Pending Quotations',
                'value' => $pendingQuotations ?? 0,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />',
                'bg_color' => 'bg-amber-100',
                'icon_color' => 'text-amber-600'
            ],
            [
                'title' => 'Total Services',
                'value' => $totalServices ?? 0,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                'bg_color' => 'bg-green-100',
                'icon_color' => 'text-green-600'
            ]
        ]" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Messages -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Recent Messages</h3>
                <a href="{{ route('admin.messages.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentMessages ?? [] as $message)
                    <div class="px-6 py-4 flex items-start">
                        <div class="flex-shrink-0">
                            <span class="inline-block h-10 w-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center">
                                {{ is_object($message) && isset($message->name) ? substr($message->name, 0, 1) : 'U' }}
                            </span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ is_object($message) && isset($message->name) ? $message->name : 'Unknown' }}
                                    @if(is_object($message) && isset($message->is_read) && !$message->is_read)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            New
                                        </span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ is_object($message) && isset($message->created_at) ? $message->created_at->diffForHumans() : '' }}
                                </p>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ is_object($message) && isset($message->subject) ? $message->subject : 'No Subject' }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ is_object($message) && isset($message->message) ? Str::limit($message->message, 100) : '' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-gray-500">
                        No recent messages.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Quotations -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Recent Quotations</h3>
                <a href="{{ route('admin.quotations.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentQuotations ?? [] as $quotation)
                    <div class="px-6 py-4">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ is_object($quotation) && isset($quotation->name) ? $quotation->name : 'Unknown' }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ is_object($quotation) && isset($quotation->status) ? (
                                   $quotation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($quotation->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($quotation->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))
                                ) : 'bg-gray-100 text-gray-800' }}">
                                {{ is_object($quotation) && isset($quotation->status) ? ucfirst($quotation->status) : 'Unknown' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ is_object($quotation) && isset($quotation->project_type) ? $quotation->project_type : 'General Inquiry' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ is_object($quotation) && isset($quotation->created_at) ? $quotation->created_at->format('M d, Y') : '' }}
                        </p>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-gray-500">
                        No recent quotations.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Recent Projects</h2>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all projects</a>
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($recentProjects ?? [] as $project)
                    <li>
                        <a href="{{ route('admin.projects.edit', is_object($project) && isset($project->id) ? $project->id : 0) }}" class="block hover:bg-gray-50">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-indigo-600 truncate">{{ is_object($project) && isset($project->title) ? $project->title : 'Untitled Project' }}</p>
                                        @if(is_object($project) && isset($project->featured) && $project->featured)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ (is_object($project) && isset($project->status) && $project->status === 'completed') ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ is_object($project) && isset($project->status) ? ucfirst($project->status) : 'In Progress' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                            </svg>
                                            {{ is_object($project) && isset($project->category) ? $project->category : 'Uncategorized' }}
                                        </p>
                                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ is_object($project) && isset($project->location) ? $project->location : 'No location specified' }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                        <p>
                                            @if(is_object($project) && isset($project->start_date) && $project->start_date && isset($project->end_date) && $project->end_date)
                                                {{ $project->start_date->format('M Y') }} - {{ $project->end_date->format('M Y') }}
                                            @elseif(is_object($project) && isset($project->year))
                                                {{ $project->year }}
                                            @else
                                                No date specified
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-5 sm:px-6 text-center text-gray-500">
                        No recent projects found.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</x-admin-layout>