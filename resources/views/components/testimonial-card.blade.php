@props(['testimonial'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-6 relative">
    <!-- Quote icon -->
    <div class="absolute top-4 right-4 opacity-10 text-amber-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24">
            <path d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z" />
        </svg>
    </div>
    
    <!-- Testimonial content -->
    <div class="mb-4">
        <p class="text-gray-700 dark:text-gray-300 italic relative z-10">
            {{ Str::limit($testimonial->content, 250) }}
        </p>
    </div>
    
    <!-- Rating stars -->
    <div class="flex mb-4">
        @for($i = 1; $i <= 5; $i++)
            @if($i <= $testimonial->rating)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300 dark:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @endif
        @endfor
    </div>
    
    <!-- Author info -->
    <div class="flex items-center">
        @if($testimonial->image)
            <div class="flex-shrink-0 mr-4">
                <img src="{{ asset('storage/' . $testimonial->image) }}" 
                     alt="{{ $testimonial->client_name }}" 
                     class="h-12 w-12 rounded-full object-cover">
            </div>
        @endif
        
        <div>
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $testimonial->client_name }}
            </h4>
            
            @if($testimonial->client_position || $testimonial->client_company)
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    @if($testimonial->client_position)
                        {{ $testimonial->client_position }}
                        @if($testimonial->client_company)
                            , 
                        @endif
                    @endif
                    
                    @if($testimonial->client_company)
                        {{ $testimonial->client_company }}
                    @endif
                </p>
            @endif
        </div>
        
        @if($testimonial->project)
            <div class="ml-auto">
                <a href="{{ route('portfolio.show', $testimonial->project->slug) }}" 
                   class="text-xs text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 transition flex items-center">
                    <span>View Project</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>