<!-- resources/views/components/admin/toggle.blade.php -->
@props([
    'name',
    'label' => null,
    'labelPosition' => 'right', // Options: left, right
    'value' => '1',
    'checked' => false,
    'disabled' => false, 
    'helper' => null,
    'error' => null,
    'size' => 'md', // Options: sm, md, lg
    'color' => 'blue', // Options: blue, green, red, amber, gray
    'id' => null,
    'onChange' => null // JavaScript function to call on change
])

@php
    $id = $id ?? $name;
    $isChecked = old($name, $checked);
    
    // Size classes
    $sizeClasses = [
        'sm' => 'h-5 w-9',
        'md' => 'h-6 w-11',
        'lg' => 'h-7 w-14'
    ][$size] ?? 'h-6 w-11';
    
    // Toggle position classes
    $togglePositionClasses = [
        'sm' => $isChecked ? 'translate-x-4' : 'translate-x-0',
        'md' => $isChecked ? 'translate-x-5' : 'translate-x-0',
        'lg' => $isChecked ? 'translate-x-7' : 'translate-x-0'
    ][$size] ?? ($isChecked ? 'translate-x-5' : 'translate-x-0');
    
    // Toggle size classes
    $toggleSizeClasses = [
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6'
    ][$size] ?? 'h-5 w-5';
    
    // Color classes for the background
    $colorClasses = [
        'blue' => $isChecked ? 'bg-blue-600 dark:bg-blue-700' : 'bg-gray-200 dark:bg-gray-700',
        'green' => $isChecked ? 'bg-green-600 dark:bg-green-700' : 'bg-gray-200 dark:bg-gray-700',
        'red' => $isChecked ? 'bg-red-600 dark:bg-red-700' : 'bg-gray-200 dark:bg-gray-700',
        'amber' => $isChecked ? 'bg-amber-600 dark:bg-amber-700' : 'bg-gray-200 dark:bg-gray-700',
        'gray' => $isChecked ? 'bg-gray-600 dark:bg-gray-500' : 'bg-gray-200 dark:bg-gray-700'
    ][$color] ?? 'bg-blue-600 dark:bg-blue-700';
    
    // Focus ring color
    $focusRingColor = [
        'blue' => 'focus:ring-blue-600',
        'green' => 'focus:ring-green-600',
        'red' => 'focus:ring-red-600',
        'amber' => 'focus:ring-amber-600',
        'gray' => 'focus:ring-gray-600'
    ][$color] ?? 'focus:ring-blue-600';
@endphp

<div class="mb-4">
    <div class="flex items-center {{ $labelPosition === 'left' ? 'flex-row-reverse justify-end' : '' }} {{ $labelPosition === 'left' ? 'space-x-reverse space-x-3' : 'space-x-3' }}">
        <div>
            <button 
                type="button" 
                role="switch"
                aria-checked="{{ $isChecked ? 'true' : 'false' }}"
                {{ $disabled ? 'disabled' : '' }}
                x-data="{ checked: {{ $isChecked ? 'true' : 'false' }} }"
                x-init="$watch('checked', value => { 
                    $refs.hiddenInput.checked = value;
                    @if($onChange) {{ $onChange }}(value); @endif
                })"
                @click="checked = !checked"
                :class="{ '{{ $colorClasses }}': true }"
                class="relative inline-flex shrink-0 {{ $sizeClasses }} border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 {{ $focusRingColor }} focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
            >
                <span 
                    aria-hidden="true"
                    :class="{ '{{ $togglePositionClasses }}': true }"
                    class="pointer-events-none inline-block {{ $toggleSizeClasses }} rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                ></span>
            </button>
            <input 
                type="checkbox"
                id="{{ $id }}"
                name="{{ $name }}"
                value="{{ $value }}"
                {{ $isChecked ? 'checked' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                x-ref="hiddenInput"
                class="hidden"
            >
        </div>
        
        @if($label)
            <label for="{{ $id }}" class="text-sm {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
                {{ $label }}
            </label>
        @endif
    </div>
    
    @if($helper && !$errors->has($name))
        <div class="{{ $labelPosition === 'left' ? 'text-right' : 'ml-12' }} mt-1">
            <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</span>
        </div>
    @endif
    
    @error($name)
        <div class="{{ $labelPosition === 'left' ? 'text-right' : 'ml-12' }} mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
</div>