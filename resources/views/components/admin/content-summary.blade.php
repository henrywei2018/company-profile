{{-- resources/views/components/admin/content-summary.blade.php --}}
@props([
    'title',
    'subtitle' => null,
    'description' => null,
    'link' => null,
    'linkText' => null,
    'badges' => [],
    'meta' => [],
    'titleClass' => 'text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate'
])

<div class="flex-1 min-w-0">
    <div class="flex items-center gap-2">
        @if($link)
            <a href="{{ $link }}" class="{{ $titleClass }}">
                {{ $title }}
            </a>
        @else
            <span class="{{ str_replace(['hover:text-blue-600', 'dark:hover:text-blue-400'], '', $titleClass) }}">
                {{ $title }}
            </span>
        @endif
        
        @foreach($badges as $badge)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] ?? 'bg-gray-100 text-gray-800' }}">
                @if(isset($badge['icon']))
                    {!! $badge['icon'] !!}
                @endif
                {{ $badge['text'] }}
            </span>
        @endforeach
    </div>
    
    @if($subtitle)
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $subtitle }}
        </p>
    @endif
    
    @if($description)
        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1 line-clamp-2">
            {{ Str::limit($description, 100) }}
        </p>
    @endif
    
    @if($linkText && $link)
        <div class="flex items-center gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                {{ $linkText }}
            </span>
        </div>
    @endif
    
    @if(count($meta) > 0)
        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
            @foreach($meta as $metaItem)
                <span>{{ $metaItem }}</span>
            @endforeach
        </div>
    @endif
</div>