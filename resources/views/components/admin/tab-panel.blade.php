<!-- resources/views/components/admin/tab-panel.blade.php -->
@props(['id'])

<div 
    x-show="activeTab === '{{ $id }}'"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    {{ $attributes }}
>
    {{ $slot }}
</div>