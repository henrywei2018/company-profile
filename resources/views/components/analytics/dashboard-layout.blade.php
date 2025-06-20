{{-- resources/views/components/analytics/dashboard-layout.blade.php --}}
<div class="analytics-dashboard" id="analytics-dashboard">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            
            <div class="flex items-center space-x-4">
                @if($showPeriodSelector)
                    <x-analytics.period-selector />
                @endif
                
                @if($showRefreshButton)
                    <button class="refresh-dashboard inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <x-icon name="refresh" class="w-4 h-4 mr-2" />
                        Refresh
                    </button>
                @endif
                
                @if($showExportOptions)
                    <div class="relative inline-block text-left">
                        <button class="export-dropdown inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <x-icon name="download" class="w-4 h-4 mr-2" />
                            Export
                            <x-icon name="chevron-down" class="w-4 h-4 ml-2" />
                        </button>
                        
                        <div class="export-menu hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <x-analytics.export-button type="visitors" label="Visitors Data" />
                                <x-analytics.export-button type="pages" label="Pages Data" />
                                <x-analytics.export-button type="referrers" label="Referrers Data" />
                                <x-analytics.export-button type="browsers" label="Browsers Data" />
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="analytics-content">
        {{ $slot }}
    </div>
</div>