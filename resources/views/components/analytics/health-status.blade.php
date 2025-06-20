{{-- resources/views/components/analytics/health-status.blade.php --}}
<div class="health-status" data-health-url="{{ $getHealthUrl() }}">
    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $getStatusClass() }}">
        <div class="w-2 h-2 rounded-full mr-2 {{ $health && $health['status'] === 'healthy' ? 'bg-green-400 animate-pulse' : 'bg-gray-400' }}"></div>
        <span class="health-status-text">
            @if($health)
                {{ ucfirst($health['status']) }}
            @else
                Checking...
            @endif
        </span>
        
        @if($showDetails && $health)
            <button class="ml-2 text-xs underline" onclick="toggleHealthDetails()">
                Details
            </button>
        @endif
    </div>
    
    @if($showDetails && $health)
        <div id="health-details" class="hidden mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-xs">
            <div><strong>Message:</strong> {{ $health['message'] ?? 'No message' }}</div>
            <div><strong>Last Update:</strong> {{ $health['last_update'] ?? 'Unknown' }}</div>
            @if(isset($health['sample_data']))
                <div><strong>Sample Data:</strong></div>
                <ul class="ml-4 mt-1">
                    @foreach($health['sample_data'] as $key => $value)
                        <li>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>