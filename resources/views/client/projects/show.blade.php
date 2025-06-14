{{-- resources/views/client/projects/show.blade.php --}}
<x-layouts.client :title="$project->title">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('client.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path>
                    </svg>
                    <a href="{{ route('client.projects.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        My Projects
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $project->title }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Project Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $project->title }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $project->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $project->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $project->status === 'planning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $project->status === 'on_hold' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    
                    @if($project->description)
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">{{ $project->description }}</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                        @if($project->category)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                                </svg>
                                {{ $project->category->name }}
                            </div>
                        @endif

                        @if($project->start_date)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"></path>
                                </svg>
                                Started {{ $project->start_date->format('M d, Y') }}
                            </div>
                        @endif

                        @if($project->end_date)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path>
                                </svg>
                                Due {{ $project->end_date->format('M d, Y') }}
                            </div>
                        @endif

                        @if($project->service)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                                {{ $project->service->title }}
                            </div>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    @if($project->status === 'in_progress' && isset($progress))
                        <div class="mt-6">
                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span>Project Progress</span>
                                <span>{{ $progress['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 lg:mt-0 lg:ml-6 flex flex-col sm:flex-row gap-3">
                    @if($project->status === 'completed' && !$project->testimonial)
                        <a href="{{ route('client.projects.testimonial', $project) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Write Review
                        </a>
                    @endif

                    @if($project->quotation)
                        <a href="{{ route('client.quotations.show', $project->quotation) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z"></path>
                            </svg>
                            View Quotation
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Project Content Tabs -->
    <div x-data="{ activeTab: 'overview' }" class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'overview'" 
                        :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Overview
                </button>

                @if($milestones && $milestones->count() > 0)
                <button @click="activeTab = 'milestones'" 
                        :class="activeTab === 'milestones' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Milestones
                    <span class="ml-2 bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs px-2 py-1">
                        {{ $milestones->count() }}
                    </span>
                </button>
                @endif

                @if($files && $files->count() > 0)
                <button @click="activeTab = 'files'" 
                        :class="activeTab === 'files' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Files
                    <span class="ml-2 bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs px-2 py-1">
                        {{ $files->sum(fn($group) => $group->count()) }}
                    </span>
                </button>
                @endif

                @if($project->images && $project->images->count() > 0)
                <button @click="activeTab = 'gallery'" 
                        :class="activeTab === 'gallery' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Gallery
                    <span class="ml-2 bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs px-2 py-1">
                        {{ $project->images->count() }}
                    </span>
                </button>
                @endif

                @if($messages && $messages->count() > 0)
                <button @click="activeTab = 'messages'" 
                        :class="activeTab === 'messages' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Messages
                    <span class="ml-2 bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs px-2 py-1">
                        {{ $messages->count() }}
                    </span>
                </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'">
                <div class="space-y-8">
                    <!-- Project Details -->
                    @if($project->challenge || $project->solution || $project->results)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        @if($project->challenge)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Challenge</h3>
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                {!! nl2br(e($project->challenge)) !!}
                            </div>
                        </div>
                        @endif

                        @if($project->solution)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Solution</h3>
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                {!! nl2br(e($project->solution)) !!}
                            </div>
                        </div>
                        @endif

                        @if($project->results)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Results</h3>
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                {!! nl2br(e($project->results)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Project Timeline Summary -->
                    @if($milestones && $milestones->count() > 0)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Timeline Overview</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Milestones Completed</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $milestones->where('status', 'completed')->count() }} of {{ $milestones->count() }}
                                </span>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-600">
                                @php
                                    $completedPercentage = $milestones->count() > 0 ? ($milestones->where('status', 'completed')->count() / $milestones->count()) * 100 : 0;
                                @endphp
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $completedPercentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Recent Activity -->
                    @if($messages && $messages->count() > 0)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Communication</h3>
                        <div class="space-y-3">
                            @foreach($messages->take(3) as $message)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $message->subject }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $message->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $message->is_read ? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                        {{ $message->is_read ? 'Read' : 'Unread' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Testimonial Section -->
                    @if($project->testimonial)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Your Review</h3>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $project->testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $project->testimonial->rating }}/5</span>
                                </div>
                                <span class="ml-auto text-sm text-gray-500 dark:text-gray-400">
                                    {{ $project->testimonial->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300">{{ $project->testimonial->content }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm {{ $project->testimonial->is_active ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                    {{ $project->testimonial->is_active ? 'Published' : 'Pending Review' }}
                                </span>
                                @if($project->testimonial->featured)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        Featured Review
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Milestones Tab -->
            @if($milestones && $milestones->count() > 0)
            <div x-show="activeTab === 'milestones'">
                <div class="space-y-6">
                    @foreach($milestones as $milestone)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $milestone->title }}</h3>
                                @if($milestone->description)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $milestone->description }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $milestone->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $milestone->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $milestone->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                            </span>
                        </div>

                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-6">
                            @if($milestone->due_date)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"></path>
                                    </svg>
                                    Due {{ $milestone->due_date->format('M d, Y') }}
                                </div>
                            @endif

                            @if($milestone->completed_at)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                                    </svg>
                                    Completed {{ $milestone->completed_at->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Files Tab -->
            @if($files && $files->count() > 0)
            <div x-show="activeTab === 'files'">
                <div class="space-y-6">
                    @foreach($files as $category => $categoryFiles)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            {{ $category ? ucfirst($category) : 'General Files' }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($categoryFiles as $file)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $file->original_name }}
                                        </p>
                                        @if($file->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $file->description }}</p>
                                        @endif
                                        <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span>{{ $file->human_readable_size }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{ $file->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('client.projects.files.download', [$project, $file]) }}" 
                                           class="inline-flex items-center p-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Gallery Tab -->
            @if($project->images && $project->images->count() > 0)
            <div x-show="activeTab === 'gallery'">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($project->images as $image)
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
                         @click="$dispatch('open-modal', { image: '{{ Storage::url($image->file_path) }}', title: '{{ $image->title ?? $project->title }}' })">
                        <div class="aspect-w-16 aspect-h-9">
                            <img src="{{ Storage::url($image->file_path) }}" 
                                 alt="{{ $image->title ?? $project->title }}"
                                 class="w-full h-64 object-cover">
                        </div>
                        @if($image->title || $image->description)
                        <div class="p-4">
                            @if($image->title)
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $image->title }}</h4>
                            @endif
                            @if($image->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $image->description }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Messages Tab -->
            @if($messages && $messages->count() > 0)
            <div x-show="activeTab === 'messages'">
                <div class="space-y-4">
                    @foreach($messages as $message)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $message->subject }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $message->created_at->format('M d, Y \a\t g:i A') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $message->is_read ? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                {{ $message->is_read ? 'Read' : 'Unread' }}
                            </span>
                        </div>
                        <div class="prose prose-sm max-w-none dark:prose-invert">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('client.messages.show', $message) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                View full conversation →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Related Projects -->
    @if($relatedProjects && $relatedProjects->count() > 0)
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Your Other {{ $project->category->name ?? 'Similar' }} Projects</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($relatedProjects as $relatedProject)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                @if($relatedProject->images->first())
                    <div class="h-32 bg-gray-200 dark:bg-gray-700 overflow-hidden">
                        <img src="{{ Storage::url($relatedProject->images->first()->file_path) }}" 
                             alt="{{ $relatedProject->title }}"
                             class="w-full h-full object-cover">
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        <a href="{{ route('client.projects.show', $relatedProject) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                            {{ $relatedProject->title }}
                        </a>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ Str::limit($relatedProject->description, 80) }}
                    </p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $relatedProject->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $relatedProject->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $relatedProject->status === 'planning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $relatedProject->status)) }}
                        </span>
                        <a href="{{ route('client.projects.show', $relatedProject) }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Image Modal -->
    <div x-data="{ open: false, currentImage: '', currentTitle: '' }" 
         @open-modal.window="open = true; currentImage = $event.detail.image; currentTitle = $event.detail.title"
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" 
                 @click="open = false"></div>

            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative bg-white dark:bg-gray-800 rounded-lg max-w-4xl mx-auto">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="open = false" 
                            class="bg-white dark:bg-gray-800 rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <img :src="currentImage" :alt="currentTitle" class="w-full h-auto rounded-lg">
                    <h3 x-text="currentTitle" class="mt-4 text-lg font-medium text-gray-900 dark:text-white text-center"></h3>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-layouts.client>