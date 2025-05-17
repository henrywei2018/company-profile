<!-- resources/views/components/status-badge.blade.php -->
@props(['status', 'statusMap' => []])

@php
    // Default status colors
    $colors = [
        'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'in_progress' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'published' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'archived' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'on_hold' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'featured' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];
    
    // Merge with custom status map
    $colors = array_merge($colors, $statusMap);
    
    // Convert snake_case or dash-case to Title Case for display
    $displayStatus = ucwords(str_replace(['_', '-'], ' ', $status));
    
    // Get the appropriate color class for the status
    $colorClass = $colors[$status] ?? $colors['default'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium ' . $colorClass]) }}>
    {{ $displayStatus }}
</span>