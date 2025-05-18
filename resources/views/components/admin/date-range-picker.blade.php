<!-- resources/views/components/admin/date-range-picker.blade.php -->
@props([
    'name',
    'label' => null,
    'startDate' => null,
    'endDate' => null,
    'minDate' => null,
    'maxDate' => null,
    'format' => 'Y-m-d',
    'displayFormat' => 'F j, Y',
    'separator' => ' to ',
    'disabled' => false,
    'required' => false,
    'helper' => null,
    'id' => null,
    'placeholder' => 'Select date range',
    'showMonths' => 2,
    'mode' => 'range' // Options: range, single
])

@php
    $id = $id ?? $name;
    $startName = $name . '_start';
    $endName = $name . '_end';
    $errorClasses = $errors->has($name) || $errors->has($startName) || $errors->has($endName) 
        ? 'border-red-300 dark:border-red-500 focus:border-red-500 focus:ring-red-500 dark:focus:border-red-500 dark:focus:ring-red-500' 
        : 'border-gray-300 dark:border-neutral-700 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500';
    $inputClasses = 'py-3 px-4 block w-full rounded-md text-sm ' . $errorClasses . ' ' . ($disabled ? 'bg-gray-100 dark:bg-neutral-900 cursor-not-allowed' : 'bg-white dark:bg-neutral-800');
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div 
        x-data="{
            picker: null,
            formattedDate: '',
            startDate: @js($startDate),
            endDate: @js($endDate),
            init() {
                const self = this;
                this.picker = flatpickr(this.$refs.input, {
                    mode: @js($mode),
                    showMonths: {{ $showMonths }},
                    dateFormat: @js($format),
                    altInput: true,
                    altFormat: @js($displayFormat),
                    defaultDate: [this.startDate, this.endDate].filter(Boolean),
                    minDate: @js($minDate),
                    maxDate: @js($maxDate),
                    rangeSeparator: @js($separator),
                    disable: []
                });
                
                // Watch for external changes to date values
                this.$watch('startDate', value => {
                    if (value !== this.picker.selectedDates[0]) {
                        if (@js($mode) === 'range' && this.endDate) {
                            this.picker.setDate([value, this.endDate]);
                        } else {
                            this.picker.setDate(value);
                        }
                    }
                });
                
                this.$watch('endDate', value => {
                    if (@js($mode) === 'range' && value !== this.picker.selectedDates[1]) {
                        this.picker.setDate([this.startDate, value]);
                    }
                });
            }
        }"
    >
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none z-20">
                <svg class="size-4 text-gray-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            
            <input 
                type="text" 
                id="{{ $id }}"
                x-ref="input"
                placeholder="{{ $placeholder }}"
                {{ $disabled ? 'disabled' : '' }}
                {{ $required ? 'required' : '' }}
                class="{{ $inputClasses }} pl-10"
            >
            
            <!-- Hidden inputs for form submission -->
            @if($mode === 'range')
                <input type="hidden" name="{{ $startName }}" x-bind:value="picker ? picker.selectedDates[0] ? picker.formatDate(picker.selectedDates[0], @js($format)) : '' : ''">
                <input type="hidden" name="{{ $endName }}" x-bind:value="picker ? picker.selectedDates[1] ? picker.formatDate(picker.selectedDates[1], @js($format)) : '' : ''">
            @else
                <input type="hidden" name="{{ $name }}" x-bind:value="picker ? picker.selectedDates[0] ? picker.formatDate(picker.selectedDates[0], @js($format)) : '' : ''">
            @endif
        </div>
    </div>
    
    @if($helper && !$errors->has($name) && !$errors->has($startName) && !$errors->has($endName))
        <div class="mt-1">
            <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</span>
        </div>
    @endif
    
    @error($name)
        <div class="mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
    
    @error($startName)
        <div class="mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
    
    @error($endName)
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
@endonce"
  }