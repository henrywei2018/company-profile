<x-layouts.app>
    <x-slot:title>{{ $service->title }}</x-slot:title>
    <x-slot:meta_description>{{ Str::limit(strip_tags($service->description), 160) }}</x-slot:meta_description>
    <x-navigation-header />
    <div class="max-w-3xl mx-auto px-4 py-12">
        <a href="{{ route('services.index') }}" class="text-amber-600 dark:text-amber-400 hover:underline flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Services
        </a>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ $service->title }}</h1>
            <div class="prose dark:prose-invert max-w-none">
                {!! $service->description !!}
            </div>
        </div>
    </div>
</x-layouts.app>
