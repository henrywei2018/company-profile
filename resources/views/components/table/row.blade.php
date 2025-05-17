<!-- resources/views/components/table/row.blade.php -->
<tr {{ $attributes->merge(['class' => 'hover:bg-gray-50 dark:hover:bg-gray-700']) }}>
    {{ $slot }}
</tr>