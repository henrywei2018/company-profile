<!-- resources/views/components/service-card.blade.php -->
@props(['service'])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl h-full flex flex-col']) }}>
    <div class="relative overflow-hidden h-48">
        @if($service->image)
            <img 
                src="{{ asset('storage/' . $service->image) }}" 
                alt="{{ $service->title }}" 
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            >
        @else
            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                @if($service->icon)
                    <i class="{{ $service->icon }} text-5xl text-gray-500 dark:text-gray-400"></i>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                @endif
            </div>
        @endif
        
        @if($service->featured)
            <div class="absolute top-4 right-4 bg-amber-500 text-white px-3 py-1 text-xs font-bold rounded-full">
                Featured
            </div>
        @endif
    </div>
    
    <div class="p-6 flex-grow flex flex-col">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
            {{ $service->title }}
        </h3>
        
        <p class="text-gray-600 dark:text-gray-300 mb-4 flex-grow">
            {{ $service->short_description ?? Str::limit(strip_tags($service->description), 150) }}
        </p>
        
        <a href="{{ route('services.show', $service->slug) }}" class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-medium inline-flex items-center mt-2">
            Learn More
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </a>
    </div>
</div>