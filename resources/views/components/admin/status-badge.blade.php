{{-- resources/views/components/admin/status-badge.blade.php --}}
@props([
    'status',
    'size' => 'sm', // xs, sm, md, lg
    'statusMap' => []
])

@php
$defaultStatusMap = [
    'active' => [
        'label' => 'Active',
        'class' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
    ],
    'inactive' => [
        'label' => 'Inactive',
        'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
    ],
    'pending' => [
        'label' => 'Pending',
        'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
    ],
    'published' => [
        'label' => 'Published',
        'class' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
    ],
    'draft' => [
        'label' => 'Draft',
        'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
    ],
    'archived' => [
        'label' => 'Archived',
        'class' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
    ],
    'scheduled' => [
        'label' => 'Scheduled',
        'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
    ],
    'expired' => [
        'label' => 'Expired',
        'class' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
    ],
    'live' => [
        'label' => 'Live',
        'class' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
    ]
];

$mergedStatusMap = array_merge($defaultStatusMap, $statusMap);
$statusConfig = $mergedStatusMap[$status] ?? [
    'label' => ucfirst($status),
    'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
];

$sizeClasses = [
    'xs' => 'px-1.5 py-0.5 text-xs',
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-sm',
    'lg' => 'px-3 py-1.5 text-base'
];

$sizeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

<span class="inline-flex items-center {{ $sizeClass }} rounded-full font-medium {{ $statusConfig['class'] }}">
    {{ $statusConfig['label'] }}
</span>