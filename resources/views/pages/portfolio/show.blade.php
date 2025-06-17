{{-- resources/views/pages/portfolio/show.blade.php --}}
<x-layouts.public
    :title="$project->title . ' - Portfolio - ' . $siteConfig['site_title']"
    :description="$project->short_description ?: 'View details of our ' . $project->title . ' project.'"
    :keywords="$project->title . ', construction project, portfolio'"
    type="article"
>

{{-- Hero Section --}}
<section class="relative pt-32 pb-20 bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('portfolio.index') }}" class="ml-1 text-gray-700 hover:text-orange-600 md:ml-2">Portfolio</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-orange-600 md:ml-2 font-medium">{{ $project->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Project Content --}}
            <div>
                <div class="flex items-center mb-4 gap-3">
                    @if($project->category)
                    <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $project->category->name }}
                    </span>
                    @endif
                    @if($project->featured)
                    <span class="bg-amber-100 text-amber-600 px-3 py-1 rounded-full text-sm font-medium">
                        Featured Project
                    </span>
                    @endif
                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    {{ $project->title }}
                </h1>
                
                @if($project->short_description)
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    {{ $project->short_description }}
                </p>
                @endif
                
                {{-- Project Details --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                    @if($project->client)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Client</div>
                            <div class="text-sm text-gray-600">{{ $project->client->name }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($project->service)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Service</div>
                            <div class="text-sm text-gray-600">{{ $project->service->title }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($project->location)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Location</div>
                            <div class="text-sm text-gray-600">{{ $project->location }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($project->year)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Year</div>
                            <div class="text-sm text-gray-600">{{ $project->year }}</div>
                        </div>
                    </div>
                    @endif
                </div>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('contact.index', ['project' => $project->title]) }}" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                        </svg>
                        Discuss Similar Project
                    </a>
                    <a href="{{ route('portfolio.index') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 border-2 border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        View All Projects
                    </a>
                </div>
            </div>
            
            {{-- Project Image --}}
            <div class="relative">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-500">
                    @if($project->featured_image_url)
                        <img src="{{ $project->featured_image_url }}" 
                             alt="{{ $project->title }}" 
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Project Details Section --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Main Content --}}
            <div class="lg:col-span-2">
                {{-- Project Description --}}
                @if($project->description)
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Project Overview</h2>
                    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($project->description)) !!}
                    </div>
                </div>
                @endif
                
                {{-- Challenge, Solution, Results --}}
                <div class="space-y-12">
                    @if($project->challenge)
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            Challenge
                        </h3>
                        <p class="text-gray-700 leading-relaxed">{{ $project->challenge }}</p>
                    </div>
                    @endif
                    
                    @if($project->solution)
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            Solution
                        </h3>
                        <p class="text-gray-700 leading-relaxed">{{ $project->solution }}</p>
                    </div>
                    @endif
                    
                    @if($project->results)
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            Results
                        </h3>
                        <p class="text-gray-700 leading-relaxed">{{ $project->results }}</p>
                    </div>
                    @endif
                </div>

                {{-- Project Gallery --}}
                @if($project->images && $project->images->count() > 0)
                <div class="mt-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Project Gallery</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($project->images as $image)
                        <div class="group relative rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 cursor-pointer gallery-item" 
                             data-src="{{ asset('storage/' . $image->image_path) }}"
                             data-alt="{{ $image->alt_text ?: $project->title }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $image->alt_text ?: $project->title }}" 
                                 class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Project Milestones --}}
                @if($project->milestones && $project->milestones->count() > 0)
                <div class="mt-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Project Milestones</h2>
                    <div class="relative">
                        {{-- Timeline Line --}}
                        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-orange-500 via-orange-400 to-orange-300"></div>
                        
                        <div class="space-y-8">
                            @foreach($project->milestones->sortBy('sort_order') as $milestone)
                            <div class="relative flex items-start">
                                {{-- Milestone Icon --}}
                                <div class="relative z-10 w-12 h-12 rounded-full flex items-center justify-center mr-6 flex-shrink-0
                                    {{ $milestone->status === 'completed' ? 'bg-green-500' : ($milestone->status === 'in_progress' ? 'bg-orange-500' : 'bg-gray-400') }}">
                                    @if($milestone->status === 'completed')
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif($milestone->status === 'in_progress')
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                
                                {{-- Milestone Content --}}
                                <div class="flex-1 bg-white rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-shadow duration-300">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $milestone->title }}</h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            {{ $milestone->status === 'completed' ? 'bg-green-100 text-green-700' : 
                                               ($milestone->status === 'in_progress' ? 'bg-orange-100 text-orange-700' : 
                                                ($milestone->status === 'delayed' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                                        </span>
                                    </div>
                                    
                                    @if($milestone->description)
                                    <p class="text-gray-600 mb-4">{{ $milestone->description }}</p>
                                    @endif
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                        @if($milestone->due_date)
                                        <div class="flex items-center text-gray-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Due: {{ $milestone->due_date->format('M d, Y') }}
                                        </div>
                                        @endif
                                        
                                        @if($milestone->completed_date)
                                        <div class="flex items-center text-green-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Completed: {{ $milestone->completed_date->format('M d, Y') }}
                                        </div>
                                        @endif
                                        
                                        @if($milestone->progress_percent !== null)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between text-xs mb-1">
                                                    <span class="text-gray-600">Progress</span>
                                                    <span class="font-medium">{{ $milestone->progress_percent }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-2 rounded-full transition-all duration-500" 
                                                         style="width: {{ $milestone->progress_percent }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($milestone->notes)
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <p class="text-sm text-gray-600 italic">{{ $milestone->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Project Info Card --}}
                <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Project Details</h3>
                    <div class="space-y-4">
                        @if($project->start_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Start Date:</span>
                            <span class="font-medium">{{ $project->start_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        
                        @if($project->end_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">End Date:</span>
                            <span class="font-medium">{{ $project->end_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        
                        @if($project->budget)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Budget:</span>
                            <span class="font-medium">${{ number_format($project->budget) }}</span>
                        </div>
                        @endif
                        
                        @if($project->progress_percentage !== null)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Progress:</span>
                            <span class="font-medium">{{ $project->progress_percentage }}%</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-green-600">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Contact Card --}}
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Interested in Similar Project?</h3>
                    <p class="text-gray-600 mb-6">
                        Get in touch with our team to discuss your project requirements and get a personalized quote.
                    </p>
                    <div class="space-y-3">
                        <a href="{{ route('contact.index', ['project' => $project->title]) }}" 
                           class="w-full bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold py-3 rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                            </svg>
                            Get Quote
                        </a>
                        
                        @if($contactInfo['phone'])
                        <a href="tel:{{ $contactInfo['phone'] }}" 
                           class="w-full border border-orange-600 text-orange-600 font-semibold py-3 rounded-xl hover:bg-orange-50 transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Call {{ $contactInfo['phone'] }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Related Projects Section --}}
@if($relatedProjects && $relatedProjects->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Related Projects</h2>
            <p class="text-gray-600">Explore other projects similar to this one.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedProjects as $relatedProject)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2">
                {{-- Project Image --}}
                <div class="relative h-48 overflow-hidden">
                    @if($relatedProject->featured_image_url)
                        <img src="{{ $relatedProject->featured_image_url }}" 
                             alt="{{ $relatedProject->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    
                    @if($relatedProject->featured)
                    <div class="absolute top-3 left-3">
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                            Featured
                        </span>
                    </div>
                    @endif
                    
                    @if($relatedProject->category)
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/90 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                            {{ $relatedProject->category->name }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Project Content --}}
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
                        {{ $relatedProject->title }}
                    </h3>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        @if($relatedProject->service)
                        <span class="text-orange-600 font-medium">{{ $relatedProject->service->title }}</span>
                        @endif
                        @if($relatedProject->location)
                        <span class="mx-2">•</span>
                        <span>{{ $relatedProject->location }}</span>
                        @endif
                    </div>
                    
                    @if($relatedProject->short_description)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $relatedProject->short_description }}
                    </p>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        @if($relatedProject->year)
                        <span class="text-xs text-gray-500">
                            {{ $relatedProject->year }}
                        </span>
                        @endif
                        <a href="{{ route('portfolio.show', $relatedProject->slug) }}" 
                           class="inline-flex items-center text-orange-600 font-medium text-sm hover:text-orange-700 transition-colors">
                            View Project
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('portfolio.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                View All Projects
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Start Your Own Project?
        </h2>
        <p class="text-xl text-orange-100 mb-8">
            Let us help you achieve the same level of quality and success for your construction needs.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact.index', ['project' => $project->title]) }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                </svg>
                Get Free Consultation
            </a>
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                View Our Services
            </a>
        </div>
    </div>
</section>

{{-- Image Zoom Modal --}}
<div id="imageModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-full max-h-full">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button id="closeModal" class="absolute top-4 right-4 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div id="imageCounter" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/50 text-white px-4 py-2 rounded-full text-sm"></div>
        <button id="prevImage" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button id="nextImage" class="absolute right-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

{{-- JavaScript for Image Gallery and Interactions --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image Gallery Zoom Functionality
    const galleryItems = document.querySelectorAll('.gallery-item');
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const closeModal = document.getElementById('closeModal');
    const prevButton = document.getElementById('prevImage');
    const nextButton = document.getElementById('nextImage');
    const counter = document.getElementById('imageCounter');
    
    let currentImageIndex = 0;
    let images = [];
    
    // Collect all gallery images
    galleryItems.forEach((item, index) => {
        images.push({
            src: item.dataset.src,
            alt: item.dataset.alt
        });
        
        item.addEventListener('click', function() {
            currentImageIndex = index;
            openModal();
        });
    });
    
    function openModal() {
        if (images.length > 0) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            updateModalImage();
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeModalFunction() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    function updateModalImage() {
        if (images[currentImageIndex]) {
            modalImage.src = images[currentImageIndex].src;
            modalImage.alt = images[currentImageIndex].alt;
            counter.textContent = `${currentImageIndex + 1} / ${images.length}`;
            
            // Show/hide navigation buttons
            prevButton.style.display = images.length > 1 ? 'flex' : 'none';
            nextButton.style.display = images.length > 1 ? 'flex' : 'none';
        }
    }
    
    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        updateModalImage();
    }
    
    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        updateModalImage();
    }
    
    // Event listeners
    closeModal.addEventListener('click', closeModalFunction);
    nextButton.addEventListener('click', nextImage);
    prevButton.addEventListener('click', prevImage);
    
    // Close modal when clicking outside image
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModalFunction();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!modal.classList.contains('hidden')) {
            switch(e.key) {
                case 'Escape':
                    closeModalFunction();
                    break;
                case 'ArrowLeft':
                    if (images.length > 1) prevImage();
                    break;
                case 'ArrowRight':
                    if (images.length > 1) nextImage();
                    break;
            }
        }
    });
    
    // Milestone progress bar animation on scroll
    const progressBars = document.querySelectorAll('.bg-gradient-to-r');
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const progressObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const progressBar = entry.target;
                const width = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = width;
                }, 100);
                progressObserver.unobserve(progressBar);
            }
        });
    }, observerOptions);
    
    progressBars.forEach(bar => {
        if (bar.parentElement.parentElement.querySelector('.text-gray-600')?.textContent === 'Progress') {
            progressObserver.observe(bar);
        }
    });
});
</script>
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose p {
    margin-bottom: 1rem;
}

.prose p:last-child {
    margin-bottom: 0;
}

/* Image gallery hover effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
@endpush

</x-layouts.public>