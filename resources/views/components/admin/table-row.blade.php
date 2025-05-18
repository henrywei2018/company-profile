<!-- resources/views/components/admin/table-row.blade.php -->
@props(['selected' => false, 'clickable' => false])

<tr {{ $attributes->merge([
    'class' => 
        ($selected ? 'bg-blue-50 dark:bg-blue-900/20' : '') . 
        ($clickable ? ' cursor-pointer' : '') . 
        ' hover:bg-gray-50 dark:hover:bg-neutral-700'
    ]) }}
>
    {{ $slot }}
</tr>