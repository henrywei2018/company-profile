{{-- resources/views/components/analytics/export-button.blade.php --}}
<a href="{{ $getExportUrl() }}" 
   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <x-icon name="download" class="w-4 h-4 mr-2" />
    {{ $label }}
</a>