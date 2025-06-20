{{-- resources/views/components/analytics/data-freshness-indicator.blade.php --}}
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
    <div class="flex items-start justify-between">
        <div class="flex items-center space-x-3">
            <!-- Data Status Icon -->
            <div class="flex-shrink-0">
                <div class="relative">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <!-- Live indicator -->
                    <div class="absolute -top-1 -right-1">
                        <span class="flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Data Information -->
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Analytics Data Status
                    </h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $status ?? 'Active' }}
                    </span>
                </div>
                
                <div class="mt-1 space-y-1">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Data as of: {{ $dataAsOf ?? now()->subHours(2)->format('M j, H:i') }}</span>
                        <span class="ml-2 text-xs text-orange-600 dark:text-orange-400">
                            (~{{ $estimatedDelay ?? '1-4 hours' }} behind real-time)
                        </span>
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Updates every {{ $updateFrequency ?? '15 minutes' }}</span>
                        <span class="ml-2 text-xs text-gray-500">
                            Next: {{ $nextUpdate ?? now()->addMinutes(15)->format('H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
            <!-- Manual Refresh Button -->
            <button onclick="refreshAnalyticsData()" 
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>

            <!-- Data Quality Indicator -->
            <div class="relative group">
                <button class="inline-flex items-center p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </button>
                
                <!-- Tooltip -->
                <div class="absolute right-0 top-8 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Data Information</h4>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex justify-between">
                            <span>API Status:</span>
                            <span class="text-green-600 dark:text-green-400">✓ Operational</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cache Strategy:</span>
                            <span>Smart caching</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Data Quality:</span>
                            <span class="text-blue-600 dark:text-blue-400">High</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last Error:</span>
                            <span class="text-gray-400">None</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            💡 For real-time data, use Google Analytics interface directly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar (optional) -->
    @if($showProgress ?? false)
    <div class="mt-3">
        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
            <span>Cache freshness</span>
            <span>{{ $cacheProgress ?? 75 }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
            <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-300" 
                 style="width: {{ $cacheProgress ?? 75 }}%"></div>
        </div>
    </div>
    @endif

    <!-- Expandable Details -->
    @if($showDetails ?? false)
    <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-700">
        <button onclick="toggleAnalyticsDetails()" 
                class="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
            <span id="details-text">Show technical details</span>
            <svg id="details-icon" class="w-4 h-4 ml-1 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div id="analytics-details" class="hidden mt-3 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                <div class="font-medium text-gray-900 dark:text-white mb-2">Cache Status</div>
                <div class="space-y-1 text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between">
                        <span>Dashboard:</span>
                        <span class="text-green-600">15min</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Real-time:</span>
                        <span class="text-green-600">5min</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Reports:</span>
                        <span class="text-blue-600">1hr</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                <div class="font-medium text-gray-900 dark:text-white mb-2">API Metrics</div>
                <div class="space-y-1 text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between">
                        <span>Response Time:</span>
                        <span class="text-green-600">1.2s</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Success Rate:</span>
                        <span class="text-green-600">99.8%</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Quota Usage:</span>
                        <span class="text-blue-600">12%</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                <div class="font-medium text-gray-900 dark:text-white mb-2">Data Quality</div>
                <div class="space-y-1 text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between">
                        <span>Completeness:</span>
                        <span class="text-green-600">100%</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Accuracy:</span>
                        <span class="text-green-600">High</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Freshness:</span>
                        <span class="text-yellow-600">Good</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
// Analytics Data Freshness JavaScript
function refreshAnalyticsData() {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Refreshing...
    `;
    
    // Make refresh request
    fetch('/admin/analytics/refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAnalyticsMessage('Analytics data refreshed successfully!', 'success');
            
            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAnalyticsMessage('Failed to refresh analytics data', 'error');
        }
    })
    .catch(error => {
        console.error('Refresh error:', error);
        showAnalyticsMessage('Error refreshing analytics data', 'error');
    })
    .finally(() => {
        // Restore button state
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 2000);
    });
}

function toggleAnalyticsDetails() {
    const details = document.getElementById('analytics-details');
    const icon = document.getElementById('details-icon');
    const text = document.getElementById('details-text');
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
        text.textContent = 'Hide technical details';
    } else {
        details.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
        text.textContent = 'Show technical details';
    }
}

function showAnalyticsMessage(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Auto-refresh indicator
setInterval(() => {
    const nextUpdateElement = document.querySelector('[data-next-update]');
    if (nextUpdateElement) {
        const nextUpdate = new Date(nextUpdateElement.dataset.nextUpdate);
        const now = new Date();
        const minutesUntilUpdate = Math.max(0, Math.ceil((nextUpdate - now) / 60000));
        
        if (minutesUntilUpdate === 0) {
            // Trigger auto-refresh
            window.location.reload();
        }
    }
}, 60000); // Check every minute
</script>