{{-- resources/views/admin/projects/create.blade.php --}}
<x-layouts.admin title="Create New Project">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Project</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Add a new project to your portfolio
            </p>
        </div>

        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <x-admin.button href="{{ route('admin.projects.index') }}" color="light" size="sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Projects
            </x-admin.button>
        </div>
    </div>

    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Projects' => route('admin.projects.index'),
        'Create New' => route('admin.projects.create'),
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

    <!-- Project Information from Quotation -->
    @if (isset($quotation) && $quotation)
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
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Creating Project from Quotation
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>This project is being created from Quotation #{{ $quotation->id }} submitted by
                            {{ $quotation->name }}.</p>
                        <p class="mt-1">Some fields have been pre-filled based on the quotation details.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Project Create Form -->
    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data" id="project-form">
        @csrf

        @if (isset($quotation) && $quotation)
            <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
        @endif
        <div class="flex flex-col lg:flex-row gap-6 py-3">
            <div class="w-full lg:flex-1 space-y-6">
                <!-- Basic Information Section -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Basic Information
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the fundamental project
                                details.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Project Title -->
                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Title
                                    *</label>
                                <input type="text" name="title" id="title"
                                    value="{{ old('title', $quotation->project_name ?? '') }}"
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
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
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
                                                {{ old('client_id', isset($quotation) && $quotation->user_id == $client->id ? $client->id : '') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('client_id') }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Client Name (fallback) -->
                            @if (Schema::hasColumn('projects', 'client_name'))
                                <div>
                                    <label for="client_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client
                                        Name</label>
                                    <input type="text" name="client_name" id="client_name"
                                        value="{{ old('client_name', $quotation->name ?? '') }}"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('client_name') border-red-500 @enderror">
                                    @error('client_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('client_name') }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Category -->
                            @if (Schema::hasColumn('projects', 'category_id'))
                                <div>
                                    <label for="category_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category
                                        *</label>
                                    <select name="category_id" id="category_id" required
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('category_id') border-red-500 @enderror">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('category_id') }}</p>
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
                                                {{ old('service_id', isset($quotation) && $quotation->service_id == $service->id ? $service->id : '') == $service->id ? 'selected' : '' }}>
                                                {{ $service->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('service_id') }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Location -->
                            <div>
                                <label for="location"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                                <input type="text" name="location" id="location"
                                    value="{{ old('location', $quotation->location ?? '') }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('location') border-red-500 @enderror">
                                @error('location')
                                    <p class="mt-2 text-sm text-red-600">{{ $errors->first('location') }}</p>
                                @enderror
                            </div>

                            <!-- Year -->
                            <div>
                                <label for="year"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                                <input type="number" name="year" id="year" min="1900"
                                    max="{{ date('Y') + 5 }}" value="{{ old('year', date('Y')) }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('year') border-red-500 @enderror">
                                @error('year')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description
                                *</label>
                            <textarea name="description" id="description" rows="4" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('description') border-red-500 @enderror">{{ old('description', $quotation->project_description ?? '') }}</textarea>
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
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('short_description') border-red-500 @enderror">{{ old('short_description') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Brief summary for listings and previews.</p>
                                @error('short_description')
                                    <p class="mt-2 text-sm text-red-600">{{ $errors->first('short_description') }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Project Details Section -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Project Details
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Additional project information and
                                documentation.</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Challenge -->
                            <div>
                                <label for="challenge"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Challenge</label>
                                <textarea name="challenge" id="challenge" rows="3"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('challenge') border-red-500 @enderror">{{ old('challenge') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">What challenges will this project address?</p>
                                @error('challenge')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Solution -->
                            <div>
                                <label for="solution"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Solution</label>
                                <textarea name="solution" id="solution" rows="3"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('solution') border-red-500 @enderror">{{ old('solution') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">How will we solve these challenges?</p>
                                @error('solution')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Result -->
                            <div>
                                <label for="result"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected
                                    Result</label>
                                <textarea name="result" id="result" rows="3"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('result') border-red-500 @enderror">{{ old('result') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">What are the expected outcomes?</p>
                                @error('result')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Images Section -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Project Images</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload images to showcase the
                                project.</p>
                        </div>

                        <!-- Upload New Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload
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
                                            <input id="images" name="images[]" type="file" class="sr-only"
                                                multiple accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WebP up to 2MB each
                                        (max 10 images)</p>
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
            </div>
            <div class="w-full lg:w-80 space-y-6">
                <!-- Project Status & Management Section -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Status & Management
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set project status, priority, and
                                initial progress.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2">
                            <!-- Status -->
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('status') border-red-500 @enderror">
                                    <option value="planning"
                                        {{ old('status', 'planning') === 'planning' ? 'selected' : '' }}>Planning
                                    </option>
                                    <option value="in_progress"
                                        {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>On
                                        Hold</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Priority (if column exists) -->
                            @if (Schema::hasColumn('projects', 'priority'))
                                <div>
                                    <label for="priority"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority
                                        *</label>
                                    <select name="priority" id="priority" required
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('priority') border-red-500 @enderror">
                                        <option value="low"
                                            {{ old('priority', 'normal') === 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="normal"
                                            {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal
                                        </option>
                                        <option value="high"
                                            {{ old('priority', 'normal') === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent"
                                            {{ old('priority', 'normal') === 'urgent' ? 'selected' : '' }}>Urgent
                                        </option>
                                    </select>
                                    @error('priority')
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('priority') }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Progress Percentage (if column exists) -->
                            @if (Schema::hasColumn('projects', 'progress_percentage'))
                                <div>
                                    <label for="progress_percentage"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Initial
                                        Progress (%)</label>
                                    <input type="number" name="progress_percentage" id="progress_percentage"
                                        min="0" max="100" value="{{ old('progress_percentage', 0) }}"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('progress_percentage') border-red-500 @enderror">
                                    @error('progress_percentage')
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('ptegress_percentage') }}
                                        </p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Featured & Active Status -->
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="hidden" name="featured" value="0">
                                    <input type="checkbox" name="featured" id="featured" value="1"
                                        {{ old('featured', false) ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    <label for="featured"
                                        class="ml-2 block text-sm text-gray-900 dark:text-white">Featured
                                        Project</label>
                                </div>

                                @if (Schema::hasColumn('projects', 'is_active'))
                                    <div class="flex items-center">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" id="is_active" value="1"
                                            {{ old('is_active', true) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="is_active"
                                            class="ml-2 block text-sm text-gray-900 dark:text-white">Active
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Timeline & Dates
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set project start and end dates.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start
                                    Date</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ old('start_date', isset($quotation) && $quotation->start_date ? $quotation->start_date->format('Y-m-d') : '') }}"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('start_date') border-red-500 @enderror">
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                <input type="date" name="end_date" id="end_date"
                                    value="{{ old('end_date', isset($quotation) && $quotation->completion_date ? $quotation->completion_date->format('Y-m-d') : '') }}"
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
                                    <input type="date" name="estimated_completion_date"
                                        id="estimated_completion_date" value="{{ old('estimated_completion_date') }}"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('estimated_completion_date') border-red-500 @enderror">
                                    @error('estimated_completion_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('field_name') }}</p>
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
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Financial
                                    Information</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Budget and cost tracking.</p>
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Project Value -->
                                @if (Schema::hasColumn('projects', 'value'))
                                    <div>
                                        <label for="value"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project
                                            Value</label>
                                        <input type="text" name="value" id="value"
                                            value="{{ old('value', isset($quotation) ? 'Rp ' . number_format($quotation->budget, 0, ',', '.') : '') }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('value') border-red-500 @enderror">
                                        @error('value')
                                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('value') }}</p>
                                        @enderror
                                    </div>
                                @endif

                                <!-- Budget -->
                                @if (Schema::hasColumn('projects', 'budget'))
                                    <div>
                                        <label for="budget"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget</label>
                                        <input type="number" name="budget" id="budget" step="0.01"
                                            value="{{ old('budget', isset($quotation) ? $quotation->budget : '') }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('budget') border-red-500 @enderror">
                                        @error('budget')
                                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('budget') }}</p>
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
                                            value="{{ old('actual_cost') }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('actual_cost') border-red-500 @enderror">
                                        @error('actual_cost')
                                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('actual_cost') }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif


                <!-- SEO Section (if columns exist) -->
                @if (Schema::hasColumn('projects', 'meta_title') ||
                        Schema::hasColumn('projects', 'meta_description') ||
                        Schema::hasColumn('projects', 'meta_keywords'))
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">SEO Information
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search engine optimization
                                    settings.</p>
                            </div>

                            <div class="space-y-6">
                                <!-- Meta Title -->
                                @if (Schema::hasColumn('projects', 'meta_title'))
                                    <div>
                                        <label for="meta_title"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta
                                            Title</label>
                                        <input type="text" name="meta_title" id="meta_title"
                                            value="{{ old('meta_title') }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('meta_title') border-red-500 @enderror">
                                        <p class="mt-1 text-sm text-gray-500">Recommended length: 50-60 characters</p>
                                        @error('meta_title')
                                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('meta_title') }}</p>
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
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('meta_description') border-red-500 @enderror">{{ old('meta_description') }}</textarea>
                                        <p class="mt-1 text-sm text-gray-500">Recommended length: 150-160 characters
                                        </p>
                                        @error('meta_description')
                                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('meta_description') }}
                                            </p>
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
                                            value="{{ old('meta_keywords') }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('meta_keywords') border-red-500 @enderror">
                                        <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                                        @error('meta_keywords')
                                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('meta_keywords') }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif


            </div>
            <!-- Form Actions -->

        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-2 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.projects.index') }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </a>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" name="action" value="save_and_add_another"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            Save & Add Another
                        </button>

                        <button type="submit" name="action" value="save_and_add_milestone"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            Save & Add Milestone
                        </button>

                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Create Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>

@push('scripts')
    <script>
        // Auto-generate slug from title
        document.getElementById('title')?.addEventListener('input', function(e) {
            const slug = document.getElementById('slug');
            if (slug && !slug.value) {
                slug.value = e.target.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/--+/g, '-')
                    .trim();
            }
        });

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

        // Form validation
        document.getElementById('project-form')?.addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date')?.value;
            const endDate = document.getElementById('end_date')?.value;

            if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                e.preventDefault();
                alert('End date must be after or equal to start date.');
                return false;
            }

            // Check budget vs actual cost
            const budget = parseFloat(document.getElementById('budget')?.value || 0);
            const actualCost = parseFloat(document.getElementById('actual_cost')?.value || 0);

            if (budget > 0 && actualCost > budget * 1.5) {
                if (!confirm(
                        'Actual cost is significantly over budget (more than 150%). Are you sure this is correct?'
                        )) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Update status-related fields
        document.getElementById('status')?.addEventListener('change', function(e) {
            const progressField = document.getElementById('progress_percentage');
            if (progressField && e.target.value === 'completed') {
                progressField.value = '100';
            }
        });

        // Handle quotation-based project creation
        @if (isset($quotation) && $quotation)
            // Pre-populate fields based on quotation data
            document.addEventListener('DOMContentLoaded', function() {
                // You can add additional logic here to handle quotation data
                console.log('Creating project from quotation #{{ $quotation->id }}');
            });
        @endif
    </script>
@endpush
