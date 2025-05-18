<!-- resources/views/components/admin/icon-button.blade.php -->
@props([
    'type' => 'button',
    'color' => 'primary', // Options: primary, success, danger, warning, light, dark
    'size' => 'md', // Options: xs, sm, md, lg
    'rounded' => true, // Whether to use rounded-full or rounded-md
    'disabled' => false,
    'href' => null,
    'tooltip' => null,
    'tooltipPosition' => 'top' // Options: top, bottom, left, right
])

@php
    // Size classes
    $sizeClasses = [
        'xs' => 'p-1 size-7',
        'sm' => 'p-1.5 size-8',
        'md' => 'p-2 size-10',
        'lg' => 'p-2.5 size-12'
    ][$size] ?? 'p-2 size-10';
    
    // Icon size classes
    $iconSizeClasses = [
        'xs' => 'size-3',
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6'
    ][$size] ?? 'size-5';
    
    // Rounded classes
    $roundedClasses = $rounded ? 'rounded-full' : 'rounded-md';
    
    // Define color classes
    $colorClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'warning' => 'bg-amber-600 hover:bg-amber-700 text-white focus:ring-amber-500',
        'light' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 focus:ring-gray-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700',
        'dark' => 'bg-gray-800 hover:bg-gray-900 text-white focus:ring-gray-500 dark:bg-neutral-900 dark:hover:bg-black'
    ][$color] ?? 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500';
    
    // Disabled classes
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
    
    // Final classes
    $classes = "inline-flex flex-shrink-0 justify-center items-center $sizeClasses $roundedClasses $colorClasses focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition-colors $disabledClasses";
@endphp

@if($href && !$disabled)
    <a 
        href="{{ $href }}" 
        {{ $attributes->merge(['class' => $classes]) }}
        @if($tooltip) data-hs-tooltip="{{ $tooltip }}" data-hs-tooltip-placement="{{ $tooltipPosition }}" @endif
    >
        <span class="{{ $iconSizeClasses }}">
            {{ $slot }}
        </span>
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
        @if($tooltip && !$disabled) data-hs-tooltip="{{ $tooltip }}" data-hs-tooltip-placement="{{ $tooltipPosition }}" @endif
    >
        <span class="{{ $iconSizeClasses }}">
            {{ $slot }}
        </span>
    </button>
@endif