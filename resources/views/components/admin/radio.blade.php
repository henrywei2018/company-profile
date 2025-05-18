<!-- resources/views/components/admin/radio.blade.php -->
@props([
    'label',
    'name',
    'options' => [],
    'value' => null,
    'disabled' => false,
    'helper' => null,
    'error' => null,
    'inline' => false
])

@php
    $selectedValue = old($name, $value);
@endphp

<div class="mb-4">
    <div class="mb-2">
        <span class="block text-sm font-medium {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
            {{ $label }}
        </span>
    </div>
    
    <div class="{{ $inline ? 'flex flex-wrap gap-x-6 gap-y-2' : 'space-y-2' }}">
        @foreach($options as $optionValue => $optionLabel)
            <div class="flex items-center">
                <input 
                    type="radio" 
                    id="{{ $name }}_{{ $optionValue }}" 
                    name="{{ $name }}" 
                    value="{{ $optionValue }}"
                    {{ $selectedValue == $optionValue ? 'checked' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    class="shrink-0 mt-0.5 border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800"
                >
                <label for="{{ $name }}_{{ $optionValue }}" class="ml-3 block text-sm {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    </div>
    
    @if($helper && !$errors->has($name))
        <div class="mt-1">
            <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</span>
        </div>
    @endif
    
    @error($name)
        <div class="mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
</div>