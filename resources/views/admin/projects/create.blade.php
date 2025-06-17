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
                        <x-universal-file-uploader 
                            :id="'project-images-uploader-create'"
                            name="temp_images" 
                            :multiple="true" 
                            :maxFiles="10"
                            maxFileSize="5MB" 
                            :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" 
                            :uploadEndpoint="route('admin.projects.upload-temp')" 
                            :deleteEndpoint="route('admin.projects.delete-temp')"
                            dropDescription="Drop project images here or click to browse (Max 5MB each)" 
                            :enableCategories="true"
                            :categories="[
                                ['value' => 'gallery', 'label' => 'Gallery Image'], 
                                ['value' => 'before', 'label' => 'Before Photo'], 
                                ['value' => 'during', 'label' => 'During Construction'], 
                                ['value' => 'after', 'label' => 'After/Final Result'],
                                ['value' => 'detail', 'label' => 'Detail Shot']
                            ]"
                            :enableDescription="true"
                            :enablePublicToggle="false"
                            :instantUpload="true" 
                            :galleryMode="true" 
                            :replaceMode="false"
                            containerClass="mb-4" 
                            theme="modern" 
                            :singleMode="false" 
                            :showFileList="true"
                            :showProgress="true"
                            :dragOverlay="true" />
                            <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-sm text-gray-600 dark:text-gray-300">Upload Status:</span>
            <span id="upload-status" class="text-sm font-medium text-gray-900 dark:text-white">0 / 10 images uploaded</span>
        </div>
        
        <div class="flex items-center space-x-2">
            <!-- Progress Bar -->
            <div class="w-24 bg-gray-200 rounded-full h-2 dark:bg-gray-600">
                <div id="upload-progress" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            
            <!-- Clear All Button -->
            <button type="button" 
                    id="clear-all-uploads" 
                    class="text-xs px-2 py-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                Clear All
            </button>
        </div>
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
// Enhanced JavaScript untuk create view - tambahkan di @push('scripts')

document.addEventListener('DOMContentLoaded', function() {
    // Enhanced logging for debugging
    const DEBUG = {{ app()->environment(['local', 'staging']) ? 'true' : 'false' }};
    
    function debugLog(message, data = null) {
        if (DEBUG) {
            console.log('[Project Upload Debug]', message, data);
        }
    }

    // Track upload state
    let uploadedFilesCount = 0;
    let totalFilesUploaded = [];

    // Listen for universal uploader events with enhanced handling
    document.addEventListener('files-uploaded', function(event) {
        debugLog('Files uploaded event received', event.detail);
        
        if (event.detail.component && event.detail.component.includes('project-images-uploader-create')) {
            const result = event.detail.result;
            const files = event.detail.files || result?.files || [];
            
            // Handle partial success scenarios
            if (result && result.errors && result.errors.length > 0) {
                // Some files failed
                const successCount = files.length;
                const errorCount = result.errors.length;
                
                showNotification(
                    `${successCount} image(s) uploaded successfully. ${errorCount} file(s) failed.`, 
                    'warning'
                );
                
                // Show specific errors
                result.errors.forEach(error => {
                    setTimeout(() => {
                        showNotification(error, 'error');
                    }, 1000);
                });
            } else {
                // All files succeeded
                const uploadedCount = files.length;
                showNotification(`${uploadedCount} image(s) uploaded successfully!`, 'success');
            }
            
            // Update counters
            uploadedFilesCount += files.length;
            totalFilesUploaded.push(...files);
            updateUploadStatus();
            
            debugLog('Upload successful', {
                files: files,
                total_uploaded: uploadedFilesCount,
                errors: result?.errors
            });
        }
    });
    document.addEventListener('files-uploaded', function(event) {
    debugLog('Files uploaded event received', event.detail);
    
    if (event.detail.component && event.detail.component.includes('project-images-uploader-create')) {
        const result = event.detail.result;
        const files = event.detail.files || result?.files || [];
        
        // Update counters
        uploadedFilesCount += files.length;
        totalFilesUploaded.push(...files);
        updateUploadStatus();
        
        // Handle response messages
        if (result && result.errors && result.errors.length > 0) {
            const successCount = files.length;
            const errorCount = result.errors.length;
            
            showNotification(
                `${successCount} image(s) uploaded successfully. ${errorCount} file(s) failed.`, 
                'warning'
            );
            
            // Show specific errors after a delay
            result.errors.forEach((error, index) => {
                setTimeout(() => {
                    showNotification(error, 'error');
                }, 1000 + (index * 500));
            });
        } else {
            const uploadedCount = files.length;
            showNotification(`${uploadedCount} image(s) uploaded successfully!`, 'success');
        }
        
        debugLog('Upload successful', {
            files: files,
            total_uploaded: uploadedFilesCount,
            errors: result?.errors
        });
    }
});
document.addEventListener('files-deleted', function(event) {
    debugLog('Files deleted event received', event.detail);
    
    if (event.detail.component && event.detail.component.includes('project-images-uploader-create')) {
        showNotification('Image deleted successfully!', 'success');
        
        // Update counter
        uploadedFilesCount = Math.max(0, uploadedFilesCount - 1);
        
        // Remove from total files array if we have the file info
        if (event.detail.file && event.detail.file.temp_id) {
            totalFilesUploaded = totalFilesUploaded.filter(file => file.temp_id !== event.detail.file.temp_id);
        }
        
        updateUploadStatus();
    }
});

    document.addEventListener('upload-error', function(event) {
        debugLog('Upload error event received', event.detail);
        
        if (event.detail.component && event.detail.component.includes('project-images-uploader-create')) {
            const errorMessage = event.detail.error || 'Unknown error occurred';
            showNotification('Upload failed: ' + errorMessage, 'error');
            
            debugLog('Upload failed', {
                error: errorMessage,
                component: event.detail.component
            });
        }
    });

    document.addEventListener('files-selected', function(event) {
    debugLog('Files selected event received', event.detail);
    
    if (event.detail.component && event.detail.component.includes('project-images-uploader-create')) {
        const selectedCount = event.detail.files ? event.detail.files.length : 0;
        
        // Validate file count
        if (uploadedFilesCount + selectedCount > 10) {
            showNotification(
                `Cannot upload ${selectedCount} more files. Maximum 10 images allowed (${uploadedFilesCount} already uploaded).`, 
                'warning'
            );
            
            // Prevent upload by clearing the selection
            // This would need integration with the universal uploader component
            return false;
        }
        
        debugLog('Files selected', {
            count: selectedCount,
            files: event.detail.files,
            current_uploaded: uploadedFilesCount
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
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
                showNotification(`Found ${uploadedFilesCount} previously uploaded images.`, 'info');
            }
        }
    })
    .catch(error => {
        debugLog('Error checking temp files', error);
    });
});
    // Update upload status display
    function updateUploadStatus() {
    const statusElement = document.getElementById('upload-status');
    const progressElement = document.getElementById('upload-progress');
    const clearButton = document.getElementById('clear-all-uploads');
    
    if (statusElement) {
        statusElement.textContent = `${uploadedFilesCount} / 10 images uploaded`;
    }
    
    if (progressElement) {
        const percentage = (uploadedFilesCount / 10) * 100;
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
            button.textContent = `${button.dataset.originalText} (${uploadedFilesCount} images)`;
        } else {
            button.textContent = button.dataset.originalText;
        }
    });
}
document.getElementById('clear-all-uploads')?.addEventListener('click', function() {
    if (uploadedFilesCount === 0) return;
    
    const confirmDelete = confirm(`Are you sure you want to remove all ${uploadedFilesCount} uploaded images?`);
    if (!confirmDelete) return;
    
    // This would need to interact with the universal uploader component
    // to clear all uploaded files
    if (window.universalUploaderInstances) {
        const uploaderInstance = window.universalUploaderInstances['project-images-uploader-create'];
        if (uploaderInstance && typeof uploaderInstance.clearAllFiles === 'function') {
            uploaderInstance.clearAllFiles();
        }
    }
    
    // Reset counters
    uploadedFilesCount = 0;
    totalFilesUploaded = [];
    updateUploadStatus();
    
    showNotification('All uploaded images have been removed.', 'success');
});
document.addEventListener('files-cleared', function(event) {
    if (event.detail.component && event.detail.component.includes('project-images-uploader-create')) {
        uploadedFilesCount = 0;
        totalFilesUploaded = [];
        updateUploadStatus();
    }
});

    // Enhanced form submission with upload validation
    const projectForm = document.getElementById('project-form');
    if (projectForm) {
        projectForm.addEventListener('submit', function(e) {
            debugLog('Form submission started', {
                uploaded_files: uploadedFilesCount,
                total_files: totalFilesUploaded
            });
            
            const title = document.getElementById('title').value.trim();
            const description = document.querySelector('[name="description"]').value.trim();
            
            if (!title) {
                e.preventDefault();
                showNotification('Please enter a project title.', 'error');
                document.getElementById('title').focus();
                return;
            }
            
            if (!description) {
                e.preventDefault();
                showNotification('Please enter a project description.', 'error');
                document.querySelector('[name="description"]').focus();
                return;
            }
            
            // Check required category field if it exists
            const categoryField = document.querySelector('[name="category_id"]');
            if (categoryField && !categoryField.value) {
                e.preventDefault();
                showNotification('Please select a project category.', 'error');
                categoryField.focus();
                return;
            }
            
            // Check if at least one image is uploaded (optional validation)
            const traditionalFiles = document.getElementById('images').files;
            if (uploadedFilesCount === 0 && traditionalFiles.length === 0) {
                const proceed = confirm('No images have been uploaded. Do you want to create the project without images?');
                if (!proceed) {
                    e.preventDefault();
                    return;
                }
            }
            
            debugLog('Form validation passed, submitting...', {
                uploaded_count: uploadedFilesCount,
                traditional_count: traditionalFiles.length
            });
            
            // Show loading state
            const submitButtons = projectForm.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = true;
                const originalText = button.dataset.originalText || button.textContent;
                button.textContent = 'Creating Project...';
                button.dataset.originalText = originalText;
            });
            
            // Show progress message
            showNotification('Creating project, please wait...', 'info');
        });
    }

    // Handle traditional file input with enhanced validation
    const fileInput = document.getElementById('images');
    const altTextFields = document.getElementById('alt-text-fields');
    const altTextContainer = document.getElementById('alt-text-container');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            altTextContainer.innerHTML = '';
            
            if (files.length > 0) {
                // Check total file limit
                if (uploadedFilesCount + files.length > 10) {
                    showNotification(
                        `Cannot select ${files.length} files. Maximum 10 images allowed (${uploadedFilesCount} already uploaded via uploader).`, 
                        'warning'
                    );
                    e.target.value = ''; // Clear selection
                    return;
                }
                
                altTextFields.style.display = 'block';
                
                // Validate and process each file
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
                        showNotification(`File "${file.name}" is too large. Maximum size is 2MB for traditional upload.`, 'error');
                        return;
                    }
                    
                    validFiles.push({file, index});
                });
                
                // Create alt text fields for valid files
                validFiles.forEach(({file, index}) => {
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
                                       value="Project image ${index + 1}">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    ${index === 0 ? '✨ This will be the featured image' : `Image ${index + 1}`}
                                </p>
                            </div>
                        </div>
                    `;
                    altTextContainer.appendChild(div);
                });
                
                if (validFiles.length !== files.length) {
                    showNotification(`${validFiles.length} of ${files.length} files are valid for upload.`, 'warning');
                }
            } else {
                altTextFields.style.display = 'none';
            }
        });
    }

    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function(e) {
            if (!slugInput.value || slugInput.dataset.userModified !== 'true') {
                const slug = generateSlug(e.target.value);
                slugInput.value = slug;
            }
        });
        
        slugInput.addEventListener('input', function() {
            slugInput.dataset.userModified = 'true';
        });
    }

    // Helper functions
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function generateSlug(title) {
        return title
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    // Enhanced notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications of the same type
        const existingNotifications = document.querySelectorAll(`.notification-toast.${type}`);
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `notification-toast ${type} fixed top-4 right-4 z-50 p-4 rounded-lg shadow-xl transition-all duration-300 transform translate-x-full max-w-sm border-l-4`;
        
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
        }, type === 'error' ? 8000 : 6000); // Errors stay longer
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

    // Initialize upload status display
    updateUploadStatus();
});
</script>
@endpush