{{-- resources/views/components/public/project-card.blade.php --}}
@props(['project'])

<div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
    <div class="relative h-64 overflow-hidden">
        @if($project->featured_image)
        <img src="{{ asset('storage/' . $project->featured_image) }}" 
             alt="{{ $project->title }}" 
             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        
        {{-- Project Category Badge --}}
        @if($project->category)
        <div class="absolute top-4 left-4">
            <span class="bg-orange-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                {{ $project->category->name }}
            </span>
        </div>
        @endif
        
        {{-- Featured Badge --}}
        @if($project->featured)
        <div class="absolute top-4 right-4">
            <span class="bg-amber-500 text-white px-2 py-1 rounded text-xs font-medium">
                Featured
            </span>
        </div>
        @endif
    </div>
    
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors duration-300">
            {{ $project->title }}
        </h3>
        <p class="text-gray-600 mb-4 line-clamp-2">
            {{ $project->description }}
        </p>
        
        {{-- Project Details --}}
        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
            @if($project->location)
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ Str::limit($project->location, 20) }}
            </span>
            @endif
            
            @if($project->end_date)
            <span>{{ $project->end_date->format('M Y') }}</span>
            @endif
        </div>
        
        <div class="flex items-center justify-between">
            <a href="{{ route('portfolio.show', $project->slug) }}" 
               class="inline-flex items-center text-orange-600 font-semibold hover:text-orange-700 transition-colors duration-300">
                View Project
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            
            @if($project->status)
            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                {{ ucfirst($project->status) }}
            </span>
            @endif
        </div>
    </div>
</div>