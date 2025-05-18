
<!-- resources/views/components/admin/dropdown-item.blade.php -->
@props([
    'href' => '#',
    'icon' => null,
    'type' => 'link',  // link, button, form
    'method' => 'POST',
    'action' => null,
    'confirm' => false,
    'confirmMessage' => 'Are you sure you want to perform this action?',
    'disabled' => false,
])

@php
    $baseClasses = 'flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300';
    
    if ($disabled) {
        $baseClasses .= ' opacity-50 pointer-events-none';
    }
@endphp

@if($type === 'link')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($icon)
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </a>
@elseif($type === 'button')
    <button type="button" {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if($icon)
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </button>
@elseif($type === 'form')
    <form action="{{ $action }}" method="POST" class="w-full">
        @csrf
        @method($method)
        <button type="submit" {{ $attributes->merge(['class' => $baseClasses . ' w-full text-left']) }}
            @if($confirm) onclick="return confirm('{{ $confirmMessage }}')" @endif
        >
            @if($icon)
                <span class="shrink-0">{!! $icon !!}</span>
            @endif
            {{ $slot }}
        </button>
    </form>
@endif