{{-- resources/views/components/admin/message-priority.blade.php --}}

@props(['priority' => 'normal'])

@php
$priorityConfig = [
    'normal' => ['color' => 'blue', 'text' => 'Normal'],
    'urgent' => ['color' => 'red', 'text' => 'Urgent']
];
$config = $priorityConfig[$priority] ?? $priorityConfig['normal'];
@endphp

<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 dark:bg-{{ $config['color'] }}-900 dark:text-{{ $config['color'] }}-200">
    <span class="w-2 h-2 mr-1 bg-{{ $config['color'] }}-400 rounded-full"></span>
    {{ $config['text'] }}
</span>