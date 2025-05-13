<!-- resources/views/components/project-card.blade.php -->
@props(['project', 'featured' => false])

<div {{ $attributes->merge(['class' => 'group bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl ' . ($featured ? 'col-span-2 row-span-2' : '')]) }}>
    <div class="relative overflow-hidden {{ $featured ? 'h-80' : 'h-60' }}">
        <img 
            src="{{ $project->getFeaturedImageUrlAttribute() }}" 
            alt="{{ $project->title }}" 
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        >
        <div class="absolute inset-0 bg-black bg-opacity-40 transition-opacity duration-300 opacity-0 group-hover:opacity-100 flex items-center justify-center">
            <a href="{{ route('portfolio.show', $project->slug) }}" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-all transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 duration-300">
                View Project
            </a>
        </div>
        @if($project->featured)
            <div class="absolute top-4 left-4 bg-yellow-500 text-white px-3 py-1 text-xs font-bold rounded-full">
                Featured
            </div>
        @endif
        @if($project->category)
            <div class="absolute bottom-4 left-4 bg-gray-900 bg-opacity-70 text-white px-3 py-1 text-xs font-medium rounded-full">
                {{ $project->category }}
            </div>
        @endif
    </div>
    <div class="p-6">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                {{ $project->title }}
            </h3>
            @if($project->year)
                <span class="text-sm text-gray-500 font-medium">{{ $project->year }}</span>
            @endif
        </div>
        <p class="text-gray-600 text-sm mb-4">
            {{ Str::limit(strip_tags($project->description), $featured ? 200 : 100) }}
        </p>
        <div class="flex justify-between items-center">
            @if($project->location)
                <div class="flex items-center text-sm text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $project->location }}
                </div>
            @endif
            <a href="{{ route('portfolio.show', $project->slug) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors flex items-center">
                Learn More
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>
</div>