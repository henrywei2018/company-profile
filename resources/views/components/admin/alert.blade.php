<!-- resources/views/components/admin/alert.blade.php -->
@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
    'icon' => true,
])

@php
    $typeClasses = [
        'info' => 'bg-blue-50 border-blue-300 text-blue-800 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400',
        'success' => 'bg-green-50 border-green-300 text-green-800 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400',
        'warning' => 'bg-amber-50 border-amber-300 text-amber-800 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400',
        'danger' => 'bg-red-50 border-red-300 text-red-800 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400',
        'error' => 'bg-red-50 border-red-300 text-red-800 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400', // Added error type
    ];
    
    $typeIcons = [
        'info' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
        'success' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        'warning' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        'danger' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        'error' => '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>', // Added error icon (same as danger)
    ];

    // Get classes with fallback to info if type not found
    $alertClasses = $typeClasses[$type] ?? $typeClasses['info'];
    $alertIcon = $typeIcons[$type] ?? $typeIcons['info'];
@endphp

<div 
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->merge(['class' => 'p-4 border rounded-lg ' . $alertClasses]) }}
    role="alert"
>
    <div class="flex">
        @if($icon)
        <div class="flex-shrink-0">
            {!! $alertIcon !!}
        </div>
        @endif

        <div class="ml-3">
            @if($title)
            <h3 class="text-sm font-medium">
                {{ $title }}
            </h3>
            @endif
            
            <div class="text-sm {{ $title ? 'mt-2' : '' }}">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button 
                    @click="show = false"
                    type="button" 
                    class="inline-flex p-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $alertClasses }}"
                >
                    <span class="sr-only">Dismiss</span>
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        @endif
    </div>
</div>