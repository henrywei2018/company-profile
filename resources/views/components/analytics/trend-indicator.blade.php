{{-- resources/views/components/analytics/trend-indicator.blade.php --}}
<div class="inline-flex items-center space-x-1">
    @if($showIcon)
        <x-icon name="{{ $getTrendIcon() }}" class="w-4 h-4 {{ $getTrendClass() }}" />
    @endif
    <span class="font-medium {{ $getTrendClass() }}">{{ $getFormattedValue() }}</span>
    <span class="text-xs text-gray-500">{{ $period }}</span>
</div>