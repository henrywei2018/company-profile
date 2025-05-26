<!-- resources/views/components/admin/avatar.blade.php -->
@props([
    'src' => null,
    'alt' => 'Avatar',
    'size' => 'md', // Options: xs, sm, md, lg, xl
    'rounded' => true, // Whether to use rounded-full or rounded-md
    'status' => null, // Options: online, away, busy, offline
    'placeholder' => null, // Text to use for initials placeholder
    'placeholderBg' => 'bg-blue-600' // Background color for placeholder
])

@php
    // Size classes
    $sizeClasses = [
        'xs' => 'size-6',
        'sm' => 'size-8',
        'md' => 'size-10',
        'lg' => 'size-12',
        'xl' => 'size-16'
    ][$size] ?? 'size-10';
    
    // Status indicator size
    $statusSizeClasses = [
        'xs' => 'size-1.5',
        'sm' => 'size-2',
        'md' => 'size-2.5',
        'lg' => 'size-3',
        'xl' => 'size-3.5'
    ][$size] ?? 'size-2.5';
    
    // Status colors
    $statusColorClasses = [
        'online' => 'bg-green-500',
        'away' => 'bg-amber-500',
        'busy' => 'bg-red-500',
        'offline' => 'bg-gray-400'
    ][$status] ?? 'bg-gray-400';
    
    // Rounded classes
    $roundedClasses = $rounded ? 'rounded-full' : 'rounded-md';
    
    // Generate initials for placeholder
    $initials = '';
    if ($placeholder) {
        $words = explode(' ', $placeholder);
        $initials = strlen($words[0]) ? substr($words[0], 0, 1) : '';
        
        if (count($words) > 1 && strlen($words[1])) {
            $initials .= substr($words[1], 0, 1);
        }
        
        $initials = strtoupper($initials);
    }
    
    // Font size for initials
    $initialsFontSize = [
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl'
    ][$size] ?? 'text-base';
@endphp

<div class="relative inline-flex">
    @if($src)
        <img 
            src="{{ $src }}" 
            alt="{{ $alt }}" 
            {{ $attributes->merge(['class' => "object-cover $sizeClasses $roundedClasses"]) }}
        >
    @else
        <div 
            {{ $attributes->merge(['class' => "inline-flex items-center justify-center $sizeClasses $roundedClasses $placeholderBg text-white $initialsFontSize font-medium"]) }}
        >
            {{ $initials ?: substr($alt, 0, 1) }}
        </div>
    @endif
    
    @if($status)
        <span class="absolute bottom-0 right-0 block rounded-full ring-2 ring-white dark:ring-neutral-800 {{ $statusSizeClasses }} {{ $statusColorClasses }}"></span>
    @endif
</div>