<!-- resources/views/components/project-details.blade.php -->
@props(['project'])

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
    <!-- Project Header -->
    <div class="relative">
        @if($project->getFeaturedImageUrlAttribute())
            <div class="h-64 md:h-80 overflow-hidden">
                <img src="{{ $project->getFeaturedImageUrlAttribute() }}" 
                     alt="{{ $project->title }}" 
                     class="w-full h-full object-cover">
            </div>
        @endif
        
        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black to-transparent p-6">
            <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $project->title }}</h1>
            
            <div class="flex flex-wrap items-center mt-2 text-white gap-4">
                @if($project->category)
                    <span class="flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ $project->category }}
                    </span>
                @endif
                
                @if($project->location)
                    <span class="flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $project->location }}
                    </span>
                @endif
                
                @if($project->year)
                    <span class="flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $project->year }}
                    </span>
                @endif
                
                @if($project->status)
                    <span class="flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ ucfirst($project->status) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Project Content -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="prose dark:prose-invert max-w-none">
                    {!! $project->description !!}
                </div>
                
                @if($project->challenge || $project->solution || $project->result)
                    <div class="mt-8 space-y-6">
                        @if($project->challenge)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">The Challenge</h3>
                                <div class="prose dark:prose-invert max-w-none">
                                    {!! $project->challenge !!}
                                </div>
                            </div>
                        @endif
                        
                        @if($project->solution)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Our Solution</h3>
                                <div class="prose dark:prose-invert max-w-none">
                                    {!! $project->solution !!}
                                </div>
                            </div>
                        @endif
                        
                        @if($project->result)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">The Result</h3>
                                <div class="prose dark:prose-invert max-w-none">
                                    {!! $project->result !!}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                
                <!-- Project Gallery -->
                @if($project->images && $project->images->count() > 0)
                    <div class="mt-10">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Project Gallery</h3>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($project->images as $image)
                                <a href="{{ asset('storage/' . $image->image_path) }}" class="block h-40 overflow-hidden rounded-lg" data-fslightbox="project-gallery">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         alt="{{ $image->alt_text ?? $project->title }}" 
                                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Project Documents -->
                @if(isset($project->files) && $project->files->where('is_public', true)->count() > 0)
                    <div class="mt-10">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Project Documents</h3>
                        
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($project->files->where('is_public', true) as $file)
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $file->file_name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ strtoupper(pathinfo($file->file_name, PATHINFO_EXTENSION)) }} · {{ number_format($file->file_size / 1024, 0) }} KB
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <a href="{{ route('download.file', $file->id) }}" class="text-sm text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-medium">
                                            Download
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="mt-6 lg:mt-0">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Project Details</h3>
                    
                    <ul class="space-y-4">
                        @if($project->client_name)
                            <li class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Client:</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $project->client_name }}</span>
                            </li>
                        @endif
                        
                        @if($project->start_date)
                            <li class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Start Date:</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $project->start_date->format('M d, Y') }}</span>
                            </li>
                        @endif
                        
                        @if($project->end_date)
                            <li class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Completion Date:</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $project->end_date->format('M d, Y') }}</span>
                            </li>
                        @endif
                        
                        @if($project->status)
                            <li class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ ucfirst($project->status) }}</span>
                            </li>
                        @endif
                        
                        @if($project->value)
                            <li class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Value:</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $project->value }}</span>
                            </li>
                        @endif
                        
                        @if($project->location)
                            <li class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Location:</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $project->location }}</span>
                            </li>
                        @endif
                    </ul>
                    
                    @if($project->services_used && is_array($project->services_used) && count($project->services_used) > 0)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Services Used</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->services_used as $service)
                                    <span class="inline-block bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300 text-xs px-2 py-1 rounded">
                                        {{ $service }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                @if(isset($project->client) && $project->client)
                    <div class="mt-6 bg-amber-50 dark:bg-amber-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-amber-800 dark:text-amber-300 mb-4">Client Testimonial</h3>
                        
                        @if($project->testimonials && $project->testimonials->where('is_active', true)->count() > 0)
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-200 dark:text-amber-800 absolute -top-2 -left-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z" />
                                </svg>
                                
                                <p class="text-amber-800 dark:text-amber-300 italic ml-6">{{ $project->testimonials->where('is_active', true)->first()->content }}</p>
                                
                                <div class="mt-4 flex items-center">
                                    @if($project->testimonials->where('is_active', true)->first()->image)
                                        <img src="{{ asset('storage/' . $project->testimonials->where('is_active', true)->first()->image) }}" 
                                             alt="{{ $project->testimonials->where('is_active', true)->first()->client_name }}" 
                                             class="h-10 w-10 rounded-full object-cover mr-3">
                                    @endif
                                    
                                    <div>
                                        <p class="text-sm font-medium text-amber-800 dark:text-amber-300">
                                            {{ $project->testimonials->where('is_active', true)->first()->client_name }}
                                        </p>
                                        @if($project->testimonials->where('is_active', true)->first()->client_position)
                                            <p class="text-xs text-amber-700 dark:text-amber-400">
                                                {{ $project->testimonials->where('is_active', true)->first()->client_position }}
                                                @if($project->testimonials->where('is_active', true)->first()->client_company)
                                                    , {{ $project->testimonials->where('is_active', true)->first()->client_company }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-amber-700 dark:text-amber-400">Would you like to share your experience working with us on this project? <a href="{{ route('testimonials.create', ['project_id' => $project->id]) }}" class="font-medium underline">Leave a testimonial</a></p>
                        @endif
                    </div>
                @endif
                
                <!-- Call to Action -->
                <div class="mt-6 bg-amber-600 text-white rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2">Interested in a similar project?</h3>
                    <p class="text-amber-100 mb-4">Contact us today to discuss your project requirements and get a free quote.</p>
                    <a href="{{ route('quotation.index') }}" class="block w-full bg-white text-amber-600 hover:bg-amber-100 text-center px-4 py-2 rounded-md font-medium transition">
                        Request a Quote
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Projects -->
    @if(isset($relatedProjects) && $relatedProjects->count() > 0)
        <div class="border-t border-gray-200 dark:border-gray-700 mt-10">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Related Projects</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedProjects as $relatedProject)
                        <x-project-card :project="$relatedProject" />
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fslightbox@3.3.1/index.min.js"></script>
@endpush