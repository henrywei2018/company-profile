{{-- resources/views/components/admin/table-row-actions.blade.php --}}
@props([
    'quickActions' => [],
    'dropdownActions' => [],
    'item'
])

<div class="flex items-center justify-end gap-2">
    <!-- Quick Actions -->
    @foreach($quickActions as $action)
        @if($action['type'] === 'form')
            <form method="POST" action="{{ $action['route'] }}" class="inline">
                @csrf
                @if(isset($action['method']) && $action['method'] !== 'POST')
                    @method($action['method'])
                @endif
                
                @if(isset($action['confirm']))
                    <button type="submit" 
                            onclick="return confirm('{{ $action['confirm'] }}')"
                            class="{{ $action['class'] ?? 'text-gray-600 hover:text-gray-900' }}"
                            title="{{ $action['title'] ?? '' }}">
                        {!! $action['icon'] !!}
                    </button>
                @else
                    <button type="submit" 
                            class="{{ $action['class'] ?? 'text-gray-600 hover:text-gray-900' }}"
                            title="{{ $action['title'] ?? '' }}">
                        {!! $action['icon'] !!}
                    </button>
                @endif
            </form>
        @elseif($action['type'] === 'link')
            <a href="{{ $action['route'] }}" 
               class="{{ $action['class'] ?? 'text-gray-600 hover:text-gray-900' }}"
               title="{{ $action['title'] ?? '' }}">
                {!! $action['icon'] !!}
            </a>
        @endif
    @endforeach

    <!-- Dropdown Menu -->
    @if(is_array($dropdownActions) && count($dropdownActions) > 0)
        <div class="relative inline-block text-left" x-data="{ open: false }">
            <button @click="open = !open" 
                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                </svg>
            </button>

            <div x-show="open" @click.away="open = false" 
                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                <div class="py-1">
                    @foreach($dropdownActions as $action)
                        @if(is_array($action) && isset($action['type']))
                            @if($action['type'] === 'divider')
                                <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            @elseif($action['type'] === 'link')
                                <a href="{{ $action['route'] ?? '#' }}" 
                                   class="flex items-center px-4 py-2 text-sm {{ $action['class'] ?? 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    @if(isset($action['icon']))
                                        {!! $action['icon'] !!}
                                    @endif
                                    {{ $action['label'] ?? 'Action' }}
                                </a>
                            @elseif($action['type'] === 'form')
                                <form method="POST" action="{{ $action['route'] ?? '#' }}" class="inline w-full"
                                      @if(isset($action['confirm'])) onsubmit="return confirm('{{ addslashes($action['confirm']) }}')" @endif>
                                    @csrf
                                    @if(isset($action['method']) && $action['method'] !== 'POST')
                                        @method($action['method'])
                                    @endif
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm {{ $action['class'] ?? 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} text-left">
                                        @if(isset($action['icon']))
                                            {!! $action['icon'] !!}
                                        @endif
                                        {{ $action['label'] ?? 'Action' }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>