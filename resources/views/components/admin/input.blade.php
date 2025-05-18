<!-- resources/views/components/admin/input.blade.php -->
@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'disabled' => false,
    'readonly' => false,
    'required' => false,
    'helper' => null,
    'error' => null,
    'prefix' => null,
    'suffix' => null,
    'id' => null
])

@php
    $id = $id ?? $name;
    $errorClasses = $errors->has($name) ? 'border-red-300 dark:border-red-500 focus:border-red-500 focus:ring-red-500 dark:focus:border-red-500 dark:focus:ring-red-500' : 'border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500';
    $inputClasses = 'py-3 px-4 block w-full rounded-md text-sm ' . $errorClasses . ' ' . ($disabled ? 'bg-gray-100 dark:bg-neutral-900 cursor-not-allowed' : 'bg-white dark:bg-neutral-800');
    
    // Add prefix/suffix padding
    $prefixPadding = $prefix ? 'ps-10' : '';
    $suffixPadding = $suffix ? 'pe-10' : '';
@endphp

<div class="mb-4">
    <label for="{{ $id }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div class="relative">
        @if($prefix)
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none z-20">
            {!! $prefix !!}
        </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $required ? 'required' : '' }}
            class="{{ $inputClasses }} {{ $prefixPadding }} {{ $suffixPadding }}"
            {{ $attributes }}
        >
        
        @if($suffix)
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none z-20">
            {!! $suffix !!}
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