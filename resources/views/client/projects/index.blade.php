{{-- resources/views/client/projects/index.blade.php --}}
<x-layouts.client title="My Projects">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Projects</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Track and manage your project progress
            </p>
        </div>

        <div class="mt-4 md:mt-0 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('client.quotations.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Request New Project
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if (isset($statistics) && !empty($statistics))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Projects
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $statistics['total'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $statistics['active'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">In Progress
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $statistics['in_progress'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Completed</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $statistics['completed'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter Projects</h3>
        </div>
        <form method="GET" action="{{ route('client.projects.index') }}" class="p-6" id="projectFilters">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search projects..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                </div>

                <div>
                    <label for="status"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning
                        </option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In
                            Progress</option>
                        <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>

                <div>
                    <label for="category"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select name="category" id="category"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="year"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                    <select name="year" id="year"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Years</option>
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}"
                                {{ request('year') == $yearOption ? 'selected' : '' }}>
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort
                            by</label>
                        <select name="sort" id="sort"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>Last
                                Updated</option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date
                                Created</option>
                            <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Project Title
                            </option>
                            <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status
                            </option>
                            <option value="end_date" {{ request('sort') === 'end_date' ? 'selected' : '' }}>End Date
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="direction"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order</label>
                        <select name="direction" id="direction"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Newest
                                First</option>
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Oldest First
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('client.projects.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-200">
                        Clear Filters
                    </a>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                        id="filterSubmitBtn">
                        <span class="filter-text">Apply Filters</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Projects Grid -->
    @if ($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($projects as $project)
                <div
                    class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <!-- Project Image -->
                    <div class="h-48 bg-gray-200 dark:bg-gray-700 overflow-hidden relative">
                        @if ($project->images && $project->images->count() > 0)
                            @php
                                $featuredImage =
                                    $project->images->where('is_featured', true)->first() ?? $project->images->first();
                            @endphp
                            <img src="{{ Storage::url($featuredImage->image_path) }}"
                                alt="{{ $featuredImage->alt_text ?? $project->title }}"
                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                loading="lazy">
                        @else
                            <div
                                class="h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <svg class="w-16 h-16 mx-auto mb-2 opacity-75" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm">No Image</p>
                                </div>
                            </div>
                        @endif

                        <!-- Image Overlay with View Button -->
                        <div
                            class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center opacity-0 hover:opacity-100">
                            <a href="{{ route('client.projects.show', $project) }}"
                                class="bg-white text-gray-900 px-4 py-2 rounded-lg font-medium transform translate-y-2 hover:translate-y-0 transition-transform duration-300">
                                View Project
                            </a>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Status Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $project->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $project->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $project->status === 'planning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $project->status === 'on_hold' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                            {{ $project->status === 'cancelled' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>

                            @if ($project->category)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $project->category->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Project Title -->
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            <a href="{{ route('client.projects.show', $project) }}"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                {{ $project->title }}
                            </a>
                        </h3>

                        <!-- Project Description -->
                        @if ($project->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                {{ Str::limit($project->description, 120) }}
                            </p>
                        @endif

                        <!-- Project Details -->
                        <div class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                            @if ($project->location)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $project->location }}
                                </div>
                            @endif

                            @if ($project->start_date)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Started: {{ $project->start_date->format('M d, Y') }}
                                </div>
                            @endif

                            @if ($project->budget && auth()->user()->can('view', $project))
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    Budget: Rp.{{ number_format($project->budget, 0) }}
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ route('client.projects.show', $project) }}"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 transition-colors duration-200">
                                View Details
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            @if ($project->status === 'completed' && !$project->testimonial)
                                <a href="{{ route('client.projects.testimonial', $project) }}"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    Add Review
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($projects->hasPages())
            <div class="flex justify-center">
                {{ $projects->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <!-- No Projects Found -->
        <div class="text-center py-12">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">No Projects Found</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                @if (request()->hasAny(['search', 'status', 'category', 'year']))
                    We couldn't find any projects matching your criteria. Try adjusting your filters.
                @else
                    You don't have any projects yet. Contact us to start your first project!
                @endif
            </p>
            @if (request()->hasAny(['search', 'status', 'category', 'year']))
                <a href="{{ route('client.projects.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors duration-200">
                    Clear All Filters
                </a>
            @else
                <a href="{{ route('client.quotations.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors duration-200">
                    Request New Project
                </a>
            @endif
        </div>
    @endif
</x-layouts.client>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('projectFilters');
            const submitBtn = document.getElementById('filterSubmitBtn');
            const filterText = submitBtn.querySelector('.filter-text');

            // Auto-submit on select change (optional - remove if you prefer manual submission)
            const selects = filterForm.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    // Add a small delay to prevent rapid submissions
                    clearTimeout(this.submitTimeout);
                    this.submitTimeout = setTimeout(() => {
                        filterForm.submit();
                    }, 300);
                });
            });

            // Search input with debounce
            const searchInput = document.getElementById('search');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Only auto-submit if search has meaningful content or is cleared
                    if (this.value.length >= 3 || this.value.length === 0) {
                        filterForm.submit();
                    }
                }, 500);
            });

            // Form submission loading state
            filterForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
                filterText.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Filtering...
        `;
            });

            // Show active filters count
            function updateActiveFiltersCount() {
                const formData = new FormData(filterForm);
                let activeFilters = 0;

                for (let [key, value] of formData.entries()) {
                    if (key !== '_token' && value !== '' && value !== 'updated_at' && value !== 'desc') {
                        activeFilters++;
                    }
                }

                const filterBtn = document.querySelector('[href*="projects.index"]:not([type="submit"])');
                if (filterBtn && activeFilters > 0) {
                    filterBtn.innerHTML = `Clear Filters (${activeFilters})`;
                    filterBtn.classList.add('text-red-600', 'border-red-300', 'hover:bg-red-50');
                }
            }

            updateActiveFiltersCount();
        });
    </script>
@endpush
