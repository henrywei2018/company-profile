<x-layouts.public 
    :title="$seoData['title']"
    :description="$seoData['description']" 
    :keywords="$seoData['keywords']"
    :breadcrumbs="$seoData['breadcrumbs']"
>
    <x-slot:title>Portfolio Projects</x-slot:title>
    <x-slot:meta_description>Discover our construction project portfolio.</x-slot:meta_description>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 dark:text-gray-100">Our Projects</h1>
            <p class="mt-3 text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                Explore our diverse portfolio of commercial, residential, and industrial projects.
            </p>
        </div>

        <!-- Filter -->
        <form method="GET" class="mb-8 flex flex-wrap justify-center gap-2">
            <select name="category" onchange="this.form.submit()" class="border rounded p-2 text-sm dark:bg-slate-900 dark:text-gray-100">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
        </form>

        <!-- Projects Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <a href="{{ route('portfolio.show', $project->slug) }}"
                   class="group rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-transparent hover:shadow-lg transition-all duration-300 bg-white dark:bg-slate-900">
                    <div class="relative pt-[50%] sm:pt-[70%]">
                        <img class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             src="{{ $project->getFeaturedImageUrlAttribute() }}"
                             alt="{{ $project->title }}">
                        <span class="absolute top-0 right-0 m-2 px-2 py-1 bg-amber-600 text-xs text-white rounded">{{ $project->category }}</span>
                    </div>
                    <div class="p-5">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ $project->title }}</h2>
                        <div class="text-gray-600 dark:text-gray-400 mb-3">
                            {{ Str::limit(strip_tags($project->description), 100) }}
                        </div>
                        <span class="inline-flex items-center gap-x-1 text-amber-600 dark:text-amber-500 font-medium">
                            View Project
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-3 text-center text-gray-400 dark:text-gray-500 py-10">
                    No projects found in this category.
                </div>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $projects->withQueryString()->links() }}
        </div>
    </div>
</x-layouts.public>
