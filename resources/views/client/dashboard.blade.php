<!-- resources/views/client/dashboard.blade.php -->
<x-layouts.client :title="'Dashboard'" :enableCharts="true" :unreadMessages="$unreadMessagesCount ?? 0" :pendingApprovals="$pendingClientQuotationsCount ?? 0">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row">
                <!-- Sidebar -->
                <x-client.client-sidebar :unreadClientMessagesCount="$unreadClientMessagesCount ?? 0" :pendingClientQuotationsCount="$pendingClientQuotationsCount ?? 0" />

                <!-- Main Content -->
                <div class="w-full md:flex-1 py-6 px-4 md:px-8">
                    <div class="mb-8">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Welcome back, {{ auth()->user()->name }}</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Here's what's happening with your projects and quotations.</p>
                    </div>

                    <!-- Project Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-md p-3">
                                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Projects</h3>
                                        <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                                            {{ $totalProjects ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3">
                                <a href="{{ route('client.projects.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                    View all projects →
                                </a>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-amber-100 dark:bg-amber-900/30 rounded-md p-3">
                                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quotations</h3>
                                        <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                                            {{ $totalQuotations ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3">
                                <a href="{{ route('client.quotations.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                    View all quotations →
                                </a>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-md p-3">
                                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Messages</h3>
                                        <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                                            {{ $totalMessages ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3">
                                <a href="{{ route('client.messages.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                    View all messages →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Active Projects -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Active Projects</h2>
                            <a href="{{ route('client.projects.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                View all
                            </a>
                        </div>

                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            @if(isset($activeProjects) && count($activeProjects) > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Project
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Timeline
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($activeProjects as $project)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $project->title }}
                                                                </div>
                                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ $project->category }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $project->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                                               ($project->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 
                                                               'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        @if(isset($project->start_date) && isset($project->end_date))
                                                            {{ $project->start_date->format('M d, Y') }} - {{ $project->end_date->format('M d, Y') }}
                                                        @else
                                                            Not specified
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('client.projects.show', $project->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                            View details
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No active projects</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        You don't have any active projects at the moment.
                                    </p>
                                    <div class="mt-6">
                                        <a href="{{ route('client.quotations.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                            Request a quote
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Latest Quotations & Messages -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Latest Quotations -->
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Latest Quotations</h3>
                            </div>
                            @if(isset($latestQuotations) && count($latestQuotations) > 0)
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($latestQuotations as $quotation)
                                        <div class="px-6 py-4">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $quotation->project_type ?? 'General Inquiry' }}
                                                </h4>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ ($quotation->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                                       ($quotation->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                                       'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400')) }}">
                                                    {{ ucfirst($quotation->status) }}
                                                </span>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Submitted on {{ $quotation->created_at->format('M d, Y') }}
                                            </p>
                                            <div class="mt-2">
                                                <a href="{{ route('client.quotations.show', $quotation->id) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                    View details →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-3 text-right">
                                    <a href="{{ route('client.quotations.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-medium">
                                        View all quotations →
                                    </a>
                                </div>
                            @else
                                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <p class="text-sm">No quotations found.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('client.quotations.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                            Request a quote
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Latest Messages -->
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Latest Messages</h3>
                            </div>
                            @if(isset($latestMessages) && count($latestMessages) > 0)
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($latestMessages as $message)
                                        <div class="px-6 py-4">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $message->subject }}
                                                </h4>
                                                @if(!$message->is_read)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                        New
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $message->created_at->format('M d, Y') }}
                                            </p>
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                                {{ Str::limit($message->message, 100) }}
                                            </p>
                                            <div class="mt-2">
                                                <a href="{{ route('client.messages.show', $message->id) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                    Read more →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-3 text-right">
                                    <a href="{{ route('client.messages.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-medium">
                                        View all messages →
                                    </a>
                                </div>
                            @else
                                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <p class="text-sm">No messages found.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('contact.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                            Contact us
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.client>