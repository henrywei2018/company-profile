{{-- resources/views/components/analytics/chart.blade.php --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        <div class="flex space-x-2">
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" 
                    onclick="refreshChart('{{ $chartId }}')">
                <x-icon name="refresh" class="w-4 h-4" />
            </button>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    onclick="toggleChartFullscreen('{{ $chartId }}')">
                <x-icon name="expand" class="w-4 h-4" />
            </button>
        </div>
    </div>
    <div class="relative" style="height: {{ $height }};">
        <canvas id="{{ $chartId }}" 
                data-chart-type="{{ $type }}"
                data-chart-data="{{ $getChartData() }}"
                data-chart-options="{{ $getChartOptions() }}">
        </canvas>
    </div>
</div>