<x-layouts.public 
    :title="$seoData['title']"
    :description="$seoData['description']" 
    :keywords="$seoData['keywords']"
    :breadcrumbs="$seoData['breadcrumbs']"
>
    <x-slot:title>{{ $project->title }}</x-slot:title>
    <x-slot:meta_description>{{ Str::limit(strip_tags($project->description), 160) }}</x-slot:meta_description>

    <div class="max-w-3xl mx-auto px-4 py-12">
        <a href="{{ route('portfolio.index') }}"
           class="text-amber-600 dark:text-amber-400 hover:underline flex items-center mb-6">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Portfolio
        </a>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ $project->title }}</h1>
            <div class="mb-4">
                <span class="inline-block px-3 py-1 bg-amber-600 text-white rounded text-xs">{{ $project->category }}</span>
            </div>
            <img src="{{ $project->getFeaturedImageUrlAttribute() }}"
                 alt="{{ $project->title }}" class="w-full rounded mb-6">
            <div class="prose dark:prose-invert max-w-none">
                {!! $project->description !!}
            </div>
            @if($project->location)
                <div class="mt-6 text-gray-500 dark:text-gray-400 text-sm">
                    <strong>Location:</strong> {{ $project->location }}
                </div>
            @endif
            @if($project->client)
                <div class="text-gray-500 dark:text-gray-400 text-sm">
                    <strong>Client:</strong> {{ $project->client }}
                </div>
            @endif
            @if($project->year)
                <div class="text-gray-500 dark:text-gray-400 text-sm">
                    <strong>Year:</strong> {{ $project->year }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.public>
    