{{-- resources/views/components/admin/header-section.blade.php --}}
@props([
    'title',
    'description' => null,
    'createRoute' => null,
    'createText' => 'Create New',
    'additionalActions' => null,
    'showCreate' => true
])

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
        @if($description)
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $description }}</p>
        @endif
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        {{ $additionalActions ?? '' }}
        
        @if($showCreate && $createRoute)
            <a href="{{ $createRoute }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ $createText }}
            </a>
        @endif
    </div>
</div>