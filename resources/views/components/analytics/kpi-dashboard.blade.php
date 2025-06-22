{{-- resources/views/components/analytics/kpi-dashboard.blade.php --}}
<div class="analytics-kpi-dashboard" id="kpi-dashboard">
    <!-- KPI Header with Controls -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Analytics KPI Dashboard
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Comprehensive website performance metrics and insights
                </p>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Period Selector -->
                <select id="kpi-period" onchange="changeKPIPeriod(this.value)" 
                        class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 3 months</option>
                </select>
                
                <!-- Refresh Button -->
                <button onclick="refreshKPIData()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh KPIs
                </button>
                
                <!-- Export Options -->
                <div class="relative">
                    <button onclick="toggleExportMenu()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div id="export-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        <div class="py-1">
                            <button onclick="exportKPIData('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Export as PDF
                            </button>
                            <button onclick="exportKPIData('excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Export as Excel
                            </button>
                            <button onclick="exportKPIData('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Export as CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Alerts Section -->
    <div id="kpi-alerts" class="mb-8">
        <!-- Alerts will be populated by JavaScript -->
    </div>

    <!-- Overview KPIs Grid -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overview KPIs</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" id="overview-kpis">
            <!-- KPI cards will be populated by JavaScript -->
        </div>
    </div>

    <!-- KPI Categories Tabs -->
    <div class="mb-8">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="KPI Categories">
                <button onclick="switchKPICategory('traffic')" 
                        id="tab-traffic"
                        class="kpi-tab active whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Traffic
                    </div>
                </button>
                
                <button onclick="switchKPICategory('engagement')" 
                        id="tab-engagement"
                        class="kpi-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Engagement
                    </div>
                </button>
                
                <button onclick="switchKPICategory('conversion')" 
                        id="tab-conversion"
                        class="kpi-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Conversion
                    </div>
                </button>
                
                <button onclick="switchKPICategory('audience')" 
                        id="tab-audience"
                        class="kpi-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Audience
                    </div>
                </button>
                
                <button onclick="switchKPICategory('acquisition')" 
                        id="tab-acquisition"
                        class="kpi-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                        Acquisition
                    </div>
                </button>
                
                <button onclick="switchKPICategory('behavior')" 
                        id="tab-behavior"
                        class="kpi-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Behavior
                    </div>
                </button>
                
                <button onclick="switchKPICategory('technical')" 
                        id="tab-technical"
                        class="kpi-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Technical
                    </div>
                </button>
            </nav>
        </div>
    </div>

    <!-- KPI Category Content -->
    <div class="kpi-category-content">
        
        <!-- Traffic KPIs -->
        <div id="traffic-content" class="kpi-category active">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Traffic Sources Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Traffic Sources</h4>
                    <canvas id="traffic-sources-chart" class="h-64">
                        <!-- Chart will be populated by JavaScript -->
                    </canvas>
                </div>
                
                <!-- Channel Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Channel Distribution</h4>
                    <canvas id="channel-distribution-chart" class="h-64">
                        <!-- Chart will be populated by JavaScript -->
                    </canvas>
                </div>
                
                <!-- Traffic Trends -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Traffic Trends</h4>
                    <canvas id="traffic-trends-chart" class="h-80">
                        <!-- Chart will be populated by JavaScript -->
                    </canvas>
                </div>
            </div>
        </div>

        <!-- Engagement KPIs -->
        <div id="engagement-content" class="kpi-category hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Engagement Metrics Cards -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Session Duration</h4>
                    <div id="session-duration-metric">
                        <!-- Metric will be populated by JavaScript -->
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bounce Rate</h4>
                    <div id="bounce-rate-metric">
                        <!-- Metric will be populated by JavaScript -->
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pages per Session</h4>
                    <div id="pages-per-session-metric">
                        <!-- Metric will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Most Engaging Pages -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2 xl:col-span-3">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Most Engaging Pages</h4>
                    <div id="engaging-pages-table">
                        <!-- Table will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion KPIs -->
        <div id="conversion-content" class="kpi-category hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Conversion Funnel -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Conversion Funnel</h4>
                    <div id="conversion-funnel" class="h-64">
                        <!-- Funnel will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Top Converting Sources -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Converting Sources</h4>
                    <div id="converting-sources-table">
                        <!-- Table will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Audience KPIs -->
        <div id="audience-content" class="kpi-category hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Geographic Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Countries</h4>
                    <canvas id="countries-chart" class="h-64">
                        <!-- Chart will be populated by JavaScript -->
                    </canvas>
                </div>
                
                <!-- Device Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Device Categories</h4>
                    <canvas id="devices-chart" class="h-64">
                        <!-- Chart will be populated by JavaScript -->
                    </canvas>
                </div>
                
                <!-- User Types -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New vs Returning</h4>
                    <canvas id="user-types-chart" class="h-64">
                        <!-- Chart will be populated by JavaScript -->
                    </canvas>
                </div>
            </div>
        </div>

        <!-- Acquisition KPIs -->
        <div id="acquisition-content" class="kpi-category hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Marketing Channels -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Marketing Channels</h4>
                    <canvas id="marketing-channels-chart" class="h-64">
                        <!-- Chart will be populated by JavaScript -->
                    </canvas>
                </div>
                
                <!-- Top Acquisition Sources -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Acquisition</h4>
                    <div id="acquisition-sources-table">
                        <!-- Table will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Behavior KPIs -->
        <div id="behavior-content" class="kpi-category hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Most Viewed Pages -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Most Viewed Pages</h4>
                    <div id="most-viewed-pages-table">
                        <!-- Table will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Landing Page Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Landing Page Performance</h4>
                    <div id="landing-pages-table">
                        <!-- Table will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical KPIs -->
        <div id="technical-content" class="kpi-category hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Site Speed Metrics -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Site Speed Performance</h4>
                    <div id="site-speed-metrics">
                        <!-- Metrics will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Browser Compatibility -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Browser Performance</h4>
                    <div id="browser-performance-table">
                        <!-- Table will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time KPI Summary (Bottom Widget) -->
    <div class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Real-time Performance Summary
            </h3>
            <div class="flex items-center text-sm text-blue-600 dark:text-blue-400">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                Live Updates
            </div>
        </div>
        
        <div id="realtime-summary" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Real-time summary will be populated by JavaScript -->
        </div>
    </div>
</div>

<style>
/* KPI Dashboard Styles */
.kpi-tab {
    @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
    @apply dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600;
}

.kpi-tab.active {
    @apply border-blue-500 text-blue-600;
    @apply dark:border-blue-400 dark:text-blue-400;
}

.kpi-category {
    animation: fadeIn 0.3s ease-in-out;
}

.kpi-category.hidden {
    display: none;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* KPI Status Colors */
.kpi-status-excellent {
    @apply text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/30;
}

.kpi-status-good {
    @apply text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/30;
}

.kpi-status-warning {
    @apply text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30;
}

.kpi-status-critical {
    @apply text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/30;
}

/* Trend Indicators */
.trend-up {
    @apply text-green-600 dark:text-green-400;
}

.trend-down {
    @apply text-red-600 dark:text-red-400;
}

.trend-stable {
    @apply text-gray-600 dark:text-gray-400;
}

/* Loading States */
.kpi-loading {
    @apply animate-pulse bg-gray-200 dark:bg-gray-700 rounded;
    height: 1rem;
}

.kpi-loading.h-8 {
    height: 2rem;
}

.kpi-loading.h-12 {
    height: 3rem;
}

/* Responsive Tables */
.kpi-table {
    @apply min-w-full divide-y divide-gray-200 dark:divide-gray-700;
}

.kpi-table th {
    @apply px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
}

.kpi-table td {
    @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white;
}

/* Chart Containers */
.chart-container {
    position: relative;
    height: 100%;
    width: 100%;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .kpi-tab {
        font-size: 0.75rem;
        padding: 0.5rem 0.25rem;
    }
    
    .kpi-tab svg {
        width: 1rem;
        height: 1rem;
    }
}
</style>
<script>
class KPIDashboard {
    constructor() {
        this.currentCategory = 'traffic';
        this.currentPeriod = 30;
        this.refreshInterval = null;
        this.kpiData = null;

        this.init();
    }

    init() {
        this.loadKPIData();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    setupEventListeners() {
        // Close export menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#export-menu') && !e.target.closest('button[onclick="toggleExportMenu()"]')) {
                const menu = document.getElementById('export-menu');
                if (menu) menu.classList.add('hidden');
            }
        });
    }

    async loadKPIData(period = this.currentPeriod) {
        try {
            this.showLoadingState();

            const response = await fetch(`/admin/analytics/kpi/dashboard?period=${period}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.kpiData = data.data;
                this.renderKPIDashboard();
                this.hideLoadingState();
                this.showSuccessMessage('KPI data loaded successfully');
            } else {
                throw new Error(data.message || 'Failed to load KPI data');
            }

        } catch (error) {
            console.error('KPI Dashboard error:', error);
            this.showErrorMessage('Failed to load KPI data: ' + error.message);
            this.renderEmptyState();
        }
    }

    renderKPIDashboard() {
        if (!this.kpiData) return;

        this.renderAlerts();
        this.renderOverviewKPIs();
        this.renderCategoryContent(this.currentCategory);
        this.renderRealtimeSummary();
    }

    renderAlerts() {
        const alertsContainer = document.getElementById('kpi-alerts');
        const alerts = this.kpiData.alerts?.alerts || [];

        if (alerts.length === 0) {
            alertsContainer.innerHTML = '';
            return;
        }

        alertsContainer.innerHTML = `
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            KPI Alerts (${alerts.length})
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <ul class="list-disc pl-5 space-y-1">
                                ${alerts.map(alert => `
                                    <li class="kpi-alert-${alert.severity}">
                                        <strong>${alert.metric}:</strong> ${alert.message}
                                        ${alert.recommendation ? `<br><em>Recommendation: ${alert.recommendation}</em>` : ''}
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    renderOverviewKPIs() {
        const container = document.getElementById('overview-kpis');
        const overview = this.kpiData.overview || {};

        const kpiCards = Object.entries(overview).map(([key, data]) => {
            const statusClass = this.getStatusClass(data.status);
            const trendIcon = this.getTrendIcon(data.trend);
            const trendClass = this.getTrendClass(data.trend);

            return `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                ${this.formatKPITitle(key)}
                            </h4>
                            <div class="flex items-center space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    ${this.formatKPIValue(key, data.current)}
                                </p>
                                ${data.change_percent !== undefined ? `
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${trendClass}">
                                        ${trendIcon}
                                        ${Math.abs(data.change_percent)}%
                                    </span>
                                ` : ''}
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    vs ${this.formatKPIValue(key, data.previous)} (prev period)
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                                    ${data.status}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = kpiCards;
    }

    renderCategoryContent(category) {
        // Hide all category content
        document.querySelectorAll('.kpi-category').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('active');
        });

        // Show selected category
        const categoryContent = document.getElementById(`${category}-content`);
        if (categoryContent) {
            categoryContent.classList.remove('hidden');
            categoryContent.classList.add('active');

            // Render category-specific content
            switch (category) {
                case 'traffic':
                    this.renderTrafficKPIs();
                    break;
                case 'engagement':
                    this.renderEngagementKPIs();
                    break;
                case 'conversion':
                    this.renderConversionKPIs();
                    break;
                case 'audience':
                    this.renderAudienceKPIs();
                    break;
                case 'acquisition':
                    this.renderAcquisitionKPIs();
                    break;
                case 'behavior':
                    this.renderBehaviorKPIs();
                    break;
                case 'technical':
                    this.renderTechnicalKPIs();
                    break;
            }
        }
    }

    renderTrafficKPIs() {
        const traffic = this.kpiData.traffic || {};

        // Traffic Sources Chart
        this.renderChart('traffic-sources-chart', {
            type: 'doughnut',
            data: {
                labels: (traffic.top_sources || []).map(s => s.source),
                datasets: [{
                    data: (traffic.top_sources || []).map(s => s.sessions),
                    backgroundColor: [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                        '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Channel Distribution Chart
        this.renderChart('channel-distribution-chart', {
            type: 'bar',
            data: {
                labels: (traffic.channel_distribution || []).map(c => c.channelGrouping),
                datasets: [{
                    label: 'Sessions',
                    data: (traffic.channel_distribution || []).map(c => c.sessions),
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Traffic Trends Chart
        this.renderChart('traffic-trends-chart', {
            type: 'line',
            data: {
                labels: (traffic.traffic_trend || []).map(t => t.date),
                datasets: [
                    {
                        label: 'Sessions',
                        data: (traffic.traffic_trend || []).map(t => t.value),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true
                    },
                    {
                        label: 'Users',
                        data: (traffic.user_trend || []).map(t => t.value),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    renderEngagementKPIs() {
        const engagement = this.kpiData.engagement || {};

        // Session Duration Metric
        document.getElementById('session-duration-metric').innerHTML = `
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    ${engagement.average_session_duration?.formatted || '0m 0s'}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Average Session Duration
                </div>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${this.getStatusClass(engagement.average_session_duration?.status)}">
                    ${engagement.average_session_duration?.status || 'unknown'}
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Benchmark: ${engagement.average_session_duration?.benchmark || 'N/A'}
                </div>
            </div>
        `;

        // Bounce Rate Metric
        document.getElementById('bounce-rate-metric').innerHTML = `
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    ${engagement.bounce_rate?.value || 0}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Bounce Rate
                </div>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${this.getStatusClass(engagement.bounce_rate?.status)}">
                    ${engagement.bounce_rate?.status || 'unknown'}
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Benchmark: ${engagement.bounce_rate?.benchmark || 'N/A'}
                </div>
            </div>
        `;

        // Pages per Session Metric
        document.getElementById('pages-per-session-metric').innerHTML = `
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    ${engagement.pages_per_session?.value || 0}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Pages per Session
                </div>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${this.getStatusClass(engagement.pages_per_session?.status)}">
                    ${engagement.pages_per_session?.status || 'unknown'}
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Benchmark: ${engagement.pages_per_session?.benchmark || 'N/A'}
                </div>
            </div>
        `;

        // Most Engaging Pages Table
        this.renderTable('engaging-pages-table', {
            headers: ['Page', 'Avg Time on Page', 'Pageviews', 'Bounce Rate'],
            rows: (engagement.most_engaging_pages || []).map(page => [
                page.pagePath || page.pageTitle || 'Unknown',
                this.formatDuration(page.averageTimeOnPage || 0),
                (page.pageviews || 0).toLocaleString(),
                `${((page.bounceRate || 0) * 100).toFixed(1)}%`
            ])
        });
    }

    renderConversionKPIs() {
        const conversion = this.kpiData.conversion || {};

        // Conversion Funnel
        const funnelData = conversion.conversion_funnel || [];
        if (funnelData.length > 0) {
            document.getElementById('conversion-funnel').innerHTML = `
                <div class="space-y-3">
                    ${funnelData.map((step, index) => `
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                    ${index + 1}
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    ${this.formatKPITitle(step.step)}
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-gray-900 dark:text-white">
                                    ${step.value.toLocaleString()}
                                </div>
                                ${step.drop_off_rate > 0 ? `
                                    <div class="text-xs text-red-600">
                                        -${step.drop_off_rate}% drop-off
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Top Converting Sources Table
        this.renderTable('converting-sources-table', {
            headers: ['Source', 'Conversions', 'Sessions', 'Conversion Rate'],
            rows: (conversion.top_converting_sources || []).map(source => [
                source.source,
                source.conversions?.toLocaleString() || '0',
                source.sessions?.toLocaleString() || '0',
                `${source.conversion_rate || 0}%`
            ])
        });
    }

    renderAudienceKPIs() {
        const audience = this.kpiData.audience || {};

        // Countries Chart
        this.renderChart('countries-chart', {
            type: 'bar',
            data: {
                labels: (audience.geographic_distribution?.top_countries || []).map(c => c.country),
                datasets: [{
                    label: 'Sessions',
                    data: (audience.geographic_distribution?.top_countries || []).map(c => c.sessions),
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Device Categories Chart
        this.renderChart('devices-chart', {
            type: 'doughnut',
            data: {
                labels: (audience.device_breakdown?.devices || []).map(d => d.deviceCategory),
                datasets: [{
                    data: (audience.device_breakdown?.devices || []).map(d => d.sessions),
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // User Types Chart
        this.renderChart('user-types-chart', {
            type: 'doughnut',
            data: {
                labels: ['New Users', 'Returning Users'],
                datasets: [{
                    data: [
                        audience.user_loyalty?.new_users_percentage || 0,
                        audience.user_loyalty?.returning_users_percentage || 0
                    ],
                    backgroundColor: ['#10B981', '#3B82F6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    renderAcquisitionKPIs() {
        const acquisition = this.kpiData.acquisition || {};

        // Marketing Channels Chart
        const channels = acquisition.marketing_channels || {};
        this.renderChart('marketing-channels-chart', {
            type: 'bar',
            data: {
                labels: Object.keys(channels).map(key => this.formatKPITitle(key)),
                datasets: [{
                    label: 'Sessions',
                    data: Object.values(channels),
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // User Acquisition Table
        this.renderTable('acquisition-sources-table', {
            headers: ['Source', 'New Users', 'Sessions', 'Sessions per User'],
            rows: (acquisition.user_acquisition?.top_acquisition_sources || []).map(source => [
                source.source,
                source.totalUsers?.toLocaleString() || '0',
                source.sessions?.toLocaleString() || '0',
                source.totalUsers > 0 ? (source.sessions / source.totalUsers).toFixed(2) : '0'
            ])
        });
    }

    renderBehaviorKPIs() {
        const behavior = this.kpiData.behavior || {};

        // Most Viewed Pages Table
        this.renderTable('most-viewed-pages-table', {
            headers: ['Page', 'Pageviews', 'Unique Views', 'Avg Time on Page'],
            rows: (behavior.content_performance?.most_viewed_pages || []).map(page => [
                page.pagePath || page.pageTitle || 'Unknown',
                page.pageviews?.toLocaleString() || '0',
                page.uniquePageviews?.toLocaleString() || '0',
                this.formatDuration(page.averageTimeOnPage || 0)
            ])
        });

        // Landing Pages Table
        this.renderTable('landing-pages-table', {
            headers: ['Landing Page', 'Sessions', 'Bounce Rate', 'Performance'],
            rows: (behavior.landing_page_performance?.top_landing_pages || []).map(page => [
                page.landingPage || 'Unknown',
                page.sessions?.toLocaleString() || '0',
                `${((page.bounceRate || 0) * 100).toFixed(1)}%`,
                this.getPerformanceIndicator(page.bounceRate || 0)
            ])
        });
    }

    renderTechnicalKPIs() {
        const technical = this.kpiData.technical || {};

        // Site Speed Metrics
        const siteSpeed = technical.site_speed || {};
        document.getElementById('site-speed-metrics').innerHTML = `
            <div class="space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        ${siteSpeed.average_page_load_time || 0}s
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Average Page Load Time
                    </div>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${this.getStatusClass(siteSpeed.status)}">
                        ${siteSpeed.status || 'unknown'}
                    </div>
                    <div class="mt-3 text-xs text-gray-500">
                        Benchmark: ${siteSpeed.speed_benchmark || 'N/A'}
                    </div>
                </div>

                ${siteSpeed.slowest_pages?.length > 0 ? `
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Slowest Pages</h5>
                        <div class="space-y-2">
                            ${siteSpeed.slowest_pages.slice(0, 3).map(page => `
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-600 dark:text-gray-400 truncate">${page.pagePath}</span>
                                    <span class="font-medium text-red-600">${page.avgPageLoadTime}s</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        `;

        // Browser Performance Table
        this.renderTable('browser-performance-table', {
            headers: ['Browser', 'Sessions', 'Bounce Rate', 'Performance Score'],
            rows: (technical.browser_compatibility?.browser_performance || []).map(browser => [
                browser.browser,
                browser.sessions?.toLocaleString() || '0',
                `${browser.bounce_rate || 0}%`,
                `${browser.performance_score || 0}/100`
            ])
        });
    }

    renderRealtimeSummary() {
        // This would typically fetch real-time data
        // For now, using mock data structure
        document.getElementById('realtime-summary').innerHTML = `
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    ${Math.floor(Math.random() * 50) + 10}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Active Users Right Now
                </div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    ${Math.floor(Math.random() * 1000) + 500}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Today's Sessions
                </div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    ${Math.floor(Math.random() * 50) + 20}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Today's Conversions
                </div>
            </div>
        `;
    }

    // --- Utility Methods ---
    renderChart(elementId, config) {
        // Use canvas for Chart.js (fix: element should be <canvas>)
        const el = document.getElementById(elementId);
        if (!el) return;

        // If this isn't a canvas, try to replace innerHTML with canvas
        if (el.tagName.toLowerCase() !== 'canvas') {
            el.innerHTML = `<canvas id="${elementId}-canvas"></canvas>`;
            return this.renderChart(`${elementId}-canvas`, config);
        }

        // Destroy existing chart if it exists
        if (el.chart) {
            el.chart.destroy();
        }

        try {
            el.chart = new Chart(el, config);
        } catch (error) {
            console.error('Chart rendering error:', error);
            el.innerHTML = '<div class="flex items-center justify-center h-full text-red-500">Chart rendering failed</div>';
        }
    }

    renderTable(elementId, config) {
        const container = document.getElementById(elementId);
        if (!container || !config.headers || !config.rows) return;

        container.innerHTML = `
            <div class="overflow-x-auto">
                <table class="kpi-table">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            ${config.headers.map(header => `<th>${header}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        ${config.rows.map(row => `
                            <tr>
                                ${row.map(cell => `<td>${cell}</td>`).join('')}
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    formatKPITitle(key) {
        return key.split('_').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    formatKPIValue(key, value) {
        if (value === undefined || value === null) return '0';

        switch (key) {
            case 'bounce_rate':
                return `${value}%`;
            case 'avg_session_duration':
                return this.formatDuration(value);
            default:
                return typeof value === 'number' ? value.toLocaleString() : value;
        }
    }

    formatDuration(seconds) {
        if (!seconds || seconds === 0) return '0s';

        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);

        if (minutes === 0) return `${remainingSeconds}s`;
        return `${minutes}m ${remainingSeconds}s`;
    }

    getStatusClass(status) {
        const classes = {
            'excellent': 'kpi-status-excellent',
            'good': 'kpi-status-good',
            'warning': 'kpi-status-warning',
            'critical': 'kpi-status-critical'
        };
        return classes[status] || 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
    }

    getTrendIcon(trend) {
        const icons = {
            'up': '↗',
            'down': '↘',
            'stable': '→'
        };
        return icons[trend] || '→';
    }

    getTrendClass(trend) {
        const classes = {
            'up': 'trend-up',
            'down': 'trend-down',
            'stable': 'trend-stable'
        };
        return classes[trend] || 'trend-stable';
    }

    getPerformanceIndicator(bounceRate) {
        if (bounceRate <= 0.4) return '🟢 Excellent';
        if (bounceRate <= 0.6) return '🟡 Good';
        if (bounceRate <= 0.8) return '🟠 Fair';
        return '🔴 Poor';
    }

    showLoadingState() {
        const elements = [
            'overview-kpis',
            'traffic-content',
            'engagement-content',
            'conversion-content',
            'audience-content',
            'acquisition-content',
            'behavior-content',
            'technical-content'
        ];

        elements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<div class="kpi-loading h-32"></div>';
            }
        });
    }

    hideLoadingState() {
        // No-op; actual content will overwrite loading state
    }

    renderEmptyState() {
        const container = document.getElementById('overview-kpis');
        if (container) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No KPI data available</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Analytics data is temporarily unavailable. Please try refreshing.
                    </p>
                </div>
            `;
        }
    }

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }

    showMessage(message, type) {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }

    startAutoRefresh() {
        this.refreshInterval = setInterval(() => {
            this.loadKPIData();
        }, 5 * 60 * 1000);
    }

    cleanup() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}

// --- Global Event Handlers / Integration ---
window.kpiDashboard = new KPIDashboard();

window.changeKPIPeriod = (period) => {
    kpiDashboard.currentPeriod = parseInt(period);
    kpiDashboard.loadKPIData(kpiDashboard.currentPeriod);
};

window.switchKPICategory = (category) => {
    document.querySelectorAll('.kpi-tab').forEach(tab => tab.classList.remove('active'));
    const tabBtn = document.getElementById('tab-' + category);
    if (tabBtn) tabBtn.classList.add('active');
    kpiDashboard.currentCategory = category;
    kpiDashboard.renderCategoryContent(category);
};

window.refreshKPIData = () => {
    kpiDashboard.loadKPIData(kpiDashboard.currentPeriod);
};

window.toggleExportMenu = () => {
    const menu = document.getElementById('export-menu');
    if (menu) menu.classList.toggle('hidden');
};

window.exportKPIData = (type) => {
    kpiDashboard.showSuccessMessage(`Export as ${type.toUpperCase()} is not implemented.`);
    const menu = document.getElementById('export-menu');
    if (menu) menu.classList.add('hidden');
};

// Cleanup interval if page is unloaded
window.addEventListener('beforeunload', () => {
    kpiDashboard.cleanup();
});
</script>