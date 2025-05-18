<!-- resources/views/components/admin/badge.blade.php -->
@props([
    'type' => 'default', // Options: default, success, warning, danger, info, primary
    'size' => 'md', // Options: sm, md, lg
    'rounded' => true, // Whether to use rounded-full or rounded-md
    'dot' => false, // Whether to show a status dot
    'outline' => false, // Whether to use outline style
    'soft' => false // Whether to use soft style
])

@php
    // Size classes
    $sizeClasses = [
        'sm' => 'px-1.5 py-0.5 text-xs',
        'md' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm'
    ][$size] ?? 'px-2.5 py-0.5 text-xs';
    
    // Rounded classes
    $roundedClasses = $rounded ? 'rounded-full' : 'rounded-md';
    
    // Define color classes based on type and variant
    $typeColorClasses = [
        'default' => [
            'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            'outline' => 'border border-gray-500 text-gray-700 dark:border-gray-400 dark:text-gray-300',
            'soft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700/50 dark:text-gray-300'
        ],
        'primary' => [
            'default' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'outline' => 'border border-blue-500 text-blue-700 dark:border-blue-500 dark:text-blue-400',
            'soft' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400'
        ],
        'success' => [
            'default' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'outline' => 'border border-green-500 text-green-700 dark:border-green-500 dark:text-green-400',
            'soft' => 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400'
        ],
        'warning' => [
            'default' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
            'outline' => 'border border-amber-500 text-amber-700 dark:border-amber-500 dark:text-amber-400',
            'soft' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400'
        ],
        'danger' => [
            'default' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'outline' => 'border border-red-500 text-red-700 dark:border-red-500 dark:text-red-400',
            'soft' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400'
        ],
        'info' => [
            'default' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
            'outline' => 'border border-sky-500 text-sky-700 dark:border-sky-500 dark:text-sky-400',
            'soft' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/20 dark:text-sky-400'
        ]
    ];
    
    // Get the correct variant based on props
    $variant = $outline ? 'outline' : ($soft ? 'soft' : 'default');
    
    // Get the color classes for the badge
    $colorClasses = $typeColorClasses[$type][$variant] ?? $typeColorClasses['default'][$variant];
    
    // Status dot colors
    $dotColors = [
        'default' => 'bg-gray-400 dark:bg-gray-400',
        'primary' => 'bg-blue-500 dark:bg-blue-400',
        'success' => 'bg-green-500 dark:bg-green-400',
        'warning' => 'bg-amber-500 dark:bg-amber-400',
        'danger' => 'bg-red-500 dark:bg-red-400',
        'info' => 'bg-sky-500 dark:bg-sky-400'
    ][$type] ?? 'bg-gray-400 dark:bg-gray-400';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium $colorClasses $sizeClasses $roundedClasses"]) }}>
    @if($dot)
    <span class=\"shrink-0 size-1.5 {{ $dotColors }} rounded-full mr-1.5\"></span>
    @endif
    {{ $slot }}
</span>"
  }