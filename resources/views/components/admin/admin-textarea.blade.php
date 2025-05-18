<!-- resources/views/components/admin/textarea.blade.php -->
@props([
    'label',
    'name',
    'value' => null,
    'placeholder' => '',
    'rows' => 3,
    'disabled' => false,
    'readonly' => false,
    'required' => false,
    'helper' => null,
    'error' => null,
    'id' => null
])

@php
    $id = $id ?? $name;
    $errorClasses = $errors->has($name) ? 'border-red-300 dark:border-red-500 focus:border-red-500 focus:ring-red-500 dark:focus:border-red-500 dark:focus:ring-red-500' : 'border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500';
    $textareaClasses = 'py-3 px-4 block w-full rounded-md text-sm ' . $errorClasses . ' ' . ($disabled ? 'bg-gray-100 dark:bg-neutral-900 cursor-not-allowed' : 'bg-white dark:bg-neutral-800');
@endphp

<div class="mb-4">
    <label for="{{ $id }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <textarea 
        id="{{ $id }}" 
        name="{{ $name }}" 
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $required ? 'required' : '' }}
        class="{{ $textareaClasses }}"
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>
    
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