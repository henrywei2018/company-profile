<!-- resources/views/components/admin/button.blade.php -->
@props([
    'type' => 'button',
    'color' => 'primary',
    'size' => 'md', 
    'icon' => null,
    'iconPosition' => 'left',
    'disabled' => false,
    'loading' => false, 
    'href' => null,
    'target' => null,
    'pill' => false,
    'outline' => false,
    'soft' => false
])

@php
    // Safely handle all props to ensure they're strings
    $type = is_string($type) ? trim($type) : 'button';
    $color = is_string($color) ? trim($color) : 'primary';
    $size = is_string($size) ? trim($size) : 'md';
    $iconPosition = is_string($iconPosition) ? trim($iconPosition) : 'left';
    
    // Convert boolean values safely
    $disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    $loading = filter_var($loading, FILTER_VALIDATE_BOOLEAN);
    $pill = filter_var($pill, FILTER_VALIDATE_BOOLEAN);
    $outline = filter_var($outline, FILTER_VALIDATE_BOOLEAN);
    $soft = filter_var($soft, FILTER_VALIDATE_BOOLEAN);
    
    // Safely handle href and target
    $href = is_string($href) ? trim($href) : null;
    $target = is_string($target) ? trim($target) : null;
    
    // Base classes
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-semibold transition-all';

    // Size classes
    $sizeClasses = [
        'xs' => 'py-1 px-2 text-xs',
        'sm' => 'py-1.5 px-3 text-sm',
        'md' => 'py-2 px-4 text-sm',
        'lg' => 'py-3 px-5 text-base',
        'xl' => 'py-3.5 px-6 text-base'
    ][$size] ?? 'py-2 px-4 text-sm';

    // Rounded classes
    $roundedClasses = $pill ? 'rounded-full' : 'rounded-md';

    // Define color classes based on variants
    $colorClasses = [
        'primary' => [
            'default' => 'border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'outline' => 'border border-blue-600 text-blue-600 hover:text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'soft' => 'border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/40'
        ],
        'success' => [
            'default' => 'border border-transparent bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'outline' => 'border border-green-600 text-green-600 hover:text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'soft' => 'border border-transparent bg-green-100 text-green-800 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/40'
        ],
        'danger' => [
            'default' => 'border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'outline' => 'border border-red-600 text-red-600 hover:text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'soft' => 'border border-transparent bg-red-100 text-red-800 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/40'
        ],
        'warning' => [
            'default' => 'border border-transparent bg-amber-600 text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'outline' => 'border border-amber-600 text-amber-600 hover:text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'soft' => 'border border-transparent bg-amber-100 text-amber-800 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/40'
        ],
        'info' => [
            'default' => 'border border-transparent bg-sky-600 text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'outline' => 'border border-sky-600 text-sky-600 hover:text-white hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800',
            'soft' => 'border border-transparent bg-sky-100 text-sky-800 hover:bg-sky-200 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 dark:bg-sky-900/30 dark:text-sky-400 dark:hover:bg-sky-900/40'
        ],
        'light' => [
            'default' => 'border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:ring-neutral-600',
            'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:ring-neutral-600',
            'soft' => 'border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:ring-neutral-600'
        ],
        'dark' => [
            'default' => 'border border-gray-800 bg-gray-800 text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 dark:border-neutral-900 dark:bg-neutral-900 dark:hover:bg-neutral-800',
            'outline' => 'border border-gray-800 text-gray-800 hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:ring-neutral-600',
            'soft' => 'border border-transparent bg-gray-800/20 text-gray-800 hover:bg-gray-800/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 dark:bg-neutral-900/30 dark:text-neutral-400 dark:hover:bg-neutral-900/40 dark:focus:ring-neutral-700'
        ],
    ];

    // Get the correct variant based on props
    $variant = $outline ? 'outline' : ($soft ? 'soft' : 'default');
    
    // Combine all classes - ensure we have valid color and variant
    $selectedColor = $colorClasses[$color] ?? $colorClasses['primary'];
    $selectedVariant = $selectedColor[$variant] ?? $selectedColor['default'];
    
    $classes = implode(' ', [
        $baseClasses,
        $sizeClasses,
        $roundedClasses,
        $selectedVariant
    ]);
    
    // Add disabled and loading states
    if ($disabled) {
        $classes .= ' opacity-50 pointer-events-none';
    }
    
    if ($loading) {
        $classes .= ' relative cursor-wait';
    }
@endphp

@if($href)
    <a href="{{ $href }}" 
       @if($target) target="{{ $target }}" @endif 
       {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <span class="absolute inset-0 flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            <span class="opacity-0">{{ $slot }}</span>
        @else
            @if($icon && $iconPosition === 'left')
                <span class="shrink-0">{!! $icon !!}</span>
            @endif
            <span>{{ $slot }}</span>
            @if($icon && $iconPosition === 'right')
                <span class="shrink-0">{!! $icon !!}</span>
            @endif
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <span class="absolute inset-0 flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            <span class="opacity-0">{{ $slot }}</span>
        @else
            @if($icon && $iconPosition === 'left')
                <span class="shrink-0">{!! $icon !!}</span>
            @endif
            <span>{{ $slot }}</span>
            @if($icon && $iconPosition === 'right')
                <span class="shrink-0">{!! $icon !!}</span>
            @endif
        @endif
    </button>
@endif