<!-- resources/views/components/admin/tabs.blade.php -->
@props([
    'activeTab' => null,
    'align' => 'left', // Options: left, center, right, justify
    'variant' => 'underline', // Options: underline, pills, basic
])

@php
    // Handle tab alignment
    $alignmentClasses = [
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end',
        'justify' => 'justify-between'
    ][$align] ?? 'justify-start';
    
    // Handle tab variant
    $variantClasses = [
        'underline' => 'border-b border-gray-200 dark:border-neutral-700',
        'pills' => 'flex-wrap space-x-1',
        'basic' => 'space-x-2'
    ][$variant] ?? 'border-b border-gray-200 dark:border-neutral-700';
    
    // Initialize the active tab if not provided
    if (!$activeTab && isset($tabs) && count($tabs) > 0) {
        $tabKeys = array_keys($tabs);
        $activeTab = $tabKeys[0];
    }
@endphp

<div x-data="{ activeTab: @js($activeTab) }" {{ $attributes }}>
    <!-- Tab Headers -->
    <div class="flex {{ $alignmentClasses }} {{ $variantClasses }}">
        @if(isset($tabs))
            @foreach($tabs as $tabId => $tabLabel)
                <button 
                    type="button" 
                    @click="activeTab = '{{ $tabId }}'"
                    :class="{ 
                        @if($variant === 'underline')
                            'border-b-2 border-transparent py-4 px-1 text-sm font-medium -mb-px': true,
                            'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500': activeTab === '{{ $tabId }}',
                            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600': activeTab !== '{{ $tabId }}'
                        @elseif($variant === 'pills')
                            'py-2 px-4 text-sm font-medium rounded-md': true,
                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-500': activeTab === '{{ $tabId }}',
                            'bg-white text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:bg-neutral-800 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-neutral-700': activeTab !== '{{ $tabId }}'
                        @else
                            'py-2 px-4 text-sm font-medium': true,
                            'text-blue-600 dark:text-blue-500': activeTab === '{{ $tabId }}',
                            'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== '{{ $tabId }}'
                        @endif
                    }"
                    class="focus:outline-none">
                    {{ $tabLabel }}
                </button>
            @endforeach
        @else
            <!-- Slot for custom tab headers -->
            {{ $tabHeaders ?? '' }}
        @endif
    </div>

    <!-- Tab Content -->
    <div class="mt-4">
        @if(isset($tabs) && $slot->isNotEmpty())
            {{ $slot }}
        @else
            <!-- Slot for custom tab content -->
            {{ $tabContents ?? '' }}
        @endif
    </div>
</div>

<!-- Tab Panel Component -->
@component('components.admin.tab-panel')
@endcomponent