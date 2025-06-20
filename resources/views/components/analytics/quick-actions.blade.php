{{-- resources/views/components/analytics/quick-actions.blade.php --}}
<div class="flex items-center space-x-2">
    @foreach($actions as $action)
        <button class="quick-action-btn inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                data-action="{{ $action['action'] }}"
                data-color="{{ $action['color'] }}">
            <x-icon name="{{ $action['icon'] }}" class="w-4 h-4 mr-1.5" />
            {{ $action['label'] }}
        </button>
    @endforeach
</div>