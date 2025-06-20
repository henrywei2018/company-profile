<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AnalyticsComponentsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Analytics Components
        $this->registerAnalyticsComponents();

        // Register Analytics Blade Directives
        $this->registerBladeDirectives();
    }

    /**
     * Register all analytics components
     */
    protected function registerAnalyticsComponents(): void
    {
        // Main analytics components
        Blade::component('analytics.stats-card', \App\View\Components\Analytics\StatsCard::class);
        Blade::component('analytics.chart', \App\View\Components\Analytics\AnalyticsChart::class);
        Blade::component('analytics.table', \App\View\Components\Analytics\AnalyticsTable::class);
        Blade::component('analytics.widget', \App\View\Components\Analytics\AnalyticsWidget::class);
        Blade::component('analytics.realtime-stats', \App\View\Components\Analytics\RealtimeStats::class);

        // UI components
        Blade::component('analytics.period-selector', \App\View\Components\Analytics\PeriodSelector::class);
        Blade::component('analytics.export-button', \App\View\Components\Analytics\ExportButton::class);
        Blade::component('analytics.dashboard-layout', \App\View\Components\Analytics\DashboardLayout::class);
        Blade::component('analytics.health-status', \App\View\Components\Analytics\HealthStatus::class);
        Blade::component('analytics.trend-indicator', \App\View\Components\Analytics\TrendIndicator::class);
        Blade::component('analytics.quick-actions', \App\View\Components\Analytics\QuickActions::class);
    }

    /**
     * Register custom Blade directives for analytics
     */
    protected function registerBladeDirectives(): void
    {
        // @analyticsRoute directive
        Blade::directive('analyticsRoute', function ($expression) {
            return "<?php echo route('admin.gtag.' . {$expression}); ?>";
        });

        // @gtagScript directive for including gtag JavaScript
        Blade::directive('gtagScript', function ($trackingId) {
            return "
                <script async src=\"https://www.googletagmanager.com/gtag/js?id={$trackingId}\"></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', '{$trackingId}');
                </script>
            ";
        });

        // @analyticsWidget directive for quick widget creation
        Blade::directive('analyticsWidget', function ($expression) {
            $params = explode(',', str_replace(['(', ')', "'", '"'], '', $expression));
            $type = trim($params[0] ?? 'visitors');
            $title = trim($params[1] ?? ucfirst($type));
            $period = trim($params[2] ?? '7');

            return "
                <x-analytics.widget 
                    title=\"{$title}\"
                    data-type=\"{$type}\"
                    :period=\"{$period}\"
                    widget-id=\"widget-{$type}\"
                    size=\"medium\" />
            ";
        });

        // @analyticsChart directive for quick chart creation
        Blade::directive('analyticsChart', function ($expression) {
            $params = explode(',', str_replace(['(', ')', "'", '"'], '', $expression));
            $chartId = trim($params[0] ?? 'chart');
            $title = trim($params[1] ?? 'Analytics Chart');
            $type = trim($params[2] ?? 'line');

            return "
                <x-analytics.chart 
                    chart-id=\"{$chartId}\"
                    title=\"{$title}\"
                    type=\"{$type}\"
                    :data=\"\$chartData['{$chartId}'] ?? []\"
                    height=\"300px\" />
            ";
        });

        // @statsCard directive for quick stats card creation
        Blade::directive('statsCard', function ($expression) {
            $params = explode(',', str_replace(['(', ')', "'", '"'], '', $expression));
            $title = trim($params[0] ?? 'Statistic');
            $value = trim($params[1] ?? '0');
            $icon = trim($params[2] ?? 'chart-bar');
            $color = trim($params[3] ?? 'blue');

            return "
                <x-analytics.stats-card 
                    title=\"{$title}\"
                    value=\"{$value}\"
                    icon=\"{$icon}\"
                    color=\"{$color}\" />
            ";
        });

    }

}