<!-- resources/views/components/form/file-input.blade.php -->
@props(['name', 'label', 'required' => false, 'disabled' => false, 'helper' => null, 'accept' => null, 'preview' => null])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-200' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    @if($preview)
        <div class="mb-3">
            <img src="{{ $preview }}" alt="Preview" class="h-24 w-auto object-cover rounded-md">
        </div>
    @endif
    
    <label for="{{ $name }}" class="group block p-4 text-center border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 dark:border-gray-700">
        <input 
            type="file" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $accept ? 'accept=' . $accept : '' }}
            class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-400"
        >
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ $slot->isEmpty() ? 'Drag and drop files here or click to upload' : $slot }}
        </p>
    </label>
    
    @if($helper)
        <div class="mt-1">
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $helper }}</span>
        </div>
    @endif
    
    @error($name)
        <div class="mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
</div>