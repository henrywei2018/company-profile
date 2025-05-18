<!-- resources/views/components/admin/card.blade.php -->
@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'headerActions' => null,
    'noPadding' => false,
    'bodyClass' => ''
])

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700']) }}>
    @if($title || $subtitle || $headerActions)
    <div class=\"px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex flex-wrap justify-between items-center gap-3\">
        <div>
            @if($title)
            <h3 class=\"text-lg font-medium text-gray-900 dark:text-white\">
                {{ $title }}
            </h3>
            @endif
            
            @if($subtitle)
            <p class=\"mt-1 text-sm text-gray-500 dark:text-neutral-400\">
                {{ $subtitle }}
            </p>
            @endif
        </div>
        
        @if($headerActions)
        <div class=\"flex items-center gap-2\">
            {{ $headerActions }}
        </div>
        @endif
    </div>
    @endif
    
    <div class=\"{{ $noPadding ? '' : 'p-6' }} {{ $bodyClass }}\">
        {{ $slot }}
    </div>
    
    @if($footer)
    <div class=\"px-6 py-4 border-t border-gray-200 dark:border-neutral-700\">
        {{ $footer }}
    </div>
    @endif
</div>