<!-- resources/views/components/post-card.blade.php -->
@props(['post', 'featured' => false])

<article {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl h-full flex flex-col' . ($featured ? ' md:col-span-2' : '')]) }}>
    <div class="relative overflow-hidden {{ $featured ? 'h-64' : 'h-48' }}">
        @if($post->featured_image)
            <img 
                src="{{ asset('storage/' . $post->featured_image) }}" 
                alt="{{ $post->title }}" 
                class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
            >
        @else
            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
        @endif
        
        @if($post->featured)
            <div class="absolute top-4 left-4 bg-amber-500 text-white px-3 py-1 text-xs font-bold rounded-full">
                Featured
            </div>
        @endif
        
        @if($post->published_at)
            <div class="absolute bottom-4 right-4 bg-gray-900 bg-opacity-70 text-white px-3 py-1 text-xs font-medium rounded-full">
                {{ $post->published_at->format('M d, Y') }}
            </div>
        @endif
    </div>
    
    <div class="p-6 flex-grow flex flex-col">
        <div class="mb-3">
            @foreach($post->categories as $category)
                <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded mr-2 mb-2">
                    {{ $category->name }}
                </span>
            @endforeach
        </div>
        
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
            {{ $post->title }}
        </h3>
        
        <p class="text-gray-600 dark:text-gray-300 mb-4 flex-grow">
            {{ $post->excerpt ?? Str::limit(strip_tags($post->content), $featured ? 200 : 120) }}
        </p>
        
        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center">
                @if($post->user && $post->user->avatar)
                    <img src="{{ asset('storage/' . $post->user->avatar) }}" alt="{{ $post->user->name }}" class="h-8 w-8 rounded-full mr-2">
                @endif
                <span class="text-sm text-gray-500 dark:text-gray-400">By {{ $post->user->name ?? 'Admin' }}</span>
            </div>
            
            <a href="{{ route('blog.show', $post->slug) }}" class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-medium inline-flex items-center">
                Read More
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>
</article>