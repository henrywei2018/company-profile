{{-- resources/views/components/public/breadcrumb.blade.php --}}
@props(['items' => []])

@if(count($items) > 1)
<nav class="bg-gray-50 border-b border-gray-200 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center space-x-2 text-sm">
            @foreach($items as $index => $item)
                @if($index === 0)
                    {{-- Home link --}}
                    <li>
                        <a href="{{ $item['url'] }}" 
                           class="text-gray-500 hover:text-orange-600 transition-colors duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"/>
                            </svg>
                        </a>
                    </li>
                @elseif($index === count($items) - 1)
                    {{-- Current page --}}
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-gray-900 font-medium">{{ $item['name'] }}</span>
                    </li>
                @else
                    {{-- Intermediate pages --}}
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        @if(isset($item['url']) && $item['url'])
                        <a href="{{ $item['url'] }}" 
                           class="text-gray-500 hover:text-orange-600 transition-colors duration-300">
                            {{ $item['name'] }}
                        </a>
                        @else
                        <span class="text-gray-500">{{ $item['name'] }}</span>
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>
@endif