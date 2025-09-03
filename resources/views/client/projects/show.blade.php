{{-- resources/views/client/projects/show.blade.php --}}
<x-layouts.client :title="$project->title">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('client.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
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
                    <a href="{{ route('client.projects.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                        Proyek Saya
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
                            {{ $project->status === 'on_hold' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                            {{ $project->status === 'cancelled' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    
                    @if($project->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $project->description }}</p>
                    @endif
                    
                    <!-- Project Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        @if($project->category)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">Category: </span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->category->name }}</span>
                            </div>
                        @endif
                        
                        @if($project->service)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H6a2 2 0 00-2-2V4m8 0H8m0 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-2V6"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">Service: </span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->service->title }}</span>
                            </div>
                        @endif
                        
                        @if($project->location)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">Location: </span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->location }}</span>
                            </div>
                        @endif
                        
                        @if($project->start_date)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">Start Date: </span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->start_date->format('M d, Y') }}</span>
                            </div>
                        @endif
                        
                        @if($project->end_date)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">End Date: </span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->end_date->format('M d, Y') }}</span>
                            </div>
                        @endif
                        
                        @if($project->budget)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">Budget: </span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1">Rp.{{ number_format($project->budget, 0) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-6 lg:mt-0 lg:ml-6 flex flex-col sm:flex-row gap-3">
                    @if($project->status === 'completed' && !$project->testimonial)
                        <a href="{{ route('client.projects.testimonial', $project) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Leave Review
                        </a>
                    @endif
                    
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Images -->
            @if($project->images && $project->images->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Gallery</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($project->images as $image)
                                <div class="relative group cursor-pointer" onclick="openImageModal('{{ Storage::url($image->image_path) }}', '{{ $image->alt_text ?? $project->title }}')">
                                    <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                                        <img src="{{ Storage::url($image->image_path) }}" 
                                             alt="{{ $image->alt_text ?? $project->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy">
                                    </div>
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                    @if($image->is_featured)
                                        <div class="absolute top-2 left-2">
                                            <span class="bg-orange-500 text-white px-2 py-1 rounded text-xs font-medium">Unggulan</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Project Progress -->
            @if($progress)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Progress</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span>Overall Progress</span>
                                <span>{{ $progress['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ $progress['percentage'] }}%"></div>
                            </div>
                        </div>
                        
                        @if($progress['method'] === 'milestone_based')
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $progress['total_milestones'] }}</div>
                                    <div class="text-sm text-gray-500">Total</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-green-600">{{ $progress['completed_milestones'] }}</div>
                                    <div class="text-sm text-gray-500">Selesai</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $progress['in_progress_milestones'] ?? 0 }}</div>
                                    <div class="text-sm text-gray-500">In Progress</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Project Milestones -->
            @if($milestones && $milestones->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($milestones as $milestone)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                            {{ $milestone->status === 'completed' ? 'bg-green-100 text-green-600' : '' }}
                                            {{ $milestone->status === 'in_progress' ? 'bg-blue-100 text-blue-600' : '' }}
                                            {{ $milestone->status === 'pending' ? 'bg-gray-100 text-gray-600' : '' }}
                                            {{ $milestone->status === 'delayed' ? 'bg-red-100 text-red-600' : '' }}">
                                            @if($milestone->status === 'completed')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <div class="w-2 h-2 rounded-full bg-current"></div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $milestone->title }}</h4>
                                        @if($milestone->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $milestone->description }}</p>
                                        @endif
                                        <div class="flex items-center mt-2 text-xs text-gray-500">
                                            @if($milestone->due_date)
                                                <span>Due: {{ $milestone->due_date->format('M d, Y') }}</span>
                                            @endif
                                            @if($milestone->completed_at)
                                                <span class="ml-3 text-green-600">Selesai: {{ $milestone->completed_at->format('M d, Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Project Files -->
            @if($files && $files->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Files</h3>
                    </div>
                    <div class="p-6">
                        @foreach($files as $category => $categoryFiles)
                            <div class="mb-6 last:mb-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3 capitalize">{{ $category ?? 'General' }}</h4>
                                <div class="space-y-2">
                                    @foreach($categoryFiles as $file)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 mr-3">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $file->original_name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $file->file_size_human }} • {{ $file->created_at->format('M d, Y') }}</div>
                                                </div>
                                            </div>
                                            <a href="{{ route('client.projects.files.download', [$project, $file]) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-600 bg-blue-100 hover:bg-blue-200 transition-colors duration-200">
                                                Unduh
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Project Summary -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Summary</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($project->quotation)
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Related Quotation</div>
                            <a href="{{ route('client.quotations.show', $project->quotation) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                View Quotation #{{ $project->quotation->id }}
                            </a>
                        </div>
                    @endif
                    
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Created</div>
                        <div class="font-medium">{{ $project->created_at->format('M d, Y') }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Last Updated</div>
                        <div class="font-medium">{{ $project->updated_at->format('M d, Y g:i A') }}</div>
                    </div>
                    
                    @if($project->actual_completion_date)
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Selesai pada</div>
                            <div class="font-medium text-green-600">{{ $project->actual_completion_date->format('M d, Y') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Messages -->
            @if($messages && $messages->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Messages</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($messages as $message)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $message->subject }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $message->created_at->format('M d, Y g:i A') }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('client.messages.index', ['project_id' => $project->id]) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                View All Messages
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Project Alerts -->
            @if(isset($projectAlerts) && !empty($projectAlerts))
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Important Updates</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($projectAlerts as $alert)
                                <div class="p-3 rounded-lg {{ $alert['type'] === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 'bg-blue-50 border border-blue-200' }}">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            @if($alert['type'] === 'warning')
                                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium {{ $alert['type'] === 'warning' ? 'text-yellow-800' : 'text-blue-800' }}">
                                                {{ $alert['message'] }}
                                            </div>
                                            @if(isset($alert['date']))
                                                <div class="text-xs {{ $alert['type'] === 'warning' ? 'text-yellow-600' : 'text-blue-600' }} mt-1">
                                                    {{ $alert['date'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Projects -->
            @if($relatedProjects && $relatedProjects->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Related Projects</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($relatedProjects as $relatedProject)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    @if($relatedProject->images && $relatedProject->images->count() > 0)
                                        <div class="w-full h-32 bg-gray-200 dark:bg-gray-600 rounded-lg mb-3 overflow-hidden">
                                            <img src="{{ Storage::url($relatedProject->images->first()->image_path) }}" 
                                                 alt="{{ $relatedProject->title }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('client.projects.show', $relatedProject) }}" 
                                               class="hover:text-blue-600 transition-colors duration-200">
                                                {{ $relatedProject->title }}
                                            </a>
                                        </h4>
                                        @if($relatedProject->category)
                                            <div class="text-xs text-gray-500 mt-1">{{ $relatedProject->category->name }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            Status: <span class="capitalize">{{ str_replace('_', ' ', $relatedProject->status) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('client.projects.index') }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                View All Projects
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Testimonial Section -->
            @if($project->status === 'completed')
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Review</h3>
                    </div>
                    <div class="p-6">
                        @if($project->testimonial)
                            <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $project->testimonial->rating)
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-green-800 dark:text-green-200 font-medium">Your Review</span>
                                </div>
                                <p class="text-sm text-green-700 dark:text-green-300">{{ $project->testimonial->content }}</p>
                                <div class="text-xs text-green-600 dark:text-green-400 mt-2">
                                    Kirimted on {{ $project->testimonial->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Share Your Experience</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                    Help others by sharing your experience with this completed project.
                                </p>
                                <a href="{{ route('client.projects.testimonial', $project) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors duration-200">
                                    Tulis Ulasan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()" 
                    class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        </div>
    </div>

    @push('scripts')
    <script>
        function openImageModal(src, alt) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = src;
            modalImage.alt = alt;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        // Auto-refresh progress bar animation
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.bg-gradient-to-r');
            if (progressBar) {
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = progressBar.style.width;
                }, 100);
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .aspect-w-16 {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
        }

        .aspect-w-16 > * {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media print {
            .no-print, 
            .no-print * {
                display: none !important;
            }
            
            .print-break {
                page-break-after: always;
            }
            
            .bg-white {
                background: white !important;
            }
            
            .text-gray-900 {
                color: black !important;
            }
            
            .border-gray-200 {
                border-color: #e5e7eb !important;
            }
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Custom scrollbar for timeline */
        .timeline-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .timeline-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .timeline-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .timeline-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .timeline-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @endpush
</x-layouts.client>