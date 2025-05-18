<!-- resources/views/components/admin/checkbox.blade.php -->
@props([
    'label',
    'name',
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null,
    'id' => null
])

@php
    $id = $id ?? $name;
    $isChecked = old($name, $checked);
@endphp

<div class="mb-4">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input 
                type="checkbox" 
                id="{{ $id }}" 
                name="{{ $name }}" 
                value="{{ $value }}"
                {{ $isChecked ? 'checked' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                class="shrink-0 mt-0.5 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800"
                {{ $attributes }}
            >
        </div>
        <div class="ml-3 text-sm">
            <label for="{{ $id }}" class="{{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
                {{ $label }}
            </label>
            
            @if($helper && !$errors->has($name))
                <p class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</p>
            @endif
            
            @error($name)
                <p class="text-xs text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>