{{-- resources/views/components/analytics/realtime-stats.blade.php --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700"
     data-realtime-url="{{ $getRealtimeUrl() }}"
     data-auto-refresh="{{ $autoRefresh ? 'true' : 'false' }}"
     data-refresh-interval="{{ $refreshInterval }}">
    
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Real-time Statistics</h3>
        <div class="flex items-center space-x-2">
            <div class="flex items-center text-xs text-green-600">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></div>
                Live
            </div>
            <span class="text-xs text-gray-500" id="last-updated">
                Updated: <span id="last-update-time">{{ now()->format('H:i:s') }}</span>
            </span>
        </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600" id="active-users">
                {{ $stats['active_users'] ?? 0 }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Active Users</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600" id="today-visitors">
                {{ $stats['today_visitors'] ?? 0 }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Today's Visitors</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600" id="today-pageviews">
                {{ $stats['today_pageviews'] ?? 0 }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Today's Views</div>
        </div>
        
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600" id="bounce-rate">
                {{ number_format($stats['bounce_rate'] ?? 0, 1) }}%
            </div>
            <div class="text-xs text-gray-500 mt-1">Bounce Rate</div>
        </div>
    </div>
</div>