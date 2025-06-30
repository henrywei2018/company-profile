{{-- resources/views/client/testimonials/index.blade.php --}}
<x-layouts.client title="My Testimonials">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['My Testimonials' => '']" />

    <!-- Header Section -->
    <x-admin.header-section 
        title="My Testimonials" 
        description="Track and manage your testimonials. Share your experience to help other clients discover our services."
        >
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('client.testimonials.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Write New Testimonial
            </a>
        </div>
    </x-admin.header-section>

    <!-- Enhanced Client Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Testimonials -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Testimonials</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                                @if($stats['this_month'] > 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        +{{ $stats['this_month'] }} this month
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Rate -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approval Rate</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['completion_rate'] }}%</div>
                                <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $stats['approved'] + $stats['featured'] }}/{{ $stats['total'] }} approved
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Average Rating</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $stats['avg_rating'] ?? 'N/A' }}
                                </div>
                                @if($stats['avg_rating'])
                                    <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">/ 5.0</div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Time -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Avg Response Time</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $stats['response_time'] ?? 'N/A' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suggestions Section -->
    @if(!empty($suggestions))
    <div class="mb-8">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Suggestions for You</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300 space-y-2">
                        @foreach($suggestions as $suggestion)
                        <div class="flex items-start">
                            <span class="flex-shrink-0 h-1.5 w-1.5 bg-blue-400 rounded-full mt-2"></span>
                            <div class="ml-3">
                                <p class="font-medium">{{ $suggestion['title'] }}</p>
                                <p class="text-blue-600 dark:text-blue-400">{{ $suggestion['message'] }}</p>
                                @if(isset($suggestion['link']))
                                <a href="{{ $suggestion['link'] }}" class="font-medium hover:text-blue-500">
                                    {{ $suggestion['action'] }} →
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Enhanced Filter Section -->
    <x-admin.filter-section 
        :action="route('client.testimonials.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search by content, project, or company..."
        :hasActiveFilters="request()->hasAny(['search', 'project_id', 'status', 'rating', 'date_from', 'date_to'])"
        :clearFiltersRoute="route('client.testimonials.index')"
        :filters="[
            [
                'name' => 'project_id',
                'label' => 'Project',
                'allLabel' => 'All Projects',
                'options' => $userProjects->pluck('title', 'id')->toArray()
            ],
            [
                'name' => 'status', 
                'label' => 'Status',
                'allLabel' => 'All Status',
                'options' => [
                    'pending' => 'Under Review',
                    'approved' => 'Approved & Live',
                    'featured' => 'Featured',
                    'rejected' => 'Needs Revision'
                ]
            ],
            [
                'name' => 'rating',
                'label' => 'Rating',
                'allLabel' => 'All Ratings',
                'options' => [
                    '5' => '⭐⭐⭐⭐⭐ (5 stars)',
                    '4' => '⭐⭐⭐⭐ (4+ stars)',
                    '3' => '⭐⭐⭐ (3+ stars)',
                    '2' => '⭐⭐ (2+ stars)',
                    '1' => '⭐ (1+ stars)'
                ]
            ]
        ]">
        
        <!-- Additional Date Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
            </div>
        </div>
    </x-admin.filter-section>

    <!-- Testimonials Table/Grid with Client-Oriented Display -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
        @if($testimonials->count() > 0)
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($testimonials as $testimonial)
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <!-- Testimonial Header -->
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <!-- Status Badge -->
                                        @php
                                            $statusConfig = [
                                                'pending' => ['bg-yellow-100 text-yellow-800', 'Under Review'],
                                                'approved' => ['bg-green-100 text-green-800', 'Live & Approved'],
                                                'featured' => ['bg-purple-100 text-purple-800', 'Featured'],
                                                'rejected' => ['bg-red-100 text-red-800', 'Needs Revision']
                                            ];
                                            $config = $statusConfig[$testimonial->status] ?? ['bg-gray-100 text-gray-800', ucfirst($testimonial->status)];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }}">
                                            {{ $config[1] }}
                                        </span>

                                        <!-- Rating Display -->
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                            <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">({{ $testimonial->rating }}/5)</span>
                                        </div>
                                    </div>

                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $testimonial->created_at->format('M d, Y') }}
                                        @if($testimonial->project)
                                            • {{ $testimonial->project->title }}
                                        @endif
                                    </div>
                                </div>

                                <!-- Testimonial Content Preview -->
                                <div class="mb-3">
                                    <p class="text-sm text-gray-900 dark:text-white line-clamp-3">
                                        {{ Str::limit($testimonial->content, 200) }}
                                    </p>
                                </div>

                                <!-- Client Info Display -->
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                    <span>By: {{ $testimonial->client_name }}</span>
                                    @if($testimonial->client_position)
                                        <span>• {{ $testimonial->client_position }}</span>
                                    @endif
                                    @if($testimonial->client_company)
                                        <span>• {{ $testimonial->client_company }}</span>
                                    @endif
                                </div>

                                <!-- Status-specific Messages -->
                                @if($testimonial->status === 'rejected')
                                    <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        This testimonial needs revision. Please edit and resubmit.
                                    </div>
                                @elseif($testimonial->status === 'featured')
                                    <div class="mt-2 text-sm text-purple-600 dark:text-purple-400">
                                        <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Featured testimonial! This is showcased prominently on our website.
                                    </div>
                                @elseif($testimonial->status === 'approved')
                                    <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                                        <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Live on our website and helping other clients discover our services.
                                    </div>
                                @endif

                                <!-- Admin Notes Display -->
                                @if($testimonial->admin_notes)
                                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-4 w-4 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-2">
                                                <p class="text-xs font-medium text-blue-800 dark:text-blue-200">
                                                    @if($testimonial->status === 'rejected')
                                                        Admin Feedback:
                                                    @elseif($testimonial->status === 'approved' || $testimonial->status === 'featured')
                                                        Admin Comment:
                                                    @else
                                                        Note from Team:
                                                    @endif
                                                </p>
                                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                                    {{ $testimonial->admin_notes }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-2">

                                @if(in_array($testimonial->status, ['pending', 'rejected']))
                                    <a href="{{ route('client.testimonials.edit', $testimonial) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('client.testimonials.destroy', $testimonial) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this testimonial?')"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $testimonials->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No testimonials found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['search', 'status', 'project_id']))
                        No testimonials match your current filters.
                    @else
                        Get started by writing your first testimonial.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'status', 'project_id']))
                        <a href="{{ route('client.testimonials.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </a>
                    @else
                        <a href="{{ route('client.testimonials.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Write Your First Testimonial
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Activity Section -->
    @if(!empty($recentActivity) && $recentActivity->count() > 0)
    <div class="mt-8">
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($recentActivity as $index => $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        @php
                                            $iconColors = [
                                                'pending' => 'bg-yellow-500',
                                                'approved' => 'bg-green-500', 
                                                'featured' => 'bg-purple-500',
                                                'rejected' => 'bg-red-500'
                                            ];
                                            $iconColor = $iconColors[$activity['status']] ?? 'bg-gray-500';
                                        @endphp
                                        <span class="h-8 w-8 rounded-full {{ $iconColor }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $activity['description'] }}
                                            </p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ $activity['date']->diffForHumans() }}
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
    @endif
</x-layouts.client>