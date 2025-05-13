<!-- resources/views/admin/projects/create.blade.php -->
<x-admin-layout :title="isset($project) ? 'Edit Project: ' . $project->title : 'Create New Project'">
    <div class="mb-6">
        <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Projects
        </a>
    </div>

    <form action="{{ isset($project) ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-8">
        @csrf
        @if(isset($project))
            @method('PUT')
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isset($project) ? 'Edit Project Details' : 'Project Details' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ isset($project) ? 'Update the information for this project.' : 'Fill in the details for the new project.' }}
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Project Title -->
                    <div class="sm:col-span-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Project Title</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title', isset($project) ? $project->title : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="sm:col-span-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="slug" 
                                   id="slug" 
                                   value="{{ old('slug', isset($project) ? $project->slug : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from title.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="sm:col-span-3">
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <div class="mt-1">
                            <select id="category" 
                                    name="category" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">Select a category</option>
                                <option value="residential" {{ old('category', isset($project) ? $project->category : '') == 'residential' ? 'selected' : '' }}>Residential</option>
                                <option value="commercial" {{ old('category', isset($project) ? $project->category : '') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="industrial" {{ old('category', isset($project) ? $project->category : '') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                                <option value="infrastructure" {{ old('category', isset($project) ? $project->category : '') == 'infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                                <option value="institutional" {{ old('category', isset($project) ? $project->category : '') == 'institutional' ? 'selected' : '' }}>Institutional</option>
                                <option value="other" {{ old('category', isset($project) ? $project->category : '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1">
                            <select id="status" 
                                    name="status" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="completed" {{ old('status', isset($project) ? $project->status : '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="in_progress" {{ old('status', isset($project) ? $project->status : '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="planned" {{ old('status', isset($project) ? $project->status : '') == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="on_hold" {{ old('status', isset($project) ? $project->status : '') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="sm:col-span-3">
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="location" 
                                   id="location" 
                                   value="{{ old('location', isset($project) ? $project->location : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Name -->
                    <div class="sm:col-span-3">
                        <label for="client_name" class="block text-sm font-medium text-gray-700">Client Name</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="client_name" 
                                   id="client_name" 
                                   value="{{ old('client_name', isset($project) ? $project->client_name : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('client_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Year or Date Range -->
                    <div class="sm:col-span-2">
                        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="year" 
                                   id="year" 
                                   value="{{ old('year', isset($project) ? $project->year : date('Y')) }}" 
                                   min="2000" 
                                   max="{{ date('Y') + 5 }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div class="sm:col-span-2">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <div class="mt-1">
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date" 
                                   value="{{ old('start_date', isset($project) && $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="sm:col-span-2">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <div class="mt-1">
                            <input type="date" 
                                   name="end_date" 
                                   id="end_date" 
                                   value="{{ old('end_date', isset($project) && $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Value -->
                    <div class="sm:col-span-3">
                        <label for="value" class="block text-sm font-medium text-gray-700">Project Value (Optional)</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="value" 
                                   id="value" 
                                   value="{{ old('value', isset($project) ? $project->value : '') }}" 
                                   placeholder="e.g. $100,000 - $250,000" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">You can use a range or specific amount.</p>
                        @error('value')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Services Used -->
                    <div class="sm:col-span-6">
                        <label for="services_used" class="block text-sm font-medium text-gray-700">Services Used</label>
                        <div class="mt-1">
                            <select id="services_used" 
                                    name="services_used[]" 
                                    multiple 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" 
                                            {{ old('services_used', 
                                                isset($project) && is_array($project->services_used) && in_array($service->id, $project->services_used) 
                                                ? 'selected' 
                                                : '') }}>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Hold Ctrl (or Cmd) to select multiple services.</p>
                        @error('services_used')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Toggle -->
                    <div class="sm:col-span-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="featured" 
                                       id="featured" 
                                       value="1" 
                                       {{ old('featured', isset($project) && $project->featured ? 'checked' : '') }} 
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="featured" class="font-medium text-gray-700">Featured Project</label>
                                <p class="text-gray-500">Featured projects appear on the homepage and at the top of the portfolio page.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Description & Details -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Project Description & Details</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Provide comprehensive information about the project.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <!-- Main Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Project Description</label>
                    <div class="mt-1">
                        <textarea id="description" 
                                  name="description" 
                                  rows="6" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', isset($project) ? $project->description : '') }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Provide a detailed description of the project.</p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Challenge, Solution, Result Sections -->
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-4">
                    <!-- Challenge -->
                    <div>
                        <label for="challenge" class="block text-sm font-medium text-gray-700">Challenge</label>
                        <div class="mt-1">
                            <textarea id="challenge" 
                                      name="challenge" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('challenge', isset($project) ? $project->challenge : '') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Describe the challenges faced in this project.</p>
                        @error('challenge')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Solution -->
                    <div>
                        <label for="solution" class="block text-sm font-medium text-gray-700">Solution</label>
                        <div class="mt-1">
                            <textarea id="solution" 
                                      name="solution" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('solution', isset($project) ? $project->solution : '') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Explain how you approached and solved the challenges.</p>
                        @error('solution')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Result -->
                    <div>
                        <label for="result" class="block text-sm font-medium text-gray-700">Result</label>
                        <div class="mt-1">
                            <textarea id="result" 
                                      name="result" 
                                      rows="4" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('result', isset($project) ? $project->result : '') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Describe the outcomes and benefits achieved.</p>
                        @error('result')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Images -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Project Images</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Upload high-quality images that showcase the project.
                </p>
            </div>

            <div class="p-6 bg-white">
                <!-- Featured Image -->
                <div class="mb-6">
                    <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image</label>
                    @if(isset($project) && $project->getFeaturedImageUrlAttribute())
                        <div class="mt-2 mb-3">
                            <img src="{{ $project->getFeaturedImageUrlAttribute() }}" alt="{{ $project->title }}" class="h-48 w-auto object-cover rounded-md">
                        </div>
                    @endif
                    <div class="mt-1">
                        <input type="file" 
                               id="featured_image" 
                               name="featured_image" 
                               accept="image/*" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This image will be displayed as the main image on listings. Recommended size: 1200×800 pixels.</p>
                    @error('featured_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gallery Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gallery Images</label>
                    @if(isset($project) && isset($project->images) && $project->images->count() > 0)
                        <div class="mt-2 mb-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($project->images as $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text }}" class="h-32 w-full object-cover rounded-md">
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.project-images.edit', $image->id) }}" class="text-white mx-1">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.project-images.destroy', $image->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-white mx-1">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-1">
                        <input type="file" 
                               id="gallery_images" 
                               name="gallery_images[]" 
                               multiple 
                               accept="image/*" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Upload multiple images for the project gallery. Hold Ctrl (or Cmd) to select multiple files.</p>
                    @error('gallery_images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('gallery_images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SEO Information -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">SEO Information</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Optimize this project for search engines.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <!-- Meta Title -->
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                    <div class="mt-1">
                        <input type="text" 
                               id="meta_title" 
                               name="meta_title" 
                               value="{{ old('meta_title', isset($project->seo) ? $project->seo->title : '') }}" 
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Keep it under 60 characters. Leave blank to use the project title.</p>
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                    <div class="mt-1">
                        <textarea id="meta_description" 
                                  name="meta_description" 
                                  rows="2" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('meta_description', isset($project->seo) ? $project->seo->description : '') }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Keep it between 150-160 characters for best results.</p>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meta Keywords -->
                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                    <div class="mt-1">
                        <input type="text" 
                               id="meta_keywords" 
                               name="meta_keywords" 
                               value="{{ old('meta_keywords', isset($project->seo) ? $project->seo->keywords : '') }}" 
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Separate keywords with commas. Example: construction, building, design</p>
                    @error('meta_keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- OG Image -->
                <div>
                    <label for="og_image" class="block text-sm font-medium text-gray-700">Social Media Image</label>
                    @if(isset($project->seo) && $project->seo->og_image)
                        <div class="mt-2 mb-3">
                            <img src="{{ asset('storage/' . $project->seo->og_image) }}" alt="Social Media Image" class="h-32 w-auto object-cover rounded-md">
                        </div>
                    @endif
                    <div class="mt-1">
                        <input type="file" 
                               id="og_image" 
                               name="og_image" 
                               accept="image/*" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This image will be used when sharing on social media. Recommended size: 1200×630 pixels.</p>
                    @error('og_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Buttons -->
        <div class="flex justify-end">
            <a href="{{ route('admin.projects.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ isset($project) ? 'Update Project' : 'Create Project' }}
            </button>
        </div>
    </form>
</x-admin-layout>