<!-- resources/views/components/form/date-picker.blade.php -->
@props(['name', 'label', 'value' => null, 'required' => false, 'disabled' => false, 'helper' => null, 'format' => 'YYYY-MM-DD'])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-200' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div 
        x-data="{ 
            value: @js(old($name, $value)),
            format: @js($format),
            init() {
                flatpickr(this.$refs.input, {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    altInput: true,
                    altFormat: 'F j, Y',
                    defaultDate: this.value,
                    disable: @js($disabled)
                });
            }
        }"
    >
        <div class="relative">
            <input 
                type="text" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                x-ref="input"
                x-model="value"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                class="py-3 px-4 pl-11 block w-full border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400{{ $disabled ? ' bg-gray-100 dark:bg-gray-700' : '' }}"
            >
            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none pl-4">
                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
            </div>
        </div>
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

@once
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush
@endonce