<!-- resources/views/components/form/radio.blade.php -->
@props(['name', 'label', 'value', 'checked' => false, 'disabled' => false])

<div class="flex">
    <input 
        type="radio" 
        id="{{ $name }}_{{ $value }}" 
        name="{{ $name }}" 
        value="{{ $value }}"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
    >
    <label for="{{ $name }}_{{ $value }}" class="ml-3 block text-sm {{ $disabled ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-200' }}">
        {{ $label }}
    </label>
</div>