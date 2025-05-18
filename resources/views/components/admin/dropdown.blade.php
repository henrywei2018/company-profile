<!-- resources/views/components/admin/dropdown.blade.php -->
@props([
    'trigger' => null,
    'width' => 'w-48',
    'placement' => 'bottom-right',
    'offset' => null,
    'triggerEvents' => 'click', // Options: click, hover
    'transition' => true,
])

@php
    $placementClasses = [
        'top' => '[--placement:top]',
        'top-left' => '[--placement:top-left]',
        'top-right' => '[--placement:top-right]',
        'bottom' => '[--placement:bottom]',
        'bottom-left' => '[--placement:bottom-left]',
        'bottom-right' => '[--placement:bottom-right]',
        'left' => '[--placement:left]',
        'left-top' => '[--placement:left-top]',
        'left-bottom' => '[--placement:left-bottom]',
        'right' => '[--placement:right]',
        'right-top' => '[--placement:right-top]',
        'right-bottom' => '[--placement:right-bottom]',
    ][$placement] ?? '[--placement:bottom-right]';
    
    $offsetAttribute = $offset ? "data-hs-dropdown-offset=\"{$offset}\"" : '';
    $triggerAttribute = "data-hs-dropdown-trigger=\"{$triggerEvents}\"";
    $transitionClasses = $transition ? 'transition-[opacity,margin] duration' : '';
@endphp

<div class="hs-dropdown relative inline-flex {{ $placementClasses }}" {{ $offsetAttribute }} {{ $triggerAttribute }}>
    <div class="hs-dropdown-toggle" id="hs-dropdown-{{ Str::random(6) }}">
        {{ $trigger }}
    </div>

    <div class="hs-dropdown-menu {{ $transitionClasses }} hs-dropdown-open:opacity-100 opacity-0 hidden z-10 {{ $width }} bg-white shadow-md rounded-lg p-2 dark:bg-neutral-800 dark:border dark:border-neutral-700" aria-labelledby="hs-dropdown-{{ Str::random(6) }}">
        {{ $slot }}
    </div>
</div>