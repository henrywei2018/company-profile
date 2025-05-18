<!-- resources/views/components/admin/chart.blade.php -->
@props([
    'id' => 'chart-' . uniqid(),
    'type' => 'line', // Options: line, area, bar, pie, donut, radar, polarArea, heatmap
    'height' => 350,
    'width' => '100%',
    'data' => [],
    'labels' => [],
    'colors' => [],
    'options' => [],
    'sparkline' => false,
    'stacked' => false,
    'autoUpdate' => false,
    'updateInterval' => 5000,
    'asyncUrl' => null
])

<div {{ $attributes }}>
    <div id="{{ $id }}" style="height: {{ $height }}px; width: {{ $width }}"></div>
    
    @if($autoUpdate && $asyncUrl)
        <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
            <div class="animate-pulse mr-1 size-2 rounded-full bg-blue-600 dark:bg-blue-500"></div>
            <span>Auto-updating</span>
        </div>
    @endif
</div>

@once
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.min.js"></script>
@endpush
@endonce

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Default chart options
        const defaultOptions = {
            chart: {
                type: '{{ $type }}',
                height: {{ $height }},
                width: '{{ $width }}',
                toolbar: {
                    show: {{ $sparkline ? 'false' : 'true' }},
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                },
                sparkline: {
                    enabled: {{ $sparkline ? 'true' : 'false' }}
                }
            },
            colors: @json($colors ?: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']),
            dataLabels: {
                enabled: {{ in_array($type, ['pie', 'donut', 'radialBar', 'polarArea']) ? 'true' : 'false' }}
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            series: @json($data),
            labels: @json($labels),
            xaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '12px'
                    }
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4,
                xaxis: {
                    lines: {
                        show: false
                    }
                }
            },
            legend: {
                labels: {
                    colors: '#6b7280'
                }
            },
            tooltip: {
                theme: 'light'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '60%',
                    borderRadius: 3,
                    distributed: false
                },
                pie: {
                    donut: {
                        labels: {
                            show: true
                        }
                    }
                },
                radialBar: {
                    hollow: {
                        size: '70%',
                    }
                }
            },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            },
            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 300
                        },
                        legend: {
                            show: false
                        }
                    }
                }
            ]
        };
        
        // Merge user options with defaults
        const chartOptions = { 
            ...defaultOptions, 
            ...@json($options) 
        };
        
        // Apply stacked property if needed
        if ({{ $stacked ? 'true' : 'false' }}) {
            chartOptions.chart.stacked = true;
        }
        
        // Create the chart
        const chart = new ApexCharts(document.getElementById('{{ $id }}'), chartOptions);
        chart.render();
        
        // Function to update chart for dark/light mode
        const updateChartForTheme = () => {
            const isDarkMode = document.documentElement.classList.contains('dark');
            
            chart.updateOptions({
                theme: {
                    mode: isDarkMode ? 'dark' : 'light'
                },
                grid: {
                    borderColor: isDarkMode ? '#404040' : '#e5e7eb'
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9ca3af' : '#6b7280'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: isDarkMode ? '#9ca3af' : '#6b7280'
                        }
                    }
                },
                legend: {
                    labels: {
                        colors: isDarkMode ? '#9ca3af' : '#6b7280'
                    }
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light'
                }
            });
        };
        
        // Apply theme changes on load
        updateChartForTheme();
        
        // Listen for dark mode changes
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.attributeName === 'class') {
                    updateChartForTheme();
                }
            });
        });
        
        observer.observe(document.documentElement, { attributes: true });
        
        // Auto-update functionality
        @if($autoUpdate && $asyncUrl)
            const fetchDataAndUpdate = async () => {
                try {
                    const response = await fetch('{{ $asyncUrl }}');
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();
                    
                    // Update chart with new data
                    chart.updateSeries(data.series || data.data);
                    
                    // Update additional options if available
                    if (data.labels) {
                        chart.updateOptions({ labels: data.labels });
                    }
                    
                } catch (error) {
                    console.error('Error updating chart:', error);
                }
            };
            
            // Initial fetch
            fetchDataAndUpdate();
            
            // Set interval for updates
            const updateIntervalId = setInterval(fetchDataAndUpdate, {{ $updateInterval }});
            
            // Clean up interval on page unload
            window.addEventListener('beforeunload', () => {
                clearInterval(updateIntervalId);
            });
        @endif
    });
</script>
@endpush"
  }