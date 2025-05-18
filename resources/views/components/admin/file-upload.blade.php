<!-- resources/views/components/admin/file-upload.blade.php -->
@props([
    'label',
    'name',
    'accept' => null,
    'multiple' => false,
    'disabled' => false,
    'required' => false,
    'helper' => null,
    'error' => null,
    'id' => null,
    'preview' => null,
    'placeholder' => 'Choose file or drag and drop',
])

@php
    $id = $id ?? $name;
@endphp

<div class="mb-4">
    <label for="{{ $id }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    @if($preview)
        <div class="mb-2">
            @if(is_array($preview))
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($preview as $previewItem)
                        <div class="relative group">
                            <img src="{{ $previewItem }}" alt="Preview" class="h-24 w-full object-cover rounded-md border border-gray-200 dark:border-neutral-700">
                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-md opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <button type="button" class="p-1 bg-red-600 text-white rounded-full hover:bg-red-700 focus:outline-none">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="relative group w-fit">
                    <img src="{{ $preview }}" alt="Preview" class="max-h-48 w-auto object-contain rounded-md border border-gray-200 dark:border-neutral-700">
                </div>
            @endif
        </div>
    @endif
    
    <div class="relative">
        <label for="{{ $id }}" class="group block p-4 text-center border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800">
            <input 
                type="file" 
                id="{{ $id }}" 
                name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
                {{ $multiple ? 'multiple' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $required ? 'required' : '' }}
                {{ $accept ? 'accept=' . $accept : '' }}
                class="sr-only"
                {{ $attributes }}
            >
            
            <div class="flex flex-col items-center justify-center space-y-2">
                <div class="size-12 flex items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-blue-600 dark:text-blue-400">Click to upload</span> or drag and drop
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $slot->isEmpty() ? $placeholder : $slot }}
                </p>
            </div>
        </label>
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