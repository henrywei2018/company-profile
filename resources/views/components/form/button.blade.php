<!-- resources/views/components/form/buttons.blade.php -->
@props(['cancelRoute' => null, 'submitText' => 'Save', 'cancelText' => 'Cancel'])

<div class="flex items-center justify-end space-x-4 mt-6">
    @if($cancelRoute)
        <a href="{{ $cancelRoute }}" class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-gray-800 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800">
            {{ $cancelText }}
        </a>
    @endif
    
    <button type="submit" class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm dark:focus:ring-offset-gray-800">
        {{ $submitText }}
    </button>
</div>