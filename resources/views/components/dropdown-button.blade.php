<!-- resources/views/components/dropdown-button.blade.php -->
@props(['icon' => null, 'method' => 'POST', 'action' => '#', 'confirm' => false, 'confirmMessage' => 'Are you sure?'])

@if($confirm)
    <button type="button" {{ $attributes->merge(['class' => 'flex w-full items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300']) }} 
        onclick="confirm('{{ $confirmMessage }}') && document.getElementById('{{ md5($action) }}').submit();">
        @if($icon)
            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    </button>
@else
    <button type="button" {{ $attributes->merge(['class' => 'flex w-full items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300']) }} 
        onclick="document.getElementById('{{ md5($action) }}').submit();">
        @if($icon)
            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif

<form id="{{ md5($action) }}" action="{{ $action }}" method="POST" class="hidden">
    @csrf
    @method($method)
</form>