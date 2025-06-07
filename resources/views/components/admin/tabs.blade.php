<!-- resources/views/components/admin/tabs.blade.php -->
@props([
    'activeTab' => null,
    'align' => 'left',
    'variant' => 'underline',
    'tabs' => []
])

@php
    // Safely handle all props
    $activeTab = is_string($activeTab) ? trim($activeTab) : null;
    $align = is_string($align) ? trim($align) : 'left';
    $variant = is_string($variant) ? trim($variant) : 'underline';
    
    // Ensure tabs is an array
    if (!is_array($tabs)) {
        $tabs = [];
    }
    
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
    if (!$activeTab && count($tabs) > 0) {
        $tabKeys = array_keys($tabs);
        $activeTab = $tabKeys[0];
    }
@endphp

<div x-data="{ activeTab: @js($activeTab) }" {{ $attributes }}>
    <!-- Tab Headers -->
    <div class="flex {{ $alignmentClasses }} {{ $variantClasses }}">
        @if(count($tabs) > 0)
            @foreach($tabs as $tabId => $tabLabel)
                @php
                    $safeTabId = is_string($tabId) ? trim($tabId) : (string) $tabId;
                    $safeTabLabel = is_string($tabLabel) ? trim($tabLabel) : (string) $tabLabel;
                @endphp
                <button 
                    type="button" 
                    @click="activeTab = '{{ $safeTabId }}'"
                    :class="{ 
                        @if($variant === 'underline')
                            'border-b-2 border-transparent py-4 px-1 text-sm font-medium -mb-px': true,
                            'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500': activeTab === '{{ $safeTabId }}',
                            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600': activeTab !== '{{ $safeTabId }}'
                        @elseif($variant === 'pills')
                            'py-2 px-4 text-sm font-medium rounded-md': true,
                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-500': activeTab === '{{ $safeTabId }}',
                            'bg-white text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:bg-neutral-800 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-neutral-700': activeTab !== '{{ $safeTabId }}'
                        @else
                            'py-2 px-4 text-sm font-medium': true,
                            'text-blue-600 dark:text-blue-500': activeTab === '{{ $safeTabId }}',
                            'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== '{{ $safeTabId }}'
                        @endif
                    }"
                    class="focus:outline-none">
                    {{ $safeTabLabel }}
                </button>
            @endforeach
        @else
            <!-- Slot for custom tab headers -->
            {{ $tabHeaders ?? '' }}
        @endif
    </div>

    <!-- Tab Content -->
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
