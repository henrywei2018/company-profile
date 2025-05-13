<!-- resources/views/admin/services/create.blade.php -->
<x-admin-layout :title="isset($service) ? 'Edit Service: ' . $service->title : 'Create New Service'">
    <div class="mb-6">
        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Services
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ isset($service) ? 'Edit Service: ' . $service->title : 'Create New Service' }}
            </h2>

            <form action="{{ isset($service) ? route('admin.services.update', $service->id) : route('admin.services.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($service))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Service Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Service Title</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('title', isset($service) ? $service->title : '') }}" 
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('slug', isset($service) ? $service->slug : '') }}">
                        <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from title.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Short Description -->
                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                        <textarea name="short_description" 
                                  id="short_description" 
                                  rows="3" 
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('short_description', isset($service) ? $service->short_description : '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Brief summary for listings (100-150 characters).</p>
                        @error('short_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icon Class -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700">Icon Class (Optional)</label>
                        <input type="text" 
                               name="icon" 
                               id="icon" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('icon', isset($service) ? $service->icon : '') }}"
                               placeholder="e.g. fas fa-building">
                        <p class="mt-1 text-sm text-gray-500">Font Awesome or other icon class.</p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Order -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('order', isset($service) ? $service->order : 0) }}"
                               min="0">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
                        @error('order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Toggles -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="active" 
                                       id="active" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('active', isset($service) && $service->active ? true : false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="active" class="font-medium text-gray-700">Active</label>
                                <p class="text-gray-500">Only active services are displayed on the website.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="featured" 
                                       id="featured" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('featured', isset($service) && $service->featured ? true : false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="featured" class="font-medium text-gray-700">Featured</label>
                                <p class="text-gray-500">Featured services are highlighted on the homepage.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Image -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700">Service Image</label>
                    @if(isset($service) && $service->image)
                        <div class="mt-2 mb-4">
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="h-40 w-auto object-cover rounded">
                        </div>
                    @endif
                    <input type="file" 
                           name="image" 
                           id="image" 
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300"
                           accept="image/*">
                    <p class="mt-1 text-sm text-gray-500">Recommended size: 800×600 pixels.</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="10" 
                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                              required>{{ old('description', isset($service) ? $service->description : '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Features/Inclusions -->
                <div class="mb-6">
                    <label for="features" class="block text-sm font-medium text-gray-700">Features/Inclusions</label>
                    <p class="text-xs text-gray-500 mb-2">Enter one feature per line</p>
                    <textarea name="features" 
                              id="features" 
                              rows="5" 
                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('features', isset($service) && is_array($service->features) ? implode("\n", $service->features) : (isset($service) ? $service->features : '')) }}</textarea>
                    @error('features')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meta Data for SEO -->
                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <h3 class="text-md font-medium text-gray-900 mb-3">SEO Information</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                            <input type="text" 
                                   name="meta_title" 
                                   id="meta_title" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('meta_title', isset($service) ? $service->meta_title : '') }}"
                                   placeholder="Optimal length: 50-60 characters">
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <textarea name="meta_description" 
                                      id="meta_description" 
                                      rows="2" 
                                      class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Optimal length: 150-160 characters">{{ old('meta_description', isset($service) ? $service->meta_description : '') }}</textarea>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.services.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ isset($service) ? 'Update Service' : 'Create Service' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($service))
        <!-- Related Projects -->
        <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Related Projects</h2>
                <p class="mt-1 text-sm text-gray-500">Projects that use this service.</p>
            </div>

            <div class="p-6">
                @if(isset($relatedProjects) && $relatedProjects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($relatedProjects as $project)
                            <div class="border border-gray-200 rounded-md overflow-hidden">
                                <div class="h-32 overflow-hidden">
                                    @if($project->getFeaturedImageUrlAttribute())
                                        <img src="{{ $project->getFeaturedImageUrlAttribute() }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $project->title }}</h3>
                                    <p class="mt-1 text-xs text-gray-500">{{ $project->location }}</p>
                                    <div class="mt-2 flex justify-end">
                                        <a href="{{ route('admin.projects.edit', $project->id) }}" class="text-xs text-indigo-600 hover:text-indigo-900">View Project →</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 p-4 rounded-md text-center">
                        <p class="text-gray-500">No projects are using this service yet.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-admin-layout>