{{-- resources/views/components/admin/form-section.blade.php --}}
@props(['title' => '', 'description' => '', 'footer' => null])
 
<div {{ $attributes->merge(['class' => 'bg-white dark:bg-neutral-800 shadow-sm rounded-xl overflow-hidden mb-6']) }}>
    @if($title || $description)
    <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
        @if($title)
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
        @endif
        @if($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">{{ $description }}</p>
        @endif
    </div>
    @endif

    <div class="px-6 py-6">
        {{ $slot }}
    </div>

    @if($footer)
    <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700">
        {{ $footer }}
    </div>
    @endif
</div>