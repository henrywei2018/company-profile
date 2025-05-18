<!-- resources/views/components/admin/toast.blade.php -->
@props([
    'type' => 'info', // Options: info, success, warning, danger, dark
    'title' => null,
    'autoDismiss' => true,
    'dismissible' => true,
    'dismissAfter' => 5000, // milliseconds
    'position' => 'bottom-right', // Options: top-left, top-right, bottom-left, bottom-right
    'icon' => true,
])

@php
    // Type classes
    $typeClasses = [
        'info' => 'bg-blue-50 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800',
        'success' => 'bg-green-50 border-green-200 dark:bg-green-900/30 dark:border-green-800',
        'warning' => 'bg-amber-50 border-amber-200 dark:bg-amber-900/30 dark:border-amber-800',
        'danger' => 'bg-red-50 border-red-200 dark:bg-red-900/30 dark:border-red-800',
        'dark' => 'bg-gray-800 border-gray-700 dark:bg-neutral-900 dark:border-neutral-800'
    ][$type] ?? 'bg-blue-50 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800';
    
    // Text classes
    $textClasses = [
        'info' => 'text-blue-800 dark:text-blue-400',
        'success' => 'text-green-800 dark:text-green-400',
        'warning' => 'text-amber-800 dark:text-amber-400',
        'danger' => 'text-red-800 dark:text-red-400',
        'dark' => 'text-white dark:text-gray-300'
    ][$type] ?? 'text-blue-800 dark:text-blue-400';
    
    // Icon based on type
    $typeIcons = [
        'info' => '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'success' => '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'warning' => '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
        'danger' => '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'dark' => '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
    ][$type] ?? '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
    
    // Position classes
    $positionClasses = [
        'top-left' => 'top-4 left-4',
        'top-right' => 'top-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'bottom-right' => 'bottom-4 right-4'
    ][$position] ?? 'bottom-4 right-4';
@endphp

<div 
    x-data="{
        show: true,
        init() {
            @if($autoDismiss)
            setTimeout(() => { this.show = false }, {{ $dismissAfter }});
            @endif
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    {{ $attributes->merge(['class' => 'fixed z-50 max-w-sm w-full shadow-lg rounded-lg border overflow-hidden ' . $positionClasses . ' ' . $typeClasses]) }}
    role="alert"
>
    <div class="relative overflow-hidden">
        <!-- Progress bar for auto dismiss -->
        @if($autoDismiss)
        <div 
            class="absolute top-0 left-0 h-1 bg-white bg-opacity-30 dark:bg-white dark:bg-opacity-20"
            style="width: 100%;"
            x-ref="progressBar"
            x-init="$nextTick(() => {
                $refs.progressBar.style.transition = `width {{ $dismissAfter }}ms linear`;
                setTimeout(() => { $refs.progressBar.style.width = '0%' }, 50);
            })"
        ></div>
        @endif
        
        <div class="p-4 sm:p-5">
            <div class="flex items-start">
                @if($icon)
                <div class="shrink-0 {{ $textClasses }}">
                    {!! $typeIcons !!}
                </div>
                @endif
                
                <div class="ml-3 w-full">
                    @if($title)
                    <h3 class="text-sm font-medium {{ $textClasses }}">
                        {{ $title }}
                    </h3>
                    @endif
                    
                    <div class="mt-1 text-sm {{ $textClasses }}">
                        {{ $slot }}
                    </div>
                </div>
                
                @if($dismissible)
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button 
                            @click="show = false"
                            type="button" 
                            class="inline-flex rounded-md p-1.5 {{ $textClasses }} hover:bg-opacity-10 hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{$type}}-500 dark:focus:ring-offset-gray-800"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>