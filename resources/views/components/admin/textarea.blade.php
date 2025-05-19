@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'rows' => 4,
    'value' => '',
])

@php
    $inputId = $attributes->get('id', $name);
    $error = $errors->has($name);
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
            {{ $label }}
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $inputId }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="block w-full rounded-md shadow-sm border-gray-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 text-sm {{ $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '' }}"
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
