<!-- resources/views/components/dashboard-stats.blade.php -->
@props(['stats'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($stats as $stat)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-md p-3 {{ $stat['bg_color'] }}">
                    <svg class="h-6 w-6 {{ $stat['icon_color'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        {!! $stat['icon'] !!}
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            {{ $stat['title'] }}
                        </dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $stat['value'] }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        @if(isset($stat['change']))
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-2">
            <div class="text-sm flex items-center">
                @if($stat['change'] > 0)
                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
                <span class="text-green-600 dark:text-green-400">{{ $stat['change'] }}% increase</span>
                @elseif($stat['change'] < 0)
                <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                <span class="text-red-600 dark:text-red-400">{{ abs($stat['change']) }}% decrease</span>
                @else
                <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                </svg>
                <span class="text-gray-600 dark:text-gray-400">No change</span>
                @endif
                <span class="ml-1 text-gray-500 dark:text-gray-400">from last month</span>
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>