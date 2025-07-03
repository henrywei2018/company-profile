{{-- resources/views/components/admin/message-date.blade.php --}}

@props(['date'])

<div class="flex flex-col">
    <span>{{ $date->format('M j, Y') }}</span>
    <span class="text-xs">{{ $date->format('g:i A') }}</span>
</div>