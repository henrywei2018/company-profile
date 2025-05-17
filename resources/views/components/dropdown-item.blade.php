<!-- resources/views/components/dropdown-item.blade.php -->
@props(['href' => '#', 'icon' => null])

<a {{ $attributes->merge(['class' => 'flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300', 'href' => $href]) }}>
    @if($icon)
        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            {!! $icon !!}
        </svg>
    @endif
    {{ $slot }}
</a>