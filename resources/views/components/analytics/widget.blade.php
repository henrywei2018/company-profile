{{-- resources/views/components/analytics/widget.blade.php --}}
<div class="analytics-widget {{ $getSizeClasses() }}" 
     id="{{ $widgetId }}"
     data-widget-type="{{ $dataType }}"
     data-widget-url="{{ $getWidgetUrl() }}"
     data-period="{{ $period }}"
     data-auto-refresh="{{ $autoRefresh ? 'true' : 'false' }}">
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 h-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            <div class="flex items-center space-x-2">
                @if($autoRefresh)
                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></div>
                        Live
                    </div>
                @endif
                <button class="widget-refresh text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" 
                        data-widget-id="{{ $widgetId }}">
                    <x-icon name="refresh" class="w-4 h-4" />
                </button>
            </div>
        </div>
        
        <div class="widget-content" id="{{ $widgetId }}-content">
            <div class="flex items-center justify-center h-32">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Loading...</span>
            </div>
        </div>
    </div>
</div>