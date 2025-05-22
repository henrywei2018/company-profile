
<!-- resources/views/components/admin/table-column.blade.php -->
@props([
    'sortable' => false, 
    'direction' => null, 
    'field' => null
])

<th scope="col" {{ $attributes->merge(['class' => 'px-2 py-2 text-start text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider']) }}>
    @if($sortable && $field)
        <a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex items-center gap-x-2">
            <span>{{ $slot }}</span>
            <span class="flex items-center gap-x-2">
                @if($direction === 'asc')
                    <svg class="h-3 w-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                @elseif($direction === 'desc')
                    <svg class="h-3 w-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg class="h-3 w-3 text-gray-400 group-hover:text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                @endif
            </span>
        </a>
    @else
        {{ $slot }}
    @endif
</th>