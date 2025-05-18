<!-- resources/views/components/admin/progress.blade.php -->
@props([
    'value' => 0, // Progress value from 0 to 100
    'max' => 100,
    'height' => 'md', // Options: xs, sm, md, lg
    'color' => 'blue', // Options: blue, green, red, amber, gray
    'striped' => false,
    'animated' => false,
    'showLabel' => false,
    'labelPosition' => 'inside', // Options: inside, outside-right, outside-left
    'rounded' => true
])

@php
    // Calculate percentage
    $percentage = min(100, max(0, ($value / $max) * 100));
    
    // Height classes
    $heightClasses = [
        'xs' => 'h-1',
        'sm' => 'h-1.5',
        'md' => 'h-2.5',
        'lg' => 'h-4'
    ][$height] ?? 'h-2.5';
    
    // Color classes
    $colorClasses = [
        'blue' => 'bg-blue-600 dark:bg-blue-500',
        'green' => 'bg-green-600 dark:bg-green-500',
        'red' => 'bg-red-600 dark:bg-red-500',
        'amber' => 'bg-amber-600 dark:bg-amber-500',
        'gray' => 'bg-gray-600 dark:bg-gray-500'
    ][$color] ?? 'bg-blue-600 dark:bg-blue-500';
    
    // Rounded classes
    $roundedClasses = $rounded ? 'rounded-full' : 'rounded-none';
    
    // Striped classes
    $stripedClasses = $striped ? 'bg-gradient-to-r from-transparent via-white/20 to-transparent bg-[length:1rem_1rem]' : '';
    
    // Animated classes
    $animatedClasses = $animated && $striped ? 'animate-[progress-bar-stripes_1s_linear_infinite]' : '';
    
    // Label classes
    $labelClasses = '';
    if ($showLabel && $labelPosition === 'inside') {
        $labelClasses = 'flex items-center justify-center text-xs font-medium text-white';
    }
@endphp

<div class="w-full bg-gray-200 {{ $roundedClasses }} dark:bg-neutral-700">
    @if($showLabel && $labelPosition === 'outside-left')
        <div class="flex items-center gap-2 mb-1">
            <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">{{ $percentage }}%</span>
        </div>
    @endif
    
    <div class="flex w-full items-center">
        <div 
            class="{{ $heightClasses }} {{ $roundedClasses }} {{ $colorClasses }} {{ $stripedClasses }} {{ $animatedClasses }} {{ $labelClasses }}" 
            style="width: {{ $percentage }}%"
            role="progressbar" 
            aria-valuenow="{{ $value }}" 
            aria-valuemin="0" 
            aria-valuemax="{{ $max }}"
        >
            @if($showLabel && $labelPosition === 'inside')
                {{ $percentage }}%
            @endif
        </div>
        
        @if($showLabel && $labelPosition === 'outside-right')
            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-neutral-300">{{ $percentage }}%</span>
        @endif
    </div>
</div>