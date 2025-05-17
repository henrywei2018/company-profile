<!-- resources/views/components/tab.blade.php -->
@props(['id', 'label', 'active' => false])

<button type="button" role="tab" id="{{ $id }}-tab" 
    {{ $attributes->merge([
        'class' => 'hs-tab-active:font-semibold hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-4 px-1 inline-flex items-center gap-2 border-b-[3px] border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 active dark:text-gray-400 dark:hover:text-blue-500',
        'data-hs-tab' => '#' . $id,
        'aria-controls' => $id,
        'aria-selected' => $active ? 'true' : 'false'
    ]) }}>
    {{ $label }}
</button>