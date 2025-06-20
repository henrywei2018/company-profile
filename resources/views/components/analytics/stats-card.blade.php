
{{-- resources/views/components/analytics/stats-card.blade.php --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $title }}</h3>
            <div class="flex items-center space-x-2">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
                @if($trend)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $getTrendClass() }}">
                        @if($showIcon ?? true)
                            <x-icon name="{{ $getTrendIcon() }}" class="w-3 h-3 mr-1" />
                        @endif
                        {{ $trend }}
                    </span>
                @endif
            </div>
            @if($subtitle)
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="text-{{ $color }}-600 dark:text-{{ $color }}-400">
            <x-icon name="{{ $icon }}" class="w-8 h-8" />
        </div>
    </div>
</div>