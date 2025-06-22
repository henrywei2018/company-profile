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
                <select id="kpi-period"
                    class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 3 months</option>
                </select>

                <!-- Refresh Button -->
                <button id="refresh-kpi-btn"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh KPIs
                </button>

                <!-- Export Button -->
                <div class="relative">
                    <button id="export-toggle"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </button>

                    <div id="export-menu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        <div class="py-1">
                            <button data-export="pdf"
                                class="export-option block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Export as PDF
                            </button>
                            <button data-export="excel"
                                class="export-option block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Export as Excel
                            </button>
                            <button data-export="csv"
                                class="export-option block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
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
            <!-- Loading state -->
            <div class="col-span-full flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600">Loading KPI data...</span>
            </div>
        </div>
    </div>

    <!-- KPI Categories Tabs -->
    <div class="mb-8">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="KPI Categories">
                <button data-category="traffic" class="kpi-tab active">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Traffic
                    </div>
                </button>

                <button data-category="engagement" class="kpi-tab">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Engagement
                    </div>
                </button>

                <button data-category="conversion" class="kpi-tab">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Conversion
                    </div>
                </button>

                <button data-category="audience" class="kpi-tab">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Audience
                    </div>
                </button>

                <button data-category="acquisition" class="kpi-tab">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                        Acquisition
                    </div>
                </button>

                <button data-category="behavior" class="kpi-tab">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Behavior
                    </div>
                </button>

                <button data-category="technical" class="kpi-tab">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Technical
                    </div>
                </button>
            </nav>
        </div>
    </div>

    <!-- KPI Category Content -->
    <div id="kpi-content" class="min-h-96">
        <!-- Content will be dynamically populated based on selected category -->
        <div class="flex items-center justify-center py-24">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600 dark:text-gray-400">Loading category data...</p>
            </div>
        </div>
    </div>

    <!-- Real-time KPI Summary (Bottom Widget) -->
    <div
        class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
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
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">--</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">--</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Today's Sessions</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">--</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Today's Conversions</div>
            </div>
        </div>
    </div>
</div>

<style>
    /* KPI Dashboard Styles */
    .kpi-tab {
        @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
        @apply dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600;
        @apply whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors cursor-pointer;
    }

    .kpi-tab.active {
        @apply border-blue-500 text-blue-600;
        @apply dark:border-blue-400 dark:text-blue-400;
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

    /* Chart Containers */
    .chart-container {
        position: relative;
        height: 300px;
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
    class SimpleKPIDashboard {
        constructor() {
            this.currentCategory = 'traffic';
            this.currentPeriod = 30;
            this.data = null;
            this.charts = {};

            this.init();
        }

        init() {
            this.setupEventListeners();
            this.loadData();
        }

        setupEventListeners() {
            // Period selector
            const periodSelect = document.getElementById('kpi-period');
            if (periodSelect) {
                periodSelect.addEventListener('change', (e) => {
                    this.currentPeriod = parseInt(e.target.value);
                    this.loadData();
                });
            }

            // Refresh button
            const refreshBtn = document.getElementById('refresh-kpi-btn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', () => {
                    this.loadData();
                });
            }

            // Export menu toggle
            const exportToggle = document.getElementById('export-toggle');
            const exportMenu = document.getElementById('export-menu');
            if (exportToggle && exportMenu) {
                exportToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    exportMenu.classList.toggle('hidden');
                });

                // Close export menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!exportMenu.contains(e.target) && !exportToggle.contains(e.target)) {
                        exportMenu.classList.add('hidden');
                    }
                });
            }

            // Export options
            document.querySelectorAll('.export-option').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const type = e.target.dataset.export;
                    this.exportData(type);
                    document.getElementById('export-menu').classList.add('hidden');
                });
            });

            // Category tabs
            document.querySelectorAll('.kpi-tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    const category = e.currentTarget.dataset.category;
                    this.switchCategory(category);
                });
            });
        }

        async loadData() {
            try {
                this.showLoading();

                const response = await fetch(`/admin/analytics/kpi/dashboard?period=${this.currentPeriod}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();

                if (result.success) {
                    this.data = result.data;
                    this.renderDashboard();
                    this.showSuccess('KPI data loaded successfully');
                } else {
                    throw new Error(result.message || 'Failed to load data');
                }

            } catch (error) {
                console.error('Error loading KPI data:', error);
                this.showError('Failed to load KPI data: ' + error.message);
                this.renderEmptyState();
            }
        }

        renderDashboard() {
            this.renderOverview();
            this.renderCategory(this.currentCategory);
            this.renderRealtimeSummary();
        }

        renderOverview() {
            const container = document.getElementById('overview-kpis');
            if (!container || !this.data?.overview) return;

            const overview = this.data.overview;
            const cards = Object.entries(overview).map(([key, data]) => {
                const change = data.change_percent || 0;
                const trendIcon = change > 0 ? '↗' : change < 0 ? '↘' : '→';
                const trendClass = change > 0 ? 'text-green-600' : change < 0 ? 'text-red-600' :
                    'text-gray-600';

                return `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                ${this.formatTitle(key)}
                            </h4>
                            <div class="flex items-center space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    ${this.formatValue(key, data.current || data.value || 0)}
                                </p>
                                ${change !== 0 ? `
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${trendClass}">
                                        ${trendIcon} ${Math.abs(change)}%
                                    </span>
                                ` : ''}
                            </div>
                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                vs ${this.formatValue(key, data.previous || 0)} (prev period)
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');

            container.innerHTML = cards;
        }

        renderCategory(category) {
            const container = document.getElementById('kpi-content');
            if (!container) return;

            // Update active tab
            document.querySelectorAll('.kpi-tab').forEach(tab => {
                tab.classList.remove('active');
                if (tab.dataset.category === category) {
                    tab.classList.add('active');
                }
            });

            // Render category content
            switch (category) {
                case 'traffic':
                    this.renderTrafficContent(container);
                    break;
                case 'engagement':
                    this.renderEngagementContent(container);
                    break;
                case 'conversion':
                    this.renderConversionContent(container);
                    break;
                case 'audience':
                    this.renderAudienceContent(container);
                    break;
                case 'acquisition':
                    this.renderAcquisitionContent(container);
                    break;
                case 'behavior':
                    this.renderBehaviorContent(container);
                    break;
                case 'technical':
                    this.renderTechnicalContent(container);
                    break;
                default:
                    this.renderNoData(container);
            }
        }

        renderTrafficContent(container) {
            const traffic = this.data?.traffic;
            if (!traffic) {
                this.renderNoData(container);
                return;
            }

            container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Traffic Sources</h4>
                    <div class="chart-container">
                        <canvas id="traffic-sources-chart"></canvas>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Channel Distribution</h4>
                    <div class="chart-container">
                        <canvas id="channel-distribution-chart"></canvas>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Traffic Trends</h4>
                    <div class="chart-container">
                        <canvas id="traffic-trends-chart"></canvas>
                    </div>
                </div>
            </div>
        `;

            // Render charts after DOM is updated
            setTimeout(() => {
                this.renderTrafficCharts(traffic);
            }, 100);
        }

        renderEngagementContent(container) {
            const engagement = this.data?.engagement;
            if (!engagement) {
                this.renderNoData(container);
                return;
            }

            container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Avg Session Duration</h4>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        ${engagement.average_session_duration?.formatted || '0m 0s'}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ${engagement.average_session_duration?.benchmark || 'No benchmark data'}
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bounce Rate</h4>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        ${engagement.bounce_rate?.value || 0}%
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ${engagement.bounce_rate?.benchmark || 'No benchmark data'}
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pages per Session</h4>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        ${engagement.pages_per_session?.value || 0}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        ${engagement.pages_per_session?.benchmark || 'No benchmark data'}
                    </div>
                </div>
            </div>
            
            <!-- Most Engaging Pages Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Most Engaging Pages</h4>
                ${this.renderEngagingPagesTable(engagement.most_engaging_pages)}
            </div>
        `;
        }

        renderConversionContent(container) {
            const conversion = this.data?.conversion;
            if (!conversion) {
                this.renderNoData(container);
                return;
            }

            container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Conversion Funnel -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Conversion Funnel</h4>
                    ${this.renderConversionFunnel(conversion.conversion_funnel)}
                </div>
                
                <!-- Top Converting Sources -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Converting Sources</h4>
                    ${this.renderConvertingSourcesTable(conversion.top_converting_sources)}
                </div>
            </div>
        `;
        }

        renderAudienceContent(container) {
            const audience = this.data?.audience;
            if (!audience) {
                this.renderNoData(container);
                return;
            }

            container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Geographic Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Countries</h4>
                    <div class="chart-container">
                        <canvas id="countries-chart"></canvas>
                    </div>
                </div>
                
                <!-- Device Breakdown -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Device Categories</h4>
                    <div class="chart-container">
                        <canvas id="devices-chart"></canvas>
                    </div>
                </div>
                
                <!-- User Types -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New vs Returning</h4>
                    <div class="chart-container">
                        <canvas id="user-types-chart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Audience Demographics Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Audience Demographics</h4>
                ${this.renderAudienceDemographicsTable(audience)}
            </div>
        `;

            // Render charts after DOM is updated
            setTimeout(() => {
                this.renderAudienceCharts(audience);
            }, 100);
        }

        renderAcquisitionContent(container) {
            const acquisition = this.data?.acquisition;
            if (!acquisition) {
                this.renderNoData(container);
                return;
            }

            container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Marketing Channels Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Marketing Channels</h4>
                    <div class="chart-container">
                        <canvas id="marketing-channels-chart"></canvas>
                    </div>
                </div>
                
                <!-- Top Acquisition Sources Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Acquisition Sources</h4>
                    ${this.renderAcquisitionSourcesTable(acquisition.user_acquisition?.top_acquisition_sources)}
                </div>
            </div>
        `;

            // Render charts after DOM is updated
            setTimeout(() => {
                this.renderAcquisitionCharts(acquisition);
            }, 100);
        }

        renderBehaviorContent(container) {
            const behavior = this.data?.behavior;
            if (!behavior) {
                this.renderNoData(container);
                return;
            }

            container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Most Viewed Pages -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Most Viewed Pages</h4>
                    ${this.renderMostViewedPagesTable(behavior.content_performance?.most_viewed_pages)}
                </div>
                
                <!-- Landing Page Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Landing Page Performance</h4>
                    ${this.renderLandingPagesTable(behavior.landing_page_performance?.top_landing_pages)}
                </div>
            </div>
        `;
        }

        renderTechnicalContent(container) {
            const technical = this.data?.technical;
            if (!technical) {
                this.renderNoData(container);
                return;
            }

            container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Site Speed Metrics -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Site Speed Performance</h4>
                    ${this.renderSiteSpeedMetrics(technical.site_speed)}
                </div>
                
                <!-- Browser Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Browser Performance</h4>
                    ${this.renderBrowserPerformanceTable(technical.browser_compatibility?.browser_performance)}
                </div>
            </div>
        `;
        }

        renderTrafficCharts(traffic) {
            // Traffic Sources Chart
            if (traffic.top_sources && traffic.top_sources.length > 0) {
                this.createChart('traffic-sources-chart', {
                    type: 'doughnut',
                    data: {
                        labels: traffic.top_sources.map(s => s.source || s.sessionSource || 'Unknown'),
                        datasets: [{
                            data: traffic.top_sources.map(s => parseInt(s.sessions) || 0),
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
            }

            // Channel Distribution Chart
            if (traffic.channel_distribution && traffic.channel_distribution.length > 0) {
                this.createChart('channel-distribution-chart', {
                    type: 'bar',
                    data: {
                        labels: traffic.channel_distribution.map(c => c.channelGrouping || c
                            .sessionDefaultChannelGroup || 'Unknown'),
                        datasets: [{
                            label: 'Sessions',
                            data: traffic.channel_distribution.map(c => parseInt(c.sessions) || 0),
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
            }

            // Traffic Trends Chart
            if (traffic.traffic_trend && traffic.traffic_trend.length > 0) {
                this.createChart('traffic-trends-chart', {
                    type: 'line',
                    data: {
                        labels: traffic.traffic_trend.map(t => t.date),
                        datasets: [{
                            label: 'Sessions',
                            data: traffic.traffic_trend.map(t => parseInt(t.value) || 0),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true
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
            }
        }

        createChart(elementId, config) {
            const canvas = document.getElementById(elementId);
            if (!canvas) {
                console.warn(`Canvas ${elementId} not found`);
                return;
            }

            // Destroy existing chart if it exists
            if (this.charts[elementId]) {
                this.charts[elementId].destroy();
            }

            // Check if Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                canvas.parentElement.innerHTML = `
                <div class="flex items-center justify-center h-full text-red-500">
                    <p class="text-sm">Chart.js library not loaded</p>
                </div>
            `;
                return;
            }

            try {
                const ctx = canvas.getContext('2d');
                this.charts[elementId] = new Chart(ctx, config);
            } catch (error) {
                console.error(`Error creating chart ${elementId}:`, error);
                canvas.parentElement.innerHTML = `
                <div class="flex items-center justify-center h-full text-red-500">
                    <p class="text-sm">Chart rendering failed</p>
                </div>
            `;
            }
        }

        renderRealtimeSummary() {
            const container = document.getElementById('realtime-summary');
            if (!container) return;

            // Use data from this.data.overview (edit keys as per your actual structure!)
            const activeUsers = this.data?.overview?.users?.current || 0;
            const sessions = this.data?.overview?.sessions?.current || 0;
            const conversions = this.data?.overview?.conversions?.current || 0;

            container.innerHTML = `
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        ${activeUsers}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        ${sessions}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Today's Sessions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        ${conversions}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Today's Conversions</div>
                </div>
            `;
        }


        renderEngagingPagesTable(pages) {
            if (!pages || pages.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No engaging pages data available
            </div>
        `;
            }

            return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Time on Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Page Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bounce Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    ${pages.map(page => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.pagePath || page.page || 'Unknown'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.avgTimeOnPage || page.avg_time || '0s'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.pageviews || page.views || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.bounceRate || page.bounce_rate || 0}%
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }

        renderConversionFunnel(funnel) {
            if (!funnel || funnel.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No conversion funnel data available
            </div>
        `;
            }

            return `
        <div class="space-y-4">
            ${funnel.map((step, index) => {
                const percentage = step.percentage || ((step.users / funnel[0].users) * 100).toFixed(1);
                return `
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                ${step.step || step.name || `Step ${index + 1}`}
                            </span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                ${step.users || step.count || 0} users (${percentage}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-8">
                            <div class="bg-blue-600 h-8 rounded-full flex items-center justify-end pr-2"
                                style="width: ${percentage}%">
                                <span class="text-xs text-white font-medium">${percentage}%</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
        }

        renderConvertingSourcesTable(sources) {
            if (!sources || sources.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No converting sources data available
            </div>
        `;
            }

            return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Conversions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Conversion Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    ${sources.map(source => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${source.source || source.name || 'Unknown'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${source.conversions || source.conversion_count || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${source.conversionRate || source.conversion_rate || 0}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                $${source.revenue || source.total_revenue || 0}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }

        renderAudienceDemographicsTable(audience) {
            const demographics = audience.demographics || {};
            const ageGroups = demographics.age_groups || [];
            const genders = demographics.genders || [];

            if (ageGroups.length === 0 && genders.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No demographics data available
            </div>
        `;
            }

            return `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h5 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">Age Distribution</h5>
                <div class="space-y-2">
                    ${ageGroups.map(group => `
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">${group.age || group.ageGroup || 'Unknown'}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-4 mr-2">
                                    <div class="bg-blue-600 h-4 rounded-full" style="width: ${group.percentage || 0}%"></div>
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-300">${group.percentage || 0}%</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            <div>
                <h5 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">Gender Distribution</h5>
                <div class="space-y-2">
                    ${genders.map(gender => `
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">${gender.gender || 'Unknown'}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-4 mr-2">
                                    <div class="bg-purple-600 h-4 rounded-full" style="width: ${gender.percentage || 0}%"></div>
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-300">${gender.percentage || 0}%</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
        }

        renderAudienceCharts(audience) {
            // Countries Chart
            if (audience.geographic && audience.geographic.top_countries) {
                const countries = audience.geographic.top_countries.slice(0, 5);
                this.createChart('countries-chart', {
                    type: 'bar',
                    data: {
                        labels: countries.map(c => c.country || 'Unknown'),
                        datasets: [{
                            label: 'Sessions',
                            data: countries.map(c => parseInt(c.sessions) || 0),
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
            }

            // Devices Chart
            if (audience.device_breakdown) {
                this.createChart('devices-chart', {
                    type: 'doughnut',
                    data: {
                        labels: audience.device_breakdown.map(d => d.deviceCategory || d.device ||
                            'Unknown'),
                        datasets: [{
                            data: audience.device_breakdown.map(d => parseInt(d.sessions) || 0),
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
            }

            // User Types Chart
            if (audience.user_types) {
                this.createChart('user-types-chart', {
                    type: 'pie',
                    data: {
                        labels: audience.user_types.map(u => u.userType || u.type || 'Unknown'),
                        datasets: [{
                            data: audience.user_types.map(u => parseInt(u.sessions) || 0),
                            backgroundColor: ['#8B5CF6', '#EC4899']
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
        }

        renderAcquisitionSourcesTable(sources) {
            if (!sources || sources.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No acquisition sources data available
            </div>
        `;
            }

            return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Source / Medium</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">New Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sessions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    ${sources.map(source => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${source.sourceMedium || source.source || 'Unknown'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${source.users || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${source.newUsers || source.new_users || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${source.sessions || 0}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }

        renderAcquisitionCharts(acquisition) {
            if (acquisition.marketing_channels && acquisition.marketing_channels.length > 0) {
                this.createChart('marketing-channels-chart', {
                    type: 'bar',
                    data: {
                        labels: acquisition.marketing_channels.map(c => c.channel || c.channelGrouping ||
                            'Unknown'),
                        datasets: [{
                            label: 'Users',
                            data: acquisition.marketing_channels.map(c => parseInt(c.users) || 0),
                            backgroundColor: '#10B981'
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
            }
        }

        renderMostViewedPagesTable(pages) {
            if (!pages || pages.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No page views data available
            </div>
        `;
            }

            return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Page Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unique Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    ${pages.map(page => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                <div class="truncate max-w-xs" title="${page.pagePath || page.page || 'Unknown'}">
                                    ${page.pagePath || page.page || 'Unknown'}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.pageviews || page.views || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.uniquePageviews || page.unique_views || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.avgTimeOnPage || page.avg_time || '0s'}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }

        renderLandingPagesTable(pages) {
            if (!pages || pages.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No landing page data available
            </div>
        `;
            }

            return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Landing Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bounce Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Conversion Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    ${pages.map(page => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                <div class="truncate max-w-xs" title="${page.landingPage || page.page || 'Unknown'}">
                                    ${page.landingPage || page.page || 'Unknown'}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.sessions || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.bounceRate || page.bounce_rate || 0}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${page.conversionRate || page.conversion_rate || 0}%
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }

        renderSiteSpeedMetrics(siteSpeed) {
            if (!siteSpeed) {
                return `
            <div class="text-center py-8 text-gray-500">
                No site speed data available
            </div>
        `;
            }

            const metrics = siteSpeed.metrics || {};
            const recommendations = siteSpeed.recommendations || [];

            return `
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Avg Page Load Time</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${metrics.avgPageLoadTime || metrics.avg_page_load_time || '0'}s
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Avg Server Response</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${metrics.avgServerResponseTime || metrics.avg_server_response_time || '0'}ms
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Avg Page Download</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${metrics.avgPageDownloadTime || metrics.avg_page_download_time || '0'}s
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Avg DOM Interactive</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ${metrics.avgDomInteractiveTime || metrics.avg_dom_interactive_time || '0'}s
                    </p>
                </div>
            </div>
            
            ${recommendations.length > 0 ? `
                <div>
                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Performance Recommendations</h5>
                    <ul class="space-y-2">
                        ${recommendations.map(rec => `
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">${rec}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            ` : ''}
        </div>
    `;
        }

        renderBrowserPerformanceTable(browsers) {
            if (!browsers || browsers.length === 0) {
                return `
            <div class="text-center py-8 text-gray-500">
                No browser performance data available
            </div>
        `;
            }

            return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Browser</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Version</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Load Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    ${browsers.map(browser => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${browser.browser || 'Unknown'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${browser.browserVersion || browser.version || 'N/A'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${browser.sessions || 0}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${browser.avgPageLoadTime || browser.avg_load_time || '0'}s
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
        }
        renderNoData(container) {
            container.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No data available</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Data for this category is not available yet.
                </p>
            </div>
        `;
        }

        renderEmptyState() {
            const container = document.getElementById('overview-kpis');
            if (container) {
                container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Unable to load KPI data</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Please check your connection and try refreshing.
                    </p>
                    <button onclick="window.kpiDashboard.loadData()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Try Again
                    </button>
                </div>
            `;
            }
        }

        switchCategory(category) {
            this.currentCategory = category;
            this.renderCategory(category);
        }

        exportData(type) {
            // Mock export functionality
            this.showSuccess(`Export as ${type.toUpperCase()} initiated`);
            console.log(`Exporting data as ${type}:`, this.data);
        }

        showLoading() {
            const overviewContainer = document.getElementById('overview-kpis');
            if (overviewContainer) {
                overviewContainer.innerHTML = `
                <div class="col-span-full flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600">Loading KPI data...</span>
                </div>
            `;
            }

            const contentContainer = document.getElementById('kpi-content');
            if (contentContainer) {
                contentContainer.innerHTML = `
                <div class="flex items-center justify-center py-24">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600 dark:text-gray-400">Loading category data...</p>
                    </div>
                </div>
            `;
            }
        }

        showSuccess(message) {
            this.showNotification(message, 'success');
        }

        showError(message) {
            this.showNotification(message, 'error');
        }

        showNotification(message, type = 'info') {
            // Remove existing notifications
            document.querySelectorAll('.kpi-notification').forEach(n => n.remove());

            const notification = document.createElement('div');
            notification.className =
                `kpi-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;

            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            notification.classList.add(bgColor, 'text-white');
            notification.textContent = message;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => notification.classList.remove('translate-x-full'), 100);

            // Animate out and remove
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        formatTitle(key) {
            return key.split('_').map(word =>
                word.charAt(0).toUpperCase() + word.slice(1)
            ).join(' ');
        }

        formatValue(key, value) {
            if (value === undefined || value === null) return '0';

            switch (key) {
                case 'bounce_rate':
                    return `${value}%`;
                case 'avg_session_duration':
                    const minutes = Math.floor(value / 60);
                    const seconds = Math.floor(value % 60);
                    return `${minutes}m ${seconds}s`;
                default:
                    return typeof value === 'number' ? value.toLocaleString() : value;
            }
        }

        destroy() {
            // Clean up charts
            Object.values(this.charts).forEach(chart => chart.destroy());
            this.charts = {};
        }
    }

    // Initialize the dashboard when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize if we're on the KPI dashboard tab or if the element exists
        const kpiDashboard = document.getElementById('kpi-dashboard');
        if (kpiDashboard) {
            window.kpiDashboard = new SimpleKPIDashboard();
        }
    });

    // Global function for external initialization (called from main dashboard tabs)
    window.initKPIDashboard = function() {
        if (!window.kpiDashboard) {
            window.kpiDashboard = new SimpleKPIDashboard();
        } else {
            window.kpiDashboard.loadData();
        }
    };

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (window.kpiDashboard) {
            window.kpiDashboard.destroy();
        }
    });
</script>
