<!-- resources/views/components/admin/form-section.blade.php -->
@props(['title', 'description', 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden mb-6']) }}>
    <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
        @if(isset($description))
            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">{{ $description }}</p>
        @endif
    </div>
    
    <div class="p-6">
        {{ $slot }}
    </div>
    
    @if($footer)
    <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900 flex items-center justify-end space-x-3">
        {{ $footer }}
    </div>
    @endif
</div>