<!-- resources/views/components/toast.blade.php -->
@props(['type' => 'info', 'title' => null, 'dismissible' => true, 'timeout' => 5000])

@php
    $typeClasses = [
        'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
        'success' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
        'error' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
    ];
    
    $iconPath = [
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ];
@endphp

<div 
    x-data="{ 
        show: false,
        timeout: null,
        init() {
            this.show = true;
            if (@js($timeout) > 0) {
                this.timeout = setTimeout(() => {
                    this.show = false;
                }, @js($timeout));
            }
        },
        close() {
            this.show = false;
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
        }
    }" 
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->merge(['class' => 'fixed top-4 right-4 z-50 max-w-sm rounded-lg shadow-lg overflow-hidden '. $typeClasses[$type]]) }}
    role="alert"
>
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $iconPath[$type] !!}
                </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                @if($title)
                    <p class="text-sm font-medium">{{ $title }}</p>
                @endif
                <div class="text-sm">
                    {{ $slot }}
                </div>
            </div>
            @if($dismissible)
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="close()" type="button" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>