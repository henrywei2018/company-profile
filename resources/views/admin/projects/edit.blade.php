{{-- resources/views/admin/projects/edit.blade.php --}}
<x-layouts.admin title="Edit Project">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Project</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Update project information and settings
            </p>
        </div>

        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <!-- Status Badge -->
            <x-admin.badge
                type="{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'warning' : 'info') }}"
                size="lg">
                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
            </x-admin.badge>

            <!-- Quick Actions -->
            <x-admin.button href="{{ route('admin.projects.show', $project) }}" color="light" size="sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Project
            </x-admin.button>

            @if ($project->slug)
                <x-admin.button href="{{ route('portfolio.projects.show', $project->slug) }}" color="info"
                    size="sm" target="_blank">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View Live
                </x-admin.button>
            @endif
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        $project->title => route('admin.projects.show', $project),
        'Edit' => route('admin.projects.edit', $project),
    ]" class="mb-6" />

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="font-bold">Please fix the following errors:</div>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Project Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 bg-blue-100 dark:bg-blue-800/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Progress</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $project->progress_percentage ?? 0 }}%</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 bg-green-100 dark:bg-green-800/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Images</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $project->images->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 bg-purple-100 dark:bg-purple-800/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Files</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $project->files->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 bg-amber-100 dark:bg-amber-800/30 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h6a2 2 0 002-2V7a2 2 0 00-2-2h-2m0 0V3a1 1 0 00-1-1H8a1 1 0 00-1 1v2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Milestones</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $project->milestones->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Information -->
    @if ($project->quotation)
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6 dark:bg-blue-900/20 dark:border-blue-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Project Created from Quotation
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        This project was created from
                        <a href="{{ route('admin.quotations.show', $project->quotation) }}"
                            class="font-medium underline hover:text-blue-600">
                            Quotation #{{ $project->quotation->id }}
                        </a>
                        submitted by {{ $project->quotation->name }}.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Project Status Alerts -->
    @if ($project->end_date && $project->end_date < now() && $project->status !== 'completed')
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Project Overdue</h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        This project is {{ now()->diffInDays($project->end_date) }} days overdue.
                        Consider updating the timeline or status.
                    </div>
                </div>
            </div>
        </div>
    @elseif($project->end_date && $project->end_date->diffInDays(now()) <= 7 && $project->status === 'in_progress')
        <div
            class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6 dark:bg-yellow-900/20 dark:border-yellow-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Deadline Approaching</h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        This project is due in {{ now()->diffInDays($project->end_date) }} days.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Project Edit Form -->
    <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data"
        id="project-form">
        @csrf
        @method('PUT')

        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Basic Information</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update the fundamental project details.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Project Title -->
                    <div>
                        <label for="title"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Title *</label>
                        <input type="text" name="title" id="title"
                            value="{{ old('title', $project->title) }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('title') border-red-500 @enderror"
                            required>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Slug -->
                    <div>
                        <label for="slug"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                        <input type="text" name="slug" id="slug"
                            value="{{ old('slug', $project->slug) }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('slug') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500">URL-friendly version of the title. Leave blank to
                            auto-generate.</p>
                        @error('slug')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client -->
                    @if (Schema::hasColumn('projects', 'client_id'))
                        <div>
                            <label for="client_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client</label>
                            <select name="client_id" id="client_id"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('client_id') border-red-500 @enderror">
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Client Name (fallback) -->
                    @if (Schema::hasColumn('projects', 'client_name'))
                        <div>
                            <label for="client_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client Name</label>
                            <input type="text" name="client_name" id="client_name"
                                value="{{ old('client_name', $project->client_name ?? $project->client?->name) }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('client_name') border-red-500 @enderror">
                            @error('client_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Category -->
                    @if (Schema::hasColumn('projects', 'category_id'))
                        <div>
                            <label for="category_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category *</label>
                            <select name="category_id" id="category_id" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('category_id') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Service -->
                    @if (Schema::hasColumn('projects', 'service_id'))
                        <div>
                            <label for="service_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service</label>
                            <select name="service_id" id="service_id"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('service_id') border-red-500 @enderror">
                                <option value="">Select Service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}"
                                        {{ old('service_id', $project->service_id) == $service->id ? 'selected' : '' }}>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Location -->
                    <div>
                        <label for="location"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                        <input type="text" name="location" id="location"
                            value="{{ old('location', $project->location) }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('location') border-red-500 @enderror">
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Year -->
                    <div>
                        <label for="year"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                        <input type="number" name="year" id="year" min="1900"
                            max="{{ date('Y') + 5 }}" value="{{ old('year', $project->year) }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('year') border-red-500 @enderror">
                        @error('year')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description *</label>
                    <textarea name="description" id="description" rows="4" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('description') border-red-500 @enderror">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Short Description (if column exists) -->
                @if (Schema::hasColumn('projects', 'short_description'))
                    <div class="mt-6">
                        <label for="short_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Short
                            Description</label>
                        <textarea name="short_description" id="short_description" rows="2"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('short_description') border-red-500 @enderror">{{ old('short_description', $project->short_description) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Brief summary for listings and previews.</p>
                        @error('short_description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </div>

        <!-- Project Status & Management Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Status & Management</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage project status, priority, and
                        progress.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Status -->
                    <div>
                        <label for="status"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('status') border-red-500 @enderror">
                            <option value="planning"
                                {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>Planning
                            </option>
                            <option value="in_progress"
                                {{ old('status', $project->status) === 'in_progress' ? 'selected' : '' }}>In Progress
                            </option>
                            <option value="on_hold"
                                {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed"
                                {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="cancelled"
                                {{ old('status', $project->status) === 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority (if column exists) -->
                    @if (Schema::hasColumn('projects', 'priority'))
                        <div>
                            <label for="priority"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                            <select name="priority" id="priority"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('priority') border-red-500 @enderror">
                                <option value="low"
                                    {{ old('priority', $project->priority ?? 'normal') === 'low' ? 'selected' : '' }}>
                                    Low</option>
                                <option value="normal"
                                    {{ old('priority', $project->priority ?? 'normal') === 'normal' ? 'selected' : '' }}>
                                    Normal</option>
                                <option value="high"
                                    {{ old('priority', $project->priority ?? 'normal') === 'high' ? 'selected' : '' }}>
                                    High</option>
                                <option value="urgent"
                                    {{ old('priority', $project->priority ?? 'normal') === 'urgent' ? 'selected' : '' }}>
                                    Urgent</option>
                            </select>
                            @error('priority')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Progress Percentage (if column exists) -->
                    @if (Schema::hasColumn('projects', 'progress_percentage'))
                        <div>
                            <label for="progress_percentage"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Progress (%)</label>
                            <input type="number" name="progress_percentage" id="progress_percentage" min="0"
                                max="100"
                                value="{{ old('progress_percentage', $project->progress_percentage ?? 0) }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('progress_percentage') border-red-500 @enderror">
                            @error('progress_percentage')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Featured & Active Status -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="hidden" name="featured" value="0">
                            <input type="checkbox" name="featured" id="featured" value="1"
                                {{ old('featured', $project->featured) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="featured" class="ml-2 block text-sm text-gray-900 dark:text-white">Featured
                                Project</label>
                        </div>

                        @if (Schema::hasColumn('projects', 'is_active'))
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                    {{ old('is_active', $project->is_active ?? true) ? 'checked' : '' }}
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">Active
                                    Project</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline & Dates Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Timeline & Dates</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set project start and end dates.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                        <input type="date" name="start_date" id="start_date"
                            value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End
                            Date</label>
                        <input type="date" name="end_date" id="end_date"
                            value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimated Completion Date (if column exists) -->
                    @if (Schema::hasColumn('projects', 'estimated_completion_date'))
                        <div>
                            <label for="estimated_completion_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated
                                Completion</label>
                            <input type="date" name="estimated_completion_date" id="estimated_completion_date"
                                value="{{ old('estimated_completion_date', $project->estimated_completion_date?->format('Y-m-d')) }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('estimated_completion_date') border-red-500 @enderror">
                            @error('estimated_completion_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Actual Completion Date (if column exists) -->
                    @if (Schema::hasColumn('projects', 'actual_completion_date'))
                        <div>
                            <label for="actual_completion_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Actual
                                Completion</label>
                            <input type="date" name="actual_completion_date" id="actual_completion_date"
                                value="{{ old('actual_completion_date', $project->actual_completion_date?->format('Y-m-d')) }}"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('actual_completion_date') border-red-500 @enderror">
                            @error('actual_completion_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial Information Section (if columns exist) -->
        @if (Schema::hasColumn('projects', 'budget') ||
                Schema::hasColumn('projects', 'actual_cost') ||
                Schema::hasColumn('projects', 'value'))
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Financial Information
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Budget and cost tracking.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <!-- Project Value -->
                        @if (Schema::hasColumn('projects', 'value'))
                            <div>
                                <label for="value"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project
                                    Value</label>
                                <input type="text" name="value" id="value"
                                    value="{{ old('value', $project->value) }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('value') border-red-500 @enderror">
                                @error('value')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Budget -->
                        @if (Schema::hasColumn('projects', 'budget'))
                            <div>
                                <label for="budget"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget</label>
                                <input type="number" name="budget" id="budget" step="0.01"
                                    value="{{ old('budget', $project->budget) }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('budget') border-red-500 @enderror">
                                @error('budget')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Actual Cost -->
                        @if (Schema::hasColumn('projects', 'actual_cost'))
                            <div>
                                <label for="actual_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Actual
                                    Cost</label>
                                <input type="number" name="actual_cost" id="actual_cost" step="0.01"
                                    value="{{ old('actual_cost', $project->actual_cost) }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('actual_cost') border-red-500 @enderror">
                                @error('actual_cost')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Project Details Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Project Details</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Detailed project information and
                        documentation.</p>
                </div>

                <div class="space-y-6">
                    <!-- Challenge -->
                    <div>
                        <label for="challenge"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Challenge</label>
                        <textarea name="challenge" id="challenge" rows="3"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('challenge') border-red-500 @enderror">{{ old('challenge', $project->challenge) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">What challenges did this project address?</p>
                        @error('challenge')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Solution -->
                    <div>
                        <label for="solution"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Solution</label>
                        <textarea name="solution" id="solution" rows="3"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('solution') border-red-500 @enderror">{{ old('solution', $project->solution) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">How did we solve these challenges?</p>
                        @error('solution')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Result -->
                    <div>
                        <label for="result"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Result</label>
                        <textarea name="result" id="result" rows="3"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('result') border-red-500 @enderror">{{ old('result', $project->result) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">What were the outcomes?</p>
                        @error('result')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Feedback (if column exists) -->
                    @if (Schema::hasColumn('projects', 'client_feedback'))
                        <div>
                            <label for="client_feedback"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client
                                Feedback</label>
                            <textarea name="client_feedback" id="client_feedback" rows="3"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('client_feedback') border-red-500 @enderror">{{ old('client_feedback', $project->client_feedback) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Client testimonials or feedback.</p>
                            @error('client_feedback')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Lessons Learned (if column exists) -->
                    @if (Schema::hasColumn('projects', 'lessons_learned'))
                        <div>
                            <label for="lessons_learned"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lessons
                                Learned</label>
                            <textarea name="lessons_learned" id="lessons_learned" rows="3"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('lessons_learned') border-red-500 @enderror">{{ old('lessons_learned', $project->lessons_learned) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Key learnings and insights from this project.</p>
                            @error('lessons_learned')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Project Gallery Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Project Gallery</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage project images and gallery.</p>
                </div>

                <!-- Existing Images -->
                @if ($project->images->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Current Images
                            ({{ $project->images->count() }})</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                            id="existing-images-grid">
                            @foreach ($project->images as $image)
                                <div class="relative group image-item" data-image-id="{{ $image->id }}">
                                    <div class="aspect-w-3 aspect-h-2">
                                        <img src="{{ Storage::url($image->image_path) }}"
                                            alt="{{ $image->alt_text }}" class="w-full h-48 object-cover rounded-lg">
                                    </div>

                                    <!-- Image overlay with controls -->
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <div class="flex space-x-2">
                                            <button type="button"
                                                class="set-featured-btn bg-white text-gray-800 px-3 py-1 rounded-md text-sm font-medium hover:bg-gray-100 {{ $image->is_featured ? 'bg-yellow-400 text-yellow-900' : '' }}"
                                                data-image-id="{{ $image->id }}">
                                                {{ $image->is_featured ? 'Featured' : 'Set Featured' }}
                                            </button>
                                            <button type="button"
                                                class="delete-image-btn bg-red-600 text-white px-3 py-1 rounded-md text-sm font-medium hover:bg-red-700"
                                                data-image-id="{{ $image->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Featured badge -->
                                    @if ($image->is_featured)
                                        <div class="absolute top-2 right-2">
                                            <span
                                                class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-medium">Featured</span>
                                        </div>
                                    @endif

                                    <!-- Image info -->
                                    <div class="mt-2">
                                        <input type="text" name="existing_image_alt[{{ $image->id }}]"
                                            value="{{ $image->alt_text }}" placeholder="Alt text"
                                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upload New Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add New
                        Images</label>
                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="images"
                                    class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload images</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple
                                        accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP up to 2MB each (max 10
                                images)</p>
                        </div>
                    </div>

                    <!-- Image Preview Container -->
                    <div id="image-preview-container"
                        class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 hidden"></div>

                    @error('images')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SEO Section (if columns exist) -->
        @if (Schema::hasColumn('projects', 'meta_title') ||
                Schema::hasColumn('projects', 'meta_description') ||
                Schema::hasColumn('projects', 'meta_keywords'))
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">SEO Information</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search engine optimization settings.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Meta Title -->
                        @if (Schema::hasColumn('projects', 'meta_title'))
                            <div>
                                <label for="meta_title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta
                                    Title</label>
                                <input type="text" name="meta_title" id="meta_title"
                                    value="{{ old('meta_title', $project->meta_title) }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('meta_title') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500">Recommended length: 50-60 characters</p>
                                @error('meta_title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Meta Description -->
                        @if (Schema::hasColumn('projects', 'meta_description'))
                            <div>
                                <label for="meta_description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta
                                    Description</label>
                                <textarea name="meta_description" id="meta_description" rows="3"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('meta_description') border-red-500 @enderror">{{ old('meta_description', $project->meta_description) }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Recommended length: 150-160 characters</p>
                                @error('meta_description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Meta Keywords -->
                        @if (Schema::hasColumn('projects', 'meta_keywords'))
                            <div>
                                <label for="meta_keywords"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta
                                    Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords"
                                    value="{{ old('meta_keywords', $project->meta_keywords) }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('meta_keywords') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                                @error('meta_keywords')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.projects.show', $project) }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </a>

                        <a href="{{ route('admin.projects.index') }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            Back to Projects
                        </a>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" name="action" value="save_and_continue"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            Save & Continue Editing
                        </button>

                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Update Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Delete Confirmation Modal -->
    <div id="delete-project-modal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-5">Delete Project</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this project? This action cannot be undone and will permanently
                        delete:
                    </p>
                    <div
                        class="mt-3 text-left bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3">
                        <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-1">
                            <li>{{ $project->images->count() }} project images</li>
                            <li>{{ $project->files->count() }} project files</li>
                            <li>{{ $project->milestones->count() }} project milestones</li>
                            <li>All project history and analytics data</li>
                        </ul>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="document.getElementById('delete-project-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

@push('scripts')
// Replace the JavaScript in your @push('scripts') section with this:

<script>
// Image Gallery Functions
function setFeaturedImage(imageId) {
    // AJAX call to set featured image
    fetch(`/admin/projects/{{ $project->id }}/images/${imageId}/set-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            document.querySelectorAll('.set-featured-btn').forEach(btn => {
                btn.textContent = 'Set Featured';
                btn.classList.remove('bg-yellow-400', 'text-yellow-900');
                btn.classList.add('bg-white', 'text-gray-800');
            });
            
            // Update the clicked button
            const button = document.querySelector(`[data-image-id="${imageId}"]`);
            button.textContent = 'Featured';
            button.classList.remove('bg-white', 'text-gray-800');
            button.classList.add('bg-yellow-400', 'text-yellow-900');
            
            // Update featured badges
            document.querySelectorAll('.featured-badge').forEach(badge => badge.remove());
            const imageItem = document.querySelector(`[data-image-id="${imageId}"]`).closest('.image-item');
            imageItem.querySelector('.relative').insertAdjacentHTML('beforeend', 
                '<div class="featured-badge absolute top-2 right-2"><span class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-medium">Featured</span></div>'
            );
        } else {
            alert('Failed to set featured image');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) return;
    
    fetch(`/admin/projects/{{ $project->id }}/images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-image-id="${imageId}"]`).closest('.image-item').remove();
        } else {
            alert('Failed to delete image');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Image upload preview
document.getElementById('images')?.addEventListener('change', function(e) {
    const files = e.target.files;
    const container = document.getElementById('image-preview-container');
    
    if (files.length > 0) {
        container.classList.remove('hidden');
        container.innerHTML = '';
        
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = `
                        <div class="relative">
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-32 object-cover rounded-lg">
                            <div class="mt-2">
                                <input type="text" name="image_alt_texts[${index}]" placeholder="Alt text for image ${index + 1}"
                                       class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', preview);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        container.classList.add('hidden');
    }
});

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Remove empty inputs before submission
            const inputs = form.querySelectorAll('input[name^="services_used"], input[name^="technologies_used"], input[name^="team_members"]');
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.remove();
                }
            });
        });
    }
    
    // Set up existing image controls
    document.querySelectorAll('.set-featured-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            setFeaturedImage(imageId);
        });
    });
    
    document.querySelectorAll('.delete-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            deleteImage(imageId);
        });
    });
});

// Helper function to show delete confirmation
function confirmDelete() {
    document.getElementById('delete-project-modal').classList.remove('hidden');
}
</script>
@endpush
