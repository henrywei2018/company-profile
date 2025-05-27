<x-layouts.admin title="Chat Reports & Analytics" :enableCharts="true">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Reports & Analytics</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Comprehensive chat performance analysis and reporting</p>
        </div>
        <div class="flex gap-3">
            <x-admin.button color="info" onclick="exportReport()">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m-6 0a9 9 0 110-18 9 9 0 010 18z"/>
                </svg>
                Export Report
            </x-admin.button>
            <x-admin.button color="light" href="{{ route('admin.chat.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </x-admin.button>
        </div>
    </div>

    <!-- Filters Section -->
    <x-admin.card title="Report Filters" class="mb-6">
        <form method="GET" action="{{ route('admin.chat.reports.index') }}" id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                    <select name="date_range" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" onchange="toggleCustomDates()">
                        <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ request('date_range') === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="last_week" {{ request('date_range') === 'last_week' ? 'selected' : '' }}>Last Week</option>
                        <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ request('date_range') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="last_30_days" {{ request('date_range') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="last_90_days" {{ request('date_range') === 'last_90_days' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="custom" {{ request('date_range') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date From -->
                <div id="custom-date-from" class="{{ request('date_range') === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Custom Date To -->
                <div id="custom-date-to" class="{{ request('date_range') === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session Status</label>
                    <select name="status" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                    <select name="priority" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <!-- Operator Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operator</label>
                    <select name="operator_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                        <option value="">All Operators</option>
                        @if(isset($operators))
                            @foreach($operators as $operator)
                                <option value="{{ $operator->id }}" {{ request('operator_id') == $operator->id ? 'selected' : '' }}>
                                    {{ $operator->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Report Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Report Type</label>
                    <select name="report_type" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                        <option value="overview" {{ request('report_type') === 'overview' ? 'selected' : '' }}>Overview</option>
                        <option value="detailed" {{ request('report_type') === 'detailed' ? 'selected' : '' }}>Detailed Sessions</option>
                        <option value="performance" {{ request('report_type') === 'performance' ? 'selected' : '' }}>Performance Metrics</option>
                        <option value="operator" {{ request('report_type') === 'operator' ? 'selected' : '' }}>Operator Performance</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Client name, email, message..."
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <x-admin.button type="button" color="light" onclick="clearFilters()">
                    Clear Filters
                </x-admin.button>
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Generate Report
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <!-- Report Results -->
    @if(isset($reportData))
        
        <!-- Overview Statistics -->
        @if(request('report_type', 'overview') === 'overview')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <x-admin.card noPadding>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Sessions</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $reportData['total_sessions'] ?? 0 }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <x-admin.card noPadding>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Messages</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $reportData['total_messages'] ?? 0 }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <x-admin.card noPadding>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Avg Response Time</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($reportData['avg_response_time'] ?? 0, 1) }}min
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <x-admin.card noPadding>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Satisfaction Rate</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($reportData['satisfaction_rate'] ?? 0, 1) }}%
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Sessions Over Time Chart -->
                <x-admin.card title="Sessions Over Time">
                    <canvas id="sessionsChart" height="300"></canvas>
                </x-admin.card>

                <!-- Response Time Trends -->
                <x-admin.card title="Response Time Trends">
                    <canvas id="responseTimeChart" height="300"></canvas>
                </x-admin.Card>
            </div>
        @endif

        <!-- Detailed Sessions Table -->
        @if(request('report_type') === 'detailed' && isset($sessions))
            <x-admin.card title="Detailed Chat Sessions">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Started</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Messages</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operator</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($sessions as $session)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $session->getVisitorName() }}
                                        </div>
                                        @if($session->getVisitorEmail())
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $session->getVisitorEmail() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $session->started_at->format('M j, Y H:i') }}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $session->getDuration() ? $session->getDuration() . ' min' : '-' }}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $session->messages_count ?? $session->messages->count() }}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $session->operator ? $session->operator->name : 'Bot' }}
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <x-admin.badge :type="$session->status === 'active' ? 'success' : ($session->status === 'waiting' ? 'warning' : 'light')">
                                            {{ ucfirst($session->status) }}
                                        </x-admin.badge>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <x-admin.badge :type="$session->priority === 'urgent' ? 'danger' : ($session->priority === 'high' ? 'warning' : 'info')">
                                            {{ ucfirst($session->priority) }}
                                        </x-admin.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if(method_exists($sessions, 'links'))
                        <div class="mt-4">
                            {{ $sessions->links() }}
                        </div>
                    @endif
                </div>
            </x-admin.card>
        @endif

    @else
        <!-- Initial State -->
        <x-admin.card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Generate Your First Report</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Use the filters above to customize and generate comprehensive chat reports.
                </p>
            </div>
        </x-admin.card>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleCustomDates() {
            const dateRange = document.querySelector('select[name="date_range"]').value;
            const customFrom = document.getElementById('custom-date-from');
            const customTo = document.getElementById('custom-date-to');
            
            if (dateRange === 'custom') {
                customFrom.classList.remove('hidden');
                customTo.classList.remove('hidden');
            } else {
                customFrom.classList.add('hidden');
                customTo.classList.add('hidden');
            }
        }

        function clearFilters() {
            const form = document.getElementById('filter-form');
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'date' || input.type === 'text') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
            toggleCustomDates();
        }

        function exportReport() {
            const form = document.getElementById('filter-form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            params.append('export', 'true');
            
            window.open(`{{ route('admin.chat.reports.export') }}?${params.toString()}`, '_blank');
        }

        // Initialize charts if data exists
        @if(isset($reportData) && request('report_type', 'overview') === 'overview')
            // Sessions Chart
            if (document.getElementById('sessionsChart')) {
                const ctx1 = document.getElementById('sessionsChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: @json($reportData['chart_labels'] ?? []),
                        datasets: [{
                            label: 'Chat Sessions',
                            data: @json($reportData['chart_sessions'] ?? []),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Response Time Chart
            if (document.getElementById('responseTimeChart')) {
                const ctx2 = document.getElementById('responseTimeChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: @json($reportData['chart_labels'] ?? []),
                        datasets: [{
                            label: 'Avg Response Time (min)',
                            data: @json($reportData['chart_response_times'] ?? []),
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        @endif
    </script>
    @endpush
</x-layouts.admin>