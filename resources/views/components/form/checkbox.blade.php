<!-- resources/views/components/form/checkbox.blade.php -->
@props(['name', 'label', 'value' => '1', 'checked' => false, 'disabled' => false, 'helper' => null])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <div class="flex">
        <input 
            type="checkbox" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ $value }}"
            {{ old($name, $checked) ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
        >
        <label for="{{ $name }}" class="ml-3 block text-sm {{ $disabled ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-200' }}">
            {{ $label }}
        </label>
    </div>
    
    @if($helper)
        <div class="mt-1 ml-6">
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $helper }}</span>
        </div>
    @endif
    
    @error($name)
        <div class="mt-1 ml-6">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
</div>