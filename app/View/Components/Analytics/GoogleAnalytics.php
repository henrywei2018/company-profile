<?php

namespace App\View\Components\Analytics;

use Illuminate\View\Component;

/**
 * Analytics Stats Card Component
 */
class StatsCard extends Component
{
    public string $title;
    public string $value;
    public string $icon;
    public ?string $trend;
    public string $color;
    public ?string $subtitle;

    public function __construct(
        string $title,
        string $value,
        string $icon = 'chart-bar',
        ?string $trend = null,
        string $color = 'blue',
        ?string $subtitle = null
    ) {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->trend = $trend;
        $this->color = $color;
        $this->subtitle = $subtitle;
    }

    public function render()
    {
        return view('components.analytics.stats-card');
    }

    public function getTrendClass(): string
    {
        if (!$this->trend) return '';
        
        $trend = (float) str_replace(['%', '+'], '', $this->trend);
        
        if ($trend > 0) return 'text-green-600 bg-green-100';
        if ($trend < 0) return 'text-red-600 bg-red-100';
        
        return 'text-gray-600 bg-gray-100';
    }

    public function getTrendIcon(): string
    {
        if (!$this->trend) return '';
        
        $trend = (float) str_replace(['%', '+'], '', $this->trend);
        
        if ($trend > 0) return 'arrow-up';
        if ($trend < 0) return 'arrow-down';
        
        return 'minus';
    }
}

/**
 * Analytics Chart Component
 */
class AnalyticsChart extends Component
{
    public string $chartId;
    public string $title;
    public string $type;
    public array $data;
    public string $height;
    public array $options;

    public function __construct(
        string $chartId,
        string $title,
        string $type = 'line',
        array $data = [],
        string $height = '300px',
        array $options = []
    ) {
        $this->chartId = $chartId;
        $this->title = $title;
        $this->type = $type;
        $this->data = $data;
        $this->height = $height;
        $this->options = $options;
    }

    public function render()
    {
        return view('components.analytics.chart');
    }

    public function getChartData(): string
    {
        return json_encode($this->data);
    }

    public function getChartOptions(): string
    {
        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['position' => 'top'],
                'title' => ['display' => true, 'text' => $this->title]
            ]
        ];

        return json_encode(array_merge($defaultOptions, $this->options));
    }
}

/**
 * Analytics Table Component
 */
class AnalyticsTable extends Component
{
    public string $title;
    public array $headers;
    public array $data;
    public string $type;
    public bool $showExport;

    public function __construct(
        string $title,
        array $headers,
        array $data = [],
        string $type = 'general',
        bool $showExport = true
    ) {
        $this->title = $title;
        $this->headers = $headers;
        $this->data = $data;
        $this->type = $type;
        $this->showExport = $showExport;
    }

    public function render()
    {
        return view('components.analytics.table');
    }

    public function getExportUrl(): string
    {
        return route('admin.gtag.export', ['type' => $this->type]);
    }
}

/**
 * Analytics Widget Container
 */
class AnalyticsWidget extends Component
{
    public string $title;
    public string $dataType;
    public int $period;
    public string $widgetId;
    public bool $autoRefresh;
    public string $size;

    public function __construct(
        string $title,
        string $dataType,
        int $period = 7,
        ?string $widgetId = null,
        bool $autoRefresh = false,
        string $size = 'medium'
    ) {
        $this->title = $title;
        $this->dataType = $dataType;
        $this->period = $period;
        $this->widgetId = $widgetId ?? 'widget-' . uniqid();
        $this->autoRefresh = $autoRefresh;
        $this->size = $size;
    }

    public function render()
    {
        return view('components.analytics.widget');
    }

    public function getWidgetUrl(): string
    {
        return route('gtag.widget', ['type' => $this->dataType]);
    }

    public function getSizeClasses(): string
    {
        return match($this->size) {
            'small' => 'col-span-1',
            'medium' => 'col-span-2',
            'large' => 'col-span-3',
            'full' => 'col-span-full',
            default => 'col-span-2'
        };
    }
}

/**
 * Real-time Analytics Component
 */
class RealtimeStats extends Component
{
    public array $stats;
    public bool $autoRefresh;
    public int $refreshInterval;

    public function __construct(
        array $stats = [],
        bool $autoRefresh = true,
        int $refreshInterval = 60
    ) {
        $this->stats = $stats;
        $this->autoRefresh = $autoRefresh;
        $this->refreshInterval = $refreshInterval;
    }

    public function render()
    {
        return view('components.analytics.realtime-stats');
    }

    public function getRealtimeUrl(): string
    {
        return route('gtag.live-stats');
    }
}

/**
 * Analytics Period Selector Component
 */
class PeriodSelector extends Component
{
    public int $currentPeriod;
    public array $periods;
    public string $target;

    public function __construct(
        int $currentPeriod = 7,
        ?array $periods = null,
        string $target = 'analytics-dashboard'
    ) {
        $this->currentPeriod = $currentPeriod;
        $this->periods = $periods ?? [
            1 => 'Today',
            7 => 'Last 7 days',
            30 => 'Last 30 days',
            90 => 'Last 3 months'
        ];
        $this->target = $target;
    }

    public function render()
    {
        return view('components.analytics.period-selector');
    }
}

/**
 * Analytics Export Button Component
 */
class ExportButton extends Component
{
    public string $type;
    public string $label;
    public string $format;
    public array $params;

    public function __construct(
        string $type,
        ?string $label = null,
        string $format = 'csv',
        array $params = []
    ) {
        $this->type = $type;
        $this->label = $label ?? ucfirst($type) . ' Data';
        $this->format = $format;
        $this->params = $params;
    }

    public function render()
    {
        return view('components.analytics.export-button');
    }

    public function getExportUrl(): string
    {
        return route('admin.gtag.export', array_merge(['type' => $this->type], $this->params));
    }
}

/**
 * Analytics Dashboard Layout Component
 */
class DashboardLayout extends Component
{
    public string $title;
    public bool $showPeriodSelector;
    public bool $showExportOptions;
    public bool $showRefreshButton;

    public function __construct(
        string $title = 'Analytics Dashboard',
        bool $showPeriodSelector = true,
        bool $showExportOptions = true,
        bool $showRefreshButton = true
    ) {
        $this->title = $title;
        $this->showPeriodSelector = $showPeriodSelector;
        $this->showExportOptions = $showExportOptions;
        $this->showRefreshButton = $showRefreshButton;
    }

    public function render()
    {
        return view('components.analytics.dashboard-layout');
    }
}

/**
 * Analytics Health Status Component
 */
class HealthStatus extends Component
{
    public ?array $health;
    public bool $showDetails;

    public function __construct(?array $health = null, bool $showDetails = false)
    {
        $this->health = $health;
        $this->showDetails = $showDetails;
    }

    public function render()
    {
        return view('components.analytics.health-status');
    }

    public function getHealthUrl(): string
    {
        return route('gtag.health');
    }

    public function getStatusClass(): string
    {
        if (!$this->health) return 'bg-gray-100 text-gray-600';
        
        $status = $this->health['status'] ?? 'unknown';
        
        return match($status) {
            'healthy' => 'bg-green-100 text-green-700',
            'warning' => 'bg-yellow-100 text-yellow-700',
            'error' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-600'
        };
    }
}

/**
 * Analytics Trend Indicator Component
 */
class TrendIndicator extends Component
{
    public float $value;
    public string $label;
    public string $period;
    public bool $showIcon;

    public function __construct(
        float $value,
        string $label = 'Growth',
        string $period = 'vs last period',
        bool $showIcon = true
    ) {
        $this->value = $value;
        $this->label = $label;
        $this->period = $period;
        $this->showIcon = $showIcon;
    }

    public function render()
    {
        return view('components.analytics.trend-indicator');
    }

    public function getTrendClass(): string
    {
        if ($this->value > 0) return 'text-green-600';
        if ($this->value < 0) return 'text-red-600';
        return 'text-gray-600';
    }

    public function getTrendIcon(): string
    {
        if ($this->value > 0) return 'arrow-up';
        if ($this->value < 0) return 'arrow-down';
        return 'minus';
    }

    public function getFormattedValue(): string
    {
        $prefix = $this->value > 0 ? '+' : '';
        return $prefix . number_format($this->value, 1) . '%';
    }
}

/**
 * Analytics Quick Actions Component
 */
class QuickActions extends Component
{
    public array $actions;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions ?: [
            [
                'label' => 'Refresh Data',
                'action' => 'refresh',
                'icon' => 'refresh',
                'color' => 'blue'
            ],
            [
                'label' => 'Clear Cache',
                'action' => 'clear-cache',
                'icon' => 'trash',
                'color' => 'red'
            ],
            [
                'label' => 'Export Report',
                'action' => 'export',
                'icon' => 'download',
                'color' => 'green'
            ]
        ];
    }

    public function render()
    {
        return view('components.analytics.quick-actions');
    }
}