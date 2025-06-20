{{-- resources/views/components/analytics/period-selector.blade.php --}}
<div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
    @foreach($periods as $days => $label)
        <button type="button"
                class="period-btn px-4 py-2 text-sm font-medium {{ $currentPeriod === $days ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} 
                       {{ $loop->first ? 'rounded-l-lg' : '' }} {{ $loop->last ? 'rounded-r-lg' : '' }} 
                       {{ !$loop->last ? 'border-r border-gray-200 dark:border-gray-600' : '' }}"
                data-period="{{ $days }}"
                data-target="{{ $target }}">
            {{ $label }}
        </button>
    @endforeach
</div>