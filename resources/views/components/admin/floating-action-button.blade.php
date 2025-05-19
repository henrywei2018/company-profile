<!-- resources/views/components/floating-action-button.blade.php -->
@props(['actions' => []])

<div 
    x-data="{ open: false }" 
    class="fixed bottom-3 right-3 z-50"
    @keydown.escape.window="open = false"
>
    <!-- Main trigger button -->
    <button 
        @click="open = !open" 
        class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-105 dark:bg-blue-700 dark:hover:bg-blue-800"
        aria-label="Quick Actions"
    >
        <svg x-show="!open" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        <svg x-show="open" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Action buttons -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        class="absolute bottom-12 right-0 mb-1 flex flex-col-reverse items-end space-y-1 space-y-reverse"
        @click.away="open = false"
    >
        @foreach($actions as $action)
            <div class="flex items-center space-x-1 mb-1">
                <!-- Label appears on hover -->
                <div 
                    class="bg-gray-800 text-white text-xs font-medium px-2 py-1 rounded-md shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap dark:bg-gray-700"
                >
                    {{ $action['title'] }}
                </div>
                
                <!-- Action button -->
                <a 
                    href="{{ $action['href'] }}" 
                    class="group flex items-center justify-center w-9 h-9 rounded-full text-white shadow-md transition-all duration-300 transform hover:scale-110 {{ $action['color_classes'] }}"
                    title="{{ $action['title'] }}"
                >
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $action['icon'] !!}
                    </svg>
                </a>
            </div>
        @endforeach
    </div>
</div>