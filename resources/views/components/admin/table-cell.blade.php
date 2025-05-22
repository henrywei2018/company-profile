<!-- resources/views/components/admin/table-cell.blade.php -->
@props(['highlight' => false])

<td {{ $attributes->merge(['class' => 'px-6 py-4 whitespace-nowrap text-sm ' . 
    ($highlight 
        ? 'font-medium text-gray-900 dark:text-white' 
        : 'text-gray-700 dark:text-gray-300')
    ]) }}>
    {{ $slot }}
</td>