<!-- resources/views/components/form/radio-group.blade.php -->
@props(['name', 'label', 'options' => [], 'selected' => null, 'required' => false, 'disabled' => false, 'helper' => null, 'inline' => false])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-200' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div class="{{ $inline ? 'flex space-x-6' : 'space-y-2' }}">
        @foreach($options as $value => $optionLabel)
            <x-form.radio 
                name="{{ $name }}" 
                label="{{ $optionLabel }}" 
                value="{{ $value }}" 
                :checked="old($name, $selected) == $value"
                :disabled="$disabled"
            />
        @endforeach
    </div>
    
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