<!-- resources/views/components/admin/select.blade.php -->
@props([
    'label',
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => 'Select an option',
    'disabled' => false,
    'required' => false,
    'helper' => null,
    'error' => null,
    'id' => null,
    'multiple' => false
])

@php
    $id = $id ?? $name;
    $errorClasses = $errors->has($name) ? 'border-red-300 dark:border-red-500 focus:border-red-500 focus:ring-red-500 dark:focus:border-red-500 dark:focus:ring-red-500' : 'border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500';
    $selectClasses = 'py-3 px-4 pr-9 block w-full rounded-md text-sm ' . $errorClasses . ' ' . ($disabled ? 'bg-gray-100 dark:bg-neutral-900 cursor-not-allowed' : 'bg-white dark:bg-neutral-800');
@endphp

<div class="mb-4">
    <label for="{{ $id }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div class="relative">
        <select 
            id="{{ $id }}" 
            name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
            {{ $disabled ? 'disabled' : '' }}
            {{ $required ? 'required' : '' }}
            {{ $multiple ? 'multiple' : '' }}
            class="{{ $selectClasses }}"
            {{ $attributes }}
        >
            @if(!$multiple && $placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $optionValue => $optionLabel)
                @if(is_array($optionLabel))
                    <optgroup label="{{ $optionValue }}">
                        @foreach($optionLabel as $groupOptionValue => $groupOptionLabel)
                            <option 
                                value="{{ $groupOptionValue }}" 
                                @if($multiple && is_array($value))
                                    {{ in_array($groupOptionValue, $value) ? 'selected' : '' }}
                                @else
                                    {{ $value == $groupOptionValue ? 'selected' : '' }}
                                @endif
                            >
                                {{ $groupOptionLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option 
                        value="{{ $optionValue }}" 
                        @if($multiple && is_array($value))
                            {{ in_array($optionValue, $value) ? 'selected' : '' }}
                        @else
                            {{ $value == $optionValue ? 'selected' : '' }}
                        @endif
                    >
                        {{ $optionLabel }}
                    </option>
                @endif
            @endforeach
        </select>
        
        @if(!$multiple)
        <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none pr-3">
            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
        @endif
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