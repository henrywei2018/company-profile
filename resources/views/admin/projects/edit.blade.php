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
                <x-admin.button href="{{ route('home', $project->slug) }}" color="info" size="sm"
                    target="_blank">
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                        <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                    <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                    <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                    <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                            <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Project Images</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage images for this project. Drag to
                        reorder, click to edit.</p>
                </div>

        
            @if ($project->images && $project->images->count() > 0)
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                            Current Images ({{ $project->images->count() }})
                        </h4>
                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Click image to view full size, hover for actions</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="current-images-grid">
                        @foreach ($project->images->sortBy('sort_order') as $image)
                            <div class="relative group bg-gray-50 dark:bg-gray-700 rounded-lg p-2 border-2 border-dashed border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 transition-colors"
                                data-image-id="{{ $image->id }}">

                                <!-- Image Display -->
                                <div class="aspect-w-16 aspect-h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-600 cursor-pointer"
                                    data-action="view-image" 
                                    data-image-url="{{ $image->image_url }}" 
                                    data-alt-text="{{ $image->alt_text }}">
                                    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}"
                                        class="w-full h-32 object-cover rounded-lg transition-transform group-hover:scale-105">

                                    <!-- Overlay -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 rounded-lg flex items-center justify-center">
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex space-x-2">
                                            <!-- View Full Size -->
                                            <button type="button"
                                                    data-action="view-image" 
                                                    data-image-url="{{ $image->image_url }}" 
                                                    data-alt-text="{{ $image->alt_text }}"
                                                class="bg-white text-gray-800 p-2 rounded-full hover:bg-gray-100 transition-colors shadow-lg"
                                                title="View full size">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </button>

                                            <!-- Delete -->
                                            <button type="button" 
                                                    data-action="delete-image" 
                                                    data-image-id="{{ $image->id }}" 
                                                    data-alt-text="{{ addslashes($image->alt_text) }}"
                                                class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors shadow-lg"
                                                title="Delete image">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                            

                                            <!-- Make Featured Button -->
                                            
                                            @if (!$image->is_featured)
                                                <button type="button" 
                                                        data-action="set-featured" 
                                                        data-image-id="{{ $image->id }}"
                                                        class="bg-yellow-600 text-white p-2 rounded-full hover:bg-yellow-700 transition-colors shadow-lg"
                                                        title="Set as featured image">
                                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110 group-hover:fill-yellow-400" 
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Info -->
                                <div class="mt-2">
                                    <div class="flex items-center justify-between mb-2">
                                        @if ($image->is_featured)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                Featured
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                #{{ $image->sort_order }}
                                            </span>
                                        @endif

                                        <span class="text-xs text-gray-400">
                                            @if (Storage::disk('public')->exists($image->image_path))
                                                {{ number_format(Storage::disk('public')->size($image->image_path) / 1024, 1) }}
                                                KB
                                            @else
                                                <span class="text-red-500">Missing</span>
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Alt Text Editor -->
                                    <div class="mt-1">
                                        <input type="text" name="existing_image_alt[{{ $image->id }}]"
                                            value="{{ $image->alt_text }}"
                                            placeholder="Alt text for accessibility..."
                                            class="w-full text-xs px-2 py-1 border border-gray-200 dark:border-gray-600 rounded 
                                            focus:ring-1 focus:ring-blue-500 focus:border-blue-500 
                                            dark:bg-gray-600 dark:text-gray-200 transition-colors"
                                            title="Alt text for accessibility" onchange="markFormAsChanged()">
                                    </div>
                                </div>

                                <!-- Drag Handle -->
                                <div class="absolute top-1 left-1 opacity-0 group-hover:opacity-100 transition-opacity cursor-move bg-white dark:bg-gray-800 rounded-full p-1 shadow-lg"
                                    title="Drag to reorder">
                                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @endif

                <div
                    class="mb-6 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">New Images:</span>
                            <span id="upload-status" class="text-sm font-medium text-gray-900 dark:text-white">0 /
                                {{ 10 - $project->images->count() }} images can be added</span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Progress Bar -->
                            <div class="w-24 bg-gray-200 rounded-full h-2 dark:bg-gray-600">
                                <div id="upload-progress"
                                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>

                            <!-- Clear All Button -->
                            <button type="button" id="clear-all-uploads"
                                class="text-xs px-2 py-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                Clear New
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload New Images -->
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        {{ $project->images->count() > 0 ? 'Add More Images' : 'Upload Images' }}
                    </h4>

                    @if ($project->images->count() >= 10)
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Maximum Images Reached</h3>
                                    <p class="mt-1 text-sm text-yellow-700">This project already has the maximum of 10
                                        images. Please delete some images before adding new ones.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Universal File Uploader for New Images -->
                        <x-universal-file-uploader :id="'project-images-uploader-edit-' . $project->id" name="temp_images" :multiple="true"
                            :maxFiles="10" maxFileSize="5MB" :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" :uploadEndpoint="route('admin.projects.upload-temp')" :deleteEndpoint="route('admin.projects.delete-temp')"
                            dropDescription="Drop new project images here or click to browse (Max 5MB each)"
                            :enableCategories="true" :categories="[
                                ['value' => 'gallery', 'label' => 'Gallery Image'],
                                ['value' => 'before', 'label' => 'Before Photo'],
                                ['value' => 'during', 'label' => 'During Construction'],
                                ['value' => 'after', 'label' => 'After/Final Result'],
                                ['value' => 'detail', 'label' => 'Detail Shot'],
                            ]" :enableDescription="true" :enablePublicToggle="false" :instantUpload="true"
                            :galleryMode="true" :replaceMode="false" containerClass="mb-4" theme="modern"
                            :singleMode="false" :showFileList="true" :showProgress="true" :dragOverlay="true" />
                    @endif
                </div>
            </div>
        </div>
        <div id="image-view-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeImageModal()">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6 dark:bg-gray-800">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                                    id="modal-title">
                                    Project Image
                                </h3>
                                <button type="button" onclick="closeImageModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-center">
                                <img id="modal-image" src="" alt=""
                                    class="max-w-full max-h-96 mx-auto rounded-lg">
                                <p id="modal-info" class="mt-2 text-sm text-gray-500 dark:text-gray-400"></p>
                            </div>
                        </div>
                    </div>
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
                                    <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                    <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
                                    <p class="mt-2 text-sm text-red-600">{{ $message ?? '' }}</p>
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
// Replace your existing JavaScript section with this complete version

<script>
    // Global scope functions and variables - accessible from Blade onclick attributes
    let PROJECT_ID = {{ $project->id }};
    let PROJECT_SLUG = '{{ $project->slug }}';
    let DEBUG = {{ app()->environment(['local', 'staging']) ? 'true' : 'false' }};
    let CURRENT_IMAGES_COUNT = {{ $project->images->count() }};
    let MAX_IMAGES = 10;
    let MAX_NEW_IMAGES = MAX_IMAGES - CURRENT_IMAGES_COUNT;
    let uploadedFilesCount = 0;
    let totalFilesUploaded = [];
    let hasUnsavedChanges = false;

    // Global helper functions
    function debugLog(message, data = null) {
        if (DEBUG) {
            console.log('[Project Edit Debug]', message, data);
        }
    }

    function showNotification(message, type = 'info') {
        // Remove existing notifications of the same type
        const existingNotifications = document.querySelectorAll(`.notification-toast.${type}`);
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className =
            `notification-toast ${type} fixed top-4 right-4 z-50 p-4 rounded-lg shadow-xl transition-all duration-300 transform translate-x-full max-w-sm border-l-4`;

        switch (type) {
            case 'success':
                notification.className += ' bg-green-50 border-green-400 text-green-800';
                break;
            case 'error':
                notification.className += ' bg-red-50 border-red-400 text-red-800';
                break;
            case 'warning':
                notification.className += ' bg-yellow-50 border-yellow-400 text-yellow-800';
                break;
            default:
                notification.className += ' bg-blue-50 border-blue-400 text-blue-800';
        }

        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${getNotificationIcon(type)}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button type="button" class="text-current hover:opacity-70 transition-opacity" onclick="this.closest('.notification-toast').remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, type === 'error' ? 8000 : 6000);
    }

    function getNotificationIcon(type) {
        const iconClass = 'w-5 h-5';
        switch (type) {
            case 'success':
                return `<svg class="${iconClass} text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>`;
            case 'error':
                return `<svg class="${iconClass} text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>`;
            case 'warning':
                return `<svg class="${iconClass} text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>`;
            default:
                return `<svg class="${iconClass} text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>`;
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Mark form as changed - GLOBAL SCOPE
    function markFormAsChanged() {
        hasUnsavedChanges = true;
    }

    // Update image grid after deletions - GLOBAL SCOPE
    function updateImageGrid() {
        const grid = document.getElementById('current-images-grid');
        if (!grid) return;
        
        const images = grid.querySelectorAll('[data-image-id]');

        // Update sort order numbers
        images.forEach((img, index) => {
            const orderSpan = img.querySelector('.text-xs.text-gray-500');
            if (orderSpan && !orderSpan.textContent.includes('Featured')) {
                orderSpan.textContent = `#${index + 1}`;
            }
        });

        // Update upload status based on remaining slots
        const currentCount = images.length;
        const newMaxImages = MAX_IMAGES - currentCount;
        const statusElement = document.getElementById('upload-status');
        if (statusElement) {
            statusElement.textContent = `${uploadedFilesCount} / ${newMaxImages} new images can be added`;
        }
    }

    // Image management functions - GLOBAL SCOPE for Blade onclick access
    function viewImage(imageUrl, altText) {
        const modal = document.getElementById('image-view-modal');
        const modalImage = document.getElementById('modal-image');
        const modalTitle = document.getElementById('modal-title');
        const modalInfo = document.getElementById('modal-info');

        if (!modal || !modalImage || !modalTitle || !modalInfo) {
            console.error('Image modal elements not found');
            return;
        }

        modalImage.src = imageUrl;
        modalImage.alt = altText;
        modalTitle.textContent = altText || 'Project Image';
        modalInfo.textContent = 'Click outside to close';

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('image-view-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function deleteProjectImage(imageId, altText) {
    if (!confirm(`Are you sure you want to delete "${altText}"? This action cannot be undone.`)) {
        return;
    }

    const imageContainer = document.querySelector(`[data-image-id="${imageId}"]`);
    if (imageContainer) {
        imageContainer.style.opacity = '0.5';
        imageContainer.style.pointerEvents = 'none';
    }

    // Construct the URL to match the route: /admin/projects/{project}/delete-image/{image}
    const deleteUrl = `/admin/projects/${PROJECT_SLUG}/delete-image/${imageId}`;

    fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Image deleted successfully!', 'success');

                // Remove the image container with animation
                if (imageContainer) {
                    imageContainer.style.transform = 'scale(0.8)';
                    imageContainer.style.opacity = '0';
                    setTimeout(() => {
                        imageContainer.remove();
                        updateImageGrid();
                    }, 300);
                }

                markFormAsChanged();
            } else {
                showNotification(data.message || 'Failed to delete image', 'error');
                // Restore image container
                if (imageContainer) {
                    imageContainer.style.opacity = '1';
                    imageContainer.style.pointerEvents = 'auto';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting the image', 'error');
            // Restore image container
            if (imageContainer) {
                imageContainer.style.opacity = '1';
                imageContainer.style.pointerEvents = 'auto';
            }
        });
}

    function setFeaturedImage(imageId) {
    // Find the button that was clicked
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Setting...';
    button.disabled = true;

    // Construct the URL to match the route: /admin/projects/{project}/set-featured-image/{image}
    const featuredUrl = `/admin/projects/${PROJECT_SLUG}/set-featured-image/${imageId}`;

    fetch(featuredUrl, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update all images to remove featured status visually
                document.querySelectorAll('[data-image-id]').forEach(container => {
                    const featuredBadge = container.querySelector('.bg-blue-100, .bg-blue-900');
                    if (featuredBadge) {
                        featuredBadge.remove();
                    }

                    const setFeaturedBtn = container.querySelector('button[data-action="set-featured"]');
                    if (setFeaturedBtn) {
                        setFeaturedBtn.style.display = 'block';
                        setFeaturedBtn.textContent = 'Set as Featured';
                        setFeaturedBtn.disabled = false;
                    }
                });

                // Add featured badge to selected image
                const selectedContainer = document.querySelector(`[data-image-id="${imageId}"]`);
                if (selectedContainer) {
                    const infoDiv = selectedContainer.querySelector('.flex.items-center.justify-between');
                    if (infoDiv) {
                        const featuredBadge = document.createElement('span');
                        featuredBadge.className =
                            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                        featuredBadge.innerHTML = `
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Featured
                        `;
                        infoDiv.appendChild(featuredBadge);
                    }

                    // Hide the set featured button
                    const setFeaturedBtn = selectedContainer.querySelector('button[data-action="set-featured"]');
                    if (setFeaturedBtn) {
                        setFeaturedBtn.style.display = 'none';
                    }
                }

                showNotification(data.message || 'Featured image updated successfully!', 'success');
            } else {
                button.textContent = originalText;
                button.disabled = false;
                showNotification(data.message || 'Failed to set featured image', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.textContent = originalText;
            button.disabled = false;
            showNotification('An error occurred while setting featured image', 'error');
        });
}

    // Event handlers for data-action approach
    function handleViewImage(element) {
        const imageUrl = element.dataset.imageUrl;
        const altText = element.dataset.altText;
        viewImage(imageUrl, altText);
    }

    function handleDeleteImage(element) {
        const imageId = element.dataset.imageId;
        const altText = element.dataset.altText;
        deleteProjectImage(imageId, altText);
    }

    function handleSetFeatured(element) {
        const imageId = element.dataset.imageId;
        setFeaturedImage(imageId);
    }

    // Event delegation for data-action approach
    document.addEventListener('click', function(e) {
        const action = e.target.closest('[data-action]')?.dataset.action;
        
        if (!action) return;
        
        // Prevent default and stop propagation for all actions
        e.preventDefault();
        e.stopPropagation();
        
        switch(action) {
            case 'view-image':
                handleViewImage(e.target.closest('[data-action]'));
                break;
                
            case 'delete-image':
                handleDeleteImage(e.target.closest('[data-action]'));
                break;
                
            case 'set-featured':
                handleSetFeatured(e.target.closest('[data-action]'));
                break;
        }
    });

    // Document ready initialization
    document.addEventListener('DOMContentLoaded', function() {
        debugLog('Project edit page initialized', {
            project_id: PROJECT_ID,
            current_images: CURRENT_IMAGES_COUNT,
            max_images: MAX_IMAGES
        });

        // Update upload status display
        function updateUploadStatus() {
            const statusElement = document.getElementById('upload-status');
            const progressElement = document.getElementById('upload-progress');
            const clearButton = document.getElementById('clear-all-uploads');

            if (statusElement) {
                const remaining = MAX_NEW_IMAGES - uploadedFilesCount;
                statusElement.textContent = `${uploadedFilesCount} / ${MAX_NEW_IMAGES} new images uploaded`;
            }

            if (progressElement) {
                const percentage = MAX_NEW_IMAGES > 0 ? (uploadedFilesCount / MAX_NEW_IMAGES) * 100 : 0;
                progressElement.style.width = `${percentage}%`;
            }

            if (clearButton) {
                clearButton.disabled = uploadedFilesCount === 0;
            }

            // Update form submission button text
            const submitButtons = document.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                if (!button.dataset.originalText) {
                    button.dataset.originalText = button.textContent;
                }

                if (uploadedFilesCount > 0) {
                    button.textContent = `${button.dataset.originalText} (+${uploadedFilesCount} new images)`;
                } else {
                    button.textContent = button.dataset.originalText;
                }
            });
        }

        // Universal uploader event handlers
        document.addEventListener('files-uploaded', function(event) {
            debugLog('Files uploaded event received', event.detail);

            if (event.detail.component && event.detail.component.includes('project-images-uploader-edit-' + PROJECT_ID)) {
                const result = event.detail.result;
                const files = event.detail.files || result?.files || [];

                // Update counters
                uploadedFilesCount += files.length;
                totalFilesUploaded.push(...files);
                updateUploadStatus();
                markFormAsChanged();

                // Handle response messages
                if (result && result.errors && result.errors.length > 0) {
                    const successCount = files.length;
                    const errorCount = result.errors.length;

                    showNotification(
                        `${successCount} image(s) uploaded successfully. ${errorCount} file(s) failed.`,
                        'warning'
                    );

                    result.errors.forEach((error, index) => {
                        setTimeout(() => {
                            showNotification(error, 'error');
                        }, 1000 + (index * 500));
                    });
                } else {
                    const uploadedCount = files.length;
                    showNotification(
                        `${uploadedCount} new image(s) uploaded successfully! Save to make changes permanent.`,
                        'success');
                }

                debugLog('Upload successful', {
                    files: files,
                    total_uploaded: uploadedFilesCount,
                    errors: result?.errors
                });
            }
        });

        document.addEventListener('upload-error', function(event) {
            debugLog('Upload error event received', event.detail);

            if (event.detail.component && event.detail.component.includes('project-images-uploader-edit-' + PROJECT_ID)) {
                const errorMessage = event.detail.error || 'Unknown error occurred';
                showNotification('Upload failed: ' + errorMessage, 'error');
            }
        });

        document.addEventListener('files-deleted', function(event) {
            debugLog('Files deleted event received', event.detail);

            if (event.detail.component && event.detail.component.includes('project-images-uploader-edit-' + PROJECT_ID)) {
                showNotification('New image deleted successfully!', 'success');

                uploadedFilesCount = Math.max(0, uploadedFilesCount - 1);

                if (event.detail.file && event.detail.file.temp_id) {
                    totalFilesUploaded = totalFilesUploaded.filter(file => file.temp_id !== event.detail.file.temp_id);
                }

                updateUploadStatus();
            }
        });

        // Handle clear all uploads
        const clearAllButton = document.getElementById('clear-all-uploads');
        if (clearAllButton) {
            clearAllButton.addEventListener('click', function() {
                if (uploadedFilesCount === 0) return;

                const confirmDelete = confirm(
                    `Are you sure you want to remove all ${uploadedFilesCount} newly uploaded images?`);
                if (!confirmDelete) return;

                // Clear via universal uploader if available
                if (window.universalUploaderInstances) {
                    const uploaderInstance = window.universalUploaderInstances['project-images-uploader-edit-' + PROJECT_ID];
                    if (uploaderInstance && typeof uploaderInstance.clearAllFiles === 'function') {
                        uploaderInstance.clearAllFiles();
                    }
                }

                uploadedFilesCount = 0;
                totalFilesUploaded = [];
                updateUploadStatus();

                showNotification('All newly uploaded images have been removed.', 'success');
            });
        }

        // Handle traditional file input
        const fileInput = document.getElementById('images');
        const altTextFields = document.getElementById('alt-text-fields');
        const altTextContainer = document.getElementById('alt-text-container');

        if (fileInput && altTextFields && altTextContainer) {
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                altTextContainer.innerHTML = '';

                if (files.length > 0) {
                    // Check total file limit
                    const totalCurrentImages = CURRENT_IMAGES_COUNT + uploadedFilesCount;
                    if (totalCurrentImages + files.length > MAX_IMAGES) {
                        showNotification(
                            `Cannot select ${files.length} files. Maximum ${MAX_IMAGES} images allowed (${totalCurrentImages} already exist).`,
                            'warning'
                        );
                        e.target.value = '';
                        return;
                    }

                    altTextFields.style.display = 'block';

                    const validFiles = [];
                    files.forEach((file, index) => {
                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            showNotification(`File "${file.name}" is not a supported image format.`, 'error');
                            return;
                        }

                        // Validate file size (2MB for traditional upload)
                        if (file.size > 2 * 1024 * 1024) {
                            showNotification(
                                `File "${file.name}" is too large. Maximum size is 2MB for traditional upload.`,
                                'error');
                            return;
                        }

                        validFiles.push({ file, index });
                    });

                    // Create alt text fields for valid files
                    validFiles.forEach(({ file, index }) => {
                        const div = document.createElement('div');
                        div.className = 'mb-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg';
                        div.innerHTML = `
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        ${file.name} <span class="text-xs text-gray-500">(${formatFileSize(file.size)})</span>
                                    </label>
                                    <input type="text" 
                                           name="image_alt_texts[]" 
                                           placeholder="Describe this image for accessibility..."
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md 
                                                  focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white"
                                           value="Project image ${CURRENT_IMAGES_COUNT + uploadedFilesCount + index + 1}"
                                           onchange="markFormAsChanged()">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Additional image ${index + 1}
                                    </p>
                                </div>
                            </div>
                        `;
                        altTextContainer.appendChild(div);
                    });

                    if (validFiles.length !== files.length) {
                        showNotification(
                            `${validFiles.length} of ${files.length} files are valid for upload.`,
                            'warning');
                    }

                    markFormAsChanged();
                } else {
                    altTextFields.style.display = 'none';
                }
            });
        }

        // Track changes in existing alt text fields
        document.querySelectorAll('input[name^="existing_image_alt"]').forEach(input => {
            input.addEventListener('change', markFormAsChanged);
        });

        // Form submission handling
        const projectForm = document.getElementById('project-form');
        if (projectForm) {
            projectForm.addEventListener('submit', function(e) {
                debugLog('Form submission started', {
                    uploaded_files: uploadedFilesCount,
                    total_files: totalFilesUploaded,
                    current_images: CURRENT_IMAGES_COUNT
                });

                // Basic validation
                const title = document.getElementById('title');
                const description = document.querySelector('[name="description"]');

                if (title && !title.value.trim()) {
                    e.preventDefault();
                    showNotification('Please enter a project title.', 'error');
                    title.focus();
                    return;
                }

                if (description && !description.value.trim()) {
                    e.preventDefault();
                    showNotification('Please enter a project description.', 'error');
                    description.focus();
                    return;
                }

                // Show loading state
                const submitButtons = projectForm.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    button.disabled = true;
                    const originalText = button.dataset.originalText || button.textContent;
                    button.textContent = 'Updating Project...';
                    button.dataset.originalText = originalText;
                });

                showNotification('Updating project, please wait...', 'info');
                hasUnsavedChanges = false; // Prevent warning after successful submission
            });
        }

        // Warn before leaving page with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton && !submitButton.disabled) {
                    submitButton.click();
                }
            }

            // Escape to close modal
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        // Initialize
        updateUploadStatus();

        // Check for existing temp files on page load
        fetch('{{ route('admin.projects.temp-files') }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.files) {
                    uploadedFilesCount = data.files.length;
                    totalFilesUploaded = data.files;
                    updateUploadStatus();

                    if (uploadedFilesCount > 0) {
                        showNotification(`Found ${uploadedFilesCount} previously uploaded new images.`, 'info');
                    }
                }
            })
            .catch(error => {
                debugLog('Error checking temp files', error);
            });
    });

    // Make sure these functions are accessible globally for any remaining inline onclick handlers
    window.viewImage = viewImage;
    window.closeImageModal = closeImageModal;
    window.deleteProjectImage = deleteProjectImage;
    window.setFeaturedImage = setFeaturedImage;
    window.markFormAsChanged = markFormAsChanged;
    window.showNotification = showNotification;
    window.updateImageGrid = updateImageGrid;
</script>
@push('scripts')

@endpush
